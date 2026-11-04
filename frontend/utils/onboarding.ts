export interface OnboardingValues {
  gym_name: string;
  gym_district: string;
  gym_address: string;
  gym_phone: string;
  gym_whatsapp: string;
  admin_first_name: string;
  admin_last_name: string;
  admin_email: string;
  admin_mobile_money_number: string;
  admin_password: string;
  admin_password_confirmation: string;
  plan_code: string;
  terms_accepted: boolean;
}

export type OnboardingField = keyof OnboardingValues;
export type OnboardingErrors = Partial<Record<OnboardingField, string>>;

export interface GymRegistrationPayload {
  gym: {
    name: string;
    district: string;
    address: string;
    phone: string;
    whatsapp: string;
  };
  admin: {
    first_name: string;
    last_name: string;
    email: string;
    mobile_money_number: string;
    password: string;
  };
  plan_code: "starter" | "essentiel" | "pro";
  terms_accepted: true;
}

export type GymRegistrationResult =
  | {
      status: "success";
      gymId: string | number | undefined;
      administrationUrl: string;
      message: string;
    }
  | { status: "invalid"; message: string; errors: OnboardingErrors }
  | { status: "rate-limited"; message: string }
  | { status: "error"; message: string };

const fieldLabels: Record<OnboardingField, string> = {
  gym_name: "Nom de la salle",
  gym_district: "Quartier ou commune",
  gym_address: "Adresse complète",
  gym_phone: "Téléphone principal",
  gym_whatsapp: "WhatsApp",
  admin_first_name: "Prénom",
  admin_last_name: "Nom",
  admin_email: "Adresse email",
  admin_mobile_money_number: "Numéro Mobile Money",
  admin_password: "Mot de passe",
  admin_password_confirmation: "Confirmation du mot de passe",
  plan_code: "Formule",
  terms_accepted: "Acceptation des conditions",
};

const planCodes = new Set(["starter", "essentiel", "pro"]);
const phonePattern = /^\+[1-9][0-9]{7,14}$/;

export function normalizeIvorianPhone(value: string): string {
  const compact = value.trim().replace(/[\s().-]/g, "");
  if (compact.startsWith("+")) return compact;

  const digits = compact.replace(/\D/g, "");
  if (digits.startsWith("225") && digits.length === 13) return `+${digits}`;
  if (digits.length === 10 && digits.startsWith("0")) return `+225${digits}`;
  return compact;
}

export function validateOnboardingStep(
  values: OnboardingValues,
  step: 1 | 2 | 3 | 4,
): OnboardingErrors {
  const errors: OnboardingErrors = {};

  if (step === 1) {
    const gymName = values.gym_name.trim();
    const district = values.gym_district.trim();
    const address = values.gym_address.trim();
    const phone = normalizeIvorianPhone(values.gym_phone);
    const whatsapp = normalizeIvorianPhone(values.gym_whatsapp);

    if (!gymName) errors.gym_name = "Saisissez le nom de votre salle.";
    else if (gymName.length > 80)
      errors.gym_name = "Le nom ne peut pas dépasser 80 caractères.";
    if (!district)
      errors.gym_district = "Choisissez un quartier ou une commune.";
    else if (district.length > 80)
      errors.gym_district =
        "Le quartier ou la commune ne peut pas dépasser 80 caractères.";
    if (!address) errors.gym_address = "Saisissez l’adresse de votre salle.";
    else if (address.length > 255)
      errors.gym_address = "L’adresse ne peut pas dépasser 255 caractères.";
    if (!values.gym_phone.trim())
      errors.gym_phone = "Saisissez le téléphone principal de votre salle.";
    else if (!phonePattern.test(phone))
      errors.gym_phone = "Saisissez un numéro ivoirien à 10 chiffres.";
    if (!values.gym_whatsapp.trim())
      errors.gym_whatsapp = "Saisissez le numéro WhatsApp de votre salle.";
    else if (!phonePattern.test(whatsapp))
      errors.gym_whatsapp = "Saisissez un numéro ivoirien à 10 chiffres.";
  }

  if (step === 2) {
    const firstName = values.admin_first_name.trim();
    const lastName = values.admin_last_name.trim();
    const email = values.admin_email.trim();
    const mobileMoney = normalizeIvorianPhone(values.admin_mobile_money_number);

    if (!firstName) errors.admin_first_name = "Saisissez votre prénom.";
    else if (firstName.length > 60)
      errors.admin_first_name = "Le prénom ne peut pas dépasser 60 caractères.";
    if (!lastName) errors.admin_last_name = "Saisissez votre nom.";
    else if (lastName.length > 60)
      errors.admin_last_name = "Le nom ne peut pas dépasser 60 caractères.";
    if (!email) errors.admin_email = "Saisissez votre adresse email.";
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
      errors.admin_email = "Saisissez une adresse email valide.";
    else if (email.length > 254)
      errors.admin_email =
        "L’adresse email ne peut pas dépasser 254 caractères.";
    if (!values.admin_mobile_money_number.trim())
      errors.admin_mobile_money_number = "Saisissez le numéro Mobile Money.";
    else if (!phonePattern.test(mobileMoney))
      errors.admin_mobile_money_number =
        "Saisissez un numéro ivoirien à 10 chiffres.";
    if (!values.admin_password)
      errors.admin_password = "Saisissez un mot de passe.";
    else if (
      values.admin_password.length < 8 ||
      values.admin_password.length > 72 ||
      !/[A-Z]/.test(values.admin_password) ||
      !/[0-9]/.test(values.admin_password)
    ) {
      errors.admin_password =
        "Le mot de passe doit contenir 8 caractères minimum, une majuscule et un chiffre.";
    }
    if (!values.admin_password_confirmation)
      errors.admin_password_confirmation = "Confirmez votre mot de passe.";
    else if (values.admin_password !== values.admin_password_confirmation)
      errors.admin_password_confirmation =
        "Les mots de passe ne correspondent pas.";
  }

  if (step === 3 && !planCodes.has(values.plan_code)) {
    errors.plan_code = "Choisissez une formule pour continuer.";
  }

  if (step === 4 && !values.terms_accepted) {
    errors.terms_accepted =
      "Acceptez les conditions générales et la politique de confidentialité.";
  }

  return errors;
}

export function validateOnboarding(values: OnboardingValues): OnboardingErrors {
  return {
    ...validateOnboardingStep(values, 1),
    ...validateOnboardingStep(values, 2),
    ...validateOnboardingStep(values, 3),
    ...validateOnboardingStep(values, 4),
  };
}

export function buildGymRegistrationPayload(
  values: OnboardingValues,
): GymRegistrationPayload {
  return {
    gym: {
      name: values.gym_name.trim(),
      district: values.gym_district.trim(),
      address: values.gym_address.trim(),
      phone: normalizeIvorianPhone(values.gym_phone),
      whatsapp: normalizeIvorianPhone(values.gym_whatsapp),
    },
    admin: {
      first_name: values.admin_first_name.trim(),
      last_name: values.admin_last_name.trim(),
      email: values.admin_email.trim(),
      mobile_money_number: normalizeIvorianPhone(
        values.admin_mobile_money_number,
      ),
      password: values.admin_password,
    },
    plan_code: values.plan_code as GymRegistrationPayload["plan_code"],
    terms_accepted: true,
  };
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null;
}

function messageFrom(body: unknown, fallback: string): string {
  if (isRecord(body) && typeof body.message === "string" && body.message.trim())
    return body.message;
  if (
    isRecord(body) &&
    isRecord(body.data) &&
    typeof body.data.message === "string" &&
    body.data.message.trim()
  ) {
    return body.data.message;
  }
  return fallback;
}

function apiErrorsFrom(body: unknown): OnboardingErrors {
  if (!isRecord(body) || !isRecord(body.errors)) return {};

  const aliases: Record<string, OnboardingField> = {
    name: "gym_name",
    district: "gym_district",
    address: "gym_address",
    phone: "gym_phone",
    whatsapp: "gym_whatsapp",
    first_name: "admin_first_name",
    last_name: "admin_last_name",
    email: "admin_email",
    mobile_money_number: "admin_mobile_money_number",
    password: "admin_password",
    plan_code: "plan_code",
    terms_accepted: "terms_accepted",
  };
  const errors: OnboardingErrors = {};

  for (const [apiName, value] of Object.entries(body.errors)) {
    const field = aliases[apiName.split(".").at(-1) ?? apiName];
    const message =
      typeof value === "string"
        ? value
        : Array.isArray(value) && typeof value[0] === "string"
          ? value[0]
          : undefined;
    if (field && message) errors[field] = message;
  }
  return errors;
}

export async function createGymRegistration(
  apiBase: string,
  values: OnboardingValues,
  fetcher: typeof fetch = fetch,
): Promise<GymRegistrationResult> {
  const validationErrors = validateOnboarding(values);
  if (Object.keys(validationErrors).length) {
    return {
      status: "invalid",
      message: "Vérifiez les champs indiqués avant de continuer.",
      errors: validationErrors,
    };
  }

  const base = apiBase.trim().replace(/\/+$/, "");
  if (!base) {
    return {
      status: "error",
      message:
        "L’inscription est momentanément indisponible. Réessayez plus tard.",
    };
  }

  try {
    const response = await fetcher(`${base}/gyms`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(buildGymRegistrationPayload(values)),
    });
    const body: unknown = await response.json().catch(() => null);

    if (response.status === 201) {
      const data = isRecord(body) && isRecord(body.data) ? body.data : {};
      return {
        status: "success",
        gymId:
          typeof data.gym_id === "string" || typeof data.gym_id === "number"
            ? data.gym_id
            : undefined,
        administrationUrl:
          typeof data.administration_url === "string"
            ? data.administration_url
            : "",
        message: messageFrom(
          body,
          "Votre salle a été créée. Consultez votre email pour confirmer votre compte.",
        ),
      };
    }
    if (response.status === 422) {
      return {
        status: "invalid",
        message: messageFrom(
          body,
          "Certaines informations sont invalides. Vérifiez les champs indiqués.",
        ),
        errors: apiErrorsFrom(body),
      };
    }
    if (response.status === 429) {
      return {
        status: "rate-limited",
        message: messageFrom(
          body,
          "Trop de tentatives. Réessayez dans une minute.",
        ),
      };
    }
    return {
      status: "error",
      message: messageFrom(
        body,
        "Une erreur est survenue pendant l’inscription. Réessayez plus tard.",
      ),
    };
  } catch {
    return {
      status: "error",
      message:
        "Impossible de joindre le service. Vérifiez votre connexion puis réessayez.",
    };
  }
}

export function labelForOnboardingField(field: OnboardingField): string {
  return fieldLabels[field];
}
