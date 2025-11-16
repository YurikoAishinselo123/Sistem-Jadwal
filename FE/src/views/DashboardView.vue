<template>
  <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
    <p class="text-xl sm:text-3xl font-bold text-black">Dashboard Jadwal Perkuliahan</p>
    <p class="text-sm sm:text-lg font-base text-black">Monitor dan kelola perkuliahan kampus</p>
  </div>

  <FilterJadwal
    :options="filterOptions"
    @filter-change="handleFilterChange"
    @reset="handleReset"
    class="shadow-lg mb-6"
  />

  <!-- Fixed table wrapper -->
  <div class="table-wrapper">
    <DashboardTable
      :columns="columns"
      :data="filteredData"
      :has-actions="true"
      @edit="handleEdit"
      @delete="handleDelete"
      @print="handlePrint"
    />
  </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import FilterJadwal from '@/components/FilterJadwal.vue'
import type { IFilterDashboard } from '@/interfaces/IFilterDashboard'
import PrintIcon from '../assets/image/Print_icon.svg'
import DashboardTable from '../components/DashboardTable.vue'

// Define columns for the table
const columns = ref([
  { key: 'hari', label: 'Hari' },
  { key: 'kodeMakul', label: 'Kode Makul' },
  { key: 'programStudi', label: 'Program Studi' },
  { key: 'mataKuliah', label: 'Mata Kuliah' },
  { key: 'dosen', label: 'Dosen' },
  { key: 'laboran', label: 'Laboran' },
  { key: 'ruang', label: 'Ruang' },
  { key: 'waktu', label: 'Waktu' },
])

// Sample jadwal data
const jadwalData = ref([
  {
    hari: 'Sabtu',
    kodeMakul: 'FK1KP',
    programStudi: 'Mesin A',
    mataKuliah: 'Pengantar Teknik Perkapalan',
    dosen1: 'Adhe Aryswan, S.Pd., M.Si.',
    dosen2: 'Haposan Vincentius, S.T., M.Sc.',
    laboran: 'Adshe Aryswan, S.Pd., M.Si.',
    ruang: '108 B',
    waktu: '07:00 - 09:30',
    periodeTahunAjaran: 'Gasal 2025',
  },
  {
    hari: 'Senin',
    kodeMakul: 'TI101',
    programStudi: 'Teknik Informatika',
    mataKuliah: 'Algoritma',
    dosen1: 'Haposan Manalu, S.T., M.Sc.',
    dosen2: 'Vincentius, S.T., M.Sc.',
    laboran: 'Adhe Aryswan, S.Pd., M.Si.',
    ruang: '201 A',
    waktu: '09:30 - 12:00',
    periodeTahunAjaran: 'Gasal 2025',
  },
  {
    hari: 'Selasa',
    kodeMakul: 'TI102',
    programStudi: 'Teknik Informatika',
    mataKuliah: 'Pemrograman',
    dosen1: 'Adhe Aryswan, S.Pd., M.Si.',
    dosen2: 'Haposan Vincentius, S.T., M.Sc.',
    laboran: 'Haposan Manalu, S.T., M.Sc.',
    ruang: '202 B',
    waktu: '13:00 - 15:30',
    periodeTahunAjaran: 'Gasal 2025',
  },
  {
    hari: 'Rabu',
    kodeMakul: 'FK2KP',
    programStudi: 'Mesin B',
    mataKuliah: 'Pengantar Teknik Perkapalan',
    dosen1: 'Adhe Aryswan, S.Pd., M.Si.',
    dosen2: 'Haposan Vincentius, S.T., M.Sc.',
    laboran: 'Adhe Aryswan, S.Pd., M.Si.',
    ruang: '108 C',
    waktu: '07:00 - 09:30',
    periodeTahunAjaran: 'Genap 2025',
  },
  {
    hari: 'Kamis',
    kodeMakul: 'TI103',
    programStudi: 'Teknik Informatika',
    mataKuliah: 'Algoritma',
    dosen1: 'Adhe Aryswan, S.Pd., M.Si.',
    dosen2: 'Haposan Vincentius, S.T., M.Sc.',
    laboran: 'Haposan Manalu, S.T., M.Sc.',
    ruang: '301 A',
    waktu: '09:30 - 12:00',
    periodeTahunAjaran: 'Gasal 2025',
  },
])

// Filter state
const activeFilters = ref<IFilterDashboard>({
  periodeTahunAjaran: '',
  hari: '',
  programStudi: '',
  mataKuliah: '',
  dosen: '',
  laboran: '',
  waktuPerkuliahan: '',
})

// Filter options
const filterOptions = {
  periodeTahunAjaran: ['Gasal 2025', 'Genap 2025'],
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
  programStudi: ['Teknik Informatika', 'Mesin A', 'Mesin B'],
  mataKuliah: ['Algoritma', 'Pemrograman', 'Pengantar Teknik Perkapalan'],
  dosen: ['Adhe Aryswan, S.Pd., M.Si.', 'Haposan Vincentius Manalu, S.T., M.Sc.'],
  laboran: ['Adhe Aryswan, S.Pd., M.Si.', 'Haposan Vincentius Manalu, S.T., M.Sc.'],
  waktuPerkuliahan: ['07:00 - 09:30', '09:30 - 12:00', '13:00 - 15:30'],
}

// Computed filtered data
const filteredData = computed(() => {
  let result = jadwalData.value

  const f = activeFilters.value
  if (f.periodeTahunAjaran)
    result = result.filter((i) => i.periodeTahunAjaran === f.periodeTahunAjaran)
  if (f.hari) result = result.filter((i) => i.hari === f.hari)
  if (f.programStudi) result = result.filter((i) => i.programStudi === f.programStudi)
  if (f.mataKuliah) result = result.filter((i) => i.mataKuliah === f.mataKuliah)
  if (f.dosen) result = result.filter((i) => i.dosen1 === f.dosen || i.dosen2 === f.dosen)
  if (f.laboran) result = result.filter((i) => i.laboran === f.laboran)
  if (f.waktuPerkuliahan) result = result.filter((i) => i.waktu === f.waktuPerkuliahan)

  return result
})

// Event handlers
const handleFilterChange = (filters: IFilterDashboard) => {
  activeFilters.value = { ...filters }
}

const handleReset = () => {
  console.log('Filters reset')
  activeFilters.value = {
    periodeTahunAjaran: '',
    hari: '',
    programStudi: '',
    mataKuliah: '',
    dosen: '',
    laboran: '',
    waktuPerkuliahan: '',
  }
}

const handleEdit = (row: any) => {
  console.log('Edit:', row)
  alert(`Edit jadwal: ${row.mataKuliah}`)
}

const handleDelete = (row: any) => {
  console.log('Delete:', row)
  if (confirm(`Hapus jadwal ${row.mataKuliah}?`)) {
    const index = jadwalData.value.findIndex((item) => item === row)
    if (index > -1) {
      jadwalData.value.splice(index, 1)
      alert('Jadwal berhasil dihapus!')
    }
  }
}

const handlePrint = () => {
  console.log('Print table')
  window.print()
}
</script>

<style scoped>
/* Critical fix for zoom issue */
.dashboard-container {
  width: 100%;
  max-width: 100%;
  overflow-x: hidden; /* Prevent horizontal scroll on container */
}

.table-wrapper {
  width: 100%;
  max-width: 100%;
  overflow-x: auto;
  overflow-y: visible;
  -webkit-overflow-scrolling: touch;
}

/* Ensure child components don't overflow */
.table-wrapper :deep(*) {
  max-width: 100%;
}

/* Force table to be responsive */
.table-wrapper :deep(table) {
  width: 100% !important;
  max-width: 100% !important;
  table-layout: auto;
}

.table-wrapper :deep(th),
.table-wrapper :deep(td) {
  word-wrap: break-word;
  overflow-wrap: break-word;
  max-width: 200px; /* Adjust based on your needs */
}

@media print {
  .no-print {
    display: none;
  }

  .table-wrapper {
    overflow: visible;
  }
}
</style>
