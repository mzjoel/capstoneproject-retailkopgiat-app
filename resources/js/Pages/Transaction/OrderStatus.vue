<template>
  <div class="bg-background min-h-screen pb-32">

    <!-- HEADER -->
    <div class="sticky top-0 bg-white px-6 py-4 flex justify-between">
      <h1 class="font-bold text-primary">Koperasi Giat</h1>
      <button>🛒</button>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-8">

      <!-- TITLE -->
      <div class="mb-8">
        <button @click="$router.back()" class="text-sm mb-2">
          ← Kembali
        </button>

        <h2 class="text-3xl font-bold text-primary">
          Pesanan {{ order.id }}
        </h2>

        <p class="text-gray-500 text-sm">
          Estimasi: {{ order.estimate }}
        </p>
      </div>

      <!-- QR -->
      <div class="bg-white p-6 rounded-xl shadow mb-8 text-center">
        <div class="w-32 h-32 bg-gray-200 mx-auto mb-3"></div>
        <p class="text-xs">Kode Pickup</p>
        <p class="text-xl font-bold text-primary">
          {{ order.pickupCode }}
        </p>
      </div>

      <!-- STATUS -->
      <div class="grid md:grid-cols-3 gap-4 mb-10">

        <div
          v-for="(step, index) in steps"
          :key="step.title"
          class="p-6 rounded-xl text-center"
          :class="getStepClass(index)"
        >
          <div class="mb-3 text-2xl">
            {{ step.icon }}
          </div>

          <h3 class="font-bold">
            {{ step.title }}
          </h3>

          <p class="text-sm text-gray-500">
            {{ step.desc }}
          </p>
        </div>

      </div>

      <!-- ITEMS -->
      <div class="mb-8">
        <h3 class="font-bold mb-4">Detail Pesanan</h3>

        <div
          v-for="item in order.items"
          :key="item.name"
          class="flex justify-between mb-3"
        >
          <div>
            <p class="font-semibold">{{ item.name }}</p>
            <p class="text-sm text-gray-500">{{ item.desc }}</p>
          </div>

          <p class="font-bold">
            Rp {{ item.price.toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- PAYMENT -->
      <div class="bg-white p-6 rounded-xl shadow mb-8">
        <div class="flex justify-between mb-2">
          <span>Subtotal</span>
          <span>Rp {{ subtotal }}</span>
        </div>

        <div class="flex justify-between mb-2">
          <span>Service</span>
          <span>Rp 1000</span>
        </div>

        <hr class="my-3">

        <div class="flex justify-between font-bold text-lg">
          <span>Total</span>
          <span class="text-primary">
            Rp {{ total }}
          </span>
        </div>
      </div>

      <!-- ACTION -->
      <div class="bg-primary text-white p-6 rounded-xl flex justify-between items-center">
        <div>
          <p class="font-bold">Butuh bantuan?</p>
          <p class="text-sm opacity-80">Hubungi kantin</p>
        </div>

        <div class="flex gap-2">
          <button class="bg-white text-primary px-4 py-2 rounded-full">
            Chat
          </button>

          <button class="border px-4 py-2 rounded-full">
            Cancel
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue"

const order = ref({
  id: "#KG-8821",
  estimate: "12:45 PM",
  pickupCode: "GIAT-X29",
  status: 1, // 0=processed,1=cooking,2=ready
  items: [
    {
      name: "Nasi Goreng",
      desc: "Level 3 • Telur",
      price: 18000
    },
    {
      name: "Es Teh",
      desc: "Less sugar",
      price: 5000
    }
  ]
})

const steps = [
  {
    title: "Diproses",
    desc: "Pesanan diterima",
    icon: "✅"
  },
  {
    title: "Dimasak",
    desc: "Sedang dimasak",
    icon: "🍳"
  },
  {
    title: "Siap",
    desc: "Siap diambil",
    icon: "🛍️"
  }
]

// STYLE ACTIVE STEP
const getStepClass = (index) => {
  if (index < order.value.status)
    return "bg-green-100"

  if (index === order.value.status)
    return "bg-yellow-100"

  return "bg-gray-100 opacity-50"
}

const subtotal = computed(() =>
  order.value.items.reduce((acc, item) => acc + item.price, 0)
)

const total = computed(() => subtotal.value + 1000)
</script>