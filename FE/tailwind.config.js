/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        'soft-blue': '#F4FDFF',
        'hover-blue': '#C5E6FF',
        'active-blue': '#00579B',
        'hover-button': '#0071C9',
        'header-table': '#BDE1FD',
      },

      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [],
}
