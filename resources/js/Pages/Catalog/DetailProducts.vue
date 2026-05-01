<template>
  <div class="bg-background text-on-surface min-h-screen pb-28 md:pb-0">

    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 glass-nav">
      <div class="flex justify-between items-center px-4 md:px-6 py-3 md:py-4 w-full max-w-7xl mx-auto">

        <!-- Logo + Desktop Links -->
        <div class="flex items-center gap-6 md:gap-8">
          <span class="text-lg md:text-xl font-black tracking-tight" style="color: #800000; font-family: 'Manrope', sans-serif;">
            Koperasi Giat
          </span>
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
    <main class="max-w-7xl mx-auto px-4 md:px-6 py-[6rem] md:py-[6rem]">

      <!-- Back Button -->
      <div class="mb-6 md:mb-8">
        <button
          @click="goBack"
          class="flex items-center text-on-surface-variant hover:text-primary transition-colors group"
        >
          <span class="material-symbols-outlined mr-2 group-hover:-translate-x-1 transition-transform">arrow_back</span>
          <span class="font-semibold text-sm md:text-base">Kembali ke Menu</span>
        </button>
      </div>

      <!-- Product Detail: Asymmetric Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-start">

        <!-- Left: Image Cluster -->
        <div class="lg:col-span-7">
          <!-- Main Image -->
          <div class="relative bg-surface-container-lowest rounded-2xl md:rounded-3xl overflow-hidden shadow-[0_12px_32px_rgba(128,0,0,0.06)]">
            <img
              :src="product.image"
              :alt="product.name"
              class="w-full aspect-[4/3] object-cover"
            />
            <!-- Contextual Badge -->
            <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-md px-3 md:px-4 py-1.5 md:py-2 rounded-full flex items-center gap-2 border border-outline-variant/20">
              <span class="material-symbols-outlined text-secondary text-sm">wb_sunny</span>
              <span class="text-[10px] md:text-xs font-bold text-on-surface-variant tracking-wider uppercase">
                {{ product.contextBadge }}
              </span>
            </div>
          </div>
        </div>

        <!-- Right: Product Info (sticky on desktop) -->
        <div class="lg:col-span-5 lg:sticky lg:top-28">
          <div class="flex flex-col space-y-6 md:space-y-8">

            <!-- Header -->
            <header>
              <span class="text-secondary font-bold tracking-widest text-xs uppercase mb-2 md:mb-3 block">
                {{ product.category }}
              </span>
              <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-primary leading-tight tracking-tight mb-3 md:mb-4" style="font-family: 'Manrope', sans-serif;">
                {{ product.name }}
              </h1>
              <div class="flex items-center gap-4">
                <span class="text-2xl md:text-3xl font-bold text-on-surface">{{ product.price }}</span>
                <div class="px-3 py-1 bg-surface-container-high rounded-full flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm text-secondary" style="font-variation-settings: 'FILL' 1, 'wght' 400;">star</span>
                  <span class="text-xs font-bold">{{ product.rating }}</span>
                </div>
              </div>
            </header>

            <!-- Description + Metadata -->
            <div class="bg-surface-container-low p-5 md:p-6 rounded-xl md:rounded-2xl">
              <p class="text-on-surface-variant leading-relaxed mb-5 md:mb-6 text-sm md:text-base">
                {{ product.description || 'Tidak ada deskripsi tersedia untuk menu ini.' }}
              </p>
              <div class="grid grid-cols-2 gap-4">
                <div
                  v-for="meta in product.meta"
                  :key="meta.label"
                  class="flex items-center space-x-3"
                >
                  <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white flex items-center justify-center text-secondary flex-shrink-0">
                    <span class="material-symbols-outlined text-lg md:text-xl">{{ meta.icon }}</span>
                  </div>
                  <div>
                    <p class="text-[9px] md:text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">{{ meta.label }}</p>
                    <p class="text-xs md:text-sm font-semibold text-on-surface">{{ meta.value }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Spice Level Selector -->
            <div class="space-y-3">
              <label class="text-sm font-bold text-primary block">Tingkat Kepedasan</label>
              <div class="flex gap-2">
                <button
                  v-for="level in ['Sedang', 'Pedas', 'Extra']"
                  :key="level"
                  @click="selectSpiceLevel(level)"
                  :class="[
                    'flex-1 py-2.5 rounded-xl font-bold text-[10px] uppercase border-2 transition-all',
                    selectedSpice === level
                      ? 'bg-primary text-white border-primary shadow-lg'
                      : 'bg-surface-container-high text-on-surface-variant border-transparent'
                  ]"
                >
                  {{ level }}
                </button>
              </div>
            </div>

            <!-- CTA -->
            <div class="pt-2 md:pt-4 flex flex-col space-y-3 md:space-y-4">
              <button
                @click="addToCart"
                class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-4 md:py-5 rounded-full font-bold text-base md:text-lg flex items-center justify-center gap-3 transition-transform active:scale-95 shadow-[0_12px_24px_rgba(87,0,0,0.2)]"
              >
                <span class="material-symbols-outlined">add_shopping_cart</span>
                Tambah ke Keranjang
              </button>
              <div class="flex justify-between items-center px-2 md:px-4">
                <span class="text-xs text-on-surface-variant font-medium">
                  Status: <span :class="product.is_available ? 'text-green-600' : 'text-error'" class="font-bold">{{ product.is_available ? 'Tersedia' : 'Habis' }}</span>
                </span>
                <button
                  @click="toggleFavorite"
                  class="text-primary font-bold text-xs uppercase tracking-widest flex items-center gap-1 hover:opacity-80 transition-opacity"
                >
                  <span
                    class="material-symbols-outlined text-sm"
                    :style="isFavorite ? 'font-variation-settings: \'FILL\' 1' : ''"
                  >favorite</span>
                  {{ isFavorite ? 'Tersimpan' : 'Simpan Favorit' }}
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>

    </main>

    <!-- Bottom Nav (Mobile Only) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-[#faf9f6]/80 backdrop-blur-md rounded-t-3xl shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <Link
        v-for="item in bottomNav"
        :key="item.label"
        :href="item.url"
        :class="[
          'flex flex-col items-center justify-center',
          item.active ? 'text-primary' : 'text-on-surface-variant'
        ]"
      >
        <span class="material-symbols-outlined">{{ item.icon }}</span>
        <span class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ item.label }}</span>
        <span v-if="item.active" class="w-1 h-1 bg-primary rounded-full mt-0.5"></span>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import { route } from 'ziggy-js'
// import axios from 'axios'

const props = defineProps({
  product: { type: Object, required: true }
})
const page = usePage()



const logout = () => {
  router.post(route('logout'))
}


const searchQuery = ref('')
const showMobileSearch = ref(false)


const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]

const bottomNav = computed(() => [
  { label: 'Beranda', icon: 'home', url: route('dashboard'), active: true },
  { label: 'Menu', icon: 'restaurant_menu', url: route('products'), active: false },
  { label: 'Cart', icon: 'shopping_cart', url: route('cart'), active: false, badge: cart.count > 0 ? cart.count : null },
  { label: 'Profil', icon: 'person', url: '#', active: false },
])




// User
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})

// --- STATE UI ---
const selectedSpice = ref('Pedas')
const isFavorite = ref(false)

// --- TRACKING LOGIC ---
const interactionsBatch = ref([]);
let entryTime = 0;

function logInteraction(type, payloadData = {}) {
    // console.log(`[Tracking] Mencoba mencatat: ${type}`, payloadData);
    
    if (!authUser.value) {
        // console.warn("[Tracking] Gagal catat: User tidak terdeteksi login");
        return; 
    }

    interactionsBatch.value.push({
        product_id: props.product.id,
        type: type,
        payload: { ...payloadData, timestamp: new Date().toISOString() }
    });
    
    //console.log(`[Tracking] Batch size sekarang: ${interactionsBatch.value.length}`);
}

function sendTrackingData() {
    if (interactionsBatch.value.length === 0 || !authUser.value) return
    
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
    }).catch(error => {
        console.error('[Tracking Error]:', {
            status: error.response?.status,
            message: error.response?.data?.result?.message || error.message,
            payload: interactionsBatch.value
        });
    });

    // Kosongkan batch segera setelah instruksi kirim diberikan
    interactionsBatch.value = [];
}


function getCsrfToken() {
    const match = document.cookie.match(new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'));
    return match ? decodeURIComponent(match[2]) : "";
}

function formatLabel(text) {
    return text.toLowerCase().replace(/[^a-z0-9]/g, '_');
}

// --- UI METHODS ---
function goBack() { window.history.back() }

function selectSpiceLevel(level) {
    selectedSpice.value = level;
    logInteraction(`variansi_rasa_${formatLabel(level)}`);
}

function addToCart() {
    cart.add(props.product);
    const safeLevel = formatLabel(selectedSpice.value);
    logInteraction(`add_to_cart_detail_${safeLevel}`);
}

function toggleFavorite() {
    isFavorite.value = !isFavorite.value;
    logInteraction(`is_favorited_${isFavorite.value ? 'true' : 'false'}`);
}

// --- LIFECYCLE ---
onMounted(() => {
    entryTime = Date.now()
    logInteraction('view', { url: window.location.href })
})


onBeforeUnmount(() => {
    const dwellTime = Math.round((Date.now() - entryTime) / 1000)
    logInteraction('dwell_time', { duration: dwellTime })
    sendTrackingData()
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #faf9f6;
}

h1, h2, h3 {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>