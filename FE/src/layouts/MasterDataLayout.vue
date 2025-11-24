<template>
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="shadow-lg">
      <Sidebar />
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <!-- Header - Fixed -->
      <div class="bg-white border-b border-gray-200 px-8 py-6 flex-shrink-0">
        <h1 class="text-3xl font-bold text-gray-900">Master Data</h1>
        <p class="text-gray-600 mt-1">Kelola data master akademik</p>
      </div>

      <!-- Tabs - Fixed -->
      <div class="bg-white border-b border-gray-200 px-8 flex-shrink-0">
        <div class="flex gap-8 overflow-x-auto">
          <router-link
            v-for="tab in tabs"
            :key="tab.id"
            :to="{ name: tab.routeName }"
            class="py-4 px-2 font-medium text-sm border-b-2 transition whitespace-nowrap"
            :class="
              isActiveTab(tab.routeName)
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-600 hover:text-gray-900'
            "
          >
            {{ tab.label }}
          </router-link>
        </div>
      </div>

      <!-- Content Area - Router View -->
      <div class="flex-1 bg-soft-blue overflow-hidden">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'

const route = useRoute()

const tabs = [
  { id: 'makul', label: 'Daftar Makul', routeName: 'masterDataMakul' },
  { id: 'dosen', label: 'Daftar Dosen', routeName: 'masterDataDosen' },
  { id: 'laboran', label: 'Daftar Laboran', routeName: 'masterDataLaboran' },
  { id: 'ruangKelas', label: 'Daftar Ruang Kelas', routeName: 'masterDataRuangKelas' },
  {
    id: 'waktuPerkuliahan',
    label: 'Daftar Waktu Perkuliahan',
    routeName: 'masterDataWaktuPerkuliahan',
  },
  { id: 'tahunAjaran', label: 'Daftar Periode Tahun Ajaran', routeName: 'masterDataTahunAjaran' },
]

const isActiveTab = (routeName: string) => {
  return route.name === routeName
}
</script>
