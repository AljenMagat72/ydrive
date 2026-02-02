<script setup lang="ts">
import { EllipsisVertical } from 'lucide-vue-next';

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "~/components/ui/table";

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "~/components/ui/dropdown-menu";
import { useFetch, useRoute } from '#app';
import { useAPI } from '#imports';
import Card from '../ui/card/Card.vue';
import Button from '../ui/button/Button.vue';

const { post } = useAPI();

const route = useRoute();
const key = route.query.key || '';

const { data, refresh } = useFetch('/api/v1/admin/driver/schedule/delinquents', {
  key: 'delinquents',
  server: false,
  retry: false,
  headers: {
    'X-Admin-Key': key as string,
  },
  default: () => {
    return {
      success: true,
      delinquents: [],
    }
  }
});

async function revert(id: number) {
  await post(`/api/v1/driver/${id}/delinquent/revert`, undefined, {
    'X-Admin-Key': key as string
  });
  refresh();
}

async function prevent(id: number) {
  await post(`/api/v1/driver/${id}/delinquent/prevent`, undefined, {
    'X-Admin-Key': key as string
  });
  refresh();
}
</script>

<template>
  <Card class="w-full">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead class="text-center">
            Name
          </TableHead>
          <TableHead class="text-center">
            City
          </TableHead>
          <TableHead class="text-center">
            Consecutive Weeks
          </TableHead>
          <TableHead class="text-center">
            Actions
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
          v-for="delinquent in data.delinquents"
          :key="delinquent.id"
        >
          <TableCell class="text-center">
            {{ `${delinquent.firstName} ${delinquent.lastName}` }}
          </TableCell>
          <TableCell class="text-center">
            {{ delinquent.city }}
          </TableCell>
          <TableCell class="text-center">
            {{ delinquent.consecutiveWeeks }}
          </TableCell>
          <TableCell class="text-center">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <button>
                  <EllipsisVertical />
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent>
                <DropdownMenuItem>
                  <Button
                    class="w-full"
                    variant="ghost"
                    @click="() => revert(delinquent.id)"
                  >
                    Revert
                  </Button>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Button
                    class="w-full"
                    variant="ghost"
                    @click="() => prevent(delinquent.id)"
                  >
                    Protect
                  </Button>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>