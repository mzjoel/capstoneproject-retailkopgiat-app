<template>
  <div class="bg-background text-on-surface font-body min-h-screen">

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

            <Link :href="route('product.wishlist')" class="hidden md:block p-2 text-primary bg-surface-container-high rounded-full transition-colors active:scale-95">
              <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">favorite</span>
            </Link>

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

    <!-- Header -->
    <div class="pt-28 pb-10 px-6 max-w-7xl mx-auto flex items-center gap-4">
      <Link :href="route('dashboard')" class="p-2 bg-surface-container-low rounded-full hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined">arrow_back</span>
      </Link>
      <div>
        <h1 class="text-4xl font-bold text-primary mb-2">
          Wishlist Saya
        </h1>
        <p class="text-on-surface-variant">
          Temukan kembali favorit Anda
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="px-6 max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">
      <div v-for="i in 3" :key="i" class="h-96 bg-surface-container-low rounded-xl animate-pulse"></div>
    </div>
    
    <!-- Empty State -->
    <div v-else-if="wishlist.length === 0" class="text-center py-32 px-6">
      <span class="material-symbols-outlined text-8xl text-outline mb-6 block">favorite_border</span>
      <h2 class="text-2xl font-bold mb-2">Belum ada wishlist</h2>
      <p class="text-on-surface-variant mb-8">Kamu belum menambahkan produk apapun ke wishlist.</p>
      <Link :href="route('products')" class="bg-gradient-to-r from-primary to-[#a00000] text-white px-8 py-3 rounded-xl font-bold inline-block hover:scale-105 transition-transform">
        Mulai Eksplorasi
      </Link>
    </div>

    <!-- Wishlist Grid -->
    <div v-else class="px-6 max-w-7xl mx-auto pb-32 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">

      <div
        v-for="item in wishlist"
        :key="item.id"
        class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group flex flex-col border border-outline-variant/10"
      >
        <Link 
            :href="route('products.detail', item.id)" 
            class="block flex-grow"
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
                <span class="font-bold text-secondary whitespace-nowrap">{{ item.price }}</span>
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
              @click="toggleFavorite(item)"
              :class="[
                'p-2.5 md:p-3 rounded-xl flex items-center justify-center transition-colors duration-200 active:scale-95 border',
                item.isFavorite 
                  ? 'bg-surface-container-lowest text-red-500 border-outline-variant/20 hover:bg-red-50'
                  : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high border-outline-variant/20'
              ]"
            >
              <span class="material-symbols-outlined" :style="item.isFavorite ? 'font-variation-settings: \'FILL\' 1' : ''">favorite</span>
            </button>
          </div>
      </div>

    </div>

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
import { ref, onMounted, computed, onBeforeUnmount } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { cart } from '@/Stores/cart'

const page = usePage()
const authUser = computed(() => page.props.auth.user)
const wishlist = ref([])
const isLoading = ref(true)

const showMobileSearch = ref(false)
const searchQuery = ref('')
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: false },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]
const bottomNav = computed(() => [
  { label: 'Beranda', icon: 'home', url: route('dashboard'), active: false },
  { label: 'Menu', icon: 'restaurant_menu', url: route('products'), active: false },
  { label: 'Cart', icon: 'shopping_cart', url: route('cart'), active: false, badge: cart.count > 0 ? cart.count : null },
  { label: 'Profil', icon: 'person', url: '#', active: false },
])
const logout = () => {
  router.post(route('logout'))
}

const fetchWishlist = async () => {
  try {
    isLoading.value = true
    const response = await window.axios.get('/api/v1/user/wishlist?include_products=true')
    if (response.data && response.data.products) {
      wishlist.value = response.data.products
    }
  } catch (error) {
    console.error('Failed to fetch wishlist products:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchWishlist()
})

const interactionsBatch = ref([])

function logInteraction(productId, type, payloadData = {}){
  if(!authUser.value) return;
  interactionsBatch.value.push({
    product_id: productId,
    type: type,
    payload: {
      ...payloadData,
      timestamp: new Date().toISOString()
    }
  });
}

function getCsrfToken(){
  const name = "XSRF-TOKEN=";
  const decodedCookie = decodeURIComponent(document.cookie);
  const ca = decodedCookie.split(';');
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') { c = c.substring(1); }
    if (c.indexOf(name) == 0) { return c.substring(name.length, c.length); }
  }
  return "";
}

function sendTrackingData(){
  if(interactionsBatch.value.length === 0 || !authUser.value) return;

  const url = '/api/v1/user/interactions';
  const body = JSON.stringify({ interactions: interactionsBatch.value});
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken(),
    },
    body: body,
    credentials: 'include'
  }).catch(err => {
    console.error('Failed to send tracking data:', err);
  });
  interactionsBatch.value = [];
}

onBeforeUnmount(() => {
  sendTrackingData()
})

const toggleFavorite = (item) => {
  item.isFavorite = !item.isFavorite
  if (item.isFavorite) {
    logInteraction(item.id, 'wishlist', { action_source: 'favorite_page' })
  } else {
    logInteraction(item.id, 'unwishlist', { action_source: 'favorite_page' })
  }
  sendTrackingData()
}

const addToCart = (item) => {
  cart.add(item)
  logInteraction(item.id, 'add_to_cart_wishlist', {
    action_source: 'favorite_page'
  })
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