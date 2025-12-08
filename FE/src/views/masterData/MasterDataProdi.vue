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
const prodiList = ref<any[]>([])
const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref<number | null>(null)

const modalData = ref({
  kode: '',
  nama: '',
})

// ================== MODAL FIELDS ==================c
const prodiFields = [
  { name: 'kode', label: 'Kode Prodi', placeholder: 'Masukkan kode prodi', type: 'text' },
  { name: 'nama', label: 'Nama Prodi', placeholder: 'Masukkan nama prodi', type: 'text' },
]

// ================== API FUNCTIONS ==================s
async function loadFromAPI() {
  try {
    const response = await MasterDataAPI.getAll('prodi')
    const rawData = response?.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      prodiList.value = []
      success('Data prodi masih kosong')
      return
    }

    prodiList.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): ILaboran => ({
          id: item.id ?? null,
          kode: item.kode_prodi ?? '',
          nama: item.nama_prodi ?? '',
        }),
      )
  } catch (err) {
    prodiList.value = []
    error('Gagal memuat data prodi')
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
  const prodi = prodiList.value[index]
  if (!prodi) return error('Data prodi tidak ditemukan')

  isEdit.value = true
  editingId.value = prodi.id

  modalData.value = {
    kode: prodi.kode,
    nama: prodi.nama,
  }

  showModal.value = true
}

// ================== SUBMIT (ADD + EDIT) ==================
async function handleSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_prodi: data.kode,
      nama_prodi: data.nama,
    }

    if (isEdit.value && editingId.value) {
      await MasterDataAPI.update('prodi', editingId.value, payload)
      success('Prodi berhasil diupdate!')
    } else {
      await MasterDataAPI.create('prodi', payload)
      success('Prodi berhasil ditambahkan!')
    }

    await loadFromAPI()
    showModal.value = false
  } catch (err) {
    error(isEdit.value ? 'Gagal update prodi' : 'Gagal menambahkan prodi')
  }
}

// Delete
async function handleDelete(index: number) {
  const prodi = prodiList.value[index]
  if (!prodi) return error('Data ruang tidak ditemukan')

  if (!confirm(`Hapus ruang "${prodi.nama}"?`)) return
  try {
    const response = await MasterDataAPI.delete('prodi', prodi.id!)
    success('Prodi berhasil dihapus!')
    await loadFromAPI()
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menghapus data prodi'
    error(message)
  }
}

onMounted(loadFromAPI)
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
    :title="isEdit ? 'Edit Laboran' : 'Tambah Laboran Baru'"
    subtitle="Masukkan informasi prodi di bawah ini"
    :fields="prodiFields"
    :data="modalData"
    :submit-text="isEdit ? 'Edit' : 'Tambah'"
    @submit="handleSubmit"
  />
</template>
