const setupNavigation = () => {
  const menu = document.querySelector(".mobile-menu");
  const backdrop = document.querySelector(".menu-backdrop");
  const toggle = document.querySelector(".menu-toggle");
  const mobileCatalog = document.querySelector(".mobile-catalog");
  const mobileCatalogToggle = document.querySelector(".mobile-catalog-toggle");
  const mobileCatalogPanel = document.querySelector(".mobile-catalog-panel");
  const catalogDropdown = document.querySelector(".catalog-dropdown");
  const catalogDropdownToggle = document.querySelector(".catalog-dropdown-toggle");
  const catalogDropdownMenu = document.querySelector(".catalog-dropdown-menu");

  const closeCatalogDropdown = () => {
    catalogDropdown?.classList.remove("open");
    catalogDropdownToggle?.setAttribute("aria-expanded", "false");
    catalogDropdownMenu?.setAttribute("aria-hidden", "true");
  };

  const closeMenu = () => {
    menu?.classList.remove("open");
    backdrop?.classList.remove("open");
    menu?.setAttribute("aria-hidden", "true");
    toggle?.setAttribute("aria-expanded", "false");
    mobileCatalog?.classList.remove("open");
    mobileCatalogToggle?.setAttribute("aria-expanded", "false");
    mobileCatalogPanel?.setAttribute("aria-hidden", "true");
  };

  toggle?.addEventListener("click", () => {
    menu?.classList.add("open");
    backdrop?.classList.add("open");
    menu?.setAttribute("aria-hidden", "false");
    toggle.setAttribute("aria-expanded", "true");
  });
  document.querySelector(".menu-close")?.addEventListener("click", closeMenu);
  backdrop?.addEventListener("click", closeMenu);
  mobileCatalogToggle?.addEventListener("click", () => {
    const open = mobileCatalog.classList.toggle("open");
    mobileCatalogToggle.setAttribute("aria-expanded", String(open));
    mobileCatalogPanel.setAttribute("aria-hidden", String(!open));
  });
  menu?.querySelectorAll("a").forEach(link => link.addEventListener("click", closeMenu));

  catalogDropdownToggle?.addEventListener("click", event => {
    event.stopPropagation();
    const open = catalogDropdown.classList.toggle("open");
    catalogDropdownToggle.setAttribute("aria-expanded", String(open));
    catalogDropdownMenu.setAttribute("aria-hidden", String(!open));
  });
  catalogDropdownMenu?.querySelectorAll("a").forEach(link => link.addEventListener("click", closeCatalogDropdown));
  document.addEventListener("click", event => {
    if (catalogDropdown && !catalogDropdown.contains(event.target)) closeCatalogDropdown();
  });

  const panel = document.querySelector(".search-panel");
  const closeSearch = () => {
    panel?.classList.remove("open");
    panel?.setAttribute("aria-hidden", "true");
  };
  document.querySelector(".search-toggle")?.addEventListener("click", () => {
    panel?.classList.add("open");
    panel?.setAttribute("aria-hidden", "false");
    panel?.querySelector("input")?.focus();
  });
  document.querySelector(".search-close")?.addEventListener("click", closeSearch);
  document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
      closeMenu();
      closeSearch();
      closeCatalogDropdown();
    }
  });
};

const setupHeroCarousel = () => {
  const slides = [...document.querySelectorAll("[data-hero-slide]")];
  const controls = [...document.querySelectorAll("[data-hero-go]")];
  if (slides.length < 2) return;
  let current = 0;
  let timer;
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const showSlide = index => {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, slideIndex) => slide.classList.toggle("active", slideIndex === current));
    controls.forEach((control, controlIndex) => {
      const active = controlIndex === current;
      control.classList.toggle("active", active);
      control.setAttribute("aria-pressed", String(active));
    });
  };
  const start = () => {
    clearInterval(timer);
    if (!reducedMotion.matches && !document.hidden) timer = setInterval(() => showSlide(current + 1), 4300);
  };
  controls.forEach(control => control.addEventListener("click", () => {
    showSlide(Number(control.dataset.heroGo));
    start();
  }));
  document.addEventListener("visibilitychange", start);
  reducedMotion.addEventListener?.("change", start);
  showSlide(0);
  start();
};

const setupCatalogFilters = () => {
  const cards = [...document.querySelectorAll("[data-product-card]")];
  const filters = [...document.querySelectorAll("[data-filter]")];
  if (!cards.length || !filters.length) return;
  const apply = filter => {
    filters.forEach(button => button.classList.toggle("active", button.dataset.filter === filter));
    cards.forEach(card => {
      card.hidden = filter !== "todos" && card.dataset.category !== filter;
    });
  };
  filters.forEach(button => button.addEventListener("click", () => apply(button.dataset.filter)));
  const requested = new URLSearchParams(window.location.search).get("categoria");
  if (["bicicletas", "repuestos", "accesorios"].includes(requested)) apply(requested);
};

const setupObserver = () => {
  const elements = document.querySelectorAll(".fade-in");
  if (!("IntersectionObserver" in window)) {
    elements.forEach(element => element.classList.add("visible"));
    return;
  }
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08 });
  elements.forEach(element => observer.observe(element));
};

const setupInquiryForm = () => {
  const form = document.querySelector("[data-inquiry-form]");
  if (!form) return;
  const status = form.querySelector("[data-form-status]");
  form.addEventListener("submit", event => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const data = new FormData(form);
    const subject = `Consulta web · ${data.get("interes")}`;
    const body = [
      `Nombre: ${data.get("nombre")}`,
      `Comercio: ${data.get("comercio") || "No indicado"}`,
      `Email: ${data.get("email")}`,
      `Teléfono: ${data.get("telefono") || "No indicado"}`,
      `Interés: ${data.get("interes")}`,
      "",
      "Consulta:",
      data.get("mensaje")
    ].join("\n");
    status.textContent = "Listo: abrimos tu correo con la consulta completa para que puedas revisarla y enviarla.";
    window.location.href = `mailto:info@fardella.com.ar?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  });
};

document.addEventListener("DOMContentLoaded", () => {
  setupNavigation();
  setupHeroCarousel();
  setupCatalogFilters();
  setupObserver();
  setupInquiryForm();
});

