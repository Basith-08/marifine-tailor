<script setup lang="ts">
import { computed } from 'vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils'; // Assuming cn utility for class merging

const props = defineProps<{
    modelValue?: string | number;
    options: { value: string | number; label: string }[];
    placeholder?: string;
    error?: string;
    id?: string;
    name?: string;
    class?: string;
    disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

const internalValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
  <Select v-model="internalValue" :disabled="props.disabled">
    <SelectTrigger :id="id" :name="name" :class="cn('w-full', props.class, { 'border-destructive': error })">
      <SelectValue :placeholder="placeholder" />
    </SelectTrigger>
    <SelectContent>
      <SelectGroup>
        <SelectLabel v-if="placeholder">{{ placeholder }}</SelectLabel>
        <SelectItem v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </SelectItem>
      </SelectGroup>
    </SelectContent>
  </Select>
  <p v-if="error" class="text-destructive text-sm mt-1">{{ error }}</p>
</template>