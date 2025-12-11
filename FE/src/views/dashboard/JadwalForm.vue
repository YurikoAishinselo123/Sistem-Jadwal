<template>
  <div class="bg-white rounded-xl shadow-lg p-8">
    <div class="mb-6">
      <h1 class="text-xl sm:text-3xl font-bold text-black">
        {{ mode === 'edit' ? 'Edit Jadwal' : 'Tambah Jadwal' }}
      </h1>
      <p class="text-sm sm:text-lg text-black">
        Isi form di bawah untuk menambahkan jadwal perkuliahan
      </p>
    </div>

    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Periode Tahun Ajaran -->
        <CustomDropdown
          v-model="form.periodeTahunAjaran"
          :options="dropdownOptions.periode"
          label="Periode Tahun Ajaran"
          placeholder="Pilih periode tahun ajaran"
          :required="true"
          :showError="errors.periodeTahunAjaran"
        />

        <!-- Hari -->
        <CustomDropdown
          v-model="form.hari"
          :options="dropdownOptions.hari"
          label="Hari"
          placeholder="Pilih hari"
          :required="true"
          :showError="errors.hari"
        />

        <!-- Jenis Jadwal -->
        <CustomDropdown
          v-model="form.jenisJadwal"
          :options="dropdownOptions.jenisJadwal"
          label="Jenis Jadwal"
          :required="true"
          :showError="errors.jenisJadwal"
        />

        <!-- Mata Kuliah -->
        <CustomDropdown
          v-model="form.mataKuliah"
          :options="dropdownOptions.makul"
          label="Mata Kuliah"
          placeholder="Pilih mata kuliah"
          :required="true"
          :showError="errors.mataKuliah"
        />

        <!-- Waktu Mulai -->
        <CustomDropdown
          v-model="form.waktuMulai"
          :options="availableStartTimes"
          label="Waktu Mulai Perkuliahan"
          placeholder="Pilih waktu mulai"
          :required="true"
          :showError="errors.waktuMulai"
        />

        <!-- Waktu Selesai (display only) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Waktu Selesai Perkuliahan <span class="text-red-500">*</span>
          </label>
          <div
            class="px-4 py-2 border rounded-lg"
            :class="
              !hasEnoughSlots && form.waktuMulai && selectedMakul
                ? 'border-red-500 bg-red-50 text-red-700'
                : 'border-gray-300 bg-gray-50 text-gray-700'
            "
          >
            {{ form.waktuSelesai || 'Otomatis dihitung' }}
          </div>
          <p v-if="selectedMakul && form.waktuMulai" class="text-xs mt-1">
            <span v-if="hasEnoughSlots" class="text-gray-500">
              Sesi {{ selectedSession === 'pagi' ? 'Pagi' : 'Malam' }}:
              {{ selectedMakul.sesi_makul }} slot waktu
            </span>
            <span v-else class="text-red-600 font-medium">
              Tidak cukup slot di sesi {{ selectedSession === 'pagi' ? 'pagi' : 'malam' }}! Butuh
              {{ selectedMakul.sesi_makul }} slot, tersisa {{ availableSlotsInSession }} slot. Pilih
              waktu yang lebih awal.
            </span>
          </p>
        </div>

        <!-- Status -->
        <CustomDropdown
          v-model="form.status"
          :options="dropdownOptions.status"
          label="Status"
          placeholder="Pilih status"
          :required="true"
          :showError="errors.status"
        />

        <!-- Program Studi -->
        <CustomDropdown
          v-model="form.programStudi"
          :options="dropdownOptions.prodi"
          label="Program Studi"
          placeholder="Pilih program studi"
          :required="true"
          :showError="errors.programStudi"
        />

        <!-- Kelas -->
        <CustomDropdown
          v-model="form.kelas"
          :options="dropdownOptions.kelas"
          label="Kelas"
          placeholder="Pilih kelas"
          :required="true"
          :showError="errors.kelas"
        />

        <!-- Dosen 1 -->
        <CustomDropdown
          v-model="form.dosen1"
          :options="dosen1Options"
          label="Dosen 1"
          placeholder="Pilih dosen 1"
          :required="true"
          :showError="errors.dosen1"
          :clearable="true"
        />

        <!-- Dosen 2 -->
        <CustomDropdown
          v-model="form.dosen2"
          :options="dosen2Options"
          label="Dosen 2"
          placeholder="Pilih dosen 2"
          :required="true"
          :showError="errors.dosen2"
          :clearable="true"
        />

        <!-- Laboran -->
        <CustomDropdown
          v-model="form.laboran"
          :options="dropdownOptions.laboran"
          label="Laboran"
          placeholder="Pilih laboran"
          :required="true"
          :showError="errors.laboran"
        />

        <!-- Ruang Kelas (hidden when Online) -->
        <CustomDropdown
          v-if="!isOnline"
          v-model="form.ruangKelas"
          :options="dropdownOptions.ruangan"
          label="Ruang Kelas"
          placeholder="Pilih ruang kelas"
          :required="!isOnline"
          :showError="errors.ruangKelas"
          :clearable="true"
        />
      </div>

      <div class="flex justify-end gap-4 mt-8">
        <button
          type="button"
          @click="handleCancel"
          class="px-8 py-2 font-semibold border-2 border-active-blue text-blue-600 rounded-lg"
        >
          Batal
        </button>
        <button type="submit" class="px-8 py-2 font-semibold bg-active-blue text-white rounded-lg">
          {{ mode === 'edit' ? 'Update' : 'Simpan' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import CustomDropdown from '@/components/CustomDropdown.vue'
import { dashboardAPI } from '@/services/dashboardAPI'
import { useToast } from '@/composables/UseToast'

const props = defineProps({
  mode: { type: String as () => 'create' | 'edit', default: 'create' },
  jadwalId: { type: Number as () => number | null, default: null },
})

const emit = defineEmits(['saved'])

const router = useRouter()
const { success, error } = useToast()

// Loading state
const loading = ref(false)

// Dropdown / API data container
const apiData = ref({
  periode: [] as any[],
  prodi: [] as any[],
  makul: [] as any[],
  dosen: [] as any[],
  laboran: [] as any[],
  ruangan: [] as any[],
  waktu: [] as any[],
})

// Time slots definition (match your tambah view)
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

// Reactive form (display values)
const form = reactive({
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

// selectedIds for API payload
const selectedIds = reactive({
  periodeId: null as number | null,
  prodiId: null as number | null,
  makulId: null as number | null,
  dosen1Id: null as number | null,
  dosen2Id: null as number | null,
  laboranId: null as number | null,
  ruanganId: null as number | null,
})

// simple error flags
const errors = reactive({
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
  laboran: false,
  ruangKelas: false,
})

// UI helpers
const isOnline = computed(() => form.status === 'Online')

// Fill dropdown options computed
const dropdownOptions = computed(() => ({
  periode: apiData.value.periode.map((p: any) => p.periode),
  hari: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
  jenisJadwal: ['Jadwal Semester', 'Jadwal Ujian', 'Jadwal Pengganti'],
  makul: apiData.value.makul.map((m: any) => m.nama_makul),
  status: ['Online', 'Offline'],
  prodi: apiData.value.prodi.map((p: any) => p.nama_prodi),
  kelas: ['A', 'B', 'C', 'D', 'E'],
  laboran: apiData.value.laboran.map((l: any) => l.nama_laboran),
  ruangan: apiData.value.ruangan.map((r: any) => r.nama_ruangan),
}))

// Computed for dosen options that exclude the other selection
const dosen1Options = computed(() =>
  apiData.value.dosen.map((d: any) => d.nama_dosen).filter((n: string) => n !== form.dosen2),
)
const dosen2Options = computed(() =>
  apiData.value.dosen.map((d: any) => d.nama_dosen).filter((n: string) => n !== form.dosen1),
)

// selected makul object (for sesi_makul)
const selectedMakul = computed(() => {
  if (!form.mataKuliah) return null
  return apiData.value.makul.find((m: any) => m.nama_makul === form.mataKuliah) || null
})

// session detection
const selectedSession = computed<'pagi' | 'malam' | null>(() => {
  if (!form.waktuMulai) return null
  if (timeSlots.pagi.includes(form.waktuMulai)) return 'pagi'
  if (timeSlots.malam.includes(form.waktuMulai)) return 'malam'
  return null
})

const availableSlotsInSession = computed(() => {
  if (!form.waktuMulai || !selectedSession.value) return 0
  const sessionTimes = timeSlots[selectedSession.value]
  const startIndex = sessionTimes.indexOf(form.waktuMulai)
  if (startIndex === -1) return 0
  return sessionTimes.length - startIndex
})

const hasEnoughSlots = computed(() => {
  if (!form.waktuMulai || !selectedMakul.value) return true
  return availableSlotsInSession.value >= (selectedMakul.value.sesi_makul ?? 0)
})

function calculateEndTime(startTime: string, sesiMakul: number): string | null {
  if (!startTime || !sesiMakul) return null
  let session: 'pagi' | 'malam' | null = null
  if (timeSlots.pagi.includes(startTime)) session = 'pagi'
  else if (timeSlots.malam.includes(startTime)) session = 'malam'
  if (!session) return null
  const sessionTimes = timeSlots[session]
  const startIndex = sessionTimes.indexOf(startTime)
  if (startIndex === -1) return null
  const endIndex = startIndex + sesiMakul
  if (endIndex < 0 || endIndex >= sessionTimes.length) return null
  return sessionTimes[endIndex] ?? null
}

// Recompute waktuSelesai whenever start or makul changes
watch([() => form.waktuMulai, () => form.mataKuliah], () => {
  if (form.waktuMulai && selectedMakul.value) {
    if (!hasEnoughSlots.value) {
      form.waktuSelesai = ''
      return
    }
    form.waktuSelesai = calculateEndTime(form.waktuMulai, selectedMakul.value.sesi_makul) || ''
  } else {
    form.waktuSelesai = ''
  }
})

// available start times — use the full combined list
const availableStartTimes = computed(() => allTimeSlots)

// Helper to map selected display value to id
function getIdFromValue<T extends { id: number }>(array: T[], value: string, nameKey: keyof T) {
  const item = array.find((i) => String(i[nameKey]) === value)
  return item?.id ?? null
}

// watches to update selectedIds
watch(
  () => form.periodeTahunAjaran,
  (v) => {
    selectedIds.periodeId = getIdFromValue(apiData.value.periode, v, 'periode')
  },
)
watch(
  () => form.programStudi,
  (v) => {
    selectedIds.prodiId = getIdFromValue(apiData.value.prodi, v, 'nama_prodi')
  },
)
watch(
  () => form.mataKuliah,
  (v) => {
    selectedIds.makulId = getIdFromValue(apiData.value.makul, v, 'nama_makul')
  },
)
watch(
  () => form.dosen1,
  (v) => {
    selectedIds.dosen1Id = getIdFromValue(apiData.value.dosen, v, 'nama_dosen')
  },
)
watch(
  () => form.dosen2,
  (v) => {
    selectedIds.dosen2Id = getIdFromValue(apiData.value.dosen, v, 'nama_dosen')
  },
)
watch(
  () => form.laboran,
  (v) => {
    selectedIds.laboranId = getIdFromValue(apiData.value.laboran, v, 'nama_laboran')
  },
)
watch(
  () => form.ruangKelas,
  (v) => {
    selectedIds.ruanganId = getIdFromValue(apiData.value.ruangan, v, 'nama_ruangan')
  },
)

// when status -> online clear ruang
watch(
  () => form.status,
  (v) => {
    if (v === 'Online') {
      form.ruangKelas = ''
      selectedIds.ruanganId = null
      errors.ruangKelas = false
    }
  },
)

// clear error flags on change
Object.keys(form).forEach((k) => {
  watch(
    () => (form as any)[k],
    (nv) => {
      if (nv) (errors as any)[k] = false
    },
  )
})

// fetch dropdowns
async function fetchFormData() {
  loading.value = true
  try {
    const res = await dashboardAPI.getJadwalFormData()
    apiData.value = res.data || apiData.value

    console.log('📦 Dropdown data loaded:', {
      periode: apiData.value.periode.length,
      prodi: apiData.value.prodi.length,
      makul: apiData.value.makul.length,
      dosen: apiData.value.dosen.length,
      laboran: apiData.value.laboran.length,
      ruangan: apiData.value.ruangan.length,
    })
  } catch (err) {
    console.error('Error fetching form data', err)
    error('Gagal memuat data form')
  } finally {
    loading.value = false
  }
}

// fetch detail and prefill for edit mode
async function fetchDetailAndPrefill(id: number) {
  if (!id) return
  loading.value = true
  try {
    const res = await dashboardAPI.getJadwalById(id)
    const data = Array.isArray(res.data) ? res.data[0] : res.data
    if (!data) throw new Error('Data not found')

    console.log('📊 Detail data received:', data)

    // Wait for next tick to ensure dropdowns are rendered
    await nextTick()

    // Map backend fields to form display values
    form.periodeTahunAjaran = data.periode || ''
    form.hari = data.hari_jadwal || ''
    form.jenisJadwal = data.jenis_jadwal || 'Jadwal Semester'
    form.waktuMulai = data.waktu_mulai || ''
    form.waktuSelesai = data.waktu_selesai || ''
    form.mataKuliah = data.nama_makul || ''

    // FIX: Properly map status from API response
    // API returns 1 or '1' for Online, 2 or '2' for Offline
    if (data.status === 1 || data.status === '1') {
      form.status = 'Online'
    } else if (data.status === 2 || data.status === '2') {
      form.status = 'Offline'
    } else {
      form.status = ''
    }

    form.programStudi = data.nama_prodi || ''
    form.kelas = data.kelas || ''
    form.dosen1 = data.nama_dosen_1 || ''
    form.dosen2 = data.nama_dosen_2 || ''
    form.laboran = data.nama_laboran || ''
    form.ruangKelas = data.kode_ruangan || ''

    // Set IDs directly from API response
    selectedIds.periodeId = data.periode_tahun_id ?? null
    selectedIds.prodiId = data.prodi_id ?? null
    selectedIds.makulId = data.makul_id ?? null
    selectedIds.dosen1Id = data.dosen_1 ?? null
    selectedIds.dosen2Id = data.dosen_2 ?? null
    selectedIds.laboranId = data.laboran_id ?? null
    selectedIds.ruanganId = data.ruangan_id ?? null

    console.log('✅ Form values set:', form)
    console.log('🔑 Selected IDs:', selectedIds)
  } catch (err) {
    console.error('Error fetching jadwal detail', err)
    error('Gagal memuat data jadwal')
    router.push({ name: 'dashboard' })
  } finally {
    loading.value = false
  }
}

// initial load
onMounted(async () => {
  // FIRST: Load all dropdown data
  await fetchFormData()

  // THEN: If edit mode, load and prefill the detail
  if (props.mode === 'edit' && props.jadwalId) {
    await fetchDetailAndPrefill(props.jadwalId)
  }
})

// submit handler
async function handleSubmit() {
  // reset errors
  Object.keys(errors).forEach((k) => ((errors as any)[k] = false))

  // required fields validation (skip ruangKelas if online)
  const requiredKeys = Object.keys(errors).filter((k) => {
    if (k === 'ruangKelas' && isOnline.value) return false
    return true
  })

  const missing = requiredKeys.filter((k) => {
    const v = (form as any)[k]
    return v === null || v === undefined || v === ''
  })

  if (missing.length) {
    missing.forEach((k) => ((errors as any)[k] = true))
    error('Harap lengkapi semua field yang wajib diisi!')
    return
  }

  if (!form.waktuSelesai) {
    error('Waktu selesai tidak dapat dihitung. Periksa waktu mulai dan mata kuliah.')
    return
  }

  if (!hasEnoughSlots.value) {
    const sessionName = selectedSession.value === 'pagi' ? 'pagi' : 'malam'
    error(
      `Waktu mulai tidak mencukupi dalam sesi ${sessionName}! Mata kuliah ini membutuhkan ${selectedMakul.value?.sesi_makul} slot waktu, tetapi hanya tersisa ${availableSlotsInSession.value} slot di sesi ${sessionName}.`,
    )
    return
  }

  // prepare payload
  const payload: any = {
    periode_tahun_id: selectedIds.periodeId,
    hari_jadwal: form.hari,
    jenis_jadwal: form.jenisJadwal,
    waktu_mulai: form.waktuMulai,
    waktu_selesai: form.waktuSelesai,
    makul_id: selectedIds.makulId,
    status: form.status === 'Online' ? 1 : 2,
    prodi_id: selectedIds.prodiId,
    kelas: form.kelas,
    dosen_1: selectedIds.dosen1Id,
    dosen_2: selectedIds.dosen2Id,
    laboran_id: selectedIds.laboranId,
    ...(isOnline.value ? {} : { ruangan_id: selectedIds.ruanganId }),
  }

  console.log('📤 Submitting payload:', payload)

  try {
    loading.value = true
    if (props.mode === 'create') {
      const res = await dashboardAPI.createJadwal(payload)
      success('Jadwal berhasil ditambahkan!')
      emit('saved', res.data)
      router.push({ name: 'dashboard' })
    } else {
      // edit
      if (!props.jadwalId) throw new Error('Missing jadwalId for update')
      const res = await dashboardAPI.updateJadwal(props.jadwalId, payload)
      success('Jadwal berhasil diperbarui!')
      emit('saved', res.data)
      router.push({ name: 'dashboard' })
    }
  } catch (err: any) {
    console.error('Submit error', err)
    const msg = err?.response?.data?.message || 'Terjadi kesalahan'
    error(msg)
  } finally {
    loading.value = false
  }
}

function handleCancel() {
  if (confirm('Apakah Anda yakin ingin membatalkan? Data yang diisi akan hilang.')) {
    router.push({ name: 'dashboard' })
  }
}
</script>

<style scoped>
/* Add any custom styles here */
</style>
