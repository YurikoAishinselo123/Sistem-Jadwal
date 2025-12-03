<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IJam {
  jam: string
  id: number
}

const pagiItems = ref<IJam[]>([])
const malamItems = ref<IJam[]>([])

const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<{ name: string; label: string; placeholder: string; type: string }[]>([])
const modalData = ref<IJam>({ jam: '', id: 0 })

let editingIndex: number | null = null
let editingList: 'pagi' | 'malam' | null = null

// =======================
// Load data from API
// =======================
async function loadTimeSlots() {
  try {
    const response = await MasterDataAPI.getAll('waktu')
    const data = response.data

    pagiItems.value = data.pagi.map((jam: string, index: number) => ({ jam, id: index }))
    malamItems.value = data.malam.map((jam: string, index: number) => ({ jam, id: index }))

    success('Data waktu berhasil dimuat!')
  } catch (err) {
    error('Gagal memuat data waktu!')
  }
}

onMounted(loadTimeSlots)

// =======================
// Submit (Create / Edit)
// =======================
async function handleSubmitTime(data: IJam) {
  if (!data.jam || !editingList) return

  const targetList = editingList === 'pagi' ? pagiItems : malamItems

  try {
    if (editingIndex !== null) {
      const item = targetList.value[editingIndex]

      if (!item) {
        error('Data tidak ditemukan')
        return
      }

      await MasterDataAPI.update('waktu', item.id, {
        jam: data.jam,
        jenis: editingList,
      })

      success('Jam berhasil diperbarui!')
    } else {
      await MasterDataAPI.create('waktu', {
        jam: data.jam,
        jenis: editingList,
      })

      success('Jam berhasil ditambahkan!')
    }

    await loadTimeSlots()
    showModal.value = false
  } catch (err) {
    error('Terjadi kesalahan pada server')
  }

  editingIndex = null
  editingList = null
}

// =======================
// Delete
// =======================
async function handleDelete(list: 'pagi' | 'malam', index: number) {
  const targetList = list === 'pagi' ? pagiItems.value : malamItems.value
  const item = targetList[index]

  if (!item) return error('Data tidak ditemukan')

  if (confirm(`Hapus jam ${item.jam}?`)) {
    try {
      await MasterDataAPI.delete('waktu', item.id)
      await loadTimeSlots()
      success('Jam berhasil dihapus!')
    } catch (err) {
      error('Gagal menghapus jam!')
    }
  }
}

// =======================
// Modal control
// =======================
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
    { name: 'jam', label: 'Jam', placeholder: 'Masukkan jam (misal: 07:50)', type: 'time' },
  ]
  modalData.value = { jam: item.jam, id: item.id }
  showModal.value = true
}

// Button handler shortcuts
const handleAddPagi = () => handleAdd('pagi')
const handleAddMalam = () => handleAdd('malam')
const handleEditPagi = (i: number) => handleEdit('pagi', i)
const handleEditMalam = (i: number) => handleEdit('malam', i)
const handleDeletePagi = (i: number) => handleDelete('pagi', i)
const handleDeleteMalam = (i: number) => handleDelete('malam', i)
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 min-h-0 h-full">
    <MasterDataTable
      title="Waktu Pagi"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="pagiItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam pagi..."
      addLabel="Jam Pagi"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddPagi"
      @edit="handleEditPagi"
      @delete="handleDeletePagi"
    />

    <MasterDataTable
      title="Waktu Malam"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="malamItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam malam..."
      addLabel="Jam Malam"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddMalam"
      @edit="handleEditMalam"
      @delete="handleDeleteMalam"
    />
  </div>

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
