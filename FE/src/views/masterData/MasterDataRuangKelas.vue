<script setup lang="ts">
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import { ref } from 'vue'

// Dummy data for Ruang Kelas
const ruangList = ref([
  { kode: 'R101', nama: 'Ruang Teori 101', kapasitas: 40, lokasi: 'Gedung A - Lantai 1' },
  { kode: 'R102', nama: 'Ruang Teori 102', kapasitas: 35, lokasi: 'Gedung A - Lantai 1' },
  { kode: 'R201', nama: 'Lab Komputer 201', kapasitas: 25, lokasi: 'Gedung B - Lantai 2' },
  { kode: 'R202', nama: 'Lab Jaringan 202', kapasitas: 20, lokasi: 'Gedung B - Lantai 2' },
  { kode: 'R301', nama: 'Ruang Seminar 301', kapasitas: 60, lokasi: 'Gedung C - Lantai 3' },
  { kode: 'R302', nama: 'Ruang Rapat 302', kapasitas: 15, lokasi: 'Gedung C - Lantai 3' },
  { kode: 'R401', nama: 'Ruang Teori 401', kapasitas: 45, lokasi: 'Gedung D - Lantai 4' },
  { kode: 'R402', nama: 'Ruang Multimedia 402', kapasitas: 30, lokasi: 'Gedung D - Lantai 4' },
  { kode: 'R403', nama: 'Ruang Musik 403', kapasitas: 20, lokasi: 'Gedung D - Lantai 4' },
  { kode: 'R501', nama: 'Lab Elektronika 501', kapasitas: 18, lokasi: 'Gedung E - Lantai 5' },
  { kode: 'R502', nama: 'Lab Robotik 502', kapasitas: 22, lokasi: 'Gedung E - Lantai 5' },
  { kode: 'R503', nama: 'Lab Mekatronika 503', kapasitas: 25, lokasi: 'Gedung E - Lantai 5' },
  { kode: 'R601', nama: 'Ruang Studio 601', kapasitas: 40, lokasi: 'Gedung F - Lantai 6' },
  { kode: 'R602', nama: 'Ruang Editing 602', kapasitas: 12, lokasi: 'Gedung F - Lantai 6' },
  { kode: 'R603', nama: 'Ruang Broadcasting 603', kapasitas: 28, lokasi: 'Gedung F - Lantai 6' },
])

// Modal state
const showModal = ref(false)

// Fields for the modal
const ruangFields = [
  { name: 'kode', label: 'Kode Ruang', placeholder: 'Masukkan kode ruang', type: 'text' },
  { name: 'nama', label: 'Nama Ruang', placeholder: 'Masukkan nama ruang', type: 'text' },
  { name: 'kapasitas', label: 'Kapasitas', placeholder: 'Masukkan kapasitas', type: 'number' },
  { name: 'lokasi', label: 'Lokasi', placeholder: 'Masukkan lokasi', type: 'text' },
]

// Handlers
const handleAddRuang = () => {
  showModal.value = true
}

const handleRuangSubmit = (data: {
  kode: string
  nama: string
  kapasitas: number
  lokasi: string
}) => {
  // Optional: prevent duplicate kode
  const exists = ruangList.value.some((r) => r.kode === data.kode)
  if (exists) {
    alert('Kode ruang sudah ada!')
    return
  }

  ruangList.value.push({
    kode: data.kode,
    nama: data.nama,
    kapasitas: data.kapasitas,
    lokasi: data.lokasi,
  })

  showModal.value = false
  alert('Ruang kelas berhasil ditambahkan!')
}

const handleEdit = (index: number) => {
  // Optional: implement edit modal
  console.log('Edit ruang at index:', index)
}

const handleDelete = (index: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus ruang ini?')) {
    ruangList.value.splice(index, 1)
    alert('Ruang kelas berhasil dihapus!')
  }
}
</script>

<template>
  <MasterDataTable
    :columns="['Kode Ruang', 'Nama Ruang', 'Kapasitas', 'Lokasi']"
    :dataKeys="['kode', 'nama', 'kapasitas', 'lokasi']"
    :cellAlign="['left', 'left', 'center', 'left']"
    :items="ruangList"
    :searchKeys="['kode', 'nama', 'kapasitas', 'lokasi']"
    searchPlaceholder="Cari berdasarkan kode, nama, kapasitas, atau lokasi..."
    addLabel="Ruang Kelas"
    :columnSizes="['150px', '1fr', '200px', '200px']"
    :show-default-actions="true"
    @add="handleAddRuang"
    @edit="handleEdit"
    @delete="handleDelete"
  />

  <!-- Modal -->
  <MasterDataModal
    v-model="showModal"
    title="Tambah Ruang Kelas Baru"
    subtitle="Masukkan informasi ruang kelas di bawah ini"
    :fields="ruangFields"
    submit-text="Tambah"
    @submit="handleRuangSubmit"
  />
</template>
