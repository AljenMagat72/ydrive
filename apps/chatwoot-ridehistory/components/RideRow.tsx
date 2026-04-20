"use client";

import * as React from "react";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { Dialog, DialogContent, DialogTitle } from "@/components/ui/dialog";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Separator } from "@/components/ui/separator";
import { cn } from "@/lib/utils";
import type { Ride } from "@/lib/types";
import { formatDateTime, formatMoney, formatPickupPrebookWindow, formatRideCardDateParts, formatTimeOnly, rideCompletedAtIso } from "@/lib/format";
import { getDropoffStop, getIntermediateStops, getPickupStop, midStopTimeRows, normalizeRideState } from "@/lib/rides";
import { CalendarDays, Car, ExternalLink, FlagTriangleRight, Info, MapPin, Phone, User, Wallet } from "lucide-react";

function rideStateBadge(state: string): { label: string; variant: React.ComponentProps<typeof Badge>["variant"]; className?: string } {
  const s = normalizeRideState(state);
  if (s === "completed") return { label: "Completed", variant: "secondary", className: "bg-emerald-500/15 text-emerald-200" };
  if (s === "canceled") return { label: "Canceled", variant: "secondary", className: "bg-amber-500/15 text-amber-200" };
  if (!s) return { label: "—", variant: "secondary", className: "bg-white/10 text-zinc-100" };
  const label = s.replace(/[_-]+/g, " ").replace(/\b\w/g, (m) => m.toUpperCase());
  return { label, variant: "secondary", className: "bg-white/10 text-zinc-100" };
}

function dropoffStatusRow(ride: Ride): { label: string; time: string } {
  const s = normalizeRideState(ride.state);
  if (s === "canceled") return { label: "Ride Canceled", time: formatDateTime(rideCompletedAtIso(ride)) };
  if (s === "completed") return { label: "Ride completed", time: formatDateTime(rideCompletedAtIso(ride)) };
  return { label: `Ride ${s}`, time: formatDateTime(rideCompletedAtIso(ride)) };
}

function buildAutofleetBookingUrl(ride: Ride) {
  return `https://control.autofleet.io/6FnkvuL1DSM3pe847fDhCX/ride/${ride.id}`;
}

function DriverPopover({ ride }: { ride: Ride }) {
  const name = [ride.driver?.firstName, ride.driver?.lastName].filter(Boolean).join(" ").trim() || "—";
  const phone = ride.driver?.phoneNumber || "";
  const telHref = phone ? `tel:${phone}` : null;

  return (
    <Popover>
      <PopoverTrigger asChild>
        <button type="button" className={cn("inline-flex items-center gap-1.5 rounded-md p-0 text-left", phone ? "cursor-pointer text-white" : "text-zinc-100")}>
          <span className="font-base">{name}</span>
        </button>
      </PopoverTrigger>
      <PopoverContent sideOffset={8} align="start" className="w-[min(100vw-2rem,380px)] max-w-[380px] border-0 bg-zinc-900 p-3 text-sm text-white shadow-widget-popover outline-none sm:p-4 sm:text-base">
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
        </div>
      </PopoverContent>
    </Popover>
  );
}

export function RideRow({ ride, serviceName, adminKey }: { ride: Ride; serviceName: string | null; adminKey: string }) {
  const pickup = getPickupStop(ride);
  const dropoff = getDropoffStop(ride);
  const midStops = getIntermediateStops(ride);

  const [priceOpen, setPriceOpen] = React.useState(false);

  const paymentId = ride.payment?.id ?? "";
  const [stripeUrl, setStripeUrl] = React.useState<string | null>(null);
  const [stripeLoading, setStripeLoading] = React.useState(false);

  React.useEffect(() => {
    setStripeUrl(null);
  }, [paymentId]);

  const resolveStripeUrl = React.useCallback(async () => {
    if (!paymentId || stripeLoading) return null;
    if (!adminKey) return null;

    setStripeLoading(true);
    try {
      const qs = new URLSearchParams({ "admin-key": adminKey, paymentId });
      const res = await fetch(`/api/stripe/payment-dashboard-url?${qs.toString()}`, {
        method: "GET",
        headers: { Accept: "application/json" },
      });
      const data = (await res.json().catch(() => ({}))) as { url?: string };
      const url = res.ok && typeof data.url === "string" ? data.url : null;
      if (url) setStripeUrl(url);
      return url;
    } finally {
      setStripeLoading(false);
    }
  }, [adminKey, paymentId, stripeLoading]);

  const onOpenPaymentClick = React.useCallback(async () => {
    if (stripeUrl) {
      window.open(stripeUrl, "_blank", "noreferrer");
      return;
    }
    const url = await resolveStripeUrl();
    if (url) window.open(url, "_blank", "noreferrer");
  }, [resolveStripeUrl, stripeUrl]);

  const bookingUrl = buildAutofleetBookingUrl(ride);
  const bookedServiceLabel = serviceName || "—";
  const pickupShort = pickup?.description?.split(",")[0] ?? "Pickup";
  const dropoffShort = dropoff?.description?.split(",")[0] ?? "Dropoff";
  const dateParts = formatRideCardDateParts(ride.createdAt);
  const pickupPrebookWindow = pickup ? formatPickupPrebookWindow(pickup.afterTime ?? null, pickup.beforeTime ?? null) : null;
  const stateBadge = rideStateBadge(ride.state ?? "");
  const dropoffStatus = dropoffStatusRow(ride);

  return (
    <Card className="overflow-hidden rounded-2xl border-0 bg-widget-panel shadow-widget-card ring-1 ring-widget-ring">
      <div className="p-4 sm:p-5">
        <div className="flex flex-col gap-5 lg:flex-row lg:items-stretch lg:gap-6">
          <div className="flex shrink-0 gap-3 border-b border-white/10 pb-4 lg:w-[112px] lg:flex-col lg:gap-2.5 lg:border-b-0 lg:border-r lg:border-white/10 lg:pb-0 lg:pr-5">
            <div className="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-widget-tile ring-1 ring-sky-500/35" aria-hidden>
              <CalendarDays className="h-4 w-4 text-sky-400" strokeWidth={1.75} />
            </div>
            <div className="w-full justify-between flex flex-row sm:flex-col gap-1.5">
              {dateParts ? (
                <>
                  <div className="text-widget-date font-bold leading-none tracking-tight text-white">{dateParts.monthDay}</div>
                  <div className="text-widget-meta font-normal leading-tight text-zinc-400">{dateParts.year}</div>
                </>
              ) : (
                <div className="text-sm text-zinc-400">—</div>
              )}
              <div className="h-px w-11 max-w-full bg-white/20" />
              <div className="text-widget-meta font-normal leading-tight text-zinc-400">{formatTimeOnly(ride.createdAt)}</div>
              <div className="pt-1">
                <Badge variant={stateBadge.variant} className={cn("h-6 px-2.5 text-xs font-semibold", stateBadge.className)}>
                  {stateBadge.label}
                </Badge>
              </div>
            </div>
          </div>

          <div className="min-w-0 flex-1">
            <div className="space-y-3">
              <div className="flex items-center gap-3">
                {pickup ? (
                  <Popover>
                    <PopoverTrigger asChild>
                      <button type="button" className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-emerald-500/55 bg-widget-route-icon text-emerald-400 transition hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/40" aria-label="Pickup details">
                        <MapPin className="h-4 w-4" strokeWidth={2.25} />
                      </button>
                    </PopoverTrigger>
                    <PopoverContent side="bottom" align="start" sideOffset={8} className="w-[min(100vw-2rem,420px)] max-w-[420px] border-0 bg-zinc-900 p-3 text-sm text-white shadow-widget-popover">
                      <div className="space-y-2">
                        <div className="text-base font-semibold text-white">Pickup</div>
                        <div className="text-white/90">{pickup.description}</div>
                        <div className="grid gap-1 text-xs text-white/75">
                          <div className="flex justify-between gap-4">
                            <span>Booking time</span>
                            <span className="text-right font-semibold text-white">{formatDateTime(ride.createdAt)}</span>
                          </div>
                          {pickupPrebookWindow ? (
                            <div className="flex justify-between gap-4">
                              <span>Prebooked time</span>
                              <span className="text-right font-semibold tabular-nums text-white">{pickupPrebookWindow}</span>
                            </div>
                          ) : null}
                          <div className="flex justify-between gap-4">
                            <span>Arrived time</span>
                            <span className="text-right font-semibold text-white">{formatDateTime(pickup.arrivedAt ?? null)}</span>
                          </div>
                          <div className="flex justify-between gap-4">
                            <span>On board time</span>
                            <span className="text-right font-semibold text-white">{formatDateTime(pickup.completedAt ?? null)}</span>
                          </div>
                        </div>
                      </div>
                    </PopoverContent>
                  </Popover>
                ) : (
                  <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/15 bg-widget-route-icon text-zinc-500">
                    <MapPin className="h-4 w-4" strokeWidth={2.25} />
                  </div>
                )}

                <>
                  <div className="h-px flex-1 border-t border-dashed border-white/25" aria-hidden />
                  {midStops.map((s) => (
                    <React.Fragment key={s.id}>
                      <Popover>
                        <PopoverTrigger asChild>
                          <button type="button" className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-amber-500/45 bg-widget-route-icon text-amber-400 transition hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/40" aria-label="Stop details">
                            <MapPin className="h-3.5 w-3.5" strokeWidth={2.25} />
                          </button>
                        </PopoverTrigger>
                        <PopoverContent side="bottom" align="start" sideOffset={8} className="w-[min(100vw-2rem,420px)] max-w-[420px] border-0 bg-zinc-900 p-3 text-sm text-white shadow-widget-popover">
                          <div className="space-y-2">
                            <div className="text-base font-semibold text-white">Stop</div>
                            <div className="text-white/90">{s.description}</div>
                            {midStopTimeRows(s).length > 0 ? (
                              <div className="grid gap-1 border-t border-white/10 pt-2 text-xs text-white/75">
                                {midStopTimeRows(s).map((r) => (
                                  <div key={r.label} className="flex justify-between gap-4">
                                    <span>{r.label}</span>
                                    <span className="text-right font-semibold text-white">{r.value}</span>
                                  </div>
                                ))}
                              </div>
                            ) : null}
                          </div>
                        </PopoverContent>
                      </Popover>
                      <div className="h-px flex-1 border-t border-dashed border-white/25" aria-hidden />
                    </React.Fragment>
                  ))}
                </>

                {dropoff ? (
                  <Popover>
                    <PopoverTrigger asChild>
                      <button type="button" className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-rose-500/55 bg-widget-route-icon text-rose-400 transition hover:bg-white/5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-400/40" aria-label="Dropoff details">
                        <FlagTriangleRight className="h-4 w-4" strokeWidth={2.25} />
                      </button>
                    </PopoverTrigger>
                    <PopoverContent side="bottom" align="start" sideOffset={8} className="w-[min(100vw-2rem,420px)] max-w-[420px] border-0 bg-zinc-900 p-3 text-sm text-white shadow-widget-popover">
                      <div className="space-y-2">
                        <div className="text-base font-semibold text-white">Dropoff</div>
                        <div className="text-white/90">{dropoff.description}</div>
                        <div className="border-t border-white/10 pt-2 text-xs">
                          <div className="flex justify-between gap-4 text-white/75">
                            <span>{dropoffStatus.label}</span>
                            <span className="font-semibold text-white">{dropoffStatus.time}</span>
                          </div>
                          {normalizeRideState(ride.state) === "canceled" && ride.priceAmount > 0 ? (
                            <div className="mt-1 flex justify-between gap-4 text-white/75">
                              <span>Cancellation fee</span>
                              <span className="font-semibold text-white">{formatMoney(ride.priceAmount, ride.priceCurrency)}</span>
                            </div>
                          ) : null}
                        </div>
                      </div>
                    </PopoverContent>
                  </Popover>
                ) : (
                  <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/15 bg-widget-route-icon text-zinc-500">
                    <FlagTriangleRight className="h-4 w-4" strokeWidth={2.25} />
                  </div>
                )}
              </div>

              <div className="flex min-w-0 items-center justify-between gap-4">
                <div className="min-w-0 text-base font-bold leading-snug text-white sm:text-lg">{pickupShort}</div>
                <div className="min-w-0 text-right text-base font-bold leading-snug text-white sm:text-lg">{dropoffShort}</div>
              </div>

              <div className="mt-4 flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1 text-widget-meta text-zinc-400 sm:text-sm">
                <span className="inline-flex items-center gap-1.5">
                  <User className="h-3.5 w-3.5 shrink-0 text-zinc-500" aria-hidden />
                  <span className="text-zinc-500">Driver:</span>
                  <span className="min-w-0 font-medium text-zinc-300">
                    <DriverPopover ride={ride} />
                  </span>
                </span>
                <span className="text-zinc-600" aria-hidden>
                  ·
                </span>
                <span className="inline-flex min-w-0 items-center gap-1.5">
                  <Car className="h-3.5 w-3.5 shrink-0 text-zinc-500" aria-hidden />
                  <span className="text-zinc-500">Type:</span>
                  <span className="font-medium text-zinc-300">{bookedServiceLabel}</span>
                </span>
              </div>
            </div>
          </div>

          <div className="flex min-w-0 justify-between shrink-0 flex-col gap-3 border-t border-white/10 pt-4 lg:min-w-[240px] lg:w-[min(100%,280px)] lg:border-t-0 lg:pt-0">
            <div className="flex w-full items-start justify-between gap-3">
              <div className="ml-auto flex items-center gap-1.5">
                <div className="text-right text-2xl font-bold tabular-nums tracking-tight text-white sm:text-3xl">{formatMoney(ride.priceAmount, ride.priceCurrency)}</div>
                <button type="button" className="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-white/10 hover:text-zinc-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-widget-surface-25" onClick={() => setPriceOpen((o) => !o)} aria-label="Price details">
                  <Info className="h-4 w-4" strokeWidth={2} />
                </button>
              </div>
            </div>

            <div className="flex w-full min-w-0 flex-col gap-3 sm:flex-row sm:justify-end sm:gap-3">
              {stripeUrl ? (
                <a
                  href={stripeUrl}
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex h-auto min-h-12 w-full min-w-0 shrink items-center justify-center gap-2 whitespace-normal rounded-sm border border-white/15 bg-widget-action-muted px-4 py-3 text-center text-base font-medium leading-tight text-zinc-300 shadow-none hover:bg-widget-surface-6 hover:text-zinc-100 sm:min-h-10 sm:w-auto sm:min-w-40 sm:px-4 sm:py-2 sm:text-sm sm:leading-none sm:whitespace-nowrap"
                >
                  <Wallet className="h-5 w-5 shrink-0 opacity-90 sm:h-4 sm:w-4" aria-hidden />
                  <span>Open Payment</span>
                </a>
              ) : (
                <button
                  type="button"
                  onClick={onOpenPaymentClick}
                  disabled={!adminKey || stripeLoading}
                  className="inline-flex h-auto min-h-12 w-full min-w-0 shrink items-center justify-center gap-2 whitespace-normal rounded-sm border border-white/15 bg-widget-action-muted px-4 py-3 text-center text-base font-medium leading-tight text-zinc-300 shadow-none hover:bg-widget-surface-6 hover:text-zinc-100 disabled:pointer-events-none disabled:opacity-45 sm:min-h-10 sm:w-auto sm:min-w-40 sm:px-4 sm:py-2 sm:text-sm sm:leading-none sm:whitespace-nowrap"
                >
                  <Wallet className="h-5 w-5 shrink-0 opacity-90 sm:h-4 sm:w-4" aria-hidden />
                  <span>{stripeLoading ? "Loading…" : "Open Payment"}</span>
                </button>
              )}

              <a
                href={bookingUrl}
                target="_blank"
                rel="noreferrer"
                className="inline-flex h-auto min-h-12 w-full min-w-0 shrink items-center justify-center gap-2 whitespace-normal rounded-sm border border-transparent bg-blue-500 px-4 py-3 text-center text-base font-semibold leading-tight text-white shadow-sm hover:bg-blue-400 sm:min-h-10 sm:w-auto sm:min-w-40 sm:px-4 sm:py-2 sm:text-sm sm:leading-none sm:whitespace-nowrap"
              >
                <CalendarDays className="h-5 w-5 shrink-0 opacity-95 sm:h-4 sm:w-4" aria-hidden />
                <span className="min-w-0">Open Booking</span>
                <ExternalLink className="h-4 w-4 shrink-0 opacity-90 sm:h-3.5 sm:w-3.5" aria-hidden />
              </a>
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
              <div className="font-extrabold text-white">{formatMoney(ride.paymentBreakdown?.preAuth ?? 0, ride.priceCurrency)}</div>
            </div>
            <div className="flex items-center justify-between gap-4">
              <div className="font-semibold text-white/80">Captured</div>
              <div className="font-extrabold text-white">{formatMoney(ride.paymentBreakdown?.captured ?? 0, ride.priceCurrency)}</div>
            </div>
            <div className="flex items-center justify-between gap-4">
              <div className="font-semibold text-white/80">Refunded</div>
              <div className="font-extrabold text-white">{formatMoney(ride.paymentBreakdown?.refunded ?? 0, ride.priceCurrency)}</div>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </Card>
  );
}

