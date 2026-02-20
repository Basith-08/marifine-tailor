import type * as z from 'zod';
import type { FormFieldDefinition } from '@/components/BaseFormDialog.vue';
import type { formSchema } from './customerFormSchema';

export const formFields: FormFieldDefinition<z.infer<typeof formSchema>>[] = [
  { name: 'name', label: 'Name', placeholder: 'Enter customer name' },
  { name: 'phone', label: 'Phone', type: 'tel', placeholder: 'Enter phone number' },
  { name: 'address', label: 'Address', type: 'textarea', placeholder: 'Enter address' },
]
