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

const handleSignup = async () => {
  try {
    const res = await authenticationAPI.signup({
      email: email.value,
      username: username.value,
      password: password.value,
    })

    console.log('Signup success:', res)

    router.push({
      name: 'login',
      params: { message: 'Akun berhasil dibuat! Silakan login.' },
    })
  } catch (error: any) {
    console.error('FULL ERROR: ', error)
    console.error('ERROR RESPONSE: ', error.response)

    alert(error.response?.data?.message || 'Signup failed')
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
        <CustomFormInput v-model="email" label="Email" type="email" placeholder="Masukkan email" />
        <CustomFormInput
          v-model="username"
          label="Username"
          type="text"
          placeholder="Masukkan username"
        />
        <CustomFormInput
          v-model="password"
          label="Password"
          type="password"
          placeholder="Masukkan password"
        />
      </div>
    </template>
  </AuthLayout>
</template>
