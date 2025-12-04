<template>
  <div class="bg-white shadow-md rounded-2xl p-6 w-full relative z-40">
    <h2 class="text-lg text-black font-semibold mb-4">Filter Jadwal</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <!-- Periode Tahun Ajaran -->
      <CustomDropdown
        v-model="filters.periodeTahunAjaran"
        :options="options.periodeTahunAjaran"
        placeholder="Periode Tahun Ajaran"
        :searchable="false"
      />

      <!-- Hari -->
      <CustomDropdown
        v-model="filters.hari"
        :options="options.hari"
        placeholder="Hari"
        :searchable="true"
      />

      <!-- Program Studi -->
      <CustomDropdown
        v-model="filters.programStudi"
        :options="options.programStudi"
        placeholder="Program Studi"
        :searchable="true"
      />

      <!-- Mata Kuliah -->
      <CustomDropdown
        v-model="filters.mataKuliah"
        :options="options.mataKuliah"
        placeholder="Mata Kuliah"
        :searchable="true"
      />

      <!-- Dosen -->
      <CustomDropdown
        v-model="filters.jenisJadwal"
        :options="options.jenisJadwal"
        placeholder="Jenis Jadwal"
        :clearable="false"
        :searchable="true"
      />

      <!-- Laboran -->
      <CustomDropdown
        v-model="filters.laboran"
        :options="options.laboran"
        placeholder="Laboran"
        :searchable="true"
      />

      <!-- Waktu Perkuliahan -->
      <CustomDropdown
        v-model="filters.waktuPerkuliahan"
        :options="options.waktuPerkuliahan"
        placeholder="Waktu Perkuliahan"
        :searchable="true"
      />

      <!-- Reset Button -->
      <button
        @click="resetFilter"
        class="bg-active-blue hover:bg-hover-button text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm transition-all duration-200 focus:outline-none"
      >
        Reset Filter
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import CustomDropdown from './CustomDropdown.vue'

// Props declaration
const props = defineProps<{
  options: {
    periodeTahunAjaran: string[]
    hari: string[]
    programStudi: string[]
    mataKuliah: string[]
    jenisJadwal: string[]
    laboran: string[]
    waktuPerkuliahan: string[]
  }
}>()

// Define filter type
interface Filters {
  periodeTahunAjaran: string
  hari: string
  programStudi: string
  mataKuliah: string
  jenisJadwal: string
  laboran: string
  waktuPerkuliahan: string
}

// Emits declaration
const emit = defineEmits<{
  (e: 'filter-change', filters: Filters): void
  (e: 'reset'): void
}>()

// Reactive filter model
const filters = reactive<Filters>({
  periodeTahunAjaran: '',
  hari: '',
  programStudi: '',
  mataKuliah: '',
  jenisJadwal: 'Jadwal Semester',
  laboran: '',
  waktuPerkuliahan: '',
})

// RESET
const resetFilter = () => {
  filters.periodeTahunAjaran = ''
  filters.hari = ''
  filters.programStudi = ''
  filters.mataKuliah = ''
  filters.jenisJadwal = 'Jadwal Semester'
  filters.laboran = ''
  filters.waktuPerkuliahan = ''

  emit('reset')
}

// Watch for changes and emit
watch(
  filters,
  (newFilters) => {
    emit('filter-change', newFilters)
  },
  { deep: true },
)
</script>
