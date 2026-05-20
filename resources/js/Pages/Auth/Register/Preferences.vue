<template>
  <div class="bg-background text-on-surface min-h-screen flex flex-col">

    <main class="flex-1 flex flex-col items-center justify-center px-4 md:px-6 py-12 w-full">

      <div class="w-full max-w-2xl flex flex-col gap-6 md:gap-8">

        <!-- Progress Indicator -->
        <div>
          <div class="flex justify-between items-end mb-2 md:mb-3">
            <span class="font-label text-[10px] md:text-xs uppercase tracking-widest text-on-surface-variant font-bold">
              Progress
            </span>
            <span class="font-label text-[10px] md:text-xs uppercase tracking-widest text-on-surface-variant font-bold">
              Step {{ currentStep }} of {{ totalSteps }}
            </span>
          </div>
          <div class="h-1 w-full bg-surface-container-high rounded-full overflow-hidden">
            <div
              class="h-full bg-primary rounded-full transition-all duration-500"
              :style="{ width: progressPercent + '%' }"
            ></div>
          </div>
        </div>

        <!-- Section Heading -->
        <div>
          <h1 class="header-font text-3xl md:text-4xl font-bold tracking-tight text-primary mb-2 md:mb-3 leading-tight">
            Apa Preferensi Rasa Favoritmu?
          </h1>
          <p class="text-on-surface-variant text-base md:text-lg leading-relaxed max-w-md">
            Pilih Preferensi rasa yang paling kamu suka untuk rekomendasi menu yang lebih personal.
          </p>
        </div>

        <!-- Category Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
          <div
            v-for="pref in preferences"
            :key="pref.key"
            @click="togglePreferences(pref.key)"
            :class="[
              'flex flex-col items-start justify-between p-5 md:p-6 h-28 md:h-32 rounded-3xl transition-all duration-300 cursor-pointer select-none',
              selectedPreferences.includes(pref.key)
                ? 'bg-primary text-white scale-[1.05] shadow-lg shadow-primary/20'
                : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high shadow-[0_12px_32px_rgba(128,0,0,0.04)]'
            ]"
          >
            <span
              class="material-symbols-outlined mb-auto text-xl md:text-2xl"
              :style="selectedPreferences.includes(pref.key) ? 'font-variation-settings: \'FILL\' 1' : ''"
            >{{ pref.icon }}</span>
            <span
              :class="[
                'font-bold text-base md:text-lg tracking-tight',
                selectedPreferences.includes(pref.key) ? 'text-white' : 'text-on-surface'
              ]"
            >
              {{ pref.label }}
            </span>
          </div>
        </div>

        <!-- Selected count hint -->
        <p
          v-if="selectedPreferences.length > 0"
          class="text-xs md:text-sm text-secondary font-semibold text-center -mt-2"
        >
          {{ selectedPreferences.length }} Preferensi Rasa dipilih
        </p>

        <!-- Continue Button -->
        <button
          @click="goNext"
          :disabled="!canContinue || form.processing"
          :class="[
            'w-full flex items-center justify-center gap-2 rounded-full px-8 py-4 font-semibold text-sm uppercase tracking-widest transition-all duration-300 active:scale-95',
            canContinue && !form.processing
              ? 'bg-gradient-to-br from-primary to-primary-container text-white hover:scale-[1.02] shadow-lg shadow-primary/20'
              : 'bg-surface-container-high text-outline opacity-60 cursor-not-allowed'
          ]"
        >
          <span v-if="form.processing" class="material-symbols-outlined animate-spin">progress_activity</span>
          <span v-else>Continue</span>
          <span v-if="!form.processing" class="material-symbols-outlined text-lg">arrow_forward_ios</span>
        </button>

      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const currentStep = ref(1)
const totalSteps = 3
const progressPercent = computed(() => (currentStep.value / totalSteps) * 100)

const preferences = [
  { key: 'pedas',        label: 'Pedas',          icon: 'restaurant'   },
  { key: 'asin',        label: 'Asin',          icon: 'restaurant'   },
  { key: 'manis',       label: 'Manis',          icon: 'local_bar'    },
  { key: 'asam',       label: 'Asam',          icon: 'local_bar'    },
  { key: 'gurih',       label: 'Gurih',          icon: 'local_bar'    },
]

const selectedPreferences = ref([])
const form = useForm({
  tastes: []
})

function togglePreferences(key) {
  const idx = selectedPreferences.value.indexOf(key)
  if (idx === -1) selectedPreferences.value.push(key)
  else selectedPreferences.value.splice(idx, 1)
}

const canContinue = computed(() => selectedPreferences.value.length > 0)

function goNext() {
  if (!canContinue.value) return
  form.tastes = selectedPreferences.value
  form.post(route('register.store.preferences'))
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  background-color: #faf9f6;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.header-font,
.font-display,
.font-headline {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  display: inline-block;
  line-height: 1;
}
</style>