import type { Measurement } from "./measurement";

export type Customer = {
    id: number;
    name: string;
    phone?: string;
    address?: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string; // Add for soft deletes
    measurements?: Measurement[]; // Add the relationship
}