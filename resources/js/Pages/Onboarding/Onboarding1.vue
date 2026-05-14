<template>
  <div class="app-wrapper">

    <!-- Top App Bar -->
    <header class="top-bar">
      <div class="top-bar__inner">
        <span class="brand">Academic Epicurean</span>
        <button class="skip-btn" @click="handleSkip">Skip</button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Progress Indicator -->
      <div class="progress-section">
        <div class="progress-labels">
          <span class="progress-step">Langkah {{ currentStep }} dari {{ totalSteps }}</span>
          <span class="progress-pct">{{ progressPercent }}%</span>
        </div>
        <div class="progress-track">
          <div class="progress-fill" :style="{ width: progressPercent + '%' }"></div>
        </div>
      </div>

      <!-- Heading -->
      <div class="heading-block">
        <h1 class="heading-title">Bagaimana seleramu?</h1>
        <p class="heading-sub">Beri tahu kami rasa favoritmu agar kami bisa menyarankan menu yang cocok.</p>
      </div>

      <!-- Content Grid -->
      <div class="content-grid">

        <!-- Flavor Multi-select -->
        <section class="flavor-section">
          <h3 class="section-label">
            <span class="material-symbols-outlined icon-sm">restaurant_menu</span>
            Pilih Profil Rasa
          </h3>
          <div class="flavor-grid">
            <button
              v-for="flavor in flavors"
              :key="flavor.id"
              class="flavor-card"
              :class="{ 'flavor-card--selected': selectedFlavors.includes(flavor.id) }"
              @click="toggleFlavor(flavor.id)"
            >
              <div class="flavor-icon">
                <span
                  class="material-symbols-outlined"
                  :style="selectedFlavors.includes(flavor.id)
                    ? 'font-variation-settings:\'FILL\' 1'
                    : ''"
                >{{ flavor.icon }}</span>
              </div>
              <div class="flavor-text">
                <p class="flavor-name">{{ flavor.name }}</p>
                <p class="flavor-desc">{{ flavor.desc }}</p>
              </div>
              <span
                v-if="selectedFlavors.includes(flavor.id)"
                class="flavor-check"
              >
                <span class="material-symbols-outlined icon-xs">check</span>
              </span>
            </button>
          </div>
        </section>

        <!-- Dietary Section -->
        <section class="dietary-section">
          <div class="dietary-card">
            <span class="dietary-bg-icon material-symbols-outlined">spa</span>
            <h3 class="section-label">
              <span class="material-symbols-outlined icon-sm">assignment_late</span>
              Pantangan Diet
            </h3>
            <div class="dietary-list">
              <label
                v-for="diet in dietaryOptions"
                :key="diet.id"
                class="dietary-item"
              >
                <div class="dietary-item__left">
                  <span class="material-symbols-outlined dietary-icon">{{ diet.icon }}</span>
                  <span class="dietary-item__label">{{ diet.label }}</span>
                </div>
                <input
                  v-model="selectedDietary"
                  type="checkbox"
                  :value="diet.id"
                  class="diet-checkbox"
                />
              </label>
            </div>
            <div class="dietary-info">
              <p class="dietary-info__text">
                <span class="dietary-info__bold">Info:</span>
                Data ini akan kami gunakan untuk menyaring menu yang mengandung bahan yang Anda hindari di seluruh kantin kampus.
              </p>
            </div>
          </div>
        </section>
      </div>

      <!-- Preview Banner -->
      <div class="preview-banner">
        <div class="preview-banner__text order-2">
          <h4 class="preview-title">Kami sedang meracik rekomendasimu...</h4>
          <p class="preview-desc">
            <template v-if="selectedFlavors.length">
              Berdasarkan pilihan
              <strong>{{ selectedFlavorNames }}</strong>,
              kami telah menemukan {{ menuCount }} menu spesial di Kantin Pusat yang mungkin Anda sukai.
            </template>
            <template v-else>
              Pilih profil rasa di atas untuk melihat rekomendasi menu yang sesuai untukmu.
            </template>
          </p>
          <div class="preview-avatars" v-if="selectedFlavors.length">
            <div class="avatar-stack">
              <div
                v-for="(img, i) in previewImages"
                :key="i"
                class="avatar-img"
              >
                <img :src="img" :alt="'Preview ' + (i + 1)" />
              </div>
              <div class="avatar-more">+{{ menuCount - 2 }}</div>
            </div>
            <span class="preview-meta">Menu tersedia sekarang</span>
          </div>
        </div>
        <div class="preview-banner__image order-1">
          <img
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBOwZeeuBSAe07mfMKHHGrx-R5-_ZSzAoI8C2mPx4cc0W6R8nbLNQwFMaNt7JlvFtd9M1V9Bw169u5WcxiIyft3sKykL5frnpcvLxDph28swsMReE7vzZkFbE1N9G_1gwWPOZ2c6dWbnCQXKjOL0Fa8gH--cjrjfAfTFAy6INWPBX5F72UyUo5le4GV6VeQOAUQcc2KNJ7zNlsimdpuUNqTLGRfYFjCDSyI94Pq2kvNQJYERZ39OHIz5rc-X4MKdt_1o0oHXVt8Zv18"
            alt="Campus café"
          />
        </div>
      </div>

    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
      <div class="bottom-nav__inner">
        <button class="btn-back" @click="handleBack">
          <span class="material-symbols-outlined">arrow_back</span>
          Back
        </button>
        <button
          class="btn-next"
          :disabled="selectedFlavors.length === 0"
          @click="handleNext"
        >
          Next
          <span class="material-symbols-outlined">arrow_forward</span>
        </button>
      </div>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// ── Step / Progress ──────────────────────────────────────────
const currentStep  = ref(2)
const totalSteps   = ref(3)
const progressPercent = computed(() => Math.round((currentStep.value / totalSteps.value) * 100))

// ── Flavor Options ────────────────────────────────────────────
const flavors = ref([
  { id: 'manis',  name: 'Manis',  desc: 'Dessert & Minuman',  icon: 'cookie'               },
  { id: 'pedas',  name: 'Pedas',  desc: 'Gairah & Rempah',    icon: 'local_fire_department' },
  { id: 'gurih',  name: 'Gurih',  desc: 'Kaya akan Umami',    icon: 'ramen_dining'          },
  { id: 'segar',  name: 'Segar',  desc: 'Buah & Sayuran',     icon: 'eco'                   },
  { id: 'pahit',  name: 'Pahit',  desc: 'Kopi & Teh Pekat',   icon: 'coffee'                },
])
const selectedFlavors = ref(['pedas'])

const selectedFlavorNames = computed(() =>
  selectedFlavors.value
    .map(id => flavors.value.find(f => f.id === id)?.name)
    .filter(Boolean)
    .join(', ')
)

function toggleFlavor(id) {
  const idx = selectedFlavors.value.indexOf(id)
  if (idx === -1) selectedFlavors.value.push(id)
  else            selectedFlavors.value.splice(idx, 1)
}

// ── Dietary Options ───────────────────────────────────────────
const dietaryOptions = ref([
  { id: 'no-spicy',     label: 'Tidak Pedas',  icon: 'no_meals'    },
  { id: 'vegetarian',   label: 'Vegetarian',   icon: 'psychiatry'  },
  { id: 'egg-allergy',  label: 'Alergi Telur', icon: 'egg_alt'     },
])
const selectedDietary = ref([])

// ── Preview Banner ────────────────────────────────────────────
const menuCount = computed(() => selectedFlavors.value.length * 4 + 4)
const previewImages = [
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCi_1YcY-4G0uTMwGx95bIhvVDpV5rk4F_5B8AaTHmMpk1iED6crEzUrbEKCF2zwobkN7NFgVH2br_Ea67ey0jCmuEhxau177BPESPE5Cb-dh71_LPxIKF0V1Peq78mXosJyNGwq_OZW3V31lQi_yjLmdxCvIvpd7M3i0EgErI3THelzs5wOloECxWkeTg5TaYN3ZmXsHSQGwygeKc9TklC9AsuLWSaeLvWJ6WuJ4cC7QTmLQV53jsD04FvjKBiKCqS0Ex3fmjexK3U',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuB75LJsh8h7IRBTBPNXqCjdgcgZNWwhup-_4D8CDLW0DyI_w0fIcjoQwjGJbRHTxsJ7tu3K5S29etDEr6TTEAXxLw4LoKWcFax5cy3fTI4UJn_t7IAxW32R5uWN15ZKxu8B0LpAdLaeuCiqRTXO8CWUBMHXUhOSGwP-YCj6_dCPUSEVtegz-9j88SnQkcd6ECmnwiA1n3N-0u86grRPWRddDszSO9TvJ6lrVF9-tzGxc4FIc5jwPFRkFhhWMBa51-D4dNMWoESyHxsN',
]

// ── Navigation ────────────────────────────────────────────────
const emit = defineEmits(['next', 'back', 'skip'])

function handleNext() {
  if (selectedFlavors.value.length === 0) return
  emit('next', { flavors: selectedFlavors.value, dietary: selectedDietary.value })
}
function handleBack() { emit('back') }
function handleSkip() { emit('skip') }
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
  --on-primary-ctr: #ff8371;
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
  --shadow:         0 12px 32px rgba(128, 0, 0, 0.07);
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
  padding-bottom: 6rem;
}

/* ========================================
   TOP BAR
======================================== */
.top-bar {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 50;
  background: rgba(250, 249, 246, 0.82);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--outline-var);
}
.top-bar__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.brand {
  font-family: var(--font-display);
  font-size: 1.2rem;
  font-weight: 800;
  color: #7f1d1d;
  letter-spacing: -0.03em;
}
.skip-btn {
  background: none;
  border: none;
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--on-surface-var);
  cursor: pointer;
  transition: opacity 0.2s;
}
.skip-btn:hover { opacity: 0.6; }

/* ========================================
   MAIN
======================================== */
.main-content {
  max-width: 1280px;
  margin: 0 auto;
  padding: 6.5rem 1.25rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}
@media (min-width: 768px) {
  .main-content { padding: 7rem 1.5rem 2rem; }
}

/* ========================================
   PROGRESS
======================================== */
.progress-section { display: flex; flex-direction: column; gap: 0.5rem; }
.progress-labels {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}
.progress-step {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--on-surface-var);
}
.progress-pct {
  font-family: var(--font-display);
  font-weight: 700;
  color: var(--primary);
}
.progress-track {
  height: 6px;
  background: var(--surface-high);
  border-radius: var(--radius-pill);
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary), var(--primary-ctr));
  border-radius: var(--radius-pill);
  transition: width 0.7s ease;
}

/* ========================================
   HEADING
======================================== */
.heading-block { max-width: 40rem; }
.heading-title {
  font-family: var(--font-display);
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 800;
  color: var(--primary);
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin-bottom: 0.75rem;
}
.heading-sub {
  font-size: 1.05rem;
  color: var(--on-surface-var);
  font-weight: 500;
  line-height: 1.6;
}

/* ========================================
   CONTENT GRID
======================================== */
.content-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}
@media (min-width: 768px) {
  .content-grid { grid-template-columns: 7fr 5fr; gap: 2.5rem; }
}

/* ========================================
   SECTION LABEL
======================================== */
.section-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--secondary);
  margin-bottom: 1.25rem;
}
.icon-sm { font-size: 1rem; }
.icon-xs { font-size: 0.75rem; }

/* ========================================
   FLAVOR CARDS
======================================== */
.flavor-section {}
.flavor-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 0.9rem;
}

.flavor-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.1rem 1.25rem;
  border-radius: var(--radius-card);
  border: 1.5px solid transparent;
  background: #ffffff;
  box-shadow: var(--shadow);
  cursor: pointer;
  transition: transform 0.25s, border-color 0.25s, background 0.25s, box-shadow 0.25s;
  font-family: var(--font-body);
  text-align: left;
}
.flavor-card:hover {
  border-color: rgba(226, 191, 185, 0.5);
  transform: translateY(-2px);
}
.flavor-card--selected {
  background: var(--primary);
  border-color: var(--primary);
  transform: scale(1.03);
  box-shadow: 0 12px 32px rgba(87, 0, 0, 0.2);
}

.flavor-icon {
  width: 3rem;
  height: 3rem;
  border-radius: var(--radius-pill);
  background: var(--surface-low);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  flex-shrink: 0;
  transition: background 0.25s, color 0.25s;
}
.flavor-card--selected .flavor-icon {
  background: var(--primary-ctr);
  color: #fff;
}
.flavor-card:not(.flavor-card--selected):hover .flavor-icon {
  background: var(--primary);
  color: #fff;
}

.flavor-text {}
.flavor-name {
  font-weight: 700;
  color: var(--on-surface);
  font-size: 0.9rem;
}
.flavor-card--selected .flavor-name { color: #fff; }
.flavor-desc {
  font-size: 0.72rem;
  color: var(--on-surface-var);
  margin-top: 2px;
}
.flavor-card--selected .flavor-desc { color: var(--on-primary-ctr); }

.flavor-check {
  position: absolute;
  top: -0.45rem;
  right: -0.45rem;
  background: var(--on-primary-ctr);
  color: #fff;
  width: 1.4rem;
  height: 1.4rem;
  border-radius: var(--radius-pill);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ========================================
   DIETARY CARD
======================================== */
.dietary-section {}
.dietary-card {
  position: relative;
  overflow: hidden;
  background: var(--surface-low);
  border-radius: var(--radius-card);
  padding: 2rem;
}
.dietary-bg-icon {
  position: absolute;
  right: -1.5rem;
  bottom: -1.5rem;
  font-size: 7.5rem;
  opacity: 0.08;
  pointer-events: none;
  color: var(--primary);
}

.dietary-list { display: flex; flex-direction: column; gap: 0.75rem; }
.dietary-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  background: #fff;
  border-radius: 0.625rem;
  cursor: pointer;
  transition: background 0.2s;
}
.dietary-item:hover { background: var(--surface); }
.dietary-item__left { display: flex; align-items: center; gap: 0.75rem; }
.dietary-icon { color: var(--secondary); font-size: 1.3rem; }
.dietary-item__label { font-weight: 600; font-size: 0.9rem; }

.diet-checkbox {
  width: 1.2rem;
  height: 1.2rem;
  cursor: pointer;
  accent-color: var(--primary);
  border-radius: 0.25rem;
  flex-shrink: 0;
}

.dietary-info {
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid rgba(226, 191, 185, 0.3);
}
.dietary-info__text {
  font-size: 0.77rem;
  color: var(--on-surface-var);
  line-height: 1.65;
}
.dietary-info__bold { font-weight: 700; }

/* ========================================
   PREVIEW BANNER
======================================== */
.preview-banner {
  background: var(--surface-low);
  border-radius: var(--radius-card);
  padding: 2rem;
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.75rem;
  align-items: center;
}
@media (min-width: 768px) {
  .preview-banner { grid-template-columns: 1fr 1fr; }
  .order-1 { order: 1; }
  .order-2 { order: 2; }
}

.preview-banner__text { display: flex; flex-direction: column; gap: 0.875rem; }
.preview-title {
  font-family: var(--font-display);
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--primary);
  font-style: italic;
  line-height: 1.3;
}
.preview-desc {
  font-size: 0.9rem;
  color: var(--on-surface-var);
  line-height: 1.65;
}
.preview-avatars {
  display: flex;
  align-items: center;
  gap: 0.875rem;
  flex-wrap: wrap;
}
.avatar-stack { display: flex; }
.avatar-img {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: var(--radius-pill);
  overflow: hidden;
  border: 2px solid var(--surface-low);
  margin-left: -0.7rem;
}
.avatar-img:first-child { margin-left: 0; }
.avatar-img img { width: 100%; height: 100%; object-fit: cover; }
.avatar-more {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: var(--radius-pill);
  background: var(--primary);
  color: #fff;
  font-size: 0.6rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: -0.7rem;
  border: 2px solid var(--surface-low);
}
.preview-meta {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--on-surface-var);
}

.preview-banner__image {
  height: 12rem;
  border-radius: 0.75rem;
  overflow: hidden;
  background: var(--surface-high);
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
@media (min-width: 768px) { .preview-banner__image { height: 16rem; } }
.preview-banner__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ========================================
   BOTTOM NAVIGATION
======================================== */
.bottom-nav {
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index: 50;
  padding: 1rem 1.25rem 2rem;
  background: linear-gradient(to top, rgba(250,249,246,0.98) 60%, transparent);
}
.bottom-nav__inner {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-back {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: none;
  border: none;
  color: var(--primary);
  font-family: var(--font-body);
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  padding: 0.875rem 1.25rem;
  border-radius: var(--radius-pill);
  transition: transform 0.2s, background 0.2s;
}
.btn-back:hover { background: rgba(87, 0, 0, 0.06); }
.btn-back:active { transform: scale(0.95); }

.btn-next {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #570000, #800000);
  color: #fff;
  border: none;
  border-radius: var(--radius-pill);
  padding: 0.95rem 2.25rem;
  font-family: var(--font-body);
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(87, 0, 0, 0.25);
  transition: transform 0.2s, opacity 0.2s;
}
.btn-next:hover:not(:disabled) { transform: scale(1.04); }
.btn-next:active:not(:disabled) { transform: scale(0.96); }
.btn-next:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
}

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