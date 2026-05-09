<template>
  <div v-if="!loading" class="am-caedo" :class="props.responsiveClass">
    <!-- Main Integrations List View -->
    <div v-if="currentView === 'list'" class="am-caedo__integrations-grid">
      <IntegrationCard
        v-for="integration in visibleIntegrations"
        :key="integration.key"
        :icon-name="integration.iconName"
        :title="integration.title"
        :description="integration.description"
        :is-connected="integration.isConnected"
        :is-available="integration.isAvailable"
        :connected-text="amLabels.red_connected"
        :not-connected-text="amLabels.red_not_connected"
        @click="openView(integration.key)"
      />
    </div>
    <!-- /Main Integrations List View -->

    <!-- Integration Detail Views -->
    <GoogleCalendarIntegration
      v-if="currentView === 'google'"
      :show-back="visibleIntegrations.length > 1"
      @back="backToList"
    />
    <OutlookCalendarIntegration
      v-if="currentView === 'outlook'"
      :show-back="visibleIntegrations.length > 1"
      @back="backToList"
    />
    <AppleCalendarIntegration
      v-if="currentView === 'apple'"
      :show-back="visibleIntegrations.length > 1"
      @back="backToList"
    />
    <ZoomIntegration
      v-if="currentView === 'zoom'"
      :show-back="visibleIntegrations.length > 1"
      @back="backToList"
    />
    <StripeIntegration
      v-if="currentView === 'stripe'"
      :show-back="visibleIntegrations.length > 1"
      @back="backToList"
    />
    <!-- /Integration Detail Views -->
  </div>
  <Skeleton v-else />
</template>

<script setup>
// * Import from Vue
import { computed, ref, inject, onMounted } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import Components
import Skeleton from '../../../Authentication/parts/Skeleton.vue'
import IntegrationCard from './IntegrationCard.vue'
import GoogleCalendarIntegration from './GoogleCalendarIntegration.vue'
import OutlookCalendarIntegration from './OutlookCalendarIntegration.vue'
import AppleCalendarIntegration from './AppleCalendarIntegration.vue'
import ZoomIntegration from './ZoomIntegration.vue'
import StripeIntegration from './StripeIntegration.vue'

import { isEmployeeConnectedToPersonalAppleCalendar } from '../../../../../../../assets/js/common/integrationApple'

// * Props
const props = defineProps({
  responsiveClass: {
    type: String,
    default: '',
  },
})

// * Store
const store = useStore()

// * Settings
const amSettings = inject('settings')

// * Labels
let amLabels = inject('amLabels')

let loading = computed(
  () =>
    store.getters['auth/getGoogleLoading'] ||
    store.getters['auth/getOutlookLoading'] ||
    store.getters['auth/getAppleLoading'] ||
    store.getters['auth/getStripeLoading'] ||
    store.getters['auth/getZoomLoading']
)

const currentView = ref('list')

const isStripeConnected = computed(() => {
  const stripeConnect = store.getters['employee/getStripeConnect']
  return !!(stripeConnect && (stripeConnect.id || stripeConnect.accountId))
})

const isZoomConnected = computed(() => {
  return !!store.getters['employee/getZoomUserId']
})

const isGoogleConnected = computed(() => {
  const token = !!store.getters['employee/getGoogleToken']
  const calendarId = !!store.getters['employee/getGoogleCalendarId']
  const userTableCalendarId = !!store.getters['employee/getUserTableGoogleCalendarId']
  return !!(token || calendarId || userTableCalendarId)
})

const isOutlookConnected = computed(() => {
  const token = !!store.getters['employee/getOutlookToken']
  const calendarId = !!store.getters['employee/getOutlookCalendarId']
  const userTableCalendarId = !!store.getters['employee/getUserTableOutlookCalendarId']
  return !!(token || calendarId || userTableCalendarId)
})

const isAppleConnected = computed(() => {
  return (
    isEmployeeConnectedToPersonalAppleCalendar.value ||
    !!store.getters['employee/getAppleCalendarId']
  )
})

const integrations = computed(() => [
  {
    key: 'google',
    iconName: 'google-calendar',
    title: amLabels.google_calendar,
    description: amLabels.red_employee_google_description,
    isConnected: isGoogleConnected.value,
    isAvailable: !!amSettings.googleCalendar.enabled,
  },
  {
    key: 'outlook',
    iconName: 'outlook-calendar',
    title: amLabels.outlook_calendar,
    description: amLabels.red_employee_outlook_description,
    isConnected: isOutlookConnected.value,
    isAvailable: !!amSettings.outlookCalendar.enabled,
  },
  {
    key: 'apple',
    iconName: 'apple-calendar',
    title: amLabels.apple_calendar,
    description: amLabels.red_employee_apple_description,
    isConnected: isAppleConnected.value,
    isAvailable: !!amSettings.appleCalendar.enabled,
  },
  {
    key: 'zoom',
    iconName: 'zoom',
    title: amLabels.zoom,
    description: amLabels.red_employee_zoom_description,
    isConnected: isZoomConnected.value,
    isAvailable: !!amSettings.zoom.enabled,
  },
  {
    key: 'stripe',
    iconName: 'stripe',
    title: amLabels.stripe_connect,
    description: amLabels.red_employee_stripe_description,
    isConnected: isStripeConnected.value,
    isAvailable:
      !!amSettings.payments.stripe.enabled &&
      amSettings.payments.stripe.connect.enabled,
  },
])

const visibleIntegrations = computed(() => {
  return integrations.value.filter((integration) => integration.isAvailable)
})

const initCurrentView = () => {
  const visible = visibleIntegrations.value
  if (visible.length === 1) {
    currentView.value = visible[0].key
  }
}

const openView = (integrationKey) => {
  currentView.value = integrationKey
}

const backToList = () => {
  currentView.value = 'list'
  initCurrentView()
}

onMounted(() => {
  initCurrentView()
})
</script>

<style lang="scss">
@mixin am-cabinet-profile {
  .am-caedo {
    // Integrations Grid
    &__integrations-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;

      @media (max-width: 768px) {
        flex-direction: column;
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-cabinet-profile;
}
</style>
