<script setup lang="ts">
import { ref, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IJam {
  jam: string
  id: number
  jenis?: 'pagi' | 'malam'
}

const pagiItems = ref<IJam[]>([])
const malamItems = ref<IJam[]>([])

const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<{ name: string; label: string; placeholder: string; type: string }[]>([])
const modalData = ref<IJam>({ jam: '', id: 0 })

let editingIndex: number | null = null
let editingList: 'pagi' | 'malam' | null = null

function getJenisFromTime(time: string): 'pagi' | 'malam' {
  if (!time || typeof time !== 'string') return 'pagi'

  const parts = time.split(':')

  if (parts.length < 2) return 'pagi'

  const hour = Number(parts[0])
  const minute = Number(parts[1])

  if (Number.isNaN(hour) || Number.isNaN(minute)) return 'pagi'

  const totalMinutes = hour * 60 + minute

  const MORNING_START = 5 * 60 // 05:00
  const MORNING_END = 16 * 60 + 59 // 16:59

  return totalMinutes >= MORNING_START && totalMinutes <= MORNING_END ? 'pagi' : 'malam'
}

function buildTimeField(list: 'pagi' | 'malam') {
  if (list === 'pagi') {
    return {
      name: 'jam',
      label: 'Jam',
      placeholder: '05:00 - 16:59',
      type: 'time',
      min: '05:00',
      max: '16:59',
    }
  }

  return {
    name: 'jam',
    label: 'Jam',
    placeholder: '17:00 - 23:59 or 00:00 only',
    type: 'time',
    min: '17:00',
    max: '23:59',
  }
}

async function loadTimeSlots() {
  try {
    const response = await MasterDataAPI.getAll('waktu')
    const data = response.data

    pagiItems.value = []
    malamItems.value = []

    if (!Array.isArray(data) || data.length === 0) {
      success('Data waktu masih kosong')
      return
    }

    data.forEach((item: any) => {
      if (!item?.jam) return

      const jenis = getJenisFromTime(item.jam)

      const formatted: IJam = {
        id: item.id,
        jam: item.jam,
        jenis,
      }

      if (jenis === 'pagi') {
        pagiItems.value.push(formatted)
      } else {
        malamItems.value.push(formatted)
      }
    })
  } catch (err) {
    error('Gagal memuat data waktu!')
  }
}

onMounted(loadTimeSlots)

async function handleSubmitTime(data: IJam) {
  if (!data.jam || !editingList) return

  const targetList = editingList === 'pagi' ? pagiItems : malamItems

  const hour = Number(data.jam.split(':')[0])

  if (editingList === 'pagi') {
    if (hour < 5 || hour > 16) {
      error('Jam pagi hanya 05:00 - 16:59')
      return
    }
  } else {
    if (!(hour >= 17 || data.jam === '00:00')) {
      error('Jam malam hanya 17:00 - 23:59 atau 00:00 saja')
      return
    }
  }

  try {
    if (editingIndex !== null) {
      const item = targetList.value[editingIndex]

      if (!item) {
        error('Data tidak ditemukan')
        return
      }

      await MasterDataAPI.update('waktu', item.id, {
        jam: data.jam,
      })

      success('Jam berhasil diperbarui!')
    } else {
      await MasterDataAPI.create('waktu', {
        jam: data.jam,
      })

      success('Jam berhasil ditambahkan!')
    }

    await loadTimeSlots()
    showModal.value = false
  } catch (err) {
    error('Terjadi kesalahan pada server')
  }

  editingIndex = null
  editingList = null
}

async function handleDelete(list: 'pagi' | 'malam', index: number) {
  const targetList = list === 'pagi' ? pagiItems.value : malamItems.value
  const item = targetList[index]

  if (!item) return error('Data tidak ditemukan')

  if (confirm(`Hapus jam ${item.jam}?`)) {
    try {
      await MasterDataAPI.delete('waktu', item.id)
      await loadTimeSlots()
      success('Jam berhasil dihapus!')
    } catch (err) {
      error('Gagal menghapus jam!')
    }
  }
}

function handleAdd(list: 'pagi' | 'malam') {
  editingIndex = null
  editingList = list

  modalTitle.value = `Tambah Jam ${list === 'pagi' ? 'Pagi' : 'Malam'}`
  modalSubmitText.value = 'Tambah'

  modalFields.value = [buildTimeField(list)]

  modalData.value = { jam: '', id: 0 }
  showModal.value = true
}

function handleEdit(list: 'pagi' | 'malam', index: number) {
  const targetList = list === 'pagi' ? pagiItems.value : malamItems.value
  const item = targetList[index]
  if (!item) return

  editingIndex = index
  editingList = list

  modalTitle.value = `Edit Jam ${list === 'pagi' ? 'Pagi' : 'Malam'}`
  modalSubmitText.value = 'Simpan'

  modalFields.value = [buildTimeField(list)]

  modalData.value = { jam: item.jam, id: item.id }
  showModal.value = true
}

// Button handler shortcuts
const handleAddPagi = () => handleAdd('pagi')
const handleAddMalam = () => handleAdd('malam')
const handleEditPagi = (i: number) => handleEdit('pagi', i)
const handleEditMalam = (i: number) => handleEdit('malam', i)
const handleDeletePagi = (i: number) => handleDelete('pagi', i)
const handleDeleteMalam = (i: number) => handleDelete('malam', i)
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 min-h-0 h-full">
    <MasterDataTable
      title="Waktu Pagi"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="pagiItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam pagi..."
      addLabel="Jam Pagi"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddPagi"
      @edit="handleEditPagi"
      @delete="handleDeletePagi"
    />

    <MasterDataTable
      title="Waktu Malam"
      :columns="['Jam']"
      :dataKeys="['jam']"
      :items="malamItems"
      :searchKeys="['jam']"
      searchPlaceholder="Cari jam malam..."
      addLabel="Jam Malam"
      :columnSizes="['1fr']"
      :show-default-actions="true"
      @add="handleAddMalam"
      @edit="handleEditMalam"
      @delete="handleDeleteMalam"
    />
  </div>

  <MasterDataModal
    v-model="showModal"
    :title="modalTitle"
    subtitle=""
    :fields="modalFields"
    :data="modalData"
    :submit-text="modalSubmitText"
    @submit="handleSubmitTime"
  />
</template>
