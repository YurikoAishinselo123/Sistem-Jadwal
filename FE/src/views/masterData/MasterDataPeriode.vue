<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import DataPeriode from '@/dummy data/dataPeriode.json'

interface IPeriode {
  id?: number | string
  namaPeriode: string
  tahun: number
  status: 'Aktif' | 'Selesai'
  tanggalMulai: string
  tanggalSelesai: string | null
}

const periodeList = ref<IPeriode[]>([])

async function loadPeriode() {
  try {
    const response = await MasterDataAPI.getAll('periode')
    periodeList.value = (response.data.data || response.data).map((p: any) => ({
      ...p,
      status: p.status === 'Aktif' ? 'Aktif' : 'Selesai',
    })) as IPeriode[]
  } catch (error) {
    console.warn('API failed, loading local JSON fallback', error)
    periodeList.value = (DataPeriode as IPeriode[]).map((p) => ({
      ...p,
      status: p.status === 'Aktif' ? 'Aktif' : 'Selesai',
    }))
  }
}

// Computed table data
const tableData = computed(() =>
  [...periodeList.value]
    .sort((a, b) => b.tahun - a.tahun)
    .map((p) => ({
      ...p,
      periodeDisplay: `${p.namaPeriode} ${p.tahun}`,
      tanggalSelesaiDisplay: p.tanggalSelesai ?? '–',
    })),
)

// === Modal State ===
const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<
  { name: string; label: string; placeholder: string; type: string; options?: string[] }[]
>([])
const modalData = ref<{ namaPeriode: string; tahun: number; tanggalMulai: string }>({
  namaPeriode: '',
  tahun: new Date().getFullYear(),
  tanggalMulai: new Date().toISOString().slice(0, 10),
})

// Add Periode
function handleAdd() {
  modalTitle.value = 'Tambah Periode Baru'
  modalSubmitText.value = 'Tambah'
  modalFields.value = [
    {
      name: 'namaPeriode',
      label: 'Nama Periode',
      placeholder: 'Masukkan nama periode',
      type: 'select',
      options: ['Ganjil', 'Genap', 'Ganjil Pendek', 'Genap Pendek'],
    },
    { name: 'tahun', label: 'Tahun', placeholder: 'Masukkan tahun', type: 'number' },
  ]
  modalData.value = {
    namaPeriode: '',
    tahun: new Date().getFullYear(),
    tanggalMulai: new Date().toISOString().slice(0, 10),
  }
  showModal.value = true
}

// Handle modal submit
async function handleSubmitPeriode(data: { namaPeriode: string; tahun: number }) {
  if (!data.namaPeriode || !data.tahun) return

  const newPeriode: IPeriode = {
    namaPeriode: data.namaPeriode,
    tahun: data.tahun,
    status: 'Aktif',
    tanggalMulai: new Date().toISOString().slice(0, 10),
    tanggalSelesai: null,
  }

  try {
    await MasterDataAPI.create('periode', newPeriode)
    await loadPeriode()
    showModal.value = false
    alert('Periode berhasil ditambahkan!')
  } catch (error) {
    console.warn('API create failed, updating local JSON only', error)
    periodeList.value.push(newPeriode)
    showModal.value = false
    alert('Periode berhasil ditambahkan (lokal)!')
  }
}

// Finish periode
async function finishPeriode(item: IPeriode) {
  const updated = {
    ...item,
    status: 'Selesai',
    tanggalSelesai: new Date().toISOString().slice(0, 10),
  }

  try {
    await MasterDataAPI.update('periode', item.id || item.namaPeriode, updated)
    await loadPeriode()
    alert('Periode berhasil ditandai selesai!')
  } catch (error) {
    console.warn('API update failed, updating local JSON only', error)
    Object.assign(item, updated)
    alert('Periode berhasil ditandai selesai (lokal)!')
  }
}

function confirmFinish(row: any) {
  const original = periodeList.value.find(
    (p) => `${p.namaPeriode} ${p.tahun}` === row.periodeDisplay,
  )
  if (!original) return

  const yes = confirm(
    `Apakah Anda yakin ingin menyelesaikan periode:\n${original.namaPeriode} ${original.tahun}?`,
  )
  if (!yes) return

  finishPeriode(original)
}

// Load data when component mounts
onMounted(loadPeriode)
</script>

<template>
  <MasterDataTable
    :columns="['Periode', 'Status', 'Tanggal Mulai', 'Tanggal Selesai']"
    :dataKeys="['periodeDisplay', 'status', 'tanggalMulai', 'tanggalSelesaiDisplay']"
    :items="tableData"
    :searchKeys="['periodeDisplay']"
    searchPlaceholder="Cari periode..."
    addLabel="Tambah Periode"
    :show-default-actions="false"
    @add="handleAdd"
  >
    <template #actions="{ row }">
      <div class="flex justify-center">
        <button
          v-if="row.status === 'Aktif'"
          @click="confirmFinish(row)"
          class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700"
        >
          Tandai Selesai
        </button>
        <span v-else class="text-gray-400 italic">Sudah Selesai</span>
      </div>
    </template>
  </MasterDataTable>

  <!-- Modal -->
  <MasterDataModal
    v-model="showModal"
    :title="modalTitle"
    subtitle=""
    :fields="modalFields"
    :data="modalData"
    :submit-text="modalSubmitText"
    @submit="handleSubmitPeriode"
  />
</template>
