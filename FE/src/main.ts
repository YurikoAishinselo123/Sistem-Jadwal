import './assets/main.css'
import './assets/tailwind.css'
import '@fontsource/inter/400.css' // normal
import '@fontsource/inter/500.css' // medium
import '@fontsource/inter/600.css' // semi-bold
import '@fontsource/inter/700.css' // bold

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
