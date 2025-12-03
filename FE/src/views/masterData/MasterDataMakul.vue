<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

const makulList = ref<any[]>([])
const showModal = ref(false)

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

async function loadMakul() {
  try {
    const response = await MasterDataAPI.getAll('makul')
    const data = response.data

    // Map API fields → table fields
    makulList.value = data.map((item: any) => ({
      id: item.id,
      kode: item.kode_makul,
      nama: item.nama_makul,
    }))
  } catch (err) {
    // console.warn('API failed : ', err)
    error('Gagal memuat data mata kuliah')
  }
}

// Add new Mata Kuliah
async function handleMakulSubmit(data: { kode: string; nama: string }) {
  try {
    const payload = {
      kode_makul: data.kode,
      nama_makul: data.nama,
    }

    await MasterDataAPI.create('makul', payload)
    await loadMakul()
    showModal.value = false

    success('Mata Kuliah berhasil ditambahkan!')
  } catch (err) {
    // console.warn('Failed Add new data : ', err)
    error('Gagal menambahkan mata kuliah')
  }
}

// Open modal
const handleAdd = () => {
  showModal.value = true
}

// Edit Mata Kuliah
async function handleEdit(index: number) {
  const makul = makulList.value[index]
  const newNama = prompt('Edit nama mata kuliah:', makul.nama)

  if (newNama !== null && newNama.trim() !== '') {
    try {
      await MasterDataAPI.update('makul', makul.id, {
        kode_makul: makul.kode,
        nama_makul: newNama,
      })
      await loadMakul()

      success('Mata Kuliah berhasil diupdate!')
    } catch (err) {
      console.warn('API update failed', err)
      error('Gagal mengupdate mata kuliah')
    }
  }
}

// Delete Mata Kuliah
async function handleDelete(index: number) {
  const makul = makulList.value[index]

  if (confirm(`Hapus Mata Kuliah "${makul.nama}"?`)) {
    try {
      await MasterDataAPI.delete('makul', makul.id)
      await loadMakul()

      success('Mata Kuliah berhasil dihapus!')
    } catch (err) {
      // console.warn('API delete failed', err)
      error('Gagal menghapus mata kuliah')
    }
  }
}

// Load data when component mounts
onMounted(loadMakul)
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
    title="Tambah Mata Kuliah Baru"
    subtitle="Masukkan informasi mata kuliah di bawah ini"
    :fields="makulFields"
    submit-text="Tambah"
    @submit="handleMakulSubmit"
  />
</template>
