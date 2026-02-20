<script setup lang="ts">
import { format } from 'date-fns';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue?: string; // YYYY-MM-DD string
    placeholder?: string;
    error?: string;
    id?: string;
    name?: string;
    class?: string;
    disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

const date = ref<Date | undefined>(props.modelValue ? new Date(props.modelValue) : undefined);

watch(date, (newDate) => {
    emit('update:modelValue', newDate ? format(newDate, 'yyyy-MM-dd') : undefined);
});

watch(() => props.modelValue, (newModelValue) => {
    date.value = newModelValue ? new Date(newModelValue) : undefined;
});

const displayDate = computed(() => {
    return date.value ? format(date.value, 'PPP') : props.placeholder || 'Pick a date';
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                variant="outline"
                :class="cn(
                    'w-full justify-start text-left font-normal',
                    !date && 'text-muted-foreground',
                    props.class,
                    { 'border-destructive': error }
                )"
                :id="id"
                :name="name"
                :disabled="disabled"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ displayDate }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar v-model:selected="date" />
        </PopoverContent>
    </Popover>
    <p v-if="error" class="text-destructive text-sm mt-1">{{ error }}</p>
</template>