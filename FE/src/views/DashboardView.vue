<script lang="ts" setup>
import { ref, computed } from 'vue'
import FilterJadwal from '@/components/FilterJadwal.vue'
import DashboardTable from '@/components/DashboardTable.vue'
import type { IFilterDashboard } from '@/interfaces/IFilterDashboard'
import JadwalData from '@/dummy data/jadwalData.json'

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

// DATA DUMMY
const jadwalData = ref(JadwalData)

// FILTER STATE
const activeFilters = ref<IFilterDashboard>({
  periodeTahunAjaran: '',
  hari: '',
  programStudi: '',
  mataKuliah: '',
  jenisJadwal: 'Jadwal Semester',
  laboran: '',
  waktuPerkuliahan: '',
})

// FILTER OPTIONS (adjusted)
const filterOptions = {
  periodeTahunAjaran: [...new Set(JadwalData.map((i) => `${i.namaPeriode} ${i.tahunPeriode}`))],
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
  programStudi: ['Teknik Informatika', 'Mesin A', 'Mesin B'],
  mataKuliah: [...new Set(JadwalData.map((i) => i.mataKuliah))],
  jenisJadwal: ['Jadwal Semester', 'Jadwal Pengganti', 'Jadwal Ujian'],
  laboran: [...new Set(JadwalData.map((i) => i.laboran))],
  waktuPerkuliahan: [...new Set(JadwalData.map((i) => `${i.waktuMulai} - ${i.waktuSelesai}`))],
}

// COMPUTED - FILTERED DATA + FORMATTING
const filteredData = computed(() => {
  let result = jadwalData.value.map((item) => ({
    ...item,

    periodeTahunAjaran: `${item.namaPeriode} ${item.tahunPeriode}`,

    dosen: `${item.dosen1}, ${item.dosen2}`,

    ruang: item.status === 'online' || !item.ruang ? '-' : `${item.ruang} ${item.kelas}`,

    waktu: `${item.waktuMulai} - ${item.waktuSelesai}`,
  }))

  const f = activeFilters.value

  if (f.periodeTahunAjaran)
    result = result.filter((i) => i.periodeTahunAjaran === f.periodeTahunAjaran)
  if (f.hari) result = result.filter((i) => i.hari === f.hari)
  if (f.programStudi) result = result.filter((i) => i.programStudi === f.programStudi)
  if (f.mataKuliah) result = result.filter((i) => i.mataKuliah === f.mataKuliah)
  if (f.jenisJadwal) result = result.filter((i) => i.jenisJadwal === f.jenisJadwal)
  if (f.laboran) result = result.filter((i) => i.laboran === f.laboran)
  if (f.waktuPerkuliahan) result = result.filter((i) => i.waktu === f.waktuPerkuliahan)

  return result
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
    periodeTahunAjaran: '',
    hari: '',
    programStudi: '',
    mataKuliah: '',
    jenisJadwal: 'Jadwal Semester',
    laboran: '',
    waktuPerkuliahan: '',
  }
}

const handleEdit = (row: any) => {
  alert(`Edit jadwal: ${row.mataKuliah}`)
}

const handleDelete = (row: any) => {
  if (confirm(`Hapus jadwal ${row.mataKuliah}?`)) {
    const index = jadwalData.value.findIndex((item) => item === row)
    if (index >= 0) jadwalData.value.splice(index, 1)
    alert('Jadwal berhasil dihapus!')
  }
}

const handlePrint = () => window.print()
</script>

<template>
  <p class="text-4xl font-bold text-black">Dashboard Jadwal Perkuliahan</p>
  <p class="text-xl font-base text-black mb-8">Monitor dan kelola perkuliahan kampus</p>
  <FilterJadwal
    :options="filterOptions"
    @filter-change="handleFilterChange"
    @reset="handleReset"
    class="shadow-lg mb-6"
  />

  <!-- Table with merged header -->
  <DashboardTable
    :columns="columns"
    :data="filteredData"
    :has-actions="true"
    @edit="handleEdit"
    @delete="handleDelete"
    @print="handlePrint"
  >
  </DashboardTable>
</template>

<style scoped>
/* Add any custom styles here */
@media print {
  .no-print {
    display: none;
  }
}
</style>
