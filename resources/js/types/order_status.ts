export interface OrderStatus {
    name: string;
    value: 'pending' | 'processing' | 'ready';
    color: string;
}
