import { type SortingState } from '@tanstack/vue-table';
import { ref } from 'vue';
import type { Customer } from '@/types/customer';
import type { Order } from '@/types/order';
import type { PaginationProps } from '@/types/pagination';

export function useOrderState(props: {
    orders: PaginationProps<Order>;
    filters: {
        search?: string,
        status?: string,
        sort?: string,
        direction?: string
    },
    customers: Customer[],
}) {
    const search = ref(props.filters.search ?? '');
    const statusFilter = ref(props.filters.status ?? '');

    const dialogState = ref<{ mode: 'create' | 'edit' | 'closed'; data?: Order }>({
        mode: 'closed',
    });

    const sorting = ref<SortingState>([
        {
            id: props.filters.sort ?? 'deadline',
            desc: props.filters.direction !== 'asc',
        },
    ]);

    return {
        search,
        statusFilter,
        dialogState,
        sorting,
    };
}
