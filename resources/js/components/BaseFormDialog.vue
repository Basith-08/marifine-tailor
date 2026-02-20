<script setup lang="ts" generic="T extends Record<string, any>">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { watch, unref } from 'vue'; // Import watch
import type * as z from 'zod'
import BaseDatePicker from '@/components/BaseDatePicker.vue'
import BaseSelect from '@/components/BaseSelect.vue'
import Button from '@/components/ui/button/Button.vue'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'

// Field definition for the form
export interface FormFieldDefinition<T> {
  name: keyof T
  label: string
  type?: string
  placeholder?: string
  options?: { value: string | number; label: string }[]; // For select type
}

interface Props {
  schema: z.ZodObject<any>
  initialValues?: T
  fields: FormFieldDefinition<T>[]
  title: string
  description?: string
  open: boolean
  class?: string
}

const props = defineProps<Props>()
const emit = defineEmits(['submit', 'update:open'])

// Form initialization
const form = useForm({
  validationSchema: toTypedSchema(props.schema),
  // initialValues: props.initialValues, // Remove from here
})

watch(() => unref(props.initialValues), (newValues) => {
  if (newValues) {
    form.resetForm({ values: newValues });
  } else {
    form.resetForm();
  }
}, { immediate: true }); // Use immediate: true to run on initial mount

// Submit handler
const onSubmit = form.handleSubmit((values) => {
  emit('submit', values, form)
})

// Handle dialog close
function onOpenChange(open: boolean) {
  if (!open) {
    form.resetForm()
  }
  emit('update:open', open)
}
</script>

<template>
  <Dialog :open="props.open" @update:open="onOpenChange">
    <DialogContent :class="cn('sm:max-w-[425px]', props.class)">
      <DialogHeader>
        <DialogTitle>{{ props.title }}</DialogTitle>
        <DialogDescription v-if="props.description">
          {{ props.description }}
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="onSubmit" class="space-y-4 py-4">
        <!-- Default slot to allow custom fields. This will take precedence over auto-generated fields if provided. -->
        <slot :form="form" />

        <!-- Render fields based on props.fields if no default slot content is provided -->
        <template v-if="!$slots.default">
          <template v-for="field in props.fields" :key="field.name.toString()">
            <FormField v-slot="{ componentField, errors }" :name="field.name.toString()">
              <FormItem>
                <FormLabel>{{ field.label }}</FormLabel>
                <FormControl>
                  <BaseSelect v-if="field.type === 'select'" v-bind="componentField" :options="field.options || []"
                    :placeholder="field.placeholder" :error="errors[0]" />
                  <BaseDatePicker v-else-if="field.type === 'date'" v-bind="componentField"
                    :placeholder="field.placeholder" :error="errors[0]" />
                  <Textarea v-else-if="field.type === 'textarea'" :placeholder="field.placeholder"
                    v-bind="componentField" :class="{ 'border-destructive': errors.length }" />
                  <Input v-else :type="field.type || 'text'" :placeholder="field.placeholder" v-bind="componentField"
                    :class="{ 'border-destructive': errors.length }" />
                </FormControl>
                <FormMessage />
              </FormItem>
            </FormField>
          </template>
        </template>

        <DialogFooter>
          <Button type="button" variant="outline" @click="onOpenChange(false)">
            Cancel
          </Button>
          <Button type="submit">
            Save changes
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
