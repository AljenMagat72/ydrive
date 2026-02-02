// ===== Driver API =====
import { getMondayOfCurrentWeek } from "~/utils/date";

// 1️⃣ Fetch all drivers (admin route: GET /v1/driver/all)
export const fetchAllDrivers = async () => {
  try {
    const response = await $fetch(`/api/v1/driver/all`);
    if (response.success) {
      return response;
    }
  } catch (error: any) {
    throw new Error(error.response?.data?.message || "Failed to fetch drivers");
  }
};

// 2️⃣ Fetch a driver’s schedule (admin route: GET /v1/driver/schedule/{id})
// NOTE: Your API does not have /v1/driver/schedule/{id} explicitly.
// Assuming you want weekly schedule: GET /v1/driver/schedule/weekly
export const fetchDriverSchedule = async (
  driverId: number,
  startDate?: string
) => {
  try {
    // Use Monday of the current week if startDate is not provided
    const monday = startDate || getMondayOfCurrentWeek();

    const response = await $fetch("/api/v1/driver/schedule/weekly", {
      params: {
        driver_id: driverId,
        start_date: monday,
      },
    });

    return response.data;
  } catch (error: any) {
    throw new Error(
      error.response?.data?.message || "Failed to fetch driver schedule"
    );
  }
};

// 3️⃣ Delete a specific driver schedule slot (admin route: DELETE /v1/driver-schedule/{id})
export const fetchDeleteSlot = async (id: number) => {
  try {
    const response = await $fetch(`/api/v1/driver-schedule/${id}`, {
      method: "DELETE",
    });

    if (response.success) {
      return response;
    }
  } catch (error: any) {
    throw new Error(error.response?.data?.message || "Failed to delete slot");
  }
};
