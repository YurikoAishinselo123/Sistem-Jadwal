<template>
  <MasterDataTable
    :columns="['Kode Laboran', 'Nama Laboran']"
    :dataKeys="['kode', 'nama']"
    :items="laboranList"
    :searchKeys="['kode', 'nama']"
    :searchPlaceholder="'Cari berdasarkan kode atau nama laboran...'"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    addLabel="Laboran"
  />

  <MasterDataModal
    v-model="showModal"
    title="Tambah Laboran Baru"
    subtitle="Masukkan informasi laboran di bawah ini"
    :fields="laboranFields"
    submit-text="Tambah"
    @submit="handleLaboranSubmit"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataLaboran from '@/dummy data/dataLaboran.json'

const laboranList = ref<any[]>([])
const showModal = ref(false)

const laboranFields = [
  { name: 'kode', label: 'Kode Laboran', placeholder: 'Masukkan kode laboran', type: 'text' },
  { name: 'nama', label: 'Nama Laboran', placeholder: 'Masukkan nama laboran', type: 'text' },
]

// Load data from API with JSON fallback
async function loadLaboran() {
  try {
    const response = await MasterDataAPI.getAll('laboran')
    laboranList.value = response.data.data || response.data
  } catch (error) {
    console.warn('API failed, loading local JSON fallback', error)
    laboranList.value = DataLaboran
  }
}

// Add new laboran
async function handleLaboranSubmit(data: { kode: string; nama: string }) {
  try {
    await MasterDataAPI.create('laboran', data)
    await loadLaboran()
    showModal.value = false
    alert('Laboran berhasil ditambahkan!')
  } catch (error) {
    console.warn('API create failed, updating local JSON only', error)
    laboranList.value.push({ ...data })
    showModal.value = false
    alert('Laboran berhasil ditambahkan (lokal)!')
  }
}

// Open modal
const handleAdd = () => {
  showModal.value = true
}

// Edit laboran
async function handleEdit(index: number) {
  const laboran = laboranList.value[index]
  const newNama = prompt('Edit nama laboran:', laboran.nama)
  if (newNama !== null) {
    try {
      await MasterDataAPI.update('laboran', laboran.id || laboran.kode, {
        ...laboran,
        nama: newNama,
      })
      await loadLaboran()
      alert('Laboran berhasil diupdate!')
    } catch (error) {
      console.warn('API update failed, updating local JSON only', error)
      laboranList.value[index].nama = newNama
      alert('Laboran berhasil diupdate (lokal)!')
    }
  }
}

// Delete laboran
async function handleDelete(index: number) {
  const laboran = laboranList.value[index]
  if (confirm(`Hapus laboran "${laboran.nama}"?`)) {
    try {
      await MasterDataAPI.delete('laboran', laboran.id || laboran.kode)
      await loadLaboran()
      alert('Laboran berhasil dihapus!')
    } catch (error) {
      console.warn('API delete failed, removing locally', error)
      laboranList.value.splice(index, 1)
      alert('Laboran berhasil dihapus (lokal)!')
    }
  }
}

// Load data when component mounts
onMounted(loadLaboran)
</script>
