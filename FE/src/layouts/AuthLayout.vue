<template>
  <div class="min-h-screen bg-[#F4FDFF] flex items-center justify-center px-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-8">
      <!-- Title -->
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-active-blue mb-2 leading-tight" v-html="title"></h1>
        <p class="text-black text-sm mt-2">{{ subtitle }}</p>
      </div>

      <!-- Success message -->
      <div
        v-if="successMessage"
        class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-5 text-center text-sm"
      >
        {{ successMessage }}
      </div>

      <form @submit.prevent="$emit('submit')">
        <slot name="form"></slot>

        <!-- Submit button -->
        <button
          type="submit"
          class="w-full bg-active-blue hover:bg-[#004884] text-white font-semibold py-3 rounded-lg mt-8 transition"
        >
          {{ buttonText }}
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-6 text-sm text-black">
        {{ footerText }}
        <router-link :to="footerLinkTo" class="text-active-blue font-semibold hover:underline ml-1">
          {{ footerLinkText }}
        </router-link>
      </div>
    </div>
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
import ToastNotification from '@/components/ToastNotification.vue'
import { useToast } from '@/composables/UseToast'

const { toastState, hideToast } = useToast()

defineProps<{
  title: string
  subtitle: string
  buttonText: string
  footerText: string
  footerLinkText: string
  footerLinkTo: string
  successMessage?: string
}>()

defineEmits<{
  submit: []
}>()

defineSlots<{
  form: () => any
}>()
</script>
