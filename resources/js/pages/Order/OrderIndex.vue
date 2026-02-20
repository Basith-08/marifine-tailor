<script setup lang="ts">
import { Plus, X } from 'lucide-vue-next';

import BaseFormDialog from '@/components/BaseFormDialog.vue';
import DataTable from '@/components/DataTable.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

import type { Customer } from '@/types/customer';
import type { Order } from '@/types/order';
import type { OrderStatus } from '@/types/order_status';
import type { PaginationProps } from '@/types/pagination';

import { getOrderFormFields } from './features/orderFormFields';
import { getOrderFormSchema } from './features/orderFormSchema';
import { useOrderState } from './features/orderState';
import { useOrderViewModel } from './features/orderViewModel';
import { columns } from './orderColumns';


const props = defineProps<{
    orders: PaginationProps<Order>;
    filters: {
        search?: string,
        status?: string,
        sort?: string,
        direction?: string
    };
    customers: Customer[];
    orderStatuses: OrderStatus[];
    orderSummary: Record<string, { count: number; label: string; color: string; }>;
}>();

const state = useOrderState(props);
const viewModel = useOrderViewModel(props, state);

const statusValues = props.orderStatuses.map(s => s.value) as [string, ...string[]];
const formSchema = getOrderFormSchema(statusValues);
const formFields = getOrderFormFields(props.customers, props.orderStatuses);

</script>

<template>
    <AppLayout>
        <div class="flex flex-col gap-4 p-4 md:p-8">
            <Heading title="Order Dashboard" />

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="summary in props.orderSummary" :key="summary.label" class="p-4 bg-white rounded-lg shadow">
                    <h3 class="font-semibold text-lg">{{ summary.label }}</h3>
                    <p class="text-2xl font-bold">{{ summary.count }}</p>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Input class="max-w-sm" placeholder="Search by customer name..." v-model="viewModel.search.value" />
                    <Select v-model="viewModel.statusFilter.value">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Filter by status" />
                            <Button v-if="viewModel.statusFilter.value" variant="ghost" size="sm"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-1 h-auto"
                                @click.stop="viewModel.statusFilter.value = ''">
                                <X class="h-4 w-4" />
                            </Button>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="status in props.orderStatuses" :key="status.value" :value="status.value">
                                {{ status.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <Button @click="viewModel.openCreateDialog" variant="default" size="sm" class="font-semibold">
                    <Plus class="size-4 mr-2" />
                    Create Order
                </Button>
            </div>

            <!-- Data Table -->
            <div class="p-6 bg-white rounded-lg shadow">
                <DataTable :columns="columns" :data="props.orders.data" :links="props.orders.links"
                    :sorting="viewModel.sorting.value" @update:sorting="viewModel.sorting.value = $event" :meta="{
                        onEdit: viewModel.onEdit,
                        onDelete: viewModel.handleDelete,
                        orderStatuses: props.orderStatuses,
                    }" />
            </div>
        </div>

        <BaseFormDialog :open="viewModel.isDialogOpen.value" @update:open="viewModel.isDialogOpen.value = $event"
            :title="viewModel.dialogTitle.value" :description="viewModel.dialogDescription.value" :schema="formSchema"
            :fields="formFields" :initial-values="viewModel.initialValues.value" @submit="viewModel.handleSubmit" />
    </AppLayout>
</template>
