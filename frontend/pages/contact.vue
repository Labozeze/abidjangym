<script setup lang="ts">
import contactBody from "~/content/contact-body.html?raw";
import {
  sendContactMessage,
  validateContactForm,
  type ContactField,
  type ContactFormValues,
} from "~/utils/contact";

const siteUrl = "https://abidjangym.com";
const pageTitle = "Contact AbidjanGym — Parlons de votre salle à Abidjan";
const pageDescription =
  "Contactez AbidjanGym pour une démo, un partenariat ou du support. Décrivez votre demande dans le formulaire ; notre équipe vous répond en moins de 24 h.";
const contactMarkup = contactBody;
const apiBase = useRuntimeConfig().public.apiBase;

const structuredData = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      name: "AbidjanGym",
      url: `${siteUrl}/`,
      logo: `${siteUrl}/icons/icon-512.png`,
    },
    {
      "@type": "WebSite",
      name: "AbidjanGym",
      url: `${siteUrl}/`,
      inLanguage: "fr-CI",
    },
    {
      "@type": "BreadcrumbList",
      itemListElement: [
        {
          "@type": "ListItem",
          position: 1,
          name: "Accueil",
          item: `${siteUrl}/`,
        },
        {
          "@type": "ListItem",
          position: 2,
          name: "Contact",
          item: `${siteUrl}/contact`,
        },
      ],
    },
  ],
};

useSeoMeta({
  title: pageTitle,
  description: pageDescription,
  ogTitle: pageTitle,
  ogDescription: pageDescription,
  ogImage: `${siteUrl}/images/landing-owner.jpg`,
  ogUrl: `${siteUrl}/contact`,
  ogType: "website",
  twitterCard: "summary_large_image",
  twitterTitle: pageTitle,
  twitterDescription: pageDescription,
  twitterImage: `${siteUrl}/images/landing-owner.jpg`,
});

useHead({
  link: [
    { rel: "canonical", href: `${siteUrl}/contact` },
    { rel: "icon", href: "/favicon.ico", sizes: "any" },
    { rel: "icon", href: "/icons/icon-192.png", type: "image/png" },
    { rel: "apple-touch-icon", href: "/icons/apple-touch-icon.png" },
    { rel: "manifest", href: "/manifest.json" },
  ],
  script: [
    {
      type: "application/ld+json",
      innerHTML: JSON.stringify(structuredData),
    },
  ],
});

let observer: IntersectionObserver | undefined;
let contactForm: HTMLFormElement | undefined;
let submitHandler: ((event: SubmitEvent) => void) | undefined;

function readFormValues(form: HTMLFormElement): ContactFormValues {
  const data = new FormData(form);
  const read = (field: ContactField) => String(data.get(field) ?? "");

  return {
    first_name: read("first_name"),
    last_name: read("last_name"),
    email: read("email"),
    phone: read("phone"),
    subject: read("subject"),
    message: read("message"),
  };
}

function clearFieldErrors(form: HTMLFormElement) {
  form.querySelectorAll<HTMLElement>("[data-error-for]").forEach((element) => {
    element.textContent = "";
    element.hidden = true;
  });
  form
    .querySelectorAll<
      HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    >("[aria-invalid='true']")
    .forEach((field) => {
      field.removeAttribute("aria-invalid");
    });
}

function showFieldErrors(
  form: HTMLFormElement,
  errors: Partial<Record<ContactField, string>>,
) {
  for (const [name, message] of Object.entries(errors) as [
    ContactField,
    string,
  ][]) {
    const field = form.elements.namedItem(name);
    const error = form.querySelector<HTMLElement>(`[data-error-for="${name}"]`);
    if (!field || !error) continue;

    if (field instanceof HTMLElement) {
      field.setAttribute("aria-invalid", "true");
      field.setAttribute("aria-describedby", error.id);
    }
    error.textContent = message;
    error.hidden = false;
  }
}

function showFormAlert(form: HTMLFormElement, message: string) {
  const alert = form.querySelector<HTMLElement>(".form-alert");
  if (!alert) return;
  alert.textContent = message;
  alert.hidden = false;
}

async function handleSubmit(event: SubmitEvent) {
  event.preventDefault();
  const form = event.currentTarget;
  if (!(form instanceof HTMLFormElement)) return;

  clearFieldErrors(form);
  const alert = form.querySelector<HTMLElement>(".form-alert");
  if (alert) {
    alert.textContent = "";
    alert.hidden = true;
  }

  const values = readFormValues(form);
  const errors = validateContactForm(values);
  if (Object.keys(errors).length) {
    showFieldErrors(form, errors);
    form.querySelector<HTMLElement>("[aria-invalid='true']")?.focus();
    return;
  }

  const button = form.querySelector<HTMLButtonElement>(".btn-submit");
  if (button) button.disabled = true;
  form.setAttribute("aria-busy", "true");

  const result = await sendContactMessage(String(apiBase ?? ""), values);

  form.removeAttribute("aria-busy");
  if (button) button.disabled = false;

  if (result.status === "success") {
    form.hidden = true;
    const success =
      form.parentElement?.querySelector<HTMLElement>(".form-success");
    const successMessage = success?.querySelector<HTMLElement>(".success-sub");
    if (successMessage) successMessage.textContent = result.message;
    success?.classList.add("active");
    success?.focus();
    return;
  }

  if (result.status === "invalid") {
    showFieldErrors(form, result.errors);
    const firstInvalid = form.querySelector<HTMLElement>(
      "[aria-invalid='true']",
    );
    if (firstInvalid) firstInvalid.focus();
    if (Object.keys(result.errors).length === 0)
      showFormAlert(form, result.message);
    return;
  }

  showFormAlert(form, result.message);
}

onMounted(() => {
  const scope = document.querySelector<HTMLElement>(".contact-page");
  contactForm = scope?.querySelector<HTMLFormElement>("#contact-form");
  submitHandler = handleSubmit;
  contactForm?.addEventListener("submit", submitHandler);

  const animatedElements = scope?.querySelectorAll<HTMLElement>(
    ".reveal, .reveal-l, .reveal-r",
  );
  if (!animatedElements) return;

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    animatedElements.forEach((element) => element.classList.add("in"));
    return;
  }

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("in");
          observer?.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -32px 0px" },
  );
  animatedElements.forEach((element) => observer?.observe(element));
});

onBeforeUnmount(() => {
  observer?.disconnect();
  if (contactForm && submitHandler) {
    contactForm.removeEventListener("submit", submitHandler);
  }
});
</script>

<template>
  <div class="contact-page">
    <!-- eslint-disable-next-line vue/no-v-html -- Static repository markup; user content is inserted with textContent. -->
    <div v-html="contactMarkup" />
  </div>
</template>
