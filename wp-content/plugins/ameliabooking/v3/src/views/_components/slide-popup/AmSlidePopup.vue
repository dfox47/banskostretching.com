<template>
  <transition duration="550" name="nested">
    <div
      v-show="visibility"
      class="am-slide-popup__block"
      :class="`am-position-${position}`"
      :style="{...cssVars, ...customCss}"
    >
      <div
        ref="dialogRef"
        v-click-outside="onClickOutside"
        class="am-slide-popup__block-inner"
        :class="[
          {'am-slide-popup__up-inner-mobile': checkScreen},
          customClass,
          `am-position-${position}`
        ]"
        role="dialog"
        aria-modal="true"
        :aria-label="props.ariaLabel || undefined"
        :aria-labelledby="props.ariaLabelledby || undefined"
        tabindex="-1"
        @keydown.tab="onTab"
        @keydown.esc="onEscape"
      >
        <div v-if="popupHeaderVisibility" class="am-slide-popup__block-header">
          <slot name="header"></slot>
          <span
            class="am-icon-close"
            tabindex="0"
            role="button"
            aria-label="Close"
            @click="emits('update:visibility', false)"
            @keydown.enter="emits('update:visibility', false)"
            @keydown.space.prevent="emits('update:visibility', false)"
          />
        </div>
        <slot></slot>
        <div v-if="props.footerVisibility" class="am-slide-popup__block-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  computed,
  useSlots,
  ref,
  watch,
  nextTick,
  onMounted,
} from 'vue';

// * Import from Libraries
import { ClickOutside as vClickOutside } from "element-plus";

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

// * Components Props
let props = defineProps({
  visibility: {
    type: Boolean,
    default: false,
    required: true
  },
  customClass: {
    type: String,
    default: ''
  },
  position: {
    type: String,
    default: 'bottom',
    validator(value) {
      return ['bottom', 'top', 'left', 'right', 'center'].includes(value)
    }
  },
  closeOutside: {
    type: Boolean,
    default: false
  },
  customCss: {
    type: Object,
    default: () => {}
  },
  footerVisibility: {
    type: Boolean,
    default: true
  },
  // Accessible name for the dialog — provide one of these two props.
  // ariaLabel: plain string label (e.g. "Filter options").
  // ariaLabelledby: id of a visible heading element inside the dialog.
  ariaLabel: {
    type: String,
    default: ''
  },
  ariaLabelledby: {
    type: String,
    default: ''
  },
})

// * Compomnets Emits
let emits = defineEmits(['click-outside', 'update:visibility'])

// ---------------------------------------------------------------------------
// Focus management
// ---------------------------------------------------------------------------

// The dialog element itself — used as a programmatic focus target fallback
const dialogRef = ref(null)

// The element that had focus before the dialog opened; restored on close
let triggerEl = null

/**
 * Returns every keyboard-reachable element inside the dialog.
 * Elements that are hidden (display:none / hidden attribute) are excluded.
 */
function getFocusableElements () {
  if (!dialogRef.value) return []
  return Array.from(
    dialogRef.value.querySelectorAll(
      'a[href], area[href], ' +
      'input:not([disabled]):not([type="hidden"]), ' +
      'select:not([disabled]), ' +
      'textarea:not([disabled]), ' +
      'button:not([disabled]), ' +
      '[tabindex]:not([tabindex="-1"]), ' +
      '[contenteditable]'
    )
  ).filter(el => !el.hasAttribute('hidden') && !el.closest('[hidden]'))
}

/** Moves focus to the first interactive child, or the dialog container itself. */
function focusFirstElement () {
  const focusable = getFocusableElements()
  if (focusable.length) {
    focusable[0].focus()
  } else {
    dialogRef.value?.focus()
  }
}

/**
 * Focus trap — keeps Tab / Shift+Tab cycling inside the dialog.
 * Attached to @keydown.tab on the dialog container so it catches the event
 * wherever focus currently sits inside the dialog.
 */
function onTab (event) {
  const focusable = getFocusableElements()
  if (!focusable.length) {
    event.preventDefault()
    return
  }

  const first = focusable[0]
  const last  = focusable[focusable.length - 1]

  if (event.shiftKey) {
    // Shift+Tab from the first focusable (or the container): wrap to last
    if (document.activeElement === first || document.activeElement === dialogRef.value) {
      event.preventDefault()
      last.focus()
    }
  } else {
    // Tab from the last focusable: wrap to first
    if (document.activeElement === last) {
      event.preventDefault()
      first.focus()
    }
  }
}

/** Escape closes the dialog (standard dialog keyboard pattern). */
function onEscape () {
  emits('update:visibility', false)
}

// Save the triggering element when the dialog opens; restore it when it closes
watch(() => props.visibility, (isVisible) => {
  if (isVisible) {
    triggerEl = document.activeElement
    nextTick(focusFirstElement)
  } else {
    nextTick(() => {
      triggerEl?.focus()
      triggerEl = null
    })
  }
})

// Handle the case where the popup is already open at mount time
onMounted(() => {
  if (props.visibility) {
    nextTick(focusFirstElement)
  }
})

const slots = useSlots()

// * Click outside of menu
function onClickOutside () {
  emits('click-outside')
  if (props.closeOutside) {
    emits('update:visibility', false)
  }
}

// * Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 460 || (cWidth.value > 560 && cWidth.value - 240 < 460))

let popupHeaderVisibility = computed(() => {
  return !!slots.header?.()
})

// * Components Colors
let amColors = inject('amColors');

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-spb-bgr': amColors.value.colorMainBgr,
    '--am-c-spb-text': amColors.value.colorMainText,
    '--am-c-spb-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1)
  }
})
</script>

<style lang="scss">
@mixin am-slide-popup {
  .am-slide-popup {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
  }

  .am-slide-popup__block {
    $classP: '.am-slide-popup__block';
    @extend .am-slide-popup;
    height: 100%;
    padding: 0;
    //background: rgba(4, 8, 11, 0.3);
    background-color: var(--am-c-spb-text-op10);
    z-index: 1000;

    * {
      box-sizing: border-box;
    }

    &-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0 24px;
      background: var(--am-c-spb-bgr);
      color: var(--am-c-spb-text);
      padding: 0;

      .am-icon-close {
        color: var(--am-c-spb-text);
        font-size: 20px;
        cursor: pointer;
        flex: 0 0 auto;

        &:focus {
          box-shadow: 0 0 0 1px var(--am-c-primary);
          border-radius: 4px;
        }
      }
    }

    &-footer {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 0 8px;

      .am-fs__ps-popup__btn-mobile {
        width: 100%;
        display: flex;
        justify-content: space-between;
      }
    }

    &-inner {
      @extend .am-slide-popup;
      background: var(--am-c-main-bgr);
      padding: 16px 32px;

      // The container receives programmatic focus only (tabindex="-1" fallback).
      // Interactive children retain their own visible focus rings.
      &:focus {
        outline: none;
      }

      &.am-position-top {
        top: 0;
        bottom: auto;
        left: 0;
        right: auto;
        min-height: 100px;
      }

      &.am-position-right {
        top: 0;
        bottom: 0;
        left: auto;
        right: 0;
        width: auto;
      }

      &.am-position-left {
        top: 0;
        bottom: 0;
        left: 0;
        right: auto;
        width: auto;
      }

      &.am-position-bottom {
        top: auto;
        bottom: 0;
        right: auto;
        min-height: 100px;
      }

      &.am-position-center {
        top: 50%;
        bottom: auto;
        left: 50%;
        right: auto;
        transform: translate(-50%, -50%);
        width: auto;
        max-width: calc(100% - 32px);
        height: auto;
        max-height: calc(100% - 32px);
      }

      &-mobile {
        padding: 16px;

        .am-fs__ps-popup__btn-mobile {
          height: auto;
          width: 100%;
          flex-direction: column;
          gap: 8px;
          white-space: break-spaces;
        }
      }
    }

    &.nested-enter-active, &.nested-leave-active {
      transition: all 0.3s ease-in-out;

      #{$classP}-inner {
        transition: all 0.3s ease-in-out;
      }
    }

    &.nested-enter-from, &.nested-leave-to {
      opacity: 0;

      &.am-position-top {
        transform: translateY(-30px);
      }

      &.am-position-right {
        transform: translateX(30px);
      }

      &.am-position-left {
        transform: translateX(-30px);
      }

      &.am-position-bottom, &.am-position-center {
        transform: translateY(30px);
      }

      #{$classP}-inner {
        &.am-position-top {
          transform: translateY(-30px);
        }

        &.am-position-right {
          transform: translateX(30px);
        }

        &.am-position-left {
          transform: translateX(-30px);
        }

        &.am-position-bottom {
          transform: translateY(30px);
        }

        &.am-position-center {
          transform: translate(-50%, 50%);
        }
        /*
          Hack around a Chrome 96 bug in handling nested opacity transitions.
          This is not needed in other browsers or Chrome 99+ where the bug
          has been fixed.
        */
        opacity: 0.001;
      }
    }

    /* delay leave of parent element */
    &.nested-leave-active {
      transition-delay: 0.25s;
    }

    /* delay enter of nested element */
    &.nested-enter-active {
      #{$classP}-inner {
        transition-delay: 0.25s;
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-slide-popup;
}

// admin
#amelia-app-backend-new {
  @include am-slide-popup;
}
</style>