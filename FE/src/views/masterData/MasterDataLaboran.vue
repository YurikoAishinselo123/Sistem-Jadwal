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
    addLabel="Laboran"
    :show-default-actions="true"
  >
  </MasterDataTable>
  <MasterDataModal
    v-model="showModal"
    title="Tambah Laboran Baru"
    subtitle="Masukkan informasi laboran di bawah ini"
    :fields="laboranFields"
    submit-text="Tambah"
    @submit="handleLaboranSubmit"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'

const laboranList = ref([
  { kode: 'L001', nama: 'Bapak Arif Setiawan' },
  { kode: 'L002', nama: 'Ibu Rina Permatasari' },
  { kode: 'L003', nama: 'Bapak Dedi Kurniawan' },
  { kode: 'L004', nama: 'Ibu Sinta Waluyo' },
  { kode: 'L005', nama: 'Bapak Rafi Nugraha' },
  { kode: 'L006', nama: 'Bapak Agus Pratama' },
  { kode: 'L007', nama: 'Ibu Maya Lestari' },
  { kode: 'L008', nama: 'Bapak Johan Wiratma' },
  { kode: 'L009', nama: 'Ibu Fitri Handayani' },
  { kode: 'L010', nama: 'Bapak Salman Hadi' },
  { kode: 'L011', nama: 'Ibu Karina Dewi' },
  { kode: 'L012', nama: 'Bapak Rizky Maulana' },
  { kode: 'L013', nama: 'Ibu Ayu Paramita' },
  { kode: 'L014', nama: 'Bapak Toni Firmansyah' },
  { kode: 'L015', nama: 'Ibu Nabila Safitri' },
])

const laboranFields = [
  {
    name: 'kode',
    label: 'Kode Laboran',
    placeholder: 'Masukkan kode laboran',
    type: 'text',
  },
  {
    name: 'nama',
    label: 'Nama Laboran',
    placeholder: 'Masukkan nama laboran',
    type: 'text',
  },
]

const showModal = ref(false)

function handleAdd() {
  showModal.value = true
}

function handleLaboranSubmit(data: { kode: string; nama: string }) {
  // Add the new dosen to the list
  laboranList.value.push({
    kode: data.kode,
    nama: data.nama,
  })

  // Optional: Show success message
  alert('Dosen berhasil ditambahkan!')
}

function handleEdit(index: number) {}

function handleDelete(index: number) {
  if (confirm('Hapus dosen ini?')) {
    laboranList.value.splice(index, 1)
    alert('Deleted')
  }
}
</script>
