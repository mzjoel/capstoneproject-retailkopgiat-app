<template>
  <footer class="w-full py-8 px-6 mt-auto flex flex-col md:flex-row justify-between items-center bg-surface border-t border-outline-variant/10">
    <div class="mb-4 md:mb-0">
      <span class="text-sm font-bold text-primary branding">Retail Koperasi Giat</span>
      <p class="text-on-surface-variant text-xs font-medium mt-1">
        © 2025 Koperasi Giat By ZPD Capstone Project. All rights reserved.
      </p>
    </div>
    <nav class="flex gap-6">
      <a
        v-for="link in footerLinks"
        :key="link.label"
        :href="link.href"
        class="text-on-surface-variant hover:text-primary text-xs font-medium transition-opacity opacity-80 hover:opacity-100"
      >
        {{ link.label }}
      </a>
    </nav>
  </footer>
</template>

<script setup>
const footerLinks = [
  { label: 'Support', href: '#' },
  { label: 'Privacy Policy', href: '#' },
  { label: 'Terms of Service', href: '#' },
]
</script>