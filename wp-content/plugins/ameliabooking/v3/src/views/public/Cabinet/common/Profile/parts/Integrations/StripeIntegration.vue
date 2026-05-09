<template>
  <div class="am-stripe-integration">
    <!-- Navigation Header -->
    <div v-if="showBack" class="am-stripe-integration__header">
      <div class="am-stripe-integration__header-back" @click="handleBack">
        <span class="am-icon-arrow-big-left"></span>
        <span class="am-stripe-integration__header-title">{{
          amLabels.stripe_connect
        }}</span>
      </div>
    </div>
    <!-- /Navigation Header -->

    <!-- Content -->
    <div class="am-stripe-integration__content">
      <p class="am-stripe-integration__description">
        {{ amLabels.red_employee_stripe_description }}
      </p>

      <StripeConnect />
    </div>
    <!-- /Content -->
  </div>
</template>

<script setup>
import { inject } from 'vue'
import StripeConnect from '../StripeConnect.vue'

const emit = defineEmits(['back'])

const props = defineProps({
  showBack: {
    type: Boolean,
    default: true,
  },
})

const amLabels = inject('amLabels')

const handleBack = () => {
  emit('back')
}
</script>

<style lang="scss">
@mixin am-stripe-integration {
  .am-stripe-integration {
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
  }
}

.amelia-v2-booking #amelia-container {
  @include am-stripe-integration;
}
</style>
