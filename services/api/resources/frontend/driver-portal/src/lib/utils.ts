import { type InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { CalendarDateTime, type DateValue, ZonedDateTime } from '@internationalized/date';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
  return typeof href === 'string' ? href : href?.url;
}

export function parseDate(date?: DateValue): Date {
  if (!date) return new Date();

  if (date instanceof ZonedDateTime) {
    return date.toDate();
  }

  if (date instanceof CalendarDateTime) {
    return new Date(
      date.year,
      date.month - 1,
      date.day,
      date.hour,
      date.minute,
      date.second ?? 0,
      date.millisecond ?? 0,
    );
  }

  return new Date(
    date.year,
    date.month - 1,
    date.day,
  );
}

export function pad(n: number) {
  return n.toString().padStart(2, '0');
}

export function toDateTimeString(date: Date) {
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ` +
    `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
}

export function toDateString(date: Date) {
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}
