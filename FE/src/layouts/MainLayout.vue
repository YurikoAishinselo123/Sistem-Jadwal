<template>
  <div class="flex flex-row w-screen">
    <aside class="shadow-lg min-h-screen">
      <Sidebar />
    </aside>

    <main class="flex-1 p-10 font-sans">
      <router-view />
    </main>
  </div>
</template>

<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue'
</script>

<style>
/* No special styles needed */
</style>
