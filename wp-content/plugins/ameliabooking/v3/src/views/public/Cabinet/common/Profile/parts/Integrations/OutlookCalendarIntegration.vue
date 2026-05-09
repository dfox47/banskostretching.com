<template>
  <div class="am-outlook-integration">
    <!-- Navigation Header -->
    <div v-if="showBack" class="am-outlook-integration__header">
      <div class="am-outlook-integration__header-back" @click="handleBack">
        <span class="am-icon-arrow-big-left"></span>
        <span class="am-outlook-integration__header-title">{{
          amLabels.outlook_calendar
        }}</span>
      </div>
    </div>
    <!-- /Navigation Header -->

    <!-- Content -->
    <div class="am-outlook-integration__content">
      <!-- Description -->
      <p v-if="isAdminConnected" class="am-outlook-integration__description">
        {{ amLabels.red_employee_outlook_calendar_connected_by_admin }}
      </p>
      <!-- /Description -->

      <!-- Outlook Calendar Accounts -->
      <div class="am-outlook-integration__accounts">
        <IntegrationConnectBox
          v-for="(account, index) in outlookAccounts"
          :key="index"
          :title="amLabels.outlook_calendar"
          :description="
            isAdminConnected
              ? amLabels.red_employee_outlook_calendar_connected_by_admin
              : account.token
              ? getAccountPrimaryCalendarName(account)
              : amLabels.red_employee_outlook_description
          "
          :is-connected="!!account.token"
          :is-connect-button-needed="true"
          icon-name="outlook-calendar"
          :is-disabled="isAdminConnected"
          :on-connect="() => useOutlookConnect(store)"
          :on-disconnect="() => handleDisconnect(account)"
        />
        <!-- Add Another Account -->
        <div
          v-if="!isAdminConnected && hasAnyOutlookConnection"
          class="am-outlook-integration__add-account"
        >
          <AmButton
            type="plain"
            category="secondary"
            :disabled="outlookAccounts.length >= 6"
            @click="addAnotherAccount"
          >
            <span class="am-icon-plus" />
            {{ amLabels.add_another_account }}
          </AmButton>
        </div>
        <!-- /Add Another Account -->
      </div>
      <!-- /Outlook Calendar Accounts -->

      <!-- Configuration Section -->
      <div
        v-if="!isAdminConnected && hasAnyOutlookConnection"
        class="am-outlook-integration__config"
      >
        <!-- Sync With -->
        <div class="am-outlook-integration__field">
          <label class="am-outlook-integration__label">{{
            amLabels.red_sync_with
          }}</label>
          <AmSelect
            v-model="outlookId"
            :placeholder="amLabels.select_calendar"
            :disabled="!hasAnyOutlookConnection"
            :loading="store.getters['auth/getOutlookLoading']"
            filterable
            :filter-method="(query) => filterCalendar(query, 'sync')"
          >
            <AmOption
              v-for="calendar in filteredSyncCalendars"
              :key="calendar.id"
              :label="calendar.name || calendar.summary"
              :value="calendar.id"
            />
          </AmSelect>
        </div>
        <!-- /Sync With -->

        <div class="am-outlook-integration__config__divider"></div>

        <!-- Block Time From Calendars -->
        <div class="am-outlook-integration__blocked-section">
          <div class="am-outlook-integration__section-title">
            {{ amLabels.block_time_from_calendars }}
          </div>

          <div
            v-for="(blockedCalendarId, index) in blockedCalendars"
            :key="index"
            class="am-outlook-integration__blocked-item"
          >
            <div
              class="am-outlook-integration__field am-outlook-integration__field--grow"
            >
              <AmSelect
                :model-value="blockedCalendarId"
                :placeholder="amLabels.calendar_name"
                filterable
                :filter-method="(query) => filterCalendar(query, index)"
                @update:model-value="updateBlockedCalendar(index, $event)"
              >
                <AmOption
                  v-for="calendar in getFilteredAvailableCalendars(index)"
                  :key="calendar.id"
                  :label="calendar.name || calendar.summary"
                  :value="calendar.id"
                />
              </AmSelect>
            </div>

            <AmButton
              type="plain"
              category="danger"
              :icon="iconBucket"
              icon-only
              @click="removeBlockedCalendar(index)"
            />
          </div>

          <div class="am-outlook-integration__add-button">
            <AmButton
              type="plain"
              category="secondary"
              :disabled="blockedCalendars.length >= 6"
              @click="addBlockedCalendar"
            >
              <span class="am-icon-plus" />
              {{ amLabels.add_calendar }}
            </AmButton>
          </div>
        </div>
        <!-- /Block Time From Calendars -->
      </div>
      <!-- /Configuration Section -->
    </div>
    <!-- /Content -->
  </div>
</template>

<script setup>
import { computed, inject, ref, watch } from 'vue'
import { useStore } from 'vuex'
import AmButton from '../../../../../../_components/button/AmButton.vue'
import AmSelect from '../../../../../../_components/select/AmSelect.vue'
import IntegrationConnectBox from './IntegrationConnectBox.vue'
import {
  useOutlookConnect,
  useOutlookDisconnect,
} from '../../../../../../../assets/js/common/integrationOutlook'
import IconComponent from '../../../../../../_components/icons/IconComponent.vue'
import AmOption from '../../../../../../_components/select/AmOption.vue'

const emit = defineEmits(['back'])

defineProps({
  showBack: {
    type: Boolean,
    default: true,
  },
})

const store = useStore()
const amLabels = inject('amLabels')

const handleBack = () => {
  emit('back')
}

const isAdminConnected = computed(() => {
  return !!store.getters['employee/getUserTableOutlookCalendarId']
})

const outlookAccounts = computed(() => {
  const accounts = store.getters['employee/getOutlookCalendarAccounts'] || []
  if (accounts.length > 0) {
    return accounts
  }
  return [
    {
      id: null,
      token: null,
      calendarList: [],
      calendarId: null,
      blockedCalendars: [],
    },
  ]
})

const hasAnyOutlookConnection = computed(() => {
  const accounts = store.getters['employee/getOutlookCalendarAccounts'] || []
  return accounts.some((account) => !!account.token)
})

const allCalendars = computed(() => {
  const calendars = []
  const usedCalendarIds = new Set()

  const accounts = store.getters['employee/getOutlookCalendarAccounts'] || []
  if (accounts.length > 0) {
    accounts.forEach((account) => {
      if (account.calendarList && account.calendarList.length > 0) {
        account.calendarList.forEach((calendar) => {
          if (!usedCalendarIds.has(calendar.id)) {
            usedCalendarIds.add(calendar.id)
            calendars.push(calendar)
          }
        })
      }
    })
  }

  const calendarList = store.getters['auth/getOutlookCalendars'] || []
  if (calendars.length === 0 && calendarList.length > 0) {
    calendarList.forEach((calendar) => {
      const calId = calendar.calendarId || calendar.id
      if (!usedCalendarIds.has(calId)) {
        usedCalendarIds.add(calId)
        calendars.push({
          id: calId,
          name: calendar.name,
          summary: calendar.name,
        })
      }
    })
  }

  return calendars
})

const outlookId = computed({
  get: () => {
    return store.getters['employee/getOutlookToken']
      ? store.getters['employee/getOutlookCalendarId']
      : store.getters['employee/getUserTableOutlookCalendarId']
  },
  set: (val) => {
    if (store.getters['employee/getOutlookToken']) {
      store.commit('employee/setOutlookCalendarId', val || '')
    } else {
      store.commit('employee/setEmployeeOutlookCalendarId', val || '')
    }
  },
})

const blockedCalendars = computed({
  get: () => store.getters['employee/getOutlookCalendarBlockedCalendars'] || [],
  set: (val) =>
    store.commit('employee/setOutlookCalendarBlockedCalendars', val),
})

const handleDisconnect = (account) => {
  useOutlookDisconnect(store, account.id)
}

const addAnotherAccount = () => {
  useOutlookConnect(store)
}

let iconBucket = {
  components: { IconComponent },
  template: `<IconComponent icon="bucket"/>`,
}

const addBlockedCalendar = () => {
  const current = blockedCalendars.value || []
  store.commit('employee/setOutlookCalendarBlockedCalendars', [...current, ''])
}

const removeBlockedCalendar = (index) => {
  const current = [...blockedCalendars.value]
  current.splice(index, 1)
  store.commit('employee/setOutlookCalendarBlockedCalendars', current)
}

const updateBlockedCalendar = (index, value) => {
  const current = [...blockedCalendars.value]
  current[index] = value
  store.commit('employee/setOutlookCalendarBlockedCalendars', current)
}

const getAvailableCalendarsForBlockedCalendar = (currentIndex) => {
  const blocked = blockedCalendars.value || []
  const selectedIds = blocked.filter((id, idx) => idx !== currentIndex && id)

  const selectedSet = new Set(selectedIds.map((id) => String(id)))

  if (outlookId.value) {
    selectedSet.add(String(outlookId.value))
  }

  return allCalendars.value.filter(
    (calendar) => !selectedSet.has(String(calendar.id))
  )
}

const getAccountPrimaryCalendarName = (account) => {
  if (account.calendarList && account.calendarList.length > 0) {
    const primaryCalendar = account.calendarList.find(
      (calendar) => calendar.owner || calendar.primary
    )
    return (
      primaryCalendar?.owner ||
      primaryCalendar?.name ||
      primaryCalendar?.summary
    )
  }
  return undefined
}

const removeSyncCalendarFromBlocked = (calendarId) => {
  const current = blockedCalendars.value || []
  if (!current.length) return
  const idStr = String(calendarId)
  const filtered = current.filter((blockedId) => String(blockedId) !== idStr)
  if (filtered.length !== current.length) {
    store.commit('employee/setOutlookCalendarBlockedCalendars', filtered)
  }
}

// * Filtering
const calendarFilterQuery = ref({})

const filteredSyncCalendars = computed(() => {
  const query = calendarFilterQuery.value['sync'] || ''
  if (!query) return allCalendars.value
  return allCalendars.value.filter((calendar) =>
    (calendar.name || calendar.summary || '')
      .toLowerCase()
      .includes(query.toLowerCase())
  )
})

const getFilteredAvailableCalendars = (index) => {
  const available = getAvailableCalendarsForBlockedCalendar(index)
  const query = calendarFilterQuery.value[index] || ''
  if (!query) return available
  return available.filter((calendar) =>
    (calendar.name || calendar.summary || '')
      .toLowerCase()
      .includes(query.toLowerCase())
  )
}

const filterCalendar = (query, key) => {
  calendarFilterQuery.value = { ...calendarFilterQuery.value, [key]: query }
}

watch(
  () => outlookId.value,
  (newCalendarId) => {
    if (newCalendarId) removeSyncCalendarFromBlocked(newCalendarId)
  }
)
</script>

<style lang="scss">
@mixin am-outlook-integration {
  .am-outlook-integration {
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
      gap: 24px;
    }

    &__description {
      font-size: 14px;
      font-weight: 400;
      line-height: 20px;
      color: $shade-650;
    }

    &__accounts {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    &__add-account {
      display: flex;
      justify-content: flex-start;

      .am-button {
        .am-icon-plus {
          font-size: 24px;
        }
      }
    }

    &__config {
      display: flex;
      flex-direction: column;
      gap: 24px;
      border-radius: 4px;
      background: $blue-300;
      padding: 12px 16px;

      &__divider {
        height: 1px;
        background: $shade-200;
        padding: 0 16px;
      }
    }

    &__field {
      display: flex;
      flex-direction: column;
      gap: 8px;
      width: 100%;

      &--grow {
        flex: 1;
      }
    }

    &__label {
      font-size: 14px;
      font-weight: 500;
      color: $shade-900;
    }

    &__blocked-section {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    &__section-title {
      font-size: 15px;
      font-weight: 500;
      color: $shade-900;
    }

    &__blocked-item {
      display: flex;
      gap: 12px;
      align-items: flex-end;

      .am-button {
        .am-icon-bucket {
          font-size: 24px;
        }
      }
    }

    &__add-button {
      display: flex;
      justify-content: flex-start;

      .am-button {
        .am-icon-plus {
          font-size: 24px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-outlook-integration;
}
</style>
