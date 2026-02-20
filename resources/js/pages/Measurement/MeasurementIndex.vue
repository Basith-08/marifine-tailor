<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Plus, ChevronLeft } from 'lucide-vue-next';
import BaseFormDialog from '@/components/BaseFormDialog.vue';
import DataTable from '@/components/DataTable.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

import type { Customer } from '@/types/customer';
import type { Measurement } from '@/types/measurement';
import type { PaginationProps } from '@/types/pagination';

import { columns } from './features/measurementColumns';
import { formFields } from './features/measurementFormFields';
import { formSchema } from './features/measurementFormSchema';
import { useMeasurementViewModel } from './features/useMeasurementViewModel';

const props = defineProps<{
    customer: Customer;
    measurements: PaginationProps<Measurement>;
}>();

const {
    isDialogOpen,
    dialogTitle,
    initialValues,
    openDialog,
    handleSubmit,
    handleDelete,
} = useMeasurementViewModel(props);

</script>

<template>
    <AppLayout>
        <div class="p-4 md:p-8 flex flex-col gap-8">
            <!-- Customer Details Card -->
            <div class="p-6 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <Heading :title="`Customer: ${props.customer.name}`" />
                    <Link :href="route('customers.index')">
                        <Button variant="outline" size="sm" class="flex items-center space-x-1">
                            <ChevronLeft class="w-4 h-4" />
                            <span>Back</span>
                        </Button>
                    </Link>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <p><strong>Phone:</strong> {{ props.customer.phone || '-' }}</p>
                    <p><strong>Address:</strong> {{ props.customer.address || '-' }}</p>
                </div>
            </div>

            <!-- Measurements Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <Heading title="Measurements" />
                    <Button @click="openDialog('create')" variant="default" size="sm">
                        <Plus class="w-4 h-4 mr-2" />
                        Add Measurement
                    </Button>
                </div>
                <div class="p-6 bg-white rounded-lg shadow">
                    <DataTable
                        :columns="columns"
                        :data="props.measurements.data"
                        :links="props.measurements.links"
                        :sorting="[]"
                        :meta="{
                            openDialog,
                            handleDelete,
                        }"
                    />
                </div>
            </div>
        </div>

        <BaseFormDialog
            :open="isDialogOpen"
            @update:open="isDialogOpen = $event"
            :title="dialogTitle"
            :schema="formSchema"
            :fields="formFields"
            :initial-values="initialValues"
            @submit="handleSubmit"
        />
    </AppLayout>
</template>
