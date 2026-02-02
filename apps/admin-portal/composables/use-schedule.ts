export const useSchedule = () => {
  // Get driver's weekly schedule
  async function getWeeklySchedule(driverId: number, startDate?: string) {
    try {
      const res = await $fetch("/api/v1/driver/schedule/weekly", {
        params: {
          driver_id: driverId,
          start_date: startDate,
        },
      });

      if (res.success) {
        return res?.schedule;
      }
    } catch (error: any) {
      throw new Error(
        error.response?.data?.message || "Failed to fetch drivers",
      );
    }
  }

  // Add new driver's schedule
  const add = async (startShift: string, endShift: string) => {
    const res = await $fetch("/api/v1/driver/schedule/add", {
      method: "POST",
      body: {
        starts_at: startShift,
        ends_at: endShift,
      },
    });

    if (res.success) {
      return { success: true, message: "New shift added." };
    } else {
      return { success: false, message: "Failed to add new shift." };
    }
  };

  // Add new driver's schedule
  const addByAdmin = async (
    id: number,
    startShift: string,
    endShift: string,
  ) => {
    const res = await $fetch("/api/v1/driver/schedule/store", {
      method: "POST",
      body: {
        driver_id: id,
        starts_at: startShift,
        ends_at: endShift,
      },
    });

    if (res.success) {
      return {
        success: true,
        message: "New shift added.",
        slot_id: res.slot_id,
      };
    } else {
      return { success: false, message: "Failed to add new shift." };
    }
  };

  return { getWeeklySchedule, add, addByAdmin };
};
