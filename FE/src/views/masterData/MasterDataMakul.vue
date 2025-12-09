<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IMakul {
  id: number | null
  kode: string
  nama: string
  jenis: string
  sks: number
}

// ================== STATE ==================
const makulList = ref<IMakul[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref<{
  kode: string
  nama: string
  jenis: string
  sks: number | null
}>({
  kode: '',
  nama: '',
  jenis: '',
  sks: null,
})

// ================== SKS VALIDATION ==================
// Get max SKS based on selected jenis
const maxSks = computed(() => {
  if (!modalData.value.jenis) return 9 // Default max
  return modalData.value.jenis.toLowerCase() === 'praktik' ? 3 : 9
})

// Validate SKS when jenis or sks changes
const validateSks = () => {
  if (modalData.value.sks !== null) {
    const max = maxSks.value
    if (modalData.value.sks > max) {
      modalData.value.sks = max
      error(`SKS maksimal untuk ${modalData.value.jenis} adalah ${max}`)
    }
    if (modalData.value.sks < 1) {
      modalData.value.sks = 1
    }
  }
}

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
  {
    name: 'jenis',
    label: 'Jenis Mata Kuliah',
    placeholder: 'Pilih jenis mata kuliah',
    type: 'dropdown',
    options: ['Praktik', 'Teori'],
  },
  {
    name: 'sks',
    label: 'Jumlah SKS Mata Kuliah',
    placeholder: 'Masukkan jumlah SKS mata kuliah',
    type: 'number',
    min: 1,
    max: maxSks,
    onChange: validateSks,
    helperText: computed(() => {
      if (!modalData.value.jenis) return 'Pilih jenis mata kuliah terlebih dahulu'
      const max = maxSks.value
      return `Maksimal ${max} SKS untuk ${modalData.value.jenis}`
    }),
  },
]

async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('makul')
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      makulList.value = []
      return
    }

    makulList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IMakul => ({
          id: item.id ?? null,
          kode: item.kode_makul ?? '',
          nama: item.nama_makul ?? '',
          jenis: item.jenis_makul
            ? item.jenis_makul.charAt(0).toUpperCase() + item.jenis_makul.slice(1)
            : '',
          sks: Number(item.sks_makul) || 0,
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
    jenis: '',
    sks: null,
  }

  showModal.value = true
}

// ================== Edit ==================
async function handleEdit(index: number) {
  const makul = makulList.value[index]
  if (!makul) return error('Data mata kuliah tidak ditemukan')

  isEdit.value = true
  editingId.value = makul.id

  modalData.value = {
    kode: makul.kode,
    nama: makul.nama,
    jenis: makul.jenis,
    sks: makul.sks,
  }

  showModal.value = true
}

// ================== SUBMIT (ADD + EDIT) ==================
async function handleSubmit(data: { kode: string; nama: string; jenis: string; sks: number }) {
  try {
    // Validate SKS before submitting
    const max = data.jenis.toLowerCase() === 'praktik' ? 3 : 9
    if (data.sks > max) {
      return error(`SKS tidak boleh lebih dari ${max} untuk jenis ${data.jenis}`)
    }
    if (data.sks < 1) {
      return error('SKS minimal adalah 1')
    }

    // Convert jenis to lowercase for API
    const payload = {
      kode_makul: data.kode,
      nama_makul: data.nama,
      jenis_makul: data.jenis.toLowerCase(), // Convert to lowercase
      sks_makul: data.sks,
    }

    if (isEdit.value && editingId.value) {
      await MasterDataAPI.update('makul', editingId.value, payload)
      success('Mata Kuliah berhasil diupdate!')
    } else {
      await MasterDataAPI.create('makul', payload)
      success('Mata Kuliah berhasil ditambahkan!')
    }

    await loadFromAPI()
    showModal.value = false
  } catch (err: any) {
    const message =
      err?.response?.data?.message ||
      (isEdit.value ? 'Gagal mengubah data makul' : 'Gagal menambahkan makul')
    error(message)
  }
}

// Delete Mata Kuliah
async function handleDelete(index: number) {
  const makul = makulList.value[index]
  if (!makul) return error('Data Mata Kuliah tidak ditemukan')

  if (!confirm(`Hapus Mata Kuliah "${makul.nama}"?`)) return
  try {
    const response = await MasterDataAPI.delete('makul', makul.id!)

    const message = response?.data?.message
    if (message) {
      return error(message)
    }

    success('Mata Kuliah berhasil dihapus!')
    await loadFromAPI()
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menghapus data mata kuliah'
    error(message)
  }
}

// Load data when component mounts
onMounted(loadFromAPI)
</script>

<template>
  <MasterDataTable
    :columns="['Kode Makul', 'Nama Mata Kuliah', 'Jenis Makul', 'SKS Makul']"
    :dataKeys="['kode', 'nama', 'jenis', 'sks']"
    :items="makulList"
    :searchKeys="['kode', 'nama']"
    :searchPlaceholder="'Cari berdasarkan kode atau nama mata kuliah...'"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
    :columnSizes="['150px', '180px', '150px', '1fr']"
    :show-default-actions="true"
    addLabel="Mata Kuliah"
  />

  <MasterDataModal
    v-model="showModal"
    :title="isEdit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah Baru'"
    subtitle="Masukkan informasi mata kuliah di bawah ini"
    :data="modalData"
    :fields="makulFields"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
