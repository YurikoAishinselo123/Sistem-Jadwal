<template>
  <MasterDataTable
    :columns="['Kode Prodi', 'Nama Prodi']"
    :dataKeys="['kode', 'nama']"
    :items="prodiList"
    :searchKeys="['kode', 'nama']"
    :searchPlaceholder="'Cari berdasarkan kode atau nama prodi...'"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    addLabel="Prodi"
  />

  <MasterDataModal
    v-model="showModal"
    title="Tambah Prodi Baru"
    subtitle="Masukkan informasi prodi di bawah ini"
    :fields="prodiFields"
    submit-text="Tambah"
    @submit="handleProdiSubmit"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataProdi from '@/dummy data/dataProdi.json'

const prodiList = ref<any[]>([])
const showModal = ref(false)

const prodiFields = [
  { name: 'kode', label: 'Kode Prodi', placeholder: 'Masukkan kode prodi', type: 'text' },
  { name: 'nama', label: 'Nama Prodi', placeholder: 'Masukkan nama prodi', type: 'text' },
]

// Load data from API, fallback to local JSON if API fails
async function loadProdi() {
  try {
    const response = await MasterDataAPI.getAll('prodi')
    // check if response contains data
    prodiList.value = response.data.data || response.data
  } catch (error) {
    console.warn('API failed, loading local JSON fallback', error)
    prodiList.value = DataProdi
  }
}

// Add new prodi
async function handleProdiSubmit(data: { kode: string; nama: string }) {
  try {
    await MasterDataAPI.create('prodi', data)
    await loadProdi()
    showModal.value = false
    alert('Prodi berhasil ditambahkan!')
  } catch (error) {
    console.warn('API create failed, updating local JSON only', error)
    prodiList.value.push({ ...data })
    showModal.value = false
    alert('Prodi berhasil ditambahkan (lokal)!')
  }
}

// Open modal
const handleAdd = () => {
  showModal.value = true
}

// Edit prodi
async function handleEdit(index: number) {
  const prodi = prodiList.value[index]
  const newNama = prompt('Edit nama prodi:', prodi.nama)
  if (newNama !== null) {
    try {
      await MasterDataAPI.update('prodi', prodi.id || prodi.kode, { ...prodi, nama: newNama })
      await loadProdi()
      alert('Prodi berhasil diupdate!')
    } catch (error) {
      console.warn('API update failed, updating local JSON only', error)
      prodiList.value[index].nama = newNama
      alert('Prodi berhasil diupdate (lokal)!')
    }
  }
}

// Delete prodi
async function handleDelete(index: number) {
  const prodi = prodiList.value[index]
  if (confirm(`Hapus prodi "${prodi.nama}"?`)) {
    try {
      await MasterDataAPI.delete('prodi', prodi.id || prodi.kode)
      await loadProdi()
      alert('Prodi berhasil dihapus!')
    } catch (error) {
      console.warn('API delete failed, removing locally', error)
      prodiList.value.splice(index, 1)
      alert('Prodi berhasil dihapus (lokal)!')
    }
  }
}

// Load data when component mounts
onMounted(loadProdi)
</script>
