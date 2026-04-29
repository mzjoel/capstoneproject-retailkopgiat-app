<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    form: Object,
});

defineEmits(['next']);
</script>

<template>
     <div class="space-y-4">
        <div>
            <InputLabel for="name" value="Nama Lengkap" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="email" value="Email Mahasiswa" />
            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
            </div>
            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
            </div>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />

        <div class="flex items-center justify-end mt-6">
            <PrimaryButton type="button" @click="$emit('next')">
                Lanjut ke Preferensi
            </PrimaryButton>
        </div>
    </div>
</template>