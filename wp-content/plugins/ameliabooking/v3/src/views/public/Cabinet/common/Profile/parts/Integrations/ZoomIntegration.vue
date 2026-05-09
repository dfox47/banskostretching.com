<template>
  <div class="am-zoom-integration">
    <!-- Navigation Header -->
    <div v-if="showBack" class="am-zoom-integration__header">
      <div class="am-zoom-integration__header-back" @click="handleBack">
        <span class="am-icon-arrow-big-left"></span>
        <span class="am-zoom-integration__header-title">{{
          amLabels.zoom
        }}</span>
      </div>
    </div>
    <!-- /Navigation Header -->

    <!-- Content -->
    <div class="am-zoom-integration__content">
      <p class="am-zoom-integration__description">
        {{ amLabels.red_employee_zoom_description }}
      </p>

      <AmSelect
        v-model="zoomUserId"
        :placeholder="amLabels.zoom_user_placeholder"
        class="am-integration-select"
        :disabled="!zoomOptions.length"
        clearable
      >
        <el-option
          v-for="option in zoomOptions"
          :key="option.value"
          :label="option.label"
          :value="option.value"
        />
      </AmSelect>
    </div>
    <!-- /Content -->
  </div>
</template>

<script setup>
import { computed, inject } from 'vue'
import { useStore } from 'vuex'
import AmSelect from '../../../../../../_components/select/AmSelect.vue'

const emit = defineEmits(['back'])

const props = defineProps({
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

const zoomOptions = computed(() => {
  let users = store.getters['auth/getZoomUsers']

  if (users.length) {
    return users.map((user) => {
      return {
        value: user.id,
        label: `${user.first_name} ${user.last_name} (${user.email})`,
      }
    })
  }

  return []
})

const zoomUserId = computed({
  get: () => store.getters['employee/getZoomUserId'],
  set: (val) => {
    store.commit('employee/setZoomUserId', val ? val : '')
  },
})
</script>

<style lang="scss">
@mixin am-zoom-integration {
  .am-zoom-integration {
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
  }
}

.amelia-v2-booking #amelia-container {
  @include am-zoom-integration;
}
</style>
