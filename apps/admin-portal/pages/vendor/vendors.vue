<script setup lang="ts">
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { MoreHorizontalIcon } from "lucide-vue-next";
const vendor = useVendor();

// Local states
const vendors = ref(null);
const loading = ref(false);

definePageMeta({
  layout: "auth",
  layoutTransition: {
    name: "slide-up",
    mode: "out-in",
  },
  middleware: ["auth"],
});

onMounted(async () => {
  loading.value = true;
  const vendorLists = await vendor.getVendors();

  if (vendorLists.length > 0) {
    vendors.value = vendorLists;
  } else {
    vendors.value = null;
  }

  loading.value = false;
});
</script>

<template>
  <h1 class="text-2xl font-semibold">Vendors Lists</h1>
  <div
    class="border shadow-sm p-6 rounded mt-4 overflow-auto lg:h-[70vh] h-[80vh]"
  >
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Vendor ID</TableHead>
          <TableHead>No Opps ID</TableHead>
          <TableHead>Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="vendor in vendors" :key="vendor.id">
          <TableCell className="font-medium">{{ vendor.vendor_id }}</TableCell>
          <TableCell>{{ vendor.no_opps_id }}</TableCell>
          <TableCell>
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="size-8">
                  <MoreHorizontalIcon />
                  <span className="sr-only">Open menu</span>
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="start">
                <DropdownMenuItem>Edit</DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem variant="destructive">
                  Delete
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<style></style>
