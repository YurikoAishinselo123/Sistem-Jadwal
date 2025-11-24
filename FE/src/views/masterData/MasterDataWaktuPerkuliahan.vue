<script setup lang="ts">
import { ref } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'

// Initial time slots
const timeSlots = {
  pagi: [
    '07:50',
    '08:40',
    '09:30',
    '10:20',
    '11:10',
    '12:00',
    '12:50',
    '13:40',
    '14:30',
    '15:20',
    '16:10',
    '17:00',
  ],
  malam: ['17:10', '18:00', '19:00', '19:35', '20:10', '20:45', '21:20', '21:55', '22:30', '23:05'],
}

// Convert to table items
const pagiItems = ref(timeSlots.pagi.map((jam) => ({ jam })))
const malamItems = ref(timeSlots.malam.map((jam) => ({ jam })))

// Modal state
const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<{ name: string; label: string; placeholder: string; type: string }[]>([])
const modalData = ref<{ jam: string }>({ jam: '' })
let editingIndex: number | null = null
let editingList: 'pagi' | 'malam' | null = null

// Common submit handler (TypeScript-safe)
function handleSubmitTime(data: { jam: string }) {
  if (!data.jam || !editingList) return

  // Determine the target list safely
  let targetList: typeof pagiItems | typeof malamItems | null = null
  if (editingList === 'pagi') targetList = pagiItems
  else if (editingList === 'malam') targetList = malamItems

  if (!targetList) return // TS-safe check

  if (editingIndex !== null) {
    // Edit existing item
    targetList.value[editingIndex] = { jam: data.jam }
  } else {
    // Add new item
    targetList.value.push({ jam: data.jam })
  }

  // Reset modal state
  showModal.value = false
  editingIndex = null
  editingList = null
}

// === PAGI ===
function handleAddPagi() {
  editingIndex = null
  editingList = 'pagi'
  modalTitle.value = 'Tambah Jam Pagi'
  modalSubmitText.value = 'Tambah'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (07:50)', type: 'text' },
  ]
  modalData.value = { jam: '' }
  showModal.value = true
}

function handleEditPagi(index: number) {
  const item = pagiItems.value[index]
  if (!item) return // <-- this prevents TS error

  editingIndex = index
  editingList = 'pagi'
  modalTitle.value = 'Edit Jam Pagi'
  modalSubmitText.value = 'Simpan'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (07:50)', type: 'text' },
  ]
  modalData.value = { jam: item.jam }
  showModal.value = true
}

function handleDeletePagi(index: number) {
  if (confirm('Hapus jam pagi ini?')) pagiItems.value.splice(index, 1)
}

// === MALAM ===
function handleAddMalam() {
  editingIndex = null
  editingList = 'malam'
  modalTitle.value = 'Tambah Jam Malam'
  modalSubmitText.value = 'Tambah'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (17:10)', type: 'text' },
  ]
  modalData.value = { jam: '' }
  showModal.value = true
}

function handleEditMalam(index: number) {
  const item = malamItems.value[index]
  if (!item) return // <-- this prevents TS error

  editingIndex = index
  editingList = 'malam'
  modalTitle.value = 'Edit Jam Malam'
  modalSubmitText.value = 'Simpan'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (17:10)', type: 'text' },
  ]
  modalData.value = { jam: item.jam }
  showModal.value = true
}

function handleDeleteMalam(index: number) {
  if (confirm('Hapus jam malam ini?')) malamItems.value.splice(index, 1)
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 min-h-0 h-full">
    <!-- === WAKTU PAGI === -->
    <MasterDataTable
      title="Waktu Pagi"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="pagiItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam pagi..."
      addLabel="Waktu Pagi"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddPagi"
      @edit="handleEditPagi"
      @delete="handleDeletePagi"
    />

    <!-- === WAKTU MALAM === -->
    <MasterDataTable
      title="Waktu Malam"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="malamItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam malam..."
      addLabel="Waktu Malam"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddMalam"
      @edit="handleEditMalam"
      @delete="handleDeleteMalam"
    />
  </div>

  <!-- Modal -->
  <MasterDataModal
    v-model="showModal"
    :title="modalTitle"
    subtitle=""
    :fields="modalFields"
    :data="modalData"
    :submit-text="modalSubmitText"
    @submit="handleSubmitTime"
  />
</template>
