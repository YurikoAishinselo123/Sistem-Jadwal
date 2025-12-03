<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

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
    const data = response.data

    laboranList.value = data.map((item: any) => ({
      id: item.id,
      kode: item.kode_laboran,
      nama: item.nama_laboran,
    }))
  } catch (err) {
    error('Gagal memuat data mata kuliah')
  }
}

// Add new laboran
async function handleLaboranSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_laboran: data.kode,
      nama_laboran: data.nama,
    }

    await MasterDataAPI.create('laboran', payload)
    await loadLaboran()
    showModal.value = false

    success('Mata Kuliah berhasil ditambahkan!')
  } catch (err) {
    error('Gagal menambahkan mata kuliah')
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
    } catch (err) {
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

      success('Mata Kuliah berhasil dihapus!')
    } catch (err) {
      error('Gagal menghapus mata kuliah')
    }
  }
}

// Load data when component mounts
onMounted(loadLaboran)
</script>

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
