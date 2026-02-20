import { type SortingState } from '@tanstack/vue-table';
import { ref } from 'vue';
import type { Customer } from '@/types/customer';
import type { PaginationProps } from '@/types/pagination';

export function useCustomerState(props: {
    customers: PaginationProps<Customer>;
    filters: {
        search?: string,
        sort?: string,
        direction?: string
    }
}) {
    const search = ref(props.filters.search ?? '');

    const dialogState = ref<{ mode: 'create' | 'edit' | 'closed'; data?: Customer }>({
        mode: 'closed',
    });

    const sorting = ref<SortingState>([
        {
            id: props.filters.sort ?? 'name',
            desc: props.filters.direction === 'desc',
        },
    ]);

    return {
        search,
        dialogState,
        sorting,
    };
}
