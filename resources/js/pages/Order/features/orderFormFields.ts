import type * as z from 'zod';
import type { FormFieldDefinition } from '@/components/BaseFormDialog.vue';
import type { Customer } from '@/types/customer';
import type { OrderStatus } from '@/types/order_status';
import type { getOrderFormSchema } from './orderFormSchema';

export function getOrderFormFields(customers: Customer[], orderStatuses: OrderStatus[]): FormFieldDefinition<z.infer<ReturnType<typeof getOrderFormSchema>>>[] {
    return [
        { name: 'customer_id', label: 'Customer', type: 'select', options: customers.map(c => ({ value: c.id, label: c.name })), placeholder: 'Select a customer' },
        { name: 'item_type', label: 'Item Type', placeholder: 'e.g., Shirt, Pants' },
        { name: 'order_date', label: 'Order Date', type: 'date' },
        { name: 'deadline', label: 'Deadline', type: 'date' },
        { name: 'status', label: 'Status', type: 'select', options: orderStatuses.map(s => ({ value: s.value, label: s.name })), placeholder: 'Select a status' },
    ];
}
