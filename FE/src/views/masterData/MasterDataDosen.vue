<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IDosen {
  id: number | null
  kode: string
  nama: string
}

// ================== STATE ==================
const dosenList = ref<IDosen[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref({
  kode: '',
  nama: '',
})

const dosenFields = [
  { name: 'kode', label: 'Kode Dosen', placeholder: 'Masukkan kode dosen', type: 'text' },
  { name: 'nama', label: 'Nama Dosen', placeholder: 'Masukkan nama dosen', type: 'text' },
]

// Load data from API
async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('dosen')
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      dosenList.value = []
      return
    }

    dosenList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IDosen => ({
          id: item.id ?? null,
          kode: item.kode_dosen ?? '',
          nama: item.nama_dosen ?? '',
        }),
      )
  } catch (err) {
    dosenList.value = []
    error('Gagal memuat data dosen')
  }
}

// ================== ADD ==================
const handleAdd = () => {
  isEdit.value = false
  editingId.value = null

  modalData.value = {
    kode: '',
    nama: '',
  }

  showModal.value = true
}

// ================== Edit ==================
async function handleEdit(index: number) {
  const dosen = dosenList.value[index]
  if (!dosen) return error('Data dosen tidak ditemukan')

  isEdit.value = true
  editingId.value = dosen.id

  modalData.value = {
    kode: dosen.kode,
    nama: dosen.nama,
  }

  showModal.value = true
}

// Add new dosen
async function handleSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_dosen: data.kode,
      nama_dosen: data.nama,
    }

    if (isEdit.value && editingId.value) {
      await MasterDataAPI.update('dosen', editingId.value, payload)
      success('Dosen berhasil diupdate!')
    } else {
      await MasterDataAPI.create('dosen', payload)
      success('Dosen berhasil ditambahkan!')
    }
    await loadFromAPI()
    showModal.value = false
  } catch (err) {
    error(isEdit.value ? 'Gagal mengubah data dosen' : 'Gagal menambahkan dosen')
  }
}

// Delete dosen
async function handleDelete(index: number) {
  const dosen = dosenList.value[index]
  if (!dosen) return error('Data dosen tidak ditemukan')

  if (!confirm(`Hapus ruang "${dosen.nama}"?`)) return
  try {
    const response = await MasterDataAPI.delete('dosen', dosen.id!)

    const message = response?.data?.message
    if (message) {
      return error(message)
    }

    success('Dosen berhasil dihapus!')
    await loadFromAPI()
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menghapus data dosen'
    error(message)
  }
}

// Load data on mount
onMounted(loadFromAPI)
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
    :title="isEdit ? 'Edit Dosen' : 'Tambah Dosen'"
    subtitle="Masukkan informasi dosen di bawah ini"
    :data="modalData"
    :fields="dosenFields"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
