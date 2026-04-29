<template>
  <div class="bg-background text-on-surface font-body min-h-screen">

    <!-- Header -->
    <div class="pt-28 pb-10 px-6 max-w-7xl mx-auto">
      <h1 class="text-4xl font-bold text-primary mb-2">
        Wishlist Saya
      </h1>
      <p class="text-on-surface-variant">
        Temukan kembali favorit Anda
      </p>
    </div>

    <!-- Wishlist Grid -->
    <div class="px-6 max-w-7xl mx-auto pb-32 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

      <div
        v-for="item in wishlist"
        :key="item.id"
        class="group bg-white rounded-xl overflow-hidden shadow hover:shadow-lg transition"
      >
        <!-- Image -->
        <div class="relative h-64 overflow-hidden">
          <img
            :src="item.image"
            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
          />

          <!-- Favorite Button -->
          <button
            @click="toggleFavorite(item)"
            class="absolute top-4 right-4 bg-white p-2 rounded-full shadow"
          >
            <span class="material-symbols-outlined text-primary">
              {{ item.isFavorite ? 'favorite' : 'favorite_border' }}
            </span>
          </button>

          <!-- Label -->
          <div class="absolute bottom-4 left-4">
            <span
              class="px-3 py-1 text-xs rounded-full font-bold"
              :class="item.isFavorite ? 'bg-primary text-white' : 'bg-gray-200'"
            >
              {{ item.isFavorite ? 'Favorit' : 'Ready Stock' }}
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 flex flex-col justify-between h-full">
          <div>
            <div class="flex justify-between mb-2">
              <h3 class="font-bold text-lg text-primary">
                {{ item.name }}
              </h3>
              <span class="font-bold text-primary">
                {{ item.price }}
              </span>
            </div>

            <p class="text-xs text-gray-500 mb-3">
              {{ item.description }}
            </p>

            <span class="text-xs bg-primary text-white px-2 py-1 rounded">
              {{ item.category }}
            </span>
          </div>

          <!-- Button -->
          <button
            @click="addToCart(item)"
            class="mt-5 bg-primary text-white py-3 rounded-lg font-bold hover:opacity-90"
          >
            + Tambah ke Keranjang
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const wishlist = ref([
  {
    id: 1,
    name: "Seblak Spesial Giat",
    price: "Rp 15.000",
    category: "MEAL",
    description: "Bumbu kencur, kerupuk, telur",
    image: "https://lh3.googleusercontent.com/aida-public/AB6AXuAr-jetYj7F5BDDKBCa0uJBzCXx6nlOCI42Wrrc2AzbGZLRfaLJMuJvbMaBzIdJKsw4oVrobX9TPwD1-WIFVuR5wX9QjUZo-FmQ954wdk2IUSNvAQfonBxgo2RjtWSEItUvk31lEl5NHQ-M0PII_bt-FXU0D5rBgY06MLY6a05sdpFqZQ_Mtsi8riJ-5qbx82Xeljfs_a1Vmww63KfCjeWi9AjVgKtJt8b8cwrKocwJr6ZPTbgd-j7EP3dq1kOhR7QZJz9gA1otyozM",
    isFavorite: true
  },
  {
    id: 2,
    name: "Batagor Ikan Tenggiri",
    price: "Rp 12.000",
    category: "SNACK",
    description: "Ikan tenggiri, tapioka, bumbu kacang",
    image: "https://lh3.googleusercontent.com/aida-public/AB6AXuC3gVgA4kjbcj1fqaiKj72VonTN_1sKARTVMvgD753NDRd6VLdKzvQ27Wnou070-BbKxdJwu_hkBLX7Juf62S0F1qimAwx-rsBvrJ24oKO4RUZER715mqHUX3It1zu5jpvDV23ptvs6elKVwFMkqF2X5hszd1G-tw0Rp51UdBCWDAHFFzQSGd1kcLfLqO_mGxbCaMeng79exzR-NI7MMDKtlUC0yHCiS4OXfTfpiVe4dW84KBLdgSAOLsb7-L6xe3bgZomD3SG0bVOr",
    isFavorite: true
  },
  {
    id: 3,
    name: "Mie Tek-tek Abang",
    price: "Rp 18.000",
    category: "MEAL",
    description: "Mie, telur, sayuran, ayam",
    image: "https://lh3.googleusercontent.com/aida-public/AB6AXuDiBkYqlvcY0yr1HJaVXv7YJPgkB7osZ0MkQRS26SiyR2xlEEz6Htso2kvPFkvSuh06yKJQsDH26rikyguIumBlSZJO18VyEJ-FIV4HiWK5F8pMRcwiKiTW1yYHMMKy_588-vP-evYmQtzFto1xHXM1HOqLG_J7IKV4qtegNEDcYU35pVoSPfYisPypdUgZpWn__v_mlkkEvPJ3OlBMkwj2aqe_A5KVmctyAnozatkRPg7BUK2-kXXRpTT04mkJw3AuGcF-wbPM_ZBj",
    isFavorite: true
  }
])

const toggleFavorite = (item) => {
  item.isFavorite = !item.isFavorite
}

const addToCart = (item) => {
  console.log("Tambah ke keranjang:", item.name)
}
</script>