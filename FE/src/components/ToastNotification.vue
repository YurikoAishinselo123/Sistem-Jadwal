<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue'

interface Props {
  show: boolean
  type?: 'success' | 'error' | 'warning' | 'info'
  message: string
  duration?: number
}

const props = withDefaults(defineProps<Props>(), {
  show: false,
  type: 'success',
  duration: 3000,
})

const emit = defineEmits<{
  (e: 'close'): void
}>()

const isVisible = ref(false)
let timeoutId: number | null = null

const closeToast = () => {
  isVisible.value = false
  emit('close')
}

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      isVisible.value = true

      // Auto close after duration
      if (timeoutId) {
        clearTimeout(timeoutId)
      }

      if (props.duration > 0) {
        timeoutId = window.setTimeout(() => {
          closeToast()
        }, props.duration)
      }
    } else {
      isVisible.value = false
    }
  },
)

onMounted(() => {
  if (props.show) {
    isVisible.value = true
  }
})

const getIconClass = () => {
  switch (props.type) {
    case 'success':
      return 'text-green-600'
    case 'error':
      return 'text-red-600'
    case 'warning':
      return 'text-yellow-600'
    case 'info':
      return 'text-blue-600'
    default:
      return 'text-green-600'
  }
}

const getBgClass = () => {
  switch (props.type) {
    case 'success':
      return 'bg-green-500'
    case 'error':
      return 'bg-red-500'
    case 'warning':
      return 'bg-yellow-500'
    case 'info':
      return 'bg-blue-500'
    default:
      return 'bg-green-500'
  }
}

const getIcon = () => {
  switch (props.type) {
    case 'success':
      return '✓'
    case 'error':
      return '✕'
    case 'warning':
      return '⚠'
    case 'info':
      return 'ℹ'
    default:
      return '✓'
  }
}
</script>

<template>
  <Transition
    enter-active-class="transition ease-out duration-300 transform"
    enter-from-class="translate-x-full opacity-0"
    enter-to-class="translate-x-0 opacity-100"
    leave-active-class="transition ease-in duration-200 transform"
    leave-from-class="translate-x-0 opacity-100"
    leave-to-class="translate-x-full opacity-0"
  >
    <div
      v-if="isVisible"
      :class="[
        'fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg min-w-[300px] max-w-[400px]',
        getBgClass(),
      ]"
    >
      <!-- Icon -->
      <div
        :class="[
          'flex items-center justify-center w-6 h-6 rounded-full bg-white font-bold text-sm',
          getIconClass(),
        ]"
      >
        {{ getIcon() }}
      </div>

      <!-- Message -->
      <p class="flex-1 text-white font-medium text-sm">{{ message }}</p>

      <!-- Close Button -->
      <button
        @click="closeToast"
        class="text-white hover:text-gray-200 transition-colors text-xl font-bold leading-none"
        aria-label="Close"
      >
        ×
      </button>
    </div>
  </Transition>
</template>

<style scoped>
/* Additional styles if needed */
</style>
