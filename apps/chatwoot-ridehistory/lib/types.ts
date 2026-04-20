export type StopType = "pickup" | "dropoff" | string;

export type StopPoint = {
  id: string;
  type: StopType;
  orderInParent: number;
  description: string;
  beforeTime?: string | null;
  afterTime?: string | null;
  plannedArrivalTime?: string | null;
  eta?: string | null;
  arrivedAt?: string | null;
  completedAt?: string | null;
};

export type Ride = {
  id: string;
  state: string;
  priceCurrency: string;
  priceAmount: number;
  createdAt: string;
  finalizedAt?: string | null;
  serviceId?: string | null;
  serviceDisplayName?: string | null;
  stopPoints: StopPoint[];
  payment?: { id: string; state?: string; paymentMethod?: { name?: string } } | null;
  paymentBreakdown?: { preAuth?: number; captured?: number; refunded?: number } | null;
  vehicle?: { year?: number | null; make?: string | null; model?: { name?: string; class?: string } } | null;
  driver?: { firstName?: string; lastName?: string; phoneNumber?: string } | null;
};

export type ChatwootSocialProfiles = {
  github?: string;
  twitter?: string;
  facebook?: string;
  linkedin?: string;
};

export type ChatwootPerson = {
  id?: number;
  name?: string;
  email?: string;
  phone_number?: string;
  identifier?: string | null;
  thumbnail?: string;
  availability_status?: string;
  custom_attributes?: Record<string, unknown>;
  additional_attributes?: {
    description?: string;
    company_name?: string;
    social_profiles?: ChatwootSocialProfiles;
  };
};

export type AppContext = {
  event?: string;
  data?: {
    conversation?: {
      id?: number;
      inbox_id?: number;
      status?: string;
      labels?: string[];
      custom_attributes?: Record<string, unknown>;
      meta?: {
        sender?: ChatwootPerson;
        channel?: string;
      };
    };
    contact?: ChatwootPerson & { id?: number };
    currentAgent?: {
      id?: number;
      name?: string;
      email?: string;
    };
  };
};

export type DerivedCustomer = {
  displayName: string;
  email: string | null;
  phone: string | null;
  identifier: string | null;
  contactId: number | undefined;
  conversationId: number | undefined;
  inboxId: number | undefined;
  thumbnail: string | null;
  company: string | null;
  description: string | null;
  availabilityStatus: string | null;
  channel: string | null;
  conversationStatus: string | null;
  labels: string[];
  agentName: string | null;
  agentEmail: string | null;
  socialProfiles: ChatwootSocialProfiles | null;
  mergedCustomAttributes: Record<string, unknown>;
};

