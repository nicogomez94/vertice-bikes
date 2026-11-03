const PRODUCTS = [
  {
    id: "sioux-blanco",
    name: "Sioux Dama / Blanco",
    color: "Blanco",
    category: "blanco",
    code: "BICSIOUX275DB",
    price: 272727.27,
    image: "assets/images/skinred/sioux-blanco.jpg",
    swatch: "#e8e7df"
  },
  {
    id: "sioux-negro-brillante",
    name: "Sioux Dama / Negro brillante",
    color: "Negro brillante",
    category: "negro",
    code: "BICSIOUX275DNB",
    price: 272727.27,
    image: "assets/images/skinred/sioux-negro-brillante.jpg",
    swatch: "#1c1c1f"
  },
  {
    id: "sioux-negro-mate",
    name: "Sioux Dama / Negro mate",
    color: "Negro mate",
    category: "negro",
    code: "BICSIOUX275DNM",
    price: 272727.27,
    image: "assets/images/skinred/sioux-negro-mate.jpg",
    swatch: "#3d3b3c"
  }
];

const formatPrice = value => "$ " + new Intl.NumberFormat("es-AR", {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2
}).format(value);

const cartKey = "skinred-demo-cart-v1";

const getCart = () => {
  try {
    return JSON.parse(localStorage.getItem(cartKey)) || [];
  } catch {
    return [];
  }
};

const saveCart = cart => {
  localStorage.setItem(cartKey, JSON.stringify(cart));
  updateCartCount();
};

const updateCartCount = () => {
  const count = getCart().reduce((sum, item) => sum + item.qty, 0);
  document.querySelectorAll("[data-cart-count]").forEach(el => {
    el.textContent = count;
  });
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

const addToCart = (id, size = "15", qty = 1) => {
  const cart = getCart();
  const existing = cart.find(item => item.id === id && item.size === size);
  if (existing) existing.qty += qty;
  else cart.push({ id, size, qty });
  saveCart(cart);
  const product = PRODUCTS.find(item => item.id === id);
  showToast(`${product.name} se agregó al carrito de muestra`);
};

const productCard = product => `
  <article class="product-card" data-category="${product.category}">
    <a class="product-image-wrap" href="producto.html?id=${product.id}" aria-label="Ver ${product.name}">
      <img src="${product.image}" alt="Bicicleta Skin Red ${product.name}" loading="lazy">
      <span class="product-tag">Skin Red / Sioux</span>
    </a>
    <div class="product-card-content">
      <div class="product-card-meta"><span>Rodado 27,5 · 21v</span><span>${product.code}</span></div>
      <a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a>
      <div class="product-card-bottom">
        <div class="product-card-price"><strong>${formatPrice(product.price)}</strong><span>Precio de lista + IVA</span></div>
        <button class="quick-add" data-quick-add="${product.id}" aria-label="Agregar ${product.name} al carrito de muestra"><i class="fa-solid fa-plus"></i></button>
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

  document.querySelectorAll("[data-filter]").forEach(button => {
    button.addEventListener("click", () => {
      document.querySelectorAll("[data-filter]").forEach(item => item.classList.remove("active"));
      button.classList.add("active");
      grid.querySelectorAll(".product-card").forEach(card => {
        card.hidden = button.dataset.filter !== "todos" && card.dataset.category !== button.dataset.filter;
      });
    });
  });
};

const renderProductPage = () => {
  const target = document.querySelector("[data-product-detail]");
  if (!target) return;
  const id = new URLSearchParams(window.location.search).get("id") || PRODUCTS[0].id;
  const product = PRODUCTS.find(item => item.id === id) || PRODUCTS[0];
  document.title = `${product.name} — Skin Red`;
  document.querySelector("[data-product-breadcrumb]").textContent = product.name;

  target.innerHTML = `
    <div class="product-gallery">
      <div class="gallery-main">
        <img src="${product.image}" alt="Bicicleta Skin Red ${product.name}">
      </div>
      <p>Fotografía del catálogo de septiembre 2026</p>
    </div>
    <div class="product-info">
      <p class="product-type">Skin Red · Sioux Dama</p>
      <h1>${product.name}</h1>
      <span class="product-code">Código ${product.code}</span>
      <p class="product-price">${formatPrice(product.price)}</p>
      <p class="product-financing">Precio de lista + IVA · Catálogo septiembre 2026</p>
      <p class="product-description">Bicicleta Skin Red Sioux Dama rodado 27,5 con 21 velocidades. El catálogo indica talles 15 y 17 y componentes Shimano en manija, plato, palanca y cambio.</p>
      <div class="choice-label"><span>Talle</span><span>Elegí una opción</span></div>
      <div class="size-options" role="group" aria-label="Elegir talle">
        <button class="size-option active" type="button" data-size="15" aria-pressed="true">15</button>
        <button class="size-option" type="button" data-size="17" aria-pressed="false">17</button>
      </div>
      <div class="choice-label"><span>Terminación</span><span>Ver otra versión</span></div>
      <div class="color-options" aria-label="Otras versiones">
        ${PRODUCTS.map(item => `<a class="color-option ${item.id === product.id ? "active" : ""}" href="producto.html?id=${item.id}" aria-label="${item.color}" title="${item.color}"><span style="background:${item.swatch}"></span></a>`).join("")}
      </div>
      <div class="buy-row">
        <div class="quantity-control" aria-label="Cantidad">
          <button type="button" data-product-minus aria-label="Restar">−</button>
          <span data-product-qty>1</span>
          <button type="button" data-product-plus aria-label="Sumar">+</button>
        </div>
        <button class="button button-accent" type="button" data-product-add>Agregar a muestra <i class="fa-solid fa-plus"></i></button>
      </div>
      <p class="stock-note"><i class="fa-solid fa-circle-info"></i> Carrito de demostración. Consultar disponibilidad y condiciones comerciales.</p>
    </div>
  `;

  document.querySelector("[data-product-specs]").innerHTML = `
    <div class="spec-row"><span>Modelo</span><strong>Sioux Dama</strong></div>
    <div class="spec-row"><span>Rodado</span><strong>27,5 pulgadas</strong></div>
    <div class="spec-row"><span>Velocidades</span><strong>21</strong></div>
    <div class="spec-row"><span>Talles</span><strong>15 / 17</strong></div>
    <div class="spec-row"><span>Componentes</span><strong>Manija, plato, palanca y cambio Shimano</strong></div>
    <div class="spec-row"><span>Color</span><strong>${product.color}</strong></div>
    <div class="spec-row"><span>Código</span><strong>${product.code}</strong></div>
  `;

  const related = document.querySelector("[data-related-grid]");
  related.innerHTML = PRODUCTS.filter(item => item.id !== product.id).map(productCard).join("");
  bindQuickAdds(related);

  target.querySelectorAll("[data-size]").forEach(button => {
    button.addEventListener("click", () => {
      target.querySelectorAll("[data-size]").forEach(item => {
        item.classList.remove("active");
        item.setAttribute("aria-pressed", "false");
      });
      button.classList.add("active");
      button.setAttribute("aria-pressed", "true");
    });
  });

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
  target.querySelector("[data-product-add]").addEventListener("click", () => {
    const size = target.querySelector("[data-size].active").dataset.size;
    addToCart(product.id, size, qty);
  });
};

const renderCart = () => {
  const list = document.querySelector("[data-cart-items]");
  if (!list) return;
  const cart = getCart().filter(item => PRODUCTS.some(product => product.id === item.id));
  const count = cart.reduce((sum, item) => sum + item.qty, 0);
  document.querySelector("[data-cart-title-count]").textContent = `(${count})`;
  const summary = document.querySelector("[data-cart-summary]");

  if (!cart.length) {
    list.innerHTML = `
      <div class="empty-cart">
        <i class="fa-solid fa-bicycle"></i>
        <h2>Tu selección está vacía.</h2>
        <p>Explorá las tres versiones Skin Red Sioux de esta maqueta.</p>
        <a class="button button-accent" href="index.html#modelos">Ver modelos <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    `;
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
          <div class="cart-item-top">
            <div><a href="producto.html?id=${product.id}"><h3>${product.name}</h3></a><span class="cart-item-meta">Talle ${item.size} · ${product.code}</span></div>
            <button class="cart-remove" data-cart-remove="${index}" aria-label="Eliminar ${product.name}"><i class="fa-regular fa-trash-can"></i></button>
          </div>
          <div class="cart-item-bottom">
            <div class="quantity-control">
              <button data-cart-minus="${index}" aria-label="Restar">−</button>
              <span>${item.qty}</span>
              <button data-cart-plus="${index}" aria-label="Sumar">+</button>
            </div>
            <strong class="cart-item-price">${formatPrice(product.price * item.qty)}</strong>
          </div>
        </div>
      </article>
    `;
  }).join("");

  const subtotal = cart.reduce((sum, item) => {
    const product = PRODUCTS.find(entry => entry.id === item.id);
    return sum + product.price * item.qty;
  }, 0);
  document.querySelector("[data-cart-subtotal]").textContent = formatPrice(subtotal);
  document.querySelector("[data-cart-total]").textContent = formatPrice(subtotal);

  list.querySelectorAll("[data-cart-remove]").forEach(button => button.addEventListener("click", () => {
    const current = getCart();
    current.splice(Number(button.dataset.cartRemove), 1);
    saveCart(current);
    renderCart();
  }));
  list.querySelectorAll("[data-cart-minus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart();
    const item = current[Number(button.dataset.cartMinus)];
    item.qty = Math.max(1, item.qty - 1);
    saveCart(current);
    renderCart();
  }));
  list.querySelectorAll("[data-cart-plus]").forEach(button => button.addEventListener("click", () => {
    const current = getCart();
    const item = current[Number(button.dataset.cartPlus)];
    item.qty = Math.min(9, item.qty + 1);
    saveCart(current);
    renderCart();
  }));
};

const setupNavigation = () => {
  const menu = document.querySelector(".mobile-menu");
  const backdrop = document.querySelector(".menu-backdrop");
  const toggle = document.querySelector(".menu-toggle");
  const closeMenu = () => {
    menu.classList.remove("open");
    backdrop.classList.remove("open");
    menu.setAttribute("aria-hidden", "true");
    toggle.setAttribute("aria-expanded", "false");
  };
  toggle.addEventListener("click", () => {
    menu.classList.add("open");
    backdrop.classList.add("open");
    menu.setAttribute("aria-hidden", "false");
    toggle.setAttribute("aria-expanded", "true");
  });
  document.querySelector(".menu-close").addEventListener("click", closeMenu);
  backdrop.addEventListener("click", closeMenu);
  menu.querySelectorAll("a").forEach(link => link.addEventListener("click", closeMenu));

  const panel = document.querySelector(".search-panel");
  const closeSearch = () => {
    panel.classList.remove("open");
    panel.setAttribute("aria-hidden", "true");
  };
  document.querySelector(".search-toggle")?.addEventListener("click", () => {
    panel.classList.add("open");
    panel.setAttribute("aria-hidden", "false");
    panel.querySelector("input").focus();
  });
  document.querySelector(".search-close")?.addEventListener("click", closeSearch);
  panel?.querySelector("input")?.addEventListener("input", event => {
    const value = event.target.value.toLocaleLowerCase("es").trim();
    const matches = value ? PRODUCTS.filter(product => `${product.name} ${product.code}`.toLocaleLowerCase("es").includes(value)) : [];
    panel.querySelector(".search-results").innerHTML = matches.length
      ? matches.map(product => `<a href="producto.html?id=${product.id}">${product.name} <i class="fa-solid fa-arrow-right"></i></a>`).join("")
      : value ? "<span>No encontramos coincidencias.</span>" : "";
  });
  document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
      closeMenu();
      closeSearch();
    }
  });
};

const setupObserver = () => {
  const elements = document.querySelectorAll(".fade-in");
  if (!("IntersectionObserver" in window)) {
    elements.forEach(el => el.classList.add("visible"));
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
  elements.forEach(el => observer.observe(el));
};

document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  renderCatalog();
  renderProductPage();
  renderCart();
  setupNavigation();
  setupObserver();
  document.querySelector(".checkout-button")?.addEventListener("click", () => {
    showToast("Esta maqueta no procesa compras ni pagos.");
  });
});
