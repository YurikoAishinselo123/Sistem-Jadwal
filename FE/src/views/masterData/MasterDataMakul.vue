<template>
  <MasterDataTable
    :columns="['Kode Makul', 'Nama Mata Kuliah']"
    :dataKeys="['kode', 'nama']"
    :items="makulList"
    :searchKeys="['kode', 'nama']"
    :searchPlaceholder="'Cari berdasarkan kode atau nama mata kuliah...'"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    addLabel="Mata Kuliah"
  />

  <MasterDataModal
    v-model="showModal"
    title="Tambah Mata Kuliah Baru"
    subtitle="Masukkan informasi mata kuliah di bawah ini"
    :fields="makulFields"
    submit-text="Tambah"
    @submit="handleMakulSubmit"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataMakul from '@/dummy data/dataMakul.json'

const makulList = ref<any[]>([])
const showModal = ref(false)

const makulFields = [
  {
    name: 'kode',
    label: 'Kode Mata Kuliah',
    placeholder: 'Masukkan kode mata kuliah',
    type: 'text',
  },
  {
    name: 'nama',
    label: 'Nama Mata Kuliah',
    placeholder: 'Masukkan nama mata kuliah',
    type: 'text',
  },
]

// Load data from API with JSON fallback
async function loadMakul() {
  try {
    const response = await MasterDataAPI.getAll('makul')
    makulList.value = response.data.data || response.data
  } catch (error) {
    console.warn('API failed, loading local JSON fallback', error)
    makulList.value = DataMakul
  }
}

// Add new Mata Kuliah
async function handleMakulSubmit(data: { kode: string; nama: string }) {
  try {
    await MasterDataAPI.create('makul', data)
    await loadMakul()
    showModal.value = false
    alert('Mata Kuliah berhasil ditambahkan!')
  } catch (error) {
    console.warn('API create failed, updating local JSON only', error)
    makulList.value.push({ ...data })
    showModal.value = false
    alert('Mata Kuliah berhasil ditambahkan (lokal)!')
  }
}

// Open modal
const handleAdd = () => {
  showModal.value = true
}

// Edit Mata Kuliah
async function handleEdit(index: number) {
  const makul = makulList.value[index]
  const newNama = prompt('Edit nama mata kuliah:', makul.nama)
  if (newNama !== null) {
    try {
      await MasterDataAPI.update('makul', makul.id || makul.kode, { ...makul, nama: newNama })
      await loadMakul()
      alert('Mata Kuliah berhasil diupdate!')
    } catch (error) {
      console.warn('API update failed, updating local JSON only', error)
      makulList.value[index].nama = newNama
      alert('Mata Kuliah berhasil diupdate (lokal)!')
    }
  }
}

// Delete Mata Kuliah
async function handleDelete(index: number) {
  const makul = makulList.value[index]
  if (confirm(`Hapus Mata Kuliah "${makul.nama}"?`)) {
    try {
      await MasterDataAPI.delete('makul', makul.id || makul.kode)
      await loadMakul()
      alert('Mata Kuliah berhasil dihapus!')
    } catch (error) {
      console.warn('API delete failed, removing locally', error)
      makulList.value.splice(index, 1)
      alert('Mata Kuliah berhasil dihapus (lokal)!')
    }
  }
}

// Load data when component mounts
onMounted(loadMakul)
</script>
