const PRODUCTS = [
  {
    id: "alba-gravel",
    name: "Alba GRX 400",
    category: "gravel",
    type: "Gravel / Aventura",
    price: 2299000,
    image: "assets/images/bike-1.jpg",
    gallery: ["assets/images/bike-1.jpg", "assets/images/bike-3.jpg", "assets/images/bike-10.jpg"],
    tag: "Más elegida",
    featured: true,
    code: "VTX-AG400-26",
    description: "Una gravel equilibrada para combinar asfalto, caminos rurales y viajes largos. Postura estable, paso de rueda generoso y transmisión confiable para sumar kilómetros sin pensar en el terreno.",
    colors: ["#d8d1bd", "#303832", "#8d4c39"],
    specs: { "Cuadro": "Aluminio 6061 hidroformado", "Horquilla": "Carbono full monocoque", "Transmisión": "Shimano GRX 2 × 10", "Frenos": "Disco hidráulico Shimano", "Ruedas": "700c tubeless ready", "Cubiertas": "Maxxis Rambler 700 × 40", "Peso": "10,8 kg aprox." }
  },
  {
    id: "rastro-trail",
    name: "Rastro Trail 29",
    category: "mtb",
    type: "MTB / Sendero",
    price: 1850000,
    image: "assets/images/bike-2.jpg",
    gallery: ["assets/images/bike-2.jpg", "assets/images/bike-4.jpg", "assets/images/bike-5.jpg"],
    tag: "Sendero",
    code: "VTX-RT29-26",
    description: "Control y agilidad para senderos técnicos. Una geometría moderna y estable que permite avanzar rápido sin resignar confianza en bajadas y curvas cerradas.",
    colors: ["#a23d2d", "#202321", "#d1d0c8"],
    specs: { "Cuadro": "Aluminio 6061 Trail Geometry", "Horquilla": "Aire 120 mm con bloqueo", "Transmisión": "Shimano Deore 1 × 12", "Frenos": "Disco hidráulico 180/160", "Ruedas": "29 pulgadas doble pared", "Cubiertas": "Maxxis Rekon 2.35", "Peso": "13,6 kg aprox." }
  },
  {
    id: "pulso-carbon",
    name: "Pulso Carbon R7",
    category: "ruta",
    type: "Ruta / Rendimiento",
    price: 3980000,
    image: "assets/images/bike-6.jpg",
    gallery: ["assets/images/bike-6.jpg", "assets/images/bike-0.jpg", "assets/images/bike-8.jpg"],
    tag: "Rendimiento",
    code: "VTX-PCR7-26",
    description: "Velocidad limpia y respuesta directa. El cuadro de carbono absorbe vibraciones sin perder rigidez, con una posición eficiente para entrenamientos largos y fondos exigentes.",
    colors: ["#171918", "#68716b"],
    specs: { "Cuadro": "Carbono Toray T700", "Horquilla": "Carbono monocasco", "Transmisión": "Shimano 105 2 × 12", "Frenos": "Disco hidráulico de montaje plano", "Ruedas": "Aluminio perfil 35 mm", "Cubiertas": "Continental Ultra Sport 28c", "Peso": "8,4 kg aprox." }
  },
  {
    id: "norte-allroad",
    name: "Norte Allroad",
    category: "gravel",
    type: "Gravel / Todo camino",
    price: 2740000,
    image: "assets/images/bike-11.jpg",
    gallery: ["assets/images/bike-11.jpg", "assets/images/bike-1.jpg", "assets/images/bike-7.jpg"],
    tag: "Novedad",
    code: "VTX-NAR-26",
    description: "Rápida en ruta y segura cuando termina el asfalto. La Norte combina una posición deportiva con cubiertas de gran volumen y componentes pensados para explorar.",
    colors: ["#5f3b32", "#b7b0a0", "#1c211e"],
    specs: { "Cuadro": "Aluminio 6069 triple conificado", "Horquilla": "Carbono con anclajes", "Transmisión": "SRAM Apex 1 × 12", "Frenos": "Disco hidráulico SRAM", "Ruedas": "700c / compatible 650b", "Cubiertas": "WTB Riddler 700 × 37", "Peso": "10,2 kg aprox." }
  },
  {
    id: "trama-urban",
    name: "Trama Urban 8",
    category: "urbana",
    type: "Urbana / Commuter",
    price: 1120000,
    image: "assets/images/bike-9.jpg",
    gallery: ["assets/images/bike-9.jpg", "assets/images/bike-8.jpg", "assets/images/bike-10.jpg"],
    tag: "Ciudad",
    code: "VTX-TU8-26",
    description: "Simple, cómoda y lista para todos los días. Cambios internos, guardabarros y una posición erguida que hace más natural cada trayecto urbano.",
    colors: ["#b8d9d8", "#d6d0bd", "#26312f"],
    specs: { "Cuadro": "Aluminio 6061 de acceso bajo", "Horquilla": "Acero CrMo confort", "Transmisión": "Shimano Nexus 8 velocidades", "Frenos": "Disco mecánico", "Ruedas": "700c doble pared", "Cubiertas": "Kenda Kwick 700 × 38", "Peso": "13,9 kg aprox." }
  },
  {
    id: "cumbre-xc",
    name: "Cumbre XC Pro",
    category: "mtb",
    type: "MTB / Cross Country",
    price: 2460000,
    image: "assets/images/bike-3.jpg",
    gallery: ["assets/images/bike-3.jpg", "assets/images/bike-5.jpg", "assets/images/bike-4.jpg"],
    tag: "Aventura",
    code: "VTX-CXC-26",
    description: "Una rígida rápida para subir fuerte y sostener ritmo. Transmisión monoplato, horquilla de aire y ruedas 29 para recorrer más terreno con menos esfuerzo.",
    colors: ["#b56b20", "#262824", "#6d735d"],
    specs: { "Cuadro": "Aluminio 6061 XC Boost", "Horquilla": "Aire 100 mm con bloqueo remoto", "Transmisión": "Shimano Deore 1 × 12", "Frenos": "Shimano MT200 hidráulicos", "Ruedas": "29 pulgadas Boost", "Cubiertas": "Maxxis Ikon 2.20", "Peso": "12,7 kg aprox." }
  }
];

const formatPrice = value => new Intl.NumberFormat("es-AR", {
  style: "currency", currency: "ARS", maximumFractionDigits: 0
}).format(value).replace("ARS", "$ ");

const getCart = () => {
  try { return JSON.parse(localStorage.getItem("vertice-cart")) || []; }
  catch { return []; }
};

const saveCart = cart => {
  localStorage.setItem("vertice-cart", JSON.stringify(cart));
  updateCartCount();
};

const updateCartCount = () => {
  const count = getCart().reduce((sum, item) => sum + item.qty, 0);
  document.querySelectorAll("[data-cart-count]").forEach(el => el.textContent = count);
};

let toastTimer;
const showToast = message => {
  const toast = document.querySelector(".toast");
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add("show");
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => toast.classList.remove("show"), 2600);
};

const addToCart = (id, size = "M", qty = 1) => {
  const cart = getCart();
  const existing = cart.find(item => item.id === id && item.size === size);
  if (existing) existing.qty += qty;
  else cart.push({ id, size, qty });
  saveCart(cart);
  const product = PRODUCTS.find(item => item.id === id);
  showToast(`${product.name} se agregó al carrito`);
};

const productCard = (product, options = {}) => `
  <article class="product-card fade-in ${product.featured && !options.compact ? "featured" : ""}" data-category="${product.category}">
    <div class="product-image-wrap">
      <a href="producto.html?id=${product.id}" aria-label="Ver ${product.name}">
        <img src="${product.image}" alt="${product.name}" loading="lazy">
      </a>
      <span class="product-tag">${product.tag}</span>
      <button class="quick-add" data-quick-add="${product.id}" aria-label="Agregar ${product.name} al carrito"><i class="fa-solid fa-plus"></i></button>
    </div>
    <div class="product-card-content">
      <div class="product-card-meta"><span>${product.type}</span><span>${product.code.slice(-2)}</span></div>
      <a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a>
      <div class="product-card-price"><strong>${formatPrice(product.price)}</strong><span>6 cuotas sin interés</span></div>
    </div>
  </article>`;

const bindQuickAdds = root => {
  root.querySelectorAll("[data-quick-add]").forEach(button => {
    button.addEventListener("click", () => addToCart(button.dataset.quickAdd));
  });
};

const renderCatalog = () => {
  const grid = document.querySelector("[data-product-grid]");
  if (!grid) return;
  grid.innerHTML = PRODUCTS.map(product => productCard(product)).join("");
  bindQuickAdds(grid);
  document.querySelectorAll("[data-filter]").forEach(button => {
    button.addEventListener("click", () => {
      document.querySelectorAll("[data-filter]").forEach(item => item.classList.remove("active"));
      button.classList.add("active");
      const filter = button.dataset.filter;
      grid.querySelectorAll(".product-card").forEach(card => {
        card.hidden = filter !== "todos" && card.dataset.category !== filter;
      });
    });
  });
};

const renderProductPage = () => {
  const target = document.querySelector("[data-product-detail]");
  if (!target) return;
  const id = new URLSearchParams(window.location.search).get("id") || PRODUCTS[0].id;
  const product = PRODUCTS.find(item => item.id === id) || PRODUCTS[0];
  document.title = `${product.name} — Vértice Bikes`;
  const breadcrumb = document.querySelector("[data-product-breadcrumb]");
  if (breadcrumb) breadcrumb.textContent = product.name;

  target.innerHTML = `
    <div class="product-gallery fade-in">
      <div class="gallery-main"><img src="${product.gallery[0]}" alt="${product.name}" data-gallery-main></div>
      <div class="gallery-thumbs">
        ${product.gallery.map((image, index) => `<button class="gallery-thumb ${index === 0 ? "active" : ""}" data-gallery-image="${image}" aria-label="Ver imagen ${index + 1} de ${product.name}"><img src="${image}" alt="" loading="lazy"></button>`).join("")}
      </div>
    </div>
    <div class="product-info fade-in">
      <p class="product-type">${product.type}</p>
      <h1>${product.name}</h1>
      <span class="product-code">Código ${product.code}</span>
      <p class="product-price">${formatPrice(product.price)}</p>
      <p class="product-financing">6 cuotas de ${formatPrice(Math.round(product.price / 6))} sin interés</p>
      <p class="product-description">${product.description}</p>
      <div class="choice-label"><span>Talle</span><button type="button" data-size-guide>Guía de talles</button></div>
      <div class="size-options" role="group" aria-label="Elegir talle">
        ${["S", "M", "L", "XL"].map(size => `<button class="size-option ${size === "M" ? "active" : ""}" data-size="${size}">${size}</button>`).join("")}
      </div>
      <div class="choice-label"><span>Color</span><span>Edición 2026</span></div>
      <div class="color-options" role="group" aria-label="Elegir color">
        ${product.colors.map((color, index) => `<button class="color-option ${index === 0 ? "active" : ""}" aria-label="Color ${index + 1}"><span style="background:${color}"></span></button>`).join("")}
      </div>
      <div class="buy-row">
        <div class="quantity-control" aria-label="Cantidad"><button type="button" data-product-minus aria-label="Restar">−</button><span data-product-qty>1</span><button type="button" data-product-plus aria-label="Sumar">+</button></div>
        <button class="button button-dark" type="button" data-product-add>Agregar al carrito <i class="fa-solid fa-arrow-right"></i></button>
      </div>
      <p class="stock-note"><span class="stock-dot"></span> Disponible para despacho en 48 horas</p>
    </div>`;

  const specTarget = document.querySelector("[data-product-specs]");
  if (specTarget) specTarget.innerHTML = Object.entries(product.specs).map(([label, value]) => `<div class="spec-row"><span>${label}</span><strong>${value}</strong></div>`).join("");
  const related = document.querySelector("[data-related-grid]");
  if (related) {
    related.innerHTML = PRODUCTS.filter(item => item.id !== product.id).slice(0, 3).map(item => productCard(item, { compact: true })).join("");
    bindQuickAdds(related);
  }

  target.querySelectorAll("[data-gallery-image]").forEach(button => button.addEventListener("click", () => {
    target.querySelectorAll("[data-gallery-image]").forEach(item => item.classList.remove("active"));
    button.classList.add("active");
    const main = target.querySelector("[data-gallery-main]");
    main.style.opacity = "0";
    setTimeout(() => { main.src = button.dataset.galleryImage; main.style.opacity = "1"; }, 170);
  }));
  target.querySelectorAll("[data-size]").forEach(button => button.addEventListener("click", () => {
    target.querySelectorAll("[data-size]").forEach(item => item.classList.remove("active"));
    button.classList.add("active");
  }));
  target.querySelectorAll(".color-option").forEach(button => button.addEventListener("click", () => {
    target.querySelectorAll(".color-option").forEach(item => item.classList.remove("active"));
    button.classList.add("active");
  }));
  let qty = 1;
  const qtyEl = target.querySelector("[data-product-qty]");
  target.querySelector("[data-product-minus]").addEventListener("click", () => { qty = Math.max(1, qty - 1); qtyEl.textContent = qty; });
  target.querySelector("[data-product-plus]").addEventListener("click", () => { qty = Math.min(9, qty + 1); qtyEl.textContent = qty; });
  target.querySelector("[data-product-add]").addEventListener("click", () => {
    const size = target.querySelector("[data-size].active").dataset.size;
    addToCart(product.id, size, qty);
  });
  target.querySelector("[data-size-guide]").addEventListener("click", () => showToast("S: 155–168 · M: 169–177 · L: 178–186 · XL: 187+ cm"));
};

const renderCart = () => {
  const list = document.querySelector("[data-cart-items]");
  if (!list) return;
  const cart = getCart();
  const count = cart.reduce((sum, item) => sum + item.qty, 0);
  const titleCount = document.querySelector("[data-cart-title-count]");
  if (titleCount) titleCount.textContent = `(${count})`;
  const summary = document.querySelector("[data-cart-summary]");

  if (!cart.length) {
    list.innerHTML = `<div class="empty-cart"><i class="fa-solid fa-bicycle"></i><h2>Tu carrito está esperando.</h2><p>Explorá la colección y encontrá la bicicleta para tu próxima salida.</p><a class="button button-dark" href="index.html#bicicletas">Ver bicicletas <i class="fa-solid fa-arrow-right"></i></a></div>`;
    if (summary) summary.hidden = true;
    return;
  }
  if (summary) summary.hidden = false;
  list.innerHTML = cart.map((item, index) => {
    const product = PRODUCTS.find(productItem => productItem.id === item.id);
    if (!product) return "";
    return `<article class="cart-item">
      <a class="cart-item-image" href="producto.html?id=${product.id}"><img src="${product.image}" alt="${product.name}"></a>
      <div class="cart-item-info">
        <div class="cart-item-top"><div><a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a><span class="cart-item-meta">Talle ${item.size} · ${product.type}</span></div><button class="cart-remove" data-cart-remove="${index}" aria-label="Eliminar ${product.name}"><i class="fa-regular fa-trash-can"></i></button></div>
        <div class="cart-item-bottom"><div class="quantity-control"><button data-cart-minus="${index}" aria-label="Restar">−</button><span>${item.qty}</span><button data-cart-plus="${index}" aria-label="Sumar">+</button></div><strong class="cart-item-price">${formatPrice(product.price * item.qty)}</strong></div>
      </div>
    </article>`;
  }).join("");

  const subtotal = cart.reduce((sum, item) => {
    const product = PRODUCTS.find(productItem => productItem.id === item.id);
    return sum + (product ? product.price * item.qty : 0);
  }, 0);
  document.querySelector("[data-cart-subtotal]").textContent = formatPrice(subtotal);
  document.querySelector("[data-cart-total]").textContent = formatPrice(subtotal);
  document.querySelector("[data-installments]").textContent = `o 6 cuotas de ${formatPrice(Math.round(subtotal / 6))} sin interés`;

  list.querySelectorAll("[data-cart-remove]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); current.splice(Number(button.dataset.cartRemove), 1); saveCart(current); renderCart(); showToast("Producto eliminado");
  }));
  list.querySelectorAll("[data-cart-minus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); const item = current[Number(button.dataset.cartMinus)]; item.qty = Math.max(1, item.qty - 1); saveCart(current); renderCart();
  }));
  list.querySelectorAll("[data-cart-plus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart(); const item = current[Number(button.dataset.cartPlus)]; item.qty = Math.min(9, item.qty + 1); saveCart(current); renderCart();
  }));
};

const setupNavigation = () => {
  const menu = document.querySelector(".mobile-menu");
  const backdrop = document.querySelector(".menu-backdrop");
  const toggle = document.querySelector(".menu-toggle");
  const closeMenu = () => {
    if (!menu) return;
    menu.classList.remove("open"); backdrop.classList.remove("open"); document.body.classList.remove("menu-open"); menu.setAttribute("aria-hidden", "true"); toggle?.setAttribute("aria-expanded", "false");
  };
  toggle?.addEventListener("click", () => {
    menu.classList.add("open"); backdrop.classList.add("open"); document.body.classList.add("menu-open"); menu.setAttribute("aria-hidden", "false"); toggle.setAttribute("aria-expanded", "true");
  });
  document.querySelector(".menu-close")?.addEventListener("click", closeMenu);
  backdrop?.addEventListener("click", closeMenu);
  menu?.querySelectorAll("a").forEach(link => link.addEventListener("click", closeMenu));

  const panel = document.querySelector(".search-panel");
  const openSearch = () => { panel?.classList.add("open"); panel?.setAttribute("aria-hidden", "false"); document.body.classList.add("search-open"); setTimeout(() => panel?.querySelector("input")?.focus(), 450); };
  const closeSearch = () => { panel?.classList.remove("open"); panel?.setAttribute("aria-hidden", "true"); document.body.classList.remove("search-open"); };
  document.querySelector(".search-toggle")?.addEventListener("click", openSearch);
  document.querySelector(".search-close")?.addEventListener("click", closeSearch);
  const searchInput = panel?.querySelector("input");
  searchInput?.addEventListener("input", () => {
    const value = searchInput.value.toLowerCase().trim();
    const matches = value ? PRODUCTS.filter(product => `${product.name} ${product.type}`.toLowerCase().includes(value)).slice(0, 5) : [];
    panel.querySelector(".search-results").innerHTML = matches.length ? matches.map(product => `<a href="producto.html?id=${product.id}">${product.name} <i class="fa-solid fa-arrow-right"></i></a>`).join("") : value ? "<span>No encontramos coincidencias.</span>" : "";
  });
  document.addEventListener("keydown", event => { if (event.key === "Escape") { closeMenu(); closeSearch(); } });
};

const setupObserver = () => {
  const elements = document.querySelectorAll(".fade-in");
  if (!("IntersectionObserver" in window)) { elements.forEach(el => el.classList.add("visible")); return; }
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add("visible"); observer.unobserve(entry.target); }
    });
  }, { threshold: .08, rootMargin: "0px 0px -35px" });
  elements.forEach(el => observer.observe(el));
};

document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  renderCatalog();
  renderProductPage();
  renderCart();
  setupNavigation();
  setupObserver();
  document.querySelector(".checkout-button")?.addEventListener("click", () => showToast("Proceso de compra de demostración: tu selección quedó guardada"));
  document.querySelector("[data-coupon]")?.addEventListener("click", () => showToast("Código no válido para esta demostración"));
});
