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

const handleLogin = async () => {
  if (!username.value || !password.value) {
    alert('Username dan password wajib diisi')
    return
  }

  // Temporary dummy login (remove after API ready)
  if (username.value === 'admin' && password.value === 'admin') {
    console.log('Dummy login successful')

    localStorage.setItem('token', 'dummy-token') // placeholder
    localStorage.setItem('isAuthenticated', 'true')

    router.push({ name: 'dashboard' })
    return
  }

  // Actual API login
  try {
    const res = await authenticationAPI.login({
      username: username.value,
      password: password.value,
    })

    console.log('Login berhasil:', res)

    // Save token or authentication status
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('isAuthenticated', 'true')

    router.push({ name: 'dashboard' })
  } catch (error: any) {
    console.error('FULL LOGIN ERROR:', error)
    alert(error.response?.data?.message || 'Login gagal')
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
      <CustomFormInput
        v-model="username"
        label="Username"
        placeholder="Masukkan username"
        type="text"
      />

      <CustomFormInput
        v-model="password"
        label="Password"
        type="password"
        placeholder="Masukkan password"
      />
    </template>
  </AuthLayout>
</template>
