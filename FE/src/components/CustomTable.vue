<script setup lang="ts">
import PrintIcon from '@/assets/image/Print_icon.svg'
import Edit_icon from '@/assets/icons/Edit_icon.vue'
import Delete_icon from '@/assets/icons/Delete_icon.vue'
import Detail_icon from '@/assets/icons/Detail_icon.vue'
import { defineProps, defineEmits, ref, onMounted, onUnmounted, computed } from 'vue'

/* ---------- TYPES ---------- */
interface Column {
  key: string
  label: string
}

interface Row {
  [key: string]: any
}

/* ---------- PROPS ---------- */
const props = defineProps<{
  columns: Column[]
  data: Row[]
  hasActions: boolean
  emptyMessage?: string
}>()

/* ---------- EMITS ---------- */
const emit = defineEmits<{
  (e: 'edit', row: Row): void
  (e: 'delete', row: Row): void
  (e: 'print'): void
  (e: 'detail', payload: Row & { index: number }): void
  (e: 'selectionChange', selectedRows: Row[]): void
  (e: 'deleteSelected', ids: number[]): void
}>()

/* ---------- STATE ---------- */
const activeMenu = ref<number | null>(null)
const selectedIds = ref<Set<number>>(new Set())

/* ---------- COMPUTED ---------- */
const isAllSelected = computed(() => {
  return props.data.length > 0 && selectedIds.value.size === props.data.length
})

const isIndeterminate = computed(() => {
  return selectedIds.value.size > 0 && selectedIds.value.size < props.data.length
})
const selectedCount = computed(() => selectedIds.value.size)

const selectedIdList = computed(() => Array.from(selectedIds.value))

/* ---------- FUNCTIONS ---------- */
const toggleMenu = (rowIndex: number) => {
  activeMenu.value = activeMenu.value === rowIndex ? null : rowIndex
}

const handleEdit = (row: Row) => {
  emit('edit', row)
  activeMenu.value = null
}

const handleDelete = (row: Row) => {
  emit('delete', row)
  activeMenu.value = null
}

const handleDetail = (row: Row, index: number) => {
  emit('detail', { ...row, index })
  activeMenu.value = null
}

const handleClickOutside = (event: MouseEvent) => {
  const menus = document.querySelectorAll('.action-menu')
  let clickedInside = false

  menus.forEach((menu) => {
    if (menu.contains(event.target as Node)) clickedInside = true
  })

  if (!clickedInside && !(event.target as HTMLElement).closest('.action-button')) {
    activeMenu.value = null
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value.clear()
  } else {
    selectedIds.value = new Set(props.data.map((row) => row.id))
  }
}

const toggleRowSelection = (rowId: number) => {
  if (selectedIds.value.has(rowId)) {
    selectedIds.value.delete(rowId)
  } else {
    selectedIds.value.add(rowId)
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

/* ---------- FORMATTERS ---------- */
const formatMataKuliah = (text: string): string => {
  if (!text) return ''

  const words = text.split(' ')
  if (words.length <= 2) return text

  return `${words.slice(0, 2).join(' ')}<br>${words.slice(2).join(' ')}`
}
</script>

<template>
  <div class="flex flex-col overflow-hidden min-h-0 shadow-lg">
    <!-- Header Section -->
    <div class="bg-active-blue text-white p-4 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-semibold">Daftar Jadwal</h2>
        <p class="text-lg">{{ data?.length || 0 }} Jadwal ditemukan</p>
      </div>

      <div class="flex items-center gap-4">
        <!-- Delete Selected Button -->
        <button
          v-if="selectedCount > 0"
          @click="emit('deleteSelected', selectedIdList)"
          class="flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
          <span class="font-medium">Hapus Terpilih ({{ selectedCount }})</span>
        </button>

        <button
          @click="emit('print')"
          class="flex items-center gap-2 bg-white text-black px-4 py-2 rounded-lg hover:bg-gray-100"
        >
          <img :src="PrintIcon" alt="Print Icon" class="w-4" />
          <span class="font-medium text-black text-md">Cetak</span>
        </button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="overflow-auto max-h-[45vh] bg-white">
      <table class="w-full table-auto relative">
        <thead class="bg-header-table sticky top-0">
          <tr>
            <!-- Select All Checkbox -->
            <th class="py-3 px-4 w-12">
              <input
                type="checkbox"
                :checked="isAllSelected"
                :indeterminate="isIndeterminate"
                @change="toggleSelectAll"
                class="w-4 h-4 cursor-pointer accent-active-blue"
              />
            </th>

            <th v-if="hasActions" class="py-3"></th>

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
          <tr
            v-for="(row, index) in data"
            :key="row.id"
            :class="{ 'bg-blue-50': selectedIds.has(row.id) }"
          >
            <!-- Row Checkbox -->
            <td class="py-2 px-4 text-center">
              <input
                type="checkbox"
                :checked="selectedIds.has(row.id)"
                @change="toggleRowSelection(row.id)"
                class="w-4 h-4 cursor-pointer accent-active-blue"
              />
            </td>

            <!-- Actions -->
            <td v-if="hasActions" class="py-2 px-1 text-center relative">
              <button
                @click="toggleMenu(index)"
                class="p-2 hover:bg-gray-100 rounded-full action-button"
              >
                <svg class="w-5 h-5 text-gray-600" viewBox="0 0 16 16">
                  <circle cx="8" cy="2" r="1.5" />
                  <circle cx="8" cy="8" r="1.5" />
                  <circle cx="8" cy="14" r="1.5" />
                </svg>
              </button>

              <!-- Popup -->
              <div
                v-if="activeMenu === index"
                class="absolute top-1/2 -translate-y-1/2 left-full bg-white rounded-xl shadow-xl border z-50 action-menu"
              >
                <button
                  @click="handleDetail(row, index)"
                  class="w-full px-4 py-2 flex gap-2 hover:bg-gray-100 text-sm"
                >
                  <Detail_icon class="w-5 h-5" /> Detail
                </button>

                <button
                  @click="handleEdit(row)"
                  class="w-full px-4 py-2 flex gap-2 hover:bg-gray-100 text-sm"
                >
                  <Edit_icon class="w-5 h-5" /> Edit
                </button>

                <button
                  @click="handleDelete(row)"
                  class="w-full px-4 py-2 flex gap-2 hover:bg-red-50 text-sm text-red-600"
                >
                  <Delete_icon class="w-5 h-5" /> Delete
                </button>
              </div>
            </td>

            <!-- DATA -->
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-4 py-4 text-xs align-middle"
              :class="{ 'text-center': ['ruang', 'status'].includes(column.key) }"
            >
              <template v-if="column.key === 'mataKuliah'">
                <span v-html="formatMataKuliah(String(row[column.key]))"></span>
              </template>

              <template v-else-if="column.key === 'dosen'">
                <span class="whitespace-pre-line">{{ row.dosen }}</span>
              </template>

              <template v-else>
                {{ row[column.key] }}
              </template>
            </td>
          </tr>

          <tr v-if="!data || data.length === 0">
            <td
              :colspan="columns.length + (hasActions ? 2 : 1)"
              class="text-center py-6 text-gray-500 italic"
            >
              {{ emptyMessage || 'Tidak ada data' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
