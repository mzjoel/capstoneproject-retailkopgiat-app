<template>
  <div class="bg-background text-on-surface min-h-screen pb-24 md:pb-0">

    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 glass-nav">
      <div class="flex justify-between items-center px-4 md:px-6 py-3 md:py-4 w-full max-w-7xl mx-auto">

        <!-- Logo + Desktop Links -->
        <div class="flex items-center gap-6 md:gap-8">
          <Link :href="route('dashboard')" class="text-lg md:text-xl font-black tracking-tight" style="color: #800000; font-family: 'Manrope', sans-serif;">
            Koperasi Giat
          </Link>
          <div class="hidden md:flex items-center gap-2">
            <Link
              v-for="link in navLinks"
              :key="link.label"
              :href="link.url"
              :class="[
                'font-bold px-3 py-1 rounded-lg transition-colors text-sm',
                link.active
                  ? 'text-primary bg-surface-container-high'
                  : 'text-on-surface-variant hover:bg-surface-container-high'
              ]"
              style="font-family: 'Manrope', sans-serif;"
            >
              {{ link.label }}
            </Link>
          </div>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-2 md:gap-4">
          <template v-if="authUser">
            <!-- Mobile search toggle -->
            <button
              class="md:hidden p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors"
              @click="showMobileSearch = !showMobileSearch"
            >
              <span class="material-symbols-outlined">search</span>
            </button>

            <Link :href="route('cart')" class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors active:scale-95 relative">
              <span class="material-symbols-outlined">shopping_cart</span>
              <span
                v-if="cart.count > 0"
                class="absolute -top-1 -right-1 bg-primary text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center font-bold"
              >{{ cart.count }}</span>
            </Link>

            <button class="hidden md:block p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors active:scale-95">
              <span class="material-symbols-outlined">notifications</span>
            </button>

            <div class="flex items-center group relative cursor-pointer">
              <div class="w-8 h-8 rounded-full overflow-hidden bg-surface-container-high">
                <img
                  :src="displayAvatar"
                  :alt="displayName"
                  class="w-full h-full object-cover"
                />
              </div>
              <!-- Dropdown Logout -->
              <div class="absolute right-0 top-10 w-48 bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant/10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-[60] p-2">
                <button @click="logout" class="w-full text-left px-4 py-3 rounded-lg hover:bg-error/10 text-error flex items-center gap-3 transition-colors">
                  <span class="material-symbols-outlined text-sm">logout</span>
                  <span class="font-bold text-sm">Logout</span>
                </button>
              </div>
            </div>
          </template>
          <template v-else>
            <Link :href="route('login')" class="text-on-surface-variant hover:text-primary font-bold text-sm">Login</Link>
            <Link :href="route('register')" class="bg-primary text-white px-6 py-2 rounded-full font-bold text-sm shadow-lg hover:scale-105 transition-all">Sign Up</Link>
          </template>
        </div>
      </div>

      <!-- Mobile Search Expandable -->
      <div
        v-show="showMobileSearch"
        class="md:hidden px-4 pb-3"
      >
        <div class="flex items-center bg-surface-container-low px-4 py-2.5 rounded-full">
          <span class="material-symbols-outlined text-on-surface-variant text-lg mr-2">search</span>
          <input
            v-model="searchQuery"
            class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-on-surface-variant"
            placeholder="Cari menu segar..."
            type="text"
            autofocus
          />
        </div>
      </div>

      <div class="bg-outline-variant/20 h-px w-full"></div>
    </nav>

    <main class="pt-24 md:pt-28 pb-32 md:pb-12 max-w-7xl mx-auto px-6 space-y-12">

      <!-- Hero & Greeting -->
      <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
          <p class="text-on-surface-variant font-medium tracking-wide">Selamat datang kembali,</p>
          <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-primary">
            Halo, {{ displayName }}!
          </h1>
        </div>
        <!-- Mobile Search -->
        <div class="md:hidden w-full">
          <div class="flex items-center bg-surface-container-lowest px-5 py-4 rounded-xl shadow-[0_12px_32px_rgba(128,0,0,0.04)]">
            <span class="material-symbols-outlined text-primary mr-3">search</span>
            <input
              v-model="searchQuery"
              class="bg-transparent border-none focus:ring-0 text-base w-full"
              placeholder="Lapar mau makan apa?"
              type="text"
            />
          </div>
        </div>
      </header>

      <!-- Weather Based Recommendation (Editorial Bento Style) -->
      <section class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Weather Banner -->
        <div class="md:col-span-8 relative overflow-hidden bg-surface-container-low rounded-xl p-8 flex flex-col justify-between min-h-[320px]">
          <div class="z-10 max-w-md space-y-4">
            <div class="flex items-center gap-3">
              <span
                class="material-symbols-outlined text-secondary text-4xl"
                style="font-variation-settings: 'FILL' 1"
              >wb_sunny</span>
              <span class="text-secondary font-bold uppercase tracking-widest text-sm">
                Pas buat cuaca panas gini
              </span>
            </div>
            <h2 class="text-3xl font-bold text-on-surface leading-tight">
              Kampus lagi terik banget nih, yuk ademin pake yang segar-segar!
            </h2>
            <p class="text-on-surface-variant">
              Suhu mencapai 32°C di sekitar kampus. Hilangkan dahaga dengan koleksi minuman dingin pilihan kami.
            </p>
            <button class="cta-gradient text-white px-8 py-3 rounded-full font-bold inline-flex items-center gap-2 mt-4 hover:scale-105 transition-transform">
              Lihat Menu Segar
              <span class="material-symbols-outlined">arrow_forward</span>
            </button>
          </div>
          <div class="absolute right-[-40px] bottom-[-20px] w-1/2 h-full opacity-90">
            <img
              :src="weatherBanner.image"
              alt="Iced tea glass"
              class="w-full h-full object-contain transform rotate-12"
            />
          </div>
        </div>

        <!-- Bestseller Card -->
        <div class="md:col-span-4 bg-surface-container-lowest rounded-xl p-6 shadow-[0_12px_32px_rgba(128,0,0,0.06)] flex flex-col items-center text-center justify-center space-y-4 border border-outline-variant/10">
          <span class="text-sm font-bold text-on-surface-variant uppercase tracking-tighter">
            Terlaris Saat Ini
          </span>
          <div class="w-32 h-32 rounded-full overflow-hidden bg-surface-container-low p-2">
            <img
              :src="bestseller.image"
              :alt="bestseller.name"
              class="w-full h-full object-cover rounded-full"
            />
          </div>
          <h3 class="font-bold text-xl">{{ bestseller.name }}</h3>
          <p class="text-on-surface-variant text-sm">{{ bestseller.orderCount }}</p>
          <span class="text-primary font-bold text-lg">{{ bestseller.price }}</span>
        </div>
      </section>

      <!-- Categories -->
      <!-- <section class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-on-surface">Kategori</h2>
          <button class="text-primary font-bold text-sm">Lihat Semua</button>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
          <button
            v-for="cat in categories"
            :key="cat"
            @click="activeCategory = cat"
            :class="[
              'px-8 py-3 rounded-full font-bold whitespace-nowrap transition-colors',
              activeCategory === cat
                ? 'bg-primary text-on-primary scale-105 shadow-lg'
                : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest'
            ]"
          >
            {{ cat }}
          </button>
        </div>
      </section> -->

      <!-- Recommendations -->
      <section class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-on-surface">Rekomendasi untuk Kamu</h2>
        </div>
        <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="i in 4" :key="i" class="animate-pulse bg-surface-container-low rounded-xl h-64"></div>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="item in menuItems"
            :key="item.id"
            class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_12px_32px_rgba(128,0,0,0.06)] group hover:-translate-y-1 transition-all duration-300"
          >
            <div class="h-40 overflow-hidden">
              <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
              />
            </div>
            <div class="p-6 space-y-4">
              <div class="flex justify-between items-start">
                <h3 class="font-bold text-lg leading-tight">{{ item.name }}</h3>
                <span class="bg-surface-container-low p-1 rounded-full">
                  <span
                    class="material-symbols-outlined text-sm text-primary"
                    style="font-variation-settings: 'FILL' 1"
                  >star</span>
                </span>
              </div>
              <div class="flex items-center gap-2 text-on-surface-variant text-sm">
                <span class="material-symbols-outlined text-sm">category</span>
                <span>{{ item.category.name }}</span>
              </div>
              <div class="flex justify-between items-center pt-2">
                <span class="text-primary font-bold text-xl">{{ item.formattedPrice }}</span>
                <button
                  @click="addToCart(item)"
                  class="p-2 bg-surface-container-low rounded-full text-primary hover:bg-primary hover:text-white transition-colors"
                >
                  <span class="material-symbols-outlined">add</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <!-- BottomNavBar (Mobile Only) -->
    <nav class="md:hidden glass-nav fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 rounded-t-3xl shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <Link
        v-for="navItem in bottomNav"
        :key="navItem.label"
        :href="navItem.url"
        :class="[
          'flex flex-col items-center justify-center relative transition-transform active:scale-90',
          navItem.active ? 'text-primary' : 'text-on-surface-variant'
        ]"
      >
        <span class="material-symbols-outlined">{{ navItem.icon }}</span>
        <span class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ navItem.label }}</span>
        <span
          v-if="navItem.badge"
          class="absolute -top-1 -right-1 bg-primary text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center"
        >{{ navItem.badge }}</span>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'

const page = usePage()

// State
const searchQuery = ref('')
const activeCategory = ref('Semua')
const menuItems = ref([])
const isLoading = ref(true)
const showMobileSearch = ref(false)

// User
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})

// Nav links
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]

// Weather banner
const weatherBanner = {
  image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA-irJyLepG2118W29nDbnRNY4OuQCtruZHNYZhqUb1xl9KLLlO9DM_qJQzYmpB-sDTfTD5Al3PhcAbocKb8dIdLDmcfzbHc33DAUPovntJxuuwmIac5ujXmEXbxx2lT0RNzDebBnV7rzVFkTVxletS4ruzZZ6lG2Qy0-4p6FMjAdNT8B5ni1xB7KR3IXlZ7q2Auc3N8xXHcsEIDMUnefrls00p5sz3XEgRbrrOt6AynHTBVZ4AfnG9ZQbQvp4WEqtdvwYg3jRy-DvS',
}

// Bestseller
const bestseller = {
  name: 'Es Kopi Susu Giat',
  orderCount: 'Telah dipesan 120+ kali hari ini',
  price: 'Rp 15.000',
  image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAenucty_ChILpDrB_Po2IaxtAJiF7fbMZJL8ThqPDmkDXYMs8dOOkF5neuXEFJSbpU2j5v0E8fQ9C-ECeagfy-pWJtWFOCaltLChTRnSHVou9RdnbOjFOmhhXQArMVZ2JBM_pWESiIVcgYwpAszZgLrKNDjYF_O3-XB9DJptZR8S51hVzxtVHKWxoYI43xtVOOgbNN75Je5y13WtWdl7Ym0K4a7CFCVnbJidXUm-fMD9-vjD4VXQmyfeAYsIo6s011VdqU-up4Ikju',
}

// Categories
const categories = ['Semua', 'Minuman', 'Makanan Berat', 'Camilan', 'Pastry', 'Sehat']

// Methods
const fetchProducts = async () => {
  try {
    isLoading.value = true
    const response = await window.axios.get('/api/v1/products')
    // Ambil maksimal 4 data
    menuItems.value = response.data.data.slice(0, 4).map(item => ({
      ...item,
      image: item.image || `https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80`,
      formattedPrice: new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(item.price)
    }))
  } catch (error) {
    console.error('Failed to fetch products:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})

// Bottom nav
const bottomNav = computed(() => [
  { label: 'Beranda', icon: 'home', url: route('dashboard'), active: true },
  { label: 'Menu', icon: 'restaurant_menu', url: route('products'), active: false },
  { label: 'Cart', icon: 'shopping_cart', url: route('cart'), active: false, badge: cart.count > 0 ? cart.count : null },
  { label: 'Profil', icon: 'person', url: '#', active: false },
])

// Methods
const logout = () => {
  router.post(route('logout'))
}

function addToCart(item) {
  cart.add(item)
  console.log('Added to cart:', item.name)
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #faf9f6;
}

h1, h2, h3, .display-font {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.glass-nav {
  background-color: rgba(250, 249, 246, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.cta-gradient {
  background: linear-gradient(135deg, #570000 0%, #800000 100%);
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>