<script setup lang="ts">
import { Plus } from 'lucide-vue-next';
import BaseFormDialog from '@/components/BaseFormDialog.vue';
import DataTable from '@/components/DataTable.vue';
import Heading from '@/components/Heading.vue';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Customer } from '@/types/customer';
import type { PaginationProps } from '@/types/pagination';
import { columns } from './features/customerColumns'
import { formFields } from './features/customerFormFields';
import { formSchema } from './features/customerFormSchema';
import { useCustomerState } from './features/customerState';
import { useCustomerViewModel } from './features/customerViewModel';

const props = defineProps<{
	customers: PaginationProps<Customer>;
	filters: {
		search?: string,
		sort?: string,
		direction?: string
	}
}>();

const state = useCustomerState(props);
const viewModel = useCustomerViewModel(props, state);

</script>

<template>
	<AppLayout>
		<div class="flex flex-col gap-4 p-4">
			<div class="flex items-center justify-between">
				<Heading title="Customers" />

                <div class="flex items-center gap-2">
                    <Input class="max-w-sm" placeholder="Search by name or phone..." v-model="viewModel.search.value" />

				    <!-- Add Customer Button -->
				    <Button @click="viewModel.openCreateDialog" variant="default" size="sm" class="font-semibold w-fit">
                        <Plus class="size-4 mr-2" />
                        Add Customer
                    </Button>
                </div>
			</div>

			<!-- Unified Customer Dialog -->
			<BaseFormDialog
				:open="viewModel.isDialogOpen.value"
                @update:open="viewModel.isDialogOpen.value = $event"
				:title="viewModel.dialogTitle.value"
				:description="viewModel.dialogDescription.value"
				:schema="formSchema"
				:fields="formFields"
				:initial-values="viewModel.initialValues.value"
				@submit="viewModel.handleSubmit"
			/>

			<DataTable
					:columns="columns"
					:data="props.customers.data"
					:links="props.customers.links"
					:from="props.customers.from"
					:total="props.customers.total"
					:sorting="viewModel.sorting.value"
                    @update:sorting="viewModel.sorting.value = $event"
					:meta="{
						onEdit: viewModel.onEdit,
						onDelete: viewModel.confirmDeleteCustomer,
					}"
				/>
		</div>
	</AppLayout>
	
</template>