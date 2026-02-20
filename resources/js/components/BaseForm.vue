<script setup lang="ts" generic="T extends Record<string, any>">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import type * as z from 'zod'

interface Props {
    schema: z.ZodObject<any>
    initialValues?: T
}

const props = defineProps<Props>()
const emit = defineEmits(['submit'])

// Initialize form
const form = useForm({
    validationSchema: toTypedSchema(props.schema),
    initialValues: props.initialValues,
})

// Wrapper function for submit
const onSubmit = form.handleSubmit((values) => {
    emit('submit', values, form)
})
</script>

<template>
    <form @submit="onSubmit" class="space-y-6">
        <slot :form="form" />
    </form>
</template>