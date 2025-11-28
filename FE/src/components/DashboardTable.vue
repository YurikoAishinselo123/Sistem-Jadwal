<template>
  <div class="flex flex-col overflow-hidden min-h-0 shadow-lg">
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

    <!-- TABLE SCROLL WRAPPER -->
    <div class="overflow-auto max-h-[45vh] bg-white">
      <table class="w-full table-auto">
        <thead class="bg-header-table sticky top-0 z-10">
          <tr>
            <th
              v-if="hasActions"
              class="py-3 text-center text-xs font-semibold text-black uppercase tracking-wider"
            ></th>

            <th
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-4 text-left text-xs font-semibold text-black uppercase tracking-wider"
              :class="{ 'text-center': ['ruang', 'status'].includes(column.key) }"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200 text-gray-800">
          <tr v-for="(row, rowIndex) in data" :key="rowIndex">
            <!-- Actions -->
            <td v-if="hasActions" class="py-2 px-1 text-center relative">
              <div class="flex items-center justify-center">
                <button
                  @click="toggleMenu(rowIndex)"
                  class="p-2 hover:bg-gray-100 rounded-full transition-colors action-button"
                >
                  <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 16 16">
                    <circle cx="8" cy="2" r="1.5" />
                    <circle cx="8" cy="8" r="1.5" />
                    <circle cx="8" cy="14" r="1.5" />
                  </svg>
                </button>

                <!-- Popup Menu -->
                <div
                  v-if="activeMenu === rowIndex"
                  class="absolute top-1/2 -translate-y-1/2 left-full bg-white rounded-xl shadow-xl border border-gray-200 z-50 action-menu min-w-[120px]"
                >
                  <button @click="" class="w-full px-4 py-3 flex gap-4 hover:bg-gray-100 text-sm">
                    <Detail_icon class="w-5 h-5" /> Detail
                  </button>

                  <button
                    @click="handleEdit(row)"
                    class="w-full px-4 py-3 flex gap-4 hover:bg-gray-100 text-sm"
                  >
                    <Edit_icon class="w-5 h-5" /> Edit
                  </button>

                  <button
                    @click="handleDelete(row)"
                    class="w-full px-4 py-3 flex gap-4 hover:bg-red-50 text-sm text-red-600"
                  >
                    <Delete_icon class="w-5 h-5" /> Delete
                  </button>
                </div>
              </div>
            </td>

            <td
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-4 text-xs align-middle"
              :class="{ 'text-center': ['ruang', 'status'].includes(column.key) }"
            >
              <template v-if="column.key === 'mataKuliah'">
                <span v-html="formatMataKuliah(row[column.key])"></span>
              </template>

              <template v-else-if="column.key === 'dosen'">
                <div class="flex flex-col">
                  <span>{{ formatDosen(row.dosen1 || row.dosen) }}</span>
                  <span v-if="row.dosen2">{{ formatDosen(row.dosen2) }}</span>
                </div>
              </template>

              <template v-else>
                {{ row[column.key] }}
              </template>
            </td>
          </tr>

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
import Edit_icon from '@/assets/icons/Edit_icon.vue'
import Delete_icon from '@/assets/icons/Delete_icon.vue'
import Detail_icon from '@/assets/icons/Detail_icon.vue'
import { defineProps, defineEmits, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  columns: Array,
  data: Array,
  hasActions: Boolean,
  emptyMessage: { type: String, default: 'Tidak ada data' },
})

const emit = defineEmits(['edit', 'delete', 'print'])
const activeMenu = ref(null)

const toggleMenu = (rowIndex) => {
  activeMenu.value = activeMenu.value === rowIndex ? null : rowIndex
}

const handleEdit = (row) => {
  emit('edit', row)
  activeMenu.value = null
}

const handleDelete = (row) => {
  emit('delete', row)
  activeMenu.value = null
}

const handleClickOutside = (event) => {
  const menus = document.querySelectorAll('.action-menu')
  let clickedInside = false

  menus.forEach((menu) => {
    if (menu.contains(event.target)) clickedInside = true
  })

  if (!clickedInside && !event.target.closest('.action-button')) activeMenu.value = null
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

const formatMataKuliah = (text) => {
  if (!text) return ''
  const words = text.split(' ')
  if (words.length <= 2) return text
  return `${words.slice(0, 2).join(' ')}<br>${words.slice(2).join(' ')}`
}

const formatDosen = (name) => {
  if (!name) return ''
  const [mainName, ...degrees] = name.split(',')
  const words = mainName.trim().split(/\s+/)
  if (words.length <= 2) return name
  const shortened = `${words[0]} ${words[1]} ${words[2] ? words[2][0] + '.' : ''}`
  return shortened + (degrees.length > 0 ? `,${degrees.join(',')}` : '')
}
</script>

<style scoped>
/* Scrollbar styling */
.flex-1::-webkit-scrollbar {
  width: 8px;
}

.flex-1::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}
.flex-1::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
