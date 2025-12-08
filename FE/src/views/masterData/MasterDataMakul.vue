<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IMakul {
  id: number | null
  kode: string
  nama: string
}

// ================== STATE ==================
const makulList = ref<IMakul[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref({
  kode: '',
  nama: '',
})

// ================== MODAL FIELDS ==================
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

async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('makul')
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      makulList.value = []
      success('Data Makul masih kosong')
      return
    }

    makulList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IMakul => ({
          id: item.id ?? null,
          kode: item.kode_makul ?? '',
          nama: item.nama_makul ?? '',
        }),
      )
  } catch (err) {
    makulList.value = []
    error('Gagal memuat data mata kuliah')
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
  const makul = makulList.value[index]
  if (!makul) return error('Data laboran tidak ditemukan')

  isEdit.value = true
  editingId.value = makul.id

  modalData.value = {
    kode: makul.kode,
    nama: makul.nama,
  }

  showModal.value = true
}

// ================== SUBMIT (ADD + EDIT) ==================
async function handleSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_makul: data.kode,
      nama_makul: data.nama,
    }

    if (isEdit.value && editingId.value) {
      // ✅ UPDATE
      await MasterDataAPI.update('makul', editingId.value, payload)
      success('Laboran berhasil diupdate!')
    } else {
      await MasterDataAPI.create('makul', payload)
      success('Laboran berhasil ditambahkan!')
    }

    await loadFromAPI()
    showModal.value = false
  } catch (err) {
    error(isEdit.value ? 'Gagal mengubah data makul' : 'Gagal menambahkan makul')
  }
}

// Delete Mata Kuliah
async function handleDelete(index: number) {
  const makul = makulList.value[index]
  if (!makul) return error('Data ruang tidak ditemukan')

  if (!confirm(`Hapus ruang "${makul.nama}"?`)) return
  try {
    const response = await MasterDataAPI.delete('makul', makul.id!)

    const message = response?.data?.message
    if (message) {
      return error(message)
    }

    success('Mata Kuliah berhasil dihapus!')
    await loadFromAPI()
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menghapus data laboran'
    error(message)
  }
}

// Load data when component mounts
onMounted(loadFromAPI)
</script>

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
    :title="isEdit ? 'Edit Laboran' : 'Tambah Laboran Baru'"
    subtitle="Masukkan informasi mata kuliah di bawah ini"
    :fields="makulFields"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
