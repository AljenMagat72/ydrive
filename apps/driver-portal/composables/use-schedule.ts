/* eslint-disable @typescript-eslint/no-explicit-any */
import { getMondayOfCurrentWeek, useAPI } from "#imports";

type AddScheduleResponse = {
  success: boolean;
};

export type ScheduleEntry = {
  id: number;
  starts_at: string;
  ends_at: string;
};

export type DaySchedule = {
  monday?: ScheduleEntry[];
  tuesday?: ScheduleEntry[];
  wednesday?: ScheduleEntry[];
  thursday?: ScheduleEntry[];
  friday?: ScheduleEntry[];
  saturday?: ScheduleEntry[];
  sunday?: ScheduleEntry[];
};

export type WeeklyScheduleResponse = {
  user: {
    id: number;
    name: string;
    email: string;
  };
  driver_id: number;
  date: string;
  schedule: DaySchedule[];
};

type DailyScheduleReponse = {
  success: boolean;
};

export function useSchedule() {
  const { post, get, del } = useAPI();

  async function add(start: string, schedule: Array<unknown>) {
    return post<AddScheduleResponse>("/api/v1/driver/schedule", {
      startDate: start,
      schedule,
    });
  }

  async function addSchedule(startShift: string, endShift: string) {
    return post<any>("/api/v1/driver/schedule/add", {
      starts_at: startShift,
      ends_at: endShift,
    });
  }

  async function weekly(date: string) {
    return get<WeeklyScheduleResponse>("/api/v1/driver/schedule/weekly", {
      start_date: getMondayOfCurrentWeek(),
    });
  }

  async function nextWeekly() {
  const today = new Date();
  const dayOfWeek = today.getDay(); 
  const nextMonday = new Date(today);
  
  const daysToMonday = (dayOfWeek === 0 ? 1 : 8 - dayOfWeek);
  nextMonday.setDate(today.getDate() + daysToMonday);
  
  const nextMondayISO = nextMonday.toISOString().split('T')[0];

  return get<WeeklyScheduleResponse>("/api/v1/driver/schedule/weekly", {
    start_date: nextMondayISO,
  });
}

  async function daily(params: object = {}) {
    return get<DailyScheduleReponse>("/api/v1/driver/schedule/daily", params);
  }

  async function fetchDeleteSlot(id: number) {
    return del(`/api/v1/driver/schedule/driver-schedule/${id}`);
  }

  return {
    add,
    weekly,
    nextWeekly,
    daily,
    fetchDeleteSlot,
    addSchedule,
  };
}
