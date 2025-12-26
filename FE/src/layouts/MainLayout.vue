<template>
  <div class="flex flex-row min-w-screen">
    <!-- Sidebar (hidden when printing) -->
    <aside class="shadow-lg min-h-screen no-print">
      <CustomSidebar />
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 font-sans flex flex-col min-h-0">
      <router-view class="flex-1 min-h-0" />
    </main>

    <!-- Toast Notification (also hidden when printing) -->
    <ToastNotification
      class="no-print"
      :show="toastState.show"
      :type="toastState.type"
      :message="toastState.message"
      @close="hideToast"
    />
  </div>
</template>

<script setup lang="ts">
import CustomSidebar from '@/components/CustomSidebar.vue'
import ToastNotification from '@/components/ToastNotification.vue'
import { useToast } from '@/composables/UseToast'

const { toastState, hideToast } = useToast()
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }

  aside {
    display: none !important;
  }

  main {
    width: 100% !important;
    padding: 0 !important;
  }

  body {
    margin: 0 !important;
  }
}
</style>
