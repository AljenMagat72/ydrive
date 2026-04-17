import { NextRequest, NextResponse } from "next/server";
import { adminKeyFromRequest, baseUrlFromEnv, jsonError, jsonFromUpstream } from "../../_proxy";

export async function GET(req: NextRequest) {
  const adminKey = adminKeyFromRequest(req);
  if (!adminKey) {
    return NextResponse.json({ message: "Missing admin-key query parameter" }, { status: 400 });
  }

  const paymentId = req.nextUrl.searchParams.get("paymentId")?.trim() ?? "";
  if (!paymentId) {
    return NextResponse.json({ message: "Missing paymentId query parameter" }, { status: 400 });
  }

  const baseUrl = baseUrlFromEnv();
  if (!baseUrl) {
    return NextResponse.json({ message: "Missing YDRIVE_API_BASE_URL server environment variable" }, { status: 500 });
  }

  const upstreamUrl = `${baseUrl}/api/v1/admin/stripe/payment-dashboard-url?${new URLSearchParams({ paymentId }).toString()}`;
  const res = await fetch(upstreamUrl, {
    method: "GET",
    headers: { Accept: "application/json", "X-Admin-Key": adminKey },
  });

  const data = await jsonFromUpstream(res);
  if (!res.ok) return jsonError(data, res.status);
  return NextResponse.json(data);
}
