import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, watch } from 'vue';
import type * as z from 'zod';
import type { Customer } from '@/types/customer';
import type { useCustomerState } from './customerState';

export function useCustomerViewModel(props: any, state: ReturnType<typeof useCustomerState>) {
    const { search, dialogState, sorting } = state;

    // Computed properties for the dialog
    const isDialogOpen = computed({
        get: () => dialogState.value.mode !== 'closed',
        set: (isOpen) => {
            if (!isOpen) {
                dialogState.value = { mode: 'closed' };
            }
        },
    });
    const dialogTitle = computed(() => dialogState.value.mode === 'create' ? 'Create New Customer' : 'Edit Customer');
    const dialogDescription = computed(() => dialogState.value.mode === 'create' ? 'Fill out the form below to add a new customer.' : 'Update customer details.');
    const initialValues = computed(() => dialogState.value.data);

    // Unified form submission handler
    function handleSubmit(values: z.infer<any>) { // Using any for schema inference for now
        if (dialogState.value.mode === 'edit' && dialogState.value.data) {
            router.put(route('customers.update', dialogState.value.data.id), values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        } else if (dialogState.value.mode === 'create') {
            router.post('/customers', values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        }
    }

    // Handle customer deletion
    function confirmDeleteCustomer(customerId: number) {
        if (confirm('Are you sure you want to delete this customer?')) {
            router.delete(route('customers.destroy', customerId));
        }
    }

    // Handler for when the edit button is clicked in the data table
    function onEdit(customer: Customer) {
        dialogState.value = { mode: 'edit', data: customer };
    }

    function openCreateDialog() {
        dialogState.value = { mode: 'create' };
    }

    // Function to send request to the server for search
    const updateParams = debounce(() => {
        const sort = sorting.value[0];
        router.get('/customers', {
            search: search.value,
            sort: sort?.id,
            direction: sort?.desc ? 'desc' : 'asc'
        }, {
            preserveState: true,
            replace: true
        });
    }, 200);

    watch(sorting, (newSorting) => {
        if (newSorting.length > 0) {
            const sort = newSorting[0];
            router.get('/customers', {
                search: search.value,
                sort: sort.id,
                direction: sort.desc ? 'desc' : 'asc',
            }, {
                preserveState: true,
                replace: true,
            });
        }
    });

    watch(search, updateParams);

    return {
        search,
        sorting,
        isDialogOpen,
        dialogTitle,
        dialogDescription,
        initialValues,
        handleSubmit,
        confirmDeleteCustomer,
        onEdit,
        openCreateDialog,
    };
}
