export const useAuth = () => {
  const authToken = useAuthToken();
  const userName = useUserName();
  const userRole = useUserRole();

  type Form = {
    name: string;
    email: string;
    password: string;
  };

  async function register(form: Form, recaptcha: string) {
    try {
      const res: any = await $fetch(`/api/register`, {
        method: "POST",
        body: { form, recaptcha },
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (res.success) {
        return { success: true, message: res.message };
      } else {
        return {
          success: false,
          message: "Registration failed, please contact developer.",
        };
      }
    } catch (err: any) {
      return { success: false, message: err?.status || "Login failed" };
    }
  }

  async function login(email: string, password: string, recaptcha: string) {
    try {
      const res: any = await $fetch(`/api/login`, {
        method: "POST",
        body: { email, password, recaptcha },
      });

      if (res.access_token) {
        authToken.value = res.access_token;
        userName.value = res.user.name;
        userRole.value = res.user.role;

        return { success: true, user: res.user };
      } else {
        return { success: false, message: "Login failed" };
      }
    } catch (err: any) {
      return { success: false, message: err?.data?.message || "Login failed" };
    }
  }

  function logout() {
    userName.value = null;
    userRole.value = null;
    authToken.value = null;
  }

  const isUserLoggedIn: boolean = authToken.value != "";

  return { login, logout, register, isUserLoggedIn };
};
