<template>
  <div>
    <!-- Header -->
    <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
      <h1 class="text-xl sm:text-3xl font-bold text-black">Beban Ruang Kelas</h1>
      <p class="text-sm sm:text-lg text-black">Monitor beban pemakaian kelas per minggu</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <CustomDropdown
        v-model="selectedKelasName"
        :options="kelasDropdownOptions"
        placeholder="Pilih kelas untuk melihat beban ruang kelas"
        label="Kelas"
        :searchable="true"
        class="max-w-full"
      />
    </div>

    <!-- Workload Summary Card -->
    <div v-if="selectedKelasID" :class="`${cardBgColor} rounded-xl shadow-lg p-8 mb-6 text-white`">
      <div class="flex items-center gap-6">
        <!-- Icon Cell -->
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

        <!-- Text Cell -->
        <div class="flex flex-col justify-center" :class="cardBgColor">
          <h2 class="text-lg font-medium mb-2">Total Beban Ruang Kelas {{ selectedKelasName }}</h2>
          <div class="text-2xl sm:text-3xl font-bold">
            {{ totalSKS }} SKS | {{ totalSesi }} Sesi / Minggu
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Table using DashboardTable Component -->
    <div v-if="selectedKelasID">
      <DashboardTable
        :columns="columns"
        :data="scheduleData"
        :has-actions="false"
        empty-message="Tidak ada jadwal ditemukan untuk kelas ini"
        @print="handlePrint"
      />
    </div>

    <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
      <img :src="Room_icon" class="w-20 mx-auto" />
      <p class="text-gray-500 mt-6">Pilih kelas untuk melihat beban pemakaian ruang kelas</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import CustomDropdown from '@/components/CustomDropdown.vue'
import DashboardTable from '@/components/DashboardTable.vue'
import Room_icon from '../assets/image/Room_icon.svg'
import { bebanRuangAPI } from '@/services/bebanRuangAPI'
import { useToast } from '@/composables/UseToast'

const { error } = useToast()

// Schedule interface
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

// Columns for table
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

const selectedKelasName = ref<string>('')
const selectedKelasID = ref<number | null>(null)
const kelasOptions = ref<IFilterSchedule[]>([])
const scheduleData = ref<ISchedule[]>([])
const loading = ref(false)

const kelasDropdownOptions = computed(() => kelasOptions.value.map((d) => d.nama))

// Load kelas list
onMounted(async () => {
  try {
    const response = await bebanRuangAPI.getRuangan()
    const rawData = response.data

    if (!rawData || !Array.isArray(rawData) || rawData.length === 0) {
      kelasOptions.value = []
      return
    }

    kelasOptions.value = rawData
      .filter((item: any) => item)
      .map(
        (item: any): IFilterSchedule => ({
          id: item.id ?? null,
          nama: item.nama_ruangan ?? '',
        }),
      )

    console.log('Kelas : ', kelasOptions.value)
  } catch (err) {
    kelasOptions.value = []
    error('Gagal memuat data kelas')
  }
})

// Watch for name changes and get the ID
watch(selectedKelasName, async (nama) => {
  if (!nama) {
    scheduleData.value = []
    selectedKelasID.value = null
    return
  }

  // Find the ID from the name
  const kelas = kelasOptions.value.find((d) => d.nama === nama)
  if (!kelas) {
    selectedKelasID.value = null
    return
  }

  selectedKelasID.value = kelas.id
  loading.value = true

  try {
    const response = await bebanRuangAPI.getRuanganById(kelas.id)
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
    scheduleData.value = []
    error('Gagal memuat jadwal kelas')
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

// Print handler
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
