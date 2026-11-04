import { describe, expect, it, vi } from "vitest";
import {
  buildGymRegistrationPayload,
  createGymRegistration,
  normalizeIvorianPhone,
  validateOnboardingStep,
  type OnboardingValues,
} from "../utils/onboarding";

const validValues: OnboardingValues = {
  gym_name: "Salle Test Abidjan",
  gym_district: "Cocody",
  gym_address: "Rue des Jardins, Cocody",
  gym_phone: "07 00 00 00 01",
  gym_whatsapp: "07 00 00 00 02",
  admin_first_name: "Awa",
  admin_last_name: "Koné",
  admin_email: "awa@example.com",
  admin_mobile_money_number: "07 00 00 00 03",
  admin_password: "MotDePasse1",
  admin_password_confirmation: "MotDePasse1",
  plan_code: "essentiel",
  terms_accepted: true,
};

function response(status: number, body: unknown): Response {
  return { status, json: async () => body } as Response;
}

describe("tunnel d’inscription", () => {
  it("signale les champs requis de l’étape salle", () => {
    const errors = validateOnboardingStep(
      { ...validValues, gym_name: "", gym_whatsapp: "" },
      1,
    );

    expect(errors.gym_name).toBe("Saisissez le nom de votre salle.");
    expect(errors.gym_whatsapp).toBe(
      "Saisissez le numéro WhatsApp de votre salle.",
    );
  });

  it("normalise un numéro ivoirien et accepte un numéro E.164", () => {
    expect(normalizeIvorianPhone("07 00 00 00 01")).toBe("+2250700000001");
    expect(normalizeIvorianPhone("+225 07 00 00 00 01")).toBe("+2250700000001");
  });

  it("valide la règle de mot de passe et sa confirmation", () => {
    expect(
      validateOnboardingStep(
        {
          ...validValues,
          admin_password: "court1",
          admin_password_confirmation: "différent1",
        },
        2,
      ),
    ).toMatchObject({
      admin_password:
        "Le mot de passe doit contenir 8 caractères minimum, une majuscule et un chiffre.",
      admin_password_confirmation: "Les mots de passe ne correspondent pas.",
    });
  });

  it("construit le payload du contrat sans envoyer la confirmation", () => {
    const payload = buildGymRegistrationPayload(validValues);

    expect(payload).toEqual({
      gym: {
        name: "Salle Test Abidjan",
        district: "Cocody",
        address: "Rue des Jardins, Cocody",
        phone: "+2250700000001",
        whatsapp: "+2250700000002",
      },
      admin: {
        first_name: "Awa",
        last_name: "Koné",
        email: "awa@example.com",
        mobile_money_number: "+2250700000003",
        password: "MotDePasse1",
      },
      plan_code: "essentiel",
      terms_accepted: true,
    });
    expect(payload.admin).not.toHaveProperty("password_confirmation");
  });

  it("crée la salle et retourne le lien d’administration après 201", async () => {
    const fetcher = vi.fn(async () =>
      response(201, {
        data: {
          gym_id: 42,
          administration_url: "https://app.abidjangym.com/dashboard",
          message:
            "Votre salle a été créée. Consultez votre email pour confirmer votre compte.",
        },
      }),
    );

    const result = await createGymRegistration(
      "http://127.0.0.1:4010/",
      validValues,
      fetcher,
    );

    expect(fetcher).toHaveBeenCalledWith(
      "http://127.0.0.1:4010/gyms",
      expect.objectContaining({ method: "POST" }),
    );
    expect(result).toMatchObject({
      status: "success",
      gymId: 42,
      administrationUrl: "https://app.abidjangym.com/dashboard",
    });
  });

  it("associe les erreurs 422 aux champs et traite la limite 429", async () => {
    const invalid = await createGymRegistration(
      "http://127.0.0.1:4010",
      validValues,
      vi.fn(async () =>
        response(422, {
          message: "Les données fournies sont invalides.",
          errors: { "admin.email": ["Cette adresse email est déjà utilisée."] },
        }),
      ),
    );
    expect(invalid).toMatchObject({
      status: "invalid",
      errors: { admin_email: "Cette adresse email est déjà utilisée." },
    });

    const limited = await createGymRegistration(
      "http://127.0.0.1:4010",
      validValues,
      vi.fn(async () =>
        response(429, {
          message: "Trop de tentatives. Réessayez dans une minute.",
        }),
      ),
    );
    expect(limited.status).toBe("rate-limited");
  });
});
