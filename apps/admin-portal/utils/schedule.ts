export const formatDayDate = (dated: Date) => {
  const yyyy = dated.getFullYear();
  const mm = String(dated.getMonth() + 1).padStart(2, "0");
  const dd = String(dated.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
};

export const getTheLastTimePreviousShiftEnded = (schedule: any) => {
  if (schedule == null || schedule.length === 0) return "00:00";

  let lastTimeOfShift = schedule[schedule.length - 1].time;
  lastTimeOfShift = lastTimeOfShift.split("-")[1].trim();
  return (lastTimeOfShift = to24Hour(lastTimeOfShift));
};

// Check split break interval
export const toMinutes = (time: any) => {
  const [h, m] = time.split(":").map(Number);
  return h * 60 + m;
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

// Change "13:00" to "01:00pm"
export const formatTime = (start: string, end: string) => {
  function to12Hour(time: any) {
    const [hourStr, minStr] = time?.split(":");
    let hour = parseInt(hourStr, 10);
    const minute = minStr;
    const period = hour >= 12 ? "pm" : "am";

    if (hour === 0)
      hour = 12; // 0 => 12am
    else if (hour > 12) hour -= 12; // 13 => 1pm, etc.

    return `${hour.toString().padStart(2, "0")}:${minute}${period}`;
  }

  return `${to12Hour(start)} - ${to12Hour(end)}`;
};

export const formatTimeamToAmAndpmToPm = (time: string) => {
  let newFormattedTime = time?.replaceAll("am", "AM");
  return newFormattedTime?.replaceAll("pm", "PM");
};

type ScheduleSlot = {
  time: string; // e.g. "7:00 PM - 12:00 AM"
};

type Schedule = Record<string, ScheduleSlot[]>;

export function calculateWeeklyAndDailyHours(schedule: any) {
  const result = {
    dailyTotals: {} as Record<string, string>,
    weeklyTotal: "00H 00M",
  };

  let totalMinutesWeek = 0;

  // Convert "7:30 PM" → minutes since start of day
  function timeToMinutes(time: string): number {
    const match = time.trim().match(/^(\d{1,2}):(\d{2})\s?(AM|PM)$/i);
    if (!match) return 0;

    let [, h, m, period] = match;

    if (!period) return 0;

    let hour = Number(h);
    const minute = Number(m);

    if (period.toUpperCase() === "PM" && hour !== 12) hour += 12;
    if (period.toUpperCase() === "AM" && hour === 12) hour = 0;

    return hour * 60 + minute;
  }

  // Format minutes → "00H 00M"
  function formatMinutes(minutes: number): string {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return `${String(h).padStart(2, "0")}H ${String(m).padStart(2, "0")}M`;
  }

  for (const day in schedule) {
    let totalMinutesDay = 0;

    schedule[day].forEach((slot: any) => {
      const [start, end] = slot.time.split(" - ");

      let startMin = timeToMinutes(start);
      let endMin = timeToMinutes(end);

      // ✅ Handle crossing midnight
      if (endMin <= startMin) {
        endMin += 24 * 60;
      }

      totalMinutesDay += endMin - startMin;
    });

    result.dailyTotals[day] = formatMinutes(totalMinutesDay);
    totalMinutesWeek += totalMinutesDay;
  }

  result.weeklyTotal = formatMinutes(totalMinutesWeek);

  return result;
}

export const differenceInEndAndStartOfNewlyAddedShift = (
  starts: string,
  ends: string
) => {
  const [sh, sm] = starts.split(":").map(Number);
  const [eh, em] = ends.split(":").map(Number);

  if (!eh || !em || !sh || !sm) return "00:00";

  let d = eh * 60 + em - (sh * 60 + sm);

  if (d <= 0) d += 24 * 60;

  const h = Math.floor(d / 60)
    .toString()
    .padStart(2, "0");
  const m = (d % 60).toString().padStart(2, "0");

  return `${h}:${m}`;
};

export const addDurations = (duration1: string, duration2: string) => {
  // Extract hours and minutes
  const [h1, m1] = duration1.split(":").map(Number);
  const [h2, m2] = duration2.split(":").map(Number);

  if (!h1 || !m1 || !h2 || !m2) return "00:00";

  let totalMinutes = h1 * 60 + m1 + h2 * 60 + m2;

  const h = Math.floor(totalMinutes / 60);
  const m = totalMinutes % 60;

  return `${h.toString().padStart(2, "0")}:${m.toString().padStart(2, "0")}`;
};
