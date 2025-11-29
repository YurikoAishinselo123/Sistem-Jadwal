<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import DataRuang from '@/dummy data/dataWaktuPerkuliahan.json'
import MasterDataAPI from '@/services/masterDataAPI'

// Type for a single jam
interface IJam {
  jam: string
  id: number
}

// Reactive lists
const pagiItems = ref<IJam[]>([])
const malamItems = ref<IJam[]>([])

// Modal state
const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<{ name: string; label: string; placeholder: string; type: string }[]>([])
const modalData = ref<IJam>({ jam: '', id: 0 })

// Editing state
let editingIndex: number | null = null
let editingList: 'pagi' | 'malam' | null = null

// =======================
// Load data from API / JSON
// =======================
async function loadTimeSlots() {
  try {
    // Example: call your API
    // const response = await MasterDataAPI.getTimeSlots()
    // const data = response.data as { pagi: string[]; malam: string[] }

    // For dummy JSON
    const data = DataRuang as { pagi: string[]; malam: string[] }

    pagiItems.value = data.pagi.map((jam, index) => ({ jam, id: index }))
    malamItems.value = data.malam.map((jam, index) => ({ jam, id: index }))
  } catch (err) {
    console.error('Failed to load time slots:', err)
  }
}

onMounted(() => {
  loadTimeSlots()
})

// =======================
// Modal submit handler
// =======================
function handleSubmitTime(data: IJam) {
  if (!data.jam || !editingList) return

  const targetList = editingList === 'pagi' ? pagiItems : malamItems

  if (editingIndex !== null && targetList.value[editingIndex]) {
    // Edit existing safely
    const existing = targetList.value[editingIndex]!
    targetList.value[editingIndex] = { jam: data.jam, id: existing.id }
  } else {
    // Add new
    const newId = targetList.value.length ? Math.max(...targetList.value.map((i) => i.id)) + 1 : 0
    targetList.value.push({ jam: data.jam, id: newId })
  }

  showModal.value = false
  editingIndex = null
  editingList = null
}

// =======================
// CRUD handlers
// =======================
function handleAddPagi() {
  handleAdd('pagi')
}
function handleAddMalam() {
  handleAdd('malam')
}
function handleEditPagi(index: number) {
  handleEdit('pagi', index)
}
function handleEditMalam(index: number) {
  handleEdit('malam', index)
}
function handleDeletePagi(index: number) {
  handleDelete('pagi', index)
}
function handleDeleteMalam(index: number) {
  handleDelete('malam', index)
}

function handleAdd(list: 'pagi' | 'malam') {
  editingIndex = null
  editingList = list
  modalTitle.value = `Tambah Jam ${list === 'pagi' ? 'Pagi' : 'Malam'}`
  modalSubmitText.value = 'Tambah'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (misal: 07:50)', type: 'time' },
  ]
  modalData.value = { jam: '', id: 0 }
  showModal.value = true
}

function handleEdit(list: 'pagi' | 'malam', index: number) {
  const targetList = list === 'pagi' ? pagiItems.value : malamItems.value
  const item = targetList[index]
  if (!item) return

  editingIndex = index
  editingList = list
  modalTitle.value = `Edit Jam ${list === 'pagi' ? 'Pagi' : 'Malam'}`
  modalSubmitText.value = 'Simpan'
  modalFields.value = [
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (misal: 07:50)', type: 'text' },
  ]
  modalData.value = { jam: item.jam, id: item.id }
  showModal.value = true
}

function handleDelete(list: 'pagi' | 'malam', index: number) {
  const targetList = list === 'pagi' ? pagiItems.value : malamItems.value
  if (confirm('Hapus jam ini?')) targetList.splice(index, 1)
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 min-h-0 h-full">
    <!-- Waktu Pagi -->
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

    <!-- Waktu Malam -->
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
