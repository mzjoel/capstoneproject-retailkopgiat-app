<template>
  <div class="bg-background min-h-screen pb-24">

    <!-- HEADER -->
    <div class="p-6 font-bold text-xl text-primary">
      Order History
    </div>

    <div class="px-6">

      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-xl p-5 mb-6 shadow"
      >

        <div class="flex justify-between mb-2">
          <span class="text-sm text-gray-500">
            {{ order.date }}
          </span>

          <span
            class="text-xs px-2 py-1 rounded-full"
            :class="{
              'bg-green-100 text-green-600': order.status === 'Delivered',
              'bg-red-100 text-red-600': order.status === 'Canceled'
            }"
          >
            {{ order.status }}
          </span>
        </div>

        <h3 class="font-bold text-lg mb-1">
          {{ order.title }}
        </h3>

        <p class="text-sm text-gray-500 mb-2">
          {{ order.desc }}
        </p>

        <p class="font-bold mb-4">
          Rp {{ order.price.toLocaleString() }}
        </p>

        <div class="flex gap-2">
          <button class="border px-4 py-2 rounded-full text-sm">
            Detail
          </button>

          <button
            class="bg-primary text-white px-4 py-2 rounded-full text-sm"
          >
            Order Again
          </button>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const orders = ref([
  {
    id: 1,
    date: "24 Oct 2023",
    status: "Delivered",
    title: "Healthy Bowl",
    desc: "Extra Avocado • 1 Item",
    price: 45000
  },
  {
    id: 2,
    date: "21 Oct 2023",
    status: "Delivered",
    title: "Burger Combo",
    desc: "Fries + Drink • 3 Items",
    price: 62500
  },
  {
    id: 3,
    date: "15 Oct 2023",
    status: "Canceled",
    title: "Pizza",
    desc: "1 Item",
    price: 38000
  }
])
</script>