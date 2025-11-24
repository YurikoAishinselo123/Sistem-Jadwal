<script setup lang="ts">
import { ref, computed } from 'vue'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'

interface IPeriode {
  namaPeriode: string
  tahun: number
  status: 'Aktif' | 'Selesai'
  tanggalMulai: string
  tanggalSelesai: string | null
}

// Initial periode list
const periodeList = ref<IPeriode[]>([
  {
    namaPeriode: 'Gasal Pendek',
    tahun: 2025,
    status: 'Aktif',
    tanggalMulai: randomDate(),
    tanggalSelesai: null,
  },
  {
    namaPeriode: 'Genap',
    tahun: 2024,
    status: 'Selesai',
    tanggalMulai: randomDate(),
    tanggalSelesai: randomDate(),
  },
  {
    namaPeriode: 'Genap Pendek',
    tahun: 2024,
    status: 'Aktif',
    tanggalMulai: randomDate(),
    tanggalSelesai: null,
  },
])

function randomDate() {
  const start = new Date(2023, 0, 1)
  const end = new Date(2025, 0, 1)
  return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()))
    .toISOString()
    .slice(0, 10)
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
const modalFields = ref<{ name: string; label: string; placeholder: string; type: string }[]>([])
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
      type: 'text',
    },
    { name: 'tahun', label: 'Tahun', placeholder: 'Masukkan tahun', type: 'number' },
  ]
  modalData.value = {
    namaPeriode: '',
    tahun: new Date().getFullYear(),
    // tanggalMulai will always be current date automatically
    tanggalMulai: new Date().toISOString().slice(0, 10),
  }
  showModal.value = true
}

// Handle modal submit
function handleSubmitPeriode(data: { namaPeriode: string; tahun: number }) {
  if (!data.namaPeriode || !data.tahun) return

  periodeList.value.push({
    namaPeriode: data.namaPeriode,
    tahun: data.tahun,
    status: 'Aktif',
    tanggalMulai: new Date().toISOString().slice(0, 10), // use current date
    tanggalSelesai: null,
  })

  showModal.value = false
}

// Finish periode
function confirmFinish(row: any) {
  const original = periodeList.value.find(
    (p) => `${p.namaPeriode} ${p.tahun}` === row.periodeDisplay,
  )
  if (!original) return

  const yes = confirm(
    `Apakah Anda yakin ingin menyelesaikan periode:\n\n${original.namaPeriode} ${original.tahun}?\n\n` +
      `Jika diselesaikan, periode ini tidak dapat diedit lagi.`,
  )
  if (!yes) return

  finishPeriode(original)
}

function finishPeriode(item: IPeriode) {
  item.status = 'Selesai'
  item.tanggalSelesai = new Date().toISOString().slice(0, 10)
  alert('Periode berhasil ditandai selesai.')
}
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
