<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { dashboardAPI } from '@/services/dashboardAPI'

const router = useRouter()
const route = useRoute()

// Ensure ID is valid
const jadwalId = Number(route.params.id)
if (!jadwalId || Number.isNaN(jadwalId)) {
  router.push('/dashboard-jadwal')
}

// Reactive state for UI
const scheduleData = reactive({
  periodeTahunAjaran: '-',
  hari: '-',
  jenisJadwal: 'Jadwal Semester',
  waktuPerkuliahan: '-',
  mataKuliah: '-',
  status: '-',
  jenisMakul: '-',
  programStudi: '-',
  kelas: '-',
  dosen1: '-',
  dosen2: '-',
  laboran: '-',
  ruangKelas: '-',
  sks: '-',
  sesi: '-',
})

// Fetch detail by ID
const fetchDetailJadwal = async () => {
  try {
    const { data } = await dashboardAPI.getJadwalById(jadwalId)
    const item = Array.isArray(data) ? data[0] : data

    if (!item) throw new Error('Data not found')

    scheduleData.periodeTahunAjaran = item.periode || '-'
    scheduleData.hari = item.hari_jadwal || '-'
    scheduleData.jenisJadwal = item.jenis_jadwal || 'Jadwal Semester'

    scheduleData.waktuPerkuliahan =
      item.waktu_mulai && item.waktu_selesai ? `${item.waktu_mulai} - ${item.waktu_selesai}` : '-'
    scheduleData.jenisMakul = capitalizeFirstLetter(item.jenis_makul || '-')
    scheduleData.mataKuliah = item.nama_makul || '-'
    scheduleData.status = item.status || '-'
    scheduleData.programStudi = item.nama_prodi || '-'
    scheduleData.kelas = item.kelas || '-'
    scheduleData.dosen1 = item.nama_dosen_1 || '-'
    scheduleData.dosen2 = item.nama_dosen_2 || '-'
    scheduleData.laboran = item.nama_laboran || '-'
    scheduleData.ruangKelas = item.nama_ruangan || '-'
    scheduleData.sks = item.sks_makul || '-'
    scheduleData.sesi = item.sesi_makul || '-'
  } catch (err) {
    console.error('Error getting jadwal detail:', err)
    router.push('/dashboard-jadwal')
  }
}

function capitalizeFirstLetter(str: string) {
  if (!str) return str
  return str.charAt(0).toUpperCase() + str.slice(1)
}

onMounted(fetchDetailJadwal)

const handleBack = () => router.push('/dashboard-jadwal')
const handleEdit = () => router.push(`/edit-jadwal/${jadwalId}`)
</script>

<template>
  <!-- Header -->
  <div class="mb-6 mt-10 sm:mt-10 xl:mt-0">
    <h1 class="text-xl sm:text-3xl font-bold text-black">Detail Jadwal</h1>
    <p class="text-sm sm:text-base text-black">Monitor dan kelola perkuliahan kampus</p>
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

        <div class="grid grid-cols-2 gap-4">
          <!-- Jenis Jadwal -->
          <div>
            <p class="text-sm text-gray-600 mb-2">Jenis Jadwal</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.jenisJadwal }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-600 mb-2">Kelas</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.kelas }}</p>
          </div>
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

        <!-- Dosen 2 -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Dosen 2</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.dosen2 }}</p>
        </div>

        <!-- Dosen 3 -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Dosen 3</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.dosen2 }}</p>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-8">
        <div class="grid grid-cols-2 gap-4">
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
        </div>

        <!-- SKS & Sesi (Same Row) -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-600 mb-2">SKS</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.sks }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-2">Sesi</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.sesi }}</p>
          </div>
        </div>

        <!-- Kelas & Ruang Kelas (Same Row) -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-600 mb-2">Ruang Kelas Teori</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.ruangKelas }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-2">Ruang Kelas Praktek</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.ruangKelas }}</p>
          </div>
        </div>

        <!-- Status & Jenis Makul (Same Row) -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-600 mb-2">Status</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.status }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-600 mb-2">Jenis Makul</p>
            <p class="text-lg font-bold text-black">{{ scheduleData.jenisMakul }}</p>
          </div>
        </div>

        <!-- Laboran -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Laboran 1</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.laboran }}</p>
        </div>

        <!-- Laboran -->
        <div>
          <p class="text-sm text-gray-600 mb-2">Laboran 2</p>
          <p class="text-lg font-bold text-black">{{ scheduleData.laboran }}</p>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-4 pt-8">
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
