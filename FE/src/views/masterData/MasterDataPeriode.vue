<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { IPeriode } from '@/interfaces/IPeriode'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

const periodeList = ref<IPeriode[]>([])

// Load Periode data
async function loadPeriode() {
  try {
    const response = await MasterDataAPI.getAll('periode')

    periodeList.value = response.data.map((p: any) => {
      const [namaPeriode, tahun] = p.periode.split(' ')

      return {
        id: p.id,
        namaPeriode,
        tahun: Number(tahun),
        status: p.status,
        tanggalMulai: p.tanggal_mulai,
        tanggalSelesai: p.tanggal_selesai,
      }
    }) as IPeriode[]
  } catch (err) {
    error('Gagal memuat data periode')
  }
}

// Computed table data
const tableData = computed(() =>
  [...periodeList.value].map((p) => ({
    ...p,
    periodeDisplay: `${p.namaPeriode} ${p.tahun}`,
    tanggalSelesaiDisplay: p.tanggalSelesai ?? '–',
  })),
)

// Modal State
const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')
const modalFields = ref<
  { name: string; label: string; placeholder: string; type: string; options?: string[] }[]
>([])

// Form data object
const modalData = ref<{ namaPeriode: string; tahun: number; tanggalMulai: string }>({
  namaPeriode: '',
  tahun: new Date().getFullYear(),
  tanggalMulai: '',
})

// Open Add Modal
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
    {
      name: 'tanggalMulai',
      label: 'Tanggal Mulai',
      placeholder: 'Pilih tanggal mulai',
      type: 'date',
    },
  ]

  modalData.value = {
    namaPeriode: '',
    tahun: new Date().getFullYear(),
    tanggalMulai: '',
  }

  showModal.value = true
}

// Handle form submit
async function handleSubmitPeriode(data: {
  namaPeriode: string
  tahun: number
  tanggalMulai: string
}) {
  if (!data.namaPeriode || !data.tahun || !data.tanggalMulai) return

  const newPeriode = {
    periode: `${data.namaPeriode} ${data.tahun}`,
    status: 'Aktif',
    tanggal_mulai: data.tanggalMulai,
    tanggal_selesai: null,
  }

  try {
    await MasterDataAPI.create('periode', newPeriode)
    await loadPeriode()
    showModal.value = false
    success('Periode berhasil ditambahkan!')
  } catch (err) {
    error('Gagal menambahkan periode')
  }
}

// Finish Periode
async function finishPeriode(item: IPeriode) {
  const updated = {
    periode: `${item.namaPeriode} ${item.tahun}`,
    status: 'Selesai',
    tanggal_mulai: item.tanggalMulai,
    tanggal_selesai: new Date().toISOString().slice(0, 10),
  }

  try {
    await MasterDataAPI.update('periode', item.id!, updated)
    await loadPeriode()
    success('Periode berhasil ditandai selesai!')
  } catch (err) {
    console.warn('API update failed, updating locally', err)
    Object.assign(item, updated)
  }
}

function confirmFinish(row: any) {
  const original = periodeList.value.find(
    (p) => `${p.namaPeriode} ${p.tahun}` === row.periodeDisplay,
  )
  if (!original) return

  if (confirm(`Yakin ingin menyelesaikan periode ${original.namaPeriode} ${original.tahun}?`)) {
    finishPeriode(original)
  }
}

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
