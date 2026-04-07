import { NextRequest, NextResponse } from "next/server";

type ClientRidesBody = {
  name?: string;
  email?: string;
  phone?: string;
  pageNumber?: number;
};

function baseUrlFromEnv(): string | null {
  const v = "https://api.ydriveapp.com";
  const u = v.trim();
  return u ? u.replace(/\/$/, "") : null;
}

function adminKeyFromRequest(req: NextRequest): string | null {
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

async function jsonFromUpstream(res: Response): Promise<unknown> {
  const text = await res.text();
  return safeJsonFromText(text);
}

function jsonError(data: unknown, status: number) {
  return NextResponse.json(data && typeof data === "object" ? data : { message: String(data) }, { status });
}

function readNonNegativeInt(v: unknown): number | undefined {
  return typeof v === "number" && Number.isFinite(v) && v >= 0 ? v : undefined;
}

async function lookupClientId(baseUrl: string, adminKey: string, body: ClientRidesBody): Promise<{ ok: true; id: string } | { ok: false; res: NextResponse }> {
  const searchUrl = `${baseUrl}/api/v1/admin/client/find`;
  const searchRes = await fetch(searchUrl, {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      "X-Admin-Key": adminKey,
    },
    body: JSON.stringify({
      phone: body.phone ?? "",
      email: body.email ?? "",
      name: body.name ?? "",
    }),
  });

  const searchData = await jsonFromUpstream(searchRes);
  if (!searchRes.ok) {
    return { ok: false, res: jsonError(searchData, searchRes.status) };
  }

  const id =
    searchData && typeof searchData === "object" && typeof (searchData as { id?: unknown }).id === "string"
      ? (searchData as { id: string }).id
      : null;

  if (!id) {
    return { ok: false, res: NextResponse.json({ message: "Client lookup returned no id" }, { status: 502 }) };
  }

  return { ok: true, id };
}

async function fetchRides(baseUrl: string, adminKey: string, clientId: string, body: ClientRidesBody): Promise<NextResponse> {
  const pageNumber = readNonNegativeInt(body.pageNumber);
  const qs = new URLSearchParams();
  if (pageNumber !== undefined) qs.set("pageNumber", String(pageNumber));

  const ridesPath = `/api/v1/admin/client/${encodeURIComponent(clientId)}/rides`;
  const ridesUrl = `${baseUrl}${ridesPath}`;
  const ridesUrlWithQuery = qs.size ? `${ridesUrl}?${qs.toString()}` : ridesUrl;
  const res = await fetch(ridesUrlWithQuery, {
    method: "GET",
    headers: {
      Accept: "application/json",
      "X-Admin-Key": adminKey,
    },
  });

  const data = await jsonFromUpstream(res);
  if (!res.ok) return jsonError(data, res.status);
  return NextResponse.json(data);
}

export async function POST(req: NextRequest) {
  const baseUrl = baseUrlFromEnv();
  const adminKey = adminKeyFromRequest(req);

  if (!baseUrl) {
    return NextResponse.json(
      { message: "Missing YDRIVE_API_BASE_URL server environment variable" },
      { status: 500 },
    );
  }
  if (!adminKey) {
    return NextResponse.json(
      { message: "Missing admin-key query parameter (e.g. POST /api/rides?admin-key=…)" },
      { status: 400 },
    );
  }

  let body: ClientRidesBody;
  try {
    body = (await req.json()) as ClientRidesBody;
  } catch {
    return NextResponse.json({ message: "Invalid JSON body" }, { status: 400 });
  }

  const lookup = await lookupClientId(baseUrl, adminKey, body);
  if (!lookup.ok) return lookup.res;
  return fetchRides(baseUrl, adminKey, lookup.id, body);
}
