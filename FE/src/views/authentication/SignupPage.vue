<script setup lang="ts">
import { ref, onMounted } from 'vue'
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

    await authenticationAPI.signup(payload)

    router.push({ name: 'login', query: { registered: 'true' } })
  } catch (err: any) {
    console.error(err)

    apiError.value = ''
    if (err.response && err.response.data?.errors) {
      const errors = err.response.data.errors
      apiError.value = Object.values(errors).flat().join('\n')
    } else {
      apiError.value = 'Terjadi kesalahan, silakan coba lagi.'
    }

    // if (err.response?.data?.errors) {
    //   // Flatten backend validation errors
    //   apiError.value = Object.values(err.response.data.errors).flat().join('\n')
    //   error(apiError.value)
    // } else if (err.response?.data?.message) {
    //   apiError.value = err.response.data.message
    //   error(apiError.value)
    // } else {
    //   apiError.value = 'Terjadi kesalahan, silakan coba lagi.'
    //   error(apiError.value)
    // }
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
          class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm whitespace-pre-line"
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
