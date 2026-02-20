import type { ColumnDef } from '@tanstack/vue-table'
import { h } from 'vue'

import DataTableDropDown from '@/components/DataTableDropDown.vue' // Import DataTableDropDown
import type { Measurement } from '@/types/measurement';

interface TableMeta {
    openDialog: (mode: 'create' | 'edit', data?: Measurement) => void;
    handleDelete: (measurementId: number) => void;
}


export const columns: ColumnDef<Measurement>[] = [
    {
        id: 'serial_number',
        header: '#',
        cell: ({ row }) => h('div', {}, row.index + 1), // Serial number column
    },
    {
        accessorKey: 'shoulder',
        header: 'Shoulder (cm)',
        cell: ({ row }) => row.getValue('shoulder') ?? '-'
    },
    {
        accessorKey: 'chest',
        header: 'Chest (cm)',
        cell: ({ row }) => row.getValue('chest') ?? '-'
    },
    {
        accessorKey: 'waist',
        header: 'Waist (cm)',
        cell: ({ row }) => row.getValue('waist') ?? '-'
    },
    {
        accessorKey: 'sleeve',
        header: 'Sleeve (cm)',
        cell: ({ row }) => row.getValue('sleeve') ?? '-'
    },
    {
        accessorKey: 'other_measurements', // New column for other_measurements
        header: 'Other Measurements',
        cell: ({ row }) => {
            let measurements = row.original.other_measurements;
            console.log('other_measurements in table cell:', measurements); // Debugging line

            // Attempt to parse JSON string if it's a string
            if (typeof measurements === 'string') {
                try {
                    measurements = JSON.parse(measurements);
                } catch (e) {
                    // Not a valid JSON string, treat as plain string
                }
            }

            if (Array.isArray(measurements) && measurements.length > 0) {
                // Convert array of key:value strings (or objects) to a readable format
                return measurements.map(item => {
                    if (typeof item === 'object' && item !== null) {
                        return Object.entries(item).map(([key, value]) => `${key}: ${value}`).join(', ');
                    }
                    return item; // Assume it's already a string
                }).join('; ');
            } else if (typeof measurements === 'object' && measurements !== null) {
                // If it's an object (after parsing or if it arrived as object), format it
                return Object.entries(measurements)
                    .map(([key, value]) => `${key}: ${value}`)
                    .join('; ');
            } else if (typeof measurements === 'string' && measurements.length > 0) {
                return measurements; // Display as plain string if not parsable or array
            }
            return '-';
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Date Created',
        cell: ({ row }) => new Date(row.original.created_at).toLocaleDateString(),
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row, table }) => {
            const measurement = row.original;
            const meta = table.options.meta as TableMeta;
            return h(DataTableDropDown, {
                item: measurement,
                routePrefix: 'customers.measurements', // Adjust routePrefix for nested resource
                onEdit: (item) => meta.openDialog('edit', item as Measurement),
                onDelete: (itemId) => meta.handleDelete(itemId),
                showManageMeasurements: false, // Hide "Manage Measurements" for measurements themselves
                showViewDetails: false,
            });
        },
    },
]
