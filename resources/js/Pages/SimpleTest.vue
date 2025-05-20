<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

const count = ref(0);
const inputValue = ref('');
</script>

<template>
    <Head title="PrimeVue Documentation Test" />

    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">PrimeVue Documentation Test</h1>
        
        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">Button Example</h2>
            <p>Count: {{ count }}</p>
            <Button label="Increment" @click="count++" class="mt-2" />
        </div>

        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-2">Input Example</h2>
            <InputText v-model="inputValue" placeholder="Type something" class="w-full" />
            <p class="mt-2">You typed: {{ inputValue }}</p>
        </div>
    </div>
</template>