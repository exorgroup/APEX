<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Minus, Plus } from 'lucide-vue-next';

// Create reactive counter value
const counter = ref<number>(0);

// Increment function
const increment = () => {
    counter.value++;
};

// Decrement function
const decrement = () => {
    counter.value--;
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-background p-4">
        <Head title="Vue Counter Test" />
        
        <Card class="w-full max-w-md">
            <CardHeader class="text-center">
                <CardTitle class="text-2xl">Vue Counter Test</CardTitle>
                <CardDescription>
                    Testing Vue reactivity with a simple counter
                </CardDescription>
            </CardHeader>
            
            <CardContent class="space-y-6">
                <!-- Counter Display -->
                <div class="grid gap-2">
                    <Label for="counter-input">Counter Value</Label>
                    <Input 
                        id="counter-input"
                        type="number"
                        v-model.number="counter"
                        class="text-center text-2xl font-bold"
                        readonly
                    />
                </div>
                
                <!-- Control Buttons -->
                <div class="flex gap-4">
                    <Button 
                        @click="decrement"
                        variant="outline"
                        size="lg"
                        class="flex-1"
                    >
                        <Minus class="mr-2 h-4 w-4" />
                        Decrement
                    </Button>
                    
                    <Button 
                        @click="increment"
                        variant="default"
                        size="lg"
                        class="flex-1"
                    >
                        <Plus class="mr-2 h-4 w-4" />
                        Increment
                    </Button>
                </div>
                
                <!-- Status Message -->
                <div class="rounded-lg bg-muted p-4 text-center">
                    <p class="text-sm text-muted-foreground">
                        Current value: <span class="font-semibold">{{ counter }}</span>
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        Vue is {{ counter !== 0 ? 'working properly!' : 'ready to test!' }}
                    </p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>