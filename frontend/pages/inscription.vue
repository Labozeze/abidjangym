<script setup lang="ts">
/* eslint-disable vue/no-v-html -- This is a local, versioned maquette fragment. */
import onboardingMarkup from "../content/onboarding-body.html?raw";
import {
  createGymRegistration,
  labelForOnboardingField,
  validateOnboardingStep,
  type OnboardingErrors,
  type OnboardingField,
  type OnboardingValues,
} from "../utils/onboarding";

const description =
  "Inscrivez votre salle sur AbidjanGym en quatre étapes. Choisissez votre formule et profitez de deux mois d’essai sur les offres payantes, sans carte bancaire.";
const canonical = "https://abidjangym.com/inscription";
const config = useRuntimeConfig();

useSeoMeta({
  title: "Inscription AbidjanGym — Créez votre salle en ligne",
  description,
  ogTitle: "Inscription AbidjanGym — Créez votre salle en ligne",
  ogDescription: description,
  ogType: "website",
  ogUrl: canonical,
  ogImage: "https://abidjangym.com/images/og-abidjangym.jpg",
  twitterCard: "summary_large_image",
  twitterTitle: "Inscription AbidjanGym — Créez votre salle en ligne",
  twitterDescription: description,
  twitterImage: "https://abidjangym.com/images/og-abidjangym.jpg",
});

useHead({
  link: [
    { rel: "canonical", href: canonical },
    { rel: "icon", type: "image/png", href: "/favicon.png" },
    { rel: "manifest", href: "/site.webmanifest" },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: JSON.stringify([
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          name: "AbidjanGym",
          url: "https://abidjangym.com",
          logo: "https://abidjangym.com/images/logo.png",
        },
        {
          "@context": "https://schema.org",
          "@type": "WebSite",
          name: "AbidjanGym",
          url: "https://abidjangym.com",
        },
        {
          "@context": "https://schema.org",
          "@type": "BreadcrumbList",
          itemListElement: [
            {
              "@type": "ListItem",
              position: 1,
              name: "Accueil",
              item: "https://abidjangym.com/",
            },
            {
              "@type": "ListItem",
              position: 2,
              name: "Inscription",
              item: canonical,
            },
          ],
        },
      ]),
    },
  ],
});

const stepRef = ref<HTMLElement>();
let detach: (() => void) | undefined;

function getValues(form: HTMLFormElement): OnboardingValues {
  const data = new FormData(form);
  return {
    gym_name: String(data.get("gym_name") ?? ""),
    gym_district: String(data.get("gym_district") ?? ""),
    gym_address: String(data.get("gym_address") ?? ""),
    gym_phone: String(data.get("gym_phone") ?? ""),
    gym_whatsapp: String(data.get("gym_whatsapp") ?? ""),
    admin_first_name: String(data.get("admin_first_name") ?? ""),
    admin_last_name: String(data.get("admin_last_name") ?? ""),
    admin_email: String(data.get("admin_email") ?? ""),
    admin_mobile_money_number: String(
      data.get("admin_mobile_money_number") ?? "",
    ),
    admin_password: String(data.get("admin_password") ?? ""),
    admin_password_confirmation: String(
      data.get("admin_password_confirmation") ?? "",
    ),
    plan_code: String(data.get("plan_code") ?? ""),
    terms_accepted: data.get("terms_accepted") === "on",
  };
}

function setupWizard(root: HTMLElement) {
  const form = root.querySelector<HTMLFormElement>("#onboarding-form");
  const alertBox = root.querySelector<HTMLElement>("#form-alert");
  const announcement = root.querySelector<HTMLElement>("#step-announcement");
  const wizard = root.querySelector<HTMLElement>("#wizard-content");
  const success = root.querySelector<HTMLElement>("#success");
  if (!form || !alertBox || !announcement || !wizard || !success) return;

  let currentStep = 1;
  let busy = false;
  const stepForField: Record<OnboardingField, number> = {
    gym_name: 1,
    gym_district: 1,
    gym_address: 1,
    gym_phone: 1,
    gym_whatsapp: 1,
    admin_first_name: 2,
    admin_last_name: 2,
    admin_email: 2,
    admin_mobile_money_number: 2,
    admin_password: 2,
    admin_password_confirmation: 2,
    plan_code: 3,
    terms_accepted: 4,
  };

  const controls = [...form.querySelectorAll<HTMLElement>("[name]")];
  const announce = (message: string) => {
    announcement.textContent = message;
  };
  const clearErrors = () => {
    alertBox.hidden = true;
    alertBox.replaceChildren();
    for (const control of controls) {
      control.removeAttribute("aria-invalid");
      const describedBy = control.getAttribute("aria-describedby");
      if (describedBy) {
        const ids = describedBy
          .split(/\s+/)
          .filter((id) => id !== "form-alert");
        if (ids.length) control.setAttribute("aria-describedby", ids.join(" "));
        else control.removeAttribute("aria-describedby");
      }
    }
  };
  const showErrors = (errors: OnboardingErrors, message: string) => {
    clearErrors();
    alertBox.hidden = false;
    const summary = document.createElement("p");
    summary.textContent = message;
    alertBox.append(summary);
    const entries = Object.entries(errors) as [OnboardingField, string][];
    if (entries.length) {
      const list = document.createElement("ul");
      for (const [field, error] of entries) {
        const item = document.createElement("li");
        item.textContent = `${labelForOnboardingField(field)} : ${error}`;
        list.append(item);
        const control = form.elements.namedItem(field);
        if (control instanceof HTMLElement) {
          control.setAttribute("aria-invalid", "true");
          const ids = (control.getAttribute("aria-describedby") ?? "")
            .split(/\s+/)
            .filter(Boolean);
          if (!ids.includes("form-alert")) ids.push("form-alert");
          control.setAttribute("aria-describedby", ids.join(" "));
        } else if (control instanceof RadioNodeList) {
          for (const radio of [...control]) {
            if (radio instanceof HTMLElement) {
              radio.setAttribute("aria-invalid", "true");
              radio.setAttribute("aria-describedby", "form-alert");
            }
          }
        }
      }
      alertBox.append(list);
    }
  };
  const updateSummary = () => {
    const values = getValues(form);
    const planNames: Record<string, string> = {
      starter: "Starter — gratuit",
      essentiel: "Essentiel — 15 000 FCFA/mois",
      pro: "Pro — 35 000 FCFA/mois",
    };
    const plan = values.plan_code;
    const paid = plan === "essentiel" || plan === "pro";
    root.querySelector("#recap-salle")!.textContent =
      values.gym_name.trim() || "—";
    root.querySelector("#recap-quartier")!.textContent =
      values.gym_district || "—";
    root.querySelector("#recap-plan")!.textContent = planNames[plan] ?? "—";
    root.querySelector("#recap-trial")!.textContent = paid
      ? "2 mois · aucun débit immédiat"
      : "Aucun essai · formule gratuite";
  };
  const goToStep = (step: number, focus = true) => {
    currentStep = step;
    for (let number = 1; number <= 4; number++) {
      const panel = root.querySelector<HTMLElement>(`#panel-${number}`)!;
      const dot = root.querySelector<HTMLElement>(`#dot-${number}`)!;
      const label = root.querySelector<HTMLElement>(`#lbl-${number}`)!;
      const item = root.querySelector<HTMLElement>(
        `[data-step-item="${number}"]`,
      )!;
      panel.classList.toggle("active", number === step);
      dot.className = `step-dot ${number < step ? "done" : number === step ? "active" : "pending"}`;
      if (number < step) {
        dot.innerHTML =
          '<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
      } else {
        dot.textContent = String(number);
      }
      label.className = `step-label ${number === step ? "active" : number < step ? "done" : "pending"}`;
      if (number === step) item.setAttribute("aria-current", "step");
      else item.removeAttribute("aria-current");
      if (number < 4) {
        root
          .querySelector<HTMLElement>(`#line-${number}`)!
          .classList.toggle("done", number < step);
      }
    }
    if (step === 4) updateSummary();
    announce(
      `Étape ${step} sur 4 : ${root.querySelector(`#lbl-${step}`)?.textContent ?? ""}`,
    );
    if (focus) root.querySelector<HTMLElement>(`#panel-title-${step}`)?.focus();
  };
  const focusFirstError = (errors: OnboardingErrors) => {
    const first = Object.keys(errors)[0] as OnboardingField | undefined;
    if (!first) return;
    const control = form.elements.namedItem(first);
    if (control instanceof HTMLElement) control.focus();
    else if (control instanceof RadioNodeList)
      (control[0] as HTMLElement | undefined)?.focus();
  };
  const validateStep = (step: 1 | 2 | 3 | 4) => {
    const errors = validateOnboardingStep(getValues(form), step);
    if (Object.keys(errors).length) {
      showErrors(
        errors,
        "Vérifiez les informations indiquées avant de continuer.",
      );
      focusFirstError(errors);
      return false;
    }
    clearErrors();
    return true;
  };
  const onClick = (event: Event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const button = target.closest<HTMLButtonElement>("[data-go-step]");
    if (!button || busy) return;
    const next = Number(button.dataset.goStep);
    if (next > currentStep && !validateStep(currentStep as 1 | 2 | 3 | 4))
      return;
    clearErrors();
    goToStep(next);
  };
  const onChange = (event: Event) => {
    const target = event.target;
    if (!(
      target instanceof HTMLInputElement || target instanceof HTMLSelectElement
    ))
      return;
    target.removeAttribute("aria-invalid");
    if (target.name === "plan_code") {
      root.querySelectorAll<HTMLElement>(".plan-opt").forEach((card) => {
        card.classList.toggle(
          "selected",
          card.querySelector<HTMLInputElement>("input")?.checked ?? false,
        );
      });
      if (currentStep === 4) updateSummary();
    }
    if (target.name === "terms_accepted" || target.name === "plan_code")
      clearErrors();
  };
  const onInput = (event: Event) => {
    const target = event.target;
    if (target instanceof HTMLElement && target.matches("input, select")) {
      target.removeAttribute("aria-invalid");
      if (!alertBox.hidden) clearErrors();
    }
  };
  const onSubmit = async (event: SubmitEvent) => {
    event.preventDefault();
    if (busy || !validateStep(4)) return;
    busy = true;
    form.setAttribute("aria-busy", "true");
    form
      .querySelectorAll<HTMLButtonElement>("button")
      .forEach((button) => (button.disabled = true));
    announce("Création de votre espace en cours.");
    const result = await createGymRegistration(
      String(config.public.apiBase ?? ""),
      getValues(form),
    );
    busy = false;
    form.removeAttribute("aria-busy");
    form
      .querySelectorAll<HTMLButtonElement>("button")
      .forEach((button) => (button.disabled = false));
    if (result.status === "success") {
      wizard.hidden = true;
      success.classList.add("active");
      success.querySelector(".success-sub")!.textContent = result.message;
      const planNames: Record<string, string> = {
        starter: "Starter",
        essentiel: "Essentiel",
        pro: "Pro",
      };
      const plan = getValues(form).plan_code;
      success.querySelector("#success-plan")!.textContent =
        planNames[plan] ?? plan;
      success.querySelector("#success-trial")!.textContent =
        plan === "starter"
          ? "Aucun essai · formule gratuite"
          : "2 mois gratuits · aucun débit immédiat";
      const urlText = success.querySelector<HTMLElement>(
        "#success-administration-url-value",
      )!;
      urlText.textContent = result.administrationUrl;
      const link = success.querySelector<HTMLAnchorElement>(
        "#success-dashboard-link",
      )!;
      try {
        const targetUrl = new URL(result.administrationUrl);
        if (["https:", "http:"].includes(targetUrl.protocol)) {
          link.href = targetUrl.href;
          link.hidden = false;
        }
      } catch {
        // Keep the API-provided URL visible as text if it is not a usable link.
      }
      announce("Inscription terminée.");
      success.querySelector<HTMLElement>(".success-title")?.focus();
      return;
    }
    if (result.status === "invalid") {
      const errors = result.errors;
      const entries = Object.keys(errors) as OnboardingField[];
      if (entries.length) {
        const step = Math.min(...entries.map((field) => stepForField[field]));
        goToStep(step, false);
        showErrors(errors, result.message);
        focusFirstError(errors);
      } else showErrors({}, result.message);
    } else {
      showErrors({}, result.message);
      alertBox.focus();
    }
    announce("L’inscription nécessite votre attention.");
  };

  form.addEventListener("click", onClick);
  form.addEventListener("change", onChange);
  form.addEventListener("input", onInput);
  form.addEventListener("submit", onSubmit);
  detach = () => {
    form.removeEventListener("click", onClick);
    form.removeEventListener("change", onChange);
    form.removeEventListener("input", onInput);
    form.removeEventListener("submit", onSubmit);
  };
}

onMounted(() => {
  if (stepRef.value) setupWizard(stepRef.value);
});
onBeforeUnmount(() => detach?.());
</script>

<template>
  <div ref="stepRef" class="onboarding-page" v-html="onboardingMarkup" />
</template>
