"use client";

import * as React from "react";
import { Badge } from "@/components/ui/badge";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Separator } from "@/components/ui/separator";
import { cn } from "@/lib/utils";
import type { DerivedCustomer } from "@/lib/types";
import { formatMoney } from "@/lib/format";
import { resolveHeroDisplayName, resolveHeroLifetimeSpend } from "@/lib/chatwoot";
import { Building2, ExternalLink, Hash, Mail, MoreHorizontal, Phone, UserCircle } from "lucide-react";

function initialsFromName(name: string) {
  const parts = name.trim().split(/\s+/).filter(Boolean);
  if (parts.length === 0) return "?";
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
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

function DetailRow({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <div className="flex flex-col gap-0.5 sm:flex-row sm:items-baseline sm:gap-3">
      <div className="flex shrink-0 items-center gap-1.5 text-zinc-500">
        <Hash className="h-3.5 w-3.5 opacity-70" />
        <span className="text-xs font-semibold uppercase tracking-wide">{label}</span>
      </div>
      <div className={cn("min-w-0 break-all text-zinc-100", mono && "font-mono text-widget-meta sm:text-widget-mono")}>{value}</div>
    </div>
  );
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
        className="max-h-[min(70vh,520px)] w-[min(100vw-2rem,400px)] overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden border-0 bg-zinc-900 p-0 text-base text-white shadow-widget-popover"
      >
        <div className="border-b border-white/10 bg-widget-surface-6 px-4 py-3">
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
          {Object.keys(profile.mergedCustomAttributes).length > 0 && (
            <>
              <Separator className="bg-white/10" />
              <div className="text-xs font-semibold uppercase tracking-wide text-zinc-500">Customer ID</div>
              <div className="mt-2 space-y-1.5 rounded-xl bg-widget-black-25 p-3 font-mono text-xs text-zinc-300 ring-1 ring-widget-surface-10">
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
}: Readonly<{ thumbnail: string | null | undefined; loading: boolean; displayName: string }>) {
  if (thumbnail) {
    // eslint-disable-next-line @next/next/no-img-element
    return <img src={thumbnail} alt="" className="h-16 w-16 shrink-0 rounded-2xl object-cover shadow-widget-avatar ring-1 ring-widget-surface-25" />;
  }
  return (
    <div
      className={cn(
        "grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-widget-tile text-xl font-bold tracking-tight text-white shadow-widget-avatar ring-2 ring-widget-surface-20",
        loading && "animate-pulse",
      )}
      aria-hidden
    >
      {loading ? "…" : initialsFromName(displayName)}
    </div>
  );
}

function CustomerHeroEmailSlot({ email, embedded, hasContext }: { email: string | null; embedded: boolean; hasContext: boolean }) {
  if (email) {
    return (
      <a href={`mailto:${email}`} className="inline-flex max-w-full items-center gap-1.5 truncate text-sky-200 hover:text-sky-100">
        <Mail className="h-4 w-4 shrink-0 opacity-90" />
        <span className="truncate sm:text-widget-contact">{email}</span>
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

function CustomerHeroPhoneSlot({ phone, embedded, hasContext }: { phone: string | null; embedded: boolean; hasContext: boolean }) {
  if (phone) {
    const telHref = `tel:${phone.replace(/\s/g, "")}`;
    return (
      <a href={telHref} className="inline-flex items-center gap-1.5 text-emerald-200/95 hover:text-emerald-100">
        <Phone className="h-4 w-4 shrink-0 opacity-90" />
        <span className="truncate sm:text-widget-contact">{phone}</span>
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

function CustomerHeroLifetimePanel({ lifetimeSpend }: Readonly<{ lifetimeSpend: number | null }>) {
  return (
    <div className="flex shrink-0 items-start justify-end gap-2 sm:pl-2">
      <div className="text-right">
        <div className="text-widget-section font-bold uppercase tracking-widget-section text-zinc-400">Lifetime spend</div>
        <div className={cn("mt-1 text-2xl font-bold tabular-nums tracking-tight text-white sm:text-3xl", lifetimeSpend == null && "text-zinc-500")}>
          {lifetimeSpend != null ? formatMoney(lifetimeSpend, "CAD") : "—"}
        </div>
      </div>
      <button
        type="button"
        className="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/80 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/40"
        aria-label="More options"
      >
        <MoreHorizontal className="h-5 w-5" />
      </button>
    </div>
  );
}

export function CustomerHero({
  embedded,
  derived,
  hasContext,
  loading,
}: Readonly<{ embedded: boolean; derived: DerivedCustomer | null; hasContext: boolean; loading: boolean }>) {
  const displayName = resolveHeroDisplayName(loading, derived, embedded);
  const lifetimeSpend = resolveHeroLifetimeSpend(derived, embedded);
  const showProfilePopover = Boolean(embedded && hasContext && derived);
  const profile = derived ?? null;

  return (
    <div className="sticky top-0 z-20 -mx-4 px-4 pb-3 pt-2 backdrop-blur sm:-mx-8 sm:px-8">
      <div className="relative overflow-hidden rounded-3xl bg-widget-panel p-4 shadow-widget-hero ring-1 ring-widget-ring sm:p-6">
        <div className="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div className="flex min-w-0 flex-1 gap-4">
            <CustomerHeroAvatar thumbnail={derived?.thumbnail} loading={loading} displayName={displayName} />
            <div className="min-w-0 flex-1 space-y-2">
              <div className="flex flex-wrap items-end gap-x-3 gap-y-1">
                <div className={cn("truncate text-2xl font-extrabold tracking-tight text-white drop-shadow-sm sm:text-4xl", loading && "animate-pulse text-white/80")}>
                  {displayName}
                </div>
                {showProfilePopover && profile ? <CustomerProfilePopover profile={profile} /> : null}
              </div>
              {derived?.company ? (
                <div className="flex items-center gap-2 text-base font-semibold text-emerald-200/95">
                  <Building2 className="h-4 w-4 shrink-0 opacity-90" />
                  <span className="truncate">{derived.company}</span>
                </div>
              ) : null}
              <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-medium">
                <CustomerHeroEmailSlot email={derived?.email ?? null} embedded={embedded} hasContext={hasContext} />
                <CustomerHeroPhoneSlot phone={derived?.phone ?? null} embedded={embedded} hasContext={hasContext} />
              </div>
              {derived?.socialProfiles && Object.values(derived.socialProfiles).some(Boolean) ? (
                <div className="flex flex-wrap gap-2 pt-1">
                  {Object.entries(derived.socialProfiles).map(([k, url]) =>
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
              ) : null}
            </div>
          </div>
          <CustomerHeroLifetimePanel lifetimeSpend={lifetimeSpend} />
        </div>
      </div>
    </div>
  );
}

