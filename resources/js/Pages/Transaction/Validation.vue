<template>
  <div class="bg-surface text-on-surface min-h-screen pb-24 lg:pb-0">

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

    <!-- Main -->
    <main class="pt-6 lg:pt-[8rem] pb-36 lg:pb-16 px-4 md:px-6  max-w-6xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">

        <!-- Left: Order Summary -->
        <div class="lg:col-span-7 space-y-6 md:space-y-8">

          <section>
            <div class="flex items-center justify-between mb-5 md:mb-6">
              <h2 class="text-2xl md:text-3xl font-extrabold font-headline tracking-tight text-primary">
                Konfirmasi Pesanan
              </h2>
              <Link :href="route('cart')" class="text-primary font-bold text-sm flex items-center gap-1.5 hover:underline decoration-2 underline-offset-4 transition-all">
                <span class="material-symbols-outlined text-sm">edit</span>
                Ubah Pesanan
              </Link>
            </div>

            <!-- Summary Order Card -->
            <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-[0_12px_32px_rgba(128,0,0,0.06)] border border-outline-variant/10">
              <div class="p-5 md:p-6 bg-surface-container-low/50 border-b border-outline-variant/10">
                <h3 class="font-bold text-on-surface flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-xl">shopping_basket</span>
                  Ringkasan Menu
                </h3>
              </div>
              <div class="divide-y divide-outline-variant/10">
                <div
                  v-for="item in cart.items"
                  :key="item.id"
                  class="p-4 md:p-5 flex items-center justify-between gap-4"
                >
                  <div class="flex items-center gap-4">
                    <img
                      :src="item.image"
                      :alt="item.name"
                      class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover flex-shrink-0"
                    />
                    <div>
                      <h4 class="font-bold text-on-surface text-sm md:text-base leading-tight">{{ item.name }}</h4>
                      <p class="text-xs text-on-surface-variant mt-0.5">{{ item.qty }} x {{ formatRupiah(item.price) }}</p>
                    </div>
                  </div>
                  <span class="font-bold text-primary text-sm md:text-base">
                    {{ formatRupiah(item.price * item.qty) }}
                  </span>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Right: Payment & Total -->
        <div class="lg:col-span-5">
          <div class="bg-surface-container-low p-6 md:p-8 rounded-2xl md:rounded-3xl lg:sticky lg:top-28">
            <h2 class="text-lg md:text-xl font-bold font-headline text-on-surface mb-5 md:mb-6">
              Detail Pembayaran
            </h2>

            <!-- Payment Methods -->
            <div class="space-y-3 mb-6 md:mb-8">
              <label
                v-for="method in paymentMethods"
                :key="method.value"
                class="block cursor-pointer"
              >
                <input
                  type="radio"
                  name="payment"
                  :value="method.value"
                  v-model="selectedPayment"
                  class="sr-only peer"
                />
                <div
                  :class="[
                    'flex items-center justify-between p-3 md:p-4 bg-surface-container-lowest rounded-xl border-2 transition-all',
                    selectedPayment === method.value
                      ? 'border-primary'
                      : 'border-transparent hover:border-outline-variant/40'
                  ]"
                >
                  <div class="flex items-center gap-3 md:gap-4">
                    <span class="material-symbols-outlined text-primary text-xl md:text-2xl">{{ method.icon }}</span>
                    <span class="font-semibold text-sm md:text-base">{{ method.label }}</span>
                  </div>
                  <!-- Radio indicator -->
                  <div
                    :class="[
                      'w-4 h-4 md:w-5 md:h-5 rounded-full border-2 flex items-center justify-center transition-all flex-shrink-0',
                      selectedPayment === method.value
                        ? 'border-primary bg-primary'
                        : 'border-outline-variant'
                    ]"
                  >
                    <div
                      v-if="selectedPayment === method.value"
                      class="w-1.5 h-1.5 md:w-2 md:h-2 bg-white rounded-full"
                    ></div>
                  </div>
                </div>
              </label>
            </div>

            <!-- Price Breakdown -->
            <div class="space-y-3 md:space-y-4 pt-5 md:pt-6 border-t border-outline-variant/30">
              <div class="flex justify-between text-on-surface-variant text-sm md:text-base">
                <span>Subtotal</span>
                <span>{{ formatRupiah(subtotal) }}</span>
              </div>
              <div class="flex justify-between items-center pt-3 md:pt-4">
                <span class="text-base md:text-lg font-bold text-on-surface">Total Pembayaran</span>
                <span class="text-xl md:text-2xl font-black text-primary font-headline tracking-tighter">
                  {{ formatRupiah(total) }}
                </span>
              </div>
            </div>

            <!-- Pay Button -->
            <button
              @click="handlePayment"
              :disabled="isLoading"
              class="w-full mt-8 md:mt-10 py-4 md:py-5 btn-gradient text-white rounded-full font-bold text-base md:text-lg shadow-xl hover:scale-[1.02] active:scale-95 transition-transform flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isLoading" class="animate-spin material-symbols-outlined text-sm">progress_activity</span>
              <span>{{ isLoading ? 'Memproses...' : 'Bayar Sekarang' }}</span>
              <span v-if="!isLoading" class="material-symbols-outlined">payments</span>
            </button>

            <p class="text-center text-xs text-on-surface-variant mt-4 md:mt-6 px-2 md:px-4 leading-relaxed">
              Dengan membayar, Anda menyetujui syarat dan ketentuan Koperasi Giat Mahasiswa.
            </p>
          </div>
        </div>

      </div>
    </main>

    <!-- Bottom Navigation Bar (Mobile Only) -->
    <nav class="lg:hidden glass-nav fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 rounded-t-3xl shadow-[0_-8px_24px_rgba(128,0,0,0.04)]">
      <template v-for="navItem in bottomNav" :key="navItem.label">
        <div
          v-if="navItem.label === 'Profil' || navItem.label === 'Profile'"
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
import { ref, computed, onMounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { cart } from '@/Stores/cart'
import { route } from 'ziggy-js'

//Props & Auth
const page = usePage()
const authUser = computed(() => page.props.auth.user)
const displayName = computed(() => {
  return authUser.value?.customer_profile?.name || authUser.value?.admin_profile?.name || authUser.value?.email || 'User'
})
const displayAvatar = computed(() => 
  authUser.value?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName.value)}&background=800000&color=fff`
)

const logout = () => {
  router.post(route('logout'));
}

//State
const searchQuery = ref('')
const showMobileSearch = ref(false)
const showMobileProfileMenu = ref(false)
const isLoading = ref(false)
const selectedPayment = ref('qris')
const paymentMethods = ref([
  {value: 'cash', label: 'Tunai', icon: 'payments'},
  {value: 'qris', label: 'QRIS', icon: 'qr_code_2'}
])

//Computed
const subtotal = computed(() => cart.subtotal)
const total = computed(() => subtotal.value)

// Helpers
function formatRupiah(amount) {
  return 'Rp ' + (amount || 0).toLocaleString('id-ID')
}

// Nav links
const navLinks = [
  { label: 'Beranda', url: route('dashboard'), active: true },
  { label: 'Menu', url: route('products'), active: false },
  { label: 'Pesanan', url: '#', active: false },
]

// Bottom nav
const bottomNav = computed(() => [
  { label: 'Beranda', icon: 'home',            url: route('dashboard'),          active: false },
  { label: 'Menu',    icon: 'restaurant_menu', url: route('products'),            active: false },
  { label: 'Cart',    icon: 'shopping_cart',   url: route('cart'),               active: true,  badge: cart.count > 0 ? cart.count : null },
  { label: 'Profil',  icon: 'person',          url: '#',                         active: false },
])

const validationData = ref(null)

// Methods
const validateOrder = async () => {
  if (cart.items.length === 0) return

  try {
    isLoading.value = true
    const response = await window.axios.post('/api/v1/transactions/validate', {
      items: cart.items.map(item => ({
        product_id: item.id,
        quantity: item.qty
      }))
    })
    validationData.value = response.data.data
    // Update payment methods if API provides them
    if (response.data.data.available_payment_methods) {
      paymentMethods.value = response.data.data.available_payment_methods.map(m => ({
        value: m.code,
        label: m.name,
        icon: m.code === 'qris' ? 'qr_code_2' : 'payments'
      }))
      // Set default payment to the first available method
      if (paymentMethods.value.length > 0) {
        selectedPayment.value = paymentMethods.value[0].value
      }
    }
  } catch (error) {
    console.error('Validation failed:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  validateOrder()
})

async function handlePayment() {
  if (isLoading.value) return;
  if (cart.items.length === 0) return alert('Keranjang belanja kosong!');

  const customerProfileId = authUser.value?.customer_profile?.id;
  if (!customerProfileId) return alert('Profil pelanggan tidak ditemukan.');

  isLoading.value = true;

  try {
    const response = await window.axios.post('/api/v1/transactions', {
      customer_profile_id: customerProfileId,
      payment_method: selectedPayment.value,
      items: cart.items.map(item => ({
        product_id: item.id,
        quantity: item.qty,
        price: item.price
      }))
    }, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });

    const transactionData = response.data.data;

    if (selectedPayment.value === 'qris' && transactionData.snap_token) {
      const tokenString = String(transactionData.snap_token);


      if (typeof window.snap !== 'undefined') {
        window.snap.pay(tokenString, {
          onSuccess: function() {
            cart.items = []; 
            window.location.href = `/transaction/${transactionData.transaction_id}/status`;
            // router.visit(`/transaction/${transactionData.transaction_id}/status`);
          },
          onPending: function() {
            cart.items = [];
            window.location.href = `/transaction/${transactionData.transaction_id}/status`;
          },
          onError: function(/** @type {{ status_message: any; }} */ result) {
            alert("Pembayaran Gagal: " + (result.status_message || 'Terjadi kesalahan'));
            isLoading.value = false;
            window.location.reload(); 
          },
          onClose: async function() {
            alert('Kamu menutup popup pembayaran. Pesanan Dibatalkan');
            try{
              await window.axios.post(`/api/v1/transactions/${transactionData.transaction_id}/status`, {
                status: 'cancelled'
              });
            } catch (error) {
              console.error('Failed to update transaction status:', error);
            } 
            isLoading.value = false;
            router.visit(route('dashboard'));
          }
        });
      } else {
        alert("Gagal memuat sistem pembayaran Midtrans. Silakan refresh halaman.");
        isLoading.value = false;
      }
    } 
    // JIKA PEMBAYARAN TUNAI (CASH)
    else {
      cart.items = [];
      router.visit(`/transaction/${transactionData.transaction_id}/status`);
    }

  } catch (error) {
    console.error('Checkout failed:', error);
    const errorMsg = error.response?.data?.result?.message || 'Gagal memproses pesanan.';
    alert(errorMsg);
    isLoading.value = false;
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #faf9f6;
}

.font-headline {
  font-family: 'Manrope', sans-serif;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.glass-nav {
  background: rgba(250, 249, 246, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

.btn-gradient {
  background: linear-gradient(135deg, #570000 0%, #800000 100%);
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>