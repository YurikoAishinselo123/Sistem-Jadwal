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
        />

        <!-- Hari -->
        <CustomDropdown
          v-model="formData.hari"
          :options="dropdownOptions.hari"
          placeholder="Pilih hari"
          label="Hari"
          :searchable="true"
        />

        <!-- Jenis Jadwal -->
        <CustomDropdown
          v-model="formData.jenisJadwal"
          :options="dropdownOptions.jenisJadwal"
          placeholder="Pilih jenis jadwal"
          label="Jenis Jadwal"
          :searchable="false"
        />

        <!-- Waktu Perkuliahan -->
        <CustomDropdown
          v-model="formData.waktuPerkuliahan"
          :options="dropdownOptions.waktuPerkuliahan"
          placeholder="Pilih waktu perkuliahan"
          label="Waktu Perkuliahan"
          :searchable="true"
        />

        <!-- Mata Kuliah -->
        <CustomDropdown
          v-model="formData.mataKuliah"
          :options="dropdownOptions.mataKuliah"
          placeholder="Pilih mata kuliah"
          label="Mata Kuliah"
          :searchable="true"
        />

        <!-- Jenis Mata Kuliah -->
        <CustomDropdown
          v-model="formData.jenisMataKuliah"
          :options="dropdownOptions.jenisMataKuliah"
          placeholder="Pilih jenis mata kuliah"
          label="Jenis Mata Kuliah"
          :searchable="true"
        />

        <!-- Program Studi -->
        <CustomDropdown
          v-model="formData.programStudi"
          :options="dropdownOptions.programStudi"
          placeholder="Pilih program studi"
          label="Program Studi"
          :searchable="true"
        />

        <!-- Kelas -->
        <CustomDropdown
          v-model="formData.kelas"
          :options="dropdownOptions.kelas"
          placeholder="Pilih kelas"
          label="Kelas"
          :searchable="true"
        />

        <!-- Dosen 1 -->
        <CustomDropdown
          v-model="formData.dosen1"
          :options="dropdownOptions.dosen"
          placeholder="Pilih dosen 1"
          label="Dosen 1"
          :searchable="true"
        />

        <!-- Dosen 2 -->
        <CustomDropdown
          v-model="formData.dosen2"
          :options="dropdownOptions.dosen"
          placeholder="Pilih dosen 2"
          label="Dosen 2"
          :searchable="true"
        />

        <!-- Laboran -->
        <CustomDropdown
          v-model="formData.laboran"
          :options="dropdownOptions.laboran"
          placeholder="Pilih laboran"
          label="Laboran"
          :searchable="true"
        />

        <!-- Ruang Kelas -->
        <CustomDropdown
          v-model="formData.ruangKelas"
          :options="dropdownOptions.ruangKelas"
          placeholder="Pilih ruang kelas"
          label="Ruang Kelas"
          :searchable="true"
        />
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end gap-4 mt-8">
        <button
          @click="handleCancel"
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
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '@/components/CustomDropdown.vue'

const router = useRouter()

// Form data
const formData = reactive({
  periodeTahunAjaran: '',
  hari: '',
  jenisJadwal: '',
  waktuPerkuliahan: '',
  mataKuliah: '',
  jenisMataKuliah: '',
  programStudi: '',
  kelas: '',
  dosen1: '',
  dosen2: '',
  laboran: '',
  ruangKelas: '',
})

// Dropdown options
const dropdownOptions = {
  periodeTahunAjaran: ['Gasal 2024', 'Genap 2024', 'Gasal 2025', 'Genap 2025'],
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
  jenisJadwal: ['Teori', 'Praktikum', 'Seminar', 'Workshop'],
  waktuPerkuliahan: ['07:00 - 09:30', '09:30 - 12:00', '13:00 - 15:30', '15:30 - 18:00'],
  mataKuliah: [
    'Algoritma',
    'Pemrograman',
    'Pengantar Teknik Perkapalan',
    'Basis Data',
    'Sistem Operasi',
  ],
  jenisMataKuliah: ['Wajib', 'Pilihan', 'MKU', 'MKDU'],
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
    'waktuPerkuliahan',
    'mataKuliah',
    'programStudi',
    'kelas',
    'dosen1',
    'ruangKelas',
  ]

  const missingFields = requiredFields.filter((field) => !formData[field as keyof typeof formData])

  if (missingFields.length > 0) {
    alert('Harap lengkapi semua field yang wajib diisi!')
    return
  }

  // Create new schedule object
  const newSchedule = {
    hari: formData.hari,
    kodeMakul: 'AUTO-GENERATED', // You might want to generate this
    programStudi: formData.programStudi,
    mataKuliah: formData.mataKuliah,
    dosen1: formData.dosen1,
    dosen2: formData.dosen2,
    laboran: formData.laboran,
    ruang: formData.ruangKelas,
    waktu: formData.waktuPerkuliahan,
    periodeTahunAjaran: formData.periodeTahunAjaran,
  }

  console.log('New schedule:', newSchedule)

  // Here you would typically send this to your backend API
  // For now, just show success message
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
