<?php
/**
 * Portada Fardella.
 *
 * @package Fardella
 */

get_header();
$u = get_template_directory_uri();
?>
<main>
	<section class="hero hero-cinematic" aria-labelledby="hero-title">
		<div class="hero-slides" aria-hidden="true">
			<img class="hero-media hero-slide active" src="<?php echo esc_url( $u . '/assets/images/fardella/hero-mtb-cinematic.jpg' ); ?>" alt="" data-hero-slide>
			<img class="hero-media hero-slide" src="<?php echo esc_url( $u . '/assets/images/fardella/hero-drivetrain.jpg' ); ?>" alt="" data-hero-slide>
			<img class="hero-media hero-slide" src="<?php echo esc_url( $u . '/assets/images/fardella/hero-accessories.jpg' ); ?>" alt="" data-hero-slide>
		</div>
		<div class="hero-shade" aria-hidden="true"></div>
		<div class="hero-copy">
			<div class="hero-kicker"><span aria-hidden="true"></span><p class="eyebrow">Fardella · Distribuidor mayorista</p></div>
			<h1 id="hero-title">Todo para tu<br><em>bicicletería.</em></h1>
			<p class="hero-description">Bicicletas, repuestos y accesorios para acompañar el crecimiento de tu negocio.</p>
			<div class="hero-actions">
				<a class="button button-accent" href="#productos">Ingresar al catálogo <i class="fa-solid fa-arrow-right"></i></a>
				<a class="button button-outline" href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Acceso mayorista</a>
			</div>
			<div class="hero-footnote"><span>PRECIOS Y CONDICIONES A CONFIRMAR</span><span>DESDE 1939</span></div>
		</div>
		<div class="hero-pagination" aria-label="Cambiar imagen destacada">
			<button class="active" type="button" data-hero-go="0" aria-label="Ver bicicleta" aria-pressed="true"></button>
			<button type="button" data-hero-go="1" aria-label="Ver transmisión" aria-pressed="false"></button>
			<button type="button" data-hero-go="2" aria-label="Ver accesorios" aria-pressed="false"></button>
		</div>
	</section>

	<section class="trust-strip" aria-label="Qué encontrás en Fardella">
		<article><i class="fa-solid fa-shop-lock"></i><div><strong>Un portal para tu comercio</strong><span>Precios exclusivos con aprobación</span></div></article>
		<article><i class="fa-solid fa-cart-flatbed"></i><div><strong>Armá tu pedido online</strong><span>Elegí productos y cantidades</span></div></article>
		<article><i class="fa-solid fa-comments"></i><div><strong>Atención directa</strong><span>Coordinamos pago y entrega</span></div></article>
	</section>

	<section class="wholesale-callout section-pad" aria-labelledby="wholesale-title">
		<div class="wholesale-callout-copy fade-in">
			<p class="eyebrow">Portal para comercios</p>
			<h2 id="wholesale-title">¿Tenés una<br>bicicletería?</h2>
			<p>Registrá tu comercio y, una vez aprobado, ingresá para ver precios mayoristas y armar pedidos online.</p>
		</div>
		<div class="wholesale-callout-actions fade-in">
			<a class="button button-light" href="<?php echo esc_url( home_url( '/mayoristas/' ) ); ?>">Solicitar cuenta <i class="fa-solid fa-arrow-right"></i></a>
			<a class="text-link light-link" href="<?php echo esc_url( home_url( '/mi-cuenta/' ) ); ?>">Ya tengo cuenta <i class="fa-regular fa-user"></i></a>
		</div>
	</section>

	<section class="categories section-pad" id="categorias">
		<div class="section-heading fade-in"><div><p class="eyebrow dark">Explorá Fardella</p><h2>Más que<br><em>bicicletas.</em></h2></div><p>Una selección del catálogo para equipar cada salida, desde la bicicleta completa hasta los detalles que hacen la diferencia.</p></div>
		<div class="category-grid">
			<a class="category-card" href="<?php echo esc_url( home_url( '/product-category/bicicletas/' ) ); ?>"><i class="fa-solid fa-bicycle"></i><h3>Bicicletas</h3><p>Opciones urbanas y MTB.</p><span>Ver productos <i class="fa-solid fa-arrow-right"></i></span></a>
			<a class="category-card" href="<?php echo esc_url( home_url( '/product-category/repuestos/' ) ); ?>"><i class="fa-solid fa-gears"></i><h3>Repuestos</h3><p>Cámaras, cubiertas y transmisión.</p><span>Ver productos <i class="fa-solid fa-arrow-right"></i></span></a>
			<a class="category-card" href="<?php echo esc_url( home_url( '/product-category/accesorios/' ) ); ?>"><i class="fa-solid fa-glasses"></i><h3>Accesorios</h3><p>Guantes, gafas y más.</p><span>Ver productos <i class="fa-solid fa-arrow-right"></i></span></a>
		</div>
	</section>

	<section class="catalog section-pad" id="productos" aria-labelledby="catalog-title">
		<div class="section-heading fade-in"><div><p class="eyebrow dark">Selección del catálogo</p><h2 id="catalog-title">Elegí lo que<br><em>necesitás.</em></h2></div><p>Consultá los precios del catálogo, elegí las cantidades y armá tu pedido directamente desde el carrito.</p></div>
		<div class="filters fade-in" role="group" aria-label="Filtrar productos">
			<button class="filter active" data-filter="todos">Todos</button>
			<button class="filter" data-filter="bicicletas">Bicicletas</button>
			<button class="filter" data-filter="repuestos">Repuestos</button>
			<button class="filter" data-filter="accesorios">Accesorios</button>
		</div>
		<div class="product-grid">
			<?php
			$catalog = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 12,
					'orderby'        => 'menu_order date',
					'order'          => 'ASC',
				)
			);
			while ( $catalog->have_posts() ) :
				$catalog->the_post();
				$product  = wc_get_product( get_the_ID() );
				$terms    = wp_get_post_terms( get_the_ID(), 'product_cat' );
				$category = $product->get_meta( '_fardella_group', true ) ?: 'otros';
				$type     = $product->get_meta( '_fardella_type', true ) ?: 'Producto';
				$brand    = $product->get_meta( '_fardella_brand', true ) ?: 'Fardella';
				?>
				<article class="product-card" data-product-card data-category="<?php echo esc_attr( $category ); ?>">
					<a class="product-image-wrap" href="<?php the_permalink(); ?>" aria-label="Ver <?php the_title_attribute(); ?>">
						<?php echo wp_kses_post( $product->get_image( 'full', array( 'loading' => 'lazy' ) ) ); ?>
						<span class="product-tag"><?php echo esc_html( $type ); ?></span>
					</a>
					<div class="product-card-content">
						<div class="product-card-meta"><span><?php echo esc_html( $brand ); ?></span><span><?php echo esc_html( $product->get_sku() ); ?></span></div>
						<a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
						<div class="product-card-bottom">
							<div class="product-card-price"><strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong></div>
							<?php if ( $product->is_purchasable() ) : ?>
								<a class="quick-add add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Agregar <?php the_title_attribute(); ?> al carrito"><i class="fa-solid fa-plus"></i></a>
							<?php else : ?>
								<a class="quick-add" href="<?php the_permalink(); ?>" aria-label="Consultar <?php the_title_attribute(); ?>"><i class="fa-solid fa-arrow-right"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<p class="catalog-note">Disponibilidad, precios y condiciones comerciales sujetos a confirmación.</p>
	</section>

	<section class="split-story" id="nosotros">
		<div class="story-image"><img src="<?php echo esc_url( $u . '/assets/images/fardella/toscana-azul.jpg' ); ?>" alt="Bicicleta urbana Toscana azul del catálogo Fardella" loading="lazy"><span class="image-index">FARDELLA / CICLISMO</span></div>
		<div class="story-copy">
			<p class="eyebrow">Quiénes somos</p><h2>Una historia<br>que sigue rodando.</h2>
			<p>Desde 1939, Fardella S.A. fabrica, importa y distribuye productos para el mundo de la bicicleta. Como empresa familiar, acompañamos a comercios con variedad, experiencia y atención cercana.</p>
			<ul><li><i class="fa-solid fa-circle-check"></i> Bicicletas para ciudad y montaña</li><li><i class="fa-solid fa-circle-check"></i> Componentes y repuestos</li><li><i class="fa-solid fa-circle-check"></i> Accesorios para cada salida</li></ul>
			<a class="text-link" href="mailto:info@fardella.com.ar">Hablemos <i class="fa-solid fa-arrow-right"></i></a>
		</div>
	</section>

	<section class="brands section-pad" id="marcas" aria-labelledby="brands-title">
		<div class="brands-heading fade-in"><div><p class="eyebrow dark">Marcas que trabajamos</p><h2 id="brands-title">Respaldo en<br><em>cada elección.</em></h2></div><p>Seleccionamos marcas reconocidas por su calidad, durabilidad y rendimiento para ofrecer soluciones confiables a cada comercio.</p></div>
		<div class="brand-grid">
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/hutchinson.png' ); ?>" alt="Hutchinson" loading="lazy"></div><span class="brand-name">Hutchinson</span><p>Cubiertas de tecnología francesa para ciudad, ruta y terrenos exigentes.</p></article>
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/shimano.svg' ); ?>" alt="Shimano" loading="lazy"></div><span class="brand-name">Shimano</span><p>Transmisiones y componentes de precisión para un andar confiable.</p></article>
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/audax.png' ); ?>" alt="Audax" loading="lazy"></div><span class="brand-name">Audax</span><p>Bicicletas de alto rendimiento con una fuerte presencia en Sudamérica.</p></article>
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/named-sport.png' ); ?>" alt="Named Sport" loading="lazy"></div><span class="brand-name">Named Sport</span><p>Nutrición deportiva pensada para acompañar el rendimiento sobre la bici.</p></article>
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/battle.jpg' ); ?>" alt="Battle" loading="lazy"></div><span class="brand-name">Battle</span><p>Bicicletas versátiles para movilidad urbana, montaña y uso recreativo.</p></article>
			<article class="brand-card fade-in"><div class="brand-logo"><img src="<?php echo esc_url( $u . '/assets/images/brands/skin-red.jpg' ); ?>" alt="Skin Red Native Bikes" loading="lazy"></div><span class="brand-name">Skin Red</span><p>Una marca del grupo Fardella con trayectoria en el mercado ciclista argentino.</p></article>
		</div>
		<p class="brands-more">También trabajamos líneas de <strong>Philco</strong> y <strong>SKD Components</strong>.</p>
	</section>

	<section class="why-us section-pad" id="elegirnos" aria-labelledby="why-title">
		<div class="why-intro fade-in"><p class="eyebrow">Por qué elegirnos</p><h2 id="why-title">Todo lo que necesitás para empezar y disfrutar del ciclismo.</h2><p>Desde Buenos Aires, reunimos bicicletas, repuestos y accesorios para distintos estilos de uso, con atención cercana y logística para todo el país.</p><a class="text-link light-link" href="#productos">Explorar el catálogo <i class="fa-solid fa-arrow-right"></i></a></div>
		<div class="why-grid">
			<article class="why-card fade-in"><i class="fa-solid fa-bicycle"></i><div><h3>Amplio catálogo de bicicletas</h3><p>Modelos urbanos, de montaña y opciones para distintos niveles de experiencia.</p></div></article>
			<article class="why-card fade-in"><i class="fa-solid fa-gears"></i><div><h3>Repuestos originales</h3><p>Componentes, cámaras, cubiertas y transmisión para mantener cada bici a punto.</p></div></article>
			<article class="why-card fade-in"><i class="fa-solid fa-helmet-safety"></i><div><h3>Accesorios esenciales</h3><p>Equipamiento para mejorar la seguridad, el confort y el rendimiento al rodar.</p></div></article>
			<article class="why-card fade-in"><i class="fa-solid fa-shield-halved"></i><div><h3>Compra simple y segura</h3><p>Armá tu selección online y coordiná las condiciones comerciales con el equipo.</p></div></article>
			<article class="why-card fade-in"><i class="fa-solid fa-headset"></i><div><h3>Atención personalizada</h3><p>Asesoramiento directo para encontrar productos, disponibilidad y alternativas.</p></div></article>
			<article class="why-card fade-in"><i class="fa-solid fa-truck-fast"></i><div><h3>Envíos a toda Argentina</h3><p>Logística desde Buenos Aires para comercios y clientes de cualquier provincia.</p></div></article>
		</div>
	</section>

	<section class="inquiry section-pad" id="consulta" aria-labelledby="inquiry-title">
		<div class="inquiry-intro fade-in"><p class="eyebrow dark">Armá tu consulta</p><h2 id="inquiry-title">Contanos qué<br><em>necesitás.</em></h2><p>Dejanos los datos de tu comercio y el tipo de producto que buscás. El equipo de Fardella te acompaña con disponibilidad, alternativas y condiciones comerciales.</p><div class="inquiry-notes"><span><i class="fa-solid fa-check"></i> Respuesta personalizada</span><span><i class="fa-solid fa-check"></i> Atención mayorista</span><span><i class="fa-solid fa-check"></i> Envíos a todo el país</span></div></div>
		<form class="inquiry-form fade-in" data-inquiry-form>
			<div class="form-grid"><label><span>Nombre y apellido *</span><input type="text" name="nombre" autocomplete="name" required placeholder="Tu nombre"></label><label><span>Comercio</span><input type="text" name="comercio" autocomplete="organization" placeholder="Nombre del comercio"></label><label><span>Email *</span><input type="email" name="email" autocomplete="email" required placeholder="nombre@comercio.com"></label><label><span>Teléfono</span><input type="tel" name="telefono" autocomplete="tel" placeholder="11 0000 0000"></label><label class="form-wide"><span>¿Qué estás buscando? *</span><select name="interes" required><option value="">Elegí una opción</option><option>Bicicletas</option><option>Cámaras, cubiertas o transmisión</option><option>Accesorios</option><option>Cuenta mayorista</option><option>Otro producto</option></select></label><label class="form-wide"><span>Tu consulta *</span><textarea name="mensaje" rows="4" required placeholder="Contanos cantidades, modelos o cualquier detalle que nos ayude a orientarte."></textarea></label></div>
			<div class="form-submit"><button class="button button-accent" type="submit">Preparar consulta <i class="fa-solid fa-arrow-right"></i></button><p data-form-status aria-live="polite">La consulta se prepara en tu correo para que puedas revisarla antes de enviarla.</p></div>
		</form>
	</section>

	<section class="feature-banner section-pad" id="contacto">
		<div class="feature-copy"><p class="eyebrow">Atención directa</p><h2>¿Buscás<br><em>algo más?</em></h2><p>Consultanos por bicicletas, repuestos, accesorios o condiciones para revendedores.</p><div class="contact-actions"><a class="button button-light" href="mailto:info@fardella.com.ar">Escribinos <i class="fa-solid fa-envelope"></i></a><a class="contact-phone" href="tel:+541160204963">11-6020-4963 <i class="fa-solid fa-phone"></i></a></div></div>
		<div class="feature-mark" aria-hidden="true">FARDELLA</div>
	</section>
</main>
<?php get_footer(); ?>

