<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import FilterJadwal from '@/components/FilterJadwal.vue'
import CustomPrintPage from '@/components/CustomPrintPage.vue'
import ConfirmationDialog from '@/components/ConfirmationDialog.vue'
import type { IFilterDashboard, IJadwalResponse } from '@/interfaces/IFilterDashboard'
import { dashboardAPI } from '@/services/dashboardAPI'
import { useRouter } from 'vue-router'
import CustomTable from '@/components/CustomTable.vue'
import { useToast } from '@/composables/UseToast'

const router = useRouter()
const { success, error } = useToast()

// TABLE COLUMNS
const columns = ref([
  { key: 'hari', label: 'Hari' },
  { key: 'programStudi', label: 'Program Studi' },
  { key: 'mataKuliah', label: 'Mata Kuliah' },
  { key: 'dosen', label: 'Dosen Pengajar' },
  { key: 'laboran', label: 'Laboran' },
  { key: 'ruang', label: 'Ruang & Kelas' },
  { key: 'status', label: 'Status' },
  { key: 'waktu', label: 'Waktu' },
])

// DATA FROM API
const jadwalData = ref<IJadwalResponse[]>([])
const prodiList = ref<string[]>([])
const makulList = ref<string[]>([])
const periodeList = ref<string[]>([])
const laboranList = ref<string[]>([])
const isLoading = ref(false)
const apiError = ref('')
const showPrintPage = ref(false)

// FILTER STATE
const activeFilters = ref<IFilterDashboard>({
  periode: '',
  hari: '',
  programStudi: '',
  mataKuliah: '',
  jenisJadwal: 'Jadwal Semester',
  laboran: '',
  waktuPerkuliahan: '',
})

// CONFIRMATION DIALOG STATE
const dialogOpen = ref(false)
const dialogTitle = ref('')
const dialogSubtitle = ref('')
const dialogAction = ref('')
const pendingAction = ref<{
  type: 'delete' | 'deleteSelected'
  data?: any
  selectedIds?: number[]
} | null>(null)

// STATIC OPTIONS
const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']
const JENIS_JADWAL = ['Jadwal Semester', 'Jadwal Pengganti', 'Jadwal Ujian']
const SESI_PERKULIAHAN = ['Sesi Pagi', 'Sesi Malam']

// Helper function to safely extract array data
const extractArrayData = (response: any): any[] => {
  if (Array.isArray(response)) return response
  if (Array.isArray(response?.data)) return response.data
  return []
}

// Helper function to determine session (Pagi/Malam) based on start time
const getSesiFromTime = (waktuMulai?: string): string => {
  if (!waktuMulai) return ''

  const [jam] = waktuMulai.split(':')
  const hour = parseInt(jam ?? '0')

  return hour < 17 ? 'Sesi Pagi' : 'Sesi Malam'
}

// DYNAMIC FILTER OPTIONS - computed from API data
const filterOptions = computed(() => {
  if (jadwalData.value.length === 0) {
    return {
      periode: periodeList.value,
      hari: DAYS,
      programStudi: prodiList.value,
      mataKuliah: makulList.value,
      jenisJadwal: JENIS_JADWAL,
      laboran: laboranList.value,
      waktuPerkuliahan: SESI_PERKULIAHAN,
    }
  }

  return {
    periode: periodeList.value,
    hari: DAYS,
    programStudi: prodiList.value,
    mataKuliah: makulList.value,
    jenisJadwal: JENIS_JADWAL,
    laboran: laboranList.value,
    waktuPerkuliahan: SESI_PERKULIAHAN,
  }
})

// Fetch all filter data in parallel
const fetchFilterData = async () => {
  try {
    const response = await dashboardAPI.getFilter()

    const data = response.data

    laboranList.value = (data.laboran || []).map((item: any) => item.nama_laboran)
    prodiList.value = (data.prodi || []).map((item: any) => item.nama_prodi)
    makulList.value = (data.makul || []).map((item: any) => item.nama_makul)
    periodeList.value = (data.periode || []).map((item: any) => item.periode)
  } catch (error) {
    console.error('Failed to fetch filter data:', error)
  }
}

// FETCH JADWAL DATA FROM API
const fetchJadwalData = async () => {
  isLoading.value = true
  apiError.value = ''

  try {
    const response = await dashboardAPI.getJadwal()

    jadwalData.value = extractArrayData(response)

    if (jadwalData.value.length === 0) {
      console.warn('No jadwal data received from API')
    }
  } catch (error: any) {
    console.error('Error fetching jadwal:', error)

    if (error.response?.status === 401) {
      apiError.value = 'Sesi Anda telah berakhir. Silakan login kembali'
    } else if (error.response?.status === 500) {
      apiError.value = 'Terjadi kesalahan pada server'
    } else {
      apiError.value = error.message || 'Gagal memuat data jadwal'
    }
  } finally {
    isLoading.value = false
  }
}

// Format single jadwal item for display
const formatJadwalItem = (item: any) => {
  const dosenList = [item.nama_dosen_1, item.nama_dosen_2].filter(Boolean).join('\n') || '-'

  const ruangKelas =
    item.status === 'Online' || !item.kode_ruangan
      ? '-'
      : `${item.kode_ruangan}${item.kelas ? ' ' + item.kelas : ''}`

  const waktu =
    item.waktu_mulai && item.waktu_selesai ? `${item.waktu_mulai} - ${item.waktu_selesai}` : '-'

  // Determine session based on start time
  const sesi = getSesiFromTime(item.waktu_mulai ?? '')

  return {
    ...item,
    hari: item.hari_jadwal || '-',
    programStudi: item.nama_prodi || '-',
    mataKuliah: item.nama_makul || '-',
    dosen: dosenList,
    laboran: item.nama_laboran || '-',
    ruang: ruangKelas,
    waktu,
    sesi,
    jenisJadwal: item.jenis_jadwal || 'Jadwal Semester',
  }
}

// COMPUTED - FILTERED DATA + FORMATTING
const filteredData = computed(() => {
  const formatted = jadwalData.value.map(formatJadwalItem)
  const f = activeFilters.value

  return formatted.filter((item) => {
    if (f.periode && item.periode !== f.periode) return false
    if (f.hari && item.hari !== f.hari) return false
    if (f.programStudi && item.programStudi !== f.programStudi) return false
    if (f.mataKuliah && item.mataKuliah !== f.mataKuliah) return false
    if (f.jenisJadwal && item.jenisJadwal !== f.jenisJadwal) return false
    if (f.laboran && item.laboran !== f.laboran) return false
    if (f.waktuPerkuliahan && item.sesi !== f.waktuPerkuliahan) return false
    return true
  })
})

// HANDLERS
const handleFilterChange = (filters: IFilterDashboard) => {
  activeFilters.value = {
    ...filters,
    jenisJadwal: filters.jenisJadwal || 'Jadwal Semester',
  }
}

const handleReset = () => {
  activeFilters.value = {
    periode: '',
    hari: '',
    programStudi: '',
    mataKuliah: '',
    jenisJadwal: 'Jadwal Semester',
    laboran: '',
    waktuPerkuliahan: '',
  }
}

const handleEdit = (row: any & { id: number }) => {
  router.push({
    name: 'editJadwal',
    params: { id: row.id },
  })
}

const handleDelete = (row: any) => {
  // Show confirmation dialog instead of browser confirm
  dialogTitle.value = 'Hapus Data Jadwal'
  dialogSubtitle.value = `Anda yakin ingin menghapus jadwal ${row.mataKuliah}? Data yang sudah dihapus tidak dapat dipulihkan.`
  dialogAction.value = 'Hapus'
  pendingAction.value = {
    type: 'delete',
    data: row,
  }
  dialogOpen.value = true
}

const handleDeleteSelected = (selectedIds: number[]) => {
  if (!Array.isArray(selectedIds) || selectedIds.length === 0) {
    alert('Tidak ada data yang dipilih')
    return
  }

  // Show confirmation dialog for bulk delete
  dialogTitle.value = 'Hapus Data Jadwal'
  dialogSubtitle.value = `Anda yakin ingin menghapus ${selectedIds.length} jadwal yang dipilih? Data yang sudah dihapus tidak dapat dipulihkan.`
  dialogAction.value = 'Hapus'
  pendingAction.value = {
    type: 'deleteSelected',
    selectedIds: selectedIds,
  }
  dialogOpen.value = true
}

const handleDialogConfirm = async () => {
  dialogOpen.value = false

  if (!pendingAction.value) return

  if (pendingAction.value.type === 'delete') {
    // Execute single delete
    const row = pendingAction.value.data
    try {
      await dashboardAPI.deleteJadwal(row.id)
      success('Jadwal berhasil dihapus!')
      await Promise.all([fetchJadwalData(), fetchFilterData()])
    } catch (err: any) {
      console.error('Error deleting jadwal:', err)
      error('Gagal menghapus jadwal')
    }
  } else if (pendingAction.value.type === 'deleteSelected') {
    // Execute bulk delete
    const selectedIds = pendingAction.value.selectedIds || []
    isLoading.value = true
    apiError.value = ''

    try {
      await dashboardAPI.deleteSelectedJadwal(selectedIds)
      alert(`${selectedIds.length} jadwal berhasil dihapus`)
      await Promise.all([fetchJadwalData(), fetchFilterData()])
    } catch (error: any) {
      console.error('Bulk delete failed:', error)
      apiError.value =
        error.response?.data?.message || error.message || 'Gagal menghapus jadwal yang dipilih'
    } finally {
      isLoading.value = false
    }
  }

  pendingAction.value = null
}

const handleDialogCancel = () => {
  dialogOpen.value = false
  pendingAction.value = null
}

const handleDetail = (row: any) => {
  const id = row.id

  if (!id) {
    console.error('Invalid row: missing ID')
    return
  }

  router.push({
    name: 'DetailJadwal',
    params: { id },
  })
}

const handlePrint = () => {
  showPrintPage.value = true
}

// FETCH DATA ON MOUNT
onMounted(async () => {
  await Promise.all([fetchJadwalData(), fetchFilterData()])
})
</script>

<template>
  <div class="no-print">
    <div class="mb-6 mt-10 sm:mt-10 xl:mt-0">
      <p class="text-xl sm:text-3xl font-bold text-black">Dashboard Jadwal Perkuliahan</p>
      <p class="text-sm sm:text-base text-black">Monitor dan kelola perkuliahan kampus</p>
    </div>

    <!-- API Error Message -->
    <div
      v-if="apiError"
      class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm flex items-center justify-between"
    >
      <span>{{ apiError }}</span>
      <button
        @click="fetchJadwalData"
        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium"
      >
        Coba Lagi
      </button>
    </div>

    <!-- Loading State -->
    <div
      v-if="isLoading"
      class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm"
    >
      Memuat data jadwal...
    </div>

    <!-- Filter Component -->
    <FilterJadwal
      v-if="!isLoading"
      :options="filterOptions"
      @filter-change="handleFilterChange"
      @reset="handleReset"
      class="shadow-lg mb-6"
    />

    <!-- Table -->
    <!-- <DashboardTable
      v-if="!isLoading && !apiError"
      :columns="columns"
      :data="filteredData"
      :has-actions="true"
      @edit="handleEdit"
      @delete="handleDelete"
      @print="handlePrint"
      @detail="handleDetail"
    /> -->

    <CustomTable
      v-if="!isLoading && !apiError"
      :columns="columns"
      :data="filteredData"
      :has-actions="true"
      @edit="handleEdit"
      @delete="handleDelete"
      @deleteSelected="handleDeleteSelected"
      @print="handlePrint"
      @detail="handleDetail"
    />

    <ConfirmationDialog
      :isOpen="dialogOpen"
      :title="dialogTitle"
      :subtitle="dialogSubtitle"
      :actionText="dialogAction"
      @confirm="handleDialogConfirm"
      @cancel="handleDialogCancel"
    />
  </div>
  <!-- Print Page -->
  <CustomPrintPage
    :show="showPrintPage"
    title="Jadwal Perkuliahan"
    :columns="columns"
    :data="filteredData"
    :filters="activeFilters"
    @close="showPrintPage = false"
  />
</template>

<style scoped>
@media print {
  .no-print {
    display: none;
  }
}
</style>
