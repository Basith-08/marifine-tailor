import * as z from 'zod';

export function getOrderFormSchema(statusValues: [string, ...string[]]) {
    return z.object({
        customer_id: z.number().min(1, 'Customer is required'),
        item_type: z.string().min(1, 'Item type is required'),
        order_date: z.date(),
        deadline: z.date(),
        status: z.enum(statusValues),
    });
}
