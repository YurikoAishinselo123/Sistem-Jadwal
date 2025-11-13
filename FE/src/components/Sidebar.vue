<script lang="ts" setup>
import logo from '../assets/image/Logo.png'
import DashboardIcon from '../assets/image/Dashboard_icon.svg'
import AddIcon from '../assets/image/Add_icon.svg'
import ChartIcon from '../assets/image/Chart_icon.svg'
import DoorIcon from '../assets/image/Door_icon.svg'
</script>

<template>
  <nav class="h-full flex flex-col bg-white w-72 font-sans">
    <div class="mb-2 flex items-center w-full p-4 mt-3">
      <img :src="logo" alt="Logo" class="h-12 w-auto mr-3 ml-1" />

      <div class="flex flex-col leading-tight">
        <span class="text-active-blue text-xl font-bold">Jadwal Kampus</span>
        <span class="text-xs text-black">Sistem Monitoring</span>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <div class="table w-full">
      <a href="/" class="table-row hover:bg-hover-blue transition-colors cursor-pointer rounded-lg">
        <div class="table-cell w-16 align-middle text-center py-6 pl-5">
          <img :src="DashboardIcon" alt="Chart Icon" class="w-6 h-auto mx-auto" />
        </div>

        <div class="table-cell align-middle pl-4 text-md font-medium text-black">
          Dashboard Jadwal
        </div>
      </a>

      <a href="/" class="table-row hover:bg-hover-blue transition-colors cursor-pointer rounded-lg">
        <div class="table-cell w-16 align-middle text-center py-6 pl-5">
          <img :src="AddIcon" alt="Add Icon" class="w-5 h-auto mx-auto" />
        </div>

        <div class="table-cell align-middle pl-4 text-md font-medium text-black">Tambah Jadwal</div>
      </a>

      <a href="/" class="table-row hover:bg-hover-blue transition-colors cursor-pointer rounded-lg">
        <div class="table-cell w-16 align-middle text-center py-6 pl-5">
          <img :src="ChartIcon" alt="Chart Icon" class="w-5 h-auto mx-auto" />
        </div>

        <div class="table-cell align-middle pl-4 text-md font-medium text-black">
          Beban Kerja Dosen
        </div>
      </a>

      <a href="/" class="table-row hover:bg-hover-blue transition-colors cursor-pointer rounded-lg">
        <div class="table-cell w-16 align-middle text-center py-6 pl-5">
          <img :src="DoorIcon" alt="Door Icon" class="w-5 h-auto mx-auto" />
        </div>

        <div class="table-cell align-middle pl-4 text-md font-medium text-black">
          Beban Ruang Kelas
        </div>
      </a>
    </div>
  </nav>
</template>
