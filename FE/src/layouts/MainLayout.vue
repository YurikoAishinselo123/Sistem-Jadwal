<template>
  <div class="flex flex-row min-w-screen">
    <aside class="shadow-lg min-h-screen">
      <Sidebar />
    </aside>

    <main class="flex-1 p-10 font-sans flex flex-col min-h-0">
      <router-view class="flex-1 min-h-0" />
    </main>
  </div>
</template>

<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue'
</script>
