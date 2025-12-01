<template>
  <div class="mt-6">
    <label class="block text-gray-700 text-sm font-medium mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <input
        :type="isPasswordVisible ? 'text' : type"
        :value="modelValue"
        @input="onInput"
        @blur="handleBlur"
        :placeholder="placeholder"
        :required="required"
        :class="[
          'w-full px-4 py-3 pr-12 rounded-lg border text-black outline-none transition text-sm',
          error
            ? 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500'
            : 'border-gray-300 focus:ring-2 focus:ring-[#0057A3] focus:border-[#0057A3]',
        ]"
      />
      <!-- Toggle Password Button -->
      <button
        v-if="type === 'password'"
        type="button"
        @click="togglePassword"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"
      >
        <span class="material-icons text-base">
          {{ isPasswordVisible ? 'visibility_off' : 'visibility' }}
        </span>
      </button>
    </div>
    <!-- Error Message -->
    <p v-if="error" class="text-red-500 text-xs mt-1">
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  modelValue: string
  label: string
  type?: string
  placeholder: string
  required?: boolean
  errorMessage?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'validation-error': [hasError: boolean]
}>()

const isPasswordVisible = ref(false)
const touched = ref(false)

const error = computed(() => {
  if (!touched.value) return ''

  // Check if field is required and empty
  if (props.required && (!props.modelValue || props.modelValue.trim() === '')) {
    return props.errorMessage || `${props.label} wajib diisi`
  }

  // Validate email format if type is email and field has value
  if (props.type === 'email' && props.modelValue && props.modelValue.trim() !== '') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(props.modelValue)) {
      return 'Format email tidak valid'
    }
  }

  return ''
})

const togglePassword = () => {
  isPasswordVisible.value = !isPasswordVisible.value
}

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement | null
  if (target) {
    emit('update:modelValue', target.value)
    // Emit validation status
    if (touched.value) {
      const isEmpty = !target.value.trim() && !!props.required
      const isInvalidEmail =
        props.type === 'email' &&
        target.value.trim() !== '' &&
        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(target.value)
      emit('validation-error', isEmpty || isInvalidEmail)
    }
  }
}

const handleBlur = () => {
  touched.value = true
  const isEmpty = !props.modelValue.trim() && !!props.required
  const isInvalidEmail =
    props.type === 'email' &&
    props.modelValue.trim() !== '' &&
    !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(props.modelValue)
  emit('validation-error', isEmpty || isInvalidEmail)
}

// Expose validate method for parent component
defineExpose({
  validate: () => {
    touched.value = true
    const isEmpty = !props.modelValue.trim() && !!props.required
    const isInvalidEmail =
      props.type === 'email' &&
      props.modelValue.trim() !== '' &&
      !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(props.modelValue)
    const hasError = isEmpty || isInvalidEmail
    emit('validation-error', hasError)
    return !hasError
  },
})
</script>
