<template>
  <div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <!-- Table Header -->
      <thead class="bg-blue-100">
        <tr>
          <th
            v-for="column in columns"
            :key="column.key"
            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
          >
            {{ column.label }}
          </th>
          <th
            v-if="hasActions"
            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
          >
            Aksi
          </th>
        </tr>
      </thead>

      <!-- Table Body -->
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(row, index) in data" :key="index" class="hover:bg-gray-50">
          <td
            v-for="column in columns"
            :key="column.key"
            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
          >
            <!-- Custom slot for each column if needed -->
            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
              {{ row[column.key] }}
            </slot>
          </td>

          <!-- Actions Column -->
          <td v-if="hasActions" class="px-6 py-4 whitespace-nowrap text-sm">
            <slot name="actions" :row="row">
              <div class="flex gap-2">
                <button
                  @click="$emit('edit', row)"
                  class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded"
                >
                  ✏️
                </button>
                <button
                  @click="$emit('delete', row)"
                  class="bg-red-500 hover:bg-red-600 text-white p-2 rounded"
                >
                  🗑️
                </button>
              </div>
            </slot>
          </td>
        </tr>

        <!-- Empty State -->
        <tr v-if="!data || data.length === 0">
          <td
            :colspan="columns.length + (hasActions ? 1 : 0)"
            class="px-6 py-8 text-center text-gray-500"
          >
            {{ emptyMessage }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'BaseTable',
  props: {
    columns: {
      type: Array,
      required: true,
      // Expected format: [{ key: 'name', label: 'Nama' }, ...]
    },
    data: {
      type: Array,
      required: true,
      // Expected format: [{ name: 'John', age: 25 }, ...]
    },
    hasActions: {
      type: Boolean,
      default: false,
    },
    emptyMessage: {
      type: String,
      default: 'Tidak ada data',
    },
  },
  emits: ['edit', 'delete'],
}
</script>
