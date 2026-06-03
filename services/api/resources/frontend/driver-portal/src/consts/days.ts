import { getLocalTimeZone, today } from "@internationalized/date";

export const now = today(getLocalTimeZone());

const jsDate = now.toDate(getLocalTimeZone());

export const calendarDateFormatter = new Intl.DateTimeFormat("en-PH", {
  day: "2-digit",
  month: "long",
});

calendarDateFormatter.format(jsDate);
