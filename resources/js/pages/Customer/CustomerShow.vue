<script setup lang="ts">
import { Link } from '@inertiajs/vue3' // Import Link for navigation
import { defineProps } from 'vue'
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import Heading from '@/components/Heading.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { Customer } from '@/types/Customer'

interface Props {
    customer: Customer;
}

const props = defineProps<Props>()

const breadcrumbs = [
    { label: 'Customers', url: route('customers.index') },
    { label: props.customer.name, url: route('customers.show', props.customer.id) },
]
</script>

<template>
    <AppLayout :title="`Customer: ${props.customer.name}`">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <Breadcrumbs :items="breadcrumbs" />
                    <Heading level="h2" class="mt-2">Customer Details: {{ props.customer.name }}</Heading>
                </div>
                <Link :href="route('customers.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Customers List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p><strong>Name:</strong> {{ props.customer.name }}</p>
                        <p><strong>Phone:</strong> {{ props.customer.phone }}</p>
                        <p><strong>Address:</strong> {{ props.customer.address }}</p>
                        <p><strong>Created At:</strong> {{ new Date(props.customer.created_at).toLocaleDateString() }}</p>
                        <p><strong>Last Updated:</strong> {{ new Date(props.customer.updated_at).toLocaleDateString() }}</p>
                        <!-- Add more customer details as needed -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
