export interface Measurement {
    id: number;
    customer_id: number;
    shoulder?: number;
    chest?: number;
    waist?: number;
    sleeve?: number;
    other_measurements?: Record<string, any>;
    created_at: string;
    updated_at: string;
}
