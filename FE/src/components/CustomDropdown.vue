<template>
  <div class="relative" ref="dropdownRef">
    <!-- Label -->
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-600 font-bold">*</span>
    </label>

    <!-- Searchable Input -->
    <div v-if="searchable" class="relative">
      <input
        ref="inputRef"
        type="text"
        :value="open ? searchTerm : modelValue"
        @input="onInput"
        @focus="open = true"
        :placeholder="placeholder"
        class="w-full px-4 py-3 pr-12 bg-white border text-sm text-black border-gray-300 rounded-lg transition-all focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
      />
      <button
        @click="toggleDropdown"
        type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center hover:bg-gray-100 rounded p-1 transition-colors"
      >
        <svg
          :class="['w-5 h-5 text-gray-400 transition-transform', open ? 'rotate-180' : '']"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 9l-7 7-7-7"
          />
        </svg>
      </button>
    </div>

    <!-- Regular Button -->
    <button
      v-else
      @click="toggleDropdown"
      type="button"
      :class="[
        'w-full px-4 py-3 bg-white border text-sm rounded-lg text-left flex items-center justify-between transition-all focus:outline-none',
        showError
          ? 'border-red-500 focus:border-red-500 focus:ring-1 focus:ring-red-500'
          : 'border-gray-300 hover:border-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500',
      ]"
    >
      <span :class="!modelValue || modelValue === placeholder ? 'text-gray-400' : 'text-gray-700'">
        {{ modelValue || placeholder }}
      </span>
      <svg
        :class="[
          'w-5 h-5 text-gray-400 transition-transform flex-shrink-0 ml-2',
          open ? 'rotate-180' : '',
        ]"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Options -->
    <div
      v-if="open"
      class="absolute z-10 w-full mt-2 text-base bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"
    >
      <!-- Clear Option -->
      <button
        v-if="modelValue && clearable"
        @click="clearSelection"
        type="button"
        class="w-full px-4 py-3 text-left hover:bg-red-50 transition-colors text-red-600 font-medium border-b border-gray-200 flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
        {{ clearText }}
      </button>

      <!-- Regular Options -->
      <button
        v-for="(option, index) in filteredOptions"
        :key="index"
        @click="selectOption(option)"
        type="button"
        :class="[
          'w-full px-4 py-3 text-left hover:bg-blue-50 transition-colors',
          modelValue === option ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-700',
        ]"
      >
        {{ option }}
      </button>

      <div v-if="filteredOptions.length === 0" class="px-4 py-3 text-gray-500 text-center">
        Tidak ditemukan
      </div>
    </div>

    <!-- Inline Error Message -->
    <p v-if="required && showError" class="text-red-500 text-xs mt-1">{{ label }} wajib diisi!</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array as () => string[], required: true },
  placeholder: { type: String, default: 'Pilih...' },
  searchable: { type: Boolean, default: false },
  label: { type: String, default: '' },
  clearable: { type: Boolean, default: false },
  clearText: { type: String, default: 'Kosongkan pilihan' },
  required: { type: Boolean, default: false },
  showError: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const searchTerm = ref('')
const dropdownRef = ref<HTMLElement | null>(null)
const inputRef = ref<HTMLInputElement | null>(null)

const filteredOptions = computed(() => {
  if (!props.searchable) return props.options
  return props.options.filter((option) =>
    String(option).toLowerCase().includes(searchTerm.value.toLowerCase()),
  )
})

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement | null
  if (target) searchTerm.value = target.value
}

const selectOption = (option: string) => {
  emit('update:modelValue', option)
  open.value = false
  searchTerm.value = ''
}

const clearSelection = () => {
  emit('update:modelValue', '')
  open.value = false
  searchTerm.value = ''
}

const toggleDropdown = () => {
  open.value = !open.value
  if (open.value && inputRef.value) {
    setTimeout(() => inputRef.value?.focus(), 10)
  }
}

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    open.value = false
    searchTerm.value = ''
  }
}

// Watch for changes in modelValue to log them (helpful for debugging)
watch(
  () => props.modelValue,
  (newVal) => {
    console.log(`🔄 ${props.label} changed to:`, newVal)
  },
  { immediate: false },
)

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>

<style scoped>
/* Custom scrollbar for dropdown */
.max-h-60::-webkit-scrollbar {
  width: 8px;
}

.max-h-60::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.max-h-60::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
