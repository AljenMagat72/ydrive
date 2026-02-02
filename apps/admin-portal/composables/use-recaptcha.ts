import { load as loadCaptcha, type ReCaptchaInstance } from "recaptcha-v3";
import { useState } from "#app";

export function useRecaptcha() {
  const recaptcha = useState<Promise<ReCaptchaInstance>>("recapctha.instance");

  function load() {
    recaptcha.value = loadCaptcha("6LfYn24rAAAAAE9sozkJn5J0mxr8noKais-Jk2Kg", {
      autoHideBadge: true,
      useRecaptchaNet: true,
    });
  }

  const getToken = async (action: string) => {
    if (!recaptcha.value) return undefined;

    const token = await (await recaptcha.value).execute(action);
    return token;
  };

  return {
    load,
    getToken,
  };
}
