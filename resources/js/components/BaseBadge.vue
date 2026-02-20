<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils'; // Assuming cn utility for class merging

const props = defineProps<{
    color?: 'gray' | 'yellow' | 'green' | 'red' | 'blue' | 'default';
    class?: string;
}>();

const colorClasses = computed(() => {
    switch (props.color) {
        case 'gray':
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
        case 'yellow':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200';
        case 'green':
            return 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200';
        case 'red':
            return 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-200';
        case 'blue':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-200';
        default:
            return 'bg-primary/10 text-primary-foreground';
    }
});
</script>

<template>
    <span :class="cn(
        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
        colorClasses,
        props.class
    )">
        <slot />
    </span>
</template>