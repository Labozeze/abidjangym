<script setup lang="ts">
import faqMarkdown from "~/content/faq.md?raw";
import { parseFaqMarkdown } from "~/utils/faq";

const faqItems = parseFaqMarkdown(faqMarkdown);
const siteUrl = "https://abidjangym.com/";
const pageTitle = "Logiciel de gestion de salle de sport en Côte d’Ivoire";
const pageDescription =
  "Gérez les abonnés, abonnements, paiements et accès de votre salle de sport en Côte d’Ivoire avec AbidjanGym. Découvrez les formules et inscrivez votre salle.";
const socialImage = `${siteUrl}images/landing-manager.jpg`;

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

const features = [
  {
    title: "Suivi des abonnés",
    description:
      "Retrouvez les fiches de vos abonnés et les informations utiles à leur accueil.",
    icon: "members",
  },
  {
    title: "Abonnements",
    description:
      "Créez et renouvelez les abonnements, puis suivez leurs dates d’expiration.",
    icon: "calendar",
  },
  {
    title: "Paiements",
    description:
      "Enregistrez les paiements selon les moyens acceptés par votre salle.",
    icon: "payments",
  },
  {
    title: "Contrôle d’accès par QR code",
    description:
      "Scannez le code personnel d’un abonné pour vérifier son accès à la salle.",
    icon: "qr",
  },
  {
    title: "Rappels d’expiration",
    description:
      "Informez vos abonnés avant la fin de leur abonnement par WhatsApp et SMS.",
    icon: "bell",
  },
  {
    title: "Reçus",
    description:
      "Téléchargez les reçus PDF depuis le navigateur et imprimez-les si besoin.",
    icon: "receipt",
  },
];

const plans = [
  { name: "Starter", price: "Gratuit", period: "", featured: false },
  {
    name: "Essentiel",
    price: "15 000 FCFA",
    period: "par mois",
    featured: true,
  },
  {
    name: "Pro",
    price: "35 000 FCFA",
    period: "par mois",
    featured: false,
  },
];
</script>

<template>
  <div class="home-page">
    <!-- 1. Navigation -->
    <!-- 2. Hero -->
    <section id="accueil" class="hero" aria-labelledby="hero-title">
      <div class="hero-decoration hero-decoration-one" aria-hidden="true"></div>
      <div class="hero-decoration hero-decoration-two" aria-hidden="true"></div>
      <div class="hero-inner">
        <div class="hero-content">
          <p class="eyebrow hero-eyebrow">
            Pour les salles de sport en Côte d’Ivoire
          </p>
          <h1 id="hero-title">
            La gestion de votre salle,<br />
            <span>plus simple au quotidien.</span>
          </h1>
          <p class="hero-copy">
            Abonnés, abonnements, paiements et accès réunis dans un même outil
            pensé pour les gérants de salle.
          </p>
          <div class="hero-actions">
            <a class="button" href="/inscription"
              >J’inscris ma salle <span aria-hidden="true">→</span></a
            >
            <a class="button button-secondary" href="#fonctionnalites"
              >Découvrir AbidjanGym</a
            >
          </div>
          <p class="hero-trial">
            2 mois d’essai gratuit sur les formules payantes, sans carte
            bancaire.
          </p>
        </div>

        <div class="hero-visual">
          <picture>
            <source srcset="/images/landing-manager.webp" type="image/webp" />
            <img
              class="hero-image"
              src="/images/landing-manager.jpg"
              alt="Gérant de salle consultant une tablette au sein de son club"
              width="960"
              height="960"
              fetchpriority="high"
              decoding="async"
            />
          </picture>
          <div class="hero-image-caption">
            <span class="caption-mark" aria-hidden="true">✓</span>
            <span
              ><strong>AbidjanGym</strong
              ><small>La gestion de votre salle</small></span
            >
          </div>
          <span class="hero-orbit hero-orbit-one" aria-hidden="true"></span>
          <span class="hero-orbit hero-orbit-two" aria-hidden="true"></span>
        </div>
      </div>
    </section>

    <!-- 3. Bande de bénéfices sans chiffres ni preuves sociales inventées -->
    <section
      class="benefit-band"
      aria-label="Les essentiels de la gestion d’une salle"
    >
      <p>Les essentiels de votre salle, réunis au même endroit</p>
      <ul>
        <li><span aria-hidden="true">✓</span> Abonnés et abonnements</li>
        <li><span aria-hidden="true">✓</span> Paiements et reçus</li>
        <li><span aria-hidden="true">✓</span> Accès par QR code</li>
      </ul>
    </section>

    <!-- 4. Six fonctionnalités -->
    <section
      id="fonctionnalites"
      class="section features-section"
      aria-labelledby="features-title"
    >
      <div class="section-heading">
        <p class="eyebrow">Fonctionnalités</p>
        <h2 id="features-title">Les outils pour gérer votre salle</h2>
        <p>
          Organisez les tâches courantes de votre salle depuis un espace de
          gestion unique.
        </p>
      </div>
      <ul class="feature-grid">
        <li
          v-for="feature in features"
          :key="feature.number"
          class="feature-card"
        >
          <div class="feature-card-top">
            <span class="feature-icon" aria-hidden="true">
              <svg
                v-if="feature.icon === 'members'"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-8a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-7.7a4 4 0 0 1 0 7.4m4 8.3v-1.5a3.5 3.5 0 0 0-2.5-3.35"
                />
              </svg>
              <svg
                v-else-if="feature.icon === 'calendar'"
                viewBox="0 0 24 24"
                fill="none"
              >
                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path d="M16 3v4M8 3v4M3 10h18m-13 4h3m-3 3h7" />
              </svg>
              <svg
                v-else-if="feature.icon === 'payments'"
                viewBox="0 0 24 24"
                fill="none"
              >
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="M3 10h18m-13 5h4" />
              </svg>
              <svg
                v-else-if="feature.icon === 'qr'"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h2m2 0h2m-6 3h2m2 3h2m0-6v2"
                />
              </svg>
              <svg
                v-else-if="feature.icon === 'bell'"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4"
                />
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none">
                <path d="M6 3h9l4 4v14H6zM14 3v5h5m-9 4h5m-5 4h5m-5 4h3" />
              </svg>
            </span>
          </div>
          <h3>{{ feature.title }}</h3>
          <p>{{ feature.description }}</p>
        </li>
      </ul>
    </section>

    <!-- 5. Pour les gérants -->
    <section
      id="pour-gerants"
      class="owner-section"
      aria-labelledby="owner-title"
    >
      <div class="owner-inner">
        <div class="owner-visual">
          <picture>
            <source srcset="/images/landing-access.webp" type="image/webp" />
            <img
              src="/images/landing-access.jpg"
              alt="Employée d’une salle consultant un téléphone à l’accueil"
              width="960"
              height="960"
              loading="lazy"
              decoding="async"
            />
          </picture>
          <div class="owner-image-note">
            <span aria-hidden="true">⌁</span> Accueil et accès des membres
          </div>
        </div>
        <div class="owner-copy">
          <p class="eyebrow">Pour les gérants</p>
          <h2 id="owner-title">Gardez le fil de votre activité</h2>
          <p class="owner-intro">
            Retrouvez les informations de votre salle et organisez le travail de
            votre équipe dans un espace réservé à votre établissement.
          </p>
          <ul class="owner-list">
            <li>
              <span aria-hidden="true">✓</span
              ><span
                ><strong>Suivez vos abonnements et paiements</strong
                ><small
                  >Consultez les informations enregistrées pour votre
                  salle.</small
                ></span
              >
            </li>
            <li>
              <span aria-hidden="true">✓</span
              ><span
                ><strong>Définissez les accès de votre équipe</strong
                ><small
                  >Le rôle caissier permet les opérations courantes sans accès
                  aux paramètres ni aux rapports.</small
                ></span
              >
            </li>
            <li>
              <span aria-hidden="true">✓</span
              ><span
                ><strong>Contrôlez les entrées par QR code</strong
                ><small
                  >Utilisez la caméra d’un téléphone ou d’une tablette pour
                  vérifier un abonnement.</small
                ></span
              >
            </li>
          </ul>
          <a class="text-link" href="/inscription"
            >Créer l’espace de ma salle <span aria-hidden="true">→</span></a
          >
        </div>
      </div>
    </section>

    <!-- 6. Tarifs; les détails non communiqués restent dans ce commentaire. -->
    <!-- [À FOURNIR] Avantages et limites détaillés des formules Starter, Essentiel et Pro. -->
    <section
      id="tarifs"
      class="section pricing-section"
      aria-labelledby="pricing-title"
    >
      <div class="section-heading section-heading-centered">
        <p class="eyebrow">Tarifs</p>
        <h2 id="pricing-title">Choisissez votre formule</h2>
        <p>Des tarifs mensuels affichés en francs CFA.</p>
      </div>
      <div class="pricing-grid">
        <article
          v-for="plan in plans"
          :key="plan.name"
          class="pricing-card"
          :class="{ 'pricing-card-featured': plan.featured }"
        >
          <span v-if="plan.name !== 'Starter'" class="plan-badge"
            >2 mois d’essai gratuit</span
          >
          <h3>{{ plan.name }}</h3>
          <p class="plan-price">{{ plan.price }}</p>
          <p v-if="plan.period" class="plan-period">{{ plan.period }}</p>
          <p v-else class="plan-period">Sans frais mensuels</p>
          <a
            class="button"
            :class="{ 'button-secondary': !plan.featured }"
            href="/inscription"
          >
            Choisir {{ plan.name }}
          </a>
        </article>
      </div>
      <p class="trial-note">
        Les formules Essentiel et Pro comprennent deux mois d’essai gratuit,
        sans carte bancaire.
      </p>
      <a class="pricing-more" href="/tarifs"
        >Consulter la page Tarifs <span aria-hidden="true">→</span></a
      >
    </section>

    <!-- 7. Aucun témoignage publié avant d’avoir de vrais retours validés. -->

    <!-- 8. FAQ issue du fichier éditorial validé -->
    <section id="faq" class="faq-section" aria-labelledby="faq-title">
      <div class="faq-inner">
        <div class="section-heading">
          <p class="eyebrow">Questions fréquentes</p>
          <h2 id="faq-title">Vous souhaitez en savoir plus ?</h2>
          <p>Les réponses aux questions que se posent les gérants de salle.</p>
        </div>
        <div class="faq-list">
          <details
            v-for="(item, index) in faqItems"
            :key="item.question"
            class="faq-item"
          >
            <summary :id="`faq-question-${index + 1}`">
              <span>{{ item.question }}</span>
              <span class="faq-toggle" aria-hidden="true"></span>
            </summary>
            <div class="faq-answer">
              <p>{{ item.answer }}</p>
            </div>
          </details>
        </div>
      </div>
    </section>

    <!-- 9. CTA final -->
    <section class="final-cta" aria-labelledby="final-cta-title">
      <div class="final-cta-inner">
        <p class="eyebrow">AbidjanGym</p>
        <h2 id="final-cta-title">Prêt à mieux organiser votre salle ?</h2>
        <p>
          Commencez l’inscription de votre salle et choisissez votre formule.
        </p>
        <a class="button button-light" href="/inscription"
          >J’inscris ma salle <span aria-hidden="true">→</span></a
        >
      </div>
    </section>

    <!-- 10. Pied de page -->
  </div>
</template>
