"use client";

import Script from "next/script";
import { useMemo, useState } from "react";
import { useSearchParams } from "next/navigation";
import { RideHistoryShell } from "@/components/RideHistoryShell";
import { useClientRidesById, useRideServices } from "@/lib/rides";
import type { Customer, DerivedCustomer } from "@/lib/types";

type ZohoRecordResponse = { data?: Array<Record<string, unknown>> };

function asZohoRecordResponse(v: unknown): ZohoRecordResponse | null {
  if (!v || typeof v !== "object") return null;
  return v as ZohoRecordResponse;
}

function customerFromZohoRecord(record: unknown): Customer | null {
  const parsed = asZohoRecordResponse(record);
  const row = parsed?.data?.[0];
  if (!row) return null;
  const identifier = String(row["AutoFleet_ID"] ?? "").trim();
  return {
    name: String(row["Name"] ?? "").trim(),
    email: String(row["Email"] ?? "").trim(),
    phone: String(row["Phone_1"] ?? row["Phone"] ?? "").trim(),
    identifier,
  };
}

function derivedFromZohoCustomer(customer: Customer | null): DerivedCustomer | null {
  if (!customer) return null;
  return {
    displayName: customer.name,
    email: customer.email || null,
    phone: customer.phone || null,
    // Zoho's AutoFleet_ID is the AutoFleet "clientId" used by our Laravel backend.
    identifier: customer.identifier || null,
    contactId: undefined,
    conversationId: undefined,
    inboxId: undefined,
    thumbnail: null,
    company: null,
    description: null,
    availabilityStatus: null,
    channel: null,
    conversationStatus: null,
    labels: [],
    agentName: null,
    agentEmail: null,
    socialProfiles: null,
    mergedCustomAttributes: {},
  };
}

export default function ZohoRideHistory() {
  const searchParams = useSearchParams();
  const adminKey = searchParams.get("admin-key")?.trim() ?? "";
  const overrideClientId = searchParams.get("clientId")?.trim() ?? "";

  const [customer, setCustomer] = useState<Customer | null>(null);
  const derived = useMemo(() => derivedFromZohoCustomer(customer), [customer]);
  const hasContext = Boolean(customer);
  const heroLoading = !hasContext;

  const clientId = overrideClientId || customer?.identifier || "";
  const hasClientId = Boolean(clientId.trim());

  // This page is Zoho-driven: the profile comes from Zoho's record payload (not Chatwoot).
  // We then use AutoFleet_ID as the `clientId` to fetch rides from our backend.
  const { rides, loading: ridesLoading, error: ridesError, hasMore, loadMore } = useClientRidesById(clientId, {
    adminKey,
    enabled: hasClientId,
  });

  const { namesById: serviceNamesById } = useRideServices(rides, {
    adminKey,
    enabled: Boolean(adminKey) && hasClientId,
  });

  let ridesStatusNode: React.ReactNode = null;
  if (!adminKey) {
    ridesStatusNode = (
      <div className="mt-6 rounded-xl border border-rose-500/30 bg-rose-950/35 px-4 py-3 text-center text-sm text-rose-100">
        Missing admin key: add <span className="font-mono">?admin-key=…</span> to the URL.
      </div>
    );
  } else if (hasContext && !hasClientId) {
    ridesStatusNode = (
      <div className="mt-6 rounded-xl border border-rose-500/30 bg-rose-950/35 px-4 py-3 text-center text-sm text-rose-100">
        Zoho record is missing <span className="font-mono">AutoFleet_ID</span>.
      </div>
    );
  } else if (ridesLoading) {
    ridesStatusNode = <div className="mt-6 text-center text-sm font-medium text-zinc-400">Loading rides…</div>;
  } else if (ridesError) {
    ridesStatusNode = (
      <div className="mt-6 rounded-xl border border-rose-500/30 bg-rose-950/35 px-4 py-3 text-center text-sm text-rose-100">
        {ridesError}
      </div>
    );
  }

  return (
    <>
      <Script
        src="https://live.zwidgets.com/js-sdk/1.4/ZohoEmbededAppSDK.min.js"
        strategy="afterInteractive"
        onLoad={() => {
          const Z = (window as unknown as { ZOHO?: unknown }).ZOHO as
            | {
                embeddedApp?: { on?: (event: string, cb: (data: unknown) => void) => void; init?: () => void };
                CRM?: { API?: { getRecord?: (args: { Entity: unknown; RecordID: unknown }) => Promise<unknown> } };
              }
            | undefined;
          if (!Z?.embeddedApp?.on) return;

          Z.embeddedApp.on("PageLoad", function (data: unknown) {
            const payload = data as { Entity?: unknown; EntityId?: unknown };
            Z.CRM?.API?.getRecord?.({
              Entity: payload.Entity,
              RecordID: payload.EntityId,
            }).then(function (record: unknown) {
              // Zoho is the source of truth for the customer profile on this page.
              console.log("zoho_record", record);
              setCustomer(customerFromZohoRecord(record));
              console.log("autofleet_id", customerFromZohoRecord(record)?.identifier);
            });
          });

          Z.embeddedApp.init?.();
        }}
      />

      <RideHistoryShell
        embedded={true}
        derived={derived}
        hasContext={hasContext}
        heroLoading={heroLoading}
        ridesStatusNode={ridesStatusNode}
        rides={rides}
        serviceNamesById={serviceNamesById}
        adminKey={adminKey}
        onLoadMore={loadMore}
        loadMoreDisabled={!hasClientId || !adminKey || ridesLoading || !!ridesError || !hasMore}
        ridesLoading={ridesLoading}
        hasMore={hasMore}
      />
    </>
  );
}