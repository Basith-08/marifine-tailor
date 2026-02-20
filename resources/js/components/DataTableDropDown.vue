<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { MoreHorizontal } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'

// Generic type for the item
interface GenericItem {
    id: number;
    // Add other common properties if necessary, or keep it minimal
    [key: string]: any; // Allows any other property
}

const props = defineProps<{
    item: GenericItem; // Use generic item type
    routePrefix: string; // e.g., 'customers' or 'orders'
    onEdit: (item: GenericItem) => void; // Function to call for edit
    onDelete: (itemId: number) => void; // Function to call for delete
    showManageMeasurements?: boolean; // Optional prop to show/hide manage measurements
    showViewDetails?: boolean;
}>()

function viewDetails() {
    router.get(route(`${props.routePrefix}.show`, props.item.id));
}

function manageMeasurements() {
    router.get(route(`${props.routePrefix}.measurements.index`, props.item.id));
}

</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem v-if="props.showViewDetails" @click="viewDetails">View Details</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem v-if="props.showManageMeasurements" @click="manageMeasurements">Manage Measurements</DropdownMenuItem>
            <DropdownMenuSeparator v-if="props.showManageMeasurements" />
            <DropdownMenuItem @click="props.onEdit(props.item)">Edit</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="props.onDelete(props.item.id)">Delete</DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
