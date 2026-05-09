<template>
  <div class="am-apple-integration">
    <!-- Navigation Header -->
    <div v-if="showBack" class="am-apple-integration__header">
      <div class="am-apple-integration__header-back" @click="handleBack">
        <span class="am-icon-arrow-big-left"></span>
        <span class="am-apple-integration__header-title">{{
          amLabels.apple_calendar
        }}</span>
      </div>
    </div>
    <!-- /Navigation Header -->

    <!-- Content -->
    <div class="am-apple-integration__content">
      <p class="am-apple-integration__description">
        {{ amLabels.red_employee_apple_description }}
      </p>

      <AmAlert
        v-if="alertVisibility"
        type="error"
        :show-border="true"
        :close-after="5000"
        @close="closeAlert"
        @trigger-close="closeAlert"
      >
        {{ message }}
      </AmAlert>

      <AmSelect
        v-model="appleId"
        :placeholder="amLabels.apple_calendar_placeholder"
        class="am-integration-select"
        :loading="store.getters['auth/getAppleLoading']"
        clearable
      >
        <el-option
          v-for="option in appleCalendarOptions"
          :key="option.value"
          :label="option.label"
          :value="option.value"
        />
      </AmSelect>

      <AmCollapse>
        <AmCollapseItem ref="appleVisibility" :side="true" :delay="500">
          <template #heading>
            {{ amLabels.apple_calendar_personal }}
          </template>
          <template #default>
            <div class="am-apple-credentials">
              <AmInput
                v-model="iCloudId"
                :disabled="isEmployeeConnectedToPersonalAppleCalendar"
                :placeholder="amLabels.apple_icloud_id"
                :label="amLabels.apple_icloud_id"
              />
              <AmInput
                v-model="appSpecificPassword"
                type="password"
                :show-password="true"
                :disabled="isEmployeeConnectedToPersonalAppleCalendar"
                :placeholder="amLabels.apple_app_specific_password"
                :label="amLabels.apple_app_specific_password"
              />
              <AmButton
                @click="
                  isEmployeeConnectedToPersonalAppleCalendar
                    ? useEmployeeAppleDisconnect(store)
                    : useEmployeeAppleConnect(store)
                "
              >
                {{
                  isEmployeeConnectedToPersonalAppleCalendar
                    ? amLabels.apple_disconnect
                    : amLabels.apple_connect
                }}
              </AmButton>
            </div>
          </template>
        </AmCollapseItem>
      </AmCollapse>
    </div>
    <!-- /Content -->
  </div>
</template>

<script setup>
import { computed, inject, ref, onMounted } from 'vue'
import { useStore } from 'vuex'
import AmButton from '../../../../../../_components/button/AmButton.vue'
import AmInput from '../../../../../../_components/input/AmInput.vue'
import AmCollapse from '../../../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../../../_components/collapse/AmCollapseItem.vue'
import AmAlert from '../../../../../../_components/alert/AmAlert.vue'
import AmSelect from '../../../../../../_components/select/AmSelect.vue'
import {
  isEmployeeConnectedToPersonalAppleCalendar,
  useAppleSync,
} from '../../../../../../../assets/js/common/integrationApple'
import httpClient from '../../../../../../../plugins/axios'

const emit = defineEmits(['back'])

const props = defineProps({
  showBack: {
    type: Boolean,
    default: true,
  },
})

const store = useStore()
const amSettings = inject('settings')
const amLabels = inject('amLabels')

const message = ref('')
const alertVisibility = ref(false)

const handleBack = () => {
  emit('back')
}

function closeAlert() {
  alertVisibility.value = false
  message.value = ''
}

const appleCalendarOptions = computed(() => {
  let calendars = store.getters['auth/getAppleCalendars']
  if (calendars.length) {
    return calendars.map((calendar) => {
      return {
        value: calendar.id,
        label: calendar.name,
      }
    })
  }
  return []
})

const appleVisibility = ref(null)

const iCloudId = computed({
  get: () => store.getters['employee/getEmployeeAppleCalendarICloudId'],
  set: (val) => {
    store.commit('employee/setEmployeeAppleCalendarICloudId', val ? val : '')
  },
})

const appSpecificPassword = computed({
  get: () =>
    store.getters['employee/getEmployeeAppleCalendarAppSpecificPassword'],
  set: (val) => {
    store.commit(
      'employee/setEmployeeAppleCalendarAppSpecificPassword',
      val ? val : null
    )
  },
})

const appleId = computed({
  get: () => store.getters['employee/getAppleCalendarId'],
  set: (val) => {
    store.commit('employee/setAppleCalendarId', val ? val : '')
  },
})

onMounted(() => {
  if (amSettings.appleCalendar.enabled && appleVisibility.value) {
    appleVisibility.value.contentVisibility =
      isEmployeeConnectedToPersonalAppleCalendar.value
  }
})

function useEmployeeAppleConnect(store) {
  const data = {
    iCloudId: store.getters['employee/getEmployeeAppleCalendarICloudId'],
    appSpecificPassword:
      store.getters['employee/getEmployeeAppleCalendarAppSpecificPassword'],
  }

  httpClient
    .post('/apple/connect/' + store.getters['employee/getId'], {
      employeeAppleCalendar: data,
    })
    .then(() => {
      store.commit('employee/setEmployeeAppleCalendarICloudId', data.iCloudId)
      store.commit(
        'employee/setEmployeeAppleCalendarAppSpecificPassword',
        data.appSpecificPassword
      )
      useAppleSync(store)
    })
    .catch((error) => {
      alertVisibility.value = true
      message.value = error.response?.data?.message ?? 'An error occurred'
    })
}

function useEmployeeAppleDisconnect(store) {
  store.commit('auth/setAppleLoading', true)
  httpClient
    .post('/apple/disconnect-employee/' + store.getters['employee/getId'])
    .then(() => {
      store.commit('employee/setEmployeeAppleCalendarICloudId', null)
      store.commit('employee/setEmployeeAppleCalendarAppSpecificPassword', null)
      isEmployeeConnectedToPersonalAppleCalendar.value = false
      useAppleSync(store)
    })
    .catch((error) => {
      console.log(error)
      alertVisibility.value = true
      message.value =
        error.response?.data?.message ?? 'An error occurred during disconnect'
    })
    .finally(() => {
      store.commit('auth/setAppleLoading', false)
    })
}
</script>

<style lang="scss">
@mixin am-apple-integration {
  .am-apple-integration {
    display: flex;
    flex-direction: column;
    gap: 24px;

    &__header {
      display: flex;
      align-items: flex-start;
      padding-bottom: 16px;
      border-bottom: 1px solid $shade-200;
      width: 100%;

      &-back {
        display: flex;
        width: 100%;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        color: $shade-900;
        font-size: 16px;
        font-weight: 500;

        &:hover {
          opacity: 0.8;
        }
      }

      &-title {
        color: $shade-900;
        font-size: 15px;
        font-style: normal;
        font-weight: 500;
        line-height: 20px;
      }
    }

    &__content {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    &__description {
      font-size: 14px;
      font-weight: 400;
      line-height: 20px;
      color: $shade-650;
    }

    .am-integration-select {
      width: 100%;
    }

    .am-apple-credentials {
      display: flex;
      flex-direction: column;
      width: 100%;
      gap: 12px;
      padding: 16px;
      border-radius: 8px;
      background-color: rgba(26, 44, 55, 0.03);
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-apple-integration;
}
</style>
