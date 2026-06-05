<template>
  <div class="bg-background text-on-surface min-h-screen pb-24 lg:pb-0">

    <nav class="hidden lg:block fixed top-0 w-full z-50 glass-nav">
      <div class="flex justify-between items-center px-4 md:px-6 py-1 w-full max-w-7xl mx-auto">

        <div class="flex items-center gap-6 md:gap-8">
          <Link :href="route('dashboard')" class="flex items-center">
            <img src="/assets/icons/giat-express-icon.png" alt="GIAT Express" class="h-8 md:h-20 w-auto object-contain" />
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

        <div class="flex items-center gap-2 md:gap-4">
          <template v-if="authUser">
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

            <Link :href="route('product.wishlist')" class="hidden md:block p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-full transition-colors active:scale-95">
              <span class="material-symbols-outlined">favorite</span>
            </Link>

            <div class="flex items-center group relative cursor-pointer">
              <div class="w-8 h-8 rounded-full overflow-hidden bg-surface-container-high">
                <img :src="displayAvatar" :alt="displayName" class="w-full h-full object-cover" />
              </div>
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

      <div v-show="showMobileSearch" class="md:hidden px-4 pb-3">
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

    <main class="pt-6 lg:pt-28 pb-32 lg:pb-12 max-w-7xl mx-auto px-6 space-y-12">

      <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
          <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-primary">
            Halo, {{ displayName }}!
          </h1>
        </div>
      </header>

      <section v-if="isRecommendationsLoading" class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="md:col-span-8 bg-surface-container-low rounded-xl min-h-[320px] animate-pulse" />
        <div class="md:col-span-4 bg-surface-container-lowest rounded-xl min-h-[320px] animate-pulse border border-outline-variant/10" />
      </section>

      <section
        v-else-if="recommendations.length === 0"
        class="grid grid-cols-1 md:grid-cols-12 gap-6"
      >
        <div class="md:col-span-12 bg-surface-container-low rounded-xl p-8 flex items-center justify-center min-h-[200px] text-on-surface-variant text-sm gap-2">
          <span class="material-symbols-outlined">sentiment_neutral</span>
          Rekomendasi belum tersedia saat ini.
        </div>
      </section>

      <section v-else class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div
          class="md:col-span-8 relative overflow-hidden bg-surface-container-low rounded-xl p-8 flex flex-col justify-between min-h-[320px]"
          :style="{
            background: `linear-gradient(135deg, ${weatherInfo.gradFrom}, ${weatherInfo.gradTo}), var(--md-sys-color-surface-container-low, #f3eded)`
          }"
        >
          <div class="z-10 max-w-md space-y-4">
            <div class="flex items-center gap-3">
              <span
                class="material-symbols-outlined text-secondary text-4xl"
                style="font-variation-settings: 'FILL' 1"
              >{{ weatherInfo.icon }}</span>
              <span class="text-secondary font-bold uppercase tracking-widest text-sm">
                {{ weatherInfo.label }}
              </span>
            </div>

            <Transition name="banner-slide" mode="out-in">
              <div :key="activeIndex" class="space-y-3">
                <h2 class="text-3xl font-bold text-on-surface leading-tight">
                  Kampus lagi {{ weatherInfo.tagline }}
                </h2>
                <p class="text-on-surface-variant">
                  <template v-if="tempLabel">{{ tempLabel }} </template>
                  Coba <strong class="text-on-surface">{{ activeBanner.name }}</strong>
                  — pilihan tepat buat kamu sekarang.
                </p>
              </div>
            </Transition>

            <Transition name="banner-slide" mode="out-in">
              <div :key="'tags-' + activeIndex" class="flex flex-wrap gap-2">
                <span
                  v-for="tag in parseTags(activeBanner.tags)"
                  :key="tag"
                  class="text-xs px-3 py-1 rounded-full bg-secondary/10 text-secondary font-medium"
                >{{ tag }}</span>
              </div>
            </Transition>

            <div class="flex items-center gap-4 mt-4">
              <Link
                :href="route('products.detail', activeBanner.id)"
                class="cta-gradient text-white px-8 py-3 rounded-full font-bold inline-flex items-center gap-2 hover:scale-105 transition-transform"
                @click="trackProductClick(activeBanner)"
              >
                {{ activeBanner.category === 'Drink' ? 'Lihat Menu Segar' : 'Pesan Sekarang' }}
                <span class="material-symbols-outlined">arrow_forward</span>
              </Link>
              <span class="font-bold text-primary text-lg">
                {{ formatPrice(activeBanner.price) }}
              </span>
            </div>
          </div>

          <Transition name="img-fade" mode="out-in">
            <div
              v-if="activeBanner.image || activeBanner.image_url"
              :key="'img-' + activeIndex"
              class="absolute right-[-40px] bottom-[-20px] w-1/2 h-full opacity-90 pointer-events-none"
            >
              <img
                :src="activeBanner.image_url || activeBanner.image"
                :alt="activeBanner.name"
                class="w-full h-full object-contain transform rotate-12"
              />
            </div>
          </Transition>

          <div
            v-if="hasMultiple"
            class="absolute bottom-4 left-8 flex items-center gap-2 z-20"
          >
            <button
              v-for="(_, idx) in recommendations"
              :key="idx"
              :aria-label="`Slide ${idx + 1}`"
              class="rounded-full transition-all duration-300"
              :class="idx === activeIndex
                ? 'w-6 h-2 bg-primary'
                : 'w-2 h-2 bg-on-surface/20 hover:bg-on-surface/40'"
              @click="goTo(idx)"
            />
          </div>

          <div
            v-if="hasMultiple"
            class="absolute bottom-0 left-0 h-[3px] w-full bg-primary/15 rounded-b-xl overflow-hidden"
          >
            <div :key="activeIndex" class="h-full bg-primary origin-left banner-progress" />
          </div>
        </div>

        <div class="md:col-span-4 bg-surface-container-lowest rounded-xl p-6 shadow-[0_12px_32px_rgba(128,0,0,0.06)] flex flex-col items-center text-center justify-center space-y-4 border border-outline-variant/10">

          <span class="text-sm font-bold text-on-surface-variant uppercase tracking-tighter flex items-center gap-1">
            <span
              class="material-symbols-outlined text-base text-error"
              style="font-variation-settings: 'FILL' 1"
            >local_fire_department</span>
            Terlaris Saat Ini
          </span>

          <div class="w-32 h-32 rounded-full overflow-hidden bg-surface-container-low p-2 ring-4 ring-primary/10">
            <img
              v-if="bestseller?.image_url || bestseller?.image"
              :src="bestseller.image_url || bestseller.image"
              :alt="bestseller.name"
              class="w-full h-full object-cover rounded-full"
            />
            <div v-else class="w-full h-full rounded-full bg-surface-container flex items-center justify-center">
              <span class="material-symbols-outlined text-4xl text-on-surface-variant">lunch_dining</span>
            </div>
          </div>

          <div class="space-y-1">
            <h3 class="font-bold text-xl">{{ bestseller?.name }}</h3>
            <p class="text-on-surface-variant text-sm">Kategori {{ bestseller?.category }}</p>
            <div v-if="bestseller?.tags" class="flex flex-wrap justify-center gap-1 mt-2">
              <span
                v-for="tag in parseTags(bestseller.tags).slice(0, 3)"
                :key="tag"
                class="text-xs px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant"
              >{{ tag }}</span>
            </div>
          </div>

          <span class="text-primary font-bold text-lg">{{ formatPrice(bestseller?.price) }}</span>

          <button
            class="w-full py-2.5 rounded-full border border-primary text-primary font-semibold text-sm hover:bg-primary hover:text-white transition-colors duration-200 flex items-center justify-center gap-2"
            @click="addToCart(bestseller)"
          >
            <span class="material-symbols-outlined text-sm">shopping_bag</span>
            Pesan Sekarang
          </button>
        </div>
      </section>

      <section class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-on-surface">Rekomendasi untuk Kamu</h2>

          <div
            v-if="weatherContext"
            class="flex items-center gap-1.5 bg-surface-container-high px-3 py-1.5 rounded-full text-xs font-bold text-primary border border-primary/20 shadow-sm"
          >
            <span class="material-symbols-outlined text-sm">{{ weatherInfo.icon }}</span>
            <span>{{ weatherContext.temp }}°C</span>
          </div>
        </div>

        <div v-if="isRecommendationsLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">
          <div v-for="i in 3" :key="i" class="animate-pulse bg-surface-container-low rounded-xl h-80" />
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8">
          <div
            v-for="item in recommendations"
            :key="item.id"
            class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group flex flex-col border border-outline-variant/10"
          >
            <Link
              :href="route('products.detail', item.id)"
              class="block flex-grow"
              @click="trackProductClick(item)"
            >
              <div class="relative aspect-[4/3] overflow-hidden">
                <img
                  :src="item.image_url || item.image"
                  :alt="item.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>
              <div class="p-4 md:p-6 flex flex-col">
                <div class="flex justify-between items-start mb-3 md:mb-4 gap-2">
                  <h3 class="font-bold text-lg md:text-xl text-on-surface leading-tight" style="font-family: 'Manrope', sans-serif;">
                    {{ item.name }}
                  </h3>
                  <span class="font-bold text-secondary whitespace-nowrap">{{ formatPrice(item.price) }}</span>
                </div>
                <p class="text-sm text-on-surface-variant mb-6 md:mb-8 line-clamp-2 leading-relaxed">
                  {{ item.description || 'Deskripsi menu lezat ini.' }}
                </p>
              </div>
            </Link>

            <div class="px-4 pb-4 md:px-6 md:pb-6 mt-auto flex gap-2">
              <button
                class="flex-1 bg-gradient-to-r from-primary to-[#a00000] text-white py-2.5 md:py-3 rounded-xl font-bold flex items-center justify-center gap-2 active:scale-95 transition-transform duration-200 text-sm md:text-base"
                @click="addToCart(item)"
              >
                <span class="material-symbols-outlined text-lg">shopping_bag</span>
                Pesan Sekarang
              </button>
              <button
                :class="[
                  'p-2.5 md:p-3 rounded-xl flex items-center justify-center transition-colors duration-200 active:scale-95 border',
                  wishlistedItems.includes(item.id)
                    ? 'bg-surface-container-lowest text-red-500 border-outline-variant/20 hover:bg-red-50'
                    : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high border-outline-variant/20'
                ]"
                @click="addToWishlist(item)"
              >
                <span
                  class="material-symbols-outlined"
                  :style="wishlistedItems.includes(item.id) ? 'font-variation-settings: \'FILL\' 1' : ''"
                >favorite</span>
              </button>
            </div>
          </div>
        </div>

        <div v-if="!isRecommendationsLoading && recommendations.length === 0" class="text-center py-20">
          <span class="material-symbols-outlined text-outline text-6xl mb-4 block">search_off</span>
          <p class="text-on-surface-variant font-medium">Belum ada rekomendasi saat ini.</p>
        </div>
      </section>

    </main>

    <nav class="lg:hidden glass-nav fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 rounded-t-3xl shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <template v-for="navItem in bottomNav" :key="navItem.label">
        <div
          v-if="navItem.label === 'Profil'"
          class="flex flex-col items-center justify-center relative cursor-pointer select-none active:scale-90 transition-transform"
          @click="showMobileProfileMenu = !showMobileProfileMenu"
        >
          <div class="w-6 h-6 rounded-full overflow-hidden bg-surface-container-high mb-1">
            <img :src="displayAvatar" :alt="displayName" class="w-full h-full object-cover" />
          </div>
          <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ navItem.label }}</span>
          
          <!-- Dropdown/Popout for Logout -->
          <div
            v-if="showMobileProfileMenu"
            class="absolute bottom-16 right-0 w-30 bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant/10 p-2 z-[60]"
          >
            <button
              @click.stop="logout"
              class="w-full text-left px-4 py-3 rounded-lg hover:bg-error/10 text-error flex items-center gap-3 transition-colors"
            >
              <span class="material-symbols-outlined text-sm">logout</span>
              <span class="font-bold text-sm">Logout</span>
            </button>
          </div>
        </div>

        <Link
          v-else
          :href="navItem.url"
          :class="[
            'flex flex-col items-center justify-center relative transition-transform active:scale-90',
            navItem.active ? 'text-primary' : 'text-on-surface-variant'
          ]"
        >
          <span class="material-symbols-outlined mb-1">{{ navItem.icon }}</span>
          <span class="text-[10px] font-bold uppercase tracking-widest">{{ navItem.label }}</span>
          <span
            v-if="navItem.badge"
            class="absolute -top-1 -right-1 bg-primary text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center"
          >{{ navItem.badge }}</span>
        </Link>
      </template>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, onUnmounted, watch } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import { route } from 'ziggy-js'
import axios from 'axios'

const page      = usePage()
const authUser  = computed(() => page.props.auth.user)
const displayName = computed(() =>
  authUser.value?.customer_profile?.name ||
  authUser.value?.admin_profile?.name ||
  authUser.value?.email ||
  'User'
)
const displayAvatar = computed(() => 
  authUser.value?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&background=800000&color=fff`
)

const searchQuery       = ref('')
const showMobileSearch  = ref(false)
const wishlistedItems   = ref([])
const showMobileProfileMenu = ref(false)

const recommendations        = ref([])
const weatherContext         = ref(null)   
const isRecommendationsLoading = ref(true)
const activeIndex    = ref(0)
let   rotateInterval = null


const activeBanner = computed(() => recommendations.value[activeIndex.value] ?? null)
const bestseller   = computed(() => recommendations.value[0] ?? null)
const hasMultiple  = computed(() => recommendations.value.length > 1)

const WEATHER_MAP = {
  Sunny:        { icon: 'wb_sunny',     label: 'Pas buat cuaca panas gini',        tagline: 'terik banget nih, yuk ademin pake yang segar-segar!', gradFrom: 'rgba(251,191,36,0.18)',  gradTo: 'rgba(253,186,116,0.08)' },
  Clear:        { icon: 'wb_sunny',     label: 'Cuaca cerah, mood bagus!',          tagline: 'Hari cerah, saatnya nikmatin makanan favoritmu!',      gradFrom: 'rgba(253,224,71,0.15)',  gradTo: 'rgba(251,191,36,0.06)'  },
  Cloudy:       { icon: 'wb_cloudy',    label: 'Santai di bawah awan',              tagline: 'Mendung dikit, enak buat ngemil sambil gabut.',         gradFrom: 'rgba(148,163,184,0.18)', gradTo: 'rgba(203,213,225,0.08)' },
  Rainy:        { icon: 'rainy',        label: 'Hujan-hujanan butuh yang hangat',   tagline: 'Hujan di luar, yuk angetin perut dari dalam!',          gradFrom: 'rgba(96,165,250,0.18)',  gradTo: 'rgba(147,197,253,0.08)' },
  Drizzle:      { icon: 'rainy',        label: 'Gerimis-gerimisan asik nih',        tagline: 'Gerimis tipis, cocok banget sambil ngupi.',             gradFrom: 'rgba(56,189,248,0.15)',  gradTo: 'rgba(186,230,253,0.06)' },
  Thunderstorm: { icon: 'thunderstorm', label: 'Badai di luar, aman di dalam',      tagline: 'Amanin perut dulu, biar kuat nunggu reda!',            gradFrom: 'rgba(167,139,250,0.18)', gradTo: 'rgba(196,181,253,0.08)' },
  Snow:         { icon: 'ac_unit',      label: 'Dingin banget, butuh yang hangat',  tagline: 'Brrr! Waktu terbaik buat yang panas-panas.',           gradFrom: 'rgba(186,230,253,0.18)', gradTo: 'rgba(224,242,254,0.08)' },
}

const weatherInfo = computed(() => {
  const cond = weatherContext.value?.condition ?? 'Sunny'
  return WEATHER_MAP[cond] ?? WEATHER_MAP['Sunny']
})

const tempLabel = computed(() => {
  const temp = weatherContext.value?.temp
  return temp ? `Suhu ${temp}°C di sekitar kampus.` : ''
})

function formatPrice(price) {
  return 'Rp ' + Number(price).toLocaleString('id-ID')
}

function parseTags(tagStr) {
  if (!tagStr) return []
  return tagStr.split(',').map(t => t.trim()).filter(Boolean).slice(0, 4)
}

function startRotate() {
  stopRotate()
  if (!hasMultiple.value) return
  rotateInterval = setInterval(() => {
    activeIndex.value = (activeIndex.value + 1) % recommendations.value.length
  }, 5000)
}

function stopRotate() {
  if (rotateInterval) { clearInterval(rotateInterval); rotateInterval = null }
}

function goTo(idx) {
  stopRotate()
  activeIndex.value = idx
  startRotate()
}

watch(recommendations, () => { activeIndex.value = 0 })

async function fetchRecommendations() {
  isRecommendationsLoading.value = true
  try {
    const response = await axios.get('/api/v1/user/recommendations', {
      headers: { Accept: 'application/json' },
      withCredentials: true,
    })

    if (response.data?.result?.status === 'Success 200') {
      const data = response.data.data

      weatherContext.value = data.context?.weather ?? null

      const rawRecs = data.recommendations ?? []
      const recs = rawRecs.map(item => ({
        ...item,
        image: item.image || item.image_url || `https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80`,
        image_url: item.image_url || item.image || `https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80`
      }))

      if (recs.length > 1) {
        const first = recs[0]
        const rest  = recs.slice(1)
        for (let i = rest.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1))
          ;[rest[i], rest[j]] = [rest[j], rest[i]]
        }
        recommendations.value = [first, ...rest]
      } else {
        recommendations.value = recs
      }
    }
  } catch (err) {
    console.error('Failed to fetch recommendations:', err)
  } finally {
    isRecommendationsLoading.value = false
  }
}

async function fetchWishlist() {
  if (!authUser.value) return
  try {
    const response = await axios.get('/api/v1/user/wishlist', {
      headers: { Accept: 'application/json' },
      withCredentials: true,
    })
    if (response.data?.data) {
      wishlistedItems.value = response.data.data
    }
  } catch (err) {
    console.error('Failed to fetch wishlist:', err)
  }
}

function addToWishlist(item) {
  if (!item) return
  if (wishlistedItems.value.includes(item.id)) {
    wishlistedItems.value = wishlistedItems.value.filter(id => id !== item.id)
    logInteraction(item.id, 'unwishlist', { action_source: 'dashboard_grid' })
  } else {
    wishlistedItems.value.push(item.id)
    logInteraction(item.id, 'wishlist', { action_source: 'dashboard_grid' })
  }
  sendTrackingData()
}

function addToCart(item) {
  if (!item) return
  router.visit(route('products.detail', item.id))
}


const interactionsBatch = ref([])
let entryTime = 0

function getCsrfToken() {
  const match = document.cookie.match(new RegExp('(^|;\\s*)XSRF-TOKEN=([^;]*)'))
  return match ? decodeURIComponent(match[2]) : ''
}

function formatLabel(text) {
  if (!text) return 'unknown'
  if (typeof text === 'object' && text.name) text = text.name
  else if (typeof text !== 'string') text = String(text)
  return text.toLowerCase().replace(/[^a-z0-9]/g, '_')
}

function logInteraction(productId, type, payloadData = {}) {
  if (!authUser.value) return
  interactionsBatch.value.push({
    product_id: productId,
    type,
    payload: { ...payloadData, timestamp: new Date().toISOString() },
  })
}

function sendTrackingData() {
  if (interactionsBatch.value.length === 0 || !authUser.value) return
  const body = JSON.stringify({ interactions: interactionsBatch.value })
  fetch('/api/v1/user/interactions', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    body,
    credentials: 'include',
  }).catch(err => console.error('Failed to send tracking data:', err))
  interactionsBatch.value = []
}

function trackProductClick(item) {
  if (!item) return
  const categoryName = typeof item.category === 'object' ? item.category.name : item.category
  logInteraction(item.id, 'click_product_catalog', {
    category: formatLabel(categoryName || 'unknown'),
  })
}

const navLinks = [
  { label: 'Beranda',  url: route('dashboard'),          active: true  },
  { label: 'Menu',     url: route('products'),            active: false },
  { label: 'Pesanan',  url: route('transaction.history'), active: false },
]

const bottomNav = computed(() => [
  { label: 'Beranda', icon: 'home',            url: route('dashboard'),          active: true  },
  { label: 'Menu',    icon: 'restaurant_menu', url: route('products'),            active: false },
  { label: 'Cart',    icon: 'shopping_cart',   url: route('cart'),               active: false, badge: cart.count > 0 ? cart.count : null },
  { label: 'Profil',  icon: 'person',          url: '#',                         active: false },
])

const logout = () => router.post(route('logout'))

onMounted(async () => {
  entryTime = Date.now()
  fetchWishlist()
  await fetchRecommendations()
  startRotate()
})

onBeforeUnmount(() => {
  sendTrackingData()
})

onUnmounted(() => {
  stopRotate()
})
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

/* Banner text slide */
.banner-slide-enter-active,
.banner-slide-leave-active { transition: opacity .35s ease, transform .35s ease; }
.banner-slide-enter-from   { opacity: 0; transform: translateX(14px); }
.banner-slide-leave-to     { opacity: 0; transform: translateX(-14px); }

/* Image fade */
.img-fade-enter-active,
.img-fade-leave-active { transition: opacity .4s ease; }
.img-fade-enter-from,
.img-fade-leave-to     { opacity: 0; }

/* Progress bar */
@keyframes progress {
  from { transform: scaleX(0); }
  to   { transform: scaleX(1); }
}
.banner-progress {
  animation: progress 5s linear forwards;
}
</style>