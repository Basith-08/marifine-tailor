import type { ColumnDef } from '@tanstack/vue-table'
import { ArrowUpDown } from 'lucide-vue-next'
import { h } from 'vue'
import BaseBadge from '@/components/BaseBadge.vue'
import DropdownAction from '@/components/DataTableDropDown.vue' // Import the new component
import { Button } from '@/components/ui/button'
import type { Order } from '@/types/order'
import type { OrderStatus } from '@/types/order_status';

interface TableMeta {
    onEdit: (order: Order) => void;
    onDelete: (orderId: number) => void;
    orderStatuses: OrderStatus[];
}

export const columns: ColumnDef<Order>[] = [
    {
        accessorKey: 'customer.name',
        header: 'Customer',
        cell: ({ row }) => row.original.customer?.name ?? 'N/A',
    },
    {
        accessorKey: 'item_type',
        header: 'Item Type',
    },
    {
        accessorKey: 'order_date',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Order Date', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => new Date(row.original.order_date).toLocaleDateString(),
    },
    {
        accessorKey: 'deadline',
        header: ({ column }) => {
            return h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => ['Deadline', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        },
        cell: ({ row }) => new Date(row.original.deadline).toLocaleDateString(),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row, table }) => {
            const order = row.original
            const meta = table.options.meta as TableMeta
            const statusInfo = meta.orderStatuses.find(s => s.value === order.status)

            if (!statusInfo)
                return h('span', order.status)


            return h(BaseBadge, { color: statusInfo.color as any }, () => statusInfo.name)
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false, // Added for consistency with customerColumns
        cell: ({ row, table }) => {
            const order = row.original;
            const meta = table.options.meta as TableMeta;

            return h('div', { class: 'relative' }, h(DropdownAction, {
                item: order, // Pass as generic item
                routePrefix: 'orders', // Specify route prefix
                onEdit: (item) => meta.onEdit(item as Order), // Cast item to Order
                onDelete: meta.onDelete,
                showManageMeasurements: false, // Don't show for orders
                showViewDetails: false,
            }))
        },
    },
]
