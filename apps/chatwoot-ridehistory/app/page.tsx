"use client";
import * as React from "react";
import { Suspense } from "react";
import { useSearchParams } from "next/navigation";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Dialog, DialogContent, DialogTitle } from "@/components/ui/dialog";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Separator } from "@/components/ui/separator";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { cn } from "@/lib/utils";
import {
  ArrowRight,
  Building2,
  ExternalLink,
  FlagTriangleRight,
  Hash,
  Info,
  Loader2,
  Mail,
  MapPin,
  Phone,
  UserCircle,
} from "lucide-react";

type StopType = "pickup" | "dropoff" | string;
type StopPoint = {
  id: string;
  type: StopType;
  orderInParent: number;
  description: string;
  beforeTime?: string | null;
  plannedArrivalTime?: string | null;
  eta?: string | null;
  arrivedAt?: string | null;
  completedAt?: string | null;
};

type Ride = {
  id: string;
  state: string;
  priceCurrency: string;
  priceAmount: number;
  createdAt: string;
  /** AutoFleet: set when the ride is finalized (use for “ride completed” time). */
  finalizedAt?: string | null;
  stopPoints: StopPoint[];
  payment?: { id: string; state?: string; paymentMethod?: { name?: string } } | null;
  paymentBreakdown?: { preAuth?: number; captured?: number; refunded?: number } | null;
  vehicle?: { year?: number | null; make?: string | null; model?: { name?: string; class?: string } } | null;
  driver?: { firstName?: string; lastName?: string; phoneNumber?: string } | null;
};

/** AutoFleet ride completion time comes from `finalizedAt`. */
function rideCompletedAtIso(ride: Ride): string | null {
  const a = ride.finalizedAt;
  if (typeof a === "string" && a.trim() !== "") return a;
  return null;
}

/** Payload from Chatwoot Dashboard Apps (`window.postMessage`). */
type ChatwootSocialProfiles = {
  github?: string;
  twitter?: string;
  facebook?: string;
  linkedin?: string;
};

type ChatwootPerson = {
  id?: number;
  name?: string;
  email?: string;
  phone_number?: string;
  identifier?: string | null;
  thumbnail?: string;
  availability_status?: string;
  custom_attributes?: Record<string, unknown>;
  additional_attributes?: {
    description?: string;
    company_name?: string;
    social_profiles?: ChatwootSocialProfiles;
  };
};

type AppContext = {
  event?: string;
  data?: {
    conversation?: {
      id?: number;
      inbox_id?: number;
      status?: string;
      labels?: string[];
      custom_attributes?: Record<string, unknown>;
      meta?: {
        sender?: ChatwootPerson;
        channel?: string;
      };
    };
    contact?: ChatwootPerson & { id?: number };
    currentAgent?: {
      id?: number;
      name?: string;
      email?: string;
    };
  };
};

const MOCK_CLIENT = {
  name: "---",
  lifetimeSpendCad: 0.00,
};

function byOrder(a: StopPoint, b: StopPoint) {
  return (a.orderInParent ?? 0) - (b.orderInParent ?? 0);
}

function sortRidesByCreatedAtDesc(rides: Ride[]): Ride[] {
  return [...rides].sort(
    (a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime(),
  );
}

function mergeRidesPage(prev: Ride[], list: Ride[], pageNumber: number): Ride[] {
  const merged = pageNumber === 0 ? list : [...prev, ...list];
  return sortRidesByCreatedAtDesc(merged);
}

function normalizePhoneForLookup(input: string): string {
  const s = input.trim();
  if (!s) return "";
  const digits = s.replace(/\D+/g, "");
  if (!digits) return "";
  return digits;
}

function formatMoney(amount: number, currency: string) {
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
  // Prevent hydration mismatches between server (often UTC) and client (user locale).
  timeZone: "UTC",
};

function formatDateTime(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", rideDateTimeFormatOptions).format(d);
}

function formatDateOnly(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", {
    month: rideDateTimeFormatOptions.month,
    day: rideDateTimeFormatOptions.day,
    year: rideDateTimeFormatOptions.year,
    timeZone: rideDateTimeFormatOptions.timeZone,
  }).format(d);
}

function formatTimeOnly(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", {
    hour: rideDateTimeFormatOptions.hour,
    minute: rideDateTimeFormatOptions.minute,
    timeZone: rideDateTimeFormatOptions.timeZone,
  }).format(d);
}

function bestStopTimestamp(s: StopPoint) {
  return s.completedAt || s.arrivedAt || s.eta || s.plannedArrivalTime || s.beforeTime || null;
}

function getPickupStop(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  return sorted.find((s) => s.type === "pickup") ?? sorted[0] ?? null;
}

function getDropoffStop(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  const drop = [...sorted].reverse().find((s) => s.type === "dropoff");
  return drop ?? sorted[sorted.length - 1] ?? null;
}

function getIntermediateStops(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  if (sorted.length <= 2) return [];
  return sorted.slice(1, -1);
}

function rideStateArrowClasses(state: string) {
  const s = String(state).toLowerCase();
  if (s === "completed") return "text-emerald-300";
  if (s === "canceled" || s === "cancelled" || s === "failed" || s === "rejected") return "text-rose-300";
  if (s === "active" || s === "dispatched") return "text-amber-300";
  if (s === "pending" || s === "matching") return "text-sky-300/90";
  return "text-sky-300/90";
}

function buildStripePaymentUrl(ride: Ride) {
  const paymentId = ride.payment?.id;
  if (!paymentId) return null;
  return `https://dashboard.stripe.com/payments/${paymentId}`;
}

function buildAutofleetBookingUrl(ride: Ride) {
  return `https://control.autofleet.io/6FnkvuL1DSM3pe847fDhCX/ride/${ride.id}`;
}

function isJsonString(s: string) {
  const t = s.trim();
  return (t.startsWith("{") && t.endsWith("}")) || (t.startsWith("[") && t.endsWith("]"));
}

function parseDashboardAppPayload(data: unknown): AppContext | null {
  let raw: unknown = data;
  if (typeof raw === "string") {
    if (!isJsonString(raw)) return null;
    try {
      raw = JSON.parse(raw);
    } catch {
      return null;
    }
  }
  if (!raw || typeof raw !== "object") return null;
  const obj = raw as { event?: string };
  if (obj.event !== "appContext") return null;
  return raw as AppContext;
}

function initialsFromName(name: string) {
  const parts = name.trim().split(/\s+/).filter(Boolean);
  if (parts.length === 0) return "?";
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function normalizeChatwootLabels(raw: unknown): string[] {
  if (!Array.isArray(raw)) return [];
  return raw
    .map((x) => {
      if (typeof x === "string") return x;
      if (x && typeof x === "object" && "title" in x && typeof (x as { title?: string }).title === "string") {
        return (x as { title: string }).title;
      }
      return "";
    })
    .filter(Boolean);
}

function readLifetimeSpendCad(attrs?: Record<string, unknown> | null): number | null {
  if (!attrs) return null;
  const keys = ["lifetime_spend", "lifetimeSpendCad", "lifetime_spend_cad", "lifetimeSpend"];
  for (const k of keys) {
    const v = attrs[k];
    if (typeof v === "number" && Number.isFinite(v)) return v;
    if (typeof v === "string") {
      const n = parseFloat(v.replace(/[^0-9.-]/g, ""));
      if (Number.isFinite(n)) return n;
    }
  }
  return null;
}

type DerivedCustomer = {
  displayName: string;
  email: string | null;
  phone: string | null;
  identifier: string | null;
  contactId: number | undefined;
  conversationId: number | undefined;
  inboxId: number | undefined;
  thumbnail: string | null;
  company: string | null;
  description: string | null;
  availabilityStatus: string | null;
  channel: string | null;
  conversationStatus: string | null;
  labels: string[];
  agentName: string | null;
  agentEmail: string | null;
  socialProfiles: ChatwootSocialProfiles | null;
  mergedCustomAttributes: Record<string, unknown>;
};

function resolveHeroDisplayName(
  loading: boolean,
  derived: DerivedCustomer | null,
  embedded: boolean,
): string {
  if (loading) return "Loading…";
  const v = derived?.displayName?.trim() ?? "";
  if (v) return v;
  return embedded ? "Unknown contact" : MOCK_CLIENT.name;
}

function resolveHeroLifetimeSpend(
  derived: DerivedCustomer | null,
  embedded: boolean,
): number | null {
  const fromAttrs = readLifetimeSpendCad(derived?.mergedCustomAttributes);
  if (fromAttrs != null) return fromAttrs;
  return embedded ? null : MOCK_CLIENT.lifetimeSpendCad;
}

function deriveCustomer(ctx: AppContext | null): DerivedCustomer | null {
  const data = ctx?.data;
  if (!data) return null;
  const contact = data.contact;
  const sender = data.conversation?.meta?.sender;
  const conv = data.conversation;
  const agent = data.currentAgent;

  const displayName =
    contact?.name?.trim() || sender?.name?.trim() || "";
  const email = contact?.email || sender?.email || null;
  const phone = contact?.phone_number || sender?.phone_number || null;
  const identifier = (contact?.identifier ?? sender?.identifier ?? null) as string | null;
  const thumbnail = contact?.thumbnail || sender?.thumbnail || null;
  const company =
    contact?.additional_attributes?.company_name || sender?.additional_attributes?.company_name || null;
  const description =
    contact?.additional_attributes?.description || sender?.additional_attributes?.description || null;
  const socialProfiles =
    contact?.additional_attributes?.social_profiles || sender?.additional_attributes?.social_profiles || null;

  const mergedCustomAttributes: Record<string, unknown> = {
    ...(sender?.custom_attributes ?? {}),
    ...(contact?.custom_attributes ?? {}),
  };

  const labels = normalizeChatwootLabels(conv?.labels);

  return {
    displayName,
    email,
    phone,
    identifier,
    contactId: contact?.id,
    conversationId: conv?.id,
    inboxId: conv?.inbox_id,
    thumbnail,
    company,
    description,
    availabilityStatus: contact?.availability_status || sender?.availability_status || null,
    channel: conv?.meta?.channel || null,
    conversationStatus: conv?.status || null,
    labels,
    agentName: agent?.name ?? null,
    agentEmail: agent?.email ?? null,
    socialProfiles: socialProfiles ?? null,
    mergedCustomAttributes,
  };
}

function normalizeRide(raw: unknown): Ride {
  if (!raw || typeof raw !== "object") {
    return raw as Ride;
  }
  const o = raw as Record<string, unknown>;
  let finalizedAt: string | null | undefined;
  if (o.finalizedAt === null) {
    finalizedAt = null;
  } else if (typeof o.finalizedAt === "string") {
    finalizedAt = o.finalizedAt;
  } else {
    finalizedAt = undefined;
  }

  const baseRide = o as unknown as Ride;
  const extra = finalizedAt === undefined ? {} : { finalizedAt };
  return {
    ...baseRide,
    ...extra,
  };
}

function normalizeRidesPayload(data: unknown): Ride[] {
  let rows: unknown[] = [];
  if (Array.isArray(data)) rows = data;
  else if (data && typeof data === "object") {
    const o = data as Record<string, unknown>;
    if (Array.isArray(o.rows)) rows = o.rows;
    else if (Array.isArray(o.data)) rows = o.data;
  }
  return rows.map((row) => normalizeRide(row));
}

function useClientRides(
  derived: DerivedCustomer | null,
  opts: { embedded: boolean; hasContext: boolean; adminKey: string },
) {
  const [rides, setRides] = React.useState<Ride[]>([]);
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<string | null>(null);
  const [pageNumber, setPageNumber] = React.useState(0);
  const [hasMore, setHasMore] = React.useState(false);

  const loadMore = React.useCallback(() => {
    setPageNumber((p) => p + 1);
  }, []);

  React.useEffect(() => {
    setPageNumber(0);
  }, [opts.embedded, opts.hasContext, opts.adminKey, derived?.displayName, derived?.email, derived?.phone]);

  React.useEffect(() => {
    // In the Chatwoot iframe, wait for `appContext` (contact / conversation) before calling the API.
    if (opts.embedded && !opts.hasContext) {
      setRides([]);
      setError(null);
      setLoading(false);
      setHasMore(false);
      return;
    }

    const name = derived?.displayName?.trim() ?? "";
    const email = derived?.email?.trim() ?? "";
    const phone = normalizePhoneForLookup(derived?.phone ?? "");
    const hasLookup = Boolean(phone) || Boolean(email) || Boolean(name && email);
    if (!hasLookup) {
      setRides([]);
      setError(null);
      setLoading(false);
      setHasMore(false);
      return;
    }

    if (!opts.adminKey) {
      setRides([]);
      setError(
        "Missing admin key: add ?admin-key=… to the dashboard app URL (same origin as this widget).",
      );
      setLoading(false);
      setHasMore(false);
      return;
    }

    let cancelled = false;
    setLoading(true);
    setError(null);

    const ridesUrl = `/api/rides?${new URLSearchParams({ "admin-key": opts.adminKey })}`;
    const pageSize = 25;

    fetch(ridesUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({
        phone,
        name,
        email,
        pageNumber,
      }),
    })
      .then(async (res) => {
        const data: unknown = await res.json().catch(() => ({}));
        if (!res.ok) {
          const msg =
            data &&
            typeof data === "object" &&
            "message" in data &&
            typeof (data as { message?: unknown }).message === "string"
              ? (data as { message: string }).message
              : `Request failed (${res.status})`;
          throw new Error(msg);
        }
        return normalizeRidesPayload(data);
      })
      .then((list) => {
        if (!cancelled) {
          setHasMore(list.length >= pageSize);
          setRides((prev) => mergeRidesPage(prev, list, pageNumber));
        }
      })
      .catch((e: unknown) => {
        if (!cancelled) {
          setError(e instanceof Error ? e.message : "Failed to load rides");
          setRides([]);
          setHasMore(false);
        }
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [
    opts.embedded,
    opts.hasContext,
    opts.adminKey,
    derived?.displayName,
    derived?.email,
    derived?.phone,
    pageNumber,
  ]);

  return { rides, loading, error, hasMore, loadMore };
}

function useChatwootAppContext() {
  const [ctx, setCtx] = React.useState<AppContext | null>(null);
  const [embedded, setEmbedded] = React.useState(false);

  React.useEffect(() => {
    setEmbedded(typeof window !== "undefined" && window.parent !== window);
  }, []);

  React.useEffect(() => {
    function onMessage(event: MessageEvent) {
      const parsed = parseDashboardAppPayload(event.data);
      if (parsed?.data) setCtx(parsed);
    }
    window.addEventListener("message", onMessage);
    if (typeof window !== "undefined" && window.parent !== window) {
      window.parent.postMessage("chatwoot-dashboard-app:fetch-info", "*");
    }
    return () => window.removeEventListener("message", onMessage);
  }, []);

  const refresh = React.useCallback(() => {
    if (typeof window !== "undefined" && window.parent !== window) {
      window.parent.postMessage("chatwoot-dashboard-app:fetch-info", "*");
    }
  }, []);

  return { ctx, embedded, refresh };
}

const StopDot = React.forwardRef<
  HTMLButtonElement,
  {
    variant: "pickup" | "dropoff" | "mid";
    tooltipTitle: string;
  } & Omit<React.ComponentPropsWithoutRef<"button">, "variant">
>(function StopDot({ variant, tooltipTitle, className, ...props }, ref) {
  let icon: React.ReactNode;
  if (variant === "dropoff") {
    icon = <FlagTriangleRight className="h-5 w-5 md:h-8 md:w-8" />;
  } else {
    icon = <MapPin className="h-6 w-6 md:h-8 md:w-8" />;
  }

  const tones =
    variant === "pickup"
      ? "text-emerald-400 hover:text-emerald-300"
      : variant === "dropoff"
        ? "text-rose-400 hover:text-rose-300"
        : "text-amber-400 hover:text-amber-300";

  return (
    <button
      ref={ref}
      type="button"
      className={cn(
        "grid h-8 w-8 place-items-center rounded-md border-0 bg-transparent transition-colors hover:bg-transparent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 md:h-9 md:w-9",
        tones,
        className,
      )}
      aria-label={tooltipTitle}
      {...props}
    >
      {icon}
    </button>
  );
});

function DriverPopover({ ride }: { ride: Ride }) {
  const name = [ride.driver?.firstName, ride.driver?.lastName].filter(Boolean).join(" ").trim() || "—";
  const phone = ride.driver?.phoneNumber || "";
  const telHref = phone ? `tel:${phone}` : null;
  const vehicleYear = ride.vehicle?.year ?? null;
  const vehicleMake = ride.vehicle?.make ?? null;
  const vehicleModelName = ride.vehicle?.model?.name ?? null;
  const vehicleModelClass = ride.vehicle?.model?.class ?? null;
  const vehicleModel = vehicleModelName ?? vehicleModelClass ?? null;
  const vehicleLine =
    [vehicleYear, vehicleMake, vehicleModel, vehicleModelName && vehicleModelClass ? vehicleModelClass : null]
      .filter(Boolean)
      .join(" / ") || "—";
  const vehicleSectionHeading =
    vehicleModelName && vehicleModelClass
      ? "Vehicle (Year / Make / Model / Class)"
      : "Vehicle (Year / Make / Model)";

  return (
    <Popover>
      <PopoverTrigger asChild>
        <button
          type="button"
          className={cn(
            "inline-flex items-center gap-1.5 rounded-md border-0 bg-transparent p-0 text-left",
            phone ? "cursor-pointer text-white" : "text-zinc-100",
          )}
        >
          <span className="font-base">{name}</span>
        </button>
      </PopoverTrigger>
      <PopoverContent
        sideOffset={8}
        align="start"
        className="w-[min(100vw-2rem,380px)] max-w-[380px] border-0 bg-zinc-900 p-4 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)] outline-none"
      >
        <div className="space-y-3">
          <div className="text-lg font-semibold text-white">{name}</div>
          <Separator className="bg-white/10" />
          <div className="space-y-2">
            <div className="text-sm font-base uppercase tracking-wide text-white/70">Phone number</div>
            {telHref ? (
              <a className="inline-flex items-center gap-2 font-semibold text-white/90 hover:text-white" href={telHref}>
                <Phone className="h-5 w-5 shrink-0 opacity-90" />
                <span className="break-all">{phone}</span>
              </a>
            ) : (
              <div className="font-medium text-white/85">—</div>
            )}
          </div>
          <Separator className="bg-white/10" />
          <div className="space-y-2">
            <div className="text-sm font-base uppercase tracking-wide text-white/70">{vehicleSectionHeading}</div>
            <div className="font-semibold text-white/90">{vehicleLine}</div>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  );
}

function RideRow({ ride }: { ride: Ride }) {
  const pickup = getPickupStop(ride);
  const dropoff = getDropoffStop(ride);
  const midStops = getIntermediateStops(ride);

  const [priceOpen, setPriceOpen] = React.useState(false);

  const stripeUrl = buildStripePaymentUrl(ride);
  const bookingUrl = buildAutofleetBookingUrl(ride);

  const vehicleLabel =
    ride.vehicle?.model?.name ||
    ride.vehicle?.model?.class ||
    (ride.vehicle ? "Vehicle" : null) ||
    "—";

  return (
    <Card className="bg-white/[0.09] shadow-[0_8px_36px_rgba(0,0,0,0.28)] ring-1 ring-white/[0.14]">
      <div className="px-2 sm:py-1 sm:px-4">
        <div className="grid gap-4 sm:grid-cols-[160px_1fr_240px] sm:items-start sm:gap-6">
          {/* Column 1: Date + time */}
          <div className="flex shrink-0 flex-row flex-wrap items-baseline gap-x-2 gap-y-0.5 text-lg font-medium leading-tight text-white sm:pt-0.5">
            <div>{formatDateOnly(ride.createdAt)}</div>
            <div className="text-base font-normal text-white/90">{formatTimeOnly(ride.createdAt)}</div>
          </div>

          {/* Column 2: Route + stops */}
          <div className="min-w-0">
            <div className="min-w-0 w-full text-xl font-medium tracking-tight text-white sm:text-3xl">
              <div className="flex min-w-0 flex-col gap-1.5 flex-row flex-wrap sm:flex-row sm:items-center sm:gap-4">
                <span className="min-w-0 break-words leading-snug text-white drop-shadow-[0_1px_12px_rgba(255,255,255,0.08)]">
                  {pickup?.description?.split(",")[0] ?? "Pickup"}
                </span>
                <ArrowRight className={cn("hidden h-5 w-5 shrink-0 sm:block", rideStateArrowClasses(ride.state))} aria-hidden />
                <span className="flex min-w-0 items-start gap-2 leading-snug sm:contents">
                  <ArrowRight className={cn("mt-0.5 h-5 w-5 shrink-0 sm:hidden", rideStateArrowClasses(ride.state))} aria-hidden />
                  <span className="min-w-0 break-words text-whites">{dropoff?.description?.split(",")[0] ?? "Dropoff"}</span>
                </span>
              </div>
            </div>

            <div className="mt-5 flex flex-wrap items-center sm:gap-11 justify-between sm:justify-start">
              {pickup ? (
                <Tooltip>
                  <TooltipTrigger asChild>
                    <StopDot variant="pickup" tooltipTitle={formatDateTime(bestStopTimestamp(pickup))} />
                  </TooltipTrigger>
                  <TooltipContent
                    sideOffset={8}
                    align="start"
                    className="max-w-[420px] flex flex-col items-stretch gap-0 border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
                  >
                    <div className="space-y-3">
                      <div className="text-lg font-semibold leading-snug text-white">Pickup</div>
                      <div className="space-y-2">

                        <div className="font-semibold leading-snug text-white/90">{pickup.description}</div>
                      </div>
                      <div className="grid gap-2 text-sm">
                        <div className="flex items-center justify-between gap-4">
                          <div className="font-semibold text-white/75">Booking time</div>
                          <div className="font-bold text-white">{formatDateTime(ride.createdAt)}</div>
                        </div>
                        <div className="flex items-center justify-between gap-4">
                          <div className="font-semibold text-white/75">Arrival time</div>
                          <div className="font-bold text-white">{formatDateTime(pickup.arrivedAt ?? null)}</div>
                        </div>
                      </div>
                    </div>
                  </TooltipContent>
                </Tooltip>
              ) : null}

              {midStops.length ? (
                <div className="flex items-center sm:gap-9 mx-12 justify-between sm:justify-start">
                  {midStops.map((s) => {
                    return (
                      <Tooltip key={s.id}>
                        <TooltipTrigger asChild>
                          <StopDot variant="mid" tooltipTitle={formatDateTime(bestStopTimestamp(s))} />
                        </TooltipTrigger>
                        <TooltipContent
                          sideOffset={8}
                          align="start"
                          className="max-w-[420px] flex flex-col items-stretch gap-0 border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
                        >
                          <div className="space-y-3">
                            <div className="text-lg font-semibold leading-snug text-white">Stop</div>
                            <div className="space-y-2">
                
                              <div className="font-semibold leading-snug text-white/90">{s.description}</div>
                            </div>
                            <div className="grid gap-2 text-sm">
                              <div className="flex items-center justify-between gap-4">
                                <div className="font-semibold text-white/75">Arrived at stop</div>
                                <div className="font-bold text-white">{formatDateTime(s.arrivedAt ?? null)}</div>
                              </div>
                              <div className="flex items-center justify-between gap-4">
                                <div className="font-semibold text-white/75">Completed stop</div>
                                <div className="font-bold text-white">{formatDateTime(s.completedAt ?? null)}</div>
                              </div>
                            </div>
                          </div>
                        </TooltipContent>
                      </Tooltip>
                    );
                  })}
                </div>
              ) : null}

              {dropoff ? (
                <Tooltip>
                  <TooltipTrigger asChild>
                    <StopDot variant="dropoff" tooltipTitle={formatDateTime(bestStopTimestamp(dropoff))} />
                  </TooltipTrigger>
                  <TooltipContent
                    sideOffset={8}
                    align="start"
                    className="max-w-[420px] flex flex-col items-stretch gap-0 border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
                  >
                    <div className="space-y-3">
                      <div className="text-lg font-semibold leading-snug text-white">Dropoff</div>
                      <div className="space-y-2">
                 
                        <div className="font-semibold leading-snug text-white/90">{dropoff.description}</div>
                      </div>
                      <div className="grid gap-2 text-sm">
                        <div className="flex items-center justify-between gap-4">
                          <div className="font-semibold text-white/75">Arrived at stop</div>
                          <div className="font-bold text-white">{formatDateTime(dropoff.arrivedAt ?? null)}</div>
                        </div>
                        <div className="flex items-center justify-between gap-4">
                          <div className="font-semibold text-white/75">Completed stop</div>
                          <div className="font-bold text-white">{formatDateTime(dropoff.completedAt ?? null)}</div>
                        </div>
                        <div className="flex items-center justify-between gap-4 border-t border-white/10 pt-2">
                          <div className="font-semibold text-white/75">Ride completed time</div>
                          <div className="font-bold text-white">{formatDateTime(rideCompletedAtIso(ride))}</div>
                        </div>
                      </div>
                    </div>
                  </TooltipContent>
                </Tooltip>
              ) : null}
            </div>

            <div className="mt-5 flex flex-wrap items-center gap-x-7 gap-y-1 text-base font-medium text-zinc-100">
              <div className="inline-flex items-center gap-2">
                <span className="font-base text-zinc-300">Driver : </span>
                <DriverPopover ride={ride} />
              </div>
              <div className="inline-flex items-center gap-2">
                <span className="font-base text-zinc-300">Vehicle type : </span>
                <span className="font-medium text-white">{vehicleLabel}</span>
              </div>
            </div>
          </div>

          {/* Column 3: Money + links */}
          <div className="shrink-0 sm:pt-0.5">
            <div className="relative flex justify-center">
              <div className="text-center text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                {formatMoney(ride.priceAmount, ride.priceCurrency)}
              </div>
              <button
                type="button"
                className="absolute -top-2 right-0 inline-flex h-8 w-8 items-center justify-center rounded-full text-white/80 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30"
                onClick={() => setPriceOpen((o) => !o)}
                aria-label="Price details"
              >
                <Info className="h-5 w-5" />
              </button>
            </div>

            <div className="mt-8 sm:mt-12 flex flex-row justify-center items-center sm:mr-4">
              <Button
                asChild
                variant="ghost"
                className="h-9  justify-center text-base font-base text-sky-300 hover:bg-white/10 hover:text-sky-200"
              >
                <a href={stripeUrl ?? "#"} target="_blank" rel="noreferrer" aria-disabled={!stripeUrl}>
                  <span>Open Payment</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-90" />
                </a>
              </Button>

              <Button
                asChild
                variant="ghost"
                className="h-9 justify-center text-base font-base text-white hover:bg-white/10 hover:text-white"
              >
                <a href={bookingUrl} target="_blank" rel="noreferrer">
                  <span>Open Booking</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-80" />
                </a>
              </Button>
            </div>
          </div>
        </div>
      </div>

      <Dialog open={priceOpen} onOpenChange={setPriceOpen}>
        <DialogContent>
          <DialogTitle className="text-xl font-bold tracking-tight text-white">Payment details</DialogTitle>
          <div className="mt-4 space-y-3 text-base">
            <div className="flex items-center justify-between gap-4">
              <div className="font-semibold text-white/80">Pre auth</div>
              <div className="font-extrabold text-white">
                {formatMoney(ride.paymentBreakdown?.preAuth ?? 0, ride.priceCurrency)}
              </div>
            </div>
            <div className="flex items-center justify-between gap-4">
              <div className="font-semibold text-white/80">Captured</div>
              <div className="font-extrabold text-white">
                {formatMoney(ride.paymentBreakdown?.captured ?? 0, ride.priceCurrency)}
              </div>
            </div>
            <div className="flex items-center justify-between gap-4">
              <div className="font-semibold text-white/80">Refunded</div>
              <div className="font-extrabold text-white">
                {formatMoney(ride.paymentBreakdown?.refunded ?? 0, ride.priceCurrency)}
              </div>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </Card>
  );
}

function formatAttrValue(v: unknown): string {
  if (v === null || v === undefined) return "—";
  if (typeof v === "object") {
    try {
      return JSON.stringify(v);
    } catch {
      return String(v);
    }
  }
  return String(v);
}

function formatOptionalId(v: unknown): string {
  if (v == null) return "—";
  if (typeof v === "object") {
    try {
      return JSON.stringify(v);
    } catch {
      return "—";
    }
  }
  return String(v);
}

function CustomerProfilePopover({ profile }: Readonly<{ profile: DerivedCustomer }>) {
  return (
    <Popover>
      <PopoverTrigger asChild>
        <button
          type="button"
          className="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-white/15 bg-white/5 px-2.5 py-1 text-sm font-semibold text-sky-200 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/50"
        >
          <UserCircle className="h-4 w-4" />
          Profile
        </button>
      </PopoverTrigger>
      <PopoverContent
        sideOffset={8}
        align="start"
        className="max-h-[min(70vh,520px)] w-[min(100vw-2rem,400px)] overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden border-0 bg-zinc-900 p-0 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
      >
        <div className="border-b border-white/10 bg-white/[0.06] px-4 py-3">
          <div className="mt-1 text-lg font-semibold leading-tight text-white">Full Profile</div>
        </div>
        <div className="space-y-4 p-4">
          <div className="grid gap-2 text-sm">
            <DetailRow label="Contact ID" value={formatOptionalId(profile.contactId)} />
            <DetailRow label="Conversation ID" value={formatOptionalId(profile.conversationId)} />
            <DetailRow label="Inbox ID" value={formatOptionalId(profile.inboxId)} />
          </div>
          <Separator className="bg-white/10" />
          <div className="grid gap-2 text-sm">
            <DetailRow label="Email" value={profile.email || "—"} mono={!!profile.email} />
            <DetailRow label="Phone" value={profile.phone || "—"} mono={!!profile.phone} />
            <DetailRow label="Company" value={profile.company || "—"} />
            {profile.description ? (
              <div className="pt-1">
                <div className="text-xs font-semibold uppercase tracking-wide text-zinc-500">Notes</div>
                <p className="mt-1 whitespace-pre-wrap text-zinc-200">{profile.description}</p>
              </div>
            ) : null}
          </div>
          <Separator className="bg-white/10" />
          <div className="grid gap-2 text-sm">
            <DetailRow label="Channel" value={profile.channel || "—"} />
            <DetailRow label="Conversation status" value={profile.conversationStatus || "—"} />
            <DetailRow label="Availability" value={profile.availabilityStatus || "—"} />
            {profile.labels.length > 0 ? (
              <div className="flex flex-wrap gap-1.5 pt-1">
                {profile.labels.map((lb) => (
                  <Badge key={lb} variant="secondary" className="bg-white/10 text-zinc-100">
                    {lb}
                  </Badge>
                ))}
              </div>
            ) : null}
          </div>
          {(profile.agentName || profile.agentEmail) && (
            <>
              <Separator className="bg-white/10" />
              <div className="rounded-xl bg-white/[0.06] p-3 ring-1 ring-white/10">
                <div className="text-xs font-semibold uppercase tracking-wide text-zinc-500">Current agent</div>
                <div className="mt-1 font-semibold text-white">{profile.agentName || "—"}</div>
                {profile.agentEmail ? (
                  <a className="mt-0.5 block text-sm text-sky-300 hover:text-sky-200" href={`mailto:${profile.agentEmail}`}>
                    {profile.agentEmail}
                  </a>
                ) : null}
              </div>
            </>
          )}
          {profile.socialProfiles && Object.values(profile.socialProfiles).some(Boolean) && (
            <>
              <Separator className="bg-white/10" />
              <div className="text-xs font-semibold uppercase tracking-wide text-zinc-500">Social</div>
              <div className="mt-2 flex flex-wrap gap-2">
                {Object.entries(profile.socialProfiles).map(([k, url]) =>
                  url ? (
                    <a
                      key={k}
                      href={url.startsWith("http") ? url : `https://${url}`}
                      target="_blank"
                      rel="noreferrer"
                      className="inline-flex items-center rounded-md bg-white/10 px-2 py-1 text-xs font-medium capitalize text-sky-200 hover:bg-white/15"
                    >
                      {k}
                      <ExternalLink className="ml-1 h-3 w-3 opacity-80" />
                    </a>
                  ) : null,
                )}
              </div>
            </>
          )}
          {Object.keys(profile.mergedCustomAttributes).length > 0 && (
            <>
              <Separator className="bg-white/10" />
              <div className="text-xs font-semibold uppercase tracking-wide text-zinc-500">Customer ID</div>
              <div className="mt-2 space-y-1.5 rounded-xl bg-black/25 p-3 font-mono text-xs text-zinc-300 ring-1 ring-white/10">
                {Object.entries(profile.mergedCustomAttributes).map(([k, v]) => (
                  <div key={k} className="flex gap-2 break-all">
                    <span className="shrink-0 text-emerald-300/90">{k}</span>
                    <span className="text-zinc-400">:</span>
                    <span className="min-w-0 text-zinc-200">{formatAttrValue(v)}</span>
                  </div>
                ))}
              </div>
            </>
          )}
        </div>
      </PopoverContent>
    </Popover>
  );
}

function CustomerHeroAvatar({
  thumbnail,
  loading,
  displayName,
}: Readonly<{
  thumbnail: string | null | undefined;
  loading: boolean;
  displayName: string;
}>) {
  if (thumbnail) {
    return (
      // eslint-disable-next-line @next/next/no-img-element
      <img
        src={thumbnail}
        alt=""
        className="h-16 w-16 shrink-0 rounded-2xl object-cover shadow-[0_8px_24px_rgba(0,0,0,0.35)] ring-1 ring-white/25"
      />
    );
  }
  return (
    <div
      className={cn(
        "grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-400/50 to-emerald-500/35 text-xl font-bold tracking-tight text-white shadow-[0_8px_24px_rgba(0,0,0,0.35)] ring-2 ring-white/25",
        loading && "animate-pulse",
      )}
      aria-hidden
    >
      {loading ? "…" : initialsFromName(displayName)}
    </div>
  );
}

function CustomerHeroCompanyLine({ company }: Readonly<{ company: string | null | undefined }>) {
  if (!company) return null;
  return (
    <div className="flex items-center gap-2 text-base font-semibold text-emerald-200/95">
      <Building2 className="h-4 w-4 shrink-0 opacity-90" />
      <span className="truncate">{company}</span>
    </div>
  );
}

function CustomerHeroEmailSlot({
  email,
  embedded,
  hasContext,
}: Readonly<{
  email: string | null;
  embedded: boolean;
  hasContext: boolean;
}>) {
  if (email) {
    return (
      <a
        href={`mailto:${email}`}
        className="inline-flex max-w-full items-center gap-1.5 truncate text-sky-200 hover:text-sky-100"
      >
        <Mail className="h-4 w-4 shrink-0 opacity-90" />
        <span className="truncate sm:text-[16px] ">{email}</span>
      </a>
    );
  }
  const placeholder = embedded && hasContext ? "No email" : "—";
  return (
    <span className="inline-flex items-center gap-1.5 text-zinc-500">
      <Mail className="h-4 w-4" />
      {placeholder}
    </span>
  );
}

function CustomerHeroPhoneSlot({
  phone,
  embedded,
  hasContext,
}: Readonly<{
  phone: string | null;
  embedded: boolean;
  hasContext: boolean;
}>) {
  if (phone) {
    const telHref = `tel:${phone.replace(/\s/g, "")}`;
    return (
      <a href={telHref} className="inline-flex items-center gap-1.5 text-emerald-200/95 hover:text-emerald-100">
        <Phone className="h-4 w-4 shrink-0 opacity-90" />
        <span className="truncate sm:text-[16px] ">{phone}</span>
      </a>
    );
  }
  const placeholder = embedded && hasContext ? "No phone" : "—";
  return (
    <span className="inline-flex items-center gap-1.5 text-zinc-500">
      <Phone className="h-4 w-4" />
      {placeholder}
    </span>
  );
}

function CustomerHeroContactRow(
  props: Readonly<{
    email: string | null;
    phone: string | null;
    embedded: boolean;
    hasContext: boolean;
  }>,
) {
  return (
    <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-medium">
      <CustomerHeroEmailSlot {...props} />
      <CustomerHeroPhoneSlot {...props} />
    </div>
  );
}

function CustomerHeroLifetimePanel({ lifetimeSpend }: Readonly<{ lifetimeSpend: number | null }>) {
  return (
    <div className="shrink-0 text-right sm:pl-2">
      <div className="text-xs font-bold uppercase tracking-[0.14em] text-zinc-400">Lifetime spend</div>
      <div
        className={cn(
          "mt-1 inline-flex items-center rounded-full bg-sky-400/25 px-3.5 py-1.5 text-lg font-bold text-white ring-1 ring-sky-300/45 shadow-[0_0_28px_rgba(56,189,248,0.25)] sm:text-xl",
          lifetimeSpend == null && "text-zinc-400 ring-zinc-600/40 shadow-none",
        )}
      >
        {lifetimeSpend != null ? formatMoney(lifetimeSpend, "CAD") : "—"}
      </div>
    </div>
  );
}

function CustomerHeroTitleRow({
  displayName,
  loading,
  showProfilePopover,
  profile,
}: Readonly<{
  displayName: string;
  loading: boolean;
  showProfilePopover: boolean;
  profile: DerivedCustomer | null;
}>) {
  return (
    <div className="flex flex-wrap items-end gap-x-3 gap-y-1">
      <div
        className={cn(
          "truncate text-2xl font-extrabold tracking-tight text-white drop-shadow-sm sm:text-4xl",
          loading && "animate-pulse text-white/80",
        )}
      >
        {displayName}
      </div>
      {showProfilePopover && profile ? <CustomerProfilePopover profile={profile} /> : null}
    </div>
  );
}

function CustomerHero({
  embedded,
  derived,
  hasContext,
  loading,
}: Readonly<{
  embedded: boolean;
  derived: DerivedCustomer | null;
  hasContext: boolean;
  loading: boolean;
}>) {
  const displayName = resolveHeroDisplayName(loading, derived, embedded);
  const lifetimeSpend = resolveHeroLifetimeSpend(derived, embedded);
  const showProfilePopover = Boolean(embedded && hasContext && derived);
  const profile = derived ?? null;

  return (
    <div className="sticky top-0 z-20 -mx-4 px-4 pb-3 pt-2 backdrop-blur sm:-mx-8 sm:px-8">
      <div className="relative overflow-hidden rounded-3xl bg-gradient-to-b from-white/[0.16] to-white/[0.07] p-5 ring-1 ring-white/[0.18] shadow-[0_12px_48px_rgba(0,0,0,0.45)] sm:p-6">
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(620px_circle_at_18%_0%,rgba(56,189,248,0.28),transparent_58%),radial-gradient(520px_circle_at_88%_18%,rgba(52,211,153,0.2),transparent_55%),radial-gradient(400px_circle_at_50%_100%,rgba(167,139,250,0.12),transparent_50%)]" />
        <div className="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div className="flex min-w-0 flex-1 gap-4">
            <CustomerHeroAvatar thumbnail={derived?.thumbnail} loading={loading} displayName={displayName} />
            <div className="min-w-0 flex-1 space-y-2">
              <CustomerHeroTitleRow
                displayName={displayName}
                loading={loading}
                showProfilePopover={showProfilePopover}
                profile={profile}
              />
              <CustomerHeroCompanyLine company={derived?.company} />
              <CustomerHeroContactRow
                email={derived?.email ?? null}
                phone={derived?.phone ?? null}
                embedded={embedded}
                hasContext={hasContext}
              />
            </div>
          </div>
          <CustomerHeroLifetimePanel lifetimeSpend={lifetimeSpend} />
        </div>
      </div>
    </div>
  );
}

function DetailRow({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <div className="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:gap-3">
      <div className="flex shrink-0 items-center gap-1.5 text-zinc-500">
        <Hash className="h-3.5 w-3.5 opacity-70" />
        <span className="text-xs font-semibold uppercase tracking-wide">{label}</span>
      </div>
      <div className={cn("min-w-0 break-all text-zinc-100", mono && "font-mono text-[13px]sm:text-[18px]")}>{value}</div>
    </div>
  );
}

function HomeContent() {
  const searchParams = useSearchParams();
  const adminKey = searchParams.get("admin-key")?.trim() ?? "";

  const { ctx, embedded } = useChatwootAppContext();
  const derived = React.useMemo(() => deriveCustomer(ctx), [ctx]);
  const hasContext = Boolean(ctx?.data);
  const heroLoading = embedded && !hasContext;

  const { rides, loading: ridesLoading, error: ridesError, hasMore, loadMore } = useClientRides(derived, {
    embedded,
    hasContext,
    adminKey,
  });

  let ridesStatusNode: React.ReactNode = null;
  if (ridesLoading) {
    ridesStatusNode = <div className="mt-6 text-center text-sm font-medium text-zinc-400">Loading rides…</div>;
  } else if (ridesError) {
    ridesStatusNode = (
      <div className="mt-6 rounded-xl border border-rose-500/30 bg-rose-950/35 px-4 py-3 text-center text-sm text-rose-100">
        {ridesError}
      </div>
    );
  }

  return (
    <TooltipProvider delayDuration={0} skipDelayDuration={0}>
      <div className="min-h-dvh bg-[#12151F] text-zinc-50">
        <div className="w-full px-4 py-4 text-base sm:px-8 sm:py-6 sm:text-[18px]">
          <CustomerHero
            embedded={embedded}
            derived={derived}
            hasContext={hasContext}
            loading={heroLoading}
          />

          {ridesStatusNode}

          <div className="mt-3 space-y-3 sm:mt-4">
            {rides.map((ride) => (
              <RideRow key={ride.id} ride={ride} />
            ))}
          </div>

          <div className="mt-4 flex justify-center">
            <Button
              type="button"
              variant="ghost"
              className="h-10 px-4 text-base font-semibold text-white hover:bg-white/10 disabled:opacity-50"
              disabled={ridesLoading || !!ridesError || !hasMore}
              onClick={loadMore}
            >
              {ridesLoading ? (
                <span className="inline-flex items-center gap-2">
                  <Loader2 className="h-4 w-4 animate-spin" aria-hidden />
                  Loading…
                </span>
              ) : hasMore ? (
                "Load more"
              ) : (
                "No more rides"
              )}
            </Button>
          </div>
        </div>
      </div>
    </TooltipProvider>
  );
}

export default function Home() {
  return (
    <Suspense
      fallback={
        <div className="flex min-h-dvh items-center justify-center bg-[#12151F] text-sm text-zinc-400">
          Loading…
        </div>
      }
    >
      <HomeContent />
    </Suspense>
  );
}
