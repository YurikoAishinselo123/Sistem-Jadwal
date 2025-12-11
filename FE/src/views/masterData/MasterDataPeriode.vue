<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { IPeriode } from '@/interfaces/IPeriode'
import MasterDataTable from '@/components/MasterDataTable.vue'
import MasterDataModal from '@/components/MasterDataModal.vue'
import MasterDataAPI from '@/services/masterDataAPI'
import { useToast } from '@/composables/UseToast'

const { success, error } = useToast()

interface IPeriodeForm {
  namaPeriode: string
  tahun: number
  tanggalMulai: string
}

const periodeList = ref<IPeriode[]>([])

async function loadPeriode() {
  try {
    const response = await MasterDataAPI.getAll('periode')
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData)) {
      periodeList.value = []
      return
    }

    periodeList.value = rawData.map(
      (item: any): IPeriode => ({
        id: item.id ?? null,
        periode: item.periode ?? '',
        status: item.status ?? '',
        tanggalMulai: item.tanggal_mulai ?? '',
        tanggalSelesai: item.tanggal_selesai ?? null,
      }),
    )
  } catch (err) {
    error('Gagal memuat data periode')
  }
}

const tableData = computed(() =>
  periodeList.value.map((p) => ({
    ...p,
    periodeDisplay: p.periode,
    tanggalMulaiDisplay: formatDateDMY(p.tanggalMulai) ?? '–',
    tanggalSelesaiDisplay: formatDateDMY(p.tanggalSelesai) ?? '–',
  })),
)

// ----------------------
// MODAL STATE
// ----------------------
const showModal = ref(false)
const modalTitle = ref('')
const modalSubmitText = ref('')

const modalFields = ref<
  { name: string; label: string; placeholder: string; type: string; options?: string[] }[]
>([])

// ----------------------
// FORM MODEL (FIX)
// ----------------------
const modalData = ref<IPeriodeForm>({
  namaPeriode: '',
  tahun: new Date().getFullYear(),
  tanggalMulai: '',
})

// ----------------------
// OPEN ADD MODAL
// ----------------------
function handleAdd() {
  modalTitle.value = 'Tambah Periode Baru'
  modalSubmitText.value = 'Tambah'

  modalFields.value = [
    {
      name: 'namaPeriode',
      label: 'Nama Periode',
      placeholder: 'Pilih jenis periode',
      type: 'dropdown',
      options: ['Ganjil', 'Genap', 'Ganjil Pendek', 'Genap Pendek'],
    },
    {
      name: 'tahun',
      label: 'Tahun',
      placeholder: 'Masukkan tahun',
      type: 'number',
    },
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

// ----------------------
// SUBMIT PERIODE
// ----------------------
async function handleSubmitPeriode(data: IPeriodeForm) {
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

// ----------------------
// FINISH PERIODE
// ----------------------
async function finishPeriode(item: IPeriode) {
  const payload = {
    periode: item.periode,
    status: 'Selesai',
    tanggal_mulai: item.tanggalMulai,
    tanggal_selesai: new Date().toISOString().slice(0, 10),
  }

  try {
    await MasterDataAPI.update('periode', item.id!, payload)
    await loadPeriode()
    success('Periode berhasil ditandai selesai!')
  } catch (err) {
    console.warn('API update failed, updating locally', err)
    Object.assign(item, payload)
  }
}

function confirmFinish(row: any) {
  const original = periodeList.value.find((p) => p.periode === row.periodeDisplay)
  if (!original) return

  if (confirm(`Yakin ingin menyelesaikan periode ${original.periode}?`)) {
    finishPeriode(original)
  }
}

function formatDateDMY(dateStr: string | null): string {
  if (!dateStr) return '–'
  const [y, m, d] = dateStr.split('-')
  return `${d}-${m}-${y}`
}

onMounted(loadPeriode)
</script>

<template>
  <MasterDataTable
    :columns="['Periode', 'Status', 'Tanggal Mulai', 'Tanggal Selesai']"
    :dataKeys="['periodeDisplay', 'status', 'tanggalMulaiDisplay', 'tanggalSelesaiDisplay']"
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
