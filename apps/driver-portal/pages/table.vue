<script setup lang="ts">
import type { ColumnDef, Row, SortingState } from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ArrowUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next'
import { definePageMeta, h, ref, useFetch, useRoute } from '#imports';
import { valueUpdater } from '~/lib/utils'
import { Card } from '~/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '~/components/ui/table';
import { Button } from '~/components/ui/button';
import CitySelect from '~/components/input/CitySelect.vue';

definePageMeta({
  layout: 'empty',
  middleware: ['chart-key']
});

type Driver = {
  firstName: string,
  lastName: string,
  phoneNumber: string,
  city: string,
  hasCurrentSchedule: boolean,
  hasNextSchedule: boolean,
}

const route = useRoute();
const key = route.query.key || '';

const { data } = useFetch('/api/v1/admin/driver/all', {
  key: 'drivers',
  server: false,
  retry: false,
  headers: {
    'X-Admin-Key': key as string,
  },
  default: () => ({
    drivers: []
  }),
});

const getSortIcon = (sortState: string) => {
  switch (sortState) {
    case 'asc':
      return ArrowUp;
    case 'desc':
      return ArrowDown;
    default:
      return ArrowUpDown;
  }
};

const selectedCity = ref<string>('All');

const cityFilter = (row: Row<Driver>, columnId: string, filterValue: string) => {
  if (!filterValue || filterValue === 'All') return true;
  return row.getValue(columnId) === filterValue;
};

const columns: ColumnDef<Driver>[] = [
  {
    accessorKey: 'firstName',
    header: () => h('div', { class: 'text-center' }, 'First Name'),
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('firstName')),
  },
  {
    accessorKey: 'lastName',
    header: () => h('div', { class: 'text-center' }, 'Last Name'),
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('lastName')),
  },
  {
    accessorKey: 'phoneNumber',
    header: () => h('div', { class: 'text-center' }, 'Phone Number'),
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('phoneNumber')),
  },
  {
    accessorKey: 'city',
    header: () => h('div', { class: 'flex items-center justify-center gap-2' }, [
      h('span', { class: 'text-sm font-medium' }, 'City:'),
      h(CitySelect, {
        class: 'flex-1',
        modelValue: selectedCity.value,
        'onUpdate:modelValue': (value: string) => {
          selectedCity.value = value;
          table.getColumn('city')?.setFilterValue(value);
        },
      })
    ]),
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('city')),
    filterFn: cityFilter,
  },
  {
    accessorKey: 'hasCurrentSchedule',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        class: 'block m-auto',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Has Current Schedule', h(getSortIcon(column.getIsSorted() as string), { class: 'ml-2 h-4 w-4 inline' })])
    },
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('hasCurrentSchedule')),
  },
  {
    accessorKey: 'hasNextSchedule',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        class: 'block m-auto',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Has Next Schedule', h(getSortIcon(column.getIsSorted() as string), { class: 'ml-2 h-4 w-4 inline' })])
    },
    cell: ({ row }) => h('div', { class: 'text-center font-medium' }, row.getValue('hasNextSchedule')),
  },
];

const sorting = ref<SortingState>([]);

const table = useVueTable({
  get data() { return data.value.drivers },
  get columns() { return columns },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  state: {
    get sorting() { return sorting.value },
  },
  filterFns: {
    cityFilter,
  },
});

</script>

<template>
  <div class="px-8 py-4 h-dvh w-dvw">
    <div class="overflow-scroll h-full w-full">
      <Card class="min-h-full min-w-full h-fit w-fit p-0">
        <Table>
          <TableHeader>
            <TableRow
              v-for="headerGroup in table.getHeaderGroups()"
              :key="headerGroup.id"
            >
              <TableHead
                v-for="header in headerGroup.headers"
                :key="header.id"
              >
                <FlexRender
                  v-if="!header.isPlaceholder"
                  :render="header.column.columnDef.header"
                  :props="header.getContext()"
                />
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="table.getRowModel().rows?.length">
              <TableRow
                v-for="row in table.getRowModel().rows"
                :key="row.id"
                :data-state="row.getIsSelected() ? 'selected' : undefined"
              >
                <TableCell
                  v-for="cell in row.getVisibleCells()"
                  :key="cell.id"
                >
                  <FlexRender
                    :render="cell.column.columnDef.cell"
                    :props="cell.getContext()"
                  />
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow>
                <TableCell
                  :colspan="columns.length"
                  class="h-24 text-center"
                >
                  No drivers found{{ selectedCity && selectedCity !== 'all' ? ` in ${selectedCity}` : '' }}.
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </Card>
    </div>
  </div>
</template>