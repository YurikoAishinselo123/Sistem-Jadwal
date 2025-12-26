<script setup lang="ts">
import { reactive, computed, watch, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '@/components/CustomDropdown.vue'

import { dashboardAPI } from '@/services/dashboardAPI'
import { useToast } from '@/composables/UseToast'

const router = useRouter()
const { success, error } = useToast()

// Type definitions
interface Periode {
  id: number
  periode: string
}

interface Prodi {
  id: number
  nama_prodi: string
}

interface Makul {
  id: number
  nama_makul: string
  sesi_makul: number
}

interface Dosen {
  id: number
  nama_dosen: string
}

interface Laboran {
  id: number
  nama_laboran: string
}

interface RuanganPraktek {
  id: number
  nama_ruangan_praktek: string
}

interface RuanganTeori {
  id: number
  nama_ruangan_teori: string
}

interface WaktuKuliah {
  id: number
  waktu: string
}

interface ApiData {
  periode: Periode[]
  prodi: Prodi[]
  makul: Makul[]
  dosen: Dosen[]
  laboran: Laboran[]
  ruanganPraktek: RuanganPraktek[]
  ruanganTeori: RuanganTeori[]
  waktu: WaktuKuliah[]
}

// API data storage
const apiData = ref<ApiData>({
  periode: [],
  prodi: [],
  makul: [],
  dosen: [],
  laboran: [],
  ruanganPraktek: [],
  ruanganTeori: [],
  waktu: [],
})

const isLoading = ref(true)

// Time slots definition
const timeSlots = {
  pagi: [
    '07:50',
    '08:40',
    '09:30',
    '10:20',
    '11:10',
    '12:00',
    '12:50',
    '13:40',
    '14:30',
    '15:20',
    '16:10',
    '17:00',
  ],
  malam: ['17:10', '18:00', '19:00', '19:35', '20:10', '20:45', '21:20', '21:55', '22:30', '23:05'],
}

const allTimeSlots = [...timeSlots.pagi, ...timeSlots.malam]
const isOnline = computed(() => formData.status === 'Online')

// Form data - stores display values with separate variables
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
  dosen3: '',
  laboran1: '',
  laboran2: '',
  ruanganKelasPraktek: '',
  ruangKelasTeori: '',
})

// Track if optional fields are visible
const showDosen3 = ref(false)
const showLaboran2 = ref(false)

// Store selected IDs for API payload
const selectedIds = reactive<{
  periodeId: number | null
  prodiId: number | null
  makulId: number | null
  dosen1Id: number | null
  dosen2Id: number | null
  dosen3Id: number | null
  laboran1Id: number | null
  laboran2Id: number | null
  ruanganPraktekId: number | null
  ruanganTeoriId: number | null
}>({
  periodeId: null,
  prodiId: null,
  makulId: null,
  dosen1Id: null,
  dosen2Id: null,
  dosen3Id: null,
  laboran1Id: null,
  laboran2Id: null,
  ruanganPraktekId: null,
  ruanganTeoriId: null,
})

// Error state
const showError = reactive({
  periodeTahunAjaran: false,
  hari: false,
  jenisJadwal: false,
  waktuMulai: false,
  mataKuliah: false,
  status: false,
  programStudi: false,
  kelas: false,
  dosen1: false,
  dosen2: false,
  laboran1: false,
  ruanganKelasPraktek: false,
  ruanganKelasTeori: false,
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

// Get selected makul object
const selectedMakul = computed<Makul | null>(() => {
  if (!formData.mataKuliah) return null
  const found = apiData.value.makul.find((item) => item.nama_makul === formData.mataKuliah)
  return found || null
})

// Determine which session the start time belongs to
const selectedSession = computed<'pagi' | 'malam' | null>(() => {
  if (!formData.waktuMulai) return null
  if (timeSlots.pagi.includes(formData.waktuMulai)) return 'pagi'
  if (timeSlots.malam.includes(formData.waktuMulai)) return 'malam'
  return null
})

// Get available slots in the current session
const availableSlotsInSession = computed(() => {
  if (!formData.waktuMulai || !selectedSession.value) return 0

  const sessionTimes = timeSlots[selectedSession.value]
  const startIndex = sessionTimes.indexOf(formData.waktuMulai)
  if (startIndex === -1) return 0

  return sessionTimes.length - startIndex
})

// Check if start time has enough slots for the course within the same session
const hasEnoughSlots = computed(() => {
  if (!formData.waktuMulai || !selectedMakul.value) return true

  const requiredSlots = selectedMakul.value.sesi_makul
  const availableSlots = availableSlotsInSession.value

  return availableSlots >= requiredSlots
})

// Calculate end time based on start time and sesi_makul (within same session)
const calculateEndTime = (startTime: string, sesiMakul: number): string | null => {
  if (!startTime || !sesiMakul) return null

  // Determine which session the start time belongs to
  let session: 'pagi' | 'malam' | null = null
  if (timeSlots.pagi.includes(startTime)) session = 'pagi'
  else if (timeSlots.malam.includes(startTime)) session = 'malam'

  if (!session) return null

  const sessionTimes = timeSlots[session]

  // Find the start time index in the session
  const startIndex = sessionTimes.indexOf(startTime)
  if (startIndex === -1) return null

  // Calculate end time index within the same session
  const endIndex = startIndex + sesiMakul

  // Check if end index is valid within the session
  if (endIndex < 0 || endIndex >= sessionTimes.length) {
    console.warn('End time exceeds available time slots in the current session')
    return null
  }

  // Return the time slot at the calculated index
  const endTime = sessionTimes[endIndex]
  return endTime ?? null
}

// Auto-calculate waktu selesai when waktuMulai or mataKuliah changes
watch([() => formData.waktuMulai, () => formData.mataKuliah], () => {
  if (formData.waktuMulai && selectedMakul.value) {
    if (!hasEnoughSlots.value) {
      formData.waktuSelesai = ''
      return
    }
    const calculatedEndTime = calculateEndTime(formData.waktuMulai, selectedMakul.value.sesi_makul)
    formData.waktuSelesai = calculatedEndTime || ''
  } else {
    formData.waktuSelesai = ''
  }
})

// Available start times
const availableStartTimes = computed(() => allTimeSlots)

// Filter dosen options - exclude already selected dosens
const dosen1Options = computed(() => {
  return apiData.value.dosen
    .map((item) => item.nama_dosen)
    .filter((name) => name !== formData.dosen2 && name !== formData.dosen3)
})

const dosen2Options = computed(() => {
  return apiData.value.dosen
    .map((item) => item.nama_dosen)
    .filter((name) => name !== formData.dosen1 && name !== formData.dosen3)
})

const dosen3Options = computed(() => {
  return apiData.value.dosen
    .map((item) => item.nama_dosen)
    .filter((name) => name !== formData.dosen1 && name !== formData.dosen2)
})

// Filter laboran options - exclude already selected laborans
const laboran1Options = computed(() => {
  return apiData.value.laboran
    .map((item) => item.nama_laboran)
    .filter((name) => name !== formData.laboran2)
})

const laboran2Options = computed(() => {
  return apiData.value.laboran
    .map((item) => item.nama_laboran)
    .filter((name) => name !== formData.laboran1)
})

// Dropdown options from API
const dropdownOptions = computed(() => ({
  periodeTahunAjaran: apiData.value.periode.map((item) => item.periode),
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
  jenisJadwal: ['Jadwal Semester', 'Jadwal Ujian', 'Jadwal Pengganti'],
  mataKuliah: apiData.value.makul.map((item) => item.nama_makul),
  status: ['Online', 'Offline'],
  programStudi: apiData.value.prodi.map((item) => item.nama_prodi),
  kelas: ['A', 'B', 'C', 'D', 'E'],
  dosen1: dosen1Options.value,
  dosen2: dosen2Options.value,
  dosen3: dosen3Options.value,
  laboran1: laboran1Options.value,
  laboran2: laboran2Options.value,
  ruangKelasPraktek: apiData.value.ruanganPraktek.map((item) => item.nama_ruangan_praktek),
  ruangKelasTeori: apiData.value.ruanganTeori.map((item) => item.nama_ruangan_teori),
}))

// Helper function to get ID from selected value
const getIdFromValue = <T extends { id: number }>(
  array: T[],
  value: string,
  nameKey: keyof T,
): number | null => {
  const item = array.find((item) => String(item[nameKey]) === value)
  return item?.id ?? null
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
  () => formData.dosen3,
  (newValue) => {
    selectedIds.dosen3Id = getIdFromValue(apiData.value.dosen, newValue, 'nama_dosen')
  },
)

watch(
  () => formData.laboran1,
  (newValue) => {
    selectedIds.laboran1Id = getIdFromValue(apiData.value.laboran, newValue, 'nama_laboran')
  },
)

watch(
  () => formData.laboran2,
  (newValue) => {
    selectedIds.laboran2Id = getIdFromValue(apiData.value.laboran, newValue, 'nama_laboran')
  },
)

watch(
  () => formData.ruanganKelasPraktek,
  (newValue) => {
    selectedIds.ruanganPraktekId = getIdFromValue(
      apiData.value.ruanganPraktek,
      newValue,
      'nama_ruangan_praktek',
    )
  },
)

watch(
  () => formData.ruangKelasTeori,
  (newValue) => {
    selectedIds.ruanganTeoriId = getIdFromValue(
      apiData.value.ruanganTeori,
      newValue,
      'nama_ruangan_teori',
    )
  },
)

watch(
  () => formData.status,
  (newValue) => {
    if (newValue === 'Online') {
      formData.ruangKelasTeori = ''
      formData.ruanganKelasPraktek = ''
      selectedIds.ruanganPraktekId = null
      selectedIds.ruanganTeoriId = null
      showError.ruanganKelasPraktek = false
      showError.ruanganKelasTeori = false
    }
  },
)

// Methods to add/remove dosen
const addDosen = () => {
  showDosen3.value = true
}

const removeDosen = () => {
  showDosen3.value = false
  formData.dosen3 = ''
  selectedIds.dosen3Id = null
}

// Methods to add/remove laboran
const addLaboran = () => {
  showLaboran2.value = true
}

const removeLaboran = () => {
  showLaboran2.value = false
  formData.laboran2 = ''
  selectedIds.laboran2Id = null
}

// Form submission
const handleSubmit = async () => {
  // Reset errors
  Object.keys(showError).forEach((key) => (showError[key as keyof typeof showError] = false))

  // Validate required fields (excluding optional dosen3 and laboran2)
  const requiredFields = Object.keys(showError).filter((key) => {
    if ((key === 'ruanganKelasPraktek' || key === 'ruanganKelasTeori') && isOnline.value) {
      return false
    }
    return true
  })

  const missingFields = requiredFields.filter((key) => !formData[key as keyof typeof formData])

  if (missingFields.length > 0) {
    missingFields.forEach((key) => (showError[key as keyof typeof showError] = true))
    alert('Harap lengkapi semua field yang wajib diisi!')
    return
  }

  // Validate waktu_selesai is calculated
  if (!formData.waktuSelesai) {
    alert('Waktu selesai tidak dapat dihitung. Periksa waktu mulai dan mata kuliah.')
    return
  }

  // Validate enough time slots
  if (!hasEnoughSlots.value) {
    const sessionName = selectedSession.value === 'pagi' ? 'pagi' : 'malam'
    alert(
      `Waktu mulai tidak mencukupi dalam sesi ${sessionName}! Mata kuliah ini membutuhkan ${selectedMakul.value?.sesi_makul} slot waktu, tetapi hanya tersisa ${availableSlotsInSession.value} slot di sesi ${sessionName}. Silakan pilih waktu mulai yang lebih awal atau pilih sesi yang berbeda.`,
    )
    return
  }

  // Prepare payload with IDs (dosen3 and laboran2 are optional)
  const payload = {
    periode_tahun_id: selectedIds.periodeId,
    hari_jadwal: formData.hari,
    jenis_jadwal: formData.jenisJadwal,
    waktu_mulai: formData.waktuMulai,
    waktu_selesai: formData.waktuSelesai,
    makul_id: selectedIds.makulId,
    status: formData.status,
    prodi_id: selectedIds.prodiId,
    kelas: formData.kelas,
    dosen_1: selectedIds.dosen1Id,
    dosen_2: selectedIds.dosen2Id,
    ...(selectedIds.dosen3Id && { dosen_3: selectedIds.dosen3Id }), // Optional
    laboran_1: selectedIds.laboran1Id,
    ...(selectedIds.laboran2Id && { laboran_2: selectedIds.laboran2Id }), // Optional
    ...(isOnline.value ? {} : { ruangan_praktek_id: selectedIds.ruanganPraktekId }),
    ...(isOnline.value ? {} : { ruangan_teori_id: selectedIds.ruanganTeoriId }),
  }

  try {
    const response = await dashboardAPI.createJadwal(payload)
    success('Jadwal berhasil ditambahkan!')

    const message = response?.data?.message
    if (message) {
      return error(message)
    }

    router.push({ name: 'dashboard' })
  } catch (err: any) {
    const message = err?.response?.data.message || 'Gagal menambahkan jadwal'
    error(message)
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
  <div class="mb-8 mt-10 sm:mt-14 xl:mt-0">
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
  <div v-else class="bg-white rounded-xl shadow-lg p-8 h-[78vh] overflow-y-auto">
    <form @submit.prevent="handleSubmit" class="space-y-10">
      <!-- Info Akademik -->
      <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Info Akademik</h2>

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
        </div>
      </div>

      <!-- WAKTU & LOKASI -->
      <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Waktu & Lokasi</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Hari -->
          <CustomDropdown
            v-model="formData.hari"
            :options="dropdownOptions.hari"
            label="Hari"
            placeholder="Pilih hari"
            :required="true"
            :showError="showError.hari"
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

          <!-- Waktu Mulai -->
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

          <!-- Waktu Selesai (Auto) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Waktu Selesai Perkuliahan <span class="text-red-500">*</span>
            </label>
            <div
              class="px-4 py-2 border rounded-lg"
              :class="
                !hasEnoughSlots && formData.waktuMulai && selectedMakul
                  ? 'border-red-500 bg-red-50 text-red-700'
                  : 'border-gray-300 bg-gray-50 text-gray-700'
              "
            >
              {{ formData.waktuSelesai || 'Otomatis dihitung' }}
            </div>

            <p v-if="selectedMakul && formData.waktuMulai" class="text-xs mt-1">
              <span v-if="hasEnoughSlots" class="text-gray-500">
                Sesi {{ selectedSession === 'pagi' ? 'Pagi' : 'Malam' }}:
                {{ selectedMakul.sesi_makul }} slot waktu
              </span>
              <span v-else class="text-red-600 font-medium">
                Tidak cukup slot! Butuh {{ selectedMakul.sesi_makul }} slot, tersisa
                {{ availableSlotsInSession }} slot.
              </span>
            </p>

            <p v-else-if="selectedMakul" class="text-xs text-gray-500 mt-1">
              Sesi: {{ selectedMakul.sesi_makul }} slot waktu
            </p>
          </div>

          <!-- Ruang Kelas Teori-->
          <CustomDropdown
            v-if="!isOnline"
            v-model="formData.ruangKelasTeori"
            :options="dropdownOptions.ruangKelasTeori"
            placeholder="Pilih ruang kelas teori"
            label="Ruang Kelas Teori"
            :searchable="true"
            :clearable="true"
            :required="!isOnline"
            :showError="showError.ruanganKelasTeori"
          />

          <!-- Ruang Kelas Praktik -->
          <CustomDropdown
            v-if="!isOnline"
            v-model="formData.ruanganKelasPraktek"
            :options="dropdownOptions.ruangKelasPraktek"
            placeholder="Pilih ruang kelas praktikum"
            label="Ruang Kelas Praktik"
            :searchable="true"
            :clearable="true"
            :required="!isOnline"
            :showError="showError.ruanganKelasPraktek"
          />
        </div>
      </div>

      <!-- Pengajar -->
      <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pengajar</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Dosen 1 -->
          <CustomDropdown
            v-model="formData.dosen1"
            :options="dropdownOptions.dosen1"
            label="Dosen 1"
            placeholder="Pilih dosen 1"
            :searchable="true"
            :clearable="true"
            :required="true"
            :showError="showError.dosen1"
          />

          <!-- Dosen 2 -->
          <CustomDropdown
            v-model="formData.dosen2"
            :options="dropdownOptions.dosen2"
            label="Dosen 2"
            placeholder="Pilih dosen 2"
            :searchable="true"
            :clearable="true"
            :required="true"
            :showError="showError.dosen2"
          />
        </div>

        <!-- Optional Dosen 3 with Remove Button -->
        <div v-if="showDosen3" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="relative">
            <CustomDropdown
              v-model="formData.dosen3"
              :options="dropdownOptions.dosen3"
              label="Dosen 3 (opsional)"
              placeholder="Pilih dosen 3"
              :searchable="true"
              :clearable="true"
            />
            <button
              @click="removeDosen"
              type="button"
              class="absolute top-0 right-0 text-red-600 text-sm hover:text-red-800 hover:underline"
            >
              ✕ Hapus
            </button>
          </div>
        </div>

        <!-- Add Dosen Button -->
        <button
          v-if="!showDosen3"
          @click="addDosen"
          type="button"
          class="mt-3 text-blue-600 text-sm underline hover:text-blue-800 mb-6"
        >
          + Tambah Dosen
        </button>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <!-- Laboran 1 -->
          <CustomDropdown
            v-model="formData.laboran1"
            :options="dropdownOptions.laboran1"
            label="Laboran 1"
            placeholder="Pilih laboran 1"
            :searchable="true"
            :clearable="true"
            :required="true"
            :showError="showError.laboran1"
          />

          <!-- Laboran 2 (Optional) with Remove Button -->
          <div v-if="showLaboran2" class="relative">
            <CustomDropdown
              v-model="formData.laboran2"
              :options="dropdownOptions.laboran2"
              label="Laboran 2 (opsional)"
              placeholder="Pilih laboran 2"
              :searchable="true"
              :clearable="true"
            />
            <button
              @click="removeLaboran"
              type="button"
              class="absolute top-0 right-0 text-red-600 text-sm hover:text-red-800 hover:underline"
            >
              ✕ Hapus
            </button>
          </div>
        </div>

        <!-- Add Laboran Button -->
        <button
          v-if="!showLaboran2"
          @click="addLaboran"
          type="button"
          class="mt-3 text-blue-600 text-sm underline hover:text-blue-800"
        >
          + Tambah Laboran
        </button>
      </div>

      <!-- Action Button -->
      <div class="flex justify-end gap-4 mt-4">
        <button
          @click="handleCancel"
          type="button"
          class="px-8 py-2 font-semibold border-2 border-active-blue text-blue-600 rounded-lg hover:bg-blue-50"
        >
          Batal
        </button>

        <button
          type="submit"
          class="px-8 py-2 font-semibold bg-active-blue text-white rounded-lg hover:bg-hover-button"
        >
          Simpan
        </button>
      </div>
    </form>
  </div>
</template>
