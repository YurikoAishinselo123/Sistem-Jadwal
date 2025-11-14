<template>
  <div class="flex flex-col gap-2">
    <label class="text-sm font-medium text-gray-900">{{ label }}</label>
    <div class="relative">
      <select
        :value="modelValue"
        @change="handleChange"
        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700"
      >
        <option value="" class="text-gray-400">{{ placeholder }}</option>
        <option v-for="(opt, idx) in options" :key="idx" :value="opt">
          {{ opt }}
        </option>
      </select>
      <svg
        class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label: string
  modelValue: string
  options: string[]
  placeholder: string
}>()

const emit = defineEmits(['update:modelValue'])

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}
</script>
