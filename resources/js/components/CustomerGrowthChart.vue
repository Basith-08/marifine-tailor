<script setup lang="ts">
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
} from 'chart.js';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps<{
    chartData: { month: string; count: number; }[];
}>();

const data = computed(() => {
    const labels = props.chartData.map(item => item.month);
    const counts = props.chartData.map(item => item.count);

    return {
        labels,
        datasets: [
            {
                label: 'New Customers',
                backgroundColor: '#34D399', // A nice green
                borderColor: '#34D399',
                data: counts,
                fill: false,
                tension: 0.1
            }
        ]
    };
});

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
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
    <Line :data="data" :options="options" />
</template>
