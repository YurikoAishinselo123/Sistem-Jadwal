<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

const dosenList = ref<any[]>([])
const showModal = ref(false)

const dosenFields = [
  { name: 'kode', label: 'Kode Dosen', placeholder: 'Masukkan kode dosen', type: 'text' },
  { name: 'nama', label: 'Nama Dosen', placeholder: 'Masukkan nama dosen', type: 'text' },
]

// Load data from API
async function loadDosen() {
  try {
    const response = await MasterDataAPI.getAll('dosen')
    const data = response.data

    dosenList.value = data.map((item: any) => ({
      id: item.id,
      kode: item.kode_dosen,
      nama: item.nama_dosen,
    }))
  } catch (err) {
    error('Gagal memuat data dosen')
  }
}

// Add new dosen
async function handleDosenSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_dosen: data.kode,
      nama_dosen: data.nama,
    }

    await MasterDataAPI.create('dosen', payload)
    await loadDosen()
    showModal.value = false

    success('Dosen berhasil ditambahkan!')
  } catch (err) {
    error('Gagal menambahkan dosen')
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

  if (newNama !== null && newNama.trim() !== '') {
    try {
      await MasterDataAPI.update('dosen', dosen.id || dosen.kode, {
        kode_dosen: dosen.kode,
        nama_dosen: newNama,
      })
      await loadDosen()

      success('Dosen berhasil diupdate!')
    } catch (err) {
      console.warn('API update failed', err)
      error('Gagal mengupdate dosen')
    }
  }
}

// Delete dosen
async function handleDelete(index: number) {
  const dosen = dosenList.value[index]

  if (confirm(`Hapus dosen "${dosen.nama}"?`)) {
    try {
      await MasterDataAPI.delete('dosen', dosen.id)
      await loadDosen()

      success('Dosen berhasil dihapus!')
    } catch (err) {
      console.warn('API delete failed', err)
      error('Gagal menghapus dosen')
    }
  }
}

// Load data on mount
onMounted(loadDosen)
</script>

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
