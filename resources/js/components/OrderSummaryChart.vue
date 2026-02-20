<script setup lang="ts">
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    chartData: Record<string, { count: number; label: string; color: string; }>;
}>();

const data = computed(() => {
    const labels = Object.values(props.chartData).map(item => item.label);
    const counts = Object.values(props.chartData).map(item => item.count);

    const colorMap: Record<string, string> = {
        gray: '#A0AEC0', // gray-500 panding
        yellow: '#FBBF24', // amber-400 processing
        green: '#34D399', // green-400 ready
    };
    const backgroundColors = Object.values(props.chartData).map(item => colorMap[item.color] || '#000000');

    return {
        labels,
        datasets: [
            {
                label: 'Order Count',
                backgroundColor: backgroundColors,
                data: counts
            }
        ]
    };
});

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            }
        }
    }
};
</script>

<template>
    <Bar :data="data" :options="options" />
</template>
