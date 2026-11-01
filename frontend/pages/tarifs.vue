<script setup lang="ts">
import pricingBody from "~/content/pricing-body.html?raw";

const siteUrl = "https://abidjangym.com";
const pageTitle = "Tarifs AbidjanGym — Prix et formules pour votre salle";
const pageDescription =
  "Comparez les offres AbidjanGym : Starter gratuite, Essentiel à 15 000 FCFA/mois et Pro à 35 000 FCFA/mois, avec 2 mois d’essai gratuit.";
const pricingMarkup = pricingBody;

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
          name: "Tarifs",
          item: `${siteUrl}/tarifs`,
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
  ogImage: `${siteUrl}/images/landing-hero.jpg`,
  ogUrl: `${siteUrl}/tarifs`,
  ogType: "website",
  twitterCard: "summary_large_image",
  twitterTitle: pageTitle,
  twitterDescription: pageDescription,
  twitterImage: `${siteUrl}/images/landing-hero.jpg`,
});

useHead({
  link: [
    { rel: "canonical", href: `${siteUrl}/tarifs` },
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

onMounted(() => {
  const animatedElements = document.querySelectorAll<HTMLElement>(
    ".tarifs-page .reveal, .tarifs-page .reveal-l, .tarifs-page .reveal-r",
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

onBeforeUnmount(() => observer?.disconnect());
</script>

<template>
  <div class="tarifs-page">
    <a class="skip-link" href="#main-content">Aller au contenu principal</a>
    <nav id="nav" aria-label="Navigation principale">
      <div class="nav-inner">
        <a href="/#accueil" class="nav-logo" aria-label="Accueil AbidjanGym">
          <div class="logo-mark" aria-hidden="true">
            <svg
              width="20"
              height="20"
              fill="none"
              stroke="white"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                d="M6 4v16M18 4v16M6 8h2M16 8h2M6 16h2M16 16h2M8 8h8M8 16h8"
              />
            </svg>
          </div>
          <span class="logo-name">Abidjan<em>Gym</em></span>
        </a>
        <div class="nav-links">
          <a href="/#fonctionnalites">Fonctionnalités</a>
          <a href="/tarifs" class="active" aria-current="page">Tarifs</a>
          <a href="/#faq">FAQ</a>
          <a href="/contact">Contact</a>
        </div>
        <div class="nav-cta">
          <a href="/#apercu" class="btn-ghost-nav">Voir la démo</a>
          <a href="/inscription" class="btn-accent-nav">J’inscris ma salle</a>
        </div>
      </div>
    </nav>

    <main id="main-content">
      <header class="page-hero">
        <div class="page-hero-inner">
          <div class="eyebrow reveal">Tarifs</div>
          <h1 class="page-title reveal">
            Nos formules<br /><em>pour votre salle</em>
          </h1>
          <p class="page-sub reveal">
            2 mois d’essai gratuit sur les formules payantes, sans carte
            bancaire.
          </p>
        </div>
      </header>

      <!-- eslint-disable-next-line vue/no-v-html -- Markup statique de la section tarifs validée. -->
      <div v-html="pricingMarkup" />
    </main>

    <footer class="public-page-footer">
      <div class="footer-inner">
        <div class="footer-brand-row">
          <div class="footer-logo-mark" aria-hidden="true">
            <svg
              width="17"
              height="17"
              fill="none"
              stroke="white"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                d="M6 4v16M18 4v16M6 8h2M16 8h2M6 16h2M16 16h2M8 8h8M8 16h8"
              />
            </svg>
          </div>
          <span class="footer-brand-name">Abidjan<em>Gym</em></span>
        </div>
        <p class="footer-tagline">
          La plateforme de gestion de salles de sport pensée pour l’Afrique de
          l’Ouest.
        </p>
        <div class="footer-links">
          <div>
            <h2>Produit</h2>
            <a href="/#fonctionnalites">Fonctionnalités</a>
            <a href="/tarifs">Tarifs</a>
            <a href="/contact">Contact</a>
          </div>
          <div>
            <h2>Légal</h2>
            <a href="/confidentialite">Confidentialité</a>
            <a href="/cgu">Conditions d’utilisation</a>
            <a href="/cookies">Cookies</a>
            <a href="/mentions-legales">Mentions légales</a>
          </div>
        </div>
        <div class="footer-bottom">
          <span>© 2026 AbidjanGym · Le Labo de Zézé, Abidjan 🇨🇮</span>
          <a href="#main-content">Retour en haut</a>
        </div>
      </div>
    </footer>
  </div>
</template>
