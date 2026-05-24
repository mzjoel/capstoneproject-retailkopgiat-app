<template>
  <div class="bg-background text-on-surface min-h-screen pb-24 md:pb-0">

    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 glass-nav bg-[#faf9f6]/90 backdrop-blur-md border-b border-outline-variant/20">
      <div class="flex justify-between items-center px-4 md:px-6 py-3 md:py-4 w-full max-w-7xl mx-auto">
        <!-- Logo + Desktop Links -->
        <div class="flex items-center gap-6 md:gap-8">
          <Link :href="route('dashboard')" class="text-lg md:text-xl font-black tracking-tight" style="color: #800000; font-family: 'Manrope', sans-serif;">
            GIAT Express
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

            <Link :href="route('product.wishlist')" class="hidden md:block p-2 text-on-surface-variant hover:text-primary hover:bg-surface-container-high rounded-full transition-colors active:scale-95">
              <span class="material-symbols-outlined">favorite</span>
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
          <template v-else>
            <Link :href="route('login')" class="text-on-surface-variant hover:text-primary font-bold text-sm">Login</Link>
            <Link :href="route('register')" class="bg-primary text-white px-6 py-2 rounded-full font-bold text-sm shadow-lg hover:scale-105 transition-all">Sign Up</Link>
          </template>
        </div>
      </div>

      <!-- Mobile Search Expandable -->
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
    </nav>

    <!-- Main Content -->
    <main class="pt-24 md:pt-28 pb-32 md:pb-12 max-w-7xl mx-auto px-6 space-y-12">
      <!-- Header -->
      <header class="mb-8 md:mb-12">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-primary tracking-tight mb-1 md:mb-2 font-headline">
          Order History
        </h1>
        <p class="text-on-surface-variant font-medium text-sm md:text-base">
          Revisit your campus favorites and academic fuel.
        </p>
      </header>

      <!-- Stats Bento Section -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-12">
        <!-- Frequent Order Card -->
        <div class="md:col-span-2 bg-surface-container-low rounded-3xl p-6 md:p-8 flex flex-col justify-between relative overflow-hidden min-h-[180px]">
          <div class="relative z-10 space-y-3 md:space-y-4">
            <span class="text-secondary font-bold text-xs uppercase tracking-widest block">
              Frequent Order
            </span>
            <h2 class="text-2xl md:text-3xl font-bold text-primary max-w-xs leading-tight">
              {{ frequentOrder ? frequentOrder.name : 'Belum ada riwayat' }}
            </h2>
            <button
              :disabled="!frequentOrder"
              @click="quickReorder(frequentOrder)"
              :class="['px-6 md:px-8 py-2.5 md:py-3 rounded-full font-bold text-sm transition-transform inline-block',
                frequentOrder ? 'bg-gradient-to-br from-primary to-primary-container text-on-primary hover:scale-105 active:scale-95' : 'bg-surface-container-highest text-on-surface-variant cursor-not-allowed opacity-50'
              ]"
            >
              Quick Reorder
            </button>
          </div>
          <div class="absolute right-0 bottom-0 w-1/2 h-full opacity-20 pointer-events-none">
            <img
              v-if="frequentOrder && frequentOrder.image"
              :src="frequentOrder.image"
              alt="Frequent order"
              class="object-cover h-full w-full"
            />
          </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-3xl p-6 md:p-8 flex flex-col justify-center items-center text-center shadow-[0_12px_32px_rgba(128,0,0,0.04)]">
           <div class="w-14 h-14 md:w-16 md:h-16 bg-surface-container-high rounded-full flex items-center justify-center mb-3 md:mb-4">
              <span class="material-symbols-outlined text-primary text-2xl md:text-3xl">restaurant</span>
           </div>
           <div class="text-3xl md:text-4xl font-extrabold text-primary mb-1">{{ stats.totalOrders }}</div>
           <div class="text-on-surface-variant font-semibold text-xs md:text-sm">Total Transaksi Selesai</div>
        </div>
      </section>

      <!-- UI States (Loading / Empty / List) -->
      <div v-if="isLoading" class="text-center py-20 text-on-surface-variant">
        <span class="material-symbols-outlined animate-spin text-4xl mb-4">refresh</span>
        <p class="font-bold">Memuat riwayat transaksi...</p>
      </div>

      <div v-else-if="orders.length === 0" class="text-center py-20 text-on-surface-variant">
        <span class="material-symbols-outlined text-6xl mb-4 opacity-20">history_toggle_off</span>
        <p class="font-bold">Belum ada riwayat transaksi.</p>
      </div>

      <!-- Orders List -->
      <div v-else class="space-y-6 md:space-y-8">
        <div
          v-for="order in orders"
          :key="order.id"
          :class="['group transition-all', order.status === 'canceled' ? 'opacity-60' : '']"
        >
          <div class="flex items-center justify-between mb-3 md:mb-4 px-1">
            <div class="flex items-center space-x-2 md:space-x-3">
              <div :class="['w-2 h-2 rounded-full', order.status === 'delivered' ? 'bg-secondary' : 'bg-outline-variant']"></div>
              <span class="font-bold uppercase text-xs md:text-sm text-on-surface-variant">
                {{ order.date }}
              </span>
            </div>
            <span :class="['px-3 py-1 rounded-full text-[10px] font-bold uppercase', 
              order.status === 'delivered' ? 'bg-surface-container-high' : 'bg-error-container text-on-error-container']">
              {{ order.statusLabel }}
            </span>
          </div>

          <div :class="['rounded-3xl p-5 md:p-8 flex flex-col sm:flex-row sm:items-center gap-5 md:gap-8 border', 
            order.status === 'delivered' ? 'bg-surface-container-lowest shadow-sm border-outline-variant/10' : 'bg-surface-container-low border-transparent']">
            
            <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl overflow-hidden bg-surface-container-high flex-shrink-0">
              <img v-if="order.image" :src="order.image" class="object-cover w-full h-full" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined opacity-20">fastfood</span>
              </div>
            </div>

            <div class="flex-grow">
              <h3 class="text-lg md:text-xl font-bold text-primary mb-1">{{ order.name }}</h3>
              <p class="text-on-surface-variant text-xs md:text-sm mb-3">{{ order.note }}</p>
              <div class="text-xl md:text-2xl font-black">{{ formatRupiah(order.total) }}</div>
            </div>

            <div class="flex flex-row sm:flex-col gap-3 flex-shrink-0">
              <button @click="viewDetails(order.raw)" class="px-6 py-2.5 rounded-full font-bold text-sm text-primary hover:bg-primary/5 transition-colors">
                View Details
              </button>
              <button @click="reorder(order.raw)" :disabled="order.status !== 'delivered'"
                :class="['px-6 py-2.5 rounded-full font-bold text-sm flex items-center justify-center gap-2 transition-transform',
                order.status === 'delivered' ? 'bg-primary text-on-primary hover:scale-105 active:scale-95' : 'bg-surface-container-highest text-on-surface-variant cursor-not-allowed opacity-50']">
                <span class="material-symbols-outlined text-sm">refresh</span> Order Again
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-[#faf9f6]/90 backdrop-blur-md rounded-t-3xl border-t border-outline-variant/10 shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <Link
        v-for="item in bottomNav"
        :key="item.label"
        :href="item.url"
        :class="[
          'flex flex-col items-center justify-center transition-transform active:scale-90 hover:scale-110',
          item.active ? 'text-primary' : 'text-on-surface-variant'
        ]"
      >
        <span class="material-symbols-outlined" :style="item.active ? 'font-variation-settings: \'FILL\' 1' : ''">
          {{ item.icon }}
        </span>
        <span class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ item.label }}</span>
        <span v-if="item.active" class="w-1 h-1 bg-primary-container rounded-full mt-0.5"></span>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import axios from 'axios'
import { route } from 'ziggy-js'

// ============================================================================
// STATE & AUTHENTICATION
// ============================================================================
const page = usePage()
const authUser = computed(() => page.props.auth?.user)
const displayName = computed(() => authUser.value?.name || 'User')
const displayAvatar = computed(() => 
  authUser.value?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&background=800000&color=fff`
)

const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url:route('transaction.history'), active: false },
]

const showMobileSearch = ref(false)
const searchQuery = ref('')
const rawTransactions = ref([])
const isLoading = ref(true)

// ============================================================================
// NAVIGATION LINKS
// ============================================================================

const bottomNav = [
  { icon: 'restaurant_menu', label: 'Menu', url: route('dashboard'), active: false },
  { icon: 'assignment', label: 'Orders', url: route('transaction.history'), active: true },
  { icon: 'shopping_bag', label: 'Cart', url: route('cart'), active: false },
  { icon: 'person', label: 'Profile', url: route('profile.edit'), active: false },
]

// ============================================================================
// FETCH DATA LOGIC
// ============================================================================
const fetchTransactions = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/api/v1/transactions/history', {
      headers: { 'Accept': 'application/json' },
      withCredentials: true 
    })
    
    if (response.data?.result?.status === 'Success 200') {
      rawTransactions.value = response.data.data
    }
  } catch (error) {
    console.error("Gagal mengambil riwayat transaksi:", error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchTransactions()
})

// ============================================================================
// COMPUTED LOGIC (Frontend Filtering & Stats)
// ============================================================================

// 1. Total Transaksi
const stats = computed(() => {
  const completedOrders = rawTransactions.value.filter(trx => ['completed', 'ready_for_pickup'].includes(trx.status))
  return { totalOrders: completedOrders.length }
})

// 2. Logic Menentukan Frequent Order (Menu paling sering dibeli)
const frequentOrder = computed(() => {
  if (rawTransactions.value.length === 0) return null
  
  const productCounts = {}
  rawTransactions.value.forEach(trx => {
    trx.details?.forEach(detail => {
      const p = detail.product
      if (p) {
        if (!productCounts[p.id]) {
          productCounts[p.id] = { count: 0, name: p.name, image: p.image_url || p.image, raw: p }
        }
        productCounts[p.id].count += 1
      }
    })
  })
  
  // Urutkan dari count terbanyak
  const sortedProducts = Object.values(productCounts).sort((a, b) => b.count - a.count)
  return sortedProducts.length > 0 ? sortedProducts[0] : null
})

// 3. Mapping struktur API ke struktur Card UI
const orders = computed(() => {
  return rawTransactions.value.map(trx => {
    const firstDetail = trx.details && trx.details.length > 0 ? trx.details[0] : null
    const product = firstDetail ? firstDetail.product : null
    
    const extraItemsCount = trx.details ? trx.details.length - 1 : 0
    const noteText = extraItemsCount > 0 ? `+ ${extraItemsCount} item lainnya` : '1 item'

    let uiStatus = 'pending'
    let uiStatusLabel = 'Diproses'
    
    if (trx.status === 'completed' || trx.status === 'ready_for_pickup') {
      uiStatus = 'delivered'
      uiStatusLabel = 'Selesai'
    } else if (['canceled', 'cancelled', 'failed'].includes(trx.status)) {
      uiStatus = 'canceled'
      uiStatusLabel = 'Dibatalkan'
    }

    return {
      id: trx.id,
      date: formatDate(trx.created_at),
      isRecent: checkIsRecent(trx.created_at),
      status: uiStatus,
      statusLabel: uiStatusLabel,
      image: product ? (product.image_url || product.image) : null,
      name: product ? product.name : 'Pesanan Tidak Diketahui',
      note: noteText,
      total: trx.grand_total || 0,
      raw: trx 
    }
  })
})

// ============================================================================
// HELPERS & ACTION METHODS
// ============================================================================

const formatRupiah = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount || 0)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Intl.DateTimeFormat('id-ID', { 
    day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
  }).format(new Date(dateString))
}

const checkIsRecent = (dateString) => {
  if (!dateString) return false
  // Note: Using .getTime() to convert Dates to numbers for subtraction
  const diffInDays = (new Date().getTime() - new Date(dateString).getTime()) / (1000 * 60 * 60 * 24)
  return diffInDays <= 7
}

const logout = () => {
  router.post(route('logout'))
}

const quickReorder = (item) => {
  if (!item) return
  console.log('Quick reorder logic for:', item.name)
  // Implementasi ke Cart Store di sini
}

const reorder = (rawOrder) => {
  console.log('Reorder full transaction:', rawOrder.id)
  // Implementasi ke Cart Store di sini
}

const viewDetails = (rawOrder) => {
  router.get(route('transaction.status', { id: rawOrder.id }))
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

/* Apply basic resets specific to this component */
.glass-nav {
  transition: background-color 0.3s ease;
}

h1, h2, h3, .font-headline {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* Spinner Animation */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
  display: inline-block;
}
</style>
