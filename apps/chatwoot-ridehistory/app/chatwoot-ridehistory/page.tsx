"use client";

import * as React from "react";
import { Suspense } from "react";
import { useSearchParams } from "next/navigation";
import { ClientMatchPicker } from "@/components/ClientMatchPicker";
import { RideHistoryShell } from "@/components/RideHistoryShell";
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
    <RideHistoryShell
      embedded={embedded}
      derived={derived}
      hasContext={hasContext}
      heroLoading={heroLoading}
      ridesStatusNode={ridesStatusNode}
      beforeRideList={
        pendingMatches && pendingMatches.length > 0 ? (
          <ClientMatchPicker matches={pendingMatches} onSelect={selectClient} disabled={ridesLoading} />
        ) : null
      }
      rides={rides}
      serviceNamesById={serviceNamesById}
      adminKey={adminKey}
      onLoadMore={loadMore}
      loadMoreDisabled={
        ridesLoading || !!ridesError || !hasMore || (pendingMatches?.length ?? 0) > 0
      }
      ridesLoading={ridesLoading}
      hasMore={hasMore}
    />
  );
}

export default function ChatwootRideHistoryPage() {
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
