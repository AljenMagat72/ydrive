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
  if (state === "active")
    return "border-0 bg-emerald-400/25 font-semibold text-emerald-50 shadow-[0_0_24px_rgba(52,211,153,0.25)] hover:bg-emerald-400/35";
  if (state === "completed")
    return "border-0 bg-sky-400/25 font-semibold text-sky-50 shadow-[0_0_24px_rgba(56,189,248,0.2)] hover:bg-sky-400/35";
  return "border-0 bg-violet-400/25 font-semibold text-violet-50 hover:bg-violet-400/35";
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
      ? "text-emerald-200 hover:text-emerald-100"
      : variant === "dropoff"
        ? "text-rose-200 hover:text-rose-100"
        : "text-amber-200 hover:text-amber-100";

  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <button
          type="button"
          className={cn(
            "grid h-7 w-7 place-items-center rounded-md border-0 bg-transparent transition-colors hover:bg-transparent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 md:h-8 md:w-8",
            tones,
          )}
          aria-label={tooltipTitle}
        >
          {icon}
        </button>
      </TooltipTrigger>
      <TooltipContent
        sideOffset={6}
        className="max-w-[360px] flex flex-col items-start gap-0 border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
      >
        <div className="space-y-2">
          <div className="text-lg font-semibold leading-snug text-white">{tooltipTitle}</div>
          {tooltipDescription ? (
            <div className="text-base font-semibold leading-snug text-white/90">{tooltipDescription}</div>
          ) : null}
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

function DriverHover({ ride }: { ride: Ride }) {
  const name = [ride.driver?.firstName, ride.driver?.lastName].filter(Boolean).join(" ").trim() || "—";
  const phone = ride.driver?.phoneNumber || "";
  const telHref = phone ? `tel:${phone}` : null;

  return (
    <HoverCard openDelay={150} closeDelay={80}>
      <HoverCardTrigger asChild>
        <span className={cn("inline-flex items-center gap-1.5", phone ? "cursor-pointer text-white" : "text-zinc-100")}>
          <span className="font-semibold">{name}</span>  
        
        </span>
      </HoverCardTrigger>
      <HoverCardContent
        sideOffset={6}
        className="w-[min(100vw-2rem,360px)] max-w-[360px] border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)] ring-0 outline-none"
      >
        <div className="space-y-2">
          <div className="text-lg font-semibold text-white">{name}</div>
          {telHref ? (
            <a
              className="inline-flex items-center gap-2 text-base font-semibold text-white/90 hover:text-white"
              href={telHref}
            >
              <Phone className="h-5 w-5 shrink-0 opacity-90" />
              <span className="break-all">{phone}</span>
            </a>
          ) : (
            <div className="text-base font-medium text-white/85">—</div>
          )}
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
    <Card className="border-0 bg-white/[0.09] shadow-[0_8px_36px_rgba(0,0,0,0.28)] ring-1 ring-white/[0.14]">
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
                  <div className="min-w-0 w-full text-xl font-bold tracking-tight text-white sm:w-auto sm:flex-1 sm:text-2xl">
                    <div className="flex flex-col gap-1.5 sm:flex-row sm:min-w-0 sm:items-center sm:gap-2">
                      <span className="min-w-0 break-words leading-snug text-white drop-shadow-[0_1px_12px_rgba(255,255,255,0.08)]">
                        {pickup?.description?.split(",")[0] ?? "Pickup"}
                      </span>
                      <ArrowRight
                        className="hidden h-5 w-5 shrink-0 text-sky-300/90 sm:block"
                        aria-hidden
                      />
                      <span className="flex min-w-0 items-start gap-2 leading-snug sm:contents">
                        <ArrowRight className="mt-0.5 h-5 w-5 shrink-0 text-sky-300/90 sm:hidden" aria-hidden />
                        <span className="min-w-0 break-words text-white">
                          {dropoff?.description?.split(",")[0] ?? "Dropoff"}
                        </span>
                      </span>
                    </div>
                  </div>
                  <Badge
                    className={cn(
                      "h-10 shrink-0 self-start rounded-full px-3 text-sm sm:self-center",
                      rideStateBadgeClasses(ride.state),
                    )}
                  >
                    {rideStateLabel(ride.state)}
                  </Badge>
                </div>

                {midStops.length ? (
                  <div className="flex flex-wrap items-start gap-2">
                    <Tooltip>
                      <TooltipTrigger asChild>
                        <Badge
                          variant="ghost"
                          className="h-7 min-h-7 cursor-pointer rounded-md border-0 bg-transparent px-2 text-sm font-medium text-amber-200 shadow-none ring-0 hover:bg-transparent hover:text-amber-100 focus-visible:ring-2 focus-visible:ring-white/30"
                        >
                          Stops +{midStops.length}
                        </Badge>
                      </TooltipTrigger>
                      <TooltipContent
                        sideOffset={6}
                        className="max-w-[360px] flex flex-col items-start gap-0 border-0 bg-zinc-900 p-3 text-base text-white shadow-[0_12px_50px_rgba(0,0,0,0.75)]"
                      >
                        <div className="space-y-4">
                          {midStopsSorted.map((s) => (
                            <div key={s.id} className="space-y-2">
                              <div className="text-lg font-semibold leading-snug text-white">
                                {formatDateTime(bestStopTimestamp(s))}
                              </div>
                              <div className="text-base font-semibold leading-snug text-white/90">{s.description}</div>
                            </div>
                          ))}
                        </div>
                      </TooltipContent>
                    </Tooltip>
                  </div>
                ) : null}

                <div className="grid gap-2 text-base font-medium text-zinc-100 sm:grid-cols-[1fr_auto] sm:items-center">
                  <div className="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <div className="inline-flex items-center gap-2">
                      <span className="font-semibold text-zinc-200">Driver : </span>
                      <DriverHover ride={ride} />
                    </div>
                    <div className="inline-flex items-center gap-2">
                      <span className="font-semibold text-zinc-200">Vehicle type : </span>
                      <span className="font-semibold text-white">{vehicleLabel}</span>
                    </div>
                    {/* {ride.payment?.paymentMethod?.name ? (
                      <div className="inline-flex items-center gap-2">
                        <span className="text-zinc-500">Payment</span>
                        <span className="text-zinc-200 text-sm">{ride.payment.paymentMethod.name}</span>
                      </div>
                    ) : null} */}
                  </div>

                  <div className="text-right text-xl font-bold tracking-tight text-white sm:text-2xl">
                    {formatMoney(ride.priceAmount, ride.priceCurrency)}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="hidden shrink-0 flex-col items-end gap-2 sm:flex">
            <div className="text-sm font-semibold text-zinc-100">{formatDateTime(ride.createdAt)}</div>
            <div className="flex items-center gap-2">
              <Button asChild variant="ghost" className="h-9 px-2 text-base font-semibold text-sky-300 hover:bg-white/10 hover:text-sky-200">
                <a href={stripeUrl ?? "#"} target="_blank" rel="noreferrer" aria-disabled={!stripeUrl}>
                  <span>Open Payment</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-90" />
                </a>
              </Button>
              <Button asChild variant="ghost" className="h-9 px-2 text-base font-semibold text-white hover:bg-white/10 hover:text-white">
                <a href={bookingUrl} target="_blank" rel="noreferrer">
                  <span>Open Booking</span>
                  <ExternalLink className="ml-2 h-4 w-4 opacity-80" />
                </a>
              </Button>
            </div>
          </div>
        </div>

        <div className="mt-3 flex items-center justify-between gap-3 sm:hidden">
          <div className="text-sm font-semibold text-zinc-100">{formatDateTime(ride.createdAt)}</div>
          <div className="flex items-center gap-2">
            <Button asChild size="sm" variant="ghost" className="h-9 px-2 text-sm font-semibold text-sky-300 hover:bg-white/10 hover:text-sky-200">
              <a href={stripeUrl ?? "#"} target="_blank" rel="noreferrer" aria-disabled={!stripeUrl}>
                Open Payment
              </a>
            </Button>
            <Button asChild size="sm" variant="ghost" className="h-9 px-2 text-sm font-semibold text-white hover:bg-white/10">
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
      <div className="min-h-dvh bg-[#12151F] text-zinc-50">
        <div className="w-full px-4 py-5 text-base sm:px-8 sm:py-7 sm:text-[18px]">
          <div className="sticky top-0 z-20 -mx-4 px-4 pb-3 pt-2 backdrop-blur sm:-mx-8 sm:px-8">
            <div className="relative overflow-hidden rounded-3xl bg-gradient-to-b from-white/[0.16] to-white/[0.07] p-5 ring-1 ring-white/[0.18] shadow-[0_12px_48px_rgba(0,0,0,0.45)] sm:p-6">
              <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(620px_circle_at_18%_0%,rgba(56,189,248,0.28),transparent_58%),radial-gradient(520px_circle_at_88%_18%,rgba(52,211,153,0.2),transparent_55%),radial-gradient(400px_circle_at_50%_100%,rgba(167,139,250,0.12),transparent_50%)]" />
              <div className="relative flex items-start justify-between gap-4">
                <div className="min-w-0">
                  <div className="text-sm font-bold uppercase tracking-[0.12em] text-sky-300">Ride history</div>
                  <div className="mt-2 truncate text-3xl font-extrabold tracking-tight text-white drop-shadow-sm sm:text-4xl">
                    {MOCK_CLIENT.name}
                  </div>
                  <div className="mt-1.5 text-base font-semibold text-zinc-100">Customer details</div>
                </div>
                <div className="text-right">

                  <div className="mt-2 inline-flex items-center rounded-full bg-sky-400/25 px-3.5 py-1.5 text-lg font-bold text-white ring-1 ring-sky-300/45 shadow-[0_0_28px_rgba(56,189,248,0.25)] sm:text-xl">
                    {formatMoney(MOCK_CLIENT.lifetimeSpendCad, "CAD")}
                  </div>
                  <div className="mt-1 text-sm font-semibold text-zinc-200">lifetime spend</div>
                </div>
              </div>
            </div>
          </div>

          <div className="mt-3 space-y-3 sm:mt-4">
            {rides.map((ride) => (
              <RideRow key={ride.id} ride={ride} />
            ))}
          </div>

          <div className="mt-6 text-center text-sm font-medium text-zinc-300">
            Mock data only. Links are placeholders until API + real URLs are wired.
          </div>
        </div>
      </div>
    </TooltipProvider>
  );
}
