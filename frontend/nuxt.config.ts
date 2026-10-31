export default defineNuxtConfig({
  modules: ["@nuxt/eslint"],
  css: ["~/assets/css/main.css"],
  typescript: {
    strict: true,
  },
  app: {
    head: {
      htmlAttrs: {
        lang: "fr",
      },
      meta: [
        { charset: "utf-8" },
        { name: "viewport", content: "width=device-width, initial-scale=1" },
      ],
      link: [
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        {
          rel: "preconnect",
          href: "https://fonts.gstatic.com",
          crossorigin: "anonymous",
        },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap",
        },
      ],
    },
  },
  runtimeConfig: {
    public: {
      apiBase: "",
    },
  },
  nitro: {
    prerender: {
      routes: ["/"],
    },
  },
});
