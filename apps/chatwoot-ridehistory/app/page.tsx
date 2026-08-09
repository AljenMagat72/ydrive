import { redirect } from "next/navigation";

type HomePageProps = {
  searchParams: Promise<Record<string, string | string[] | undefined>>;
};

function searchParamsToQueryString(sp: Record<string, string | string[] | undefined>): string {
  const q = new URLSearchParams();
  for (const [key, value] of Object.entries(sp)) {
    if (value === undefined) continue;
    if (Array.isArray(value)) {
      for (const v of value) {
        q.append(key, v);
      }
    } else {
      q.append(key, value);
    }
  }
  return q.toString();
}

export default async function Home({ searchParams }: HomePageProps) {
  const sp = await searchParams;
  const qs = searchParamsToQueryString(sp);
  redirect(qs ? `/chatwoot-ridehistory?${qs}` : "/chatwoot-ridehistory");
}
