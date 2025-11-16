<template>
  <div class="overflow-hidden bg-white rounded-lg shadow-lg">
    <!-- Header Section -->
    <div class="bg-active-blue text-white p-4 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-semibold">Daftar Jadwal</h2>
        <p class="text-lg">{{ data.length }} Jadwal ditemukan</p>
      </div>

      <button
        @click="$emit('print')"
        class="flex items-center gap-2 bg-white text-black px-4 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200"
      >
        <img :src="PrintIcon" alt="Print Icon" class="w-4 h-auto mx-auto" />
        <span class="font-medium text-black text-md">Cetak</span>
      </button>
    </div>

    <!-- Table Section -->
    <div class="w-full overflow-x-auto max-w-full">
      <table class="min-w-full divide-y divide-gray-200">
        <!-- Table Header -->
        <thead class="bg-header-table">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
            >
              {{ column.label }}
            </th>
            <th
              v-if="hasActions"
              class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider"
            >
              Aksi
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
          <tr v-for="(row, rowIndex) in data" :key="rowIndex">
            <td v-for="column in columns" :key="column.key" class="px-4 py-4 text-xs align-middle">
              <!-- Mata Kuliah: break line after 2nd word -->
              <template v-if="column.key === 'mataKuliah'">
                <span v-html="formatMataKuliah(row[column.key])"></span>
              </template>

              <!-- Dosen: shorten but keep degrees -->
              <template v-else-if="column.key === 'dosen'">
                <div class="flex flex-col">
                  <span>{{ formatDosen(row.dosen1 || row.dosen) }}</span>
                  <span v-if="row.dosen2">{{ formatDosen(row.dosen2) }}</span>
                </div>
              </template>

              <!-- Default render -->
              <template v-else>
                {{ row[column.key] }}
              </template>
            </td>

            <!-- Actions -->
            <td v-if="hasActions" class="px-6 py-4 text-center">
              <div class="flex items-center justify-center gap-3">
                <button @click="$emit('edit', row)">
                  <img :src="EditIcon" alt="Edit Icon" class="w-7 min-w-[28px] h-auto" />
                </button>
                <button @click="$emit('delete', row)">
                  <img :src="DeleteIcon" alt="Delete Icon" class="w-7 min-w-[28px] h-auto" />
                </button>
              </div>
            </td>
          </tr>

          <!-- When no data -->
          <tr v-if="data.length === 0">
            <td
              :colspan="columns.length + (hasActions ? 1 : 0)"
              class="text-center py-6 text-gray-500 italic"
            >
              {{ emptyMessage }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import PrintIcon from '@/assets/image/Print_icon.svg'
import EditIcon from '@/assets/image/Edit_icon.svg'
import DeleteIcon from '@/assets/image/Delete_icon.svg'

import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
  hasActions: {
    type: Boolean,
    default: false,
  },
  emptyMessage: {
    type: String,
    default: 'Tidak ada data',
  },
})

defineEmits(['edit', 'delete', 'print'])

// 🔹 Break line after 2nd word for Mata Kuliah
const formatMataKuliah = (text) => {
  if (!text) return ''
  const words = text.split(' ')
  if (words.length <= 2) return text
  return `${words.slice(0, 2).join(' ')}<br>${words.slice(2).join(' ')}`
}

// 🔹 Shorten Dosen name but keep degrees
const formatDosen = (name) => {
  if (!name) return ''

  // Split by comma to separate name and degree
  const [mainName, ...degrees] = name.split(',')
  const words = mainName.trim().split(/\s+/)

  if (words.length <= 2) return name

  // Keep first 2 words + first letter of 3rd (if exists)
  const shortened = `${words[0]} ${words[1]} ${words[2] ? words[2][0] + '.' : ''}`
  const degreePart = degrees.length > 0 ? `,${degrees.join(',')}` : ''

  return shortened + degreePart
}
</script>
