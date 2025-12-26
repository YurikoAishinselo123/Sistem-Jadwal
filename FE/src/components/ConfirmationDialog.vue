<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="onCancel">
    <div class="modal-container">
      <div class="icon-container">
        <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" fill="#E53E3E" />
          <path d="M12 8V12" stroke="white" stroke-width="2" stroke-linecap="round" />
          <circle cx="12" cy="16" r="1" fill="white" />
        </svg>
      </div>

      <h2 class="title">{{ title }}</h2>
      <p class="subtitle">{{ subtitle }}</p>

      <div class="button-group">
        <button class="btn btn-cancel" @click="onCancel">Batal</button>
        <button class="btn btn-confirm" @click="onConfirm">
          {{ actionText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'ConfirmationDialog',
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Konfirmasi',
    },
    subtitle: {
      type: String,
      default: 'Apakah Anda yakin ingin melanjutkan?',
    },
    actionText: {
      type: String,
      default: 'Ya',
    },
  },
  emits: ['confirm', 'cancel'],
  methods: {
    onConfirm() {
      this.$emit('confirm')
    },
    onCancel() {
      this.$emit('cancel')
    },
  },
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background: white;
  border-radius: 16px;
  padding: 32px 24px 24px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.icon-container {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.icon {
  width: 64px;
  height: 64px;
}

.title {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 12px 0;
}

.subtitle {
  font-size: 14px;
  color: #4a5568;
  margin: 0 0 24px 0;
  line-height: 1.5;
}

.button-group {
  display: flex;
  gap: 12px;
}

.btn {
  flex: 1;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-cancel {
  background-color: #e2e8f0;
  color: #2d3748;
}

.btn-cancel:hover {
  background-color: #cbd5e0;
}

.btn-confirm {
  background-color: #e53e3e;
  color: white;
}

.btn-confirm:hover {
  background-color: #c53030;
}
</style>
