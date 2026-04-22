<template>
  <div class="bg-surface text-on-surface min-h-screen flex flex-col">
    <Head title="Daftar" />
    <!-- Login Content Wrapper -->
    <main class="flex-grow flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-5xl grid md:grid-cols-2 gap-12 items-center">

        <!-- Left: Branding Panel -->
        <BrandingPanel />

        <!-- Right: Login Form Card -->
        <div class="bg-surface-container-lowest p-8 md:p-12 rounded-[2rem] shadow-sm border border-outline-variant/10">

          <!-- Header -->
          <div class="mb-10 text-center md:text-left">
            <!-- Mobile branding -->
            <div class="md:hidden mb-6">
              <span class="text-primary-container font-headline font-extrabold text-2xl tracking-tight branding">
                Koperasi Giat
              </span>
            </div>
            <h2 class="text-3xl font-headline font-bold text-on-surface mb-2">Selamat Datang</h2>
            <p class="text-on-surface-variant font-body">
              Silahkan masukan data anda untuk mendaftar.
            </p>
          </div>

          <!-- Form -->
          <form class="space-y-8" @submit.prevent="submit">

            <!-- Email -->
            <AuthInput
              id="email"
              name="email"
              label="Email"
              type="email"
              v-model="form.email"
              :error="form.errors.email"
            />

            <AuthInput
              id="name"
              name="name"
              label="Nama Lengkap"
              type="text"
              v-model="form.name"
              :error="form.errors.name"
            />

            <!-- Password -->
            <AuthInput
              id="password"
              name="password"
              label="Password"
              type="password"
              v-model="form.password"
              :error="form.errors.password"
            >
              <template #action>
                <a
                  href="#"
                  class="text-xs font-label font-semibold text-primary hover:underline underline-offset-4 transition-colors"
                >
                  Lupa Password?
                </a>
              </template>
            </AuthInput>

             <AuthInput
              id="password_confirmation"
              name="password_confirmation"
              label="Konfirmasi Password"
              type="password"
              v-model="form.password_confirmation"
              :error="form.errors.password_confirmation"
            >
            </AuthInput>

            <!-- Submit + Social -->
            <div class="pt-4 space-y-6">
              <button
                type="submit"
                class="w-full bg-primary text-on-primary font-headline font-bold py-4 rounded-full shadow-lg hover:bg-primary-container hover:text-on-primary-container transition-all active:scale-[0.98] disabled:opacity-50"
                :disabled="form.processing"
              >
                Daftar
              </button>
            </div>
          </form>

          <!-- Register Link -->
          <div class="mt-12 text-center">
            <p class="text-on-surface-variant font-body text-sm">
              Sudah punya akun?
              <a href="#" class="text-primary font-bold ml-1 hover:underline underline-offset-4">
                Login Disini
              </a>
            </p>
          </div>

        </div>
      </div>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import BrandingPanel from './Components/BrandingPanel.vue'
import AuthInput from './Components/AuthInput.vue'
import AppFooter from './Components/AppFooter.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Manrope:wght@400;500;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

body { font-family: 'Manrope', sans-serif; }
h1, h2, h3, .branding { font-family: 'Plus Jakarta Sans', sans-serif; }

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* Tailwind custom colors — add to tailwind.config.js if using Vite/CLI */
</style>