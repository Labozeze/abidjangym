<script setup lang="ts">
import faqMarkdown from "~/content/faq.md?raw";
import landingBody from "~/content/landing-body.html?raw";
import { parseFaqMarkdown } from "~/utils/faq";

const faqItems = parseFaqMarkdown(faqMarkdown);
const siteUrl = "https://abidjangym.com/";
const pageTitle = "Logiciel de gestion de salle de sport en Côte d’Ivoire";
const pageDescription =
  "Gérez les abonnés, abonnements, paiements et accès de votre salle de sport en Côte d’Ivoire avec AbidjanGym. Découvrez les formules et inscrivez votre salle.";
const socialImage = `${siteUrl}images/landing-hero.jpg`;
const escapeHtml = (value: string) =>
  value.replace(/[&<>"']/g, (character) => {
    const entities: Record<string, string> = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    };

    return entities[character] ?? character;
  });

const faqMarkup = `<section id="faq" class="faq-section" aria-labelledby="faq-heading">
  <span class="eyebrow reveal">Questions fréquentes</span>
  <h2 id="faq-heading" class="section-h reveal">Vos questions,<br><em>nos réponses</em></h2>
  <div class="faq-list">
    ${faqItems
      .map(
        ({ question, answer }) => `<details class="faq-item reveal">
      <summary>${escapeHtml(question)}</summary>
      <p>${escapeHtml(answer)}</p>
    </details>`,
      )
      .join("\n    ")}
  </div>
</section>`;
const landingMarkup = landingBody.replace("<!-- FAQ_SECTION -->", faqMarkup);

const structuredData = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      name: "AbidjanGym",
      url: siteUrl,
      logo: `${siteUrl}icons/icon-512.png`,
    },
    {
      "@type": "WebSite",
      name: "AbidjanGym",
      url: siteUrl,
      inLanguage: "fr-CI",
    },
    {
      "@type": "FAQPage",
      mainEntity: faqItems.map(({ question, answer }) => ({
        "@type": "Question",
        name: question,
        acceptedAnswer: {
          "@type": "Answer",
          text: answer,
        },
      })),
    },
  ],
};

useSeoMeta({
  title: pageTitle,
  description: pageDescription,
  ogTitle: pageTitle,
  ogDescription: pageDescription,
  ogImage: socialImage,
  ogUrl: siteUrl,
  ogType: "website",
  twitterCard: "summary_large_image",
  twitterTitle: pageTitle,
  twitterDescription: pageDescription,
  twitterImage: socialImage,
});

useHead({
  link: [
    { rel: "canonical", href: siteUrl },
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
let navScrollHandler: (() => void) | undefined;

onMounted(() => {
  const nav = document.querySelector<HTMLElement>("#nav");
  navScrollHandler = () =>
    nav?.classList.toggle("scrolled", window.scrollY > 24);
  navScrollHandler();
  window.addEventListener("scroll", navScrollHandler, { passive: true });

  const animatedElements = document.querySelectorAll<HTMLElement>(
    ".reveal, .reveal-l, .reveal-r",
  );
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
    { threshold: 0.12 },
  );
  animatedElements.forEach((element) => observer?.observe(element));
});

onBeforeUnmount(() => {
  observer?.disconnect();
  if (navScrollHandler) window.removeEventListener("scroll", navScrollHandler);
});
</script>

<template>
  <div class="landing-page">
    <!-- eslint-disable-next-line vue/no-v-html -- Static repository markup; FAQ text is HTML-escaped. -->
    <div v-html="landingMarkup" />
  </div>
</template>
