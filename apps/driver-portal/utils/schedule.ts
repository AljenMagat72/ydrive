/* eslint-disable @typescript-eslint/no-explicit-any */
export const formatDayDate = (dated: Date) => {
  const yyyy = dated.getFullYear();
  const mm = String(dated.getMonth() + 1).padStart(2, "0");
  const dd = String(dated.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
};

export const normalizeWeeklySchedule = (
  schedule: Array<Record<string, any[]>>
) => {
  return schedule?.reduce(
    (acc, dayObj) => {
      const [day, value] = Object.entries(dayObj)[0];
      acc[day] = value;
      return acc;
    },
    {} as Record<string, any[]>
  );
};

export function getDailyAndWeeklyTotals(schedule: any) {
  const result: any = {};
  let weeklyMinutes = 0;

  for (const day in schedule) {
    let dailyMinutes = 0;

    schedule[day].forEach((slot: any) => {
      const [sh, sm] = slot.starts_at.split(":").map(Number);
      const [eh, em] = slot.ends_at.split(":").map(Number);

      const start = sh * 60 + sm;
      const end = eh * 60 + em;

      dailyMinutes += end - start;
    });

    weeklyMinutes += dailyMinutes;
    result[day] = formatMinutes(dailyMinutes);
  }

  result.weeklyTotal = formatMinutes(weeklyMinutes);
  return result;
}

export function formatMinutes(minutes: any) {
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return `${h}h ${m}m`;
}

export function getWeeklyTotalFromDaily(totals: any) {
  let totalMinutes = 0;

  for (const key in totals) {
    if (key === "weeklyTotal") continue; // skip if exists

    const [hours, minutes] = totals[key]
      .replace("h", "")
      .replace("m", "")
      .split(" ")
      .map(Number);

    totalMinutes += hours * 60 + minutes;
  }

  return formatMinutes(totalMinutes);
}

export const getTheLastTimePreviousShiftEnded = (schedule: any) => {
  if (schedule == null || schedule.length === 0) return "00:00";

  let lastTimeOfShift = schedule[schedule.length - 1].time;
  lastTimeOfShift = lastTimeOfShift.split("-")[1].trim();
  return (lastTimeOfShift = to24Hour(lastTimeOfShift));
};

// Conver 12H to 24H format
export const to24Hour = (time: any) => {
  const [_, h, m, period] = time.match(/(\d+):(\d+)(am|pm)/i);

  let hour = parseInt(h, 10);
  const minute = m;

  if (period.toLowerCase() === "pm" && hour !== 12) hour += 12;
  if (period.toLowerCase() === "am" && hour === 12) hour = 0;

  return `${hour.toString().padStart(2, "0")}:${minute}`;
};

export const differenceInEndAndStartOfNewlyAddedShift = (
  starts: string,
  ends: string
) => {
  const [sh, sm] = starts.split(":").map(Number);
  const [eh, em] = ends.split(":").map(Number);

  if ([sh, sm, eh, em].some(Number.isNaN)) return "00:00";

  let d = eh * 60 + em - (sh * 60 + sm);

  if (d <= 0) d += 24 * 60;

  const h = Math.floor(d / 60)
    .toString()
    .padStart(2, "0");
  const m = (d % 60).toString().padStart(2, "0");

  return `${h}:${m}`;
};

export const addDurations = (duration1: string, duration2: string) => {
  const [h1, m1] = duration1.split(":").map(Number);
  const [h2, m2] = duration2.split(":").map(Number);

  if ([h1, m1, h2, m2].some(Number.isNaN)) return "00:00";

  const totalMinutes = h1 * 60 + m1 + h2 * 60 + m2;

  const h = Math.floor(totalMinutes / 60);
  const m = totalMinutes % 60;

  return `${h.toString().padStart(2, "0")}:${m.toString().padStart(2, "0")}`;
};
