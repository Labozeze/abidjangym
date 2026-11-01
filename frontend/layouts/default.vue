<template>
  <div class="site-shell">
    <a class="skip-link" href="#contenu-principal"
      >Aller au contenu principal</a
    >
    <AppHeader />
    <main id="contenu-principal">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>
