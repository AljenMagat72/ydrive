import { NextRequest, NextResponse } from "next/server";
import { adminKeyFromRequest, baseUrlFromEnv, jsonError, jsonFromUpstream } from "../../../_proxy";

export async function GET(req: NextRequest, ctx: { params: Promise<{ chargeId: string }> }) {
  const adminKey = adminKeyFromRequest(req);
  if (!adminKey) return NextResponse.json({ message: "Missing admin-key query parameter" }, { status: 400 });

  const baseUrl = baseUrlFromEnv();

  const { chargeId } = await ctx.params;
  const upstreamUrl = `${baseUrl}/api/v1/admin/stripe/charge/${encodeURIComponent(chargeId)}`;
  const res = await fetch(upstreamUrl, {
    method: "GET",
    headers: { Accept: "application/json", "X-Admin-Key": adminKey },
  });

  const data = await jsonFromUpstream(res);
  if (!res.ok) return jsonError(data, res.status);
  return NextResponse.json(data);
}
