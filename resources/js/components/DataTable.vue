<script setup lang="ts" generic="TData, TValue">
import { Link } from '@inertiajs/vue3'
import type {
  ColumnDef,
  SortingState,
} from '@tanstack/vue-table'

import {
  FlexRender,
  getCoreRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'


const props = defineProps<{
  columns: ColumnDef<TData, TValue>[]
  data?: TData[] // Make data prop optional
  links?: {
    url: string | null;
    label: string;
    active: boolean;
  }[]
  from?: number
  total?: number
  sorting: SortingState
  meta?: Record<string, any>; // Add meta prop
}>()

const emit = defineEmits(['update:sorting'])

const table = useVueTable({
  get data() { return props.data ?? [] },
  get columns() { return props.columns },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onSortingChange: (updaterOrValue) => {
    const newValue = typeof updaterOrValue === 'function' ? updaterOrValue(props.sorting) : updaterOrValue;
    emit('update:sorting', newValue);
  },
  state: {
    get sorting() { return props.sorting },
  },
  meta: {
    from: props.from ?? 1,
    ...props.meta,
  }
})
</script>

<template>
  <div>
    <div class="border rounded-md">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
              :data-state="row.getIsSelected() ? 'selected' : undefined">
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colspan="columns.length" class="h-24 text-center">
                No results.
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div class="flex items-center justify-between px-2 py-4 border-t mt-2">
      <div class="text-sm text-muted-foreground italic">
        Showing data from a total of {{ props.total }} customers.
      </div>

      <div class="flex items-center space-x-2">
        <template v-for="(link, index) in props.links" :key="index">
          <Link v-if="link.url" :href="link.url"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-auto min-w-[36px] px-3 shadow-sm"
            :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90': link.active }">
            <span v-html="link.label"></span>
            </Link>

          <span v-else
            class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background h-9 w-auto min-w-[36px] px-3 opacity-40 text-muted-foreground cursor-not-allowed shadow-sm bg-gray-50"
            v-html="link.label" />
        </template>
      </div>
    </div>
  </div>
</template>
