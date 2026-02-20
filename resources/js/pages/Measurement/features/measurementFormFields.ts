import type * as z from 'zod';
import type { FormFieldDefinition } from '@/components/BaseFormDialog.vue';
import type { formSchema } from './measurementFormSchema';

export const formFields: FormFieldDefinition<z.infer<typeof formSchema>>[] = [
  { name: 'shoulder', label: 'Shoulder', type: 'number', placeholder: 'cm' },
  { name: 'chest', label: 'Chest', type: 'number', placeholder: 'cm' },
  { name: 'waist', label: 'Waist', type: 'number', placeholder: 'cm' },
  { name: 'sleeve', label: 'Sleeve', type: 'number', placeholder: 'cm' },
  { name: 'other_measurements', label: 'Other Measurements', type: 'textarea', placeholder: 'e.g., Neck: 38cm, Bicep: 30cm' },
];
