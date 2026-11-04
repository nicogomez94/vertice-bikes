const PRODUCTS = [
  {
    id: "sicilia-negra",
    name: "Sicilia Dama R28",
    brand: "Battle / Philco",
    category: "bicicletas",
    type: "Bicicleta urbana",
    code: "BICSICILIA28DN",
    price: 156198.35,
    image: "assets/images/fardella/sicilia-negra.jpg",
    description: "Bicicleta de paseo Sicilia Dama rodado 28, de una velocidad, con guardabarros y portapaquete. Terminación negra.",
    specs: { Marca: "Battle / Philco", Modelo: "Sicilia Dama", Rodado: "28 pulgadas", Velocidades: "1", Equipamiento: "Guardabarros y portapaquete", Color: "Negro" }
  },
  {
    id: "battle-gris",
    name: "Battle R29 24v",
    brand: "Battle",
    category: "bicicletas",
    type: "Bicicleta MTB",
    code: "BICBATTLE2924GR",
    price: 289256.20,
    image: "assets/images/fardella/battle-gris.jpg",
    description: "Bicicleta Battle rodado 29 con 24 velocidades, frenos hidráulicos, suspensión Suntour con bloqueo y transmisión Microshift con palanca TY301. Color gris.",
    specs: { Marca: "Battle", Rodado: "29 pulgadas", Velocidades: "24", Frenos: "Hidráulicos", Suspensión: "Suntour con bloqueo", Transmisión: "Microshift con palanca TY301", Color: "Gris" }
  },
  {
    id: "cobra-r29",
    name: "Cubierta Cobra R29",
    brand: "Hutchinson",
    category: "repuestos",
    type: "Cubierta",
    code: "PV699992",
    price: 38603.31,
    image: "assets/images/fardella/cubierta-cobra.jpg",
    description: "Cubierta Hutchinson Cobra rodado 29 × 2.10, construcción de 127 TPI.",
    specs: { Marca: "Hutchinson", Modelo: "Cobra", Medida: "R29 × 2.10", Construcción: "127 TPI" }
  },
  {
    id: "gafas-flash",
    name: "Gafas Flash Azul",
    brand: "Accesorios",
    category: "accesorios",
    type: "Gafas",
    code: "X1917230496",
    price: 14834,
    image: "assets/images/fardella/gafas-flash.jpg",
    description: "Gafas Flash en color azul, incluidas en la selección de accesorios del catálogo.",
    specs: { Producto: "Gafas Flash", Color: "Azul", Código: "X1917230496" }
  },
  {
    id: "guante-power",
    name: "Guante Power Negro",
    brand: "Accesorios",
    category: "accesorios",
    type: "Guantes",
    code: "X20122201005",
    price: 9257.12,
    image: "assets/images/fardella/guante-power.jpg",
    description: "Guante Power negro, disponible en talles M, L, XL y XXL según el catálogo.",
    specs: { Producto: "Guante Power", Color: "Negro", Talles: "M, L, XL, XXL", Código: "X20122201005" }
  }
];

const formatPrice = value => "$ " + new Intl.NumberFormat("es-AR", {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2
}).format(value);

const cartKey = "fardella-cart-v1";
const getCart = () => {
  try { return JSON.parse(localStorage.getItem(cartKey)) || []; }
  catch { return []; }
};
const saveCart = cart => {
  localStorage.setItem(cartKey, JSON.stringify(cart));
  updateCartCount();
};
const updateCartCount = () => {
  const count = getCart().reduce((sum, item) => sum + item.qty, 0);
  document.querySelectorAll("[data-cart-count]").forEach(el => { el.textContent = count; });
};

let toastTimer;
const showToast = message => {
  const toast = document.querySelector(".toast");
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add("show");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove("show"), 2800);
};

const addToCart = (id, qty = 1) => {
  const cart = getCart();
  const existing = cart.find(item => item.id === id);
  if (existing) existing.qty += qty;
  else cart.push({ id, qty });
  saveCart(cart);
  showToast(`${PRODUCTS.find(item => item.id === id).name} se agregó a tu selección`);
};

const productCard = product => `
  <article class="product-card" data-category="${product.category}">
    <a class="product-image-wrap" href="producto.html?id=${product.id}" aria-label="Ver ${product.name}">
      <img src="${product.image}" alt="${product.name}" loading="lazy">
      <span class="product-tag">${product.type}</span>
    </a>
    <div class="product-card-content">
      <div class="product-card-meta"><span>${product.brand}</span><span>${product.code}</span></div>
      <a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a>
      <div class="product-card-bottom">
        <div class="product-card-price"><strong>${formatPrice(product.price)}</strong><span>Precio de lista + IVA</span></div>
        <button class="quick-add" data-quick-add="${product.id}" aria-label="Agregar ${product.name} a la selección"><i class="fa-solid fa-plus"></i></button>
      </div>
    </div>
  </article>
`;

const bindQuickAdds = root => {
  root.querySelectorAll("[data-quick-add]").forEach(button => {
    button.addEventListener("click", () => addToCart(button.dataset.quickAdd));
  });
};

const renderCatalog = () => {
  const grid = document.querySelector("[data-product-grid]");
  if (!grid) return;
  grid.innerHTML = PRODUCTS.map(productCard).join("");
  bindQuickAdds(grid);
  const applyFilter = filter => {
    document.querySelectorAll("[data-filter]").forEach(button => {
      button.classList.toggle("active", button.dataset.filter === filter);
    });
    grid.querySelectorAll(".product-card").forEach(card => {
      card.hidden = filter !== "todos" && card.dataset.category !== filter;
    });
  };
  document.querySelectorAll("[data-filter]").forEach(button => {
    button.addEventListener("click", () => applyFilter(button.dataset.filter));
  });
  document.querySelectorAll("[data-category-link]").forEach(link => {
    link.addEventListener("click", () => applyFilter(link.dataset.categoryLink));
  });
};

const renderProductPage = () => {
  const target = document.querySelector("[data-product-detail]");
  if (!target) return;
  const id = new URLSearchParams(window.location.search).get("id");
  const product = PRODUCTS.find(item => item.id === id) || PRODUCTS[0];
  document.title = `${product.name} — Fardella S.A.`;
  document.querySelector("[data-product-breadcrumb]").textContent = product.name;
  target.innerHTML = `
    <div class="product-gallery"><div class="gallery-main"><img src="${product.image}" alt="${product.name}"></div></div>
    <div class="product-info">
      <p class="product-type">${product.brand} · ${product.type}</p>
      <h1>${product.name}</h1>
      <span class="product-code">Código ${product.code}</span>
      <p class="product-price">${formatPrice(product.price)}</p>
      <p class="product-financing">Precio de lista + IVA</p>
      <p class="product-description">${product.description}</p>
      <div class="buy-row">
        <div class="quantity-control" aria-label="Cantidad">
          <button type="button" data-product-minus aria-label="Restar">−</button>
          <span data-product-qty>1</span>
          <button type="button" data-product-plus aria-label="Sumar">+</button>
        </div>
        <button class="button button-accent" type="button" data-product-add>Agregar a selección <i class="fa-solid fa-plus"></i></button>
      </div>
      <p class="stock-note"><i class="fa-solid fa-circle-info"></i> Disponibilidad y condiciones comerciales sujetas a confirmación.</p>
      <a class="text-link product-contact" href="mailto:info@fardella.com.ar?subject=Consulta%20${encodeURIComponent(product.code)}">Consultar este producto <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  `;
  document.querySelector("[data-product-specs]").innerHTML = Object.entries(product.specs)
    .map(([label, value]) => `<div class="spec-row"><span>${label}</span><strong>${value}</strong></div>`).join("");
  const related = document.querySelector("[data-related-grid]");
  related.innerHTML = PRODUCTS.filter(item => item.id !== product.id).slice(0, 3).map(productCard).join("");
  bindQuickAdds(related);
  let qty = 1;
  const qtyEl = target.querySelector("[data-product-qty]");
  target.querySelector("[data-product-minus]").addEventListener("click", () => {
    qty = Math.max(1, qty - 1);
    qtyEl.textContent = qty;
  });
  target.querySelector("[data-product-plus]").addEventListener("click", () => {
    qty = Math.min(9, qty + 1);
    qtyEl.textContent = qty;
  });
  target.querySelector("[data-product-add]").addEventListener("click", () => addToCart(product.id, qty));
};

const renderCart = () => {
  const list = document.querySelector("[data-cart-items]");
  if (!list) return;
  const cart = getCart().filter(item => PRODUCTS.some(product => product.id === item.id));
  document.querySelector("[data-cart-title-count]").textContent = `(${cart.reduce((sum, item) => sum + item.qty, 0)})`;
  const summary = document.querySelector("[data-cart-summary]");
  if (!cart.length) {
    list.innerHTML = `<div class="empty-cart"><i class="fa-solid fa-bicycle"></i><h2>Tu selección está vacía.</h2><p>Explorá las categorías de Fardella y encontrá lo que necesitás.</p><a class="button button-accent" href="index.html#productos">Ver productos <i class="fa-solid fa-arrow-right"></i></a></div>`;
    summary.hidden = true;
    return;
  }
  summary.hidden = false;
  list.innerHTML = cart.map((item, index) => {
    const product = PRODUCTS.find(entry => entry.id === item.id);
    return `
      <article class="cart-item">
        <a class="cart-item-image" href="producto.html?id=${product.id}"><img src="${product.image}" alt="${product.name}"></a>
        <div class="cart-item-info">
          <div class="cart-item-top"><div><a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a><span class="cart-item-meta">${product.type} · ${product.code}</span></div><button class="cart-remove" data-cart-remove="${index}" aria-label="Eliminar ${product.name}"><i class="fa-regular fa-trash-can"></i></button></div>
          <div class="cart-item-bottom"><div class="quantity-control"><button data-cart-minus="${index}" aria-label="Restar">−</button><span>${item.qty}</span><button data-cart-plus="${index}" aria-label="Sumar">+</button></div><strong class="cart-item-price">${formatPrice(product.price * item.qty)}</strong></div>
        </div>
      </article>
    `;
  }).join("");
  const subtotal = cart.reduce((sum, item) => sum + PRODUCTS.find(p => p.id === item.id).price * item.qty, 0);
  document.querySelector("[data-cart-subtotal]").textContent = formatPrice(subtotal);
  document.querySelector("[data-cart-total]").textContent = formatPrice(subtotal);
  list.querySelectorAll("[data-cart-remove]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); current.splice(Number(button.dataset.cartRemove), 1); saveCart(current); renderCart();
  }));
  list.querySelectorAll("[data-cart-minus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); current[Number(button.dataset.cartMinus)].qty = Math.max(1, current[Number(button.dataset.cartMinus)].qty - 1); saveCart(current); renderCart();
  }));
  list.querySelectorAll("[data-cart-plus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); current[Number(button.dataset.cartPlus)].qty = Math.min(9, current[Number(button.dataset.cartPlus)].qty + 1); saveCart(current); renderCart();
  }));
};

const setupShareSelection = () => {
  const button = document.querySelector("[data-share-selection]");
  if (!button) return;
  button.addEventListener("click", () => {
    const lines = getCart().filter(item => PRODUCTS.some(p => p.id === item.id)).map(item => {
      const product = PRODUCTS.find(p => p.id === item.id);
      return `${item.qty} × ${product.name} · ${product.code} · ${formatPrice(product.price * item.qty)}`;
    });
    const text = `Mi selección Fardella S.A.:\n\n${lines.join("\n")}\n\nPrecios de lista + IVA.`;
    const area = document.createElement("textarea");
    area.value = text;
    area.style.position = "fixed";
    area.style.opacity = "0";
    document.body.appendChild(area);
    area.select();
    const copied = document.execCommand("copy");
    area.remove();
    showToast(copied ? "Selección copiada para compartir" : "No se pudo copiar la selección");
  });
};

const setupNavigation = () => {
  const menu = document.querySelector(".mobile-menu");
  const backdrop = document.querySelector(".menu-backdrop");
  const toggle = document.querySelector(".menu-toggle");
  const closeMenu = () => {
    menu.classList.remove("open"); backdrop.classList.remove("open");
    menu.setAttribute("aria-hidden", "true"); toggle.setAttribute("aria-expanded", "false");
  };
  toggle.addEventListener("click", () => {
    menu.classList.add("open"); backdrop.classList.add("open");
    menu.setAttribute("aria-hidden", "false"); toggle.setAttribute("aria-expanded", "true");
  });
  document.querySelector(".menu-close").addEventListener("click", closeMenu);
  backdrop.addEventListener("click", closeMenu);
  menu.querySelectorAll("a").forEach(link => link.addEventListener("click", event => {
    const href = link.getAttribute("href");
    const target = href?.startsWith("#") ? document.querySelector(href) : null;
    closeMenu();
    if (target) {
      event.preventDefault();
      history.replaceState(null, "", href);
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }));
  const panel = document.querySelector(".search-panel");
  const closeSearch = () => { panel?.classList.remove("open"); panel?.setAttribute("aria-hidden", "true"); };
  document.querySelector(".search-toggle")?.addEventListener("click", () => {
    panel.classList.add("open"); panel.setAttribute("aria-hidden", "false"); panel.querySelector("input").focus();
  });
  document.querySelector(".search-close")?.addEventListener("click", closeSearch);
  panel?.querySelector("input")?.addEventListener("input", event => {
    const value = event.target.value.toLocaleLowerCase("es").trim();
    const matches = value ? PRODUCTS.filter(p => `${p.name} ${p.brand} ${p.type} ${p.code}`.toLocaleLowerCase("es").includes(value)) : [];
    panel.querySelector(".search-results").innerHTML = matches.length
      ? matches.map(p => `<a href="producto.html?id=${p.id}">${p.name} <i class="fa-solid fa-arrow-right"></i></a>`).join("")
      : value ? "<span>No encontramos coincidencias.</span>" : "";
  });
  document.addEventListener("keydown", event => { if (event.key === "Escape") { closeMenu(); closeSearch(); } });
};

const setupObserver = () => {
  const elements = document.querySelectorAll(".fade-in");
  if (!("IntersectionObserver" in window)) { elements.forEach(el => el.classList.add("visible")); return; }
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add("visible"); observer.unobserve(entry.target); } });
  }, { threshold: .08 });
  elements.forEach(el => observer.observe(el));
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
    if (!reducedMotion.matches && !document.hidden) {
      timer = setInterval(() => showSlide(current + 1), 4300);
    }
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
  updateCartCount(); renderCatalog(); renderProductPage(); renderCart();
  setupNavigation(); setupObserver(); setupShareSelection(); setupHeroCarousel(); setupInquiryForm();
});
