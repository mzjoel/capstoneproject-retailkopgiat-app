<template>
  <div class="bg-surface text-on-surface min-h-screen">

    <!-- TopNavBar -->
   <nav class="fixed top-0 w-full z-50 glass-nav">
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
    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8 md:py-[15vh] pb-36 md:pb-16">
      <div class="flex flex-col lg:flex-row gap-8 md:gap-12">

        <!-- Left: Cart Items -->
        <div class="flex-1 space-y-6 md:space-y-8">

          <!-- Header -->
          <header class="mb-6 md:mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-primary mb-1 md:mb-2" style="font-family: 'Manrope', sans-serif;">
              Keranjang Belanja
            </h1>
            <p class="text-on-surface-variant font-medium text-sm md:text-base">
              Anda memiliki <span class="font-bold text-on-surface">{{ cart.count }}</span> item berkualitas tinggi dalam keranjang anda.
            </p>
          </header>

          <!-- Empty State -->
          <div v-if="cart.items.length === 0" class="text-center py-20 flex flex-col items-center gap-4">
            <span class="material-symbols-outlined text-outline-variant text-6xl">shopping_cart</span>
            <p class="text-on-surface-variant font-medium">Keranjang kamu kosong.</p>
            <Link :href="route('products')" class="text-primary font-bold text-sm underline underline-offset-4">Kembali Belanja</Link>
          </div>

          <!-- Cart Item Cards -->
          <div class="space-y-4 md:space-y-6">
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="bg-surface-container-lowest rounded-xl p-4 md:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 md:gap-6 group transition-all duration-300 shadow-[0_12px_32px_rgba(128,0,0,0.04)]"
            >
              <!-- Image -->
              <div class="w-full sm:w-28 md:w-32 h-40 sm:h-28 md:h-32 rounded-xl overflow-hidden flex-shrink-0">
                <img
                  :src="item.image"
                  :alt="item.name"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
              </div>

              <!-- Info -->
              <div class="flex-1 w-full">
                <div class="flex justify-between items-start mb-1 md:mb-2 gap-2">
                  <div>
                    <h3 class="text-lg md:text-xl font-bold text-on-surface leading-tight">{{ item.name }}</h3>
                    <p class="text-xs md:text-sm text-on-surface-variant mt-0.5 line-clamp-2">{{ item.description }}</p>
                  </div>
                  <button
                    @click="cart.remove(item.id)"
                    class="text-on-surface-variant hover:text-error transition-colors p-1.5 md:p-2 flex-shrink-0"
                  >
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>

                <!-- Quantity + Price -->
                <div class="flex flex-wrap justify-between items-center mt-4 md:mt-6 gap-3">
                  <!-- Quantity Control -->
                  <div class="flex items-center bg-surface-container-low rounded-full px-2 py-1">
                    <button
                      @click="cart.decreaseQty(item.id)"
                      class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center hover:bg-surface-container-lowest rounded-full transition-colors"
                    >
                      <span class="material-symbols-outlined text-sm">remove</span>
                    </button>
                    <span class="px-3 md:px-4 font-bold text-on-surface min-w-[2rem] text-center">{{ item.qty }}</span>
                    <button
                      @click="cart.increaseQty(item.id)"
                      class="w-8 h-8 md:w-10 md:h-10 flex items-center justify-center hover:bg-surface-container-lowest rounded-full transition-colors"
                    >
                      <span class="material-symbols-outlined text-sm">add</span>
                    </button>
                  </div>

                  <!-- Item Total -->
                  <span class="text-base md:text-lg font-bold text-primary">
                    {{ formatRupiah(item.price * item.qty) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="lg:w-96 w-full">
          <div class="bg-surface-container-low rounded-2xl md:rounded-3xl p-6 md:p-8 lg:sticky lg:top-28">
            <h2 class="text-xl md:text-2xl font-bold text-primary mb-6 md:mb-8 tracking-tight" style="font-family: 'Manrope', sans-serif;">
              Ringkasan Pesanan
            </h2>

            <!-- Breakdown -->
            <div class="space-y-3 md:space-y-4 mb-6 md:mb-8">
              <div class="flex justify-between text-on-surface-variant font-medium text-sm md:text-base">
                <span>Subtotal</span>
                <span>{{ formatRupiah(cart.subtotal) }}</span>
              </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-outline-variant/20 pt-4 md:pt-6 mb-6 md:mb-10">
              <div class="flex justify-between items-baseline">
                <span class="text-base md:text-lg font-bold text-on-surface">Total Harga</span>
                <span class="text-2xl md:text-3xl font-extrabold text-primary">{{ formatRupiah(total) }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3 md:space-y-4">
              <Link
                :href="route('validation')"
                class="w-full cta-gradient text-white font-bold py-3.5 md:py-4 rounded-full text-base md:text-lg shadow-lg hover:brightness-110 active:scale-95 transition-all text-center block"
                :disabled="cart.items.length === 0"
                :class="cart.items.length === 0 ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''"
              >
                Lanjut ke Checkout
              </Link>
              <Link :href="route('products')" class="w-full bg-surface-container-lowest text-primary font-bold py-3.5 md:py-4 rounded-full text-base md:text-lg hover:bg-white transition-all text-center block">
                Kembali Belanja
              </Link>
            </div>

            <!-- Trust Badge -->
            <div class="mt-6 md:mt-8 flex items-start gap-3 p-3 md:p-4 bg-white/50 rounded-xl md:rounded-2xl border border-outline-variant/20">
              <span class="material-symbols-outlined text-secondary flex-shrink-0 mt-0.5">verified</span>
              <p class="text-xs text-on-surface-variant font-medium leading-relaxed">
                Transaksi Anda aman dan terenkripsi. Produk berasal dari unit usaha resmi Koperasi Giat.
              </p>
            </div>
          </div>
        </div>

      </div>
    </main>

    <!-- Bottom Nav (Mobile Only) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 bg-[#faf9f6]/80 backdrop-blur-md flex justify-around items-center px-4 pb-6 pt-3 rounded-t-3xl shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <Link
        v-for="item in bottomNav"
        :key="item.label"
        :href="item.url"
        :class="[
          'flex flex-col items-center justify-center transition-transform hover:scale-110 active:scale-90',
          item.active ? 'text-primary' : 'text-on-surface-variant'
        ]"
      >
        <span class="material-symbols-outlined">{{ item.icon }}</span>
        <span class="text-[10px] font-bold uppercase tracking-widest mt-1">{{ item.label }}</span>
        <span v-if="item.active" class="w-1 h-1 bg-primary-container rounded-full mt-0.5"></span>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import { route } from 'ziggy-js'

const page = usePage()
const searchQuery = ref('')
const showMobileSearch = ref(false)

// User
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&color=7F9CF5&background=EBF4FF`
})

const logout = () => router.post(route('logout'))

// Nav links
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]

// Bottom nav (mobile)
const bottomNav = [
  { icon: 'restaurant_menu', label: 'Menu', url: route('products'), active: false },
  { icon: 'assignment', label: 'Orders', url: '#', active: false },
  { icon: 'shopping_bag', label: 'Cart', url: route('cart'), active: true },
  { icon: 'person', label: 'Profile', url: '#', active: false },
]

// Computed totals
const total = computed(() => cart.subtotal)

// Helpers
function formatRupiah(amount) {
  return 'Rp ' + (amount || 0).toLocaleString('id-ID')
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

h1, h2, h3 {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.cta-gradient {
  background: linear-gradient(135deg, #570000 0%, #800000 100%);
}
</style>