import { describe, expect, it, vi } from "vitest";
import {
  normalizeContactValues,
  sendContactMessage,
  validateContactForm,
  type ContactFormValues,
} from "../utils/contact";

const validValues: ContactFormValues = {
  first_name: "Awa",
  last_name: "Koné",
  email: "awa@example.com",
  phone: "+225 07 00 00 00 01",
  subject: "demo",
  message: "Je souhaite découvrir les formules.",
};

function response(status: number, body: unknown): Response {
  return {
    status,
    json: async () => body,
  } as Response;
}

describe("formulaire de contact", () => {
  it("valide les champs requis et accepte le téléphone facultatif", () => {
    expect(validateContactForm({ ...validValues, phone: "" })).toEqual({});
    expect(
      validateContactForm({ ...validValues, email: "adresse invalide" }).email,
    ).toBe("Saisissez une adresse email valide.");
  });

  it("normalise le téléphone au format E.164 du contrat", () => {
    expect(normalizeContactValues(validValues).phone).toBe("+2250700000001");
  });

  it("envoie le formulaire et affiche le message de succès 201", async () => {
    const fetcher = vi.fn(async () =>
      response(201, { message: "Message reçu. Réponse sous 24 h." }),
    );

    const result = await sendContactMessage(
      "http://127.0.0.1:4010/",
      validValues,
      fetcher,
    );

    expect(fetcher).toHaveBeenCalledWith(
      "http://127.0.0.1:4010/contact-messages",
      expect.objectContaining({ method: "POST" }),
    );
    expect(result).toEqual({
      status: "success",
      message: "Message reçu. Réponse sous 24 h.",
    });
  });

  it("associe les erreurs 422 aux champs concernés", async () => {
    const fetcher = vi.fn(async () =>
      response(422, {
        message: "Les données fournies sont invalides.",
        errors: { email: ["Le champ email est invalide."] },
      }),
    );

    const result = await sendContactMessage(
      "http://127.0.0.1:4010",
      validValues,
      fetcher,
    );

    expect(result).toEqual({
      status: "invalid",
      message: "Les données fournies sont invalides.",
      errors: { email: "Le champ email est invalide." },
    });
  });

  it("affiche la limitation de débit 429", async () => {
    const fetcher = vi.fn(async () =>
      response(429, {
        message: "Trop de tentatives. Réessayez dans une minute.",
      }),
    );

    const result = await sendContactMessage(
      "http://127.0.0.1:4010",
      validValues,
      fetcher,
    );

    expect(result).toEqual({
      status: "rate-limited",
      message: "Trop de tentatives. Réessayez dans une minute.",
    });
  });
});
