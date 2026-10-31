<script setup lang="ts">
const pageTitle = "AbidjanGym — La gestion de votre salle de sport à Abidjan";
const pageDescription =
  "Découvrez AbidjanGym, la plateforme de gestion pensée pour les salles de sport en Côte d’Ivoire : abonnements, paiements Mobile Money et suivi des membres.";
const siteUrl = "https://abidjangym.com/";
const structuredData = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      name: "AbidjanGym",
      url: siteUrl,
    },
    {
      "@type": "WebSite",
      name: "AbidjanGym",
      url: siteUrl,
    },
  ],
};

useSeoMeta({
  title: pageTitle,
  description: pageDescription,
  ogTitle: pageTitle,
  ogDescription: pageDescription,
  ogUrl: siteUrl,
  twitterCard: "summary",
});

useHead({
  link: [{ rel: "canonical", href: siteUrl }],
  script: [
    {
      type: "application/ld+json",
      innerHTML: JSON.stringify(structuredData),
    },
  ],
});
</script>
<template>
  <div class="home-page">
    <section id="presentation" class="hero" aria-labelledby="hero-title">
      <div class="hero-content">
        <p class="eyebrow">Conçu pour les salles de sport en Côte d’Ivoire</p>
        <h1 id="hero-title">Gérez votre salle de sport avec AbidjanGym</h1>
        <p class="hero-copy">
          Retrouvez au même endroit la gestion des abonnements, les paiements et
          le suivi des membres de votre salle.
        </p>
        <a class="button" href="/#fonctionnalites"
          >Découvrir les fonctionnalités</a
        >
      </div>
      <div class="hero-card" aria-hidden="true">
        <span class="hero-card-label">AbidjanGym</span>
        <span class="hero-card-accent" />
      </div>
    </section>

    <section
      id="fonctionnalites"
      class="features"
      aria-labelledby="features-title"
    >
      <p class="eyebrow">Une gestion plus simple</p>
      <h2 id="features-title">Les essentiels de votre salle, réunis</h2>
      <ul class="feature-list">
        <li>
          <h3>Abonnements</h3>
          <p>Suivez les formules et les périodes d’activité de vos membres.</p>
        </li>
        <li>
          <h3>Paiements</h3>
          <p>
            Consultez les paiements et les modes Mobile Money prévus pour
            AbidjanGym.
          </p>
        </li>
        <li>
          <h3>Accès</h3>
          <p>Préparez le suivi des entrées et le contrôle par QR code.</p>
        </li>
      </ul>
    </section>
  </div>
</template>
