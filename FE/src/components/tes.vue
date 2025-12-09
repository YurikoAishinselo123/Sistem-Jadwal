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
            <label v-if="field.type !== 'select'" class="block text-sm font-medium mb-2 text-black">
              {{ field.label }}
            </label>

            <!-- SELECT FIELD -->
            <CustomDropdown
              v-if="field.type === 'select'"
              v-model="formData[field.name]"
              :options="field.options || []"
              :placeholder="field.placeholder"
              :label="field.label"
            />
            <!-- INLINE ERROR FOR SELECT -->
            <p v-if="validationErrors[field.name]" class="text-red-500 text-sm mt-1">
              {{ validationErrors[field.name] }}
            </p>

            <!-- INPUT FIELD -->
            <input
              v-else
              v-model="formData[field.name]"
              :type="field.type || 'text'"
              :placeholder="field.placeholder"
              :min="field.min"
              :max="field.max"
              :step="field.type === 'time' ? 60 : undefined"
              @input="
                field.name === 'kode'
                  ? (formData[field.name] = formData[field.name].toUpperCase())
                  : null
              "
              class="w-full px-3 py-2 text-black border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="field.name === 'kode' ? 'uppercase-input' : ''"
              :required="field.required !== false"
            />
            <!-- INLINE ERROR FOR INPUT -->
            <p v-if="validationErrors[field.name]" class="text-red-500 text-sm mt-1">
              {{ validationErrors[field.name] }}
            </p>
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
