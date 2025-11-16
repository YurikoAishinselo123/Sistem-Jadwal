<script setup lang="ts">
import { ref } from 'vue'
import logo from '../assets/image/Logo.png'
import DashboardIcon from '../assets/image/Dashboard_icon.svg'
import AddIcon from '../assets/image/Add_icon.svg'
import ChartIcon from '../assets/image/Chart_icon.svg'
import DoorIcon from '../assets/image/Door_icon.svg'
import MasterDataIcon from '../assets/image/Data_icon.svg'

const isOpen = ref(false)
const toggleMenu = () => (isOpen.value = !isOpen.value)
</script>

<template>
  <!-- Desktop Sidebar -->
  <nav class="hidden xl:flex flex-col h-full bg-white w-64 font-sans border-r border-gray-200">
    <!-- Logo Section -->
    <div class="mb-2 flex items-center w-full p-4 mt-3">
      <img :src="logo" alt="Logo" class="h-10 w-auto mr-3 ml-1" />
      <div class="flex flex-col leading-tight">
        <span class="text-active-blue text-xl font-bold">Jadwal Kampus</span>
        <span class="text-xs text-black">Sistem Monitoring</span>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <div class="flex flex-col space-y-1">
      <a
        href="/"
        class="grid grid-cols-[32px_1fr] items-center gap-3 py-4 px-6 hover:bg-hover-blue"
      >
        <img :src="DashboardIcon" class="w-5 mx-auto" />
        <span class="text-md font-medium text-black">Dashboard Jadwal</span>
      </a>

      <a
        href="/"
        class="grid grid-cols-[32px_1fr] items-center gap-3 py-4 px-6 hover:bg-hover-blue"
      >
        <img :src="AddIcon" class="w-5 mx-auto" />
        <span class="text-md font-medium text-black">Tambah Jadwal</span>
      </a>

      <a
        href="/"
        class="grid grid-cols-[32px_1fr] items-center gap-3 py-4 px-6 hover:bg-hover-blue"
      >
        <img :src="ChartIcon" class="w-4 mx-auto" />
        <span class="text-md font-medium text-black">Beban Kerja Dosen</span>
      </a>

      <a
        href="/"
        class="grid grid-cols-[32px_1fr] items-center gap-3 py-4 px-6 hover:bg-hover-blue"
      >
        <img :src="DoorIcon" class="w-4 mx-auto" />
        <span class="text-md font-medium text-black">Beban Ruang Kelas</span>
      </a>

      <a
        href="/"
        class="grid grid-cols-[32px_1fr] items-center gap-3 py-4 px-6 hover:bg-hover-blue"
      >
        <img :src="MasterDataIcon" class="w-5 mx-auto" />
        <span class="text-md font-medium text-black">Master Data</span>
      </a>
    </div>
  </nav>

  <!-- Mobile Navbar -->
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

      <!-- Right: Burger Icon -->
      <button @click="toggleMenu" class="focus:outline-none">
        <svg
          v-if="!isOpen"
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 text-gray-700"
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
          class="h-6 w-6 text-gray-700"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Dropdown Menu (when open) -->
    <transition name="fade">
      <div
        v-if="isOpen"
        class="bg-white shadow-lg border-t border-gray-100 flex flex-col p-3 space-y-2"
      >
        <a href="/" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-hover-blue">
          <img :src="DashboardIcon" alt="Dashboard Icon" class="w-5 h-auto" />
          <span class="text-sm font-medium text-black">Dashboard Jadwal</span>
        </a>
        <a href="/" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-hover-blue">
          <img :src="AddIcon" alt="Add Icon" class="w-5 h-auto" />
          <span class="text-sm font-medium text-black">Tambah Jadwal</span>
        </a>
        <a href="/" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-hover-blue">
          <img :src="ChartIcon" alt="Chart Icon" class="w-5 h-auto" />
          <span class="text-sm font-medium text-black">Beban Kerja Dosen</span>
        </a>
        <a href="/" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-hover-blue">
          <img :src="DoorIcon" alt="Door Icon" class="w-5 h-auto" />
          <span class="text-sm font-medium text-black">Beban Ruang Kelas</span>
        </a>
        <a href="/" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-hover-blue">
          <img :src="MasterDataIcon" alt="Master Data Icon" class="w-5 h-auto" />
          <span class="text-sm font-medium text-black">Master Data</span>
        </a>
      </div>
    </transition>
  </header>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
