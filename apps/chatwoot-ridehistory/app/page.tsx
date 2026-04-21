"use client";

import * as React from "react";
import { Suspense } from "react";
import { useSearchParams } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Loader2 } from "lucide-react";
import { ClientMatchPicker } from "@/components/ClientMatchPicker";
import { CustomerHero } from "@/components/CustomerHero";
import { RideRow } from "@/components/RideRow";
import { deriveCustomer, useChatwootAppContext } from "@/lib/chatwoot";
import { useClientRides, useRideServices } from "@/lib/rides";

function HomeContent() {
  const searchParams = useSearchParams();
  const adminKey = searchParams.get("admin-key")?.trim() ?? "";

  const { ctx, embedded } = useChatwootAppContext();
  const derived = React.useMemo(() => deriveCustomer(ctx), [ctx]);
  const hasContext = Boolean(ctx?.data);
  const heroLoading = embedded && !hasContext;

  const {
    rides,
    loading: ridesLoading,
    error: ridesError,
    hasMore,
    loadMore,
    pendingMatches,
    selectClient,
  } = useClientRides(derived, {
    embedded,
    hasContext,
    adminKey,
  });

  const { namesById: serviceNamesById } = useRideServices(rides, {
    adminKey,
    enabled: embedded ? hasContext : true,
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
    <div className="min-h-dvh bg-widget-app text-zinc-50">
      <div className="w-full px-4 py-4 text-base sm:px-8 sm:py-6 sm:text-lg">
        <CustomerHero
          embedded={embedded}
          derived={derived}
          hasContext={hasContext}
          loading={heroLoading}
        />

        {ridesStatusNode}

        {pendingMatches && pendingMatches.length > 0 ? (
          <ClientMatchPicker matches={pendingMatches} onSelect={selectClient} disabled={ridesLoading} />
        ) : null}

        <div className="mt-3 space-y-3 sm:mt-4">
          {rides.map((ride) => (
            <RideRow
              key={ride.id}
              ride={ride}
              serviceName={ride.serviceId ? serviceNamesById[String(ride.serviceId)] ?? null : null}
              adminKey={adminKey}
            />
          ))}
        </div>

        <div className="mt-4 flex justify-center">
          <Button
            type="button"
            variant="ghost"
            className="cursor-pointer h-10 px-4 text-base font-semibold text-white disabled:opacity-50"
            disabled={ridesLoading || !!ridesError || !hasMore || (pendingMatches?.length ?? 0) > 0}
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
  );
}

export default function Home() {
  return (
    <Suspense
      fallback={
        <div className="flex min-h-dvh items-center justify-center bg-widget-app text-sm text-zinc-400">
          Loading…
        </div>
      }
    >
      <HomeContent />
    </Suspense>
  );
}
