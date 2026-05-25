<template>
  <div class="app-wrapper">

    <!-- Top Navigation Bar -->
    <nav class="top-nav">
      <div class="top-nav__inner">
        <span class="brand">Campus Epicurean</span>

        <!-- Desktop Menu -->
        <div class="desktop-menu">
          <a
            v-for="item in navItems"
            :key="item.label"
            href="#"
            class="nav-link"
            :class="{ 'nav-link--active': item.active }"
          >{{ item.label }}</a>
        </div>

        <!-- Nav Actions -->
        <div class="nav-actions">
          <button class="icon-action" aria-label="Wishlist">
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">favorite</span>
          </button>
          <button class="icon-action" aria-label="Keranjang">
            <span class="material-symbols-outlined">shopping_bag</span>
          </button>
          <div class="avatar">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDaFcI49msf8vqWV7gBN68E_lbXxU4KM6Fc-f03gmAYd5d1XIxxOqeGuKCIVPeT7roQ-gXdBnhstS2KQv_WSHdoemdU8BFkPalTlKvrxXp1txHHXj31AbXLoDs2hZoQXKZgxREpv8TQx3pmFXb0bhomrig9-cZk9kvcTlVGR-TCaHbS-_JbaCIzJ0cZP-QJrmkDtP_On-9m7N7ZC4BS0CGNJNEk2tPilu0mQ5V7kSuCP5Ea3DrKde4s7Iv2jOqOsp0_jYoI7AU6fI5p"
              alt="Profil pengguna"
            />
          </div>
        </div>
      </div>
      <div class="top-nav__divider"></div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Page Header -->
      <header class="page-header">
        <div class="page-header__text">
          <h1 class="page-title">Wishlist Saya</h1>
          <p class="page-sub">Temukan kembali santapan kampus favorit yang Anda simpan.</p>
        </div>
        <span class="wishlist-count">{{ wishlistItems.length }} item</span>
      </header>

      <!-- Empty State -->
      <transition name="fade">
        <div v-if="wishlistItems.length === 0" class="empty-state">
          <span class="material-symbols-outlined empty-icon">favorite_border</span>
          <p class="empty-title">Wishlist masih kosong</p>
          <p class="empty-sub">Tambahkan makanan favoritmu dari halaman Canteen.</p>
        </div>
      </transition>

      <!-- Wishlist Grid -->
      <div v-if="wishlistItems.length > 0" class="wishlist-grid">
        <div
          v-for="item in wishlistItems"
          :key="item.id"
          class="food-card"
        >
          <!-- Image -->
          <div class="food-card__img-wrap">
            <img :src="item.image" :alt="item.name" class="food-card__img" />

            <!-- Remove from Wishlist -->
            <button
              class="wishlist-btn"
              :class="{ 'wishlist-btn--active': item.wishlisted }"
              aria-label="Hapus dari wishlist"
              @click="toggleWishlist(item)"
            >
              <span
                class="material-symbols-outlined"
                :style="item.wishlisted ? 'font-variation-settings:\'FILL\' 1' : ''"
              >favorite</span>
            </button>

            <!-- Badge -->
            <span
              class="food-card__badge"
              :class="item.badge === 'Favorit' ? 'food-card__badge--primary' : 'food-card__badge--neutral'"
            >{{ item.badge }}</span>
          </div>

          <!-- Body -->
          <div class="food-card__body">
            <div class="food-card__info">
              <div class="food-card__row">
                <h3 class="food-card__name">{{ item.name }}</h3>
                <span class="food-card__price">Rp {{ item.price.toLocaleString('id-ID') }}</span>
              </div>
              <div class="food-card__tags">
                <span class="tag tag--primary">{{ item.category }}</span>
                <span class="tag tag--neutral">{{ item.ingredients }}</span>
              </div>
            </div>

            <button
              class="add-btn"
              @click="addToCart(item)"
            >
              <span class="material-symbols-outlined">add_shopping_cart</span>
              Tambah ke Keranjang
            </button>
          </div>
        </div>
      </div>

    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="bottom-nav">
      <a
        v-for="item in bottomNavItems"
        :key="item.label"
        href="#"
        class="bottom-nav__item"
        :class="{ 'bottom-nav__item--active': item.active }"
        @click.prevent="setActiveNav(item)"
      >
        <span
          class="material-symbols-outlined"
          :style="item.active ? 'font-variation-settings:\'FILL\' 1' : ''"
        >{{ item.icon }}</span>
        <span class="bottom-nav__label">{{ item.label }}</span>
      </a>
    </nav>

    <!-- Toast -->
    <transition name="toast-slide">
      <div v-if="toast.visible" class="toast">
        <span class="material-symbols-outlined">{{ toast.icon }}</span>
        {{ toast.message }}
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref } from 'vue'

// ── Navigation ────────────────────────────────────────────────
const navItems = ref([
  { label: 'Canteen',  active: false },
  { label: 'Orders',   active: false },
  { label: 'Wishlist', active: true  },
  { label: 'Account',  active: false },
])

const bottomNavItems = ref([
  { label: 'Canteen',  icon: 'restaurant',    active: false },
  { label: 'Orders',   icon: 'receipt_long',  active: false },
  { label: 'Wishlist', icon: 'favorite',      active: true  },
  { label: 'Account',  icon: 'person',        active: false },
])

function setActiveNav(selected) {
  bottomNavItems.value.forEach(i => (i.active = i.label === selected.label))
}

// ── Wishlist Items ────────────────────────────────────────────
const wishlistItems = ref([
  {
    id: 1,
    name:        'Seblak Spesial Giat',
    price:       15000,
    category:    'MEAL',
    ingredients: 'Bumbu kencur, kerupuk, telur',
    badge:       'Favorit',
    wishlisted:  true,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAr-jetYj7F5BDDKBCa0uJBzCXx6nlOCI42Wrrc2AzbGZLRfaLJMuJvbMaBzIdJKsw4oVrobX9TPwD1-WIFVuR5wX9QjUZo-FmQ954wdk2IUSNvAQfonBxgo2RjtWSEItUvk31lEl5NHQ-M0PII_bt-FXU0D5rBgY06MLY6a05sdpFqZQ_Mtsi8riJ-5qbx82Xeljfs_a1Vmww63KfCjeWi9AjVgKtJt8b8cwrKocwJr6ZPTbgd-j7EP3dq1kOhR7QZJz9gA1otyozM',
  },
  {
    id: 2,
    name:        'Batagor Ikan Tenggiri',
    price:       12000,
    category:    'SNACK',
    ingredients: 'Ikan tenggiri, tapioka, bumbu kacang',
    badge:       'Ready Stock',
    wishlisted:  true,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuC3gVgA4kjbcj1fqaiKj72VonTN_1sKARTVMvgD753NDRd6VLdKzvQ27Wnou070-BbKxdJwu_hkBLX7Juf62S0F1qimAwx-rsBvrJ24oKO4RUZER715mqHUX3It1zu5jpvDV23ptvs6elKVwFMkqF2X5hszd1G-tw0Rp51UdBCWDAHFFzQSGd1kcLfLqO_mGxbCaMeng79exzR-NI7MMDKtlUC0yHCiS4OXfTfpiVe4dW84KBLdgSAOLsb7-L6xe3bgZomD3SG0bVOr',
  },
  {
    id: 3,
    name:        'Mie Tek-tek Abang',
    price:       18000,
    category:    'MEAL',
    ingredients: 'Mie, telur, sayuran, ayam',
    badge:       'Favorit',
    wishlisted:  true,
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDiBkYqlvcY0yr1HJaVXv7YJPgkB7osZ0MkQRS26SiyR2xlEEz6Htso2kvPFkvSuh06yKJQsDH26rikyguIumBlSZJO18VyEJ-FIV4HiWK5F8pMRcwiKiTW1yYHMMKy_588-vP-evYmQtzFto1xHXM1HOqLG_J7IKV4qtegNEDcYU35pVoSPfYisPypdUgZpWn__v_mlkkEvPJ3OlBMkwj2aqe_A5KVmctyAnozatkRPg7BUK2-kXXRpTT04mkJw3AuGcF-wbPM_ZBj',
  },
])

function toggleWishlist(item) {
  item.wishlisted = !item.wishlisted
  if (!item.wishlisted) {
    showToast(`${item.name} dihapus dari wishlist`, 'heart_broken')
    setTimeout(() => {
      wishlistItems.value = wishlistItems.value.filter(i => i.id !== item.id)
    }, 400)
  }
}

// ── Cart ──────────────────────────────────────────────────────
function addToCart(item) {
  showToast(`${item.name} ditambahkan ke keranjang!`, 'check_circle')
}

// ── Toast ─────────────────────────────────────────────────────
const toast = ref({ visible: false, message: '', icon: 'check_circle' })
let toastTimer = null

function showToast(message, icon = 'check_circle') {
  clearTimeout(toastTimer)
  toast.value = { visible: true, message, icon }
  toastTimer = setTimeout(() => { toast.value.visible = false }, 2600)
}
</script>

<style scoped>
/* ========================================
   CSS VARIABLES
======================================== */
:root {
  --primary:        #570000;
  --primary-ctr:    #800000;
  --on-primary:     #ffffff;
  --secondary:      #96473b;
  --surface:        #faf9f6;
  --surface-low:    #f4f3f1;
  --surface-ctr:    #efeeeb;
  --surface-high:   #e9e8e5;
  --on-surface:     #1a1c1a;
  --on-surface-var: #5a413d;
  --outline-var:    #e2bfb9;
  --font-display:   'Manrope', sans-serif;
  --font-body:      'Plus Jakarta Sans', sans-serif;
  --radius-card:    0.875rem;
  --radius-pill:    9999px;
  --shadow-hover:   0 12px 32px rgba(128, 0, 0, 0.08);
  --shadow-nav:     0 -8px 30px rgba(128, 0, 0, 0.05);
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
@media (min-width: 768px) { .app-wrapper { padding-bottom: 0; } }

/* ========================================
   TOP NAVIGATION
======================================== */
.top-nav {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 50;
  background: rgba(250, 249, 246, 0.82);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}
.top-nav__inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.top-nav__divider {
  height: 1px;
  background: var(--surface-low);
  width: 100%;
}

.brand {
  font-family: var(--font-display);
  font-size: 1.15rem;
  font-weight: 800;
  color: var(--primary-ctr);
  text-transform: uppercase;
  letter-spacing: -0.03em;
  white-space: nowrap;
}

/* Desktop Menu */
.desktop-menu { display: none; }
@media (min-width: 768px) {
  .desktop-menu { display: flex; gap: 2rem; }
}

.nav-link {
  text-decoration: none;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 0.875rem;
  color: var(--on-surface-var);
  letter-spacing: -0.01em;
  transition: color 0.2s;
  padding-bottom: 2px;
}
.nav-link:hover { color: var(--primary); }
.nav-link--active {
  color: var(--primary-ctr);
  border-bottom: 2px solid var(--primary-ctr);
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.icon-action {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--primary-ctr);
  display: flex;
  transition: transform 0.2s;
}
.icon-action:hover { transform: scale(1.12); }

.avatar {
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-pill);
  overflow: hidden;
  border: 1px solid rgba(226, 191, 185, 0.3);
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }

/* ========================================
   MAIN CONTENT
======================================== */
.main-content {
  max-width: 1280px;
  margin: 0 auto;
  padding: 7rem 1.25rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}
@media (min-width: 768px) { .main-content { padding: 7.5rem 1.5rem 2rem; } }

/* ========================================
   PAGE HEADER
======================================== */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}
.page-title {
  font-family: var(--font-display);
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 800;
  color: var(--primary);
  letter-spacing: -0.02em;
  line-height: 1.1;
  margin-bottom: 0.35rem;
}
.page-sub {
  color: var(--on-surface-var);
  font-weight: 500;
  font-size: 0.9rem;
}
.wishlist-count {
  font-family: var(--font-display);
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--on-surface-var);
  background: var(--surface-high);
  padding: 0.4rem 0.875rem;
  border-radius: var(--radius-pill);
}

/* ========================================
   EMPTY STATE
======================================== */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 5rem 1rem;
  gap: 0.75rem;
}
.empty-icon { font-size: 4rem; color: var(--outline-var); }
.empty-title { font-family: var(--font-display); font-weight: 700; font-size: 1.2rem; color: var(--primary); }
.empty-sub { color: var(--on-surface-var); font-size: 0.9rem; }

/* ========================================
   WISHLIST GRID
======================================== */
.wishlist-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}
@media (min-width: 640px) { .wishlist-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .wishlist-grid { grid-template-columns: repeat(3, 1fr); } }

/* ========================================
   FOOD CARD
======================================== */
.food-card {
  background: #fff;
  border-radius: var(--radius-card);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.4s;
}
.food-card:hover { box-shadow: var(--shadow-hover); }

/* Image */
.food-card__img-wrap {
  position: relative;
  height: 16rem;
  overflow: hidden;
}
.food-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.65s;
}
.food-card:hover .food-card__img { transform: scale(1.08); }

/* Wishlist Button */
.wishlist-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  z-index: 2;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(6px);
  border: none;
  border-radius: var(--radius-pill);
  width: 2.4rem;
  height: 2.4rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  color: var(--on-surface-var);
  transition: transform 0.2s, color 0.2s;
}
.wishlist-btn:active { transform: scale(0.88); }
.wishlist-btn--active { color: var(--primary); }

/* Badge */
.food-card__badge {
  position: absolute;
  bottom: 1rem;
  left: 1rem;
  font-size: 0.6rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  padding: 0.3rem 0.75rem;
  border-radius: var(--radius-pill);
}
.food-card__badge--primary { background: var(--primary); color: var(--on-primary); }
.food-card__badge--neutral { background: var(--surface-high); color: var(--on-surface-var); }

/* Body */
.food-card__body {
  padding: 1.4rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
  flex-grow: 1;
}
.food-card__info { display: flex; flex-direction: column; gap: 0.75rem; }

.food-card__row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.75rem;
}
.food-card__name {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--primary);
  line-height: 1.25;
  flex: 1;
}
.food-card__price {
  font-family: var(--font-display);
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--primary);
  white-space: nowrap;
  flex-shrink: 0;
}

/* Tags */
.food-card__tags { display: flex; flex-direction: column; gap: 0.4rem; align-items: flex-start; }
.tag {
  font-size: 0.62rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  padding: 0.3rem 0.65rem;
  border-radius: 0.375rem;
}
.tag--primary   { background: var(--primary); color: var(--on-primary); }
.tag--neutral   { background: var(--surface-high); color: var(--on-surface-var); }

/* Add Button */
.add-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.95rem 1.25rem;
  border-radius: 0.75rem;
  border: none;
  background: linear-gradient(135deg, var(--primary), var(--primary-ctr));
  color: var(--on-primary);
  font-family: var(--font-body);
  font-weight: 700;
  font-size: 0.875rem;
  letter-spacing: 0.03em;
  cursor: pointer;
  transition: opacity 0.2s, transform 0.15s;
  margin-top: auto;
}
.add-btn:hover   { opacity: 0.88; }
.add-btn:active  { transform: scale(0.98); }

/* ========================================
   BOTTOM NAVIGATION (Mobile)
======================================== */
.bottom-nav {
  display: flex;
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 50;
  justify-content: space-around;
  align-items: center;
  padding: 0.75rem 1rem 1.75rem;
  background: rgba(250, 249, 246, 0.82);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-radius: 1.75rem 1.75rem 0 0;
  box-shadow: var(--shadow-nav);
}
@media (min-width: 768px) { .bottom-nav { display: none; } }

.bottom-nav__item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.15rem;
  text-decoration: none;
  color: var(--on-surface-var);
  opacity: 0.6;
  transition: opacity 0.2s, transform 0.2s, color 0.2s;
  -webkit-tap-highlight-color: transparent;
}
.bottom-nav__item:active { transform: scale(0.9); }
.bottom-nav__item--active {
  color: var(--primary-ctr);
  opacity: 1;
}
.bottom-nav__label {
  font-size: 0.58rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

/* ========================================
   TOAST
======================================== */
.toast {
  position: fixed;
  bottom: 5.5rem;
  left: 50%;
  transform: translateX(-50%);
  background: var(--on-surface);
  color: #fff;
  padding: 0.75rem 1.4rem;
  border-radius: var(--radius-pill);
  font-size: 0.84rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 200;
  box-shadow: 0 8px 24px rgba(0,0,0,0.18);
  white-space: nowrap;
  pointer-events: none;
}
@media (min-width: 768px) { .toast { bottom: 2rem; } }

/* ========================================
   TRANSITIONS
======================================== */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.toast-slide-enter-active,
.toast-slide-leave-active { transition: opacity 0.3s, transform 0.3s; }
.toast-slide-enter-from,
.toast-slide-leave-to { opacity: 0; transform: translateX(-50%) translateY(0.75rem); }

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