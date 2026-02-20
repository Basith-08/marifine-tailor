import { computed } from 'vue';
import type { OrderStatus, OrderStatusInfo } from '@/types/order-status';

export function useOrderStatus(allOrderStatuses: OrderStatusInfo[]) {
    const getStatusInfo = (statusValue: OrderStatus): OrderStatusInfo | undefined => {
        return allOrderStatuses.find(s => s.value === statusValue);
    };

    const getStatusLabel = (statusValue: OrderStatus): string => {
        return getStatusInfo(statusValue)?.label || statusValue;
    };

    const getStatusColor = (statusValue: OrderStatus): string => {
        return getStatusInfo(statusValue)?.color || 'default';
    };

    return {
        getStatusInfo,
        getStatusLabel,
        getStatusColor,
        allOrderStatuses: computed(() => allOrderStatuses),
    };
}
