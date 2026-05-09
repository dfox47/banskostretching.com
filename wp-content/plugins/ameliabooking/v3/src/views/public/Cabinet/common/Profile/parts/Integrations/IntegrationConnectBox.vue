<template>
  <div class="am-integration-connect-box">
    <div class="am-integration-connect-box__wrapper">
      <div class="am-integration-connect-box__icon">
        <img
          :src="
            baseUrls.wpAmeliaPluginURL +
            '/v3/src/assets/img/icons/' +
            iconName +
            '.svg'
          "
          height="30"
          alt=""
          aria-hidden="true"
        />
      </div>

      <div class="am-integration-connect-box__content">
        <div class="am-integration-connect-box__info">
          <div class="am-integration-connect-box__title">{{ title }}</div>
          <div class="am-integration-connect-box__description">
            {{ description }}
          </div>
        </div>

        <div
          v-if="isConnectButtonNeeded"
          class="am-integration-connect-box__actions"
        >
          <AmButton
            size="small"
            :category="isConnected ? 'danger' : 'primary'"
            :disabled="isDisabled"
            @click="!isConnected ? onConnect?.() : onDisconnect?.()"
          >
            {{
              isConnected
                ? amLabels.disconnect
                : amLabels.connect
            }}
          </AmButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject } from 'vue'
const baseUrls = inject('baseUrls')
import AmButton from '../../../../../../_components/button/AmButton.vue'

const amLabels = inject('amLabels')

defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    default: '',
  },
  isConnected: {
    type: Boolean,
    default: false,
  },
  isConnectButtonNeeded: {
    type: Boolean,
    default: true,
  },
  iconName: {
    type: String,
    required: true,
  },
  onConnect: {
    type: Function,
    default: null,
  },
  onDisconnect: {
    type: Function,
    default: null,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
})
</script>

<style lang="scss">
@mixin am-integration-connect-box {
  .am-integration-connect-box {
    width: 100%;

    &__wrapper {
      display: flex;
      gap: 16px;
      padding: 16px;
      border-radius: 8px;
      border: 1px solid $shade-200;
      background: $am-white;
    }

    &__icon {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 40px;
      height: 40px;
    }

    &__content {
      display: flex;
      width: 100%;
      justify-content: space-between;
      gap: 12px;

      @media (max-width: 768px) {
        flex-direction: column;
      }
    }

    &__info {
      display: flex;
      flex-direction: column;
      gap: 4px;
      flex: 1;
    }

    &__title {
      font-size: 16px;
      font-weight: 500;
      color: var(--am-c-main-text);
    }

    &__description {
      font-size: 14px;
      font-weight: 400;
      line-height: 20px;
      color: var(--am-c-main-text);
      opacity: 0.7;
    }

    &__actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include am-integration-connect-box;
}
</style>
