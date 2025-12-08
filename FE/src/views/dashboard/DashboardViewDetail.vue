<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { dashboardAPI } from '@/services/dashboardAPI'

const router = useRouter()
const route = useRoute()

const jadwalId = Number(route.params.id)

// Reactive object for UI
const scheduleData = reactive({
  periodeTahunAjaran: '',
  hari: '',
  jenisJadwal: '',
  waktuPerkuliahan: '',
  mataKuliah: '',
  status: '',
  programStudi: '',
  kelas: '',
  dosen1: '',
  dosen2: '',
  laboran: '',
  ruangKelas: '',
})

// Fetch detail by ID
const fetchDetailJadwal = async () => {
  try {
    const res = await dashboardAPI.getJadwalById(jadwalId)
    const item = res.data

    scheduleData.periodeTahunAjaran =
      item?.nama_periode && item?.tahun_periode ? `${item.nama_periode} ${item.tahun_periode}` : '-'

    scheduleData.hari = item?.hari_jadwal || '-'
    scheduleData.jenisJadwal = item?.jenis_jadwal || 'Jadwal Semester'

    scheduleData.waktuPerkuliahan =
      item?.waktu_mulai && item?.waktu_selesai ? `${item.waktu_mulai} - ${item.waktu_selesai}` : '-'

    scheduleData.mataKuliah = item?.nama_makul || '-'
    scheduleData.status = item?.status || '-'
    scheduleData.programStudi = item?.nama_prodi || '-'
    scheduleData.kelas = item?.kelas || '-'

    scheduleData.dosen1 = item?.nama_dosen_1 || '-'
    scheduleData.dosen2 = item?.nama_dosen_2 || '-'
    scheduleData.laboran = item?.nama_laboran || '-'

    scheduleData.ruangKelas = item?.kode_ruangan ? `${item.kode_ruangan}` : '-'
  } catch (err) {
    console.error('Error getting jadwal detail:', err)
    alert('Gagal memuat detail jadwal')
    router.push('/dashboard-jadwal')
  }
}

onMounted(() => {
  if (jadwalId) {
    fetchDetailJadwal()
  } else {
    router.push('/dashboard-jadwal')
  }
})

const handleBack = () => {
  router.push('/dashboard-jadwal')
}

const handleEdit = () => {
  router.push(`/edit-jadwal/${jadwalId}`)
}
</script>
