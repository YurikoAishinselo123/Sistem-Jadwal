<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import FormJadwal from '@/views/dashboard/FormJadwal.vue'
import { dashboardAPI } from '@/services/dashboardAPI'
import { useToast } from '@/composables/UseToast'

const router = useRouter()
const route = useRoute()
const { success, error } = useToast()

const jadwalData = ref<any>(null)
const isLoading = ref(true)

// CONFIRMATION DIALOG STATE
const dialogOpen = ref(false)
const dialogTitle = ref('')
const dialogSubtitle = ref('')
const dialogAction = ref('')
const pendingPayload = ref<any>(null)

// Get ID from route params
const jadwalId = ref<number>(Number(route.params.id))

// Fetch jadwal data by ID
const fetchJadwalData = async () => {
  try {
    isLoading.value = true
    const response = await dashboardAPI.getJadwalById(jadwalId.value)

    // API returns array with single object
    if (response.data && response.data.length > 0) {
      jadwalData.value = response.data[0]
    } else {
      error('Data jadwal tidak ditemukan')
      router.push({ name: 'dashboard' })
    }
  } catch (err: any) {
    console.error('Error fetching jadwal data:', err)
    const message = err?.response?.data?.message || 'Gagal memuat data jadwal'
    error(message)
    router.push({ name: 'dashboard' })
  } finally {
    isLoading.value = false
  }
}

const handleSubmit = (payload: any) => {
  dialogTitle.value = 'Simpan Perubahan'
  dialogSubtitle.value =
    'Anda yakin ingin menyimpan perubahan pada jadwal ini? Pastikan semua data sudah benar.'
  dialogAction.value = 'Simpan'
  pendingPayload.value = payload
  dialogOpen.value = true
}

const handleDialogConfirm = async () => {
  dialogOpen.value = false

  if (!pendingPayload.value) return

  try {
    const response = await dashboardAPI.updateJadwal(jadwalId.value, pendingPayload.value)
    success('Jadwal berhasil diperbarui!')

    const message = response?.data?.message
    if (message) {
      error(message)
      return
    }

    router.push({ name: 'dashboard' })
  } catch (err: any) {
    const message = err?.response?.data?.message || 'Gagal memperbarui jadwal'
    error(message)
  } finally {
    pendingPayload.value = null
  }
}

const handleDialogCancel = () => {
  dialogOpen.value = false
  pendingPayload.value = null
}

// Handle cancel
const handleCancel = () => {
  router.push({ name: 'dashboard' })
}

// Fetch data on mount
onMounted(() => {
  fetchJadwalData()
})
</script>

<template>
  <div>
    <div class="mb-6 mt-10 sm:mt-10 xl:mt-0">
      <h1 class="text-xl sm:text-3xl font-bold text-black">Edit Jadwal</h1>
      <p class="text-sm sm:text-base text-black">Ubah data jadwal perkuliahan</p>
    </div>

    <!-- Loading state while fetching jadwal data -->
    <div v-if="isLoading" class="bg-white rounded-xl shadow-lg p-8">
      <div class="flex items-center justify-center py-12">
        <div class="text-center">
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-active-blue mx-auto mb-4"
          ></div>
          <p class="text-gray-600">Memuat data jadwal...</p>
        </div>
      </div>
    </div>

    <!-- Form with initial data -->
    <FormJadwal
      v-else-if="jadwalData"
      mode="edit"
      :initial-data="jadwalData"
      @submit="handleSubmit"
      @cancel="handleCancel"
    />

    <ConfirmationDialog
      :isOpen="dialogOpen"
      :title="dialogTitle"
      :subtitle="dialogSubtitle"
      :actionText="dialogAction"
      @confirm="handleCancel"
      @cancel="handleDialogCancel"
    />
  </div>
</template>
