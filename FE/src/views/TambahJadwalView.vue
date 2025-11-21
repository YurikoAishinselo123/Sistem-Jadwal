<template>
  <!-- Header -->
  <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
    <h1 class="text-xl sm:text-3xl font-bold text-black">Tambah Jadwal</h1>
    <p class="text-sm sm:text-lg text-black">
      Isi form di bawah untuk menambahkan jadwal perkuliahan
    </p>
  </div>

  <!-- Form -->
  <div class="bg-white rounded-xl shadow-lg p-8">
    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Periode Tahun Ajaran -->
        <CustomDropdown
          v-model="formData.periodeTahunAjaran"
          :options="dropdownOptions.periodeTahunAjaran"
          placeholder="Pilih periode tahun ajaran"
          label="Periode Tahun Ajaran"
          :searchable="false"
          :clearable="false"
          :required="true"
        />

        <!-- Hari -->
        <CustomDropdown
          v-model="formData.hari"
          :options="dropdownOptions.hari"
          placeholder="Pilih hari"
          label="Hari"
          :searchable="true"
          :clearable="false"
          :required="true"
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
        />

        <!-- Dosen 1 -->
        <CustomDropdown
          v-model="formData.dosen1"
          :options="dropdownOptions.dosen"
          placeholder="Pilih dosen 1"
          label="Dosen 1"
          :searchable="true"
          :clearable="false"
          :required="true"
        />

        <!-- Dosen 2 -->
        <CustomDropdown
          v-model="formData.dosen2"
          :options="dropdownOptions.dosen"
          placeholder="Pilih dosen 2"
          label="Dosen 2"
          :searchable="true"
          :clearable="false"
          :required="true"
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
        />

        <!-- Ruang Kelas -->
        <CustomDropdown
          v-model="formData.ruangKelas"
          :options="dropdownOptions.ruangKelas"
          placeholder="Pilih ruang kelas"
          label="Ruang Kelas"
          :searchable="true"
          :clearable="true"
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

<script setup lang="ts">
import { reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '@/components/CustomDropdown.vue'

const router = useRouter()

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

// All time slots combined
const allTimeSlots = [...timeSlots.pagi, ...timeSlots.malam]

// Form data
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

// Determine which session (pagi/malam) based on selected start time
const selectedSession = computed(() => {
  if (!formData.waktuMulai) return null
  if (timeSlots.pagi.includes(formData.waktuMulai)) return 'pagi'
  if (timeSlots.malam.includes(formData.waktuMulai)) return 'malam'
  return null
})

// Available start times (all times)
const availableStartTimes = computed(() => {
  return allTimeSlots
})

// Available end times based on selected start time
const availableEndTimes = computed(() => {
  if (!formData.waktuMulai) return allTimeSlots

  const session = selectedSession.value
  if (!session) return []

  // Get the appropriate session times
  const sessionTimes = timeSlots[session]

  // Find the index of selected start time
  const startIndex = sessionTimes.indexOf(formData.waktuMulai)

  // Return only times after the start time in the same session
  return sessionTimes.slice(startIndex + 1)
})

// Watch for changes in waktuMulai to reset waktuSelesai if needed
watch(
  () => formData.waktuMulai,
  (newStartTime) => {
    // If start time changes, check if end time is still valid
    if (formData.waktuSelesai) {
      const validEndTimes = availableEndTimes.value
      if (!validEndTimes.includes(formData.waktuSelesai)) {
        formData.waktuSelesai = ''
      }
    }
  },
)

// Dropdown options
const dropdownOptions = {
  periodeTahunAjaran: ['Gasal 2024', 'Genap 2024', 'Gasal 2025', 'Genap 2025'],
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
  jenisJadwal: ['Jadwal Semester', 'Jadwal Ujian', 'Jadwal Pengganti'],
  mataKuliah: [
    'Algoritma',
    'Pemrograman',
    'Pengantar Teknik Perkapalan',
    'Basis Data',
    'Sistem Operasi',
  ],
  status: ['Online', 'Offline'],
  programStudi: ['Teknik Informatika', 'Mesin A', 'Mesin B', 'Teknik Elektro'],
  kelas: ['A', 'B', 'C', 'D', 'E'],
  dosen: [
    'Adhe Aryswan, S.Pd., M.Si.',
    'Haposan Vincentius, S.T., M.Sc.',
    'Dr. Budi Santoso, M.Kom.',
    'Prof. Siti Rahayu, Ph.D.',
  ],
  laboran: ['Adhe Aryswan, S.Pd., M.Si.', 'Haposan Manalu, S.T., M.Sc.', 'Andi Wijaya, S.Kom.'],
  ruangKelas: ['108 A', '108 B', '108 C', '201 A', '201 B', '202 A', '202 B', '301 A', '301 B'],
}

// Form submission
const handleSubmit = () => {
  // Validate required fields
  const requiredFields = [
    'periodeTahunAjaran',
    'hari',
    'jenisJadwal',
    'waktuMulai',
    'waktuSelesai',
    'mataKuliah',
    'status',
    'programStudi',
    'kelas',
    'dosen1',
    'dosen2',
    'laboran',
  ]

  const missingFields = requiredFields.filter((field) => !formData[field as keyof typeof formData])

  if (missingFields.length > 0) {
    alert('Harap lengkapi semua field yang wajib diisi!')
    return
  }

  // Validate time selection
  if (!availableEndTimes.value.includes(formData.waktuSelesai)) {
    alert(
      'Waktu selesai tidak valid! Pastikan waktu selesai lebih besar dari waktu mulai dan dalam sesi yang sama.',
    )
    return
  }

  // Create new schedule object
  const newSchedule = {
    hari: formData.hari,
    kodeMakul: 'AUTO-GENERATED',
    programStudi: formData.programStudi,
    mataKuliah: formData.mataKuliah,
    status: formData.status,
    jenisJadwal: formData.jenisJadwal,
    dosen1: formData.dosen1,
    dosen2: formData.dosen2,
    laboran: formData.laboran,
    ruang: formData.ruangKelas,
    waktuMulai: formData.waktuMulai,
    waktuSelesai: formData.waktuSelesai,
    waktuPerkuliahan: `${formData.waktuMulai} - ${formData.waktuSelesai}`,
    sesi: selectedSession.value,
    periodeTahunAjaran: formData.periodeTahunAjaran,
  }

  console.log('New schedule:', newSchedule)

  // Here you would typically send this to your backend API
  alert('Jadwal berhasil ditambahkan!')

  // Navigate back to dashboard
  router.push('/dashboard-jadwal')
}

// Cancel action
const handleCancel = () => {
  if (confirm('Apakah Anda yakin ingin membatalkan? Data yang diisi akan hilang.')) {
    router.push('/dashboard-jadwal')
  }
}
</script>

<style scoped>
/* Add any additional custom styles here if needed */
</style>
