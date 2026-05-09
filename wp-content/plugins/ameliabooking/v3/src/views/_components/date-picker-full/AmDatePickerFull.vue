<template>
  <!-- Date Picker -->
  <el-popover
    ref="popoverRef"
    :visible="visible"
    popper-class="am-popover-calendar"
    placement="bottom"
    :width="'100%'"
    :popper-style="cssPopVars"
    :disabled="props.disabled"
    :persistent="props.persistent"
    :show-arrow="false"
    trigger="click"
    @after-enter="reRenderCalendar"
  >
    <template #reference>
      <div ref="inputWrapperRef">
        <AmInput
          v-model="selectedDate"
          class="am-dp__input"
          :class="{'am-dp__input-focused': visible}"
          :disabled="props.disabled"
          :prefix-icon="calendarIcon"
          :readonly="props.readonly"
          :clearable="props.clearable"
          :placeholder="inputPlaceholder"
          :aria-label="ariaLabel"
          :aria-expanded="visible"
          aria-haspopup="dialog"
          @click="selectCalendar"
          @clear="clearCalendar"
          @keydown="handleInputKeydown"
        />
      </div>
    </template>
    <div
      class="am-dp__wrapper"
      role="dialog"
      aria-modal="true"
      :aria-label="ariaLabel"
      @keydown="handleCalendarKeydown"
      @focusin="handleCalendarFocusin"
    >
      <FullCalendar
        ref="popCalendarRef"
        v-click-outside="onClickOutside"
        class="am-dp"
        :options="options"
      />
    </div>
  </el-popover>
  <!-- /Date Picker -->
</template>

<script setup>
// * Libraries
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import allLocales from "@fullcalendar/core/locales-all";
import moment from "moment";

// * Import from Vue
import {
  onBeforeMount,
  onMounted,
  computed,
  inject,
  ref,
  nextTick,
  watch,
} from "vue";

// * Sttings
import { shortLocale } from "../../../plugins/settings";

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";
import { getFrontedFormattedDate } from "../../../assets/js/common/date";

// * _components
import AmInput from "../input/AmInput";
import IconComponent from "../icons/IconComponent.vue";

// * Import from Element Plus
import { ClickOutside as vClickOutside } from "element-plus";

/**
 * Component Props
 */
const props = defineProps({
  initialView: {
    type: String,
    default: 'dayGridMonth'
  },
  weekDaysVisibility: {
    type: Boolean,
    default: true
  },
  calendarMinimumDate: {
    type: String,
    default: ''
  },
  calendarMaximumDate: {
    type: String,
    default: ''
  },
  id: {
    type: Number,
    default: 0
  },
  disabled: {
    type: Boolean,
    default: true
  },
  inputPlaceholder: {
    type: String,
    default: ''
  },
  existingDate: {
    type: [String, Object],
    default: ''
  },
  persistent: {
    type: Boolean,
    default: true
  },
  weekStartsFromDay: {
    type: [String, Number],
    default: 1
  },
  refreshValue: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: true
  },
  ariaLabel: {
    type: String,
    default: 'date picker'
  }
})

let popoverRef = ref(null)
let popCalendarRef = ref(null)
let inputWrapperRef = ref(null)

const emits = defineEmits([
  'selectedDate',
  'clearDate'
])

const visible = ref(false)

let nonFormattedSelectedDate = ref(props.existingDate)

let selectedDate = ref('')

let minimumDate = ref(null)
let maximumDate = ref(null)

// * Icons
let calendarIcon = {
  components: { IconComponent },
  template: `<IconComponent icon='calendar'/>`,
}

function selectCalendar () {
  if (!props.disabled) {
    visible.value = true
  }
}

// * Keyboard: open/close calendar from the input field
function handleInputKeydown (event) {
  if (event.key === 'Enter' || event.key === ' ') {
    event.preventDefault()
    if (!props.disabled) {
      visible.value = !visible.value
    }
  } else if (event.key === 'Escape') {
    event.preventDefault()
    if (visible.value) {
      closeCalendar()
    }
  } else if (event.key !== 'Tab') {
    // Block text input for all other non-navigation keys (field is readonly)
    event.preventDefault()
  }
}

// * ARIA grid structure helpers

/**
 * Upgrades the FullCalendar day-cell table from role="presentation" to
 * role="grid" and makes sure every week row has role="row".
 * FullCalendar sets role="presentation" on its layout tables, which strips
 * all row/column context from the accessibility tree.  Setting role="grid"
 * here restores that context so screen readers can announce
 * "row 2, column 4 of 7" when a user navigates with arrow keys.
 * The grid is labelled by the visible month/year title via aria-labelledby.
 */
function initGridAriaRoles () {
  const calendarEl = popCalendarRef.value?.$el
  if (!calendarEl) return

  // Target the table that contains the day cells (.fc-daygrid-body table).
  const bodyTable = calendarEl.querySelector('.fc-daygrid-body table')
  if (bodyTable) {
    bodyTable.setAttribute('role', 'grid')

    // Label the grid with the visible month/year toolbar title
    const titleEl = calendarEl.querySelector('.fc-toolbar-title')
    if (titleEl) {
      // Assign a stable id so aria-labelledby survives month changes
      if (!titleEl.id) {
        titleEl.id = `am-dp-title-${props.id !== 0 ? props.id : 'default'}`
      }
      bodyTable.setAttribute('aria-labelledby', titleEl.id)
    }
  }

  // Explicitly set role="row" on every week row.
  // The prior role="presentation" on the ancestor table would have stripped
  // the implicit row semantics from <tr> elements; we restore them here.
  calendarEl.querySelectorAll('.fc-daygrid-body table tr').forEach(row => {
    row.setAttribute('role', 'row')
  })
}

// * Roving tabindex helpers

/**
 * Returns all day-cell <td> elements in the current calendar view.
 * Both selectable and disabled cells are included so arrow keys traverse
 * the full grid (matching the WCAG grid navigation pattern).
 */
function getDayCells () {
  const calendarEl = popCalendarRef.value?.$el
  if (!calendarEl) return []
  return Array.from(calendarEl.querySelectorAll('td.fc-day'))
}

/**
 * Moves the roving tabindex seat to `targetEl` and focuses it.
 * All other day cells are set to tabindex="-1".
 */
function setRovingFocus (targetEl) {
  getDayCells().forEach(cell => {
    cell.setAttribute('tabindex', cell === targetEl ? '0' : '-1')
  })
  targetEl.focus()
}

/**
 * Sets the roving tabindex "home" cell after a render or month navigation
 * WITHOUT stealing focus (focus stays on whichever element has it now).
 * Priority: selected day → today (non-disabled) → first non-disabled,
 * non-other-month cell → first cell.
 */
function initRovingTabindex () {
  const calendarEl = popCalendarRef.value?.$el
  if (!calendarEl) return

  const cells = getDayCells()
  if (!cells.length) return

  cells.forEach(cell => cell.setAttribute('tabindex', '-1'))

  const popCalendar = popCalendarRef.value.getApi()
  const viewType = popCalendar.currentData.currentViewType
  const selectedClass = `am-dp__${viewType}-selected`
  const disabledClass = `am-dp__${viewType}-disabled`

  const homeCell =
    calendarEl.querySelector(`td.fc-day.${selectedClass}`) ||
    calendarEl.querySelector(`td.fc-day-today:not(.${disabledClass})`) ||
    cells.find(c => !c.classList.contains(disabledClass) && !c.classList.contains('fc-day-other')) ||
    cells[0]

  if (homeCell) {
    homeCell.setAttribute('tabindex', '0')
  }
}

// * Update roving seat whenever any day cell receives focus (e.g. via mouse click)
function handleCalendarFocusin (event) {
  const dayCell = event.target.closest?.('td.fc-day')
  if (!dayCell) return

  getDayCells().forEach(cell => {
    cell.setAttribute('tabindex', cell === dayCell ? '0' : '-1')
  })
}

// * Keyboard: close calendar or navigate the day grid with arrow keys
function handleCalendarKeydown (event) {
  if (event.key === 'Escape') {
    event.preventDefault()
    event.stopPropagation()
    closeCalendar()
    return
  }

  // Arrow-key grid navigation (roving tabindex)
  const arrowKeys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown']
  if (!arrowKeys.includes(event.key)) return

  const dayCell = event.target.closest?.('td.fc-day')
  if (!dayCell) return

  event.preventDefault()

  const cells = getDayCells()
  const currentIndex = cells.indexOf(dayCell)
  if (currentIndex === -1) return

  const offsets = {
    ArrowRight: 1,
    ArrowLeft: -1,
    ArrowDown: 7,
    ArrowUp: -7,
  }
  const nextIndex = currentIndex + offsets[event.key]

  if (nextIndex >= 0 && nextIndex < cells.length) {
    setRovingFocus(cells[nextIndex])
  }
}

// * Close calendar and return focus to the input
function closeCalendar () {
  visible.value = false
  nextTick(() => {
    const input = inputWrapperRef.value?.querySelector('input')
    if (input) input.focus()
  })
}

watch(() => props.refreshValue, (newValue) => {
  if (newValue) {
    selectedDate.value = getFrontedFormattedDate(
        props.existingDate ? moment(props.existingDate).format('YYYY-MM-DD') : ''
    )
  }
})

const options = ref({
  initialDate: props.calendarMinimumDate ? moment(props.calendarMinimumDate, 'YYYY-MM-DD HH:mm').toDate() : null,
  locales: allLocales,
  locale: shortLocale,
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: props.initialView,
  dayCellDidMount: function (info) {
    // Start every cell at -1; initRovingTabindex() promotes the "home" cell to 0
    info.el.setAttribute('tabindex', '-1')
    info.el.setAttribute(
      'aria-label',
      `Select date ${info.date.toDateString()}`
    )

    // role="gridcell" (not "button") preserves row/column context in the
    // role="grid" table so screen readers can announce position information.
    // role="button" tells AT this is a standalone button and drops all
    // grid context ("row 2, column 4 of 7").
    info.el.setAttribute('role', 'gridcell')

    // Reflect initial selection and disabled state to AT
    const viewType = info.view.type
    const selectedClass = `am-dp__${viewType}-selected`
    const disabledClass  = `am-dp__${viewType}-disabled`
    info.el.setAttribute(
      'aria-selected',
      info.el.classList.contains(selectedClass) ? 'true' : 'false'
    )
    if (info.el.classList.contains(disabledClass)) {
      info.el.setAttribute('aria-disabled', 'true')
    }

    // Enhanced keyboard navigation
    info.el.addEventListener('keydown', (e) => {
      const calendarApi = info.view.calendar

      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault()

        // Use moment to format the date the same way as mouse clicks do
        // This avoids timezone issues with toISOString()
        const localDateStr = moment(info.date).format('YYYY-MM-DD')

        // Build the event object manually (as FullCalendar would)
        const dateClickEvent = {
          date: info.date,
          dateStr: localDateStr, // Use local date string instead of ISO string
          allDay: true,
          dayEl: info.el,
          jsEvent: e,
          view: info.view,
        }

        // First trigger dateClick
        calendarApi.trigger('dateClick', dateClickEvent)
      }
    })
  },
  headerToolbar: {
    start: 'title',
    center: '',
    end: 'prevYear,prev,next,nextYear'
  },
  weekends: props.weekDaysVisibility,
  views: {
    dayGridMonth: {},
  },
  slotLabelFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false
  },
  eventTimeFormat: {
    hour: 'numeric',
    minute: '2-digit',
    hour12: false
  },
  aspectRatio: 1.45,
  firstDay: props.weekStartsFromDay,
  dayMaxEvents: true,
  selectLongPressDelay: 0,
  datesSet: function () {
    // Re-establish the roving tabindex seat and ARIA grid roles after every
    // month/view change (new cells are mounted on each navigation).
    nextTick(() => {
      initRovingTabindex()
      initGridAriaRoles()
    })
  },
  dayHeaderClassNames: calendarDayHeaderClassBuilder,
  dayCellClassNames: calendarDayClassBuilder,
  dateClick: calendarDateClick
})

function calendarDayHeaderClassBuilder (data) {
  let classCollector = [`am-dp__${data.view.type}-header-cell`];

  // * Week days class
  if (data.date.getDay() === 0 || data.date.getDay() === 6) {
    classCollector.push(`am-dp__${data.view.type}-header-weekend`);
  }

  return classCollector;
}

function calendarDayClassBuilder (data) {
  let classCollector = [`am-dp__${data.view.type}-cell`]

  if (moment(data.date).isSameOrBefore(minimumDate.value) || moment(data.date).isSameOrAfter(maximumDate.value)) {
    classCollector.push(`am-dp__${data.view.type}-disabled`)
  }

  if (moment(data.date).format('YYYY-MM-DD') === moment(nonFormattedSelectedDate.value).format('YYYY-MM-DD')) {
    classCollector.push(`am-dp__${data.view.type}-selected`)
  }

  return classCollector;
}

function calendarDateClick (data) {
  const popCalendar = popCalendarRef.value.getApi()
  const popCalendarType = popCalendar.currentData.currentViewType
  const disabledDayClass = `am-dp__${popCalendarType}-disabled`
  const selectedDayClass = `am-dp__${popCalendarType}-selected`

  // Remove selected class and clear aria-selected on the previously selected cell
  popCalendar.el.querySelectorAll(`.${selectedDayClass}`).forEach(el => {
    el.classList.remove(selectedDayClass)
    el.setAttribute('aria-selected', 'false')
  })

  if (!data.dayEl.classList.contains(disabledDayClass)) {
    emits('selectedDate', data.dateStr)
    nonFormattedSelectedDate.value = data.date
    selectedDate.value = getFrontedFormattedDate(data.dateStr)
    data.dayEl.classList.add(selectedDayClass)
    data.dayEl.setAttribute('aria-selected', 'true')
    closeCalendar()
  }
}

function onClickOutside () {
  visible.value = false
}

function reRenderCalendar () {
  nextTick(() => {
    popCalendarRef.value.getApi().render()
    // Initialise roving tabindex and ARIA grid structure, then move focus to
    // the first nav button so keyboard users can Tab forward into the grid.
    nextTick(() => {
      initRovingTabindex()
      initGridAriaRoles()
      const calendarEl = popCalendarRef.value?.$el
      if (calendarEl) {
        const firstBtn = calendarEl.querySelector('.fc-button')
        if (firstBtn) firstBtn.focus()
      }
    })
  })
}

function selectDate (date) {
  if (date) {
    selectedDate.value = getFrontedFormattedDate(moment(date).format('YYYY-MM-DD'))
  }
}

function clearCalendar () {
  emits('clearDate')
  setTimeout(() => {
    const popCalendar = popCalendarRef.value.getApi()
    const popCalendarType = popCalendar.currentData.currentViewType
    const selectedDayClass = `am-dp__${popCalendarType}-selected`

    // Remove visual selection and aria-selected from all previously selected cells
    popCalendar.el.querySelectorAll(`.${selectedDayClass}`).forEach(el => {
      el.classList.remove(selectedDayClass)
      el.setAttribute('aria-selected', 'false')
    })

    popCalendar.unselect()
  }, 200)
}

// * Color Vars
let amColors = inject('amColors',  {
  amColors: {
    value: {
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
      colorCalCell: '#1246D6',
      colorCalCellText: '#1246D6',
      colorCalCellLow: '#1246D6',
      colorCalCellLowText: '#1246D6',
      colorCalCellHigh: '#1246D6',
      colorCalCellHighText: '#1246D6',
      colorCalCellSelected: '#1246D6',
      colorCalCellSelectedText: '#FFFFFF',
      colorCalCellDisabled: '#B4190F',
      colorCalCellDisabledText: '#1A2C37',
      colorBtnPrim: '#265CF2',
      colorBtnPrimText: '#FFFFFF',
      colorBtnSec: '#1A2C37',
      colorBtnSecText: '#FFFFFF',
    }
  }
})

let cssPopVars = computed(() => {
  return {
    // dpf - date picker full / nije omrazeni dpf filter iz auta :)
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-primary-op80': useColorTransparency(amColors.value.colorPrimary, 0.8),
    '--am-c-dpf-bgr': amColors.value.colorDropBgr,
    '--am-c-dpf-border': amColors.value.colorDropBorder,
    '--am-c-dpf-text': amColors.value.colorDropText,
    '--am-c-dpf-text-op60': useColorTransparency(amColors.value.colorDropText, 0.6),
    '--am-c-dpf-text-op20': useColorTransparency(amColors.value.colorDropText, 0.2),
    '--am-c-dpf-text-op10': useColorTransparency(amColors.value.colorDropText, 0.1),
  }
})

onBeforeMount(() => {
  if (props.calendarMinimumDate) {
    minimumDate.value = moment(props.calendarMinimumDate, 'YYYY-MM-DD HH:mm')
  }
  if (props.calendarMaximumDate) {
    maximumDate.value = moment(props.calendarMaximumDate, 'YYYY-MM-DD HH:mm')
  }
})

onMounted(() => {
  nextTick(() => {
    selectDate(props.existingDate)
  })
})
</script>

<script>

</script>
<style lang="scss">
//Amelia Calendar
$amCalClass: am-dp;
@mixin am-dp-block {
  .am-dp {
    margin-bottom: 4px;

    &__wrapper {
      --am-fs-dpf: 15px;
      --am-c-advsc-text: var(--am-c-main-text);
      // Calendar cell
      --am-c-dpf-cell-bgr: transparent;
      --am-c-dpf-cell-border: transparent;
      --am-c-dpf-cell-text: var(--am-c-dpf-text);

      * {
        font-family: var(--am-font-family);
        box-sizing: border-box;
      }

      // element animation
      & > div {
        $count: 5;
        @for $i from 0 through $count {
          &:nth-child(#{$i + 1}) {
            animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
            animation-fill-mode: both;
          }
        }
      }
    }

    table, tr, th {
      background-color: transparent;
      margin: 0;
    }

    &.fc {
      &-theme-standard {
        .fc {
          &-scrollgrid {
            border: none;

            &-section {
              &-header {
                table {
                  border: none;
                }
              }
            }
          }

          &-toolbar {
            margin-bottom: 8px;

            &-title {
              font-size: 20px;
              color: var(--am-c-dpf-text);
            }

            &-chunk {
              .fc-button {
                background-color: transparent;
                border: none;
                color: var(--am-c-dpf-text);
                padding: 2px 4px;
                margin: 0 4px;
                transition: color 0.3s ease-in-out;

                &:hover {
                  color: var(--am-c-dpf-text-op60);
                }

                // Visible keyboard focus ring for navigation buttons
                &:focus {
                  outline: 2px solid var(--am-c-primary);
                  outline-offset: 2px;
                  border: none;
                  box-shadow: none;
                }
              }
            }
          }
        }

        th, td {
          border: none;
        }

        // Calendar header cell
        th.#{$amCalClass} {
          // Month View
          &__dayGridMonth {
            &-header {
              &-cell {
                font-size: 16px;
                line-height: 1.5;
                color: var(--am-c-dpf-text);
                padding: 4px 6px;

                .fc-col-header-cell-cushion {
                  font-size: var(--am-fs-dpf);
                  text-transform: initial;
                  text-decoration: none;
                  line-height: 1;
                  letter-spacing: 0;
                  color: var(--am-c-dpf-text);
                  padding: 6px 0;
                  white-space: nowrap;
                }
              }
              &-weekend {
                .fc-col-header-cell-cushion {
                  color: var(--am-c-dpf-text);
                }
              }
            }
          }
        }

        // Calendar cell
        td.#{$amCalClass} {
          // Month view
          &__dayGridMonth {
            // Calendar cell
            &-cell {
              position: relative;
              border: none;
              padding: 4px 6px;

              // Calendar today day
              &.fc-day-today {
                position: relative;
                background: none;

                // Calendar cell state
                &.#{$amCalClass} {
                  // Selected cell
                  &__dayGridMonth-selected {
                    .fc-daygrid-day-frame {
                      background-color: var(--am-c-dpf-cell-bgr);
                      border-color: var(--am-c-dpf-cell-border);
                      &:after {
                        background-color: var(--am-c-dpf-cell-text);
                      }
                    }
                  }

                  // Disabled cell
                  &__dayGridMonth-disabled {
                    .fc-daygrid-day {
                      &-frame {
                        --am-c-dpf-cell-bgr: var(--am-c-dpf-text-op10);
                        --am-c-dpf-cell-border: transparent;
                        &:hover {
                          --am-c-dpf-cell-bgr: var(--am-c-dpf-text-op10);
                          --am-c-dpf-cell-border: transparent;
                          cursor: not-allowed;
                        }
                      }
                      &-number {
                        --am-c-dpf-cell-text: var(--am-c-dpf-text-op60);
                      }
                    }
                  }
                }

                .fc-daygrid-day-frame {
                  // Today marker - dot
                  &:after {
                    content: '';
                    display: block;
                    position: absolute;
                    top: 4px;
                    right: 4px;
                    width: 4px;
                    height: 4px;
                    border-radius: 50%;
                    background-color: var(--am-c-primary);
                  }
                }
              }

              // Calendar cell state
              &.#{$amCalClass} {
                // Disabled cell
                &__dayGridMonth-disabled {
                  .fc-daygrid-day {
                    &-frame {
                      --am-c-dpf-cell-bgr: var(--am-c-dpf-text-op10);
                      --am-c-dpf-cell-border: transparent;
                      &:hover {
                        --am-c-dpf-cell-bgr: var(--am-c-dpf-text-op10);
                        --am-c-dpf-cell-border: transparent;
                        cursor: not-allowed;
                      }
                    }
                    &-number {
                      --am-c-dpf-cell-text: var(--am-c-dpf-text-op60);
                    }
                  }
                }

                // Selected cell
                &__dayGridMonth-selected {
                  .fc-daygrid-day-frame {
                    --am-c-dpf-cell-text: var(--am-c-dpf-bgr);
                    --am-c-dpf-cell-bgr: var(--am-c-primary);
                    --am-c-dpf-cell-border: var(--am-c-primary);
                    &:hover {
                      --am-c-dpf-cell-bgr: var(--am-c-primary-op80);
                      --am-c-dpf-cell-border: var(--am-c-primary-op80);
                    }
                  }

                  // Not current month days
                  &.fc-day-other {
                    .fc-daygrid-day-top {
                      opacity: 1;
                    }
                  }
                }

                // Weekend days cell
                &__dayGridMonth-weekend {
                  .fc-daygrid-day {
                    &-frame {
                      background-color: transparent;
                      border-color: transparent;
                      &:hover {
                        border-color: transparent;
                        cursor: not-allowed;
                      }
                    }
                  }
                }
              }

              // Calendar inner cell items
              .fc-daygrid-day {
                // Calendar inner cell wrapper
                &-frame {
                  position: absolute;
                  width: calc(100% - 9px);
                  height: calc(100% - 5px);
                  min-height: auto;
                  top: 2px;
                  left: 4px;
                  background-color: var(--am-c-dpf-cell-bgr);
                  border: 1px solid var(--am-c-dpf-cell-border);
                  border-radius: 4px;
                  cursor: pointer;
                  &:hover {
                    --am-c-dpf-cell-bgr: var(--am-c-dpf-text);
                    --am-c-dpf-cell-text: var(--am-c-dpf-bgr);
                    --am-c-dpf-cell-border: var(--am-c-dpf-text);
                    transition: all 0.3s ease-in-out;
                  }
                }

                // Calendar date slot availability wrapper
                &-bg {
                  .fc-bg-event {
                    background: none;
                    opacity: 1;
                  }
                }

                // Inner cell date wrapper
                &-top {
                  position: absolute;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                }

                // Inner cell date holder
                &-number {
                  color: var(--am-c-dpf-cell-text);
                  line-height: 1;
                  padding: 0;
                  white-space: nowrap;
                  text-decoration: none;
                }
              }

              // Not current month days
              &.fc-day-other {
                .fc-daygrid-day-top {
                  opacity: 0.7;
                }
              }

              // Keyboard focus ring on individual day cells
              &:focus {
                outline: none;
                .fc-daygrid-day-frame {
                  outline: 2px solid var(--am-c-primary);
                  outline-offset: -2px;
                }
              }
            }
          }
        }
      }
    }
  }
}

.am-popover-calendar {
  z-index: 999999999 !important;
  &.el-popover.el-popper {
    min-width: auto;
    background-color: var(--am-c-dpf-bgr);
    border-color: var(--am-c-dpf-border);
    padding: 12px;
    max-width: 460px;
  }

  @include am-dp-block;
}

@mixin am-dp-input-block {
  .am-dp__input {
    &-focused {
      pointer-events: none;
    }
  }
}
// Public
.amelia-v2-booking #amelia-container {
  @include am-dp-input-block;
}

// Admin
#amelia-app-backend-new {
  @include am-dp-input-block;
}
</style>
