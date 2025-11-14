<template>
  <!-- Header -->
  <div class="mb-8 mt-12 sm:mt-10 xl:mt-0">
    <h1 class="text-xl sm:text-3xl font-bold text-black">Tambah Jadwal</h1>
    <p class="text-sm sm:text-lg font-base text-black">
      Isi form di bawah untuk menambahkan jadwal perkuliahan
    </p>
  </div>

  <!-- Form -->
  <div class="bg-white rounded-xl shadow-lg p-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Row 1 -->
      <SelectField
        label="Hari"
        v-model="formData.hari"
        :options="options.hari"
        placeholder="Pilih hari"
      />
      <SelectField
        label="Jenis Jadwal"
        v-model="formData.jenisJadwal"
        :options="options.jenisJadwal"
        placeholder="Pilih jenis jadwal"
      />

      <!-- Row 2 -->
      <SelectField
        label="Program Studi"
        v-model="formData.programStudi"
        :options="options.programStudi"
        placeholder="Pilih program studi"
      />
      <SelectField
        label="Mata Kuliah"
        v-model="formData.mataKuliah"
        :options="options.mataKuliah"
        placeholder="Pilih mata kuliah"
      />

      <!-- Row 3 -->
      <SelectField
        label="Dosen 1"
        v-model="formData.dosen1"
        :options="options.dosen"
        placeholder="Pilih dosen 1"
      />
      <SelectField
        label="Dosen 2"
        v-model="formData.dosen2"
        :options="options.dosen"
        placeholder="Pilih dosen 2"
      />

      <!-- Row 4 -->
      <SelectField
        label="Laboran"
        v-model="formData.laboran"
        :options="options.laboran"
        placeholder="Pilih laboran"
      />
      <SelectField
        label="Kelas"
        v-model="formData.kelas"
        :options="options.kelas"
        placeholder="Pilih kelas"
      />

      <!-- Row 5 -->
      <SelectField
        label="Jenis Mata Kuliah"
        v-model="formData.jenisMataKuliah"
        :options="options.jenisMataKuliah"
        placeholder="Pilih jenis mata kuliah"
      />
      <SelectField
        label="Ruang Kelas"
        v-model="formData.ruangKelas"
        :options="options.ruangKelas"
        placeholder="Pilih ruang kelas"
      />
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4 mt-8">
      <button
        @click="handleBatal"
        class="px-8 py-2 font-semibold border-2 border-active-blue text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
      >
        Batal
      </button>
      <button
        @click="handleSimpan"
        class="px-8 py-2 font-semibold bg-active-blue text-white rounded-lg hover:bg-hover-button transition-colors"
      >
        Simpan
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import SelectField from '@/components/SelectField.vue'

const formData = ref({
  hari: '',
  jenisJadwal: '',
  programStudi: '',
  mataKuliah: '',
  dosen1: '',
  dosen2: '',
  laboran: '',
  kelas: '',
  jenisMataKuliah: '',
  ruangKelas: '',
})

// Sample options data
const options = {
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
  jenisJadwal: ['Reguler', 'Praktikum', 'Seminar'],
  programStudi: ['Teknik Informatika', 'Mesin A', 'Mesin B', 'Elektro'],
  mataKuliah: ['Algoritma', 'Pemrograman', 'Pengantar Teknik Perkapalan', 'Basis Data'],
  dosen: [
    'Adhe Aryswan, S.Pd., M.Si.',
    'Haposan Vincentius Manalu, S.T., M.Sc.',
    'Dr. Budi Santoso, M.Kom.',
  ],
  laboran: ['Adhe Aryswan, S.Pd., M.Si.', 'Haposan Vincentius Manalu, S.T., M.Sc.'],
  kelas: ['A', 'B', 'C', 'D'],
  jenisMataKuliah: ['Teori', 'Praktikum', 'Teori & Praktikum'],
  ruangKelas: ['101 A', '102 B', '108 C', '201 A', '301 A'],
}

const handleBatal = () => {
  formData.value = {
    hari: '',
    jenisJadwal: '',
    programStudi: '',
    mataKuliah: '',
    dosen1: '',
    dosen2: '',
    laboran: '',
    kelas: '',
    jenisMataKuliah: '',
    ruangKelas: '',
  }
  console.log('Form dibatalkan')
}

const handleSimpan = () => {
  console.log('Data yang akan disimpan:', formData.value)
  alert('Jadwal berhasil disimpan!')
}
</script>
