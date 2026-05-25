<template>
  <div class="app-wrapper">

    <!-- Top App Bar -->
    <header class="top-bar">
      <div class="top-bar__inner">
        <span class="brand">The Epicurean</span>
        <div class="top-bar__right">
          <span class="step-label">Step {{ currentStep }} of {{ totalSteps }}</span>
          <button class="help-btn" aria-label="Bantuan" @click="showHelp = !showHelp">
            <span class="material-symbols-outlined">help_outline</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Progress Dots -->
      <div class="progress-dots">
        <span
          v-for="i in totalSteps"
          :key="i"
          class="progress-dot"
          :class="i === currentStep ? 'progress-dot--active' : 'progress-dot--done'"
        ></span>
      </div>

      <!-- Hero -->
      <section class="hero">
        <h1 class="hero__title">Menyesuaikan dengan Suasana</h1>
        <p class="hero__sub">
          Izinkan kami mengurasi hidangan yang sempurna berdasarkan kondisi cuaca dan waktu favorit Anda untuk bersantap.
        </p>
      </section>

      <!-- Preferences Grid -->
      <div class="prefs-grid">

        <!-- Weather Context -->
        <div class="pref-block pref-block--full">
          <div class="block-heading">
            <span class="material-symbols-outlined icon-secondary">thermostat</span>
            <h2 class="block-title">Respons Cuaca</h2>
          </div>
          <div class="weather-grid">
            <div
              v-for="weather in weatherOptions"
              :key="weather.id"
              class="weather-card"
              :class="{ 'weather-card--on': weather.enabled }"
              @click="weather.enabled = !weather.enabled"
            >
              <div class="weather-card__top">
                <span
                  class="material-symbols-outlined weather-icon"
                  :class="{ 'weather-icon--active': weather.enabled }"
                >{{ weather.icon }}</span>
                <!-- Toggle Switch -->
                <div class="toggle" :class="{ 'toggle--on': weather.enabled }">
                  <div class="toggle__thumb" :class="{ 'toggle__thumb--on': weather.enabled }"></div>
                </div>
              </div>
              <div>
                <h3 class="weather-card__name">{{ weather.name }}</h3>
                <p class="weather-card__desc">{{ weather.desc }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Meal Times -->
        <div class="pref-block pref-block--full">
          <div class="block-heading">
            <span class="material-symbols-outlined icon-secondary">schedule</span>
            <h2 class="block-title">Waktu Makan Favorit</h2>
          </div>
          <div class="meal-pills">
            <button
              v-for="meal in mealTimes"
              :key="meal.id"
              class="meal-pill"
              :class="{ 'meal-pill--active': selectedMeals.includes(meal.id) }"
              @click="toggleMeal(meal.id)"
            >
              <span
                class="material-symbols-outlined"
                :style="selectedMeals.includes(meal.id)
                  ? 'font-variation-settings:\'FILL\' 1'
                  : ''"
              >{{ meal.icon }}</span>
              <span>{{ meal.label }}</span>
            </button>
          </div>
        </div>

        <!-- Atmosphere Image -->
        <div class="pref-block pref-block--full atmosphere-wrap">
          <img
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBrxiHHhQlTfEYYNdi8LcH2lGb_w0YkEbymVDaKrgNSAUabZtrOFZ-LBtiHf22WFuA4Z1Vb6lGtpYXg_OnA9-YtRFmsBkXYvURrrcYwVixprtfcoow2YYwhLLxBiq6egUQFJVozWUyArm0OV9dqCylL1SMvD2IK8p-BElAN0_WXWKqlKzmxoMijvSOR0QJc4Pka5RkfjeZ088Ct0Pb-xy0QsIXuHSnPyuLkDU6j6jYcIAH6I0FEGcICelczQS7S3i86-27x3gyqwQwb"
            alt="Dining atmosphere"
            class="atmosphere-img"
            :class="{ 'atmosphere-img--hovered': imgHovered }"
            @mouseenter="imgHovered = true"
            @mouseleave="imgHovered = false"
          />
          <div class="atmosphere-overlay"></div>
        </div>

      </div>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <div class="bottom-nav__inner">
        <button class="btn-back" @click="handleBack">
          <span class="material-symbols-outlined icon-sm">arrow_back_ios</span>
          Back
        </button>
        <button
          class="btn-continue"
          :disabled="selectedMeals.length === 0"
          @click="handleContinue"
        >
          Continue
          <span class="material-symbols-outlined icon-sm">arrow_forward_ios</span>
        </button>
      </div>
    </nav>

    <!-- Help Tooltip -->
    <transition name="fade">
      <div v-if="showHelp" class="help-toast" @click="showHelp = false">
        <span class="material-symbols-outlined">info</span>
        Preferensi dapat diubah kapan saja di halaman Pengaturan.
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref } from 'vue'

// ── Step / Progress ───────────────────────────────────────────
const currentStep = ref(3)
const totalSteps  = ref(3)

// ── Weather Options ───────────────────────────────────────────
const weatherOptions = ref([
  {
    id:      'hot',
    name:    'Saat Terik',
    desc:    'Prioritaskan minuman dingin & salad segar',
    icon:    'light_mode',
    enabled: true,
  },
  {
    id:      'rainy',
    name:    'Saat Hujan',
    desc:    'Saran sup hangat & kopi artisan',
    icon:    'rainy',
    enabled: true,
  },
])

// ── Meal Times ────────────────────────────────────────────────
const mealTimes = ref([
  { id: 'morning', label: 'Morning', icon: 'wb_twilight' },
  { id: 'lunch',   label: 'Lunch',   icon: 'restaurant'  },
  { id: 'dinner',  label: 'Dinner',  icon: 'dark_mode'   },
])
const selectedMeals = ref(['lunch'])

function toggleMeal(id) {
  const idx = selectedMeals.value.indexOf(id)
  if (idx === -1) selectedMeals.value.push(id)
  else            selectedMeals.value.splice(idx, 1)
}

// ── Image hover state ─────────────────────────────────────────
const imgHovered = ref(false)

// ── Help tooltip ──────────────────────────────────────────────
const showHelp = ref(false)

// ── Navigation ────────────────────────────────────────────────
const emit = defineEmits(['continue', 'back'])

function handleContinue() {
  if (!selectedMeals.value.length) return
  emit('continue', {
    weather:   weatherOptions.value.filter(w => w.enabled).map(w => w.id),
    mealTimes: selectedMeals.value,
  })
}
function handleBack() { emit('back') }
</script>

<style scoped>
/* ========================================
   CSS VARIABLES
======================================== */
:root {
  --primary:        #570000;
  --primary-ctr:    #800000;
  --secondary:      #96473b;
  --on-primary:     #ffffff;
  --surface:        #faf9f6;
  --surface-low:    #f4f3f1;
  --surface-ctr:    #efeeeb;
  --surface-high:   #e9e8e5;
  --on-surface:     #1a1c1a;
  --on-surface-var: #5a413d;
  --outline-var:    #e2bfb9;
  --font-display:   'Manrope', sans-serif;
  --font-body:      'Plus Jakarta Sans', sans-serif;
  --radius-card:    1rem;
  --radius-pill:    9999px;
  --shadow-card:    0 12px 32px rgba(128, 0, 0, 0.06);
}

/* ========================================
   BASE
======================================== */
* { box-sizing: border-box; margin: 0; padding: 0; }

.app-wrapper {
  font-family: var(--font-body);
  background: var(--surface);
  color: var(--on-surface);
  min-height: 100vh;
  padding-bottom: 6.5rem;
}

/* ========================================
   TOP APP BAR
======================================== */
.top-bar {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(250, 249, 246, 0.82);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(226, 191, 185, 0.35);
}
.top-bar__inner {
  max-width: 48rem;
  margin: 0 auto;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.brand {
  font-family: var(--font-display);
  font-size: 0.8rem;
  font-weight: 900;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--primary);
}
.top-bar__right {
  display: flex;
  align-items: center;
  gap: 0.875rem;
}
.step-label {
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--on-surface-var);
}
.help-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--primary-ctr);
  display: flex;
  align-items: center;
  transition: opacity 0.2s;
  padding: 0;
}
.help-btn:hover { opacity: 0.65; }

/* ========================================
   MAIN CONTENT
======================================== */
.main-content {
  max-width: 48rem;
  margin: 0 auto;
  padding: 2.5rem 1.25rem 1rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}
@media (min-width: 640px) {
  .main-content { padding: 3rem 1.5rem 1rem; }
}

/* ========================================
   PROGRESS DOTS
======================================== */
.progress-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}
.progress-dot {
  height: 6px;
  border-radius: var(--radius-pill);
  transition: width 0.4s ease, background 0.3s;
}
.progress-dot--done   { width: 3rem; background: var(--surface-high); }
.progress-dot--active { width: 6rem; background: var(--primary-ctr); }

/* ========================================
   HERO
======================================== */
.hero {}
.hero__title {
  font-family: var(--font-display);
  font-size: clamp(1.75rem, 5vw, 2.5rem);
  font-weight: 700;
  color: var(--primary);
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 0.875rem;
}
.hero__sub {
  font-size: 1rem;
  color: var(--on-surface-var);
  line-height: 1.7;
  max-width: 36rem;
}

/* ========================================
   PREFS GRID
======================================== */
.prefs-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

.pref-block {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.pref-block--full { grid-column: 1 / -1; }

.block-heading {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.block-title {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--on-surface);
}
.icon-secondary { color: var(--secondary); font-size: 1.4rem; }

/* ========================================
   WEATHER CARDS
======================================== */
.weather-section {
  background: var(--surface-low);
  border-radius: var(--radius-card);
  padding: 2rem;
}
.weather-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
  background: var(--surface-low);
  border-radius: var(--radius-card);
  padding: 1.5rem;
}
@media (min-width: 480px) {
  .weather-grid { grid-template-columns: 1fr 1fr; }
}

.weather-card {
  background: #fff;
  border-radius: 0.875rem;
  padding: 1.5rem;
  cursor: pointer;
  box-shadow: var(--shadow-card);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  border: 1.5px solid transparent;
  transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
  user-select: none;
}
.weather-card:hover { transform: scale(1.02); }
.weather-card--on {
  border-color: rgba(87, 0, 0, 0.12);
  box-shadow: 0 12px 32px rgba(87, 0, 0, 0.1);
}

.weather-card__top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.weather-icon { font-size: 1.85rem; color: var(--secondary); transition: color 0.2s; }
.weather-icon--active { color: var(--primary); }

/* Toggle Switch */
.toggle {
  width: 2.5rem;
  height: 1.5rem;
  border-radius: var(--radius-pill);
  background: var(--surface-high);
  position: relative;
  transition: background 0.3s;
  flex-shrink: 0;
}
.toggle--on { background: var(--primary); }
.toggle__thumb {
  position: absolute;
  top: 0.2rem;
  left: 0.2rem;
  width: 1.1rem;
  height: 1.1rem;
  background: #fff;
  border-radius: var(--radius-pill);
  transition: transform 0.3s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.15);
}
.toggle__thumb--on { transform: translateX(1rem); }

.weather-card__name {
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--on-surface);
  margin-bottom: 0.25rem;
}
.weather-card__desc {
  font-size: 0.8rem;
  color: var(--on-surface-var);
  line-height: 1.5;
}

/* ========================================
   MEAL TIME PILLS
======================================== */
.meal-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}
.meal-pill {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.75rem;
  border-radius: var(--radius-pill);
  border: none;
  background: var(--surface-high);
  color: var(--on-surface-var);
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: transform 0.2s, background 0.2s, color 0.2s, box-shadow 0.2s;
  white-space: nowrap;
}
.meal-pill:hover { transform: scale(1.04); }
.meal-pill--active {
  background: var(--primary);
  color: #fff;
  transform: scale(1.06);
  box-shadow: var(--shadow-card);
}

/* ========================================
   ATMOSPHERE IMAGE
======================================== */
.atmosphere-wrap {
  position: relative;
  border-radius: var(--radius-card);
  overflow: hidden;
  aspect-ratio: 21 / 9;
}
.atmosphere-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(100%);
  opacity: 0.4;
  transition: filter 0.7s, opacity 0.7s;
  display: block;
}
.atmosphere-img--hovered {
  filter: grayscale(0);
  opacity: 1;
}
.atmosphere-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, var(--surface) 0%, transparent 60%);
  pointer-events: none;
}

/* ========================================
   BOTTOM NAVIGATION
======================================== */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 50;
  background: rgba(250, 249, 246, 0.92);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 2rem 2rem 0 0;
  box-shadow: 0 -12px 32px rgba(128, 0, 0, 0.05);
  padding: 1.25rem 1.5rem 2.25rem;
}
.bottom-nav__inner {
  max-width: 48rem;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-back {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  background: none;
  border: none;
  color: var(--on-surface-var);
  font-family: var(--font-body);
  font-weight: 700;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  cursor: pointer;
  padding: 0.875rem 1.25rem;
  border-radius: var(--radius-pill);
  transition: transform 0.2s, background 0.2s;
}
.btn-back:hover { background: rgba(87,0,0,0.05); }
.btn-back:active { transform: scale(0.95); }

.btn-continue {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #570000, #800000);
  color: #fff;
  border: none;
  border-radius: var(--radius-pill);
  padding: 0.95rem 2.25rem;
  font-family: var(--font-body);
  font-weight: 700;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(87, 0, 0, 0.25);
  transition: transform 0.2s, opacity 0.2s;
}
.btn-continue:hover:not(:disabled) { transform: scale(1.04); }
.btn-continue:active:not(:disabled) { transform: scale(0.96); }
.btn-continue:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  box-shadow: none;
}

.icon-sm { font-size: 1rem !important; }

/* ========================================
   HELP TOAST
======================================== */
.help-toast {
  position: fixed;
  top: 4.5rem;
  left: 50%;
  transform: translateX(-50%);
  background: var(--on-surface);
  color: #fff;
  padding: 0.75rem 1.25rem;
  border-radius: var(--radius-pill);
  font-size: 0.8rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 100;
  box-shadow: 0 8px 24px rgba(0,0,0,0.18);
  white-space: nowrap;
  cursor: pointer;
  max-width: 90vw;
  white-space: normal;
  text-align: center;
}

/* ========================================
   TRANSITIONS
======================================== */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s, transform 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateX(-50%) translateY(-0.5rem); }

/* ========================================
   MATERIAL ICONS
======================================== */
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  font-size: 1.4rem;
  line-height: 1;
  vertical-align: middle;
}
</style>