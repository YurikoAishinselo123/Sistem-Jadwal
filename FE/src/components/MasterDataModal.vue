<!-- ReusableModal.vue -->
<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    @click.self="closeModal"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
      <h2 class="text-xl font-bold mb-2 text-black">{{ title }}</h2>
      <p class="text-gray-600 text-sm mb-6">{{ subtitle }}</p>

      <form @submit.prevent="handleSubmit">
        <div class="space-y-4">
          <div v-for="field in fields" :key="field.name">
            <label class="block text-sm font-medium mb-2 text-black">
              {{ field.label }}
            </label>
            <input
              v-model="formData[field.name]"
              :type="field.type || 'text'"
              :placeholder="field.placeholder"
              class="w-full px-3 py-2 text-black border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :required="field.required !== false"
            />
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button
            type="button"
            @click="closeModal"
            class="px-6 py-2 border-active-blue rounded-lg font-medium text-active-blue border-2 hover:bg-gray-50 transition"
          >
            Batal
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-active-blue text-white rounded-lg font-medium hover:bg-blue-900 transition"
          >
            {{ submitText }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  fields: {
    type: Array,
    required: true,
  },
  submitText: {
    type: String,
    default: 'Tambah',
  },
})

const emit = defineEmits(['update:modelValue', 'submit'])

const formData = reactive({})

// Initialize formData with empty values
watch(
  () => props.fields,
  (newFields) => {
    newFields.forEach((field) => {
      if (!(field.name in formData)) {
        formData[field.name] = ''
      }
    })
  },
  { immediate: true },
)

const closeModal = () => {
  emit('update:modelValue', false)
}

const handleSubmit = () => {
  emit('submit', { ...formData })
  // Reset form
  Object.keys(formData).forEach((key) => (formData[key] = ''))
  closeModal()
}
</script>
