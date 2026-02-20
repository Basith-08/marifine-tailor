<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CustomerGrowthChart from '@/components/CustomerGrowthChart.vue';
import OrderSummaryChart from '@/components/OrderSummaryChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';


const props = defineProps<{
    orderSummary: Record<string, { count: number; label: string; color: string; }>;
    customerGrowth: { month: string; count: number; }[];
}>();

const colorMap: Record<string, string> = {
    pending: '#A0AEC0', // gray-500
    processing: '#FBBF24', // amber-400
    ready: '#34D399', // green-400
};

const getBackgroundColor = (colorKey: string) => {
    return colorMap[colorKey.toLowerCase()] || '#A0AEC0'; // Default to gray if not found
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout >
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    v-for="summary in props.orderSummary" :key="summary.label"
                    class="relative  overflow-hidden rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                    :style="{ backgroundColor: getBackgroundColor(summary.label) }"
                >
                    <h3 class="font-semibold text-lg">{{ summary.label }}</h3>
                    <p class="text-3xl font-bold">{{ summary.count }}</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-4">
                 <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border" style="height: 400px;">
                    <h3 class="font-semibold text-lg mb-4">Order Status Summary</h3>
                    <OrderSummaryChart :chart-data="props.orderSummary" />
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border" style="height: 400px;">
                    <h3 class="font-semibold text-lg mb-4">Customer Growth (Last 12 Months)</h3>
                    <CustomerGrowthChart :chart-data="props.customerGrowth" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
