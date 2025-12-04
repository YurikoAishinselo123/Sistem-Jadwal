<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center modal-wrapper"
  >
    <div
      class="bg-white rounded-lg flex flex-col print-no-shadow"
      style="width: 90%; max-width: 1400px; height: 90%"
    >
      <!-- Preview Header -->
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 no-print">
        <h3 class="text-lg font-semibold text-gray-800">Print Preview</h3>
        <p class="text-sm text-gray-600 mt-1">Review before printing</p>
      </div>

      <!-- Printable Area -->
      <div class="flex-1 overflow-auto bg-gray-100 p-6">
        <div class="print-container">
          <!-- Title Section -->
          <div class="title-section">
            <h1 class="title">{{ title }}</h1>
            <div class="subtitle">
              <div>Filter: {{ getActiveFilters() }}</div>
              <div class="print-time">Dicetak: {{ getCurrentDateTime() }}</div>
            </div>
          </div>

          <!-- Table -->
          <table class="print-table">
            <thead>
              <tr>
                <th
                  v-for="col in columns"
                  :key="col.key"
                  class="table-header"
                  :class="{ 'text-center': col.key === 'ruang' }"
                >
                  {{ col.label }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, i) in data" :key="i" class="table-row">
                <td
                  v-for="col in columns"
                  :key="col.key"
                  class="table-cell"
                  :class="{ 'text-center': col.key === 'ruang' }"
                  v-html="String(row[col.key] ?? '-').replace(/\n/g, '<br>')"
                ></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 no-print">
        <button
          class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors"
          @click="$emit('close')"
        >
          Close
        </button>
        <button
          class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
          @click="triggerPrint"
        >
          Print
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  show: boolean
  title: string
  data: any[]
  columns: { key: string; label: string }[]
  filters?: {
    periodeTahunAjaran?: string
    hari?: string
    programStudi?: string
    mataKuliah?: string
    jenisJadwal?: string
    laboran?: string
    waktuPerkuliahan?: string
  }
}>()

const triggerPrint = () => window.print()

// Format active filters for display
const getActiveFilters = () => {
  if (!props.filters) return 'Semua Data'

  const activeFilters: string[] = []

  if (props.filters.periodeTahunAjaran)
    activeFilters.push(`Periode - ${props.filters.periodeTahunAjaran}`)
  if (props.filters.hari) activeFilters.push(`Hari - ${props.filters.hari}`)
  if (props.filters.programStudi) activeFilters.push(`Prodi - ${props.filters.programStudi}`)
  if (props.filters.mataKuliah) activeFilters.push(`Mata Kuliah - ${props.filters.mataKuliah}`)
  if (props.filters.jenisJadwal) activeFilters.push(`Jenis Jadwal - ${props.filters.jenisJadwal}`)
  if (props.filters.laboran) activeFilters.push(`Laboran: ${props.filters.laboran}`)
  if (props.filters.waktuPerkuliahan) activeFilters.push(`Waktu: ${props.filters.waktuPerkuliahan}`)
  return activeFilters.length > 0 ? activeFilters.join(' || ') : 'Semua Data'
}

const getCurrentDateTime = () => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<style scoped>
/* =======================
   NORMAL VIEW
======================= */

.print-container {
  width: 210mm;
  padding: 15mm;
  background: white;
  margin: 0 auto;
}

.title-section {
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #e5e7eb;
}

.title {
  font-size: 24px;
  font-weight: bold;
  color: #111827;
}

.subtitle {
  font-size: 12px;
  color: #6b7280;
  margin-top: 8px;
}

.print-time {
  margin-top: 4px;
  font-size: 11px;
  color: #9ca3af;
}

.print-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
  color: #1f2937;
}

.print-table th,
.print-table td {
  padding: 8px;
  border: 1px solid #e5e7eb;
  text-align: left;
  vertical-align: top;
}

.text-center {
  text-align: center !important;
}

.print-table th {
  background-color: #f9fafb;
  font-weight: 600;
}

/* =======================
   PRINT STYLE
======================= */

@media print {
  /* Hide modal wrapper (backdrop and outer container) */
  .modal-wrapper {
    background: none !important;
    position: static !important;
  }

  /* Hide elements with no-print class */
  .no-print {
    display: none !important;
  }

  /* Reset modal styling for print */
  .modal-wrapper > div {
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
    box-shadow: none !important;
    border-radius: 0 !important;
  }

  /* Make printable area full width */
  .flex-1 {
    padding: 0 !important;
    background: white !important;
  }

  .print-container {
    position: static;
    width: 100%;
    padding: 5mm;
    margin: 0;
    box-shadow: none;
  }

  /* Remove background colors for print */
  .bg-gray-100,
  .bg-gray-50 {
    background: white !important;
  }

  /* Restore table behavior */
  table {
    display: table !important;
    page-break-inside: auto;
  }

  thead {
    display: table-header-group !important;
  }

  tbody {
    display: table-row-group !important;
  }

  tr {
    display: table-row !important;
    page-break-inside: avoid;
    page-break-after: auto;
  }

  td,
  th {
    display: table-cell !important;
  }

  /* Ensure table borders are visible in print */
  .print-table th,
  .print-table td {
    border: 1px solid #000 !important;
  }

  .print-table th {
    background-color: #f3f4f6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
}
</style>
