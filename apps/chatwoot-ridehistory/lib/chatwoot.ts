"use client";

import * as React from "react";
import type { AppContext, ChatwootPerson, DerivedCustomer } from "@/lib/types";

type ReactNativeWebViewBridge = { postMessage?: (message: string) => void };
type DocumentWithMessageEvent = Document & {
  addEventListener?: (type: "message", listener: (event: MessageEvent) => void) => void;
  removeEventListener?: (type: "message", listener: (event: MessageEvent) => void) => void;
};

const CHATWOOT_LAST_CTX_KEY = "chatwoot-dashboard-app:lastAppContext";

function safeSessionGet(key: string): string | null {
  try {
    return sessionStorage.getItem(key);
  } catch {
    return null;
  }
}

function safeSessionSet(key: string, value: string): boolean {
  try {
    sessionStorage.setItem(key, value);
    return true;
  } catch {
    return false;
  }
}

function isJsonString(s: string) {
  const t = s.trim();
  return (t.startsWith("{") && t.endsWith("}")) || (t.startsWith("[") && t.endsWith("]"));
}

export function parseDashboardAppPayload(data: unknown): AppContext | null {
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
  const obj = raw as { event?: unknown };
  if (obj.event !== "appContext") return null;
  return raw as AppContext;
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

export function useChatwootAppContext() {
  const [ctx, setCtx] = React.useState<AppContext | null>(null);
  const [embedded, setEmbedded] = React.useState(false);
  const hasCtxRef = React.useRef(false);

  React.useEffect(() => {
    setEmbedded(typeof window !== "undefined" && window.parent !== window);
  }, []);

  React.useEffect(() => {
    const raw = safeSessionGet(CHATWOOT_LAST_CTX_KEY);
    if (raw) {
      const restored = parseDashboardAppPayload(raw);
      if (restored?.data) {
        setCtx(restored);
        setEmbedded(true);
        hasCtxRef.current = true;
      }
    }

    function onMessage(event: MessageEvent) {
      const parsed = parseDashboardAppPayload(event.data);
      if (parsed?.data) {
        setCtx(parsed);
        setEmbedded(true);
        hasCtxRef.current = true;
        safeSessionSet(CHATWOOT_LAST_CTX_KEY, JSON.stringify(parsed));
      }
    }

    window.addEventListener("message", onMessage);
    (document as DocumentWithMessageEvent).addEventListener?.("message", onMessage);

    const requestInfo = () => {
      window.parent?.postMessage?.("chatwoot-dashboard-app:fetch-info", "*");
      (window as unknown as { ReactNativeWebView?: ReactNativeWebViewBridge }).ReactNativeWebView?.postMessage?.(
        "chatwoot-dashboard-app:fetch-info",
      );
    };

    requestInfo();
    let tries = 0;
    const interval = window.setInterval(() => {
      if (hasCtxRef.current) {
        window.clearInterval(interval);
        return;
      }
      tries += 1;
      if (tries > 12) {
        window.clearInterval(interval);
        return;
      }
      requestInfo();
    }, 500);

    return () => {
      window.removeEventListener("message", onMessage);
      (document as DocumentWithMessageEvent).removeEventListener?.("message", onMessage);
      window.clearInterval(interval);
    };
  }, []);

  const refresh = React.useCallback(() => {
    window.parent?.postMessage?.("chatwoot-dashboard-app:fetch-info", "*");
    (window as unknown as { ReactNativeWebView?: ReactNativeWebViewBridge }).ReactNativeWebView?.postMessage?.(
      "chatwoot-dashboard-app:fetch-info",
    );
  }, []);

  return { ctx, embedded, refresh };
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

export function resolveHeroDisplayName(loading: boolean, derived: DerivedCustomer | null, embedded: boolean): string {
  if (loading) return "Loading…";
  const v = derived?.displayName?.trim() ?? "";
  if (v) return v;
  return embedded ? "Unknown contact" : "---";
}

export function resolveHeroLifetimeSpend(derived: DerivedCustomer | null, embedded: boolean): number | null {
  const fromAttrs = readLifetimeSpendCad(derived?.mergedCustomAttributes);
  if (fromAttrs != null) return fromAttrs;
  return embedded ? null : 0;
}

export function deriveCustomer(ctx: AppContext | null): DerivedCustomer | null {
  const data = ctx?.data;
  if (!data) return null;
  const contact = data.contact;
  const sender = data.conversation?.meta?.sender;
  const conv = data.conversation;
  const agent = data.currentAgent;

  const displayName = contact?.name?.trim() || sender?.name?.trim() || "";
  const email = contact?.email || sender?.email || null;
  const phone = contact?.phone_number || sender?.phone_number || null;
  const identifier = (contact?.identifier ?? sender?.identifier ?? null) as string | null;
  const thumbnail = contact?.thumbnail || sender?.thumbnail || null;
  const company = contact?.additional_attributes?.company_name || sender?.additional_attributes?.company_name || null;
  const description = contact?.additional_attributes?.description || sender?.additional_attributes?.description || null;
  const socialProfiles = contact?.additional_attributes?.social_profiles || sender?.additional_attributes?.social_profiles || null;

  const mergedCustomAttributes: Record<string, unknown> = {
    ...((sender as ChatwootPerson | undefined)?.custom_attributes ?? {}),
    ...((contact as ChatwootPerson | undefined)?.custom_attributes ?? {}),
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

