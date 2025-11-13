<template>
  <div class="relative" ref="dropdownRef">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
    </label>

    <!-- Searchable Input -->
    <div v-if="searchable" class="relative">
      <input
        ref="inputRef"
        type="text"
        :value="open ? searchTerm : modelValue"
        @input="searchTerm = $event.target.value"
        @focus="open = true"
        :placeholder="placeholder"
        class="w-full px-4 py-3 bg-white border text-sm text-black border-gray-300 rounded-lg pr-10 transition-all"
      />
      <button @click="toggleDropdown" class="absolute right-3 top-1/2 -translate-y-1/2">
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
      @click="open = !open"
      class="w-full px-4 py-3 bg-white border text-sm border-gray-300 rounded-lg text-left flex items-center justify-between hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
    >
      <span :class="!modelValue || modelValue === placeholder ? 'text-gray-400' : 'text-gray-700'">
        {{ modelValue || placeholder }}
      </span>
      <svg
        :class="['w-5 h-5 text-gray-400 transition-transform', open ? 'rotate-180' : '']"
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
      <button
        v-for="(option, index) in filteredOptions"
        :key="index"
        @click="selectOption(option)"
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'Select...',
  },
  searchable: {
    type: Boolean,
    default: false,
  },
  label: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const searchTerm = ref('')
const dropdownRef = ref(null)
const inputRef = ref(null)

const filteredOptions = computed(() => {
  if (!props.searchable) return props.options

  return props.options.filter((option) =>
    option.toLowerCase().includes(searchTerm.value.toLowerCase()),
  )
})

const selectOption = (option) => {
  emit('update:modelValue', option)
  open.value = false
  searchTerm.value = ''
}

const toggleDropdown = () => {
  open.value = !open.value
  if (open.value && inputRef.value) {
    inputRef.value.focus()
  }
}

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    open.value = false
    searchTerm.value = ''
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})
</script>
