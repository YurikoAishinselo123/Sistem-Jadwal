<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

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
    const data = response.data

    ruangList.value = data.map((item: any) => ({
      id: item.id,
      kode: item.kode_ruangan,
      nama: item.nama_ruangan,
    }))
  } catch (err) {
    error('Gagal memuat data mata kuliah')
  }
}

// --- Handlers ---
const handleAddRuang = () => {
  showModal.value = true
}

async function handleRuangSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_ruangan: data.kode,
      nama_ruangan: data.nama,
    }

    await MasterDataAPI.create('ruangan', payload)
    await loadFromAPI()
    showModal.value = false

    success('Mata Kuliah berhasil ditambahkan!')
  } catch (err) {
    error('Gagal menambahkan mata kuliah')
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
      success('Mata Kuliah berhasil dihapus!')
    } catch (err) {
      error('Gagal menghapus mata kuliah')
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
