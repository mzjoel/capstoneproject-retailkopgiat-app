<template>
  <div class="bg-background text-on-surface min-h-screen pb-32 md:pb-12">

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

    <!-- Main -->
    <main v-if="!isLoading && transaction" class="max-w-4xl mx-auto px-4 md:px-6 pt-24 md:pt-32 space-y-8 md:space-y-10">

      <!-- Header: Order ID -->
      <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
          <!-- Back link -->
          <Link :href="route('products')" class="flex items-center gap-1.5 text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            <span class="text-xs md:text-sm tracking-wide font-bold uppercase">Kembali ke Menu</span>
          </Link>
          <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-primary font-headline">
            Pesanan #{{ transaction.order_id }}
          </h1>
          <p class="text-on-surface-variant text-sm md:text-base">
            Waktu Pesan: {{ new Date(transaction.created_at).toLocaleString('id-ID') }} • Kantin Giat
          </p>
        </div>
      </header>

      <!-- Status Timeline -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-px bg-surface-container-high/20 rounded-3xl overflow-hidden">
        <div
          v-for="(step, idx) in statusSteps"
          :key="step.key"
          :class="[
            'p-6 md:p-8 flex flex-col items-center text-center space-y-3 md:space-y-4 relative transition-all',
            step.state === 'done'    ? 'bg-surface-container-high/40'    : '',
            step.state === 'active'  ? 'bg-surface-container-lowest'     : '',
            step.state === 'pending' ? 'bg-surface-container-high/20 opacity-50' : '',
            idx === 0                ? 'rounded-t-3xl md:rounded-l-3xl md:rounded-tr-none' : '',
            idx === statusSteps.length - 1 ? 'rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none' : '',
          ]"
        >
          <!-- Icon -->
          <div
            :class="[
              'w-11 h-11 md:w-12 md:h-12 rounded-full flex items-center justify-center',
              step.state === 'done'    ? 'bg-primary text-on-primary' : '',
              step.state === 'active'  ? 'bg-primary-container text-on-primary-container animate-pulse' : '',
              step.state === 'pending' ? 'bg-surface-container-highest text-on-surface-variant' : '',
            ]"
          >
            <span
              class="material-symbols-outlined"
              :style="step.state !== 'pending' ? 'font-variation-settings: \'FILL\' 1' : ''"
            >{{ step.icon }}</span>
          </div>

          <!-- Text -->
          <div>
            <h3
              :class="[
                'font-bold text-base md:text-lg',
                step.state === 'pending' ? 'text-on-surface-variant' : 'text-primary'
              ]"
            >
              {{ step.label }}
            </h3>
            <p class="text-xs md:text-sm text-on-surface-variant mt-0.5">{{ step.description }}</p>
          </div>

          <!-- Progress bar (active step only) -->
          <div
            v-if="step.state === 'active'"
            class="absolute bottom-0 left-0 w-full h-1 bg-surface-container-low overflow-hidden"
          >
            <div class="h-full bg-primary-container" :style="`width: ${step.progress}%`"></div>
          </div>
        </div>
      </section>

      <!-- Order Detail Section -->
      <section class="space-y-5 md:space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl md:text-2xl font-bold tracking-tight text-primary font-headline">
            Detail Pesanan
          </h2>
          <div class="text-secondary font-semibold flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">verified</span>
            <span class="text-xs md:text-sm hidden sm:block">Pesanan Terverifikasi</span>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
          <!-- Items List -->
          <div class="space-y-3 md:space-y-4">
            <div
              v-for="detail in transaction.details"
              :key="detail.id"
              class="bg-surface-container-lowest p-3 md:p-4 rounded-2xl md:rounded-3xl flex gap-3 md:gap-4 items-center transition-all hover:scale-[1.02]"
            >
              <img
                :src="detail.product.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80'"
                :alt="detail.product.name"
                class="w-16 h-16 md:w-20 md:h-20 rounded-xl md:rounded-2xl object-cover flex-shrink-0"
              />
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                  <h4 class="font-bold text-on-surface text-sm md:text-base leading-tight">{{ detail.product.name }}</h4>
                  <span class="text-secondary font-bold text-[10px] md:text-xs uppercase tracking-wider whitespace-nowrap">{{ detail.product.category?.name || 'Umum' }}</span>
                </div>
                <p class="text-[10px] md:text-xs text-on-surface-variant mt-1">
                  {{ formatRupiah(detail.price_transaction) }} × {{ detail.quantity }}
                </p>
              </div>
            </div>
          </div>

          <!-- Receipt & Payment -->
          <div class="bg-surface-container-low p-5 md:p-8 rounded-2xl md:rounded-3xl flex flex-col justify-between gap-6">
            <div class="space-y-3 md:space-y-4">
              <div
                v-for="row in receiptRows"
                :key="row.label"
                class="flex justify-between text-sm md:text-base"
              >
                <span class="text-on-surface-variant">{{ row.label }}</span>
                <span class="font-semibold">{{ row.value }}</span>
              </div>
              <div class="border-t border-outline-variant/30 pt-3 md:pt-4 flex justify-between items-center">
                <span class="text-base md:text-lg font-bold text-primary">Total Bayar</span>
                <span class="text-base md:text-lg font-black text-primary">{{ formatRupiah(transaction.grand_total) }}</span>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="p-3 md:p-4 bg-surface-container-lowest rounded-xl md:rounded-2xl flex items-center gap-3">
              <span class="material-symbols-outlined text-primary">receipt_long</span>
              <div>
                <p class="text-[10px] md:text-xs font-bold uppercase tracking-wider text-on-surface-variant">
                  Metode Pembayaran
                </p>
                <p class="text-sm md:text-base font-semibold text-on-surface uppercase">{{ transaction.payment_method }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Help Banner -->
      <section class="bg-primary-container rounded-2xl md:rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 md:gap-6 overflow-hidden relative">
        <!-- Decoration -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="relative z-10">
          <h3 class="text-lg md:text-xl font-bold text-white mb-1 md:mb-2 font-headline">
            Butuh Bantuan dengan Pesanan?
          </h3>
          <p class="text-primary-fixed-dim text-sm md:text-base max-w-sm leading-relaxed">
            Hubungi merchant langsung jika ada permintaan khusus atau perubahan mendadak.
          </p>
        </div>

        <div class="flex gap-3 relative z-10 flex-shrink-0 w-full md:w-auto">
          <button
            @click="contactMerchant"
            class="flex-1 md:flex-none bg-white text-primary px-5 md:px-6 py-2.5 md:py-3 rounded-full font-bold transition-transform hover:scale-105 active:scale-95 text-sm"
          >
            Hubungi Kantin
          </button>
          <button
            @click="cancelOrder"
            class="flex-1 md:flex-none border border-white/30 text-white px-5 md:px-6 py-2.5 md:py-3 rounded-full font-bold transition-transform hover:scale-105 active:scale-95 text-sm"
          >
            Batalkan
          </button>
        </div>
      </section>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'

const props = defineProps({
  id: String
})

const page = usePage()

// User
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})

// Search & Nav
const showMobileSearch = ref(false)
const searchQuery = ref('')
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]

// Transaction Data
const transaction = ref(null)
const isLoading = ref(true)

// Status steps mapping
const getStatusSteps = (status) => {
  const steps = [
    {
      key: 'received',
      label: 'Diterima',
      description: 'Pesanan telah diterima',
      icon: 'check_circle',
      state: 'pending',
      progress: 0,
    },
    {
      key: 'processing',
      label: 'Diproses',
      description: 'Pesanan sedang diproses',
      icon: 'restaurant',
      state: 'pending',
      progress: 0,
    },
    {
      key: 'ready',
      label: 'Siap Diambil',
      description: 'Silakan ambil di counter',
      icon: 'shopping_basket',
      state: 'pending',
      progress: 0,
    },
  ]

  if (status === 'pending') {
     steps[0].state = 'active'
     steps[0].progress = 50
  } else if (status === 'onprocess') {
     steps[0].state = 'done'
     steps[1].state = 'active'
     steps[1].progress = 50
  } else if (status === 'success' || status === 'completed' || status === 'paid') {
     steps[0].state = 'done'
     steps[1].state = 'done'
     steps[2].state = 'active'
     steps[2].progress = 100
  }

  return steps
}

const statusSteps = computed(() => {
  if (!transaction.value) return getStatusSteps('pending')
  return getStatusSteps(transaction.value.status)
})

// Fetch Data
const fetchTransaction = async () => {
  try {
    isLoading.value = true
    const response = await window.axios.get(`/api/v1/transactions/${props.id}/status`)
    transaction.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch transaction:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchTransaction()
})

// Receipt rows
const subtotal = computed(() => transaction.value?.grand_total || 0)
const receiptRows = computed(() => [
  { label: 'Subtotal', value: formatRupiah(subtotal.value) },
])

// Helpers
function formatRupiah(amount) {
  return 'Rp ' + Math.floor(amount || 0).toLocaleString('id-ID')
}

// Methods
function contactMerchant() {
  console.log('Contacting merchant for order:', transaction.value?.order_id)
}

function cancelOrder() {
  console.log('Cancel order:', transaction.value?.order_id)
}

function logout() {
  router.post(route('logout'))
}
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

.font-headline {
  font-family: 'Manrope', sans-serif;
}

.glass-nav {
  background: rgba(250, 249, 246, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>