<script setup lang="ts">
import { reactive, computed, watch, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '@/components/CustomDropdown.vue'

import DataWaktuPerkuliahan from '@/dummy data/dataWaktuPerkuliahan.json'
import { dashboardAPI } from '@/services/dashboardAPI'
import { useToast } from '@/composables/UseToast'

const router = useRouter()
const { success } = useToast()

// API data storage
const apiData = ref({
  periode: [],
  prodi: [],
  makul: [],
  dosen: [],
  laboran: [],
  ruangan: [],
  waktu: [],
})

const isLoading = ref(true)

// Time slots definition
const timeSlots = { ...DataWaktuPerkuliahan }
const allTimeSlots = [...timeSlots.pagi, ...timeSlots.malam]
const isOnline = computed(() => formData.status === 'Online')

// Form data - stores display values
const formData = reactive({
  periodeTahunAjaran: '',
  hari: '',
  jenisJadwal: '',
  waktuMulai: '',
  waktuSelesai: '',
  mataKuliah: '',
  status: '',
  programStudi: '',
  kelas: '',
  dosen1: '',
  dosen2: '',
  laboran: '',
  ruangKelas: '',
})

// Store selected IDs for API payload
const selectedIds = reactive({
  periodeId: null,
  prodiId: null,
  makulId: null,
  dosen1Id: null,
  dosen2Id: null,
  laboranId: null,
  ruanganId: null,
})

// Error state
const showError = reactive({
  periodeTahunAjaran: false,
  hari: false,
  jenisJadwal: false,
  waktuMulai: false,
  waktuSelesai: false,
  mataKuliah: false,
  status: false,
  programStudi: false,
  kelas: false,
  dosen1: false,
  dosen2: false,
  laboran: false,
  ruangKelas: false,
})

// Fetch form data from API
const fetchFormData = async () => {
  try {
    isLoading.value = true
    const response = await dashboardAPI.getJadwalFormData()
    apiData.value = response.data
  } catch (error) {
    console.error('Error fetching form data:', error)
    alert('Gagal memuat data form. Silakan refresh halaman.')
  } finally {
    isLoading.value = false
  }
}

// Fetch data on component mount
onMounted(() => {
  fetchFormData()
})

// Determine session based on start time
const selectedSession = computed(() => {
  if (!formData.waktuMulai) return null
  if (timeSlots.pagi.includes(formData.waktuMulai)) return 'pagi'
  if (timeSlots.malam.includes(formData.waktuMulai)) return 'malam'
  return null
})

// Available start times
const availableStartTimes = computed(() => allTimeSlots)

// Available end times based on start time
const availableEndTimes = computed(() => {
  if (!formData.waktuMulai) return allTimeSlots
  const session = selectedSession.value
  if (!session) return []
  const sessionTimes = timeSlots[session]
  const startIndex = sessionTimes.indexOf(formData.waktuMulai)
  return sessionTimes.slice(startIndex + 1)
})

// Watch start time changes
watch(
  () => formData.waktuMulai,
  () => {
    if (formData.waktuSelesai && !availableEndTimes.value.includes(formData.waktuSelesai)) {
      formData.waktuSelesai = ''
    }
  },
)

// Filter dosen options for Dosen 1 (exclude dosen2)
const dosen1Options = computed(() => {
  return apiData.value.dosen
    .map((item: any) => item.nama_dosen)
    .filter((name: string) => name !== formData.dosen2)
})

// Filter dosen options for Dosen 2 (exclude dosen1)
const dosen2Options = computed(() => {
  return apiData.value.dosen
    .map((item: any) => item.nama_dosen)
    .filter((name: string) => name !== formData.dosen1)
})

// Dropdown options from API
const dropdownOptions = computed(() => ({
  periodeTahunAjaran: apiData.value.periode.map((item: any) => item.periode),
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
  jenisJadwal: ['Jadwal Semester', 'Jadwal Ujian', 'Jadwal Pengganti'],
  mataKuliah: apiData.value.makul.map((item: any) => item.nama_makul),
  status: ['Online', 'Offline'],
  programStudi: apiData.value.prodi.map((item: any) => item.nama_prodi),
  kelas: ['A', 'B', 'C', 'D', 'E'],
  dosen1: dosen1Options.value,
  dosen2: dosen2Options.value,
  laboran: apiData.value.laboran.map((item: any) => item.nama_laboran),
  ruangKelas: apiData.value.ruangan.map((item: any) => item.kode_ruangan),
}))

// Helper function to get ID from selected value
const getIdFromValue = (array: any[], value: string, nameKey: string) => {
  const item = array.find((item) => item[nameKey] === value)
  return item ? item.id : null
}

// Convert status string to number for API
const getStatusValue = (status: string): number => {
  return status === 'Online' ? 1 : 2
}

// Watch for changes and update IDs
watch(
  () => formData.periodeTahunAjaran,
  (newValue) => {
    selectedIds.periodeId = getIdFromValue(apiData.value.periode, newValue, 'periode')
  },
)

watch(
  () => formData.programStudi,
  (newValue) => {
    selectedIds.prodiId = getIdFromValue(apiData.value.prodi, newValue, 'nama_prodi')
  },
)

watch(
  () => formData.mataKuliah,
  (newValue) => {
    selectedIds.makulId = getIdFromValue(apiData.value.makul, newValue, 'nama_makul')
  },
)

watch(
  () => formData.dosen1,
  (newValue) => {
    selectedIds.dosen1Id = getIdFromValue(apiData.value.dosen, newValue, 'nama_dosen')
  },
)

watch(
  () => formData.dosen2,
  (newValue) => {
    selectedIds.dosen2Id = getIdFromValue(apiData.value.dosen, newValue, 'nama_dosen')
  },
)

watch(
  () => formData.laboran,
  (newValue) => {
    selectedIds.laboranId = getIdFromValue(apiData.value.laboran, newValue, 'nama_laboran')
  },
)

watch(
  () => formData.ruangKelas,
  (newValue) => {
    selectedIds.ruanganId = getIdFromValue(apiData.value.ruangan, newValue, 'kode_ruangan')
  },
)

watch(
  () => formData.status,
  (newValue) => {
    if (newValue === 'Online') {
      formData.ruangKelas = ''
      selectedIds.ruanganId = null
      showError.ruangKelas = false
    }
  },
)

// Form submission
const handleSubmit = async () => {
  // Reset errors
  Object.keys(showError).forEach((key) => (showError[key as keyof typeof showError] = false))

  // Validate required fields
  const requiredFields = Object.keys(showError).filter((key) => {
    if (key === 'ruangKelas' && isOnline.value) return false
    return true
  })
  const missingFields = requiredFields.filter((key) => !formData[key as keyof typeof formData])

  if (missingFields.length > 0) {
    missingFields.forEach((key) => (showError[key as keyof typeof showError] = true))
    alert('Harap lengkapi semua field yang wajib diisi!')
    return
  }

  // Validate waktuSelesai
  if (!availableEndTimes.value.includes(formData.waktuSelesai)) {
    showError.waktuSelesai = true
    alert(
      'Waktu selesai tidak valid! Pastikan lebih besar dari waktu mulai dan dalam sesi yang sama.',
    )
    return
  }

  // Prepare payload with IDs
  const payload = {
    periode_tahun_id: selectedIds.periodeId,
    hari_jadwal: formData.hari,
    jenis_jadwal: formData.jenisJadwal,
    waktu_mulai: formData.waktuMulai,
    waktu_selesai: formData.waktuSelesai,
    makul_id: selectedIds.makulId,
    status: getStatusValue(formData.status),
    prodi_id: selectedIds.prodiId,
    kelas: formData.kelas,
    dosen_1: selectedIds.dosen1Id,
    dosen_2: selectedIds.dosen2Id,
    laboran_id: selectedIds.laboranId,
    ...(isOnline.value ? {} : { ruangan_id: selectedIds.ruanganId }),
  }

  try {
    const response = await dashboardAPI.createJadwal(payload)
    console.log('Jadwal created successfully:', response.data)
    success('Jadwal berhasil ditambahkan!')
    router.push({ name: 'dashboard' })
  } catch (error) {
    console.log('Payload : ', payload)
    console.error('Error creating jadwal:', error)
    alert('Gagal menambahkan jadwal. Silakan coba lagi.')
  }
}

// Cancel
const handleCancel = () => {
  if (confirm('Apakah Anda yakin ingin membatalkan? Data yang diisi akan hilang.')) {
    router.push({ name: 'dashboard' })
  }
}

// Clear error on field change
Object.keys(formData).forEach((key) => {
  watch(
    () => formData[key as keyof typeof formData],
    (newValue) => {
      if (newValue) showError[key as keyof typeof showError] = false
    },
  )
})
</script>

<template>
  <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
    <h1 class="text-xl sm:text-3xl font-bold text-black">Tambah Jadwal</h1>
    <p class="text-sm sm:text-lg text-black">
      Isi form di bawah untuk menambahkan jadwal perkuliahan
    </p>
  </div>

  <!-- Loading state -->
  <div v-if="isLoading" class="bg-white rounded-xl shadow-lg p-8">
    <div class="flex items-center justify-center py-12">
      <div class="text-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-active-blue mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Memuat data...</p>
      </div>
    </div>
  </div>

  <!-- Form -->
  <div v-else class="bg-white rounded-xl shadow-lg p-8">
    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Periode Tahun Ajaran -->
        <CustomDropdown
          v-model="formData.periodeTahunAjaran"
          :options="dropdownOptions.periodeTahunAjaran"
          label="Periode Tahun Ajaran"
          placeholder="Pilih periode tahun ajaran"
          :required="true"
          :showError="showError.periodeTahunAjaran"
        />

        <!-- Hari -->
        <CustomDropdown
          v-model="formData.hari"
          :options="dropdownOptions.hari"
          label="Hari"
          placeholder="Pilih hari"
          :required="true"
          :showError="showError.hari"
        />

        <!-- Jenis Jadwal -->
        <CustomDropdown
          v-model="formData.jenisJadwal"
          :options="dropdownOptions.jenisJadwal"
          placeholder="Pilih jenis jadwal"
          label="Jenis Jadwal"
          :searchable="false"
          :clearable="false"
          :required="true"
          :showError="showError.jenisJadwal"
        />

        <!-- Time Selection Row - Waktu Mulai and Waktu Selesai -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Waktu Mulai Perkuliahan -->
          <CustomDropdown
            v-model="formData.waktuMulai"
            :options="availableStartTimes"
            placeholder="Pilih waktu mulai"
            label="Waktu Mulai Perkuliahan"
            :searchable="true"
            :clearable="false"
            :required="true"
            :showError="showError.waktuMulai"
          />
          <!-- Waktu Selesai Perkuliahan -->
          <CustomDropdown
            v-model="formData.waktuSelesai"
            :options="availableEndTimes"
            placeholder="Pilih waktu selesai"
            label="Waktu Selesai Perkuliahan"
            :searchable="true"
            :clearable="false"
            :required="true"
            :showError="showError.waktuSelesai"
          />
        </div>

        <!-- Mata Kuliah -->
        <CustomDropdown
          v-model="formData.mataKuliah"
          :options="dropdownOptions.mataKuliah"
          placeholder="Pilih mata kuliah"
          label="Mata Kuliah"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.mataKuliah"
        />

        <!-- Status -->
        <CustomDropdown
          v-model="formData.status"
          :options="dropdownOptions.status"
          placeholder="Pilih Status"
          label="Status"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.status"
        />

        <!-- Program Studi -->
        <CustomDropdown
          v-model="formData.programStudi"
          :options="dropdownOptions.programStudi"
          placeholder="Pilih program studi"
          label="Program Studi"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.programStudi"
        />

        <!-- Kelas -->
        <CustomDropdown
          v-model="formData.kelas"
          :options="dropdownOptions.kelas"
          placeholder="Pilih kelas"
          label="Kelas"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.kelas"
        />

        <!-- Dosen 1 -->
        <CustomDropdown
          v-model="formData.dosen1"
          :options="dropdownOptions.dosen1"
          placeholder="Pilih dosen 1"
          label="Dosen 1"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.dosen1"
        />

        <!-- Dosen 2 -->
        <CustomDropdown
          v-model="formData.dosen2"
          :options="dropdownOptions.dosen2"
          placeholder="Pilih dosen 2"
          label="Dosen 2"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.dosen2"
        />

        <!-- Laboran -->
        <CustomDropdown
          v-model="formData.laboran"
          :options="dropdownOptions.laboran"
          placeholder="Pilih laboran"
          label="Laboran"
          :searchable="true"
          :clearable="false"
          :required="true"
          :showError="showError.laboran"
        />

        <!-- Ruang Kelas -->
        <CustomDropdown
          v-if="!isOnline"
          v-model="formData.ruangKelas"
          :options="dropdownOptions.ruangKelas"
          placeholder="Pilih ruang kelas"
          label="Ruang Kelas"
          :searchable="true"
          :clearable="true"
          :required="!isOnline"
          :showError="showError.ruangKelas"
        />
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-4 mt-8">
        <button
          @click="handleCancel"
          type="button"
          class="px-8 py-2 font-semibold border-2 border-active-blue text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
        >
          Batal
        </button>
        <button
          type="submit"
          class="px-8 py-2 font-semibold bg-active-blue text-white rounded-lg hover:bg-hover-button transition-colors"
        >
          Simpan
        </button>
      </div>
    </form>
  </div>
</template>
