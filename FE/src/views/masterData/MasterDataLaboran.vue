<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface ILaboran {
  id: number | null
  kode: string
  nama: string
}

// ================== STATE ==================
const laboranList = ref<ILaboran[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref({
  kode: '',
  nama: '',
})

// ================== MODAL FIELDS ==================c
const laboranFields = [
  { name: 'kode', label: 'Kode Laboran', placeholder: 'Masukkan kode laboran', type: 'text' },
  { name: 'nama', label: 'Nama Laboran', placeholder: 'Masukkan nama laboran', type: 'text' },
]

// ================== API FUNCTIONS ==================s
async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('laboran')
    const rawData = response?.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      laboranList.value = []
      success('Data ruang masih kosong')
      return
    }

    laboranList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): ILaboran => ({
          id: item.id ?? null,
          kode: item.kode_laboran ?? '',
          nama: item.nama_laboran ?? '',
        }),
      )
  } catch (err) {
    laboranList.value = []
    error('Gagal memuat data laboran')
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
const handleEdit = (index: number) => {
  const laboran = laboranList.value[index]
  if (!laboran) return error('Data laboran tidak ditemukan')

  isEdit.value = true
  editingId.value = laboran.id

  modalData.value = {
    kode: laboran.kode,
    nama: laboran.nama,
  }

  showModal.value = true
}

// ================== SUBMIT (ADD + EDIT) ==================
async function handleSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_laboran: data.kode,
      nama_laboran: data.nama,
    }

    if (isEdit.value && editingId.value) {
      await MasterDataAPI.update('laboran', editingId.value, payload)
      success('Laboran berhasil diupdate!')
    } else {
      await MasterDataAPI.create('laboran', payload)
      success('Laboran berhasil ditambahkan!')
    }

    await loadFromAPI()
    showModal.value = false
  } catch (err) {
    error(isEdit.value ? 'Gagal mengubah data laboran' : 'Gagal menambahkan laboran')
  }
}

// Delete laboran
async function handleDelete(index: number) {
  const laboran = laboranList.value[index]
  if (!laboran) return error('Data ruang tidak ditemukan')

  if (!confirm(`Hapus ruang "${laboran.nama}"?`)) return
  try {
    const response = await MasterDataAPI.delete('laboran', laboran.id!)
    success('Ruang berhasil dihapus!')
    await loadFromAPI()
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menghapus data laboran'
    error(message)
  }
}

onMounted(loadFromAPI)
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
    :title="isEdit ? 'Edit Laboran' : 'Tambah Laboran Baru'"
    subtitle="Masukkan informasi laboran di bawah ini"
    :fields="laboranFields"
    :data="modalData"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
