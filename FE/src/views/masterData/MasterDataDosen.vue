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
  >
  </MasterDataTable>

  <MasterDataModal
    v-model="showModal"
    title="Tambah Dosen Baru"
    subtitle="Masukkan informasi dosen di bawah ini"
    :fields="dosenFields"
    submit-text="Tambah"
    @submit="handleDosenSubmit"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'

const dosenList = ref([
  { kode: 'D001', nama: 'Dr. Suryo, S.Pd, M.Pd' },
  { kode: 'D002', nama: 'Prof. Ahmad Dahlan, M.Kom' },
  { kode: 'D003', nama: 'Dr. Siti Nurhaliza, S.T., M.T.' },
  { kode: 'D004', nama: 'Ir. Budi Santoso, M.Sc' },
  { kode: 'D005', nama: 'Dr. Rina Wijaya, S.Kom., M.Kom' },
  { kode: 'D006', nama: 'Prof. Dr. Hendra, M.T.' },
  { kode: 'D007', nama: 'Drs. Agus Salim, M.Pd' },
  { kode: 'D008', nama: 'Dr. Fitri Handayani, S.Si., M.Si' },
  { kode: 'D009', nama: 'Ir. Darmawan, M.Eng' },
  { kode: 'D010', nama: 'Dr. Yuni Kartika, S.Pd., M.Pd' },
  { kode: 'D011', nama: 'Prof. Rudi Hartono, Ph.D' },
  { kode: 'D012', nama: 'Dr. Lestari, S.Kom., M.T.' },
  { kode: 'D013', nama: 'Ir. Bambang Suryadi, M.Sc' },
  { kode: 'D014', nama: 'Dr. Nina Karlina, S.T., M.Kom' },
  { kode: 'D015', nama: 'Prof. Dr. Teguh Prasetyo, M.Eng' },
  { kode: 'D016', nama: 'Dra. Indah Permata, M.Pd' },
  { kode: 'D017', nama: 'Dr. Hadi Kusuma, S.Si., M.Si' },
  { kode: 'D018', nama: 'Ir. Faisal Rahman, M.T.' },
  { kode: 'D019', nama: 'Dr. Dewi Sartika, S.Kom., M.Kom' },
  { kode: 'D020', nama: 'Prof. Wahyu Nugroho, Ph.D' },
])

const dosenFields = [
  {
    name: 'kode',
    label: 'Kode Dosen',
    placeholder: 'Masukkan kode dosen',
    type: 'text',
  },
  {
    name: 'nama',
    label: 'Nama Dosen',
    placeholder: 'Masukkan nama dosen',
    type: 'text',
  },
]

const showModal = ref(false)

function handleAdd() {
  showModal.value = true
}

function handleDosenSubmit(data: { kode: string; nama: string }) {
  // Add the new dosen to the list
  dosenList.value.push({
    kode: data.kode,
    nama: data.nama,
  })

  // Optional: Show success message
  alert('Dosen berhasil ditambahkan!')
}

function handleEdit(index: number) {}

function handleDelete(index: number) {
  if (confirm('Hapus dosen ini?')) {
    dosenList.value.splice(index, 1)
    alert('Deleted')
  }
}
</script>
