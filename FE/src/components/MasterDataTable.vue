<template>
  <div class="p-8 h-full flex flex-col min-h-0">
    <!-- Search + Add Button -->
    <div class="flex justify-between items-center mb-6 flex-shrink-0">
      <div class="relative flex-1 max-w-2xl">
        <svg
          class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>

        <input
          v-model="searchQuery"
          type="text"
          :placeholder="searchPlaceholder"
          class="w-full pl-10 pr-4 py-3 border text-black border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
        />
      </div>

      <button
        @click="emitAdd"
        class="ml-4 flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4v16m8-8H4"
          />
        </svg>
        {{ addLabel }}
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm flex-1 min-h-0 flex flex-col overflow-hidden">
      <!-- TABLE HEADER -->
      <div
        class="bg-blue-100 gap-4 px-6 py-4 font-semibold text-gray-900 border-b border-gray-200"
        :style="{ display: 'grid', gridTemplateColumns: columnTemplate }"
      >
        <div v-for="(col, index) in columns" :key="index" :class="cellAlignClass(index)">
          {{ col }}
        </div>

        <!-- SHOW LAST HEADER ONLY WHEN ACTIONS PRESENT -->
        <div v-if="hasActionColumn" class="text-center">Aksi</div>
      </div>

      <!-- TABLE BODY -->
      <div class="overflow-y-auto flex-1 scrollable-table">
        <div class="divide-y divide-gray-200">
          <div
            v-for="(row, index) in filteredItems"
            :key="index"
            class="gap-4 px-6 py-4 hover:bg-gray-50 transition"
            :style="{ display: 'grid', gridTemplateColumns: columnTemplate }"
          >
            <div
              v-for="(key, idx2) in dataKeys"
              :key="idx2"
              class="text-gray-900"
              :class="[cellAlignClass(idx2)]"
            >
              {{ row[key] }}
            </div>

            <!-- ACTION COLUMN -->
            <div v-if="hasActionColumn" class="flex items-center justify-center gap-2">
              <slot name="actions" :row="row" :index="index">
                <!-- DEFAULT ACTIONS -->
                <template v-if="showDefaultActions">
                  <button
                    @click="emitEdit(index)"
                    class="p-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition"
                  >
                    Edit
                  </button>

                  <button
                    @click="emitDelete(index)"
                    class="p-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
                  >
                    Delete
                  </button>
                </template>
              </slot>
            </div>
          </div>
        </div>

        <!-- EMPTY STATE -->
        <div v-if="filteredItems.length === 0" class="p-6 text-center text-gray-500">
          No matching results found.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, useSlots } from 'vue'

export interface TableRow {
  [key: string]: string | number | null | undefined
}

const props = defineProps<{
  columns: string[]
  items: TableRow[]
  dataKeys: string[]
  addLabel?: string
  searchKeys: string[]
  searchPlaceholder?: string
  columnSizes?: string[]
  showDefaultActions?: boolean
  cellAlign?: string[] // <──── Added support for alignment
}>()

const emit = defineEmits(['add', 'edit', 'delete'])

const slots = useSlots()

const searchQuery = ref('')

/* SEARCH SYSTEM */
const filteredItems = computed(() => {
  const q = (searchQuery.value || '').toLowerCase()
  if (!props.items.length) return []
  return props.items.filter((item) =>
    props.searchKeys.some((key) =>
      String(item[key] ?? '')
        .toLowerCase()
        .includes(q),
    ),
  )
})

const cellAlignClass = (index: number) => {
  const align = props.cellAlign?.[index] || 'left'

  return {
    'flex items-center justify-start text-left': align === 'left',
    'flex items-center justify-center text-center': align === 'center',
    'flex items-center justify-end text-right': align === 'right',
  }
}

/* DETECT IF ACTION COLUMN IS NEEDED */
const hasActionColumn = computed(() => {
  return props.showDefaultActions || !!slots.actions
})

/* FIX COLUMN TEMPLATE */
const columnTemplate = computed(() => {
  const base = props.columnSizes?.length
    ? props.columnSizes.join(' ')
    : props.dataKeys.map(() => '1fr').join(' ')

  return hasActionColumn.value ? `${base} 150px` : base
})

const addLabel = props.addLabel || 'Tambah'
const searchPlaceholder = props.searchPlaceholder || 'Search...'
const showDefaultActions = props.showDefaultActions === true

function emitAdd() {
  emit('add')
}
function emitEdit(index: number) {
  emit('edit', index)
}
function emitDelete(index: number) {
  emit('delete', index)
}
</script>

<style scoped>
.scrollable-table::-webkit-scrollbar {
  width: 8px;
}
.scrollable-table::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}
.scrollable-table::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}
.scrollable-table::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
