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
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
const vendor = useVendor();

// Local states
const drivers = ref(null);
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
  const driversLists = await vendor.get();

  if (driversLists.length > 0) {
    drivers.value = driversLists;
  } else {
    drivers.value = null;
  }

  loading.value = false;
});

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr);

  return date
    .toLocaleString("en-US", {
      month: "short",
      day: "2-digit",
      year: "numeric",
      hour: "numeric",
      minute: "2-digit",
      hour12: true,
    })
    .replace(",", "");
};

const revertVendor = async (id: number, city: string) => {
  try {
    await vendor.revert(id, city);
    if (drivers.value != null) {
      drivers.value = drivers.value.filter((driver: any) => driver.id != id);
    }
  } catch (error) {
    console.error(error);
  }
};
</script>

<template>
  <h1 class="text-2xl font-semibold">
    {{ drivers?.length }} Drivers with NO OPPS Vendor
  </h1>
  <div
    class="border shadow-sm p-6 rounded mt-4 overflow-auto lg:h-[70vh] h-[80vh]"
  >
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Driver</TableHead>
          <TableHead>NO OPPS Vendor ID</TableHead>
          <TableHead>Date Added</TableHead>
          <TableHead>Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="driver in drivers" :key="driver.id">
          <TableCell className="font-medium"
            >{{ driver.first_name }} {{ driver.last_name }}</TableCell
          >
          <TableCell>{{ driver.city_id }}</TableCell>
          <TableCell>{{ formatDate(driver.created_at) }}</TableCell>
          <TableCell>
            <AlertDialog>
              <AlertDialogTrigger asChild>
                <Button>Remove</Button>
              </AlertDialogTrigger>
              <AlertDialogContent>
                <AlertDialogHeader>
                  <AlertDialogTitle>Are you sure ?</AlertDialogTitle>
                  <AlertDialogDescription>
                    This will remove driver from No Opps Vendor Lists and the
                    Opportunity board will now visible again on his end.
                  </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                  <AlertDialogCancel>Cancel</AlertDialogCancel>
                  <AlertDialogAction
                    @click="revertVendor(driver.id, driver.city_id)"
                    >Continue</AlertDialogAction
                  >
                </AlertDialogFooter>
              </AlertDialogContent>
            </AlertDialog>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>

<style></style>
