<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IRuang {
  id: number | null
  kode: string
  nama: string
}

// ================== STATE ==================
const ruangList = ref<IRuang[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref({
  kode: '',
  nama: '',
})

// ================== MODAL FIELDS ==================
const ruangFields = [
  {
    name: 'kode',
    label: 'Kode Ruang',
    placeholder: 'Masukkan kode ruang',
    type: 'text',
  },
  {
    name: 'nama',
    label: 'Nama Ruang',
    placeholder: 'Masukkan nama ruang',
    type: 'text',
  },
]

// ================== API FUNCTIONS ==================s
async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('ruangan')
    const rawData = response?.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      ruangList.value = []
      success('Data ruang masih kosong')
      return
    }

    ruangList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IRuang => ({
          id: item.id ?? null,
          kode: item.kode_ruangan ?? '',
          nama: item.nama_ruangan ?? '',
        }),
      )
  } catch (err) {
    ruangList.value = []
    error('Gagal memuat data ruang')
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

// ================== EDIT ==================
function handleEdit(index: number) {
  const ruang = ruangList.value[index]
  if (!ruang) return error('Data ruang tidak ditemukan')

  isEdit.value = true
  editingId.value = ruang.id

  modalData.value = {
    kode: ruang.kode,
    nama: ruang.nama,
  }

  showModal.value = true
}

// ================== SUBMIT (ADD + EDIT) ==================
async function handleSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_ruangan: data.kode,
      nama_ruangan: data.nama,
    }

    if (isEdit.value && editingId.value) {
      await MasterDataAPI.update('ruangan', editingId.value, payload)
      success('Ruang berhasil diperbarui!')
    } else {
      await MasterDataAPI.create('ruangan', payload)
      success('Ruang berhasil ditambahkan!')
    }

    await loadFromAPI()
    showModal.value = false
  } catch (err) {
    error(isEdit.value ? 'Gagal mengubah data makul' : 'Gagal menambahkan makul')
  }
}

// ================== DELETE ==================
async function handleDelete(index: number) {
  const ruang = ruangList.value[index]
  if (!ruang) return error('Data ruang tidak ditemukan')

  if (!confirm(`Hapus ruang "${ruang.nama}"?`)) return

  try {
    const response = await MasterDataAPI.delete('ruangan', ruang.id!)

    // const message = response?.data.message

    // For Temp
    const message = response?.data.messsage
    alert(message)

    success('Ruang berhasil dihapus!')
    await loadFromAPI()
  } catch (err) {
    error('Gagal menghapus data ruang')
  }
}

onMounted(loadFromAPI)
</script>

<template>
  <MasterDataTable
    :columns="['Kode Ruang', 'Nama Ruang']"
    :dataKeys="['kode', 'nama']"
    :cellAlign="['left', 'left']"
    :items="ruangList"
    :searchKeys="['kode', 'nama']"
    searchPlaceholder="Cari berdasarkan kode atau nama ruang..."
    addLabel="Ruang Kelas"
    :columnSizes="['150px', '1fr']"
    :show-default-actions="true"
    @add="handleAdd"
    @edit="handleEdit"
    @delete="handleDelete"
  />

  <MasterDataModal
    v-model="showModal"
    :title="isEdit ? 'Edit Ruang Kelas' : 'Tambah Ruang Kelas Baru'"
    subtitle="Masukkan informasi ruang kelas di bawah ini"
    :fields="ruangFields"
    :data="modalData"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
