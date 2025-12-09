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
        v-model="selectedDosenName"
        :options="dosenDropdownOptions"
        placeholder="Pilih dosen untuk melihat beban kerja dosen"
        label="Dosen"
        :searchable="true"
        class="max-w-full"
      />
    </div>

    <!-- Workload Summary Card -->
    <div v-if="selectedDosenId" :class="`${cardBgColor} rounded-xl shadow-lg p-8 mb-6 text-white`">
      <div class="flex items-center gap-6">
        <!-- Icon -->
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

        <!-- Text -->
        <div class="flex flex-col justify-center">
          <h2 class="text-lg font-medium mb-2">Total Beban Kerja {{ selectedDosenName }}</h2>
          <div class="text-2xl sm:text-3xl font-bold">
            {{ totalSKS }} SKS | {{ totalSesi }} Sesi / Minggu
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Table -->
    <div v-if="selectedDosenId">
      <DashboardTable
        :columns="columns"
        :data="scheduleData"
        :has-actions="false"
        empty-message="Tidak ada jadwal ditemukan untuk dosen ini"
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
import { bebanDosenAPI } from '@/services/bebanDosenAPI'
import { useToast } from '@/composables/UseToast'

const { error } = useToast()

interface ISchedule {
  id: number
  periode: string
  hari: string
  prodi: string
  mataKuliah: string
  ruang: string
  waktu: string
  sks: string
  sesi: string
}

interface IFilterSchedule {
  id: number
  nama: string
}

// Table columns
const columns = ref([
  { key: 'periode', label: 'Periode' },
  { key: 'hari', label: 'Hari' },
  { key: 'prodi', label: 'Program Studi' },
  { key: 'mataKuliah', label: 'Mata Kuliah' },
  { key: 'sks', label: 'SKS' },
  { key: 'sesi', label: 'Sesi' },
  { key: 'ruang', label: 'Ruang' },
  { key: 'waktu', label: 'Waktu' },
])

// State
const selectedDosenName = ref<string>('')
const selectedDosenId = ref<number | null>(null)
const dosenOptions = ref<IFilterSchedule[]>([])
const scheduleData = ref<ISchedule[]>([])
const loading = ref(false)

// Dropdown options - just the names
const dosenDropdownOptions = computed(() => dosenOptions.value.map((d) => d.nama))

// Load dosen list
onMounted(async () => {
  try {
    const response = await bebanDosenAPI.getDosen()
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      dosenOptions.value = []
      return
    }

    dosenOptions.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IFilterSchedule => ({
          id: item.id ?? null,
          nama: item.nama_dosen ?? '',
        }),
      )
  } catch (err) {
    dosenOptions.value = []
    error('Gagal memuat data dosen')
  }
})

// Watch for name changes and get the ID
watch(selectedDosenName, async (nama) => {
  if (!nama) {
    scheduleData.value = []
    selectedDosenId.value = null
    return
  }

  // Find the ID from the name
  const dosen = dosenOptions.value.find((d) => d.nama === nama)
  if (!dosen) {
    selectedDosenId.value = null
    return
  }

  selectedDosenId.value = dosen.id
  loading.value = true

  try {
    const response = await bebanDosenAPI.getDosenById(dosen.id)
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      scheduleData.value = []
      return
    }

    scheduleData.value = rawData.map((item: any) => ({
      id: item.id,
      periode: item.periode ?? '-',
      hari: item.hari_jadwal,
      prodi: item.nama_prodi ?? '-',
      mataKuliah: item.nama_makul ?? '-',
      ruang: item.nama_ruangan ?? '-',
      waktu: `${item.waktu_mulai} - ${item.waktu_selesai}`,
      sks: item.sks_makul ? `${item.sks_makul} SKS` : '-',
      sesi: item.sesi_makul ? `${item.sesi_makul} Sesi` : '-',
    }))
  } catch (err) {
    console.error('Failed to load schedule:', err)
    scheduleData.value = []
    error('Gagal memuat jadwal dosen')
  } finally {
    loading.value = false
  }
})

// Compute totals
const totalSKS = computed(() =>
  scheduleData.value.reduce((sum, s) => sum + (parseInt(s.sks.replace(' SKS', '')) || 0), 0),
)

const totalSesi = computed(() =>
  scheduleData.value.reduce((sum, s) => sum + (parseInt(s.sesi.replace(' Sesi', '')) || 0), 0),
)

// Colors
const cardBgColor = computed(() => {
  if (totalSesi.value >= 40) return 'bg-[#D00000]'
  if (totalSesi.value > 25) return 'bg-[#D08700]'
  return 'bg-green-emerald'
})

const iconColor = computed(() => {
  if (totalSesi.value >= 40) return 'text-red-600'
  return 'text-green-600'
})

// Print
const handlePrint = () => window.print()
</script>

<style scoped>
@media print {
  .bg-white.rounded-xl.shadow-lg.p-6.mb-8 {
    display: none;
  }
  .overflow-x-auto {
    overflow: visible;
  }
  table {
    page-break-inside: auto;
  }
  tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }
}
</style>
