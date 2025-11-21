<template>
  <!-- Header -->
  <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
    <h1 class="text-xl sm:text-3xl font-bold text-black">Detail Jadwal</h1>
    <p class="text-sm sm:text-lg text-black">Monitor dan kelola perkuliahan kampus</p>
  </div>

  <!-- Detail Card -->
  <div class="bg-white rounded-xl shadow-lg p-8">
    <!-- Grid Layout for Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-8">
      <!-- Left Column -->
      <div class="space-y-8">
        <!-- Periode Tahun Ajaran -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Periode Tahun Ajaran</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.periodeTahunAjaran }}</p>
        </div>

        <!-- Jenis Jadwal -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Jenis Jadwal</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.jenisJadwal }}</p>
        </div>

        <!-- Mata Kuliah -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Mata Kuliah</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.mataKuliah }}</p>
        </div>

        <!-- Program Studi -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Program Studi</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.programStudi }}</p>
        </div>

        <!-- Dosen 1 -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Dosen 1</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.dosen1 }}</p>
        </div>

        <!-- Laboran -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Laboran</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.laboran }}</p>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-8">
        <!-- Hari -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Hari</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.hari }}</p>
        </div>

        <!-- Waktu Perkuliahan -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Waktu Perkuliahan</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.waktuPerkuliahan }}</p>
        </div>

        <!-- Status -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Status</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.status }}</p>
        </div>

        <!-- Kelas -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Kelas</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.kelas }}</p>
        </div>

        <!-- Dosen 2 -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Dosen 2</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.dosen2 }}</p>
        </div>

        <!-- Ruang Kelas -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Ruang Kelas</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.ruangKelas }}</p>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-4 mt-12 pt-8">
      <button
        @click="handleBack"
        class="px-8 py-2 font-semibold border-2 border-active-blue text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
      >
        Kembali
      </button>
      <button
        @click="handleEdit"
        class="px-8 py-2 font-semibold bg-active-blue text-white rounded-lg hover:bg-hover-button transition-colors"
      >
        Edit Jadwal
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Temporary static data for UI preview
const scheduleData = reactive({
  periodeTahunAjaran: 'Semester Gasal 2025',
  hari: 'Senin',
  jenisJadwal: 'Jadwal Semester',
  waktuPerkuliahan: '13:00 - 16:00',
  mataKuliah: 'Algoritma dan Pemrograman',
  status: 'Online',
  programStudi: 'Teknik Informatika',
  kelas: 'A',
  dosen1: 'Adhe Aryswan, S.Pd., M.Si.',
  dosen2: 'Haposan Vincentius, S.T., M.Sc.',
  laboran: 'Andi Wijaya, S.Kom.',
  ruangKelas: '108 A',
})

// Navigate back to dashboard
const handleBack = () => {
  router.push('/dashboard-jadwal')
}

// Navigate to edit page
const handleEdit = () => {
  // Later you can pass the ID: router.push(`/edit-jadwal/${id}`)
  router.push('/edit-jadwal')
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
