import { mount } from "@vue/test-utils";
import { describe, expect, it } from "vitest";
import AppHeader from "../components/AppHeader.vue";

describe("AppHeader", () => {
  it("présente la marque et une navigation accessible en français", () => {
    const wrapper = mount(AppHeader);

    expect(wrapper.get('a[aria-label="Accueil AbidjanGym"]').text()).toBe(
      "AbidjanGym",
    );
    expect(wrapper.get("nav").attributes("aria-label")).toBe(
      "Navigation principale",
    );
    expect(wrapper.findAll("nav a").map((link) => link.text())).toEqual([
      "Fonctionnalités",
      "Pour les gérants",
      "Tarifs",
      "FAQ",
      "J’inscris ma salle",
    ]);
    expect(wrapper.get("button").attributes("aria-expanded")).toBe("false");
  });
});
