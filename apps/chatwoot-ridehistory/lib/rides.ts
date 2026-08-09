import * as React from "react";
import type { ClientMatch, Ride, StopPoint } from "@/lib/types";
import { formatDateTime } from "@/lib/format";

function extractClientIdFromRidesPayload(data: unknown): string | null {
  if (data && typeof data === "object" && !Array.isArray(data)) {
    const id = (data as Record<string, unknown>).clientId;
    return typeof id === "string" && id ? id : null;
  }
  return null;
}

function parseNeedsSelectionPayload(data: unknown): ClientMatch[] | null {
  if (!data || typeof data !== "object") return null;
  const o = data as Record<string, unknown>;
  if (o.needsSelection !== true) return null;
  return (o.matches as ClientMatch[]) ?? null;
}

export function byOrder(a: StopPoint, b: StopPoint) {
  return (a.orderInParent ?? 0) - (b.orderInParent ?? 0);
}

export function normalizeRideState(state: string): string {
  return state.trim().toLowerCase();
}

export function getPickupStop(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  return sorted.find((s) => s.type === "pickup") ?? sorted[0] ?? null;
}

export function getDropoffStop(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  const drop = [...sorted].reverse().find((s) => s.type === "dropoff");
  return drop ?? sorted[sorted.length - 1] ?? null;
}

export function getIntermediateStops(ride: Ride) {
  const sorted = [...ride.stopPoints].sort(byOrder);
  if (sorted.length <= 2) return [];
  return sorted.slice(1, -1).filter((s) => typeof s.description === "string" && s.description.trim() !== "");
}

export function midStopTimeRows(stop: StopPoint): Array<{ label: string; value: string }> {
  const rows: Array<{ label: string; value: string }> = [];
  if (stop.arrivedAt) rows.push({ label: "Stop Time", value: formatDateTime(stop.arrivedAt) });
  if (stop.completedAt) rows.push({ label: "Complete Time", value: formatDateTime(stop.completedAt) });
  return rows;
}

function sortRidesByCreatedAtDesc(rides: Ride[]): Ride[] {
  return [...rides].sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
}

function mergeRidesPage(prev: Ride[], list: Ride[], pageNumber: number): Ride[] {
  const merged = pageNumber === 0 ? list : [...prev, ...list];
  return sortRidesByCreatedAtDesc(merged);
}

function normalizeRide(raw: unknown): Ride {
  if (!raw || typeof raw !== "object") return raw as Ride;
  const o = raw as Record<string, unknown>;
  let finalizedAt: string | null | undefined;
  if (o.finalizedAt === null) finalizedAt = null;
  else if (typeof o.finalizedAt === "string") finalizedAt = o.finalizedAt;
  else finalizedAt = undefined;
  const baseRide = o as unknown as Ride;
  const extra = finalizedAt === undefined ? {} : { finalizedAt };
  return { ...baseRide, ...extra };
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

export function useClientRides(
  derived: { displayName?: string; email?: string | null; phone?: string | null } | null,
  opts: { embedded: boolean; hasContext: boolean; adminKey: string },
) {
  const [rides, setRides] = React.useState<Ride[]>([]);
  const [loading, setLoading] = React.useState(false);
  const [error, setError] = React.useState<string | null>(null);
  const [pageNumber, setPageNumber] = React.useState(0);
  const [hasMore, setHasMore] = React.useState(false);
  const [pendingMatches, setPendingMatches] = React.useState<ClientMatch[] | null>(null);
  const [selectionNonce, setSelectionNonce] = React.useState(0);
  const clientIdRef = React.useRef<string | null>(null);

  const loadMore = React.useCallback(() => {
    setPageNumber((p) => p + 1);
  }, []);

  const selectClient = React.useCallback((id: string) => {
    const t = id.trim();
    if (!t) return;
    clientIdRef.current = t;
    setPendingMatches(null);
    setPageNumber(0);
    setSelectionNonce((n) => n + 1);
  }, []);

  React.useEffect(() => {
    setPageNumber(0);
    clientIdRef.current = null;
    setPendingMatches(null);
  }, [opts.embedded, opts.hasContext, opts.adminKey, derived?.displayName, derived?.email, derived?.phone]);

  React.useEffect(() => {
    if (opts.embedded && !opts.hasContext) {
      setRides([]);
      setError(null);
      setLoading(false);
      setHasMore(false);
      return;
    }

    const name = derived?.displayName?.trim() ?? "";
    const email = derived?.email?.trim() ?? "";
    const phone = (derived?.phone ?? "").replace(/\D+/g, "");
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
      setError("Missing admin key: add ?admin-key=… to the dashboard app URL (same origin as this widget).");
      setLoading(false);
      setHasMore(false);
      return;
    }

    if (pendingMatches !== null) {
      return;
    }

    let cancelled = false;
    setLoading(true);
    setError(null);

    const ridesUrl = "/api/rides";
    const pageSize = 25;

    const cid = clientIdRef.current;
    const body: Record<string, unknown> = { phone, name, email, pageNumber };
    if (cid) body.clientId = cid;

    fetch(ridesUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json", "X-Admin-Key": opts.adminKey },
      body: JSON.stringify(body),
    })
      .then(async (res) => {
        const data: unknown = await res.json().catch(() => ({}));
        if (!res.ok) {
          const msg =
            data && typeof data === "object" && "message" in data && typeof (data as { message?: unknown }).message === "string"
              ? (data as { message: string }).message
              : `Request failed (${res.status})`;
          throw new Error(msg);
        }
        const pick = parseNeedsSelectionPayload(data);
        if (pick) {
          clientIdRef.current = null;
          if (!cancelled) {
            setPendingMatches(pick);
            setRides([]);
            setHasMore(false);
          }
          return null;
        }

        const resolved = extractClientIdFromRidesPayload(data);
        if (resolved) clientIdRef.current = resolved;

        return normalizeRidesPayload(data);
      })
      .then((list) => {
        if (list === null) return;
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
    pendingMatches,
    selectionNonce,
  ]);

  return { rides, loading, error, hasMore, loadMore, pendingMatches, selectClient };
}

// ClientId-only rides loader (no Chatwoot → client lookup).
// Used when we already know the AutoFleet client id (e.g. from a Zoho record's AutoFleet_ID).
export function useClientRidesById(
  clientId: string | null | undefined,
  opts: { adminKey: string; enabled: boolean },
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
  }, [clientId, opts.adminKey, opts.enabled]);

  React.useEffect(() => {
    if (!opts.enabled) {
      setRides([]);
      setError(null);
      setLoading(false);
      setHasMore(false);
      return;
    }

    const cid = (clientId ?? "").trim();
    if (!cid) {
      setRides([]);
      setError(null);
      setLoading(false);
      setHasMore(false);
      return;
    }

    if (!opts.adminKey) {
      setRides([]);
      setError("Missing admin key: add ?admin-key=… to the dashboard app URL (same origin as this widget).");
      setLoading(false);
      setHasMore(false);
      return;
    }

    let cancelled = false;
    setLoading(true);
    setError(null);

    const ridesUrl = "/api/rides";
    const pageSize = 25;

    fetch(ridesUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json", "X-Admin-Key": opts.adminKey },
      body: JSON.stringify({ clientId: cid, pageNumber }),
    })
      .then(async (res) => {
        const data: unknown = await res.json().catch(() => ({}));
        if (!res.ok) {
          const msg =
            data && typeof data === "object" && "message" in data && typeof (data as { message?: unknown }).message === "string"
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
  }, [clientId, opts.adminKey, opts.enabled, pageNumber]);

  return { rides, loading, error, hasMore, loadMore };
}

export type ServiceNameById = Record<string, string>;

function readServiceDisplayName(raw: unknown): string | null {
  if (!raw || typeof raw !== "object") return null;
  const o = raw as Record<string, unknown>;
  const displayName = o.displayName;
  if (typeof displayName === "string" && displayName.trim() !== "") return displayName.trim();
  const name = o.name;
  if (typeof name === "string" && name.trim() !== "") return name.trim();
  return null;
}

export function useRideServices(
  rides: Ride[],
  opts: { adminKey: string; enabled: boolean },
): { namesById: ServiceNameById; loading: boolean } {
  const [namesById, setNamesById] = React.useState<ServiceNameById>({});
  const [loading, setLoading] = React.useState(false);
  const namesRef = React.useRef<ServiceNameById>({});

  React.useEffect(() => {
    namesRef.current = namesById;
  }, [namesById]);

  React.useEffect(() => {
    if (!opts.enabled) return;
    if (!opts.adminKey) return;

    const ids = Array.from(
      new Set(
        rides
          .map((r) => (typeof r.serviceId === "string" ? r.serviceId.trim() : ""))
          .filter(Boolean),
      ),
    );
    const missing = ids.filter((id) => !namesRef.current[id]);
    if (missing.length === 0) return;

    let cancelled = false;
    setLoading(true);

    const qs = new URLSearchParams({ "admin-key": opts.adminKey });

    Promise.all(
      missing.map(async (id) => {
        const res = await fetch(`/api/services/${encodeURIComponent(id)}?${qs.toString()}`, {
          method: "GET",
          headers: { Accept: "application/json" },
        });
        const data: unknown = await res.json().catch(() => null);
        if (!res.ok) return { id, name: null as string | null };
        return { id, name: readServiceDisplayName(data) };
      }),
    )
      .then((pairs) => {
        if (cancelled) return;
        setNamesById((prev) => {
          const next = { ...prev };
          for (const p of pairs) {
            if (p.name) next[p.id] = p.name;
          }
          return next;
        });
      })
      .finally(() => {
        if (!cancelled) setLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [rides, opts.adminKey, opts.enabled]);

  return { namesById, loading };
}

