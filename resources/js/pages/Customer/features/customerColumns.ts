import type { ColumnDef } from '@tanstack/vue-table'
import { ArrowUpDown, ArrowDown, ArrowUp } from 'lucide-vue-next'
import { h } from 'vue'
import DropdownAction from '@/components/DataTableDropDown.vue' // This is now the generic component
import { Button } from '@/components/ui/button'
import type { Customer } from '@/types/customer'

// Define the interface for the meta object
interface CustomerTableMeta {
    onEdit: (customer: Customer) => void;
    onDelete: (customerId: number) => void;
    from: number;
}

export const columns: ColumnDef<Customer>[] = [
    {
        id: 'no',
        header: 'No',
        cell: ({ row, table }) => {
            const from = (table.options.meta as CustomerTableMeta)?.from ?? 1
            return from + row.index
        }
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => {
                const sort = column.getIsSorted();
                const icon = sort === 'asc' ? ArrowUp : sort === 'desc' ? ArrowDown : ArrowUpDown;
                return ['Name', h(icon, { class: 'ml-2 h-4 w-4' })]
            })
        },
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
    },
    {
        accessorKey: 'phone',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => {
                const sort = column.getIsSorted();
                const icon = sort === 'asc' ? ArrowUp : sort === 'desc' ? ArrowDown : ArrowUpDown;
                return ['Phone', h(icon, { class: 'ml-2 h-4 w-4' })]
            })
        },
        cell: ({ row }) => row.getValue('phone') ?? '-'
    },
    {
        accessorKey: 'address',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => {
                const sort = column.getIsSorted();
                const icon = sort === 'asc' ? ArrowUp : sort === 'desc' ? ArrowDown : ArrowUpDown;
                return ['Address', h(icon, { class: 'ml-2 h-4 w-4' })]
            })
        },
        cell: ({ row }) => row.getValue('address') ?? '-'
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row, table }) => {
            const customer = row.original;
            const meta = table.options.meta as CustomerTableMeta; // Cast with specific type

            return h('div', { class: 'relative' }, h(DropdownAction, {
                item: customer, // Pass as generic item
                routePrefix: 'customers', // Specify route prefix
                onEdit: (item) => meta.onEdit(item as Customer),
                onDelete: meta.onDelete,
                showManageMeasurements: true, // Show for customers
                showViewDetails: true,
            }))
        },
    },
]