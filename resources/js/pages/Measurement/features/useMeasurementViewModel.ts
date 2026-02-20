import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type * as z from 'zod';
import type { Customer } from '@/types/customer';
import type { Measurement } from '@/types/measurement';
import { formSchema } from './measurementFormSchema'; // Import formSchema directly
import { MeasurementPayload } from '@/types/MeasurementPayload'; // Import MeasurementPayload

type FormSchema = z.infer<typeof formSchema>; // Infer type from schema

export function useMeasurementViewModel(props: { customer: Customer }) {
    const dialogState = ref<{ mode: 'create' | 'edit' | 'closed'; data?: Measurement }>({
        mode: 'closed',
    });

    const isDialogOpen = computed({
        get: () => dialogState.value.mode !== 'closed',
        set: (isOpen) => {
            if (!isOpen) {
                dialogState.value = { mode: 'closed' };
            }
        },
    });

    const dialogTitle = computed(() => dialogState.value.mode === 'create' ? 'Add New Measurement' : 'Edit Measurement');
    const initialValues = computed<FormSchema | undefined>(() => { // Explicitly type initialValues
        const data = dialogState.value.data;
        if (!data) return undefined;

        const values: FormSchema = {
            shoulder: typeof data.shoulder === 'string' ? parseFloat(data.shoulder) : data.shoulder ?? null,
            chest: typeof data.chest === 'string' ? parseFloat(data.chest) : data.chest ?? null,
            waist: typeof data.waist === 'string' ? parseFloat(data.waist) : data.waist ?? null,
            sleeve: typeof data.sleeve === 'string' ? parseFloat(data.sleeve) : data.sleeve ?? null,
            other_measurements: '', // Initialize as empty string for textarea
        };

        // Process other_measurements from various backend formats to a string for textarea
        if (data.other_measurements !== undefined && data.other_measurements !== null) {
            if (typeof data.other_measurements === 'string') {
                values.other_measurements = data.other_measurements;
            } else if (Array.isArray(data.other_measurements)) {
                values.other_measurements = data.other_measurements.join('\n');
            } else if (typeof data.other_measurements === 'object') {
                values.other_measurements = Object.entries(data.other_measurements)
                    .map(([key, value]) => `${key}: ${value}`)
                    .join('\n');
            }
        }
        
        return values;
    });

    function openDialog(mode: 'create' | 'edit', data?: Measurement) {
        dialogState.value = { mode, data };
    }

    function handleSubmit(values: z.infer<typeof formSchema>) {
        const payload: MeasurementPayload = {
            customer_id: props.customer.id,
            shoulder: values.shoulder,
            chest: values.chest,
            waist: values.waist,
            sleeve: values.sleeve,
            other_measurements: undefined, // Initialize here, will be set below
        };

        // Convert other_measurements string from textarea back to array for backend
        if (typeof values.other_measurements === 'string') {
            const processedMeasurements = values.other_measurements
                .split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0);
            
            payload.other_measurements = processedMeasurements.length > 0 ? processedMeasurements : null; // Send null if empty
        } else {
            payload.other_measurements = null; // Ensure it's null if not a string (e.g., already null/undefined)
        }


        if (dialogState.value.mode === 'edit' && dialogState.value.data) {
            router.put(route('measurements.update', dialogState.value.data.id), payload, {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    router.reload({ only: ['measurements'] }); // Reload measurements after successful update
                },
            });
        } else if (dialogState.value.mode === 'create') {
            router.post(route('customers.measurements.store', props.customer.id), payload, {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    router.reload({ only: ['measurements'] }); // Reload measurements after successful creation
                },
            });
        }
    }

    function handleDelete(measurementId: number) {
        if (confirm('Are you sure you want to delete this measurement?')) {
            router.delete(route('measurements.destroy', measurementId));
        }
    }

    return {
        isDialogOpen,
        dialogTitle,
        initialValues,
        openDialog,
        handleSubmit,
        handleDelete,
    }
}
