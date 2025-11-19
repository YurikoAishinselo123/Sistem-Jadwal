<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

import logo from '../assets/image/Logo.png'

// Icon components
import Dashboard_icon from '@/assets/icons/Dashboard_icon.vue'
import Add_Icon from '@/assets/icons/Add_icon.vue'
import Chart_icon from '@/assets/icons/Chart_icon.vue'
import Door_icon from '@/assets/icons/Door_icon.vue'
import Database_icon from '@/assets/icons/Database_icon.vue'

// route + router
const router = useRouter()
const route = useRoute()

// responsive menu
const isOpen = ref(false)
const toggleMenu = () => (isOpen.value = !isOpen.value)

const menuItems = [
  { name: 'Dashboard Jadwal', routeName: 'dashboard', icon: Dashboard_icon },
  { name: 'Tambah Jadwal', routeName: 'tambahJadwal', icon: Add_Icon },
  { name: 'Beban Kerja Dosen', routeName: 'bebanKerjaDosen', icon: Chart_icon },
  { name: 'Beban Ruang Kelas', routeName: 'bebanRuangKelas', icon: Door_icon },
  { name: 'Master Data', routeName: 'masterData', icon: Database_icon },
]

const goTo = (name: string) => {
  isOpen.value = false // close mobile menu on navigation
  router.push({ name })
}
</script>

<template>
  <!-- DESKTOP SIDEBAR -->
  <nav class="hidden xl:flex flex-col h-full bg-white w-64 border-r border-gray-200">
    <!-- Logo -->
    <div class="mb-2 flex items-center w-full p-4 mt-3">
      <img :src="logo" alt="Logo" class="h-10 w-auto mr-3 ml-1" />
      <div class="flex flex-col leading-tight">
        <span class="text-active-blue text-xl font-bold">Jadwal Kampus</span>
        <span class="text-xs text-black">Sistem Monitoring</span>
      </div>
    </div>

    <!-- Menu -->
    <ul class="flex flex-col">
      <table class="w-full text-black border-collapse">
        <tr
          v-for="item in menuItems"
          :key="item.routeName"
          @click="goTo(item.routeName)"
          class="h-14 cursor-pointer transition"
          :class="
            route.name === item.routeName ? 'bg-active-blue text-white' : 'hover:bg-hover-blue'
          "
        >
          <!-- INNER CELL WRAPPER for padding -->
          <td colspan="2" class="py-3 pl-2">
            <div class="grid grid-cols-[60px_1fr] items-center">
              <!-- ICON -->
              <component :is="item.icon" class="w-5 h-5 mx-auto" />

              <!-- TEXT -->
              <span class="text-md font-medium">{{ item.name }}</span>
            </div>
          </td>
        </tr>
      </table>
    </ul>
  </nav>

  <!-- MOBILE NAVBAR -->
  <header
    class="xl:hidden bg-white border-b border-gray-200 shadow-sm w-full fixed top-0 left-0 z-50"
  >
    <div class="flex justify-between items-center px-4 py-3">
      <!-- Left: Logo -->
      <div class="flex items-center space-x-2">
        <img :src="logo" alt="Logo" class="h-10 w-auto" />
        <div class="flex flex-col leading-tight">
          <span class="font-semibold text-active-blue text-lg">Jadwal Kampus</span>
          <span class="font-semibold text-black text-xs">Sistem Monitoring</span>
        </div>
      </div>

      <!-- Right: Burger Button -->
      <button @click="toggleMenu" class="focus:outline-none text-gray-700">
        <svg
          v-if="!isOpen"
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>

        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- MOBILE DROPDOWN MENU -->
    <!-- MOBILE DROPDOWN MENU -->
    <transition name="fade">
      <div v-if="isOpen" class="bg-white shadow-lg border-t border-gray-100 flex flex-col">
        <table class="w-full text-black border-collapse">
          <tr
            v-for="item in menuItems"
            :key="item.routeName"
            @click="goTo(item.routeName)"
            class="cursor-pointer transition"
            :class="
              route.name === item.routeName ? 'bg-active-blue text-white' : 'hover:bg-hover-blue'
            "
          >
            <td colspan="2" class="py-3 px-2">
              <div class="grid grid-cols-[40px_1fr] items-center">
                <!-- Icon -->
                <component :is="item.icon" class="w-5 h-5 mx-auto" />

                <!-- Text -->
                <span class="text-sm font-medium">{{ item.name }}</span>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </transition>
  </header>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
