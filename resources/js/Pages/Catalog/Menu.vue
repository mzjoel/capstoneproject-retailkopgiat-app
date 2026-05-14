<template>
  <div class="text-on-surface">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-50 bg-[#faf9f6]/80 backdrop-blur-xl">
      <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
        <span class="text-xl font-black text-[#800000] font-headline">
          Koperasi Giat
        </span>
      </div>
    </nav>

    <!-- MAIN -->
    <main class="pt-28 pb-32 px-6 max-w-7xl mx-auto">

      <!-- HEADER -->
      <header class="mb-12">
        <h1 class="text-4xl font-bold text-primary-container mb-3">
          Menu Segar Hari Ini
        </h1>
        <p class="text-gray-500">
          Pilihan favorit untuk mendinginkan harimu di kampus.
        </p>
      </header>

      <!-- CATEGORY -->
      <div class="flex gap-3 mb-10 overflow-x-auto">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="selectedCategory = cat"
          :class="[
            'px-6 py-2 rounded-full whitespace-nowrap',
            selectedCategory === cat
              ? 'bg-red-800 text-white'
              : 'bg-gray-200 text-gray-600'
          ]"
        >
          {{ cat }}
        </button>
      </div>

      <!-- PRODUCT GRID -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

        <div
          v-for="item in filteredMenu"
          :key="item.id"
          class="bg-white rounded-xl overflow-hidden shadow flex flex-col"
        >

          <!-- IMAGE -->
          <img
            :src="item.image"
            class="w-full h-48 object-cover"
          />

          <!-- CONTENT -->
          <div class="p-6 flex flex-col flex-grow">
            <div class="flex justify-between mb-3">
              <h3 class="font-bold">{{ item.name }}</h3>
              <span class="text-red-800 font-bold">
                Rp {{ item.price }}
              </span>
            </div>

            <p class="text-sm text-gray-500 mb-6">
              {{ item.desc }}
            </p>

            <!-- BUTTON -->
            <button
              @click="addToCart(item)"
              class="mt-auto bg-red-800 text-white py-3 rounded-xl"
            >
              Add to Cart
            </button>
          </div>

        </div>

      </div>
    </main>

    <!-- BOTTOM NAV -->
    <nav class="md:hidden fixed bottom-0 w-full flex justify-around py-4 bg-white">
      <div>Beranda</div>
      <div class="text-red-800 font-bold">Menu</div>
      <div>Pesanan</div>
      <div>Profil</div>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const categories = ref([
  'Semua',
  'Minuman',
  'Camilan',
  'Kopi'
])

const selectedCategory = ref('Semua')

const menu = ref([
  {
    id: 1,
    name: 'Es Teh Manis',
    price: 5000,
    category: 'Minuman',
    desc: 'Teh segar',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCRtMpXHdk7sJrxO4-DhkLrC9zedfEB6kqh9IbKGXFJVCNVQ4ZXN5QZz9-oX854cHNmFjGJvO7Ydw3NBCanLvimll-BSyD-fBJbSnI3xr9SBjwXGLzrji6T7jGvOEdJYhLwN1VubgWamZ58D8XCp3L3qPYPf2MIj214MyTkaP4FzdXPh3vPQc_eGtk-ffeR3LXX8XEHrcM_N2JWYSOiWXbAQFOEadgP7_fcDELulwGmKZoo_F-5XNodMLJhiKz9YRs0Spz651SixNA5'
  },
  {
    id: 2,
    name: 'Es Jeruk',
    price: 10000,
    category: 'Minuman',
    desc: 'Jeruk segar',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDHaFGRcaDvJ-E9kq4ObVweNsE3u1as_329cugooxNJFYPQSnCqKvqqRfa6kr3Gmu4JSkE421j2wcoS7dJb3_FJHlB6pkIB-5ZXOmKR07t7xzj77k-eMM9EDRJV_-cbkEXIcY2VZ7vaJ1Ir8sn5Ps-0To5AG4dcSvxdfDf3gM1GcxOxlJTqXcs_MHEHsVrFvV_HS7CNlOV2YXmrICjc5IsP0t2wirdXwNZCRvXZP9VFR05XYudRC83bAK-L9nd5YQVAQJOYvGEbed2L'
  },
  {
    id: 3,
    name: 'Cold Brew',
    price: 18000,
    category: 'Kopi',
    desc: 'Low acid coffee',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAQCtAiFaAvePU0TAmEzP2rhtPZQBCdJAMWBkESNhxa9z5DJB0W8DEVfenF1McfFoHGyAisOghhJSgXIJ0X013CYlrlOcEkehkBZrZ6GBlwaiUlodLgapY4lhtWtN9xC0g9_nYOm2OkmRc01JwjOunpOh6K1JLBUXF1xTZsJmNtqS1rX6FE5Bhdf1dDY0H2AveeuEm1UY2FQq_HW6Edga1cnsS4K-EXHhocEgGWbJFJ04vo_e9eXNmfqrbetCHblhQmpwuwxSzha1SB'
  }
])

const filteredMenu = computed(() => {
  if (selectedCategory.value === 'Semua') return menu.value
  return menu.value.filter(m => m.category === selectedCategory.value)
})

const addToCart = (item) => {
  console.log('Added:', item)
}
</script>

<style>
.font-headline {
  font-family: 'Manrope', sans-serif;
}
</style>