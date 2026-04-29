<template>
  <div class="space-y-1">
    <div class="flex justify-between items-center">
      <label
        :for="id"
        class="block text-xs font-label font-bold uppercase tracking-wider text-on-surface-variant"
      >
        {{ label }}
      </label>
      <slot name="action" />
    </div>
    <div class="relative">
      <input
        :id="id"
        :name="name"
        :type="inputType"
        :placeholder="placeholder"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="auth-input w-full py-3 pr-10 focus:ring-0 placeholder:text-outline-variant text-on-surface"
      />
      <!-- Toggle visibility for password -->
      <span
        v-if="type === 'password'"
        class="material-symbols-outlined absolute right-0 top-1/2 -translate-y-1/2 text-outline cursor-pointer text-xl select-none"
        @click="toggleVisible"
      >
        {{ showPassword ? 'visibility_off' : 'visibility' }}
      </span>
    </div>
    <!-- Error Message -->
    <p v-if="error" class="text-xs font-label font-medium text-error mt-1">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  id: { type: String, required: true },
  name: { type: String, required: true },
  label: { type: String, required: true },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  error: { type: String, default: '' },
})

defineEmits(['update:modelValue'])

const showPassword = ref(false)

const inputType = computed(() => {
  if (props.type === 'password') return showPassword.value ? 'text' : 'password'
  return props.type
})

function toggleVisible() {
  showPassword.value = !showPassword.value
}
</script>

<style scoped>
.auth-input:focus {
  outline: none;
  border-bottom: 2px solid #570000;
}
.auth-input {
  border: none;
  border-bottom: 1px solid #e2bfb9;
  background-color: transparent;
  transition: all 0.2s ease;
}
</style>