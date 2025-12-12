import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
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
import tes from '@/views/tesView.vue'
// import MasterDataWaktuPerkuliahan from '@/views/masterData/MasterDataWaktuPerkuliahan.vue'
import DashboardViewDetail from '@/views/dashboard/DashboardViewDetail.vue'
import JadwalForm from '@/views/dashboard/JadwalForm.vue'
import MasterDataPeriode from '@/views/masterData/MasterDataPeriode.vue'
import LoginPage from '@/views/authentication/LoginPage.vue'
import SignupPage from '@/views/authentication/SignupPage.vue'
import MasterDataProdi from '@/views/masterData/MasterDataProdi.vue'

const routes = [
  // Auth routes (no layout)
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    props: (route: RouteLocationNormalized) => ({ successMessage: route.params.message }),
    meta: { requiresGuest: true },
  },
  {
    path: '/signup',
    name: 'signup',
    component: SignupPage,
    meta: { requiresGuest: true },
  },

  // Main app routes (with MainLayout)
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: DashboardView,
      },
      {
        path: '/dashboard/detail/:id',
        name: 'DetailJadwal',
        component: DashboardViewDetail,
      },
      {
        path: '/jadwal/tambah',
        name: 'tambahJadwal',
        component: TambahJadwalView,
        props: { mode: 'add' },
      },
      {
        path: '/tes',
        name: 'tes',
        component: tes,
      },
      {
        path: '/jadwal',
        name: 'jadwal',
        component: JadwalForm,
      },
      {
        path: '/jadwal/edit/:id',
        name: 'EditJadwal',
        component: JadwalForm,
        props: (route: RouteLocationNormalized) => ({
          mode: 'edit',
          id: Number(route.params.id),
        }),
      },
      {
        path: 'beban-kerja-dosen',
        name: 'bebanKerjaDosen',
        component: BebanKerjaDosenView,
      },
      {
        path: 'beban-ruang-kelas',
        name: 'bebanRuangKelas',
        component: BebanRuangKelasView,
      },
    ],
  },

  // Master data routes (with MasterDataLayout)
  {
    path: '/master-data',
    name: 'masterData',
    component: MasterDataLayout,
    redirect: '/master-data/makul',
    meta: { requiresAuth: true },
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
        path: 'prodi',
        name: 'masterDataProdi',
        component: MasterDataProdi,
      },
      {
        path: 'ruang-kelas',
        name: 'masterDataRuangKelas',
        component: MasterDataRuangKelas,
      },
      // {
      //   path: 'waktu-perkuliahan',
      //   name: 'masterDataWaktuPerkuliahan',
      //   component: MasterDataWaktuPerkuliahan,
      // },
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

// Navigation guard for authentication
router.beforeEach((to: RouteLocationNormalized, from: RouteLocationNormalized, next) => {
  // Check if user is authenticated (you can modify this logic based on your auth system)
  const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true'

  // If route requires authentication and user is not authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' })
  }
  // If route is for guests only (login/signup) and user is authenticated
  else if (to.meta.requiresGuest && isAuthenticated) {
    next({ name: 'dashboard' })
  }
  // Otherwise, proceed normally
  else {
    next()
  }
})

export default router
