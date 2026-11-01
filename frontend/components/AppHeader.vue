<script setup lang="ts">
import { ref } from "vue";

const menuOpen = ref(false);
</script>

<template>
  <header class="site-header">
    <div class="header-inner">
      <a class="brand" href="/#accueil" aria-label="Accueil AbidjanGym">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M7 5v14M17 5v14M7 8h2m6 0h2M7 16h2m6 0h2M9 8h6m-6 8h6" />
          </svg>
        </span>
        <span>Abidjan<span class="brand-accent">Gym</span></span>
      </a>

      <button
        class="menu-toggle"
        type="button"
        :aria-expanded="menuOpen"
        aria-controls="primary-navigation"
        :aria-label="menuOpen ? 'Masquer le menu' : 'Afficher le menu'"
        @click="menuOpen = !menuOpen"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav
        id="primary-navigation"
        class="primary-navigation"
        :class="{ 'is-open': menuOpen }"
        aria-label="Navigation principale"
      >
        <a href="/#fonctionnalites" @click="menuOpen = false"
          >Fonctionnalités</a
        >
        <a href="/#pour-gerants" @click="menuOpen = false">Pour les gérants</a>
        <a href="/#tarifs" @click="menuOpen = false">Tarifs</a>
        <a href="/#faq" @click="menuOpen = false">FAQ</a>
        <a class="button button-small" href="/inscription"
          >J’inscris ma salle</a
        >
      </nav>
    </div>
  </header>
</template>
