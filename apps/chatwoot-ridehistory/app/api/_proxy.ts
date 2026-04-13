import { NextRequest, NextResponse } from "next/server";

export function baseUrlFromEnv(): string | null {
  const v = "https://y-drive-api-develop.up.railway.app";
  const u = v.trim();
  return u ? u.replace(/\/$/, "") : null;
}

export function adminKeyFromRequest(req: NextRequest): string | null {
  const k = req.nextUrl.searchParams.get("admin-key")?.trim() ?? "";
  return k || null;
}

async function safeJsonFromText(text: string): Promise<unknown> {
  const t = text.trim();
  if (!t) return null;
  try {
    return JSON.parse(t) as unknown;
  } catch {
    return { message: t || "Upstream returned non-JSON" };
  }
}

export async function jsonFromUpstream(res: Response): Promise<unknown> {
  const text = await res.text();
  return safeJsonFromText(text);
}

export function jsonError(data: unknown, status: number) {
  return NextResponse.json(data && typeof data === "object" ? data : { message: String(data) }, { status });
}

