<template>
  <div class="app-wrapper">
    <!-- Top Navigation Bar -->
    <nav class="glass-nav sticky-nav">
      <div class="nav-inner">
        <div class="nav-left">
          <span class="brand-name">Koperasi Giat</span>
          <div class="desktop-menu">
            <a
              v-for="item in navItems"
              :key="item.label"
              href="#"
              class="nav-link"
              :class="{ 'nav-link--active': item.active }"
            >{{ item.label }}</a>
          </div>
        </div>

        <div class="nav-right">
          <div class="search-bar desktop-search">
            <span class="material-symbols-outlined icon-sm">search</span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari jajanan kampus..."
              class="search-input"
            />
          </div>
          <div class="nav-actions">
            <button class="icon-btn" @click="goToCart">
              <span class="material-symbols-outlined">shopping_cart</span>
            </button>
            <button class="icon-btn">
              <span class="material-symbols-outlined">notifications</span>
            </button>
            <div class="avatar">
              <img
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCay-MPyaxZfsMrbqmQCZh5B38NStanm674Fy2yjpMEBL_0XILq1bH1cv6BHX7K_dMnXwILuhmA5HZF2xhTESFARPLG9uTwlmWHf7fVQBJ7k2IuHEWmtpIetaD0sjmIwgAESzdvC4tGPJGFcT228uwX2vgVUq1l6Ce7i2K22GDKqEDV19Y2wFpsmdEaAncbnPMVfcW6GgrNo0aL3235nJTjC8ezNAwJ3BqCa-qrvbizYtY5DKUvqLKfSpjt7Czg4G2setLTZ-YNf6-q"
                alt="Profil Mahasiswa"
              />
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Hero / Greeting -->
      <header class="hero-section">
        <div class="greeting">
          <p class="greeting-sub">Selamat datang kembali,</p>
          <h1 class="greeting-name">Halo, {{ userName }}!</h1>
        </div>
        <!-- Mobile Search -->
        <div class="mobile-search">
          <span class="material-symbols-outlined icon-primary">search</span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Lapar mau makan apa?"
            class="search-input"
          />
        </div>
      </header>

      <!-- Weather Based Recommendation (Bento Grid) -->
      <section class="bento-grid">
        <!-- Main Banner Card -->
        <div class="bento-main">
          <div class="bento-main__content">
            <div class="weather-badge">
              <span class="material-symbols-outlined icon-weather" style="font-variation-settings:'FILL' 1">wb_sunny</span>
              <span class="weather-label">Pas buat cuaca panas gini</span>
            </div>
            <h2 class="bento-main__title">
              Kampus lagi terik banget nih, yuk ademin pake yang segar-segar!
            </h2>
            <p class="bento-main__desc">
              Suhu mencapai 32°C di sekitar kampus. Hilangkan dahaga dengan koleksi minuman dingin pilihan kami.
            </p>
            <button class="cta-btn" @click="filterCategory('Minuman')">
              Lihat Menu Segar
              <span class="material-symbols-outlined">arrow_forward</span>
            </button>
          </div>
          <div class="bento-main__image">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuA-irJyLepG2118W29nDbnRNY4OuQCtruZHNYZhqUb1xl9KLLlO9DM_qJQzYmpB-sDTfTD5Al3PhcAbocKb8dIdLDmcfzbHc33DAUPovntJxuuwmIac5ujXmEXbxx2lT0RNzDebBnV7rzVFkTVxletS4ruzZZ6lG2Qy0-4p6FMjAdNT8B5ni1xB7KR3IXlZ7q2Auc3N8xXHcsEIDMUnefrls00p5sz3XEgRbrrOt6AynHTBVZ4AfnG9ZQbQvp4WEqtdvwYg3jRy-DvS"
              alt="Es teh segar"
              class="bento-main__drink"
            />
          </div>
        </div>

        <!-- Bestseller Card -->
        <div class="bento-side">
          <span class="bestseller-label">Terlaris Saat Ini</span>
          <div class="bestseller-img-wrap">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuAenucty_ChILpDrB_Po2IaxtAJiF7fbMZJL8ThqPDmkDXYMs8dOOkF5neuXEFJSbpU2j5v0E8fQ9C-ECeagfy-pWJtWFOCaltLChTRnSHVou9RdnbOjFOmhhXQArMVZ2JBM_pWESiIVcgYwpAszZgLrKNDjYF_O3-XB9DJptZR8S51hVzxtVHKWxoYI43xtVOOgbNN75Je5y13WtWdl7Ym0K4a7CFCVnbJidXUm-fMD9-vjD4VXQmyfeAYsIo6s011VdqU-up4Ikju"
              alt="Es Kopi Susu"
            />
          </div>
          <h3 class="bestseller-title">Es Kopi Susu Giat</h3>
          <p class="bestseller-count">Telah dipesan 120+ kali hari ini</p>
          <span class="bestseller-price">Rp 15.000</span>
        </div>
      </section>

      <!-- Categories -->
      <section class="categories-section">
        <div class="section-header">
          <h2 class="section-title">Kategori</h2>
          <button class="see-all-btn">Lihat Semua</button>
        </div>
        <div class="categories-scroll">
          <button
            v-for="cat in categories"
            :key="cat"
            class="category-pill"
            :class="{ 'category-pill--active': activeCategory === cat }"
            @click="filterCategory(cat)"
          >{{ cat }}</button>
        </div>
      </section>

      <!-- Recommendations -->
      <section class="recommendations-section">
        <div class="section-header">
          <h2 class="section-title">Rekomendasi untuk Kamu</h2>
          <p class="section-sub">Berdasarkan pesanan terakhirmu</p>
        </div>
        <div class="cards-grid">
          <div
            v-for="item in filteredItems"
            :key="item.id"
            class="food-card"
            @click="selectItem(item)"
          >
            <div class="food-card__img-wrap">
              <img :src="item.image" :alt="item.name" class="food-card__img" />
            </div>
            <div class="food-card__body">
              <div class="food-card__header">
                <h3 class="food-card__name">{{ item.name }}</h3>
                <span class="food-card__star">
                  <span class="material-symbols-outlined icon-star" style="font-variation-settings:'FILL' 1">star</span>
                </span>
              </div>
              <div class="food-card__meta">
                <span class="material-symbols-outlined icon-xs">timer</span>
                <span>{{ item.time }}</span>
                <span class="dot">•</span>
                <span>{{ item.vendor }}</span>
              </div>
              <div class="food-card__footer">
                <span class="food-card__price">Rp {{ item.price.toLocaleString('id-ID') }}</span>
                <button
                  class="add-btn"
                  @click.stop="addToCart(item)"
                >
                  <span class="material-symbols-outlined">add</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="bottom-nav glass-nav">
      <a
        v-for="item in bottomNavItems"
        :key="item.label"
        href="#"
        class="bottom-nav__item"
        :class="{ 'bottom-nav__item--active': item.active }"
      >
        <span class="material-symbols-outlined">{{ item.icon }}</span>
        <span class="bottom-nav__label">{{ item.label }}</span>
        <span v-if="item.badge" class="bottom-nav__badge">{{ item.badge }}</span>
      </a>
    </nav>

    <!-- Toast Notification -->
    <transition name="toast-fade">
      <div v-if="toast.visible" class="toast">
        <span class="material-symbols-outlined">check_circle</span>
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const userName = ref('Isyfi Salma')
const searchQuery = ref('')
const activeCategory = ref('Semua')

const navItems = ref([
  { label: 'Menu', active: true },
  { label: 'Orders', active: false },
  { label: 'Cart', active: false },
  { label: 'Profile', active: false },
])

const categories = ref(['Semua', 'Minuman', 'Makanan Berat', 'Camilan', 'Pastry', 'Sehat'])

const menuItems = ref([
  {
    id: 1,
    name: 'Nasi Goreng Spesial Kampus',
    time: '15-20 mnt',
    vendor: 'Kantin Pusat',
    price: 22000,
    category: 'Makanan Berat',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCx-2oPp-0H8hw1MHNf67FoOWyynBhABhdu8b0pL19ZyU8fEthJj3wK6NGOvRykILfBS7s2fMQ27qJE5pj4FJKUuioCm0FTc0RxCuC8d5OdfAZN4V6_6RHI6x83yXRPPZiHm4TZveDpyGeUDH1HclwGDqXQM4PZH5in4MqHXqneACL2kU2SNopk5f3L8FajhDNABw8U60nkFbHSVWcfW4vtsMJxZ_VD3puKhFzE8b50NrwmjP3w_CC-BiqUpDE1Sx-Z7BOOGu3rhmcs',
  },
  {
    id: 2,
    name: 'Donat Artisan Cokelat',
    time: '5 mnt',
    vendor: 'Bakery Giat',
    price: 12500,
    category: 'Pastry',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDidqJA2J6FHoh5g3YtPomI_IwDv11T3YyMzmxnzs3N091lWfZ-Zw186Mqf-CRCMXagmWzctg-NUNlFSD5pRIM3d3NDQToKFyRTVBPwi_ddVIBWV3y4ps3p9z4jZFqWjtfOyMhVXsnOvosSoCtn92OtfwtxBayQ23H69OqVPNZw46UMzT_UwQeqPhIelnqLiSyBHSwgkkbVkfKJrmJWRfxrnmzK5s61nkHBANzvMuE-4zJGIB3_94tFxO6c7nuhYKhQq48FjqDrXM71',
  },
  {
    id: 3,
    name: 'Green Bowl Chicken Salad',
    time: '10 mnt',
    vendor: 'Fit Kitchen',
    price: 28000,
    category: 'Sehat',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBoXdo4xeHJQSN8A5T9UhkB-dC2CAcT9Yg13FhyzDBFBX_rYHzKfr2CjZmiPwj66o1VwJgfjaqoHUMDlC8Y4qVNn3tXdH0BkHzcYCgkKrnMIPuDMO5zJ30a-Nq9kbcUGg-aFyCBbU9EEAsrW-EZCZVwh-JLZ6zpWr9IVmQISCEctk8Mj_wcU9fxxPIhMVZ4K9TZL0Bc0vivJV0DeNsCn-1RNTJYtxZl_oZ9hz0LkTQehyTYMm0m5Ezc62lpsnZ_7lVToOpkjPF8zqzn',
  },
  {
    id: 4,
    name: 'Premium Matcha Latte',
    time: '5-8 mnt',
    vendor: 'Brew Haven',
    price: 18000,
    category: 'Minuman',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCd219mvbGZhRt0eF-Qr-FzpRl99xOXF4VVKCZWQ1yRootLxyTQLuJP93q1cpEXbFSN2fTU_g_pUojlffXajboEzIL47hjdpd963LB5SA7OvQo_dkoGNqxJZmP7ueTZ5vSPjcsKxbu4brxs9TLQxUaM1E_1Rled86XF1tj7UmINTQaCGfujYXWHmhE0jjzhtyFqIxGBdVRJIYrQrfh_G8GVV188bVYe6RjAstsiD17mxDo-3d7g6DkPZb5bNMmaig3Gq1HxqF97o',
  },
])

const cart = ref([])

const bottomNavItems = ref([
  { label: 'Menu', icon: 'restaurant_menu', active: true },
  { label: 'Orders', icon: 'assignment', active: false },
  { label: 'Cart', icon: 'shopping_bag', active: false, badge: computed(() => cart.value.length || null) },
  { label: 'Profile', icon: 'person', active: false },
])

const toast = ref({ visible: false, message: '' })

const filteredItems = computed(() => {
  let items = menuItems.value
  if (activeCategory.value !== 'Semua') {
    items = items.filter(i => i.category === activeCategory.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    items = items.filter(i => i.name.toLowerCase().includes(q) || i.vendor.toLowerCase().includes(q))
  }
  return items
})

function filterCategory(cat) {
  activeCategory.value = cat
}

function addToCart(item) {
  cart.value.push(item)
  showToast(`${item.name} ditambahkan ke keranjang!`)
}

function goToCart() {
  showToast(`Keranjang: ${cart.value.length} item`)
}

function selectItem(item) {
  showToast(`Membuka detail ${item.name}…`)
}

function showToast(msg) {
  toast.value = { visible: true, message: msg }
  setTimeout(() => { toast.value.visible = false }, 2500)
}
</script>

<style scoped>
/* ========================================
   CSS VARIABLES & RESET
======================================== */
:root {
  --primary:           #570000;
  --primary-container: #800000;
  --on-primary:        #ffffff;
  --secondary:         #96473b;
  --surface:           #faf9f6;
  --surface-low:       #f4f3f1;
  --surface-container: #efeeeb;
  --surface-high:      #e9e8e5;
  --on-surface:        #1a1c1a;
  --on-surface-var:    #5a413d;
  --outline-var:       #e2bfb9;
  --radius-pill:       9999px;
  --radius-card:       1.25rem;
  --radius-btn:        0.75rem;
  --shadow-card:       0 12px 32px rgba(128, 0, 0, 0.07);
  --shadow-nav:        0 -8px 24px rgba(128, 0, 0, 0.05);
  --font-display:      'Manrope', sans-serif;
  --font-body:         'Plus Jakarta Sans', sans-serif;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

/* ========================================
   APP WRAPPER
======================================== */
.app-wrapper {
  font-family: var(--font-body);
  background-color: var(--surface);
  color: var(--on-surface);
  min-height: 100vh;
  padding-bottom: 5.5rem; /* space for bottom nav on mobile */
}

@media (min-width: 768px) {
  .app-wrapper { padding-bottom: 0; }
}

/* ========================================
   GLASS NAV UTILITY
======================================== */
.glass-nav {
  background-color: rgba(250, 249, 246, 0.82);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}

/* ========================================
   TOP NAVIGATION
======================================== */
.sticky-nav {
  position: sticky;
  top: 0;
  z-index: 50;
  border-bottom: 1px solid var(--outline-var);
}

.nav-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0.875rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.nav-left   { display: flex; align-items: center; gap: 2rem; }
.nav-right  { display: flex; align-items: center; gap: 1rem; }

.brand-name {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--primary);
  white-space: nowrap;
}

.desktop-menu {
  display: none;
  gap: 1.5rem;
}
@media (min-width: 768px) {
  .desktop-menu { display: flex; }
}

.nav-link {
  text-decoration: none;
  color: var(--on-surface-var);
  font-weight: 500;
  font-size: 0.9rem;
  transition: color 0.2s;
}
.nav-link:hover,
.nav-link--active {
  color: var(--primary);
  font-weight: 700;
  border-bottom: 2px solid var(--primary);
  padding-bottom: 2px;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--surface-low);
  border-radius: var(--radius-pill);
  padding: 0.5rem 1.1rem;
  width: 17rem;
}
.desktop-search { display: none; }
@media (min-width: 768px) { .desktop-search { display: flex; } }

.search-input {
  background: transparent;
  border: none;
  outline: none;
  font-size: 0.875rem;
  font-family: var(--font-body);
  color: var(--on-surface);
  width: 100%;
}
.search-input::placeholder { color: var(--on-surface-var); }

.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: var(--radius-pill);
  color: var(--on-surface-var);
  transition: background 0.2s;
  display: flex;
}
.icon-btn:hover { background: var(--surface-high); }

.avatar {
  width: 2.4rem;
  height: 2.4rem;
  border-radius: var(--radius-pill);
  overflow: hidden;
  border: 1.5px solid var(--outline-var);
  margin-left: 0.25rem;
}
.avatar img { width: 100%; height: 100%; object-fit: cover; }

/* ========================================
   MAIN CONTENT
======================================== */
.main-content {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 3rem;
}
@media (min-width: 768px) {
  .main-content { padding: 2.5rem 1.5rem; }
}

/* ========================================
   HERO / GREETING
======================================== */
.hero-section {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
@media (min-width: 768px) {
  .hero-section {
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
  }
}

.greeting-sub {
  color: var(--on-surface-var);
  font-weight: 500;
  font-size: 0.9rem;
  letter-spacing: 0.03em;
}
.greeting-name {
  font-family: var(--font-display);
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 800;
  color: var(--primary);
  line-height: 1.1;
}

.mobile-search {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: #fff;
  border-radius: var(--radius-card);
  padding: 1rem 1.25rem;
  box-shadow: var(--shadow-card);
}
@media (min-width: 768px) { .mobile-search { display: none; } }

/* ========================================
   BENTO GRID
======================================== */
.bento-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
}
@media (min-width: 768px) {
  .bento-grid { grid-template-columns: 2fr 1fr; }
}

/* Main Banner */
.bento-main {
  position: relative;
  overflow: hidden;
  background: var(--surface-low);
  border-radius: var(--radius-card);
  padding: 2rem;
  min-height: 280px;
  display: flex;
  align-items: center;
}
@media (min-width: 640px) { .bento-main { min-height: 320px; } }

.bento-main__content {
  position: relative;
  z-index: 2;
  max-width: 60%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
@media (max-width: 480px) { .bento-main__content { max-width: 100%; } }

.weather-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.weather-label {
  color: var(--secondary);
  font-weight: 700;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}
.icon-weather { font-size: 2rem; color: var(--secondary); }

.bento-main__title {
  font-family: var(--font-display);
  font-size: clamp(1.1rem, 2.5vw, 1.65rem);
  font-weight: 700;
  color: var(--on-surface);
  line-height: 1.3;
}
.bento-main__desc {
  font-size: 0.88rem;
  color: var(--on-surface-var);
  line-height: 1.6;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #570000 0%, #800000 100%);
  color: #fff;
  border: none;
  border-radius: var(--radius-pill);
  padding: 0.75rem 1.75rem;
  font-weight: 700;
  font-size: 0.9rem;
  font-family: var(--font-body);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  width: fit-content;
}
.cta-btn:hover { transform: scale(1.04); box-shadow: 0 6px 20px rgba(87,0,0,0.3); }

.bento-main__image {
  position: absolute;
  right: -2rem;
  bottom: -1rem;
  width: 52%;
  height: 110%;
  opacity: 0.92;
  pointer-events: none;
}
.bento-main__drink {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transform: rotate(12deg);
}
@media (max-width: 480px) { .bento-main__image { display: none; } }

/* Bestseller Card */
.bento-side {
  background: #fff;
  border-radius: var(--radius-card);
  padding: 1.75rem;
  box-shadow: var(--shadow-card);
  border: 1px solid var(--outline-var);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 0.75rem;
}

.bestseller-label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--on-surface-var);
}
.bestseller-img-wrap {
  width: 7rem;
  height: 7rem;
  border-radius: var(--radius-pill);
  overflow: hidden;
  background: var(--surface-container);
  padding: 0.25rem;
}
.bestseller-img-wrap img { width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-pill); }
.bestseller-title { font-weight: 700; font-size: 1.1rem; }
.bestseller-count { font-size: 0.82rem; color: var(--on-surface-var); }
.bestseller-price { font-weight: 700; font-size: 1.15rem; color: var(--primary); }

/* ========================================
   CATEGORIES
======================================== */
.categories-section { display: flex; flex-direction: column; gap: 1rem; }

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.section-title {
  font-family: var(--font-display);
  font-size: 1.35rem;
  font-weight: 700;
}
.section-sub { font-size: 0.82rem; color: var(--on-surface-var); font-style: italic; }
.see-all-btn {
  background: none;
  border: none;
  color: var(--primary);
  font-weight: 700;
  font-size: 0.88rem;
  cursor: pointer;
  font-family: var(--font-body);
}

.categories-scroll {
  display: flex;
  gap: 0.65rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
  scrollbar-width: none;
}
.categories-scroll::-webkit-scrollbar { display: none; }

.category-pill {
  background: var(--surface-high);
  color: var(--on-surface-var);
  border: none;
  border-radius: var(--radius-pill);
  padding: 0.65rem 1.5rem;
  font-weight: 700;
  font-size: 0.875rem;
  font-family: var(--font-body);
  white-space: nowrap;
  cursor: pointer;
  transition: background 0.2s, color 0.2s, transform 0.15s;
}
.category-pill:hover { background: var(--outline-var); }
.category-pill--active {
  background: var(--primary);
  color: var(--on-primary);
  transform: scale(1.05);
  box-shadow: 0 4px 14px rgba(87, 0, 0, 0.25);
}

/* ========================================
   RECOMMENDATIONS
======================================== */
.recommendations-section { display: flex; flex-direction: column; gap: 1rem; }

.cards-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
}
@media (min-width: 480px) { .cards-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .cards-grid { grid-template-columns: repeat(4, 1fr); } }

.food-card {
  background: #fff;
  border-radius: var(--radius-card);
  overflow: hidden;
  box-shadow: var(--shadow-card);
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}
.food-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(128, 0, 0, 0.1);
}

.food-card__img-wrap {
  height: 10rem;
  overflow: hidden;
}
.food-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}
.food-card:hover .food-card__img { transform: scale(1.08); }

.food-card__body {
  padding: 1.1rem 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.food-card__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.5rem;
}
.food-card__name {
  font-weight: 700;
  font-size: 0.975rem;
  line-height: 1.3;
  flex: 1;
}
.food-card__star {
  background: var(--surface-low);
  border-radius: var(--radius-pill);
  padding: 0.25rem;
  display: flex;
}
.icon-star { font-size: 0.9rem; color: var(--primary); }

.food-card__meta {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.8rem;
  color: var(--on-surface-var);
}
.icon-xs { font-size: 0.9rem; }
.dot { opacity: 0.35; }

.food-card__footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.25rem;
}
.food-card__price {
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--primary);
}

.add-btn {
  background: var(--surface-low);
  border: none;
  border-radius: var(--radius-pill);
  width: 2.1rem;
  height: 2.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  cursor: pointer;
  transition: background 0.2s, color 0.2s, transform 0.15s;
}
.add-btn:hover {
  background: var(--primary);
  color: #fff;
  transform: scale(1.1);
}

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
  padding: 0.75rem 1rem 1.4rem;
  border-top: 1px solid var(--outline-var);
  border-radius: 1.5rem 1.5rem 0 0;
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
  position: relative;
  font-size: 0.62rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  transition: color 0.2s;
}
.bottom-nav__item--active { color: var(--primary); }
.bottom-nav__item--active::after {
  content: '';
  display: block;
  width: 0.3rem;
  height: 0.3rem;
  border-radius: 50%;
  background: var(--primary);
  margin-top: 0.1rem;
}

.bottom-nav__label { font-size: 0.6rem; }

.bottom-nav__badge {
  position: absolute;
  top: -0.2rem;
  right: -0.3rem;
  background: var(--primary);
  color: #fff;
  font-size: 0.55rem;
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

/* ========================================
   ICON HELPERS
======================================== */
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  font-size: 1.4rem;
  line-height: 1;
}
.icon-sm { font-size: 1.1rem; }
.icon-primary { color: var(--primary); }

/* ========================================
   TOAST NOTIFICATION
======================================== */
.toast {
  position: fixed;
  bottom: 6rem;
  left: 50%;
  transform: translateX(-50%);
  background: var(--on-surface);
  color: #fff;
  padding: 0.75rem 1.5rem;
  border-radius: var(--radius-pill);
  font-size: 0.875rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  z-index: 100;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
  white-space: nowrap;
}
@media (min-width: 768px) { .toast { bottom: 2rem; } }

.toast-fade-enter-active,
.toast-fade-leave-active { transition: opacity 0.3s, transform 0.3s; }
.toast-fade-enter-from,
.toast-fade-leave-to { opacity: 0; transform: translateX(-50%) translateY(1rem); }
</style>