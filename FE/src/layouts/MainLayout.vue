<template>
  <div class="flex h-screen w-screen">
    <aside class="shadow-lg">
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
