"use client";

import * as React from "react";
import { Button } from "@/components/ui/button";
import type { ClientMatch } from "@/lib/types";
import { cn } from "@/lib/utils";

function labelFor(row: Record<string, unknown>): string {
  const r = row as unknown as { firstName: string; lastName: string; email: string; phoneNumber: string };
  const first = r.firstName?.trim?.() ?? "";
  const last = r.lastName?.trim?.() ?? "";
  const name = [first, last].filter(Boolean).join(" ").trim();
  if (name) return name;
  if (r.email?.trim?.()) return r.email.trim();
  if (r.phoneNumber?.trim?.()) return r.phoneNumber.trim();
  return "Client";
}

function subFor(row: Record<string, unknown>): string {
  const r = row as unknown as { email: string; phoneNumber: string };
  const bits: string[] = [];
  if (r.email?.trim?.()) bits.push(r.email.trim());
  if (r.phoneNumber?.trim?.()) bits.push(r.phoneNumber.trim());
  return bits.join(" · ") || "—";
}

export function ClientMatchPicker({
  matches,
  onSelect,
  disabled,
}: {
  matches: ClientMatch[];
  onSelect: (id: string) => void;
  disabled?: boolean;
}) {
  return (
    <div className="mt-6 space-y-3">
      <p className="text-center text-sm text-zinc-300">
        This email matches more than one client. Pick one to load rides.
      </p>
      <ul className="mx-auto flex w-full flex-col gap-2">
        {matches.map((m) => (
          <li key={m.id}>
            <Button
              type="button"
              variant="secondary"
              disabled={disabled}
              className={cn(
                "cursor-pointer h-auto min-h-12 w-full flex-col items-stretch gap-0.5 whitespace-normal rounded-xl border border-white/15 bg-widget-panel px-4 py-3 text-left text-base hover:bg-white/10",
              )}
              onClick={() => onSelect(m.id)}
            >
              <span className="font-semibold text-white">{labelFor(m.row)}</span>
              <span className="text-sm text-zinc-400">{subFor(m.row)}</span>
            </Button>
          </li>
        ))}
      </ul>
    </div>
  );
}

