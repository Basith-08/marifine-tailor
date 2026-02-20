import * as z from 'zod';

export const formSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  phone: z.string().optional(),
  address: z.string().optional(),
})
