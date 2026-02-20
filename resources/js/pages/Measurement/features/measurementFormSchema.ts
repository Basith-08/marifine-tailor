import * as z from 'zod';

export const formSchema = z.object({
  shoulder: z.number().positive().optional().nullable(),
  chest: z.number().positive().optional().nullable(),
  waist: z.number().positive().optional().nullable(),
  sleeve: z.number().positive().optional().nullable(),
  other_measurements: z.string().nullable().optional(), // Added for other measurements
});
