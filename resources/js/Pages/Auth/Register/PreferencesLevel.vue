<template>
  <div class="bg-background min-h-screen flex flex-col">

    <!-- Main: vertically + horizontally centered -->
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
          <h1 class="font-display font-bold tracking-tight text-3xl md:text-4xl text-primary mb-2 md:mb-3 leading-tight">
            Pilih Level Preferensimu
          </h1>
          <p class="text-on-surface-variant text-base md:text-lg leading-relaxed">
            Tentukan seberapa kuat selera makanmu untuk setiap kategori yang kamu pilih sebelumnya.
          </p>
        </div>

        <!-- Dynamic Preference Options Card -->
        <!-- Looping berdasarkan tastes yang dipilih di step sebelumnya -->
        <div v-if="tastes.length === 0" class="text-center p-6 bg-surface-container-low rounded-xl">
            <p class="text-on-surface-variant font-bold">Belum ada preferensi yang dipilih dari langkah sebelumnya.</p>
        </div>

        <section 
          v-for="tasteKey in tastes" 
          :key="tasteKey"
          class="bg-surface-container-low p-5 md:p-8 rounded-xl w-full"
        >
          <h2 class="font-label text-[10px] md:text-xs uppercase tracking-[0.2em] font-extrabold text-secondary mb-4 md:mb-6">
            Level {{ levelConfig[tasteKey]?.label || tasteKey }}
          </h2>
          
          <div class="space-y-3 md:space-y-4">
            <div
              v-for="level in levelConfig[tasteKey]?.levels"
              :key="level.key"
              @click="selectLevel(tasteKey, level.key)"
              :class="[
                'flex items-center justify-between p-3 md:p-4 bg-surface-container-lowest rounded-xl transition-all cursor-pointer select-none',
                selectedLevel[tasteKey] === level.key
                  ? 'shadow-[0_8px_24px_rgba(128,0,0,0.08)] ring-1 ring-primary/10'
                  : 'hover:shadow-[0_8px_24px_rgba(128,0,0,0.04)]'
              ]"
            >
              <!-- Left: Icon + Label -->
              <div class="flex items-center gap-3 md:gap-4">
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed-variant flex-shrink-0">
                  <span class="material-symbols-outlined text-lg md:text-xl">{{ levelConfig[tasteKey]?.icon }}</span>
                </div>
                <div>
                  <p class="font-label font-bold text-on-surface text-sm md:text-base">{{ level.label }}</p>
                  <p class="text-[10px] md:text-xs text-on-surface-variant mt-0.5">{{ level.desc }}</p>
                </div>
              </div>

              <!-- Right: Radio Indicator -->
              <div
                :class="[
                  'w-5 h-5 md:w-6 md:h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200',
                  selectedLevel[tasteKey] === level.key ? 'border-primary' : 'border-outline-variant'
                ]"
              >
                <div
                  :class="[
                    'w-2.5 h-2.5 md:w-3 md:h-3 rounded-full bg-primary transition-all duration-200',
                    selectedLevel[tasteKey] === level.key ? 'opacity-100 scale-100' : 'opacity-0 scale-50'
                  ]"
                ></div>
              </div>
            </div>
          </div>
        </section>

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
          <span v-else>Selesaikan Registrasi</span>
          <span v-if="!form.processing" class="material-symbols-outlined text-lg">check_circle</span>
        </button>

      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
  tastes: {
    type: /** @type {import('vue').PropType<string[]>} */ (Array),
    default: () => ['pedas', 'manis', 'asin', 'asam', 'pahit', 'gurih']
  }
})

const currentStep = ref(3)
const totalSteps = 3
const progressPercent = computed(() => (currentStep.value / totalSteps) * 100)

/** @type {import('vue').Ref<Record<string, string>>} */
const selectedLevel = ref({})

/** @type {Record<string, { label: string, icon: string, levels: { key: string, label: string, desc: string }[] }>} */
const levelConfig = {
  pedas: { 
    label: 'Pedas', icon: 'local_fire_department', 
    levels: [
      { key: 'pedas_rendah', label: 'Sedikit Pedas', desc: 'Rasa pedas yang sangat ringan' },
      { key: 'pedas_sedang', label: 'Pedas', desc: 'Pedas standar yang nikmat' },
      { key: 'pedas_tinggi', label: 'Sangat Pedas', desc: 'Level menantang dan ekstra pedas' }
    ]
  },
  asin: { 
    label: 'Asin', icon: 'water_drop', 
    levels: [
      { key: 'asin_rendah', label: 'Sedikit Asin', desc: 'Kurangi garam (less salt)' },
      { key: 'asin_sedang', label: 'Asin', desc: 'Gurih garam standar' },
      { key: 'asin_tinggi', label: 'Sangat Asin', desc: 'Rasa asin yang kuat dan pekat' }
    ]
  },
  manis: { 
    label: 'Manis', icon: 'cake', 
    levels: [
      { key: 'manis_rendah', label: 'Sedikit Manis', desc: 'Manis ringan (less sugar)' },
      { key: 'manis_sedang', label: 'Manis', desc: 'Kemanisan standar' },
      { key: 'manis_tinggi', label: 'Sangat Manis', desc: 'Sangat manis dan legit (extra sugar)' }
    ]
  },
  asam: { 
    label: 'Asam', icon: 'lemon', 
    levels: [
      { key: 'asam_rendah', label: 'Sedikit Asam', desc: 'Sensasi asam yang tipis' },
      { key: 'asam_sedang', label: 'Asam', desc: 'Asam segar standar' },
      { key: 'asam_tinggi', label: 'Sangat Asam', desc: 'Rasa asam yang mendominasi' }
    ]
  },
  gurih: { 
    label: 'Gurih', icon: 'ramen_dining', 
    levels: [
      { key: 'gurih_rendah', label: 'Sedikit Gurih', desc: 'Kaldu/bumbu yang ringan' },
      { key: 'gurih_sedang', label: 'Gurih', desc: 'Gurih standar khas nusantara' },
      { key: 'gurih_tinggi', label: 'Sangat Gurih', desc: 'Rasa umami yang sangat kuat' }
    ]
  }
}


const form = useForm({
  level: []
})


function selectLevel(tasteKey, levelKey) {
  selectedLevel.value[tasteKey] = levelKey
}

const canContinue = computed(() => {
  return props.tastes.length > 0 && Object.keys(selectedLevel.value).length === props.tastes.length
})

function goNext() {
  if (!canContinue.value) return
  form.level =  Object.values(selectedLevel.value)
  form.post(route('register.store'))
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  background-color: #faf9f6;
  font-family: 'Plus Jakarta Sans', sans-serif;
  color: #1a1c1a;
}

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