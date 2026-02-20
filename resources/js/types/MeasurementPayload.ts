import type * as z from 'zod';
import type { formSchema } from '../pages/Measurement/features/measurementFormSchema';
import type { FormDataConvertible } from '@inertiajs/vue3'; // Import FormDataConvertible

type FormSchema = z.infer<typeof formSchema>;

// Define the type for the payload sent to the backend
// It should contain all fields from FormSchema, but with other_measurements as string[]
export interface MeasurementPayload extends FormSchema {
    other_measurements?: string[] | null; // Backend expects an array or null
    customer_id: number;
    [key: string]: FormDataConvertible | undefined; // Add index signature
}
