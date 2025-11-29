<template>
  <MasterDataTable
    :columns="['Kode Dosen', 'Nama Dosen']"
    :dataKeys="['kode', 'nama']"
    :items="dosenList"
    :searchKeys="['kode', 'nama']"
    :searchPlaceholder="'Cari berdasarkan kode atau nama dosen...'"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    addLabel="Dosen"
  />

  <MasterDataModal
    v-model="showModal"
    title="Tambah Dosen Baru"
    subtitle="Masukkan informasi dosen di bawah ini"
    :fields="dosenFields"
    submit-text="Tambah"
    @submit="handleDosenSubmit"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataDosen from '@/dummy data/dataDosen.json'

const dosenList = ref<any[]>([])
const showModal = ref(false)

const dosenFields = [
  { name: 'kode', label: 'Kode Dosen', placeholder: 'Masukkan kode dosen', type: 'text' },
  { name: 'nama', label: 'Nama Dosen', placeholder: 'Masukkan nama dosen', type: 'text' },
]

// Load data from API, fallback to local JSON
async function loadDosen() {
  try {
    const response = await MasterDataAPI.getAll('dosen')
    dosenList.value = response.data.data || response.data
  } catch (error) {
    console.warn('API failed, loading local JSON fallback', error)
    dosenList.value = DataDosen
  }
}

// Add new dosen
async function handleDosenSubmit(data: { kode: string; nama: string }) {
  try {
    await MasterDataAPI.create('dosen', data)
    await loadDosen()
    showModal.value = false
    alert('Dosen berhasil ditambahkan!')
  } catch (error) {
    console.warn('API create failed, updating local JSON only', error)
    dosenList.value.push({ ...data })
    showModal.value = false
    alert('Dosen berhasil ditambahkan (lokal)!')
  }
}

// Open modal
const handleAdd = () => {
  showModal.value = true
}

// Edit dosen
async function handleEdit(index: number) {
  const dosen = dosenList.value[index]
  const newNama = prompt('Edit nama dosen:', dosen.nama)
  if (newNama !== null) {
    try {
      await MasterDataAPI.update('dosen', dosen.id || dosen.kode, { ...dosen, nama: newNama })
      await loadDosen()
      alert('Dosen berhasil diupdate!')
    } catch (error) {
      console.warn('API update failed, updating local JSON only', error)
      dosenList.value[index].nama = newNama
      alert('Dosen berhasil diupdate (lokal)!')
    }
  }
}

// Delete dosen
async function handleDelete(index: number) {
  const dosen = dosenList.value[index]
  if (confirm(`Hapus dosen "${dosen.nama}"?`)) {
    try {
      await MasterDataAPI.delete('dosen', dosen.id || dosen.kode)
      await loadDosen()
      alert('Dosen berhasil dihapus!')
    } catch (error) {
      console.warn('API delete failed, removing locally', error)
      dosenList.value.splice(index, 1)
      alert('Dosen berhasil dihapus (lokal)!')
    }
  }
}

// Load data on mount
onMounted(loadDosen)
</script>
