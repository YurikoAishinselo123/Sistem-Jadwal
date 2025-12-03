// composables/useToast.ts
import { ref } from 'vue'

interface ToastState {
  show: boolean
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
}

const toastState = ref<ToastState>({
  show: false,
  type: 'success',
  message: '',
})

export function useToast() {
  const showToast = (
    message: string,
    type: 'success' | 'error' | 'warning' | 'info' = 'success',
  ) => {
    toastState.value = {
      show: true,
      type,
      message,
    }
  }

  const hideToast = () => {
    toastState.value.show = false
  }

  const success = (message: string) => {
    showToast(message, 'success')
  }

  const error = (message: string) => {
    showToast(message, 'error')
  }

  const warning = (message: string) => {
    showToast(message, 'warning')
  }

  const info = (message: string) => {
    showToast(message, 'info')
  }

  return {
    toastState,
    showToast,
    hideToast,
    success,
    error,
    warning,
    info,
  }
}
