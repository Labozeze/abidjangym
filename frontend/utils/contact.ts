export interface ContactFormValues {
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  subject: string;
  message: string;
}

export type ContactField = keyof ContactFormValues;
export type ContactFieldErrors = Partial<Record<ContactField, string>>;

export interface ContactMessagePayload {
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  subject: string;
  message: string;
}

export type ContactSubmissionResult =
  | { status: "success"; message: string }
  | {
      status: "invalid";
      message: string;
      errors: ContactFieldErrors;
    }
  | { status: "rate-limited"; message: string }
  | { status: "error"; message: string };

const fieldLabels: Record<ContactField, string> = {
  first_name: "Le prénom",
  last_name: "Le nom",
  email: "L’adresse email",
  phone: "Le numéro de téléphone",
  subject: "L’objet",
  message: "Le message",
};

export function normalizePhone(phone: string): string {
  return phone.trim().replace(/[\s().-]/g, "");
}

export function normalizeContactValues(
  values: ContactFormValues,
): ContactMessagePayload {
  const phone = normalizePhone(values.phone);

  return {
    first_name: values.first_name.trim(),
    last_name: values.last_name.trim(),
    email: values.email.trim(),
    ...(phone ? { phone } : {}),
    subject: values.subject.trim(),
    message: values.message.trim(),
  };
}

export function validateContactForm(
  values: ContactFormValues,
): ContactFieldErrors {
  const payload = normalizeContactValues(values);
  const errors: ContactFieldErrors = {};

  for (const field of [
    "first_name",
    "last_name",
    "email",
    "subject",
    "message",
  ] as const) {
    if (!payload[field])
      errors[field] = `${fieldLabels[field]} est obligatoire.`;
  }

  if (payload.first_name.length > 60) {
    errors.first_name = "Le prénom ne peut pas dépasser 60 caractères.";
  }
  if (payload.last_name.length > 60) {
    errors.last_name = "Le nom ne peut pas dépasser 60 caractères.";
  }
  if (payload.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) {
    errors.email = "Saisissez une adresse email valide.";
  }
  if (payload.email.length > 254) {
    errors.email = "L’adresse email ne peut pas dépasser 254 caractères.";
  }
  if (payload.phone && !/^\+[1-9][0-9]{7,14}$/.test(payload.phone)) {
    errors.phone =
      "Saisissez un numéro au format international, par exemple +2250700000001.";
  }
  if (payload.subject.length > 150) {
    errors.subject = "L’objet ne peut pas dépasser 150 caractères.";
  }
  if (payload.message.length > 5000) {
    errors.message = "Le message ne peut pas dépasser 5 000 caractères.";
  }

  return errors;
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === "object" && value !== null;
}

function getApiMessage(body: unknown, fallback: string): string {
  if (
    isRecord(body) &&
    typeof body.message === "string" &&
    body.message.trim()
  ) {
    return body.message;
  }
  return fallback;
}

function getFieldErrors(body: unknown): ContactFieldErrors {
  if (!isRecord(body) || !isRecord(body.errors)) return {};

  const errors: ContactFieldErrors = {};
  for (const field of Object.keys(fieldLabels) as ContactField[]) {
    const value = body.errors[field];
    if (typeof value === "string") errors[field] = value;
    else if (Array.isArray(value) && typeof value[0] === "string") {
      errors[field] = value[0];
    }
  }
  return errors;
}

export async function sendContactMessage(
  apiBase: string,
  values: ContactFormValues,
  fetcher: typeof fetch = fetch,
): Promise<ContactSubmissionResult> {
  const payload = normalizeContactValues(values);
  const errors = validateContactForm(values);
  if (Object.keys(errors).length) {
    return {
      status: "invalid",
      message: "Vérifiez les champs indiqués puis réessayez.",
      errors,
    };
  }

  const base = apiBase.trim().replace(/\/+$/, "");
  if (!base) {
    return {
      status: "error",
      message:
        "Le formulaire est momentanément indisponible. Réessayez plus tard.",
    };
  }

  try {
    const response = await fetcher(`${base}/contact-messages`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(payload),
    });
    const body: unknown = await response.json().catch(() => null);

    if (response.status === 201) {
      return {
        status: "success",
        message: getApiMessage(
          body,
          "Merci, votre message a bien été envoyé. Notre équipe vous répond en moins de 24 h.",
        ),
      };
    }
    if (response.status === 422) {
      return {
        status: "invalid",
        message: getApiMessage(
          body,
          "Vérifiez les champs indiqués puis réessayez.",
        ),
        errors: getFieldErrors(body),
      };
    }
    if (response.status === 429) {
      return {
        status: "rate-limited",
        message: getApiMessage(
          body,
          "Trop de messages ont été envoyés. Réessayez dans une minute.",
        ),
      };
    }

    return {
      status: "error",
      message: getApiMessage(
        body,
        "Une erreur est survenue lors de l’envoi. Réessayez plus tard.",
      ),
    };
  } catch {
    return {
      status: "error",
      message:
        "Impossible d’envoyer votre message. Vérifiez votre connexion puis réessayez.",
    };
  }
}
