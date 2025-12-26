<script setup lang="ts">
import { useRouter } from 'vue-router'
import FormJadwal from '@/views/dashboard/FormJadwal.vue'
import { dashboardAPI } from '@/services/dashboardAPI'
import { useToast } from '@/composables/UseToast'

const router = useRouter()
const { success, error } = useToast()

// Handle form submission
const handleSubmit = async (payload: any) => {
  try {
    const response = await dashboardAPI.createJadwal(payload)
    success('Jadwal berhasil ditambahkan!')

    const message = response?.data?.message
    if (message) {
      return error(message)
    }

    router.push({ name: 'dashboard' })
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal menambahkan jadwal'
    error(message)
  }
}

// Handle cancel
const handleCancel = () => {
  if (confirm('Apakah Anda yakin ingin membatalkan? Data yang diisi akan hilang.')) {
    router.push({ name: 'dashboard' })
  }
}
</script>

<template>
  <div>
    <div class="mb-6 mt-10 sm:mt-10 xl:mt-0">
      <h1 class="text-xl sm:text-3xl font-bold text-black">Tambah Jadwal</h1>
      <p class="text-sm sm:text-base text-black">
        Isi form di bawah untuk menambahkan jadwal perkuliahan
      </p>
    </div>

    <FormJadwal mode="create" @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
