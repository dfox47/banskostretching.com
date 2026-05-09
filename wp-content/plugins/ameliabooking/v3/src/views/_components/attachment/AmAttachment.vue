<template>
  <el-upload
    ref="attachments"
    v-model:file-list="model"
    :class="['am-attachment', props.customClass]"
    :action="props.action"
    :multiple="props.multiple"
    :data="props.data"
    :name="props.name"
    :with-credentials="props.withCredentials"
    :show-file-list="props.showFileList"
    :drag="props.drag"
    :accept="props.accept"
    :auto-upload="props.autoUpload"
    :disabled="props.disabled"
    :limit="props.limit"
    :style="cssVars"
    :on-change="onChange"
    :on-remove="onRemove"
  >
    <AmButton
      class="am-attachment__btn"
      tabindex="-1"
      :icon-only="props.iconOnly"
      :size="props.btnSize"
      :category="props.btnCategory"
      :type="props.btnType"
      :native-type="props.btnNativeType"
      :round="props.btnRound"
      :circle="props.btnCircle"
      :loading="props.btnLoading"
      :autofocus="props.btnAutofocus"
      :prefix="props.btnPrefix"
      :suffix="props.btnSuffix"
      :icon="props.btnIcon"
      :loading-icon="props.btnLoadingIcon"
      :disabled="props.disabled"
    >
      <slot></slot>
    </AmButton>
  </el-upload>
</template>

<script setup>
// * Components
import AmButton from '../button/AmButton.vue'

// * Import from Vue
import { computed, ref } from "vue";

// * Composables
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: [String, Number],
    default: 0
  },
  modelValue: {
    type: Array
  },
  action: {
    type: String,
    default: ''
  },
  multiple: {
    type: Boolean,
    default: false
  },
  data: {
    type: [String, Object, Array, Function, Number],
  },
  name: {
    type: String,
    name: 'file'
  },
  withCredentials: {
    type: Boolean,
    default: false
  },
  showFileList: {
    type: Boolean,
    default: true
  },
  drag: {
    type: Boolean,
    default: false
  },
  accept: String,
  autoUpload: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  },
  limit: Number,
  customClass: {
    type: String,
    default: ''
  },
  iconOnly: {
    type: Boolean,
    default: false
  },
  btnSize: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  btnCategory: {
    // primary / secondary / success / warning / danger / error
    type: String,
    default: 'primary',
    validator(value) {
      return ['primary', 'secondary', 'success', 'warning', 'danger', 'error'].includes(value)
    }
  },
  btnType: {
    // filled / plain / text
    type: String,
    default: 'filled',
    validator(value) {
      return ['filled', 'plain', 'text'].includes(value)
    }
  },
  btnNativeType: {
    // button / submit / reset
    type: String,
    default: 'button',
    validator(value) {
      return ['button', 'submit', 'reset'].includes(value)
    }
  },
  btnRound: {
    type: Boolean,
    default: false
  },
  btnCircle: {
    type: Boolean,
    default: false
  },
  btnLoading: {
    type: Boolean,
    default: false
  },
  btnAutofocus: {
    type: Boolean,
    default: false
  },
  btnPrefix: {
    type: [String, Object, Function],
    default: ''
  },
  btnSuffix: {
    type: [String, Object, Function],
    default: ''
  },
  btnIcon: {
    type: [String, Object, Function],
    default: ''
  },
  btnLoadingIcon: {
    type: [String, Object, Function],
    default: ''
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['change', 'remove', 'update:modelValue'])

// * Component model
let model = computed({
  get: () => props.modelValue,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

const attachments = ref(null)

/**
 * Component Event Handlers
 */
function onChange (uploadFile, uploadFiles) {
  let file = {
    id: props.id,
    raw: uploadFiles
  }

  emits('change', file)
}

function onRemove (removedFile, arrayOfFiles) {
  let file = {
    id: props.id,
    raw: arrayOfFiles
  }

  emits('remove', file)
}

// * Colors
let amColors = inject('amColors', ref({
  colorPrimary: '#1246D6',
  colorSuccess: '#019719',
  colorError: '#B4190F',
  colorWarning: '#CCA20C',
  colorMainBgr: '#FFFFFF',
  colorMainHeadingText: '#33434C',
  colorMainText: '#1A2C37',
  colorSbBgr: '#17295A',
  colorSbText: '#FFFFFF',
  colorInpBgr: '#FFFFFF',
  colorInpBorder: '#D1D5D7',
  colorInpText: '#1A2C37',
  colorInpPlaceHolder: '#1A2C37',
  colorDropBgr: '#FFFFFF',
  colorDropBorder: '#D1D5D7',
  colorDropText: '#0E1920',
  colorBtnPrim: '#265CF2',
  colorBtnPrimText: '#FFFFFF',
  colorBtnSec: '#1A2C37',
  colorBtnSecText: '#FFFFFF',
}))

let cssVars = computed(() => {
  return {
    '--am-c-btn-op30': useColorTransparency(amColors.value.colorBtnPrim, 0.3)
  }
})
</script>

<style lang="scss">
.amelia-v2-booking {
  #amelia-container {
    .am-attachment {
      // -atf- attachment file
      --am-c-atf-text: var(--am-c-main-text);
      --am-c-atf-bgr: var(--am-c-main-bgr);
      max-width: 100%;
      width: 100%;

      &__btn {
        width: 100%;
        margin: 0;

        &.is-disabled {
          cursor: not-allowed;
        }
      }

      .el-upload {
        width: 100%;

        &:focus {
          border-radius: 6px;
          box-shadow: 0 0 0 3px var(--am-c-btn-op30);
        }

        &-list {
          &__item {
            width: 100%;
            margin: 4px 0;
            background-color: var(--am-c-atf-bgr);

            &:hover {
              --am-c-atf-bgr: var(--am-c-inp-bgr);
              --am-c-atf-text: var(--am-c-inp-text);
            }

            &-name {
              max-width: 200px;
              padding: 4px;
              margin: 0 24px 0 0;
              color: var(--am-c-atf-text);

              * {
                color: var(--am-c-atf-text);
              }
            }

            &-status-label {
              top: 7px;
              i {
                color: var(--am-c-primary);
              }
            }

            .el-icon--close {
              color: var(--am-c-atf-text);
            }

            .el-progress {
              display: none;
            }
          }
        }
      }
    }
  }
}
</style>
