<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layouts/AuthLayout.vue'
import CustomFormInput from '@/components/CustomFormInput.vue'
import { authenticationAPI } from '@/services/authenticationAPI'

const router = useRouter()
const username = ref('')
const password = ref('')
const successMessage = ref<string | undefined>(undefined)

// Refs for form inputs
const usernameInput = ref<InstanceType<typeof CustomFormInput> | null>(null)
const passwordInput = ref<InstanceType<typeof CustomFormInput> | null>(null)

// Track validation errors
const hasUsernameError = ref(false)
const hasPasswordError = ref(false)

// API error message
const apiError = ref('')

const handleLogin = async () => {
  // Clear previous API error
  apiError.value = ''

  // Trigger validation on both fields
  const isUsernameValid = usernameInput.value?.validate() ?? false
  const isPasswordValid = passwordInput.value?.validate() ?? false

  // If either field is invalid, stop submission
  if (!isUsernameValid || !isPasswordValid) {
    return
  }

  // Actual API login
  try {
    const payload = {
      name: username.value,
      password: password.value,
    }
    console.log('Sending payload:', payload)
    const res = await authenticationAPI.login(payload)

    // Save token or authentication status
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('isAuthenticated', 'true')
    router.push({ name: 'dashboard' })
  } catch (error: any) {
    console.error('FULL LOGIN ERROR:', error)

    // Handle different types of errors
    if (error.response?.status === 401) {
      apiError.value = 'Username atau password salah'
    } else if (error.response?.status === 422) {
      apiError.value = 'Akun tidak ditemukan. Silakan daftar terlebih dahulu'
    } else if (error.response?.status === 500) {
      apiError.value = 'Terjadi kesalahan pada server, silakan coba lagi'
    }
  }
}
</script>

<template>
  <AuthLayout
    title="Sistem<br>Monitoring Jadwal"
    subtitle="Masuk ke akun anda"
    buttonText="Masuk"
    footerText="Belum punya akun?"
    footerLinkText="Daftar di sini"
    footerLinkTo="/signup"
    :successMessage="successMessage"
    @submit="handleLogin"
  >
    <template #form>
      <!-- API Error Message -->
      <div
        v-if="apiError"
        class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"
      >
        {{ apiError }}
      </div>

      <CustomFormInput
        ref="usernameInput"
        v-model="username"
        label="Username"
        placeholder="Masukkan username"
        type="text"
        :required="true"
        @validation-error="hasUsernameError = $event"
      />
      <CustomFormInput
        ref="passwordInput"
        v-model="password"
        label="Password"
        type="password"
        placeholder="Masukkan password"
        :required="true"
        @validation-error="hasPasswordError = $event"
      />
    </template>
  </AuthLayout>
</template>
