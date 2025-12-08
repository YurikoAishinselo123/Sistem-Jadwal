<template>
  <div>
    <!-- Header -->
    <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
      <h1 class="text-xl sm:text-3xl font-bold text-black">Beban Kerja Dosen</h1>
      <p class="text-sm sm:text-lg text-black">Monitor beban mengajar dosen per minggu</p>
    </div>

    <!-- Dosen Selector -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <CustomDropdown
        v-model="selectedDosen"
        :options="dosenOptions"
        placeholder="Pilih dosen untuk melihat beban kerja dosen"
        label="Dosen"
        :searchable="true"
        class="max-w-full"
      />
    </div>

    <!-- Workload Summary Card -->
    <div v-if="selectedDosen" :class="`${cardBgColor} rounded-xl shadow-lg p-8 mb-6 text-white`">
      <div class="flex items-center gap-6">
        <div class="bg-white rounded-full p-4 flex items-center justify-center w-16 h-16">
          <svg
            class="w-8 h-8"
            :class="iconColor"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
        </div>

        <div>
          <h2 class="text-lg font-medium mb-2">Total Beban Kerja {{ selectedDosen }}</h2>
          <div class="text-2xl sm:text-3xl font-bold">
            {{ totalSKS }} SKS | {{ totalSesi }} Sesi / Minggu
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Table -->
    <div v-if="selectedDosen">
      <DashboardTable
        :columns="columns"
        :data="scheduleList"
        :has-actions="false"
        empty-message="Tidak ada jadwal"
        @print="handlePrint"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
      <img :src="Room_icon" class="w-20 mx-auto" />
      <p class="text-gray-500 mt-6">Pilih dosen untuk melihat beban kerja mengajar</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import CustomDropdown from '@/components/CustomDropdown.vue'
import DashboardTable from '@/components/DashboardTable.vue'
import Room_icon from '../assets/image/Room_icon.svg'
import { bebandosenAPI } from '@/services/bebandosenAPI'
import apiClient from '@/services/apiClient'

// Define schedule interface
interface ISchedule {
  hari: string
  kodeMakul: string
  programStudi: string
  mataKuliah: string
  sks: number
  sesi: number
  ruang: string
  waktu: string
}

// Table columns
const columns = ref([
  { key: 'hari', label: 'Hari' },
  { key: 'kodeMakul', label: 'Kode Makul' },
  { key: 'programStudi', label: 'Program Studi' },
  { key: 'mataKuliah', label: 'Mata Kuliah' },
  { key: 'sks', label: 'SKS' },
  { key: 'sesi', label: 'Sesi' },
  { key: 'ruang', label: 'Ruang' },
  { key: 'waktu', label: 'Waktu' },
])

// Selected dosen
const selectedDosen = ref('')
const dosenRaw = ref<any[]>([])
const scheduleList = ref<ISchedule[]>([])

// lookup data api
const makulMap = ref<Record<number, string>>({})
const prodiMap = ref<Record<number, string>>({})
const ruanganMap = ref<Record<number, string>>({})

const fetchMasterData = async () => {
  const [makul, prodi, ruangan] = await Promise.all([
    apiClient.get('/makul'),
    apiClient.get('/prodi'),
    apiClient.get('/ruangan'),
  ])

  makul.data.forEach((m: any) => (makulMap.value[m.id] = m.nama_makul))
  prodi.data.forEach((p: any) => (prodiMap.value[p.id] = p.nama_prodi))
  ruangan.data.forEach((r: any) => (ruanganMap.value[r.id] = r.nama_ruangan))
}

// fetch dosen
const fetchDosen = async () => {
  const res = await bebandosenAPI.getDosen()
  dosenRaw.value = res.data
}

const dosenOptions = computed(() => dosenRaw.value.map((d) => d.nama_dosen))

// fetch jadwal dosen
const fetchJadwalByDosen = async (namaDosen: string) => {
  const dosen = dosenRaw.value.find((d) => d.nama_dosen === namaDosen)
  if (!dosen) return

  const res = await bebandosenAPI.getDosenbyid(dosen.id)

  scheduleList.value = res.data.map((item: any) => ({
    hari: item.hari_jadwal,
    kodeMakul: makulMap.value[item.makul_id] ?? '-',
    programStudi: prodiMap.value[item.prodi_id] ?? '-',
    mataKuliah: makulMap.value[item.makul_id] ?? '-',
    sks: Number(item.sks ?? 1),
    sesi: Number(item.sesi ?? 1),
    ruang: ruanganMap.value[item.ruangan_id] ?? '-',
    waktu: `${item.waktu_mulai} - ${item.waktu_selesai}`,
  }))
}

// watch dropdown
watch(selectedDosen, (val) => {
  if (val) fetchJadwalByDosen(val)
})

// total sks
const totalSKS = computed(() => scheduleList.value.reduce((sum, s) => sum + s.sks, 0))

// Total sessions per week
const totalSesi = computed(() => scheduleList.value.reduce((sum, s) => sum + s.sesi, 0))

// Dynamic card background color
const cardBgColor = computed(() => {
  if (totalSesi.value >= 40) return 'bg-red-600'
  if (totalSesi.value >= 25) return 'bg-yellow-500'
  return 'bg-green-600'
})

// Optional: icon color based on background
const iconColor = computed(() => {
  if (totalSesi.value >= 25) return 'text-white'
  return 'text-green-600'
})

// Print handler
const handlePrint = () => window.print()

onMounted(async () => {
  await fetchMasterData()
  await fetchDosen()
})
</script>

<style scoped>
@media print {
  .bg-white.rounded-xl.shadow-lg.p-6.mb-8 {
    display: none;
  }
}
</style>
