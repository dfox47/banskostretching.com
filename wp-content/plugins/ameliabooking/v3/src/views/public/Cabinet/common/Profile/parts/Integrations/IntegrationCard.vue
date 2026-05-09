<template>
  <!-- Integration Card Container -->
  <div class="am-integration-card">
    <!-- Card -->
    <div
      class="am-integration-card__wrapper"
      :class="{
        'am-integration-card__wrapper--disabled': !isAvailable,
        'am-integration-card__wrapper--connected': isConnected,
      }"
      :aria-disabled="!isAvailable"
      role="button"
      tabindex="0"
      @click="handleClick"
    >
      <!-- Card Header -->
      <div class="am-integration-card__header">
        <!-- Icon -->
        <div class="am-integration-card__icon">
          <img
            :src="
              baseUrls.wpAmeliaPluginURL +
              '/v3/src/assets/img/icons/' +
              iconName +
              '.svg'
            "
            alt=""
            aria-hidden="true"
            height="40"
          />
        </div>
        <!-- /Icon -->

        <!-- Title & Status -->
        <div class="am-integration-card__info">
          <div class="am-integration-card__title">{{ title }}</div>
          <div
            class="am-integration-card__status"
            :class="{
              'am-integration-card__status--connected': isConnected,
              'am-integration-card__status--disconnected':
                !isConnected && isAvailable,
            }"
          >
            {{ statusText }}
          </div>
        </div>
        <!-- /Title & Status -->
      </div>
      <!-- /Card Header -->

      <!-- Card Description -->
      <div class="am-integration-card__description">
        {{ description }}
      </div>
      <!-- /Card Description -->
    </div>
    <!-- /Card -->
  </div>
  <!-- /Integration Card Container -->
</template>

<script setup>
import { computed, inject } from 'vue'
const baseUrls = inject('baseUrls')

const props = defineProps({
  iconName: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
  isConnected: {
    type: Boolean,
    default: false,
  },
  isAvailable: {
    type: Boolean,
    default: true,
  },
  connectedText: {
    type: String,
    default: 'Connected',
  },
  notConnectedText: {
    type: String,
    default: 'Not Connected',
  },
})

const emit = defineEmits(['click'])

const statusText = computed(() => {
  return props.isConnected ? props.connectedText : props.notConnectedText
})

const handleClick = () => {
  if (props.isAvailable) {
    emit('click')
  }
}
</script>

<style lang="scss">
@mixin am-integration-card {
  .am-integration-card {
    display: flex;
    overflow: hidden;
    width: 100%;

    @media (min-width: 768px) {
      width: 240px;
    }

    &__wrapper {
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding: 16px;
      border-radius: 8px;
      border: 1px solid var(--am-c-inp-border);
      background: var(--am-c-main-bgr);
      transition: border 0.3s ease;
      cursor: pointer;
      width: 100%;

      &:hover:not(&--disabled) {
        border-color: var(--am-c-primary);
      }

      &--disabled {
        background: var(--am-c-main-text-op10);
        cursor: not-allowed;
        opacity: 0.6;
      }
    }

    &__header {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    &__icon {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 40px;
      height: 40px;
      font-size: 24px;
    }

    &__info {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    &__title {
      font-size: 15px;
      font-style: normal;
      font-weight: 500;
      line-height: 24px;
      color: $shade-1000;
    }

    &__status {
      font-size: 14px;
      font-weight: 400;
      line-height: 20px;

      &--connected {
        color: $green-850;
      }

      &--disconnected {
        color: $shade-650;
      }
    }

    &__description {
      color: $shade-650;
      font-size: 14px;
      font-style: normal;
      line-height: 20px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-integration-card;
}
</style>
