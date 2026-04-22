<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    form: Object,
    processing: Boolean
});

defineEmits(['back', 'submit']);

const tasteOptions = [
    { id: 'sweet', label: 'Manis' },
    { id: 'spicy', label: 'Pedas' },
    { id: 'savory', label: 'Gurih/Asin' },
    { id: 'bitter', label: 'Pahit (Kopi)' }
];

</script>

<template>
     <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900">Bantu AI Mengenal Anda</h3>
            <p class="text-sm text-gray-600">Preferensi ini akan membantu kami memberikan rekomendasi menu yang sesuai saat cuaca berubah.</p>
        </div>

        <div>
            <InputLabel value="Selera Rasa Dominan" />
            <div class="grid grid-cols-2 gap-2 mt-2">
                <button 
                    v-for="option in tasteOptions" 
                    :key="option.id"
                    type="button"
                    @click="form.preferences.taste = option.id"
                    :class="[
                        'px-4 py-2 text-sm rounded-lg border transition-all',
                        form.preferences.taste === option.id 
                        ? 'bg-indigo-600 text-white border-indigo-600' 
                        : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-400'
                    ]"
                >
                    {{ option.label }}
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <SecondaryButton @click="$emit('back')">
                Kembali
            </SecondaryButton>
            
            <PrimaryButton @click="$emit('submit')" :class="{ 'opacity-25': processing }" :disabled="processing">
                Selesaikan Pendaftaran
            </PrimaryButton>
        </div>
    </div>
</template>