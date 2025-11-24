import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import DashboardView from '@/views/DashboardView.vue'
import TambahJadwalView from '@/views/TambahJadwalView.vue'
import BebanKerjaDosenView from '@/views/BebanKerjaDosenView.vue'
import BebanRuangKelasView from '@/views/BebanRuangKelasView.vue'
import MasterDataLayout from '@/layouts/MasterDataLayout.vue'
import MasterDataDosen from '@/views/masterData/MasterDataDosen.vue'
import MasterDataMakul from '@/views/masterData/MasterDataMakul.vue'
import MasterDataLaboran from '@/views/masterData/MasterDataLaboran.vue'
import MasterDataRuangKelas from '@/views/masterData/MasterDataRuangKelas.vue'
import MasterDataWaktuPerkuliahan from '@/views/masterData/MasterDataWaktuPerkuliahan.vue'
import DashboardViewDetail from '@/views/dashboard/DashboardViewDetail.vue'
import MasterDataPeriode from '@/views/masterData/MasterDataPeriode.vue'

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', name: 'dashboard', component: DashboardView },
      { path: 'tambah-jadwal', name: 'tambahJadwal', component: TambahJadwalView },
      { path: 'beban-kerja-dosen', name: 'bebanKerjaDosen', component: BebanKerjaDosenView },
      { path: 'beban-ruang-kelas', name: 'bebanRuangKelas', component: BebanRuangKelasView },
      { path: 'detail', name: 'detail', component: DashboardViewDetail },
    ],
  },

  // Separate Master Data Layout (different from MainLayout)
  {
    path: '/master-data',
    name: 'masterData',
    component: MasterDataLayout,
    redirect: '/master-data/makul',
    children: [
      {
        path: 'makul',
        name: 'masterDataMakul',
        component: MasterDataMakul,
      },
      {
        path: 'dosen',
        name: 'masterDataDosen',
        component: MasterDataDosen,
      },
      {
        path: 'laboran',
        name: 'masterDataLaboran',
        component: MasterDataLaboran,
      },
      {
        path: 'ruang-kelas',
        name: 'masterDataRuangKelas',
        component: MasterDataRuangKelas,
      },
      {
        path: 'waktu-perkuliahan',
        name: 'masterDataWaktuPerkuliahan',
        component: MasterDataWaktuPerkuliahan,
      },
      {
        path: 'tahun-ajaran',
        name: 'masterDataTahunAjaran',
        component: MasterDataPeriode,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
