import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { computed, watch } from 'vue';
import type * as z from 'zod';
import type { Order } from '@/types/order';
import type { useOrderState } from './orderState';

export function useOrderViewModel(props: any, state: ReturnType<typeof useOrderState>) {
    const { search, statusFilter, dialogState, sorting } = state;

    const isDialogOpen = computed({
        get: () => dialogState.value.mode !== 'closed',
        set: (isOpen) => {
            if (!isOpen) {
                dialogState.value = { mode: 'closed' };
            }
        },
    });
    const dialogTitle = computed(() => dialogState.value.mode === 'create' ? 'Create New Order' : 'Edit Order');
    const dialogDescription = computed(() => 'Fill out the form below.');
    const initialValues = computed(() => {
        if (dialogState.value.mode === 'edit' && dialogState.value.data) {
            const orderData = dialogState.value.data;
            return {
                customer_id: orderData.customer_id,
                item_type: orderData.item_type,
                order_date: new Date(orderData.order_date),
                deadline: new Date(orderData.deadline),
                status: orderData.status,
            };
        }
        return {
            customer_id: 0,
            item_type: '',
            order_date: new Date(),
            deadline: new Date(),
            status: 'pending',
        };
    });

    function handleSubmit(values: z.infer<any>) {
        if (dialogState.value.mode === 'edit' && dialogState.value.data) {
            router.put(route('orders.update', dialogState.value.data.id), values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        } else if (dialogState.value.mode === 'create') {
            router.post(route('orders.store'), values, {
                onSuccess: () => {
                    dialogState.value = { mode: 'closed' };
                },
            });
        }
    }

    function handleDelete(orderId: number) {
        if (confirm('Are you sure you want to delete this order?')) {
            router.delete(route('orders.destroy', orderId));
        }
    }

    function onEdit(order: Order) {
        dialogState.value = { mode: 'edit', data: order };
    }

    function openCreateDialog() {
        dialogState.value = { mode: 'create' };
    }

    const updateParams = debounce(() => {
        const sort = sorting.value[0];
        router.get(route('orders.index'), {
            search: search.value,
            status: statusFilter.value,
            sort: sort?.id,
            direction: sort?.desc ? 'desc' : 'asc'
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);

    watch([search, statusFilter, sorting], updateParams, { deep: true });

    return {
        search,
        statusFilter,
        sorting,
        isDialogOpen,
        dialogTitle,
        dialogDescription,
        initialValues,
        handleSubmit,
        handleDelete,
        onEdit,
        openCreateDialog,
    };
}
