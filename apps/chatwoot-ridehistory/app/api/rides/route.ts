import { NextRequest, NextResponse } from "next/server";
import { adminKeyFromRequest, baseUrlFromEnv, jsonError, jsonFromUpstream } from "../_proxy";

type ClientRidesBody = {
  name?: string;
  email?: string;
  phone?: string;
  pageNumber?: number;
  clientId?: string;
};

function readNonNegativeInt(v: unknown): number | undefined {
  return typeof v === "number" && Number.isFinite(v) && v >= 0 ? v : undefined;
}

type ClientMatch = { id: string; row: Record<string, unknown> };

async function findAfterLookup(
  baseUrl: string,
  adminKey: string,
  body: ClientRidesBody,
): Promise<
  | { ok: true; kind: "id"; id: string }
  | { ok: true; kind: "selection"; matches: ClientMatch[] }
  | { ok: false; res: NextResponse }
> {
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

  if (!searchData || typeof searchData !== "object") {
    return { ok: false, res: NextResponse.json({ message: "Client lookup returned no matches" }, { status: 502 }) };
  }

  const o = searchData as Record<string, unknown>;
  if (typeof o.id === "string" && o.id) return { ok: true, kind: "id", id: o.id };

  const raw = o.matches;
  if (!Array.isArray(raw) || raw.length === 0) {
    return { ok: false, res: NextResponse.json({ message: "Client lookup returned no matches" }, { status: 502 }) };
  }

  const matches = raw as ClientMatch[];
  if (matches.length === 1 && typeof matches[0]?.id === "string" && matches[0].id) {
    return { ok: true, kind: "id", id: matches[0].id };
  }

  return { ok: true, kind: "selection", matches };
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
  const ridesPayload =
    data && typeof data === "object" && !Array.isArray(data)
      ? (data as Record<string, unknown>)
      : { rows: Array.isArray(data) ? data : [] };

  return NextResponse.json({ clientId, ...ridesPayload });
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

  if (typeof body.clientId === "string" && body.clientId) {
    return fetchRides(baseUrl, adminKey, body.clientId, body);
  }

  const found = await findAfterLookup(baseUrl, adminKey, body);
  if (!found.ok) return found.res;

  if (found.kind === "selection") {
    return NextResponse.json({ needsSelection: true, matches: found.matches }, { status: 200 });
  }

  return fetchRides(baseUrl, adminKey, found.id, body);
}
