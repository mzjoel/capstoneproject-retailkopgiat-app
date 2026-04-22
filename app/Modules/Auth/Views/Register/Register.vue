<script setup>
    import { ref } from 'vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import AccountForm from './Components/AccountForm.vue';
    import PreferenceForm from './Components/PreferenceForm.vue';

    const currentStep = ref(1);

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        preferences: {
            taste: null,
        }
    });
    

    function nextStep() {
        currentStep.value++;
    }

    function prevStep() {
        currentStep.value--;
    }

    function submit() {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    }
</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun Baru" />

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Pendaftaran Mahasiswa</h2>
            <p class="text-sm text-gray-600">Lengkapi data untuk mendapatkan rekomendasi menu cerdas.</p>
            
            <!-- Progress Stepper -->
            <div class="flex items-center mt-4 space-x-4">
                <div :class="['h-2 flex-1 rounded-full', currentStep >= 1 ? 'bg-indigo-600' : 'bg-gray-200']"></div>
                <div :class="['h-2 flex-1 rounded-full', currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200']"></div>
            </div>
        </div>

        <!-- Step 1: Account Details -->
        <AccountForm 
            v-if="currentStep === 1" 
            :form="form" 
            @next="nextStep" 
        />

        <!-- Step 2: Preferences -->
        <PreferenceForm 
            v-else 
            :form="form" 
            :processing="form.processing"
            @back="prevStep" 
            @submit="submit" 
        />

        <div class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun? 
            <a :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-500">
                Masuk di sini
            </a>
        </div>
    </GuestLayout>
</template>