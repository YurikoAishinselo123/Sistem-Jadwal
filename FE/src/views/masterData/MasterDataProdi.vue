<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

const prodiList = ref<any[]>([])
const showModal = ref(false)

const prodiFields = [
  { name: 'kode', label: 'Kode Prodi', placeholder: 'Masukkan kode prodi', type: 'text' },
  { name: 'nama', label: 'Nama Prodi', placeholder: 'Masukkan nama prodi', type: 'text' },
]

async function loadProdi() {
  try {
    const response = await MasterDataAPI.getAll('prodi')
    const data = response.data
    prodiList.value = data.map((item: any) => ({
      id: item.id,
      kode: item.kode_prodi,
      nama: item.nama_prodi,
    }))
  } catch (err) {
    error('Gagal memuat data mata kuliah')
  }
}

// Add new prodi
async function handleProdiSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_prodi: data.kode,
      nama_prodi: data.nama,
    }

    await MasterDataAPI.create('prodi', payload)
    await loadProdi()
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

      success('Mata Kuliah berhasil dihapus!')
    } catch (err) {
      error('Gagal menghapus mata kuliah')
    }
  }
}

// Load data when component mounts
onMounted(loadProdi)
</script>

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
