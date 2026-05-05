<template>
  <div class="text-on-surface min-h-screen" style="background-color: #faf9f6;">

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

            <div class="flex items-center group relative cursor-pointer ml-1">
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

    <!-- Main Content -->
    <main class="pt-24 md:pt-28 pb-32 md:pb-12 px-4 md:px-6 max-w-7xl mx-auto">

      <!-- Header -->
      <header class="mb-8 md:mb-12">
        <h1
          class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 md:mb-3"
          style="color: #800000; font-family: 'Manrope', sans-serif;"
        >
          Daftar Menu 
        </h1>
        <p class="text-on-surface-variant text-base md:text-lg max-w-2xl leading-relaxed">
          Pilihan favorit untuk mendinginkan harimu di kampus.
        </p>
      </header>

      <!-- Category Pills -->
      <div v-if="isCategoriesLoading" class="flex gap-3 mb-10 overflow-x-auto pb-2 no-scrollbar">
        <div v-for="i in 5" :key="i" class="h-10 w-24 bg-surface-container-high rounded-full animate-pulse"></div>
      </div>
      <div v-else class="flex gap-2 md:gap-3 mb-8 md:mb-10 overflow-x-auto pb-2 no-scrollbar">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="selectCategory(cat)"
          :class="[
            'px-5 md:px-6 py-2 md:py-2.5 rounded-full font-semibold whitespace-nowrap text-sm transition-all duration-200',
            activeCategory === cat
              ? 'bg-primary text-on-primary scale-105 shadow-md'
              : 'bg-surface-container-high text-on-surface-variant hover:opacity-80'
          ]"
        >
          {{ cat }}
        </button>
      </div>

      <!-- Product Grid -->
      <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">
        <div v-for="i in 6" :key="i" class="bg-surface-container-lowest rounded-xl h-80 animate-pulse"></div>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group flex flex-col border border-outline-variant/10"
        >
          <Link 
            :href="route('products.detail', item.id)" 
            class="block flex-grow"
            @click="trackProductClick(item)"
          >
            <!-- Image -->
            <div class="relative aspect-[4/3] overflow-hidden">
              <img
                :src="item.image_url || item.image"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
            </div>

            <!-- Info -->
            <div class="p-4 md:p-6 flex flex-col">
              <div class="flex justify-between items-start mb-3 md:mb-4 gap-2">
                <h3 class="font-bold text-lg md:text-xl text-on-surface leading-tight" style="font-family: 'Manrope', sans-serif;">
                  {{ item.name }}
                </h3>
                <span class="font-bold text-secondary whitespace-nowrap">Rp {{ item.price }}</span>
              </div>
              <p class="text-sm text-on-surface-variant mb-6 md:mb-8 line-clamp-2 leading-relaxed">
                {{ item.description || 'Deskripsi menu lezat ini.' }}
              </p>
            </div>
          </Link>
          
          <div class="px-4 pb-4 md:px-6 md:pb-6 mt-auto flex gap-2">
            <button
              @click="addToCart(item)"
              class="flex-1 bg-gradient-to-r from-primary to-[#a00000] text-white py-2.5 md:py-3 rounded-xl font-bold flex items-center justify-center gap-2 active:scale-95 transition-transform duration-200 text-sm md:text-base"
            >
              <span class="material-symbols-outlined text-lg">add_shopping_cart</span>
              Tambah
            </button>
            <button
              @click="addToWishlist(item)"
              :class="[
                'p-2.5 md:p-3 rounded-xl flex items-center justify-center transition-colors duration-200 active:scale-95 border',
                wishlistedItems.includes(item.id) 
                  ? 'bg-surface-container-lowest text-red-500 border-outline-variant/20 hover:bg-red-50'
                  : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high border-outline-variant/20'
              ]"
            >
              <span class="material-symbols-outlined" :style="wishlistedItems.includes(item.id) ? 'font-variation-settings: \'FILL\' 1' : ''">favorite</span>
            </button>
          </div>
        </div>
      </div>
      <!-- Empty State -->
      <div v-if="filteredItems.length === 0" class="text-center py-20">
        <span class="material-symbols-outlined text-outline text-6xl mb-4 block">search_off</span>
        <p class="text-on-surface-variant font-medium">Tidak ada menu ditemukan.</p>
      </div>

    </main>

    <!-- Bottom Navigation Bar (Mobile only) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full flex justify-around items-center px-4 pt-3 pb-6 glass-nav z-50 rounded-t-3xl shadow-[0_-4px_20px_rgba(128,0,0,0.06)]">
      <Link
        v-for="navItem in bottomNav"
        :key="navItem.label"
        :href="navItem.url"
        :class="[
          'flex flex-col items-center justify-center text-[10px] font-medium transition-all active:scale-90 duration-300 relative',
          navItem.active ? 'text-primary' : 'text-on-surface-variant'
        ]"
      >
        <span class="material-symbols-outlined mb-1">{{ navItem.icon }}</span>
        {{ navItem.label }}
        <span v-if="navItem.active" class="w-1 h-1 bg-primary rounded-full mt-0.5"></span>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import { route } from 'ziggy-js'

const page = usePage()

// State
const searchQuery = ref('')
const activeCategory = ref('Semua Menu')
const showMobileSearch = ref(false)
const menuItems = ref([])
const apiCategories = ref([])
const isLoading = ref(true)
const isCategoriesLoading = ref(true)
const wishlistedItems = ref([])

// User
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})

// Nav links desktop
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: false },
  { label: 'Menu', url: route('products'), active: true },
  { label: 'Pesanan', url: route('transaction.history'), active: false },
]

// Categories
const categories = computed(() => ['Semua Menu', ...apiCategories.value.map(c => c.name)])

// Bottom nav (mobile)
const bottomNav = [
  { label: 'Beranda', icon: 'home', url: route('dashboard'), active: false },
  { label: 'Menu', icon: 'restaurant_menu', url: route('products'), active: true },
  { label: 'Pesanan', icon: 'receipt_long', url: '#', active: false },
  { label: 'Profil', icon: 'person', url: '#', active: false },
]

// Methods
const fetchProducts = async () => {
  try {
    isLoading.value = true
    const response = await window.axios.get('/api/v1/products')
    menuItems.value = response.data.data.map(item => ({
      ...item,
      image: item.image || `https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80`,
      formattedPrice: new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(item.price),
      description: item.description || item.ingredients || 'Nikmati kesegaran menu pilihan kami yang dibuat dengan bahan berkualitas.'
    }))
  } catch (error) {
    console.error('Failed to fetch products:', error)
  } finally {
    isLoading.value = false
  }
}

const fetchCategories = async () => {
  try {
    isCategoriesLoading.value = true
    const response = await window.axios.get('/api/v1/categories')
    apiCategories.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  } finally {
    isCategoriesLoading.value = false
  }
}

const logout = () => {
  router.post(route('logout'))
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
  fetchWishlist()
  entryTime = Date.now();
})

onBeforeUnmount(() => {
    sendTrackingData();
});

function getCsrfToken() {
    const match = document.cookie.match(new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'));
    return match ? decodeURIComponent(match[2]) : "";
}

const interactionsBatch = ref([]);
let entryTime = 0;

function logInteraction(productId, type, payloadData = {}) {
    if (!authUser.value) return; 
    interactionsBatch.value.push({
        product_id: productId, 
        type: type, 
        payload: { 
            ...payloadData, 
            timestamp: new Date().toISOString() 
        }
    });
}

function sendTrackingData() {
    if (interactionsBatch.value.length === 0 || !authUser.value) return;
    
    const url = '/api/v1/user/interactions';
    const body = JSON.stringify({ interactions: interactionsBatch.value });

    fetch(url, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-XSRF-TOKEN': getCsrfToken()
        },
        body: body,
        keepalive: true,
        credentials: 'include'
    }).catch(() => { /* silent fail */ });

    interactionsBatch.value = [];
}

function formatLabel(text) {
    if (!text) return 'unknown';
    
    if (typeof text === 'object' && text.name) {
        text = text.name;
    } else if (typeof text !== 'string') {
        text = String(text);
    }

    return text.toLowerCase().replace(/[^a-z0-9]/g, '_');
}

function trackProductClick(item) {
    const categoryName = typeof item.category === 'object' ? item.category.name : item.category;
    
    logInteraction(item.id, 'click_product_catalog', {
        category: formatLabel(categoryName || 'unknown')
    });
}

function addToCart(item) {
    cart.add(item);
    
    logInteraction(item.id, 'add_to_cart_catalog', {
        action_source: 'catalog_grid'
    });
}

function selectCategory(cat) {
    activeCategory.value = cat;
}

async function fetchWishlist() {
  if (!authUser.value) return;
  try {
    const response = await window.axios.get('/api/v1/user/wishlist');
    if (response.data && response.data.data) {
      wishlistedItems.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to fetch wishlist:', error);
  }
}

function addToWishlist(item){
  if (wishlistedItems.value.includes(item.id)) {
    wishlistedItems.value = wishlistedItems.value.filter(id => id !== item.id);
    logInteraction(item.id, 'unwishlist', {
      action_source: 'catalog_grid'
    });
  } else {
    wishlistedItems.value.push(item.id);
    logInteraction(item.id, 'wishlist', {
      action_source: 'catalog_grid'
    });
  }
  sendTrackingData(); // Instantly track it
}

// Computed: filter by category and search
const filteredItems = computed(() => {
  return menuItems.value.filter(item => {
    const matchCategory =
      activeCategory.value === 'Semua Menu' || item.category.name === activeCategory.value
    const matchSearch =
      !searchQuery.value ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchCategory && matchSearch
  })
})

</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #faf9f6;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.glass-nav {
  background-color: rgba(250, 249, 246, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.primary-gradient {
  background: linear-gradient(135deg, #570000 0%, #800000 100%);
}

.product-shadow {
  box-shadow: 0 12px 32px rgba(128, 0, 0, 0.06);
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>