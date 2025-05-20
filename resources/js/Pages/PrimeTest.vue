<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';

// Components
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Card from 'primevue/card';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Calendar from 'primevue/calendar';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';

// State
const count = ref(0);
const inputValue = ref('');
const selectedDate = ref(null);
const dialogVisible = ref(false);
const toast = useToast();

// Dropdown options
const cities = ref([
    { name: 'New York', code: 'NY' },
    { name: 'London', code: 'LDN' },
    { name: 'Paris', code: 'PRS' },
    { name: 'Tokyo', code: 'TYO' }
]);
const selectedCity = ref(null);

// Table data
const products = ref([
    { id: 1, name: 'Product A', category: 'Electronics', price: 100 },
    { id: 2, name: 'Product B', category: 'Books', price: 25 },
    { id: 3, name: 'Product C', category: 'Clothing', price: 50 }
]);

// Methods
const showToast = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Message sent successfully', life: 3000 });
};

const showDialog = () => {
    dialogVisible.value = true;
};

const hideDialog = () => {
    dialogVisible.value = false;
    showToast();
};
</script>

<template>
    <Head title="PrimeVue Test" />
    
    <!-- Toast for notifications -->
    <Toast />
    
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">PrimeVue Components Demo</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Counter Card -->
                <Card>
                    <template #title>Counter Demo</template>
                    <template #content>
                        <p class="mb-3">Count: {{ count }}</p>
                        <div class="flex flex-wrap gap-2">
                            <Button label="Increment" icon="pi pi-plus" @click="count++" />
                            <Button label="Decrement" icon="pi pi-minus" severity="secondary" @click="count--" />
                            <Button label="Reset" icon="pi pi-refresh" severity="danger" @click="count = 0" />
                        </div>
                    </template>
                </Card>
                
                <!-- Form Elements Card -->
                <Card>
                    <template #title>Form Elements</template>
                    <template #content>
                        <div class="mb-3">
                            <label class="block mb-1">Input Text</label>
                            <InputText v-model="inputValue" placeholder="Type something" class="w-full" />
                            <p class="mt-1 text-sm">You typed: {{ inputValue }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block mb-1">Dropdown</label>
                            <Dropdown v-model="selectedCity" :options="cities" optionLabel="name" 
                                      placeholder="Select a City" class="w-full" />
                        </div>
                        
                        <div class="mb-3">
                            <label class="block mb-1">Calendar</label>
                            <Calendar v-model="selectedDate" showIcon class="w-full" />
                        </div>
                        
                        <Button label="Open Dialog" icon="pi pi-external-link" @click="showDialog" />
                    </template>
                </Card>
            </div>
            
            <!-- DataTable Card -->
            <Card class="mb-6">
                <template #title>DataTable Example</template>
                <template #content>
                    <DataTable :value="products" stripedRows>
                        <Column field="id" header="ID"></Column>
                        <Column field="name" header="Name"></Column>
                        <Column field="category" header="Category"></Column>
                        <Column field="price" header="Price">
                            <template #body="slotProps">
                                ${{ slotProps.data.price }}
                            </template>
                        </Column>
                        <Column header="Actions">
                            <template #body>
                                <Button icon="pi pi-pencil" rounded text severity="success" class="mr-2" />
                                <Button icon="pi pi-trash" rounded text severity="danger" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
            
            <!-- Dialog Component -->
            <Dialog v-model:visible="dialogVisible" header="Dialog Example" :style="{ width: '30rem' }">
                <p class="m-0">
                    This is a sample dialog component from PrimeVue. You can use it to display important information
                    or collect user input.
                </p>
                <template #footer>
                    <Button label="Close" icon="pi pi-times" @click="hideDialog" text />
                    <Button label="Save" icon="pi pi-check" @click="hideDialog" autofocus />
                </template>
            </Dialog>
        </div>
    </div>
</template>