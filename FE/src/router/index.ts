import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import DashboardView from '@/views/DashboardView.vue'
import TambahJadwalView from '@/views/TambahJadwalView.vue'
import BebanKerjaDosenView from '@/views/BebanKerjaDosenView.vue'
import BebanRuangKelasView from '@/views/BebanRuangKelasView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: MainLayout,
      children: [
        { path: '', name: 'dashboard', component: DashboardView },
        { path: 'tambah-jadwal', name: 'tambahJadwal', component: TambahJadwalView },
        { path: 'beban-kerja-dosen', name: 'bebanKerjaDosen', component: BebanKerjaDosenView },
      ],
    },
  ],
})

export default router
