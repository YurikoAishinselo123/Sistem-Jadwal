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
  >
  </MasterDataTable>
  <MasterDataModal
    v-model="showModal"
    title="Tambah Mata Kuliah Baru"
    subtitle="Masukkan informasi mata kuliah di bawah ini"
    :fields="makulFIelds"
    submit-text="Tambah"
    @submit="handleMakulSubmit"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'

const makulList = ref([
  { kode: 'MK001', nama: 'Algoritma dan Pemrograman' },
  { kode: 'MK002', nama: 'Struktur Data' },
  { kode: 'MK003', nama: 'Basis Data' },
  { kode: 'MK004', nama: 'Jaringan Komputer' },
  { kode: 'MK005', nama: 'Sistem Operasi' },
  { kode: 'MK006', nama: 'Pemrograman Berorientasi Objek' },
  { kode: 'MK007', nama: 'Rekayasa Perangkat Lunak' },
  { kode: 'MK008', nama: 'Kecerdasan Buatan' },
  { kode: 'MK009', nama: 'Pemrograman Web' },
  { kode: 'MK010', nama: 'Keamanan Informasi' },
  { kode: 'MK011', nama: 'Manajemen Proyek TI' },
  { kode: 'MK012', nama: 'Grafika Komputer' },
  { kode: 'MK013', nama: 'Sistem Informasi' },
  { kode: 'MK014', nama: 'Analisis dan Perancangan Sistem' },
  { kode: 'MK015', nama: 'Pemrograman Mobile' },
])

const makulFIelds = [
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
const showModal = ref(false)

function handleAdd() {
  showModal.value = true
}

function handleMakulSubmit(data: { kode: string; nama: string }) {
  // Add the new dosen to the list
  makulList.value.push({
    kode: data.kode,
    nama: data.nama,
  })

  // Optional: Show success message
  alert('Dosen berhasil ditambahkan!')
}

function handleEdit(index: number) {}

function handleDelete(index: number) {
  if (confirm('Hapus mata kuliah ini?')) {
    makulList.value.splice(index, 1)
    alert('Deleted')
  }
}
</script>
