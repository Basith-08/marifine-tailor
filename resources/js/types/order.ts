import type { Customer } from "./customer";

export interface Order {
    id: number;
    customer_id: number;
    customer?: Customer;
    order_date: string;
    deadline: string;
    item_type: string;
    status: 'pending' | 'processing' | 'ready';
    created_at: string;
    updated_at: string;
}
