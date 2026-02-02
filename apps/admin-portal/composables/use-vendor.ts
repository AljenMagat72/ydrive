export const useVendor = () => {
  async function getVendors() {
    try {
      const res = await $fetch("/api/v1/driver/vendor/all");

      if (res.vendors.length > 0) {
        return res.vendors;
      }
    } catch (error: any) {
      return error;
    }
  }

  async function update(id: number, city: string) {
    try {
      const res = await $fetch("/api/v1/driver/vendor/update", {
        method: "POST",
        body: {
          driver_id: id,
          vendor_id: city,
        },
      });

      if (res.success) {
        return res;
      }
    } catch (error: any) {
      return error;
    }
  }

  async function revert(id: number, city: string) {
    try {
      const res = await $fetch("/api/v1/driver/vendor/revert", {
        method: "POST",
        body: {
          driver_id: id,
          no_opps_id: city,
        },
      });

      if (res.success) {
        return res;
      }
    } catch (error: any) {
      return error;
    }
  }

  async function get() {
    try {
      const res = await $fetch("/api/v1/driver/vendor/get");

      if (res.success) {
        return res.drivers;
      }
    } catch (error: any) {
      return error;
    }
  }

  return { getVendors, update, get, revert };
};
