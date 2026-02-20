<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, User, ShoppingBag } from 'lucide-vue-next';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import customers from '@/routes/customers';
import dashboard from '@/routes/dashboard';
import orders from '@/routes/orders';
import type { NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard.index().url,
        icon: LayoutGrid,
    },
    {
        title: 'Customer',
        href: customers.index().url,
        icon: User
    },
    {
        title: 'Orders',
        href: orders.index().url,
        icon: ShoppingBag
    }
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard.index().url">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
