import{d as G,o as W,c as U,B as D,l as J,A as Q,i as K,w as Z}from"./CVhH7h8S.js";import{a as Y,u as X}from"./D6I813Mh.js";const nn=`<div class="ob-layout">
  <!-- LEFT PANEL -->
  <aside class="ob-left">
    <a href="/" class="brand-mark">
      <div class="logo-icon">
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
      <span class="logo-name">Abidjan<span>Gym</span></span>
    </a>
    <h1 class="ob-headline">Votre salle en ligne en 5 minutes</h1>
    <p class="ob-sub">
      Commencez avec 2 mois d’essai gratuit sur les formules payantes, sans
      carte bancaire. Vous pouvez payer via Orange Money, MTN MoMo ou Wave.
    </p>
    <div class="perk-list">
      <div class="perk">
        <div class="perk-ic" style="background: rgba(255, 90, 30, 0.15)">
          <svg
            width="16"
            height="16"
            fill="none"
            stroke="var(--accent)"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <polyline points="20 6 9 17 4 12" />
          </svg>
        </div>
        <div class="perk-text">
          <strong>2 mois d'essai gratuit</strong>
          <span>Sans engagement, sans carte bancaire requise</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-ic" style="background: rgba(22, 163, 74, 0.15)">
          <svg
            width="16"
            height="16"
            fill="none"
            stroke="#16A34A"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
          </svg>
        </div>
        <div class="perk-text">
          <strong>QR Code activé immédiatement</strong>
          <span>Vos membres peuvent scanner dès aujourd'hui</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-ic" style="background: rgba(99, 102, 241, 0.15)">
          <svg
            width="16"
            height="16"
            fill="none"
            stroke="#6366F1"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <path
              d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
            />
          </svg>
        </div>
        <div class="perk-text">
          <strong>Notifications WhatsApp incluses</strong>
          <span>Rappels automatiques pour vos abonnés</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-ic" style="background: rgba(251, 191, 36, 0.15)">
          <svg
            width="16"
            height="16"
            fill="none"
            stroke="#F59E0B"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>
        <div class="perk-text">
          <strong>Données sécurisées</strong>
          <span>Hébergées en Côte d'Ivoire</span>
        </div>
      </div>
    </div>
    <div class="ob-bottom">
      <svg
        width="13"
        height="13"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        viewBox="0 0 24 24"
      >
        <rect x="3" y="11" width="18" height="11" rx="2" />
        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
      </svg>
      Connexion sécurisée · vos données restent privées
    </div>
  </aside>

  <!-- RIGHT PANEL -->
  <main class="ob-right">
    <form id="onboarding-form" novalidate>
      <div id="wizard-content">
        <div
          class="form-alert"
          id="form-alert"
          role="alert"
          aria-live="assertive"
          tabindex="-1"
          hidden
        ></div>
        <p
          class="sr-only"
          id="step-announcement"
          role="status"
          aria-live="polite"
        ></p>
        <!-- Stepper -->
        <div class="steps" role="list" aria-label="Étapes de l’inscription">
          <div
            class="step-item"
            role="listitem"
            data-step-item="1"
            aria-current="step"
          >
            <div class="step-dot active" id="dot-1" aria-hidden="true">1</div>
            <div class="step-label active" id="lbl-1">Votre salle</div>
          </div>
          <div class="step-line" id="line-1"></div>
          <div class="step-item" role="listitem" data-step-item="2">
            <div class="step-dot pending" id="dot-2" aria-hidden="true">2</div>
            <div class="step-label pending" id="lbl-2">Compte admin</div>
          </div>
          <div class="step-line" id="line-2"></div>
          <div class="step-item" role="listitem" data-step-item="3">
            <div class="step-dot pending" id="dot-3" aria-hidden="true">3</div>
            <div class="step-label pending" id="lbl-3">Formule</div>
          </div>
          <div class="step-line" id="line-3"></div>
          <div class="step-item" role="listitem" data-step-item="4">
            <div class="step-dot pending" id="dot-4" aria-hidden="true">4</div>
            <div class="step-label pending" id="lbl-4">Confirmation</div>
          </div>
        </div>

        <!-- STEP 1 — Infos salle -->
        <div
          class="step-panel active form-section"
          id="panel-1"
          role="group"
          aria-labelledby="panel-title-1"
        >
          <h2 class="form-title" id="panel-title-1" tabindex="-1">
            Votre salle de sport
          </h2>
          <div class="form-subtitle">
            Ces informations seront visibles par vos abonnés.
          </div>
          <div class="field-row">
            <div class="field">
              <label for="gym_name">Nom de la salle <span>*</span></label>
              <input
                class="inp"
                type="text"
                placeholder="ex: FitZone Abidjan"
                id="gym_name"
                name="gym_name"
                autocomplete="organization"
                required
                maxlength="80"
                aria-describedby="form-alert"
              />
            </div>
            <div class="field">
              <label for="gym_district"
                >Quartier / Commune <span>*</span></label
              >
              <select
                class="inp"
                id="gym_district"
                name="gym_district"
                required
                maxlength="80"
                aria-describedby="form-alert"
              >
                <option value="">Sélectionner...</option>
                <option>Cocody</option>
                <option>Marcory</option>
                <option>Yopougon</option>
                <option>Adjamé</option>
                <option>Plateau</option>
                <option>Treichville</option>
                <option>Abobo</option>
                <option>Koumassi</option>
                <option>Port-Bouët</option>
                <option>Bingerville</option>
                <option>Autre</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label for="gym_address">Adresse complète <span>*</span></label>
            <input
              class="inp"
              type="text"
              id="gym_address"
              name="gym_address"
              autocomplete="street-address"
              required
              maxlength="255"
              aria-describedby="form-alert"
              placeholder="ex: Rue des Jardins, face Total, Cocody"
            />
          </div>
          <div class="field-row">
            <div class="field">
              <label for="gym_phone">Téléphone principal <span>*</span></label>
              <div class="inp-prefix-wrap">
                <span class="inp-prefix">🇨🇮 +225</span>
                <input
                  class="inp"
                  type="tel"
                  placeholder="07 00 00 00 00"
                  id="gym_phone"
                  name="gym_phone"
                  autocomplete="tel-national"
                  required
                  inputmode="tel"
                  aria-describedby="form-alert"
                />
              </div>
            </div>
            <div class="field">
              <label for="gym_whatsapp"
                >WhatsApp (pour les notifs) <span>*</span></label
              >
              <div class="inp-prefix-wrap">
                <span class="inp-prefix">🇨🇮 +225</span>
                <input
                  class="inp"
                  id="gym_whatsapp"
                  name="gym_whatsapp"
                  type="tel"
                  autocomplete="tel-national"
                  required
                  inputmode="tel"
                  aria-describedby="form-alert"
                  placeholder="07 00 00 00 00"
                />
              </div>
            </div>
          </div>
          <div class="form-footer">
            <a href="/" class="btn-back">← Retour</a>
            <button class="btn-next" type="button" data-go-step="2">
              Continuer
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
              </svg>
            </button>
          </div>
        </div>

        <!-- STEP 2 — Compte admin -->
        <div
          class="step-panel form-section"
          id="panel-2"
          role="group"
          aria-labelledby="panel-title-2"
        >
          <h2 class="form-title" id="panel-title-2" tabindex="-1">
            Votre compte administrateur
          </h2>
          <div class="form-subtitle">
            Vous utiliserez ces identifiants pour vous connecter.
          </div>
          <div class="field-row">
            <div class="field">
              <label for="admin_first_name">Prénom <span>*</span></label>
              <input
                class="inp"
                id="admin_first_name"
                name="admin_first_name"
                type="text"
                autocomplete="given-name"
                required
                maxlength="60"
                aria-describedby="form-alert"
                placeholder="Jean-Marc"
              />
            </div>
            <div class="field">
              <label for="admin_last_name">Nom <span>*</span></label>
              <input
                class="inp"
                id="admin_last_name"
                name="admin_last_name"
                type="text"
                autocomplete="family-name"
                required
                maxlength="60"
                aria-describedby="form-alert"
                placeholder="Kouassi"
              />
            </div>
          </div>
          <div class="field">
            <label for="admin_email">Adresse email <span>*</span></label>
            <input
              class="inp"
              id="admin_email"
              name="admin_email"
              type="email"
              autocomplete="email"
              required
              maxlength="254"
              aria-describedby="form-alert email-hint"
              placeholder="jean@fitzoneabidjan.com"
            />
            <span class="field-hint" id="email-hint"
              >Vous recevrez votre lien de confirmation ici.</span
            >
          </div>
          <div class="field">
            <label for="admin_mobile_money_number"
              >Numéro Mobile Money (pour payer votre abonnement)
              <span>*</span></label
            >
            <div class="inp-prefix-wrap">
              <span class="inp-prefix">🇨🇮 +225</span>
              <input
                class="inp"
                id="admin_mobile_money_number"
                name="admin_mobile_money_number"
                type="tel"
                autocomplete="tel-national"
                required
                inputmode="tel"
                aria-describedby="form-alert mobile-money-hint"
                placeholder="07 00 00 00 00"
              />
            </div>
            <span class="field-hint" id="mobile-money-hint"
              >Orange Money, MTN MoMo ou Wave acceptés.</span
            >
          </div>
          <div class="field">
            <label for="admin_password">Mot de passe <span>*</span></label>
            <input
              class="inp"
              id="admin_password"
              name="admin_password"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
              maxlength="72"
              aria-describedby="form-alert password-hint"
              placeholder="Minimum 8 caractères"
            />
            <span class="field-hint" id="password-hint"
              >8 caractères minimum, avec une majuscule et un chiffre.</span
            >
          </div>
          <div class="field">
            <label for="admin_password_confirmation"
              >Confirmer le mot de passe <span>*</span></label
            >
            <input
              class="inp"
              id="admin_password_confirmation"
              name="admin_password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              minlength="8"
              maxlength="72"
              aria-describedby="form-alert"
              placeholder="Répétez le mot de passe"
            />
          </div>
          <div class="form-footer">
            <button class="btn-back" type="button" data-go-step="1">
              ← Retour
            </button>
            <button class="btn-next" type="button" data-go-step="3">
              Continuer
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
              </svg>
            </button>
          </div>
        </div>

        <!-- STEP 3 — Formule -->
        <div
          class="step-panel form-section"
          id="panel-3"
          role="group"
          aria-labelledby="panel-title-3"
        >
          <h2 class="form-title" id="panel-title-3" tabindex="-1">
            Choisissez votre formule
          </h2>
          <div class="form-subtitle">
            2 mois d’essai gratuit sur les formules payantes. Pas de CB requise.
          </div>
          <div class="plan-cards">
            <label class="plan-opt">
              <input
                type="radio"
                name="plan_code"
                class="plan-opt-radio"
                value="starter"
                required
                aria-label="Formule Starter"
              />
              <div class="plan-check"></div>
              <div class="plan-opt-name">Starter</div>
              <div class="plan-opt-price">0 <sub>FCFA/mois</sub></div>
              <div class="plan-opt-features">
                50 abonnés max · QR code · Mobile Money
              </div>
            </label>
            <label class="plan-opt selected">
              <input
                type="radio"
                name="plan_code"
                class="plan-opt-radio"
                value="essentiel"
                checked
                required
                aria-label="Formule Essentiel"
              />
              <div class="plan-check">
                <svg
                  width="10"
                  height="10"
                  fill="none"
                  stroke="white"
                  stroke-width="3"
                  viewBox="0 0 24 24"
                >
                  <polyline points="20 6 9 17 4 12" />
                </svg>
              </div>
              <div class="plan-opt-name">Essentiel ⭐</div>
              <div class="plan-opt-price">15 000 <sub>FCFA/mois</sub></div>
              <div class="plan-opt-features">
                Abonnés illimités · WhatsApp · Rapports
              </div>
            </label>
            <label class="plan-opt">
              <input
                type="radio"
                name="plan_code"
                class="plan-opt-radio"
                value="pro"
                required
                aria-label="Formule Pro"
              />
              <div class="plan-check"></div>
              <div class="plan-opt-name">Pro</div>
              <div class="plan-opt-price">35 000 <sub>FCFA/mois</sub></div>
              <div class="plan-opt-features">
                Multi-salles · API · Support prioritaire
              </div>
            </label>
          </div>
          <div
            style="
              background: var(--ok-bg);
              border: 1px solid rgba(22, 163, 74, 0.2);
              border-radius: 12px;
              padding: 14px 16px;
              display: flex;
              align-items: flex-start;
              gap: 10px;
              margin-bottom: 8px;
            "
          >
            <svg
              width="16"
              height="16"
              fill="none"
              stroke="var(--ok)"
              stroke-width="2.5"
              viewBox="0 0 24 24"
              style="flex-shrink: 0; margin-top: 1px"
            >
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 8 12 12 14 14" />
            </svg>
            <div style="font-size: 13px; color: var(--fg-2)">
              <strong style="color: var(--fg)">2 mois gratuits</strong> — vous
              ne serez débité qu'à la fin de la période d'essai. Vous pouvez
              annuler à tout moment depuis votre espace.
            </div>
          </div>
          <div class="form-footer">
            <button class="btn-back" type="button" data-go-step="2">
              ← Retour
            </button>
            <button class="btn-next" type="button" data-go-step="4">
              Valider et créer mon espace
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
              </svg>
            </button>
          </div>
        </div>

        <!-- STEP 4 — Confirmation -->
        <div
          class="step-panel form-section"
          id="panel-4"
          role="group"
          aria-labelledby="panel-title-4"
        >
          <h2 class="form-title" id="panel-title-4" tabindex="-1">
            Tout est prêt !
          </h2>
          <div class="form-subtitle">
            Vérifiez les informations avant de finaliser.
          </div>
          <div
            style="
              background: var(--surface);
              border: 1px solid var(--border);
              border-radius: 14px;
              padding: 20px 24px;
              margin-bottom: 24px;
            "
          >
            <div
              style="
                font-family: var(--fd);
                font-size: 13px;
                font-weight: 700;
                color: var(--fg-3);
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin-bottom: 12px;
              "
            >
              Récapitulatif
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px">
              <div
                style="
                  display: flex;
                  justify-content: space-between;
                  font-size: 13px;
                  padding-bottom: 10px;
                  border-bottom: 1px solid var(--border);
                "
              >
                <span style="color: var(--fg-3)">Salle</span>
                <span style="font-weight: 600" id="recap-salle">—</span>
              </div>
              <div
                style="
                  display: flex;
                  justify-content: space-between;
                  font-size: 13px;
                  padding-bottom: 10px;
                  border-bottom: 1px solid var(--border);
                "
              >
                <span style="color: var(--fg-3)">Commune</span>
                <span style="font-weight: 600" id="recap-quartier">—</span>
              </div>
              <div
                style="
                  display: flex;
                  justify-content: space-between;
                  font-size: 13px;
                  padding-bottom: 10px;
                  border-bottom: 1px solid var(--border);
                "
              >
                <span style="color: var(--fg-3)">Formule</span>
                <span
                  style="font-weight: 600; color: var(--accent)"
                  id="recap-plan"
                  >Essentiel — 15 000 FCFA/mois</span
                >
              </div>
              <div
                style="
                  display: flex;
                  justify-content: space-between;
                  font-size: 13px;
                "
              >
                <span style="color: var(--fg-3)">Essai gratuit</span>
                <span
                  style="font-weight: 600; color: var(--ok)"
                  id="recap-trial"
                  >2 mois · aucun débit immédiat</span
                >
              </div>
            </div>
          </div>
          <div
            style="
              display: flex;
              align-items: flex-start;
              gap: 10px;
              margin-bottom: 28px;
            "
          >
            <input
              type="checkbox"
              id="cgu"
              name="terms_accepted"
              required
              style="
                width: 16px;
                height: 16px;
                margin-top: 2px;
                accent-color: var(--accent);
              "
            />
            <label
              for="cgu"
              style="font-size: 13px; color: var(--fg-2); cursor: pointer"
            >
              J'accepte les
              <a href="/cgu" style="color: var(--accent)"
                >conditions générales d'utilisation</a
              >
              et la
              <a href="/confidentialite" style="color: var(--accent)"
                >politique de confidentialité</a
              >
              d'AbidjanGym.
            </label>
          </div>
          <div class="form-footer">
            <button class="btn-back" type="button" data-go-step="3">
              ← Retour
            </button>
            <button
              class="btn-next"
              type="submit"
              style="
                background: var(--ok);
                box-shadow: 0 4px 16px rgba(22, 163, 74, 0.3);
              "
            >
              <svg
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                viewBox="0 0 24 24"
              >
                <polyline points="20 6 9 17 4 12" />
              </svg>
              Créer mon espace gratuitement
            </button>
          </div>
        </div>
      </div>
      <!-- SUCCESS -->
      <div class="success-wrap form-section" id="success">
        <div class="success-icon">
          <svg
            width="32"
            height="32"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <polyline points="20 6 9 17 4 12" />
          </svg>
        </div>
        <h2 class="success-title" tabindex="-1">
          Bienvenue sur AbidjanGym ! 🎉
        </h2>
        <p class="success-sub">
          Votre espace est créé. Un email de confirmation a été envoyé. Vous
          pouvez accéder à votre tableau de bord maintenant.
        </p>
        <div class="success-info">
          <div class="si-row">
            <span class="si-label">URL de votre espace</span>
            <span
              class="si-value"
              id="success-administration-url-value"
              style="
                color: var(--accent);
                font-family: monospace;
                font-size: 12px;
              "
            ></span>
          </div>
          <div class="si-row">
            <span class="si-label">Formule</span>
            <span class="si-value" id="success-plan"></span>
          </div>
          <div class="si-row">
            <span class="si-label">Essai gratuit</span>
            <span class="si-value" id="success-trial"></span>
          </div>
        </div>
        <a
          id="success-dashboard-link"
          class="btn-next"
          hidden
          style="display: inline-flex; text-decoration: none"
        >
          Accéder à mon tableau de bord
          <svg
            width="15"
            height="15"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            viewBox="0 0 24 24"
          >
            <line x1="5" y1="12" x2="19" y2="12" />
            <polyline points="12 5 19 12 12 19" />
          </svg>
        </a>
      </div>
    </form>
  </main>
</div>
`,en={gym_name:"Nom de la salle",gym_district:"Quartier ou commune",gym_address:"Adresse complète",gym_phone:"Téléphone principal",gym_whatsapp:"WhatsApp",admin_first_name:"Prénom",admin_last_name:"Nom",admin_email:"Adresse email",admin_mobile_money_number:"Numéro Mobile Money",admin_password:"Mot de passe",admin_password_confirmation:"Confirmation du mot de passe",plan_code:"Formule",terms_accepted:"Acceptation des conditions"},tn=new Set(["starter","essentiel","pro"]),q=/^\+[1-9][0-9]{7,14}$/;function _(n){const l=n.trim().replace(/[\s().-]/g,"");if(l.startsWith("+"))return l;const i=l.replace(/\D/g,"");return i.startsWith("225")&&i.length===13?`+${i}`:i.length===10&&i.startsWith("0")?`+225${i}`:l}function x(n,l){const i={};if(l===1){const p=n.gym_name.trim(),o=n.gym_district.trim(),c=n.gym_address.trim(),r=_(n.gym_phone),t=_(n.gym_whatsapp);p?p.length>80&&(i.gym_name="Le nom ne peut pas dépasser 80 caractères."):i.gym_name="Saisissez le nom de votre salle.",o?o.length>80&&(i.gym_district="Le quartier ou la commune ne peut pas dépasser 80 caractères."):i.gym_district="Choisissez un quartier ou une commune.",c?c.length>255&&(i.gym_address="L’adresse ne peut pas dépasser 255 caractères."):i.gym_address="Saisissez l’adresse de votre salle.",n.gym_phone.trim()?q.test(r)||(i.gym_phone="Saisissez un numéro ivoirien à 10 chiffres."):i.gym_phone="Saisissez le téléphone principal de votre salle.",n.gym_whatsapp.trim()?q.test(t)||(i.gym_whatsapp="Saisissez un numéro ivoirien à 10 chiffres."):i.gym_whatsapp="Saisissez le numéro WhatsApp de votre salle."}if(l===2){const p=n.admin_first_name.trim(),o=n.admin_last_name.trim(),c=n.admin_email.trim(),r=_(n.admin_mobile_money_number);p?p.length>60&&(i.admin_first_name="Le prénom ne peut pas dépasser 60 caractères."):i.admin_first_name="Saisissez votre prénom.",o?o.length>60&&(i.admin_last_name="Le nom ne peut pas dépasser 60 caractères."):i.admin_last_name="Saisissez votre nom.",c?/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(c)?c.length>254&&(i.admin_email="L’adresse email ne peut pas dépasser 254 caractères."):i.admin_email="Saisissez une adresse email valide.":i.admin_email="Saisissez votre adresse email.",n.admin_mobile_money_number.trim()?q.test(r)||(i.admin_mobile_money_number="Saisissez un numéro ivoirien à 10 chiffres."):i.admin_mobile_money_number="Saisissez le numéro Mobile Money.",n.admin_password?(n.admin_password.length<8||n.admin_password.length>72||!/[A-Z]/.test(n.admin_password)||!/[0-9]/.test(n.admin_password))&&(i.admin_password="Le mot de passe doit contenir 8 caractères minimum, une majuscule et un chiffre."):i.admin_password="Saisissez un mot de passe.",n.admin_password_confirmation?n.admin_password!==n.admin_password_confirmation&&(i.admin_password_confirmation="Les mots de passe ne correspondent pas."):i.admin_password_confirmation="Confirmez votre mot de passe."}return l===3&&!tn.has(n.plan_code)&&(i.plan_code="Choisissez une formule pour continuer."),l===4&&!n.terms_accepted&&(i.terms_accepted="Acceptez les conditions générales et la politique de confidentialité."),i}function sn(n){return{...x(n,1),...x(n,2),...x(n,3),...x(n,4)}}function an(n){return{gym:{name:n.gym_name.trim(),district:n.gym_district.trim(),address:n.gym_address.trim(),phone:_(n.gym_phone),whatsapp:_(n.gym_whatsapp)},admin:{first_name:n.admin_first_name.trim(),last_name:n.admin_last_name.trim(),email:n.admin_email.trim(),mobile_money_number:_(n.admin_mobile_money_number),password:n.admin_password},plan_code:n.plan_code,terms_accepted:!0}}function y(n){return typeof n=="object"&&n!==null}function z(n,l){return y(n)&&typeof n.message=="string"&&n.message.trim()?n.message:y(n)&&y(n.data)&&typeof n.data.message=="string"&&n.data.message.trim()?n.data.message:l}function rn(n){if(!y(n)||!y(n.errors))return{};const l={name:"gym_name",district:"gym_district",address:"gym_address",phone:"gym_phone",whatsapp:"gym_whatsapp",first_name:"admin_first_name",last_name:"admin_last_name",email:"admin_email",mobile_money_number:"admin_mobile_money_number",password:"admin_password",plan_code:"plan_code",terms_accepted:"terms_accepted"},i={};for(const[p,o]of Object.entries(n.errors)){const c=l[p.split(".").at(-1)??p],r=typeof o=="string"?o:Array.isArray(o)&&typeof o[0]=="string"?o[0]:void 0;c&&r&&(i[c]=r)}return i}async function on(n,l,i=fetch){const p=sn(l);if(Object.keys(p).length)return{status:"invalid",message:"Vérifiez les champs indiqués avant de continuer.",errors:p};const o=n.trim().replace(/\/+$/,"");if(!o)return{status:"error",message:"L’inscription est momentanément indisponible. Réessayez plus tard."};try{const c=await i(`${o}/gyms`,{method:"POST",headers:{Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify(an(l))}),r=await c.json().catch(()=>null);if(c.status===201){const t=y(r)&&y(r.data)?r.data:{};return{status:"success",gymId:typeof t.gym_id=="string"||typeof t.gym_id=="number"?t.gym_id:void 0,administrationUrl:typeof t.administration_url=="string"?t.administration_url:"",message:z(r,"Votre salle a été créée. Consultez votre email pour confirmer votre compte.")}}return c.status===422?{status:"invalid",message:z(r,"Certaines informations sont invalides. Vérifiez les champs indiqués."),errors:rn(r)}:c.status===429?{status:"rate-limited",message:z(r,"Trop de tentatives. Réessayez dans une minute.")}:{status:"error",message:z(r,"Une erreur est survenue pendant l’inscription. Réessayez plus tard.")}}catch{return{status:"error",message:"Impossible de joindre le service. Vérifiez votre connexion puis réessayez."}}}function ln(n){return en[n]}const dn=["innerHTML"],M="Inscrivez votre salle sur AbidjanGym en quatre étapes. Choisissez votre formule et profitez de deux mois d’essai sur les offres payantes, sans carte bancaire.",j="https://abidjangym.com/inscription",mn=G({__name:"inscription",setup(n){const l=Z();Y({title:"Inscription AbidjanGym — Créez votre salle en ligne",description:M,ogTitle:"Inscription AbidjanGym — Créez votre salle en ligne",ogDescription:M,ogType:"website",ogUrl:j,ogImage:"https://abidjangym.com/images/og-abidjangym.jpg",twitterCard:"summary_large_image",twitterTitle:"Inscription AbidjanGym — Créez votre salle en ligne",twitterDescription:M,twitterImage:"https://abidjangym.com/images/og-abidjangym.jpg"}),X({link:[{rel:"canonical",href:j},{rel:"icon",type:"image/png",href:"/favicon.png"},{rel:"manifest",href:"/site.webmanifest"}],script:[{type:"application/ld+json",innerHTML:JSON.stringify([{"@context":"https://schema.org","@type":"Organization",name:"AbidjanGym",url:"https://abidjangym.com",logo:"https://abidjangym.com/images/logo.png"},{"@context":"https://schema.org","@type":"WebSite",name:"AbidjanGym",url:"https://abidjangym.com"},{"@context":"https://schema.org","@type":"BreadcrumbList",itemListElement:[{"@type":"ListItem",position:1,name:"Accueil",item:"https://abidjangym.com/"},{"@type":"ListItem",position:2,name:"Inscription",item:j}]}])}]});const i=K();let p;function o(r){const t=new FormData(r);return{gym_name:String(t.get("gym_name")??""),gym_district:String(t.get("gym_district")??""),gym_address:String(t.get("gym_address")??""),gym_phone:String(t.get("gym_phone")??""),gym_whatsapp:String(t.get("gym_whatsapp")??""),admin_first_name:String(t.get("admin_first_name")??""),admin_last_name:String(t.get("admin_last_name")??""),admin_email:String(t.get("admin_email")??""),admin_mobile_money_number:String(t.get("admin_mobile_money_number")??""),admin_password:String(t.get("admin_password")??""),admin_password_confirmation:String(t.get("admin_password_confirmation")??""),plan_code:String(t.get("plan_code")??""),terms_accepted:t.get("terms_accepted")==="on"}}function c(r){const t=r.querySelector("#onboarding-form"),f=r.querySelector("#form-alert"),E=r.querySelector("#step-announcement"),L=r.querySelector("#wizard-content"),g=r.querySelector("#success");if(!t||!f||!E||!L||!g)return;let S=1,k=!1;const O={gym_name:1,gym_district:1,gym_address:1,gym_phone:1,gym_whatsapp:1,admin_first_name:2,admin_last_name:2,admin_email:2,admin_mobile_money_number:2,admin_password:2,admin_password_confirmation:2,plan_code:3,terms_accepted:4},H=[...t.querySelectorAll("[name]")],C=a=>{E.textContent=a},w=()=>{f.hidden=!0,f.replaceChildren();for(const a of H){a.removeAttribute("aria-invalid");const s=a.getAttribute("aria-describedby");if(s){const e=s.split(/\s+/).filter(d=>d!=="form-alert");e.length?a.setAttribute("aria-describedby",e.join(" ")):a.removeAttribute("aria-describedby")}}},A=(a,s)=>{w(),f.hidden=!1;const e=document.createElement("p");e.textContent=s,f.append(e);const d=Object.entries(a);if(d.length){const m=document.createElement("ul");for(const[u,v]of d){const $=document.createElement("li");$.textContent=`${ln(u)} : ${v}`,m.append($);const h=t.elements.namedItem(u);if(h instanceof HTMLElement){h.setAttribute("aria-invalid","true");const b=(h.getAttribute("aria-describedby")??"").split(/\s+/).filter(Boolean);b.includes("form-alert")||b.push("form-alert"),h.setAttribute("aria-describedby",b.join(" "))}else if(h instanceof RadioNodeList)for(const b of[...h])b instanceof HTMLElement&&(b.setAttribute("aria-invalid","true"),b.setAttribute("aria-describedby","form-alert"))}f.append(m)}},F=()=>{const a=o(t),s={starter:"Starter — gratuit",essentiel:"Essentiel — 15 000 FCFA/mois",pro:"Pro — 35 000 FCFA/mois"},e=a.plan_code,d=e==="essentiel"||e==="pro";r.querySelector("#recap-salle").textContent=a.gym_name.trim()||"—",r.querySelector("#recap-quartier").textContent=a.gym_district||"—",r.querySelector("#recap-plan").textContent=s[e]??"—",r.querySelector("#recap-trial").textContent=d?"2 mois · aucun débit immédiat":"Aucun essai · formule gratuite"},T=(a,s=!0)=>{S=a;for(let e=1;e<=4;e++){const d=r.querySelector(`#panel-${e}`),m=r.querySelector(`#dot-${e}`),u=r.querySelector(`#lbl-${e}`),v=r.querySelector(`[data-step-item="${e}"]`);d.classList.toggle("active",e===a),m.className=`step-dot ${e<a?"done":e===a?"active":"pending"}`,e<a?m.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>':m.textContent=String(e),u.className=`step-label ${e===a?"active":e<a?"done":"pending"}`,e===a?v.setAttribute("aria-current","step"):v.removeAttribute("aria-current"),e<4&&r.querySelector(`#line-${e}`).classList.toggle("done",e<a)}a===4&&F(),C(`Étape ${a} sur 4 : ${r.querySelector(`#lbl-${a}`)?.textContent??""}`),s&&r.querySelector(`#panel-title-${a}`)?.focus()},B=a=>{const s=Object.keys(a)[0];if(!s)return;const e=t.elements.namedItem(s);e instanceof HTMLElement?e.focus():e instanceof RadioNodeList&&e[0]?.focus()},R=a=>{const s=x(o(t),a);return Object.keys(s).length?(A(s,"Vérifiez les informations indiquées avant de continuer."),B(s),!1):(w(),!0)},N=a=>{const s=a.target;if(!(s instanceof Element))return;const e=s.closest("[data-go-step]");if(!e||k)return;const d=Number(e.dataset.goStep);d>S&&!R(S)||(w(),T(d))},V=a=>{const s=a.target;(s instanceof HTMLInputElement||s instanceof HTMLSelectElement)&&(s.removeAttribute("aria-invalid"),s.name==="plan_code"&&(r.querySelectorAll(".plan-opt").forEach(e=>{e.classList.toggle("selected",e.querySelector("input")?.checked??!1)}),S===4&&F()),(s.name==="terms_accepted"||s.name==="plan_code")&&w())},I=a=>{const s=a.target;s instanceof HTMLElement&&s.matches("input, select")&&(s.removeAttribute("aria-invalid"),f.hidden||w())},P=async a=>{if(a.preventDefault(),k||!R(4))return;k=!0,t.setAttribute("aria-busy","true"),t.querySelectorAll("button").forEach(e=>e.disabled=!0),C("Création de votre espace en cours.");const s=await on(String(l.public.apiBase??""),o(t));if(k=!1,t.removeAttribute("aria-busy"),t.querySelectorAll("button").forEach(e=>e.disabled=!1),s.status==="success"){L.hidden=!0,g.classList.add("active"),g.querySelector(".success-sub").textContent=s.message;const e={starter:"Starter",essentiel:"Essentiel",pro:"Pro"},d=o(t).plan_code;g.querySelector("#success-plan").textContent=e[d]??d,g.querySelector("#success-trial").textContent=d==="starter"?"Aucun essai · formule gratuite":"2 mois gratuits · aucun débit immédiat";const m=g.querySelector("#success-administration-url-value");m.textContent=s.administrationUrl;const u=g.querySelector("#success-dashboard-link");try{const v=new URL(s.administrationUrl);["https:","http:"].includes(v.protocol)&&(u.href=v.href,u.hidden=!1)}catch{}C("Inscription terminée."),g.querySelector(".success-title")?.focus();return}if(s.status==="invalid"){const e=s.errors,d=Object.keys(e);if(d.length){const m=Math.min(...d.map(u=>O[u]));T(m,!1),A(e,s.message),B(e)}else A({},s.message)}else A({},s.message),f.focus();C("L’inscription nécessite votre attention.")};t.addEventListener("click",N),t.addEventListener("change",V),t.addEventListener("input",I),t.addEventListener("submit",P),p=()=>{t.removeEventListener("click",N),t.removeEventListener("change",V),t.removeEventListener("input",I),t.removeEventListener("submit",P)}}return W(()=>{i.value&&c(i.value)}),U(()=>p?.()),(r,t)=>(Q(),D("div",{ref_key:"stepRef",ref:i,class:"onboarding-page",innerHTML:J(nn)},null,8,dn))}});export{mn as default};
