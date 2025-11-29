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
        <div class="flex flex-col justify-center">
          <h2 class="text-lg font-medium mb-2">Total Beban Kerja {{ selectedDosen }}</h2>
          <div class="text-2xl sm:text-3xl font-bold">
            {{ totalSKS }} SKS | {{ totalSesi }} Sesi / Minggu
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Table using DashboardTable Component -->
    <div v-if="selectedDosen">
      <DashboardTable
        :columns="columns"
        :data="filteredSchedules"
        :has-actions="false"
        empty-message="Tidak ada jadwal ditemukan untuk dosen ini"
        @print="handlePrint"
      />
    </div>

    <!-- Empty State - No Dosen Selected -->
    <div v-else class="bg-white rounded-xl shadow-lg p-12 text-center">
      <img :src="Room_icon" class="w-20 mx-auto" />
      <p class="text-gray-500 mt-6">Pilih dosen untuk melihat beban kerja mengajar</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import CustomDropdown from '@/components/CustomDropdown.vue'
import DashboardTable from '@/components/DashboardTable.vue'
import Room_icon from '../assets/image/Room_icon.svg'
import BebanData from '../dummy data/bebanData.json'

// Define schedule interface
interface I {
  hari: string
  kodeMakul: string
  programStudi: string
  mataKuliah: string
  dosen: string
  ruang: string
  waktu: string
  sks: string
  sesi: string
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

// Dosen options
const dosenOptions = [...new Set(BebanData.map((item) => item.dosen))]

// Sample schedule data
const bebanData = ref(BebanData)

// Filtered schedules by selected dosen
const filteredSchedules = computed(() =>
  bebanData.value.filter((s) => s.dosen === selectedDosen.value),
)

// Total SKS
const totalSKS = computed(() =>
  filteredSchedules.value.reduce((sum, s) => sum + (parseInt(s.sks.replace(' SKS', '')) || 0), 0),
)

// Total sessions per week
const totalSesi = computed(() =>
  filteredSchedules.value.reduce((sum, s) => sum + (parseInt(s.sesi.replace(' Sesi', '')) || 0), 0),
)

// Dynamic card background color
const cardBgColor = computed(() => {
  if (totalSesi.value === 40) return 'bg-[#D00000]' // Bright red
  if (totalSesi.value > 25) return 'bg-[#D08700]' // Warm yellow
  return 'bg-green-emerald' // Default green
})

// Optional: icon color based on background
const iconColor = computed(() => {
  if (totalSesi.value >= 25) return 'text-white'
  return 'text-green-600'
})

// Print handler
const handlePrint = () => window.print()
</script>

<style scoped>
@media print {
  /* Hide dropdown selector when printing */
  .bg-white.rounded-xl.shadow-lg.p-6.mb-8 {
    display: none;
  }

  /* Optimize table for printing */
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
