"use client";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { HoverCard, HoverCardContent, HoverCardTrigger } from "@/components/ui/hover-card";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Separator } from "@/components/ui/separator";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import { cn } from "@/lib/utils";
import { ArrowRight, ExternalLink, FlagTriangleRight, MapPin, Phone } from "lucide-react";

type StopType = "pickup" | "dropoff" | string;
type RideState = "active" | "completed" | string;

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
  state: RideState;
  priceCurrency: string;
  priceAmount: number;
  createdAt: string;
  stopPoints: StopPoint[];
  payment?: { id: string; state?: string; paymentMethod?: { name?: string } } | null;
  vehicle?: { model?: { name?: string; class?: string } } | null;
  driver?: { firstName?: string; lastName?: string; phoneNumber?: string } | null;
};

const MOCK_CLIENT = {
  name: "Daniel",
  lifetimeSpendCad: 1249.55,
};

const MOCK_RIDES: Ride[] = [
  {
    id: "23f7dc5e-84b3-4b17-9616-68d69ec123a6",
    state: "active",
    priceCurrency: "CAD",
    priceAmount: 48.91,
    createdAt: "2026-03-29T03:15:52.314Z",
    stopPoints: [
      {
        id: "2d39ba87-a75e-4a59-8a63-38ee03b8a40b",
        type: "pickup",
        orderInParent: 0,
        description: "36 Division Rd, Peterborough, ON K9L 1J3, Canada",
        beforeTime: "2026-03-29T03:35:00.000Z",
        arrivedAt: "2026-03-29T03:25:27.256Z",
        completedAt: "2026-03-29T03:26:29.640Z",
      },
      {
        id: "ebe70cd3-2f1a-48c6-8f0c-9419ac1f4858",
        type: "dropoff",
        orderInParent: 1,
        description: "224 N Indian Rd, Hastings, ON, Canada",
        plannedArrivalTime: "2026-03-29T03:54:27Z",
        eta: "2026-03-29T03:55:27Z",
      },
    ],
    payment: { id: "2ec3487a-2b7d-46ea-884f-6b94187d9f49", state: "pending", paymentMethod: { name: "Mastercard 1218" } },
    vehicle: { model: { name: "Honda Civic", class: "A" } },
    driver: { firstName: "Benjamin", lastName: "Bentil", phoneNumber: "12269617061" },
  },
  {
    id: "d2508261-1e07-4872-b10c-7bae17468c50",
    state: "completed",
    priceCurrency: "CAD",
    priceAmount: 47.56,
    createdAt: "2025-12-13T07:20:43.507Z",
    stopPoints: [
      {
        id: "a290bb00-edde-440d-863e-72d364c9a94b",
        type: "pickup",
        orderInParent: 0,
        description: "602 Corrigan Crescent, Peterborough, ON K9J 7N8, Canada",
        beforeTime: "2025-12-13T07:40:00.000Z",
        arrivedAt: "2025-12-13T07:40:50.354Z",
        completedAt: "2025-12-13T07:40:52.608Z",
      },
      {
        id: "1c3f9204-03b4-4985-9b5c-3ed3b1ae8231",
        type: "dropoff",
        orderInParent: 1,
        description: "224 North Indian Road, Hastings, ON, Canada",
        arrivedAt: "2025-12-13T08:09:18.527Z",
        completedAt: "2025-12-13T08:09:20.841Z",
      },
    ],
    payment: { id: "48d37d0b-613f-4b09-ade5-d4f500d700c4", state: "paid", paymentMethod: { name: "Mastercard 0681" } },
    vehicle: { model: { name: "Honda Civic", class: "A" } },
    driver: { firstName: "Benjamin", lastName: "Bentil", phoneNumber: "12269617061" },
  },
  {
    id: "c8034b92-1bc9-41d0-a054-e0234883c790",
    state: "completed",
    priceCurrency: "CAD",
    priceAmount: 61.09,
    createdAt: "2024-08-29T05:54:24.775Z",
    stopPoints: [
      {
        id: "efc89e5b-069d-433a-85bd-c36e0919853d",
        type: "pickup",
        orderInParent: 0,
        description: "259 George St N, Peterborough, ON K9J 3G9, Canada",
        beforeTime: "2024-08-29T06:24:00.000Z",
        arrivedAt: "2024-08-29T06:20:47.010Z",
        completedAt: "2024-08-29T06:21:28.214Z",
      },
      {
        id: "mid-stop-1",
        type: "stop",
        orderInParent: 1,
        description: "360 George Street N, Peterborough, Ontario",
        plannedArrivalTime: "2024-08-29T06:30:00.000Z",
      },
      {
        id: "b5ab966e-d340-43e4-835a-f8eedc1456bf",
        type: "dropoff",
        orderInParent: 2,
        description: "224 North Indian Road, Hastings, ON, Canada",
        arrivedAt: "2024-08-29T06:57:25.735Z",
        completedAt: "2024-08-29T06:57:27.593Z",
      },
    ],
    payment: { id: "dc478c9a-c2c3-4965-8190-d2c3ff201ed5", state: "paid", paymentMethod: { name: "Mastercard 3373" } },
    vehicle: { model: { name: "Honda Civic", class: "A" } },
    driver: { firstName: "Akash", lastName: "Pravir", phoneNumber: "17057726275" },
  },
];

function byOrder(a: StopPoint, b: StopPoint) {
  return (a.orderInParent ?? 0) - (b.orderInParent ?? 0);
}

function formatMoney(amount: number, currency: string) {
  try {
    return new Intl.NumberFormat("en-CA", { style: "currency", currency }).format(amount);
  } catch {
    return `${currency} ${amount.toFixed(2)}`;
  }
}

function formatDateTime(iso?: string | null) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return new Intl.DateTimeFormat("en-CA", {
    month: "short",
    day: "2-digit",
    year: "numeric",
    hour: "numeric",
    minute: "2-digit",
    // Prevent hydration mismatches between server (often UTC) and client (user locale).
    timeZone: "UTC",
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

function rideStateLabel(state: RideState) {
  if (state === "active") return "Active";
  if (state === "completed") return "Completed";
  return state;
}

function rideStateBadgeClasses(state: RideState) {
  if (state === "active") return "bg-emerald-500/15 text-emerald-200 hover:bg-emerald-500/20 border-0";
  if (state === "completed") return "bg-sky-500/15 text-sky-200 hover:bg-sky-500/20 border-0";
  return "bg-zinc-500/15 text-zinc-200 hover:bg-zinc-500/20 border-0";
}

function buildStripePaymentUrl(ride: Ride) {
  const paymentId = ride.payment?.id;
  if (!paymentId) return null;
  return `https://dashboard.stripe.com/payments/${paymentId}`;
}

function buildAutofleetBookingUrl(ride: Ride) {
  return `https://control.autofleet.io/6FnkvuL1DSM3pe847fDhCX/ride/${ride.id}`;
}

function StopDot({
  variant,
  tooltipTitle,
  tooltipDescription,
}: {
  variant: "pickup" | "dropoff" | "mid";
  tooltipTitle: string;
  tooltipDescription?: string;
}) {
  const icon =
    variant === "pickup" ? (
      <MapPin className="h-4 w-4 md:h-5 md:w-5" />
    ) : variant === "dropoff" ? (
      <FlagTriangleRight className="h-4 w-4 md:h-5 md:w-5" />
    ) : (
      <span className="h-2.5 w-2.5 rounded-full bg-current md:h-3 md:w-3" />
    );

  const tones =
    variant === "pickup"
      ? "text-emerald-300 hover:text-emerald-200"
      : variant === "dropoff"
        ? "text-red-300 hover:text-red-200"
        : "text-amber-300 hover:text-amber-200";

  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <button
          type="button"
          className={cn(
            "grid h-7 w-7 place-items-center rounded-md bg-transparent transition-colors hover:bg-white/[0.06] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 md:h-8 md:w-8",
            tones,
          )}
          aria-label={tooltipTitle}
        >
          {icon}
        </button>
      </TooltipTrigger>
      <TooltipContent sideOffset={6} className="max-w-[360px] border-white/15 bg-zinc-900 text-zinc-100 shadow-[0_12px_50px_rgba(0,0,0,0.75)]">
        <div className="space-y-1">
          <div className="text-sm font-medium text-zinc-50">{tooltipTitle}</div>
          {tooltipDescription ? <div className="text-xs text-zinc-300">{tooltipDescription}</div> : null}
        </div>
      </TooltipContent>
    </Tooltip>
  );
}

function stopVariant(idx: number, total: number): "pickup" | "dropoff" | "mid" {
  if (idx === 0) return "pickup";
  if (idx === total - 1) return "dropoff";
  return "mid";
}

function stopDotClass(variant: "pickup" | "dropoff" | "mid") {
  if (variant === "pickup") return "bg-emerald-500/15 text-emerald-100 ring-emerald-400/25";
  if (variant === "dropoff") return "bg-red-500/15 text-red-100 ring-red-400/25";
  return "bg-amber-400/15 text-amber-100 ring-amber-300/25";
}

function DriverHover({ ride }: { ride: Ride }) {
  const name = [ride.driver?.firstName, ride.driver?.lastName].filter(Boolean).join(" ").trim() || "—";
  const phone = ride.driver?.phoneNumber || "";
  const telHref = phone ? `tel:${phone}` : null;

  return (
    <HoverCard openDelay={150} closeDelay={80}>
      <HoverCardTrigger asChild>
        <span className={cn("inline-flex items-center gap-1.5", phone ? "cursor-pointer text-zinc-50" : "text-zinc-200")}>
          <span className="font-medium">{name}</span>
          {phone ? <span className="text-xs text-zinc-400">(hover)</span> : null}
        </span>
      </HoverCardTrigger>
      <HoverCardContent className="w-[280px] border-white/10 bg-zinc-950/95 p-3 text-zinc-100 shadow-xl">
        <div className="space-y-1.5">
          <div className="text-sm font-medium text-zinc-50">Driver</div>
          <div className="text-sm text-zinc-200">{name}</div>
          <Separator className="bg-white/10" />
          <div className="flex items-center justify-between gap-3">
            <div className="text-xs text-zinc-400">Phone</div>
            {telHref ? (
              <a className="inline-flex items-center gap-2 text-sm text-sky-200 hover:text-sky-100" href={telHref}>
                <Phone className="h-4 w-4" />
                <span className="font-medium">{phone}</span>
              </a>
            ) : (
              <div className="text-sm text-zinc-300">—</div>
            )}
          </div>
        </div>
      </HoverCardContent>
    </HoverCard>
  );
}

function RideRow({ ride }: { ride: Ride }) {
  const pickup = getPickupStop(ride);
  const dropoff = getDropoffStop(ride);
  const midStops = getIntermediateStops(ride);
  const midStopsSorted = [...midStops].sort(byOrder);

  const stripeUrl = buildStripePaymentUrl(ride);
  const bookingUrl = buildAutofleetBookingUrl(ride);

  const vehicleLabel =
    ride.vehicle?.model?.name ||
    ride.vehicle?.model?.class ||
    (ride.vehicle ? "Vehicle" : null) ||
    "—";

  return (
    <Card className="border-0 bg-white/[0.06] shadow-[0_8px_30px_rgba(0,0,0,0.35)] ring-1 ring-white/[0.09]">
      <div className="p-4 sm:p-5">
        <div className="flex items-start justify-between gap-4">
          <div className="min-w-0 flex-1">
            <div className="flex items-start gap-3">
              <div className="mt-0.5 flex items-center gap-2">
                {pickup ? (
                  <StopDot
                    variant="pickup"
                    tooltipTitle={formatDateTime(bestStopTimestamp(pickup))}
                    tooltipDescription={pickup.description}
                  />
                ) : null}
                {midStops.length ? (
                  <div className="flex items-center gap-2">
                    {midStops.map((s) => (
                      <StopDot
                        key={s.id}
                        variant="mid"
                        tooltipTitle={formatDateTime(bestStopTimestamp(s))}
                        tooltipDescription={s.description}
                      />
                    ))}
                  </div>
                ) : null}
                {dropoff ? (
                  <StopDot
                    variant="dropoff"
                    tooltipTitle={formatDateTime(bestStopTimestamp(dropoff))}
                    tooltipDescription={dropoff.description}
                  />
                ) : null}
              </div>

              <div className="min-w-0 flex-1 space-y-1">
                <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-x-3 sm:gap-y-2">
                  <div className="min-w-0 w-full text-lg font-semibold tracking-tight text-zinc-50 sm:w-auto sm:flex-1">
                    <div className="flex flex-col gap-1.5 sm:flex-row sm:min-w-0 sm:items-center sm:gap-2">
                      <span className="min-w-0 break-words leading-snug">
                        {pickup?.description?.split(",")[0] ?? "Pickup"}
                      </span>
                      <ArrowRight
                        className="hidden h-4 w-4 shrink-0 text-zinc-400 sm:block"
                        aria-hidden
                      />
                      <span className="flex min-w-0 items-start gap-2 leading-snug sm:contents">
                        <ArrowRight className="mt-0.5 h-4 w-4 shrink-0 text-zinc-400 sm:hidden" aria-hidden />
                        <span className="min-w-0 break-words">
                          {dropoff?.description?.split(",")[0] ?? "Dropoff"}
                        </span>
                      </span>
                    </div>
                  </div>
                  <Badge
                    className={cn(
                      "h-10 shrink-0 self-start rounded-full px-2.5 text-sms font-large sm:self-center",
                      rideStateBadgeClasses(ride.state),
                    )}
                  >
                    {rideStateLabel(ride.state)}
                  </Badge>
                </div>

                <div className="flex flex-wrap items-start gap-2">
                  {pickup ? (
                    <span
                      className={cn(
                        "inline-flex max-w-full min-w-0 items-start gap-2 rounded-2xl px-2.5 py-1.5 text-xs ring-1 sm:max-w-[min(100%,24rem)] sm:rounded-full sm:py-1",
                        stopDotClass("pickup"),
                      )}
                    >
                      <span className="min-w-0 flex-1 break-words leading-snug">{pickup.description}</span>
                    </span>
                  ) : null}
                  {midStops.length ? (
                    <Popover>
                      <PopoverTrigger asChild>
                        <Button
                          type="button"
                          variant="ghost"
                          className="h-8 rounded-full bg-white/[0.05] px-3 text-xs text-amber-100 ring-1 ring-amber-300/20 hover:bg-white/[0.08]"
                        >
                          Stops +{midStops.length}
                        </Button>
                      </PopoverTrigger>
                      <PopoverContent
                        align="start"
                        className="w-[360px] border-white/15 bg-zinc-900 text-zinc-100 shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
                      >
                        <div className="space-y-2">
                          <div className="text-sm font-semibold text-zinc-50">Stops</div>
                          <div className="space-y-1.5">
                            {midStopsSorted.map((s) => {
                              return (
                                <div key={s.id} className="flex items-start gap-2 rounded-lg bg-white/[0.04] p-2 ring-1 ring-white/[0.06]">
                                  <div className={cn("mt-0.5 h-2.5 w-2.5 rounded-full ring-1", stopDotClass("mid"))} />
                                  <div className="min-w-0 flex-1">
                                    <div className="text-xs font-medium text-zinc-200">{s.description}</div>
                                    <div className="mt-0.5 text-[11px] text-zinc-400">{formatDateTime(bestStopTimestamp(s))}</div>
                                  </div>
                                </div>
                              );
                            })}
                          </div>
                        </div>
                      </PopoverContent>
                    </Popover>
                  ) : null}
                  {dropoff ? (
                    <span
                      className={cn(
                        "inline-flex max-w-full min-w-0 items-start gap-2 rounded-2xl px-2.5 py-1.5 text-xs ring-1 sm:max-w-[min(100%,24rem)] sm:rounded-full sm:py-1",
                        stopDotClass("dropoff"),
                      )}
                    >
                      <span className="min-w-0 flex-1 break-words leading-snug">{dropoff.description}</span>
                    </span>
                  ) : null}
                </div>

                <div className="grid gap-2 text-sm text-zinc-400 sm:grid-cols-[1fr_auto] sm:items-center">
                  <div className="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <div className="inline-flex items-center gap-2">
                      <span className="text-zinc-500">Driver</span>
                      <DriverHover ride={ride} />
                    </div>
                    <div className="inline-flex items-center gap-2">
                      <span className="text-zinc-500">Vehecle Type</span>
                      <span className="text-zinc-200">{vehicleLabel}</span>
                    </div>
                    {/* {ride.payment?.paymentMethod?.name ? (
                      <div className="inline-flex items-center gap-2">
                        <span className="text-zinc-500">Payment</span>
                        <span className="text-zinc-200 text-sm">{ride.payment.paymentMethod.name}</span>
                      </div>
                    ) : null} */}
                  </div>

                  <div className="text-right text-lg font-semibold tracking-tight text-zinc-50 sm:text-xl">
                    {formatMoney(ride.priceAmount, ride.priceCurrency)}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="hidden shrink-0 flex-col items-end gap-2 sm:flex">
            <div className="text-sm text-zinc-500">{formatDateTime(ride.createdAt)}</div>
            <div className="flex items-center gap-2">
              <Button asChild variant="ghost" className="h-8 px-2 text-sky-200 hover:bg-white/5 hover:text-sky-100">
                <a href={stripeUrl ?? "#"} target="_blank" rel="noreferrer" aria-disabled={!stripeUrl}>
                  <span>Open Payment</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-80" />
                </a>
              </Button>
              <Button asChild variant="ghost" className="h-8 px-2 text-zinc-200 hover:bg-white/5 hover:text-zinc-50">
                <a href={bookingUrl} target="_blank" rel="noreferrer">
                  <span>Open Booking</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-80" />
                </a>
              </Button>
            </div>
          </div>
        </div>

        <div className="mt-3 flex items-center justify-between gap-3 sm:hidden">
          <div className="text-sm text-zinc-500">{formatDateTime(ride.createdAt)}</div>
          <div className="flex items-center gap-2">
            <Button asChild size="sm" variant="ghost" className="h-8 px-2 text-sky-200 hover:bg-white/5 hover:text-sky-100">
              <a href={stripeUrl ?? "#"} target="_blank" rel="noreferrer" aria-disabled={!stripeUrl}>
                Open Payment
              </a>
            </Button>
            <Button asChild size="sm" variant="ghost" className="h-8 px-2 text-zinc-200 hover:bg-white/5 hover:text-zinc-50">
              <a href={bookingUrl} target="_blank" rel="noreferrer">
                Open Booking
              </a>
            </Button>
          </div>
        </div>
      </div>
    </Card>
  );
}

export default function Home() {
  const rides = [...MOCK_RIDES].sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());

  return (
    <TooltipProvider delayDuration={0} skipDelayDuration={0}>
      <div className="min-h-dvh bg-[#0B0D12] text-zinc-100">
        <div className="w-full px-4 py-5 text-base sm:px-8 sm:py-7 sm:text-[17px]">
          <div className="sticky top-0 z-20 -mx-4 px-4 pb-3 pt-2 backdrop-blur sm:-mx-8 sm:px-8">
            <div className="relative overflow-hidden rounded-3xl bg-gradient-to-b from-white/[0.11] to-white/[0.04] p-5 ring-1 ring-white/[0.11] shadow-[0_10px_40px_rgba(0,0,0,0.55)] sm:p-6">
              <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(600px_circle_at_20%_0%,rgba(56,189,248,0.18),transparent_55%),radial-gradient(500px_circle_at_90%_20%,rgba(34,197,94,0.12),transparent_55%)]" />
              <div className="relative flex items-start justify-between gap-4">
                <div className="min-w-0">
                  <div className="text-xs font-medium tracking-wide text-zinc-300/90">Ride history</div>
                  <div className="mt-1 truncate text-2xl font-semibold tracking-tight text-zinc-50 sm:text-3xl">
                    {MOCK_CLIENT.name}
                  </div>
                  <div className="mt-1 text-sm text-zinc-300/80">Customer details</div>
                </div>
                <div className="text-right">

                  <div className="mt-2 inline-flex items-center rounded-full bg-sky-500/10 px-3 py-1 text-base font-semibold text-sky-100 ring-1 ring-sky-300/20 sm:text-lg">
                    {formatMoney(MOCK_CLIENT.lifetimeSpendCad, "CAD")}
                  </div>
                  <div className="mt-1 text-sms text-zinc-300/70">lifetime spend</div>
                </div>
              </div>
            </div>
          </div>

          <div className="mt-3 space-y-3 sm:mt-4">
            {rides.map((ride) => (
              <RideRow key={ride.id} ride={ride} />
            ))}
          </div>

          <div className="mt-6 text-center text-xs text-zinc-500">
            Mock data only. Links are placeholders until API + real URLs are wired.
          </div>
        </div>
      </div>
    </TooltipProvider>
  );
}
