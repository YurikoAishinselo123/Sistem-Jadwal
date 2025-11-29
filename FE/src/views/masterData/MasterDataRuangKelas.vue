<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataRuang from '@/dummy data/dataRuangKelas.json'

// State
const ruangList = ref<any[]>([])
const showModal = ref(false)

// Modal fields
const ruangFields = [
  { name: 'kode', label: 'Kode Ruang', placeholder: 'Masukkan kode ruang', type: 'text' },
  { name: 'nama', label: 'Nama Ruang', placeholder: 'Masukkan nama ruang', type: 'text' },
]

async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('ruangan')
    ruangList.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching ruangan from API:', error)
    ruangList.value = DataRuang
  }
}

// --- Handlers ---
const handleAddRuang = () => {
  showModal.value = true
}

async function handleRuangSubmit(data: { kode: string; nama: string }) {
  // Optional: prevent duplicate kode
  const exists = ruangList.value.some((r) => r.kode === data.kode)
  if (exists) {
    alert('Kode ruang sudah ada!')
    return
  }

  try {
    // Save via API
    await MasterDataAPI.create('ruangan', data)
    await loadFromAPI() // refresh table
    showModal.value = false
    alert('Ruang kelas berhasil ditambahkan!')
  } catch (error) {
    console.error('Error adding ruangan:', error)
    alert('Gagal menambahkan ruangan. Menggunakan dummy data.')
    // fallback to JSON
    ruangList.value.push(data)
    showModal.value = false
  }
}

async function handleEdit(index: number) {
  const ruang = ruangList.value[index]
  const newNama = prompt('Edit nama ruang:', ruang.nama)
  if (newNama !== null) {
    try {
      await MasterDataAPI.update('ruangan', ruang.id || ruang.kode, { ...ruang, nama: newNama })
      await loadFromAPI()
      alert('Ruang berhasil diupdate!')
    } catch (error) {
      console.error('Error updating ruangan:', error)
      alert('Gagal mengupdate ruangan. Mengubah dummy data saja.')
      ruang.nama = newNama
    }
  }
}

async function handleDelete(index: number) {
  const ruang = ruangList.value[index]
  if (confirm(`Hapus ruang "${ruang.nama}"?`)) {
    try {
      await MasterDataAPI.delete('ruangan', ruang.id || ruang.kode)
      await loadFromAPI()
      alert('Ruang berhasil dihapus!')
    } catch (error) {
      console.error('Error deleting ruangan:', error)
      alert('Gagal menghapus ruangan. Menghapus dummy data saja.')
      ruangList.value.splice(index, 1)
    }
  }
}

// Load data on mount
onMounted(loadFromAPI)
</script>

<template>
  <MasterDataTable
    :columns="['Kode Ruang', 'Nama Ruang']"
    :dataKeys="['kode', 'nama']"
    :cellAlign="['left', 'left']"
    :items="ruangList"
    :searchKeys="['kode', 'nama']"
    searchPlaceholder="Cari berdasarkan kode atau nama ruang..."
    addLabel="Ruang Kelas"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    @add="handleAddRuang"
    @edit="handleEdit"
    @delete="handleDelete"
  />

  <MasterDataModal
    v-model="showModal"
    title="Tambah Ruang Kelas Baru"
    subtitle="Masukkan informasi ruang kelas di bawah ini"
    :fields="ruangFields"
    submit-text="Tambah"
    @submit="handleRuangSubmit"
  />
</template>
