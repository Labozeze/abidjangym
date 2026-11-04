import{d as q,w as C,o as z,c as E,B as S,C as O,l as T,A as B}from"./CVhH7h8S.js";import{a as F,u as I}from"./D6I813Mh.js";const $=`<a class="skip-link" href="#main-content">Aller au contenu principal</a>
<!-- NAV -->
<nav aria-label="Navigation principale">
  <div class="nav-inner">
    <a href="/" class="nav-logo" aria-label="Accueil AbidjanGym">
      <div class="logo-mark">
        <svg
          width="20"
          height="20"
          fill="none"
          stroke="white"
          stroke-width="2.5"
          viewBox="0 0 24 24"
        >
          <path d="M6 4v16M18 4v16M6 8h2M16 8h2M6 16h2M16 16h2M8 8h8M8 16h8" />
        </svg>
      </div>
      <span class="logo-name">Abidjan<em>Gym</em></span>
    </a>
    <div class="nav-links">
      <a href="/#fonctionnalites">Fonctionnalités</a>
      <a href="/tarifs">Tarifs</a>
      <a href="/#faq">FAQ</a>
      <a href="/contact" class="active">Contact</a>
    </div>
    <div class="nav-cta">
      <a href="/#apercu" class="btn-ghost-nav">Voir la démo</a>
      <a href="/inscription" class="btn-accent-nav">J'inscris ma salle</a>
    </div>
  </div>
</nav>

<main id="main-content">
  <!-- PAGE HERO -->
  <header class="page-hero">
    <div class="page-hero-inner">
      <div class="eyebrow reveal">Contact</div>
      <h1 class="page-title reveal">Parlons de<br /><em>votre salle</em></h1>
      <p class="page-sub reveal">
        Notre équipe est basée à Abidjan. On vous répond en moins de 24h sur
        WhatsApp ou par email.
      </p>
    </div>
  </header>

  <!-- CONTACT SECTION -->
  <section class="contact-section">
    <div class="contact-grid">
      <!-- FORM -->
      <div class="contact-form-wrap reveal-l">
        <form id="contact-form" novalidate>
          <div class="form-head">
            <h2>Envoyez-nous un message</h2>
            <p>Démo, partenariat, support technique — on est là.</p>
          </div>
          <div class="form-alert" role="alert" aria-live="polite" hidden></div>
          <div class="field-row">
            <div class="field">
              <label for="first_name">Prénom</label>
              <input
                class="inp"
                id="first_name"
                name="first_name"
                type="text"
                placeholder="Konan"
                autocomplete="given-name"
                maxlength="60"
                required
              />
              <span
                class="field-error"
                id="error-first_name"
                data-error-for="first_name"
                hidden
              ></span>
            </div>
            <div class="field">
              <label for="last_name">Nom</label>
              <input
                class="inp"
                id="last_name"
                name="last_name"
                type="text"
                placeholder="Bamba"
                autocomplete="family-name"
                maxlength="60"
                required
              />
              <span
                class="field-error"
                id="error-last_name"
                data-error-for="last_name"
                hidden
              ></span>
            </div>
          </div>
          <div class="field">
            <label for="email">Email</label>
            <input
              class="inp"
              id="email"
              name="email"
              type="email"
              placeholder="visiteur@example.com"
              autocomplete="email"
              maxlength="254"
              required
            />
            <span
              class="field-error"
              id="error-email"
              data-error-for="email"
              hidden
            ></span>
          </div>
          <div class="field">
            <label for="phone">Téléphone / WhatsApp</label>
            <input
              class="inp"
              id="phone"
              name="phone"
              type="tel"
              placeholder="+225 07 00 00 00 01"
              autocomplete="tel"
              inputmode="tel"
              aria-describedby="error-phone"
            />
            <span
              class="field-error"
              id="error-phone"
              data-error-for="phone"
              hidden
            ></span>
          </div>
          <div class="field">
            <label for="subject">Objet</label>
            <select
              class="inp"
              id="subject"
              name="subject"
              required
              aria-describedby="error-subject"
            >
              <option value="">Choisir un sujet...</option>
              <option value="demo">Demande de démo</option>
              <option value="tarif">Question sur les tarifs</option>
              <option value="support">Support technique</option>
              <option value="partenariat">Partenariat</option>
              <option value="autre">Autre</option>
            </select>
            <span
              class="field-error"
              id="error-subject"
              data-error-for="subject"
              hidden
            ></span>
          </div>
          <div class="field">
            <label for="message">Message</label>
            <textarea
              class="inp"
              id="message"
              name="message"
              placeholder="Décrivez votre salle, vos besoins, vos questions..."
              maxlength="5000"
              required
              aria-describedby="error-message"
            ></textarea>
            <span
              class="field-error"
              id="error-message"
              data-error-for="message"
              hidden
            ></span>
          </div>
          <button class="btn-submit" type="submit">
            <svg
              aria-hidden="true"
              width="16"
              height="16"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <line x1="22" y1="2" x2="11" y2="13" />
              <polygon points="22 2 15 22 11 13 2 9 22 2" />
            </svg>
            Envoyer le message
          </button>
        </form>
        <div class="form-success" id="form-success" tabindex="-1">
          <div class="success-icon">
            <svg
              width="28"
              height="28"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <div class="success-title">Message envoyé !</div>
          <p class="success-sub">
            Merci, on vous revient dans les 24h. Pour une réponse plus rapide,
            écrivez-nous directement sur <strong>WhatsApp</strong>.
          </p>
        </div>
      </div>

      <!-- SIDEBAR -->
      <div class="contact-info">
        <!-- Photo -->
        <div class="photo-card reveal-r">
          <picture
            ><source srcset="/images/landing-owner.webp" type="image/webp" />
            <img
              src="/images/landing-owner.jpg"
              alt="Gérant de salle consultant son téléphone dans son établissement"
              loading="lazy"
              decoding="async"
          /></picture>
          <div class="photo-card-overlay"></div>
          <div class="photo-card-text">
            <p>Notre équipe à Abidjan</p>
            <span>Toujours disponibles sur WhatsApp</span>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="info-card reveal-r" style="transition-delay: 0.08s">
          <div
            class="info-card-icon"
            aria-hidden="true"
            style="background: rgba(22, 163, 74, 0.1)"
          >
            <svg
              width="22"
              height="22"
              fill="none"
              stroke="#16A34A"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
              />
            </svg>
          </div>
          <h3>WhatsApp — réponse rapide</h3>
          <p>
            Écrivez-nous directement. Disponibles du lundi au samedi, 8h–20h
            (heure d'Abidjan).
          </p>
          <!-- [À FOURNIR] Numéro WhatsApp officiel (#13). -->
          <button
            class="info-link contact-whatsapp-disabled"
            type="button"
            style="color: var(--ok)"
            disabled
            aria-disabled="true"
          >
            <svg
              width="13"
              height="13"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
              />
            </svg>
            Ouvrir WhatsApp →
          </button>
        </div>

        <!-- Email -->
        <div class="info-card reveal-r" style="transition-delay: 0.16s">
          <div
            class="info-card-icon"
            aria-hidden="true"
            style="background: var(--accent-soft)"
          >
            <svg
              width="22"
              height="22"
              fill="none"
              stroke="var(--accent)"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
              />
              <polyline points="22,6 12,13 2,6" />
            </svg>
          </div>
          <h3>Email</h3>
          <p>
            Pour les demandes de partenariat, devis détaillé ou questions
            administratives.
          </p>
          <a class="info-link" href="mailto:contact@abidjangym.com">
            contact@abidjangym.com
          </a>
        </div>

        <!-- Adresse -->
        <div class="info-card reveal-r" style="transition-delay: 0.24s">
          <div
            class="info-card-icon"
            aria-hidden="true"
            style="background: rgba(99, 102, 241, 0.09)"
          >
            <svg
              width="22"
              height="22"
              fill="none"
              stroke="#6366F1"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
          </div>
          <h3>Abidjan, Côte d'Ivoire 🇨🇮</h3>
          <p>
            Cocody, Abidjan. On se déplace aussi dans toute la ville pour les
            démos sur site.
          </p>
          <span class="info-link">Cocody, Abidjan CI</span>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-brand-row">
      <div class="footer-logo-mark">
        <svg
          width="17"
          height="17"
          fill="none"
          stroke="white"
          stroke-width="2.5"
          viewBox="0 0 24 24"
        >
          <path d="M6 4v16M18 4v16M6 8h2M16 8h2M6 16h2M16 16h2M8 8h8M8 16h8" />
        </svg>
      </div>
      <span class="footer-brand-name">Abidjan<em>Gym</em></span>
    </div>
    <p class="footer-tagline">
      La plateforme de gestion de salles de sport pensée pour l'Afrique de
      l'Ouest.
    </p>
    <div class="footer-bottom">
      <span>© 2026 AbidjanGym · Le Labo de Zézé, Abidjan 🇨🇮</span>
      <!-- [À FOURNIR] Liens des comptes sociaux officiels. -->
    </div>
  </div>
</footer>
`,_={first_name:"Le prénom",last_name:"Le nom",email:"L’adresse email",phone:"Le numéro de téléphone",subject:"L’objet",message:"Le message"};function N(n){return n.trim().replace(/[\s().-]/g,"")}function M(n){const s=N(n.phone);return{first_name:n.first_name.trim(),last_name:n.last_name.trim(),email:n.email.trim(),...s?{phone:s}:{},subject:n.subject.trim(),message:n.message.trim()}}function k(n){const s=M(n),t={};for(const i of["first_name","last_name","email","subject","message"])s[i]||(t[i]=`${_[i]} est obligatoire.`);return s.first_name.length>60&&(t.first_name="Le prénom ne peut pas dépasser 60 caractères."),s.last_name.length>60&&(t.last_name="Le nom ne peut pas dépasser 60 caractères."),s.email&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s.email)&&(t.email="Saisissez une adresse email valide."),s.email.length>254&&(t.email="L’adresse email ne peut pas dépasser 254 caractères."),s.phone&&!/^\+[1-9][0-9]{7,14}$/.test(s.phone)&&(t.phone="Saisissez un numéro au format international, par exemple +2250700000001."),s.subject.length>150&&(t.subject="L’objet ne peut pas dépasser 150 caractères."),s.message.length>5e3&&(t.message="Le message ne peut pas dépasser 5 000 caractères."),t}function j(n){return typeof n=="object"&&n!==null}function g(n,s){return j(n)&&typeof n.message=="string"&&n.message.trim()?n.message:s}function R(n){if(!j(n)||!j(n.errors))return{};const s={};for(const t of Object.keys(_)){const i=n.errors[t];typeof i=="string"?s[t]=i:Array.isArray(i)&&typeof i[0]=="string"&&(s[t]=i[0])}return s}async function D(n,s,t=fetch){const i=M(s),p=k(s);if(Object.keys(p).length)return{status:"invalid",message:"Vérifiez les champs indiqués puis réessayez.",errors:p};const u=n.trim().replace(/\/+$/,"");if(!u)return{status:"error",message:"Le formulaire est momentanément indisponible. Réessayez plus tard."};try{const l=await t(`${u}/contact-messages`,{method:"POST",headers:{Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(i)}),m=await l.json().catch(()=>null);return l.status===201?{status:"success",message:g(m,"Merci, votre message a bien été envoyé. Notre équipe vous répond en moins de 24 h.")}:l.status===422?{status:"invalid",message:g(m,"Vérifiez les champs indiqués puis réessayez."),errors:R(m)}:l.status===429?{status:"rate-limited",message:g(m,"Trop de messages ont été envoyés. Réessayez dans une minute.")}:{status:"error",message:g(m,"Une erreur est survenue lors de l’envoi. Réessayez plus tard.")}}catch{return{status:"error",message:"Impossible d’envoyer votre message. Vérifiez votre connexion puis réessayez."}}}const H={class:"contact-page"},V=["innerHTML"],d="https://abidjangym.com",b="Contact AbidjanGym — Parlons de votre salle à Abidjan",y="Contactez AbidjanGym pour une démo, un partenariat ou du support. Décrivez votre demande dans le formulaire ; notre équipe vous répond en moins de 24 h.",W=q({__name:"contact",setup(n){const s=$,t=C().public.apiBase,i={"@context":"https://schema.org","@graph":[{"@type":"Organization",name:"AbidjanGym",url:`${d}/`,logo:`${d}/icons/icon-512.png`},{"@type":"WebSite",name:"AbidjanGym",url:`${d}/`,inLanguage:"fr-CI"},{"@type":"BreadcrumbList",itemListElement:[{"@type":"ListItem",position:1,name:"Accueil",item:`${d}/`},{"@type":"ListItem",position:2,name:"Contact",item:`${d}/contact`}]}]};F({title:b,description:y,ogTitle:b,ogDescription:y,ogImage:`${d}/images/landing-owner.jpg`,ogUrl:`${d}/contact`,ogType:"website",twitterCard:"summary_large_image",twitterTitle:b,twitterDescription:y,twitterImage:`${d}/images/landing-owner.jpg`}),I({link:[{rel:"canonical",href:`${d}/contact`},{rel:"icon",href:"/favicon.ico",sizes:"any"},{rel:"icon",href:"/icons/icon-192.png",type:"image/png"},{rel:"apple-touch-icon",href:"/icons/apple-touch-icon.png"},{rel:"manifest",href:"/manifest.json"}],script:[{type:"application/ld+json",innerHTML:JSON.stringify(i)}]});let p,u,l;function m(r){const e=new FormData(r),a=o=>String(e.get(o)??"");return{first_name:a("first_name"),last_name:a("last_name"),email:a("email"),phone:a("phone"),subject:a("subject"),message:a("message")}}function x(r){r.querySelectorAll("[data-error-for]").forEach(e=>{e.textContent="",e.hidden=!0}),r.querySelectorAll("[aria-invalid='true']").forEach(e=>{e.removeAttribute("aria-invalid")})}function w(r,e){for(const[a,o]of Object.entries(e)){const f=r.elements.namedItem(a),c=r.querySelector(`[data-error-for="${a}"]`);!f||!c||(f instanceof HTMLElement&&(f.setAttribute("aria-invalid","true"),f.setAttribute("aria-describedby",c.id)),c.textContent=o,c.hidden=!1)}}function A(r,e){const a=r.querySelector(".form-alert");a&&(a.textContent=e,a.hidden=!1)}async function L(r){r.preventDefault();const e=r.currentTarget;if(!(e instanceof HTMLFormElement))return;x(e);const a=e.querySelector(".form-alert");a&&(a.textContent="",a.hidden=!0);const o=m(e),f=k(o);if(Object.keys(f).length){w(e,f),e.querySelector("[aria-invalid='true']")?.focus();return}const c=e.querySelector(".btn-submit");c&&(c.disabled=!0),e.setAttribute("aria-busy","true");const h=await D(String(t??""),o);if(e.removeAttribute("aria-busy"),c&&(c.disabled=!1),h.status==="success"){e.hidden=!0;const v=e.parentElement?.querySelector(".form-success");v?.classList.add("active"),v?.focus();return}if(h.status==="invalid"){w(e,h.errors);const v=e.querySelector("[aria-invalid='true']");v&&v.focus(),Object.keys(h.errors).length===0&&A(e,h.message);return}A(e,h.message)}return z(()=>{const r=document.querySelector(".contact-page");u=r?.querySelector("#contact-form"),l=L,u?.addEventListener("submit",l);const e=r?.querySelectorAll(".reveal, .reveal-l, .reveal-r");if(e){if(window.matchMedia("(prefers-reduced-motion: reduce)").matches){e.forEach(a=>a.classList.add("in"));return}p=new IntersectionObserver(a=>{a.forEach(o=>{o.isIntersecting&&(o.target.classList.add("in"),p?.unobserve(o.target))})},{threshold:.1,rootMargin:"0px 0px -32px 0px"}),e.forEach(a=>p?.observe(a))}}),E(()=>{p?.disconnect(),u&&l&&u.removeEventListener("submit",l)}),(r,e)=>(B(),S("div",H,[O("div",{innerHTML:T(s)},null,8,V)]))}});export{W as default};
