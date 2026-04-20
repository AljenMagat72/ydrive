import type { Ride } from "@/lib/types";

export function normalizePhoneForLookup(input: string): string {
  const s = input.trim();
  if (!s) return "";
  const digits = s.replace(/\D+/g, "");
  if (!digits) return "";
  return digits;
}

export function formatMoney(amount: number, currency: string) {
  try {
    return new Intl.NumberFormat("en-CA", { style: "currency", currency }).format(amount);
  } catch {
    return `${currency} ${amount.toFixed(2)}`;
  }
}

const rideDateTimeFormatOptions = {
  month: "short" as const,
  day: "2-digit" as const,
  year: "numeric" as const,
  hour: "numeric" as const,
  minute: "2-digit" as const,
  timeZone: "UTC",
};

export function formatDateTime(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", rideDateTimeFormatOptions).format(d);
}

export function formatTimeOnly(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", {
    hour: rideDateTimeFormatOptions.hour,
    minute: rideDateTimeFormatOptions.minute,
    timeZone: rideDateTimeFormatOptions.timeZone,
  }).format(d);
}

export function formatDateOnly(iso: string) {
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", {
    month: "short",
    day: "2-digit",
    year: "numeric",
    timeZone: rideDateTimeFormatOptions.timeZone,
  }).format(d);
}

export function formatRideCardDateParts(iso?: string | null): { monthDay: string; year: string } | null {
  if (!iso) return null;
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return null;
  const tz = rideDateTimeFormatOptions.timeZone;
  const monthDay = new Intl.DateTimeFormat("en-CA", {
    month: "short",
    day: "numeric",
    timeZone: tz,
  }).format(d);
  const year = new Intl.DateTimeFormat("en-CA", {
    year: "numeric",
    timeZone: tz,
  }).format(d);
  return { monthDay, year };
}

export function formatPickupPrebookWindow(afterTime?: string | null, beforeTime?: string | null): string | null {
  const a = typeof afterTime === "string" ? afterTime.trim() : "";
  const b = typeof beforeTime === "string" ? beforeTime.trim() : "";
  if (!a || !b) return null;
  const start = new Date(a);
  const end = new Date(b);
  if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) return null;
  if (end.getTime() <= start.getTime()) return null;

  const sameUtcDay =
    start.getUTCFullYear() === end.getUTCFullYear() &&
    start.getUTCMonth() === end.getUTCMonth() &&
    start.getUTCDate() === end.getUTCDate();

  if (sameUtcDay) {
    return `${formatDateOnly(a)}, ${formatTimeOnly(a)} – ${formatTimeOnly(b)}`;
  }
  return `${formatDateTime(a)} – ${formatDateTime(b)}`;
}

export function rideCompletedAtIso(ride: Ride): string | null {
  const a = ride.finalizedAt;
  if (typeof a === "string" && a.trim() !== "") return a;
  return null;
}

