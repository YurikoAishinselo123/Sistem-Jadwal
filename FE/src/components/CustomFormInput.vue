<template>
  <div class="mt-6">
    <label class="block text-gray-700 text-sm font-medium mb-1">
      {{ label }}
    </label>

    <div class="relative">
      <input
        :type="isPasswordVisible ? 'text' : type"
        :value="modelValue"
        @input="onInput"
        :placeholder="placeholder"
        :required="required"
        class="w-full px-4 py-3 pr-12 rounded-lg border text-black border-gray-300 focus:ring-2 focus:ring-[#0057A3] focus:border-[#0057A3] outline-none transition text-sm"
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
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  modelValue: string
  label: string
  type?: string
  placeholder: string
  required?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const isPasswordVisible = ref(false)

const togglePassword = () => {
  isPasswordVisible.value = !isPasswordVisible.value
}

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement | null
  if (target) emit('update:modelValue', target.value)
}
</script>
