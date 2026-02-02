import { navigateTo, useState } from "#app";
import { useAPI, useAuthToken } from "#imports";
import { computed, readonly } from "vue";

type User = {
  avatar: string;
  firstName: string;
  lastName: string;
  cityId: string;
  minimumScheduledHours: string;
  acceptanceRate: string;
  acceptanceRateNeeded: string;
};

type LoginResponse = {
  success: boolean;
  token: string;
};

type VerifyResponse = {
  success: boolean;
  token: string;
  user: User;
};

type ResendResponse = {
  success: boolean;
};

type MeResponse = {
  success: boolean;
  user: User;
};

export function useAuth() {
  const { post, get } = useAPI();
  const authToken = useAuthToken();
  const user = useState<User | undefined>("auth.user", () => undefined);
  const verifyToken = useState<string | undefined>(
    "auth.verifyToken",
    () => undefined
  );

  const isLoggedIn = computed(() => !!user.value);

  async function login(phoneNumber: string, captcha: string) {

      const response = await post<LoginResponse>(
        "/api/v1/auth/driver/sms/login",
        { phoneNumber, captcha }
      );

      if (response.success) {
        verifyToken.value = response.token;
      }

      return response;
  }

  async function verify(code: string, captcha: string) {

    const response = await post<VerifyResponse>(
      "/api/v1/auth/driver/sms/verify",
      { code, captcha },
      {
        Authorization: `Bearer ${verifyToken.value}`,
      }
    );

    if (response.success) {
        authToken.value = response.token;
        user.value = response.user;
      } 

      return response;
  }

  async function resend() {
    return post<ResendResponse>("/api/v1/auth/driver/sms/resend",
      {},
      {
        Authorization: `Bearer ${verifyToken.value}`,
      }
    );
  }

  async function me() {
    const response = await get<MeResponse>("/api/v1/driver/me");

    if (response) {
      user.value = response.user;
    }

    return response;
  }

  async function logout() {
    await post("/api/v1/auth/driver/logout");
    navigateTo("/login");

    user.value = undefined;
  }

  return {
    user: readonly(user),
    isLoggedIn,
    me,
    login,
    verify,
    resend,
    logout,
  };
}
