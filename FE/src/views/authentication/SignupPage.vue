<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layouts/AuthLayout.vue'
import CustomFormInput from '@/components/CustomFormInput.vue'
import { authenticationAPI } from '@/services/authenticationAPI'

const router = useRouter()
const username = ref('')
const email = ref('')
const password = ref('')

// Refs for form inputs
const emailInput = ref<InstanceType<typeof CustomFormInput> | null>(null)
const usernameInput = ref<InstanceType<typeof CustomFormInput> | null>(null)
const passwordInput = ref<InstanceType<typeof CustomFormInput> | null>(null)

// Track validation errors
const hasEmailError = ref(false)
const hasUsernameError = ref(false)
const hasPasswordError = ref(false)

// API error message
const apiError = ref('')

const handleSignup = async () => {
  // Clear previous API error
  apiError.value = ''

  // Trigger validation on all fields
  const isEmailValid = emailInput.value?.validate() ?? false
  const isUsernameValid = usernameInput.value?.validate() ?? false
  const isPasswordValid = passwordInput.value?.validate() ?? false

  // If any field is invalid, stop submission
  if (!isEmailValid || !isUsernameValid || !isPasswordValid) {
    return
  }

  try {
    const payload = {
      email: email.value,
      name: username.value,
      password: password.value,
    }
    console.log('Sending payload:', payload)
    const res = await authenticationAPI.signup(payload)

    console.log('FULL RESPONSE:', res)
    console.log('RESPONSE DATA:', res.data)

    // Check different possible response structures
    const token = res.data?.token || res.data?.data?.token
    const user = res.data?.user || res.data?.data?.user

    if (token) {
      localStorage.setItem('token', token)
      if (user) {
        localStorage.setItem('user', JSON.stringify(user))
      }
      localStorage.setItem('isAuthenticated', 'true')

      console.log('Registration successful, redirecting to dashboard...')

      // Force redirect to dashboard
      await router.push({ name: 'dashboard' })
    } else {
      console.warn('No token received in response')
      apiError.value = 'Pendaftaran berhasil, silakan login'
      setTimeout(() => {
        router.push({ name: 'login' })
      }, 2000)
    }
  } catch (error: any) {
    console.error('FULL ERROR: ', error)
    console.error('ERROR RESPONSE: ', error.response)
    console.error('ERROR DATA: ', error.response?.data)

    // Handle different types of errors
    if (error.response?.status === 400) {
      apiError.value = error.response?.data?.message || 'Data yang dimasukkan tidak valid'
    } else if (error.response?.status === 422) {
      apiError.value = 'Email sudah terdaftar, Silahkan mendaftar menggunakan email lain'
    } else if (error.response?.status === 500) {
      apiError.value = 'Terjadi kesalahan pada server, silakan coba lagi'
    }
  }
}
</script>

<template>
  <AuthLayout
    title="Daftar Akun Baru"
    subtitle="Daftar akun untuk mengakses sistem"
    buttonText="Daftar"
    footerText="Sudah punya akun?"
    footerLinkText="Masuk di sini"
    footerLinkTo="/login"
    @submit="handleSignup"
  >
    <template #form>
      <div>
        <!-- API Error Message -->
        <div
          v-if="apiError"
          class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"
        >
          {{ apiError }}
        </div>

        <CustomFormInput
          ref="emailInput"
          v-model="email"
          label="Email"
          type="email"
          placeholder="Masukkan email"
          :required="true"
          @validation-error="hasEmailError = $event"
        />
        <CustomFormInput
          ref="usernameInput"
          v-model="username"
          label="Username"
          type="text"
          placeholder="Masukkan username"
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
      </div>
    </template>
  </AuthLayout>
</template>
