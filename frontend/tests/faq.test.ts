import { readFileSync } from "node:fs";
import { resolve } from "node:path";
import { describe, expect, it } from "vitest";
import { parseFaqMarkdown } from "../utils/faq";

const faqMarkdown = readFileSync(
  resolve(process.cwd(), "content/faq.md"),
  "utf8",
);

describe("FAQ landing", () => {
  const items = parseFaqMarkdown(faqMarkdown);

  it("reprend les douze questions de la FAQ éditoriale", () => {
    expect(items).toHaveLength(12);
    expect(items[0].question).toBe("Quels sont les prix des formules ?");
    expect(items[11].question).toBe(
      "Que faire si un abonné perd son QR code ?",
    );
  });

  it("ne rend pas les commentaires internes dans les réponses", () => {
    expect(items.every(({ answer }) => !answer.includes("[À FOURNIR]"))).toBe(
      true,
    );
    expect(items.every(({ answer }) => !answer.includes("CDC"))).toBe(true);
  });

  it("inclut la décision sur l’activation des moyens de paiement", () => {
    const paymentAnswer = items.find(({ question }) =>
      question.includes("moyens de paiement"),
    )?.answer;

    expect(paymentAnswer).toContain("s’active ou se désactive en un clic");
    expect(paymentAnswer).toContain(
      "seuls ceux qui sont activés sont proposés",
    );
  });
});
