export type OrderStatus = 'pending' | 'processing' | 'ready';

export type OrderStatusInfo = {
    name: string;
    value: OrderStatus;
    label: string;
    color: string;
};