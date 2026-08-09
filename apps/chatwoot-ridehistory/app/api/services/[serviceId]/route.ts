import { NextRequest, NextResponse } from "next/server";
import { adminKeyFromRequest, baseUrlFromEnv, jsonError, jsonFromUpstream } from "../../_proxy";

export async function GET(req: NextRequest, ctx: { params: Promise<{ serviceId: string }> }) {
  const baseUrl = baseUrlFromEnv();
  const adminKey = adminKeyFromRequest(req);
  const params = await ctx.params;

  if (!baseUrl) {
    return NextResponse.json({ message: "Missing YDRIVE_API_BASE_URL server environment variable" }, { status: 500 });
  }
  if (!adminKey) {
    return NextResponse.json(
      { message: "Missing admin-key query parameter (e.g. GET /api/services/:id?admin-key=…)" },
      { status: 400 },
    );
  }
  if (!params.serviceId) {
    return NextResponse.json({ message: "Missing serviceId route param" }, { status: 400 });
  }

  const url = `${baseUrl}/api/v1/admin/services/${encodeURIComponent(params.serviceId)}`;
  const res = await fetch(url, {
    method: "GET",
    headers: { Accept: "application/json", "X-Admin-Key": adminKey },
  });

  const data = await jsonFromUpstream(res);
  if (!res.ok) return jsonError(data, res.status);
  return NextResponse.json(data);
}

