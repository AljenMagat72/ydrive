"use client";

import * as React from "react";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { CustomerHero } from "@/components/CustomerHero";
import { RideRow } from "@/components/RideRow";
import type { DerivedCustomer, Ride } from "@/lib/types";
import type { ServiceNameById } from "@/lib/rides";

export type RideHistoryShellProps = Readonly<{
  embedded: boolean;
  derived: DerivedCustomer | null;
  hasContext: boolean;
  heroLoading: boolean;
  ridesStatusNode: React.ReactNode;
  beforeRideList?: React.ReactNode;
  rides: Ride[];
  serviceNamesById: ServiceNameById;
  adminKey: string;
  onLoadMore: () => void;
  loadMoreDisabled: boolean;
  ridesLoading: boolean;
  hasMore: boolean;
}>;

export function RideHistoryShell({
  embedded,
  derived,
  hasContext,
  heroLoading,
  ridesStatusNode,
  beforeRideList,
  rides,
  serviceNamesById,
  adminKey,
  onLoadMore,
  loadMoreDisabled,
  ridesLoading,
  hasMore,
}: RideHistoryShellProps) {
  return (
    <div className="min-h-dvh bg-widget-app text-zinc-50">
      <div className="w-full px-4 py-4 text-base sm:px-8 sm:py-6 sm:text-lg">
        <CustomerHero embedded={embedded} derived={derived} hasContext={hasContext} loading={heroLoading} />

        {ridesStatusNode}

        {beforeRideList}

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
            disabled={loadMoreDisabled}
            onClick={onLoadMore}
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
