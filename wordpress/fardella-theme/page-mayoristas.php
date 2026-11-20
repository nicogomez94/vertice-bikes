<?php
/**
 * Página de registro y acceso mayorista.
 *
 * @package Fardella
 */

get_header();
$messages = fardella_registration_messages();
$user     = wp_get_current_user();
?>
<main class="wholesale-page">
	<?php if ( ! empty( $messages['success'] ) ) : ?>
		<div class="wholesale-notice success"><i class="fa-solid fa-circle-check"></i><p><?php echo esc_html( $messages['success'] ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $messages['errors'] ) ) : ?>
		<div class="wholesale-notice error"><i class="fa-solid fa-circle-exclamation"></i><div><?php foreach ( $messages['errors'] as $error ) : ?><p><?php echo esc_html( $error ); ?></p><?php endforeach; ?></div></div>
	<?php endif; ?>

	<?php if ( is_user_logged_in() ) : ?>
		<section class="wholesale-account section-pad">
			<?php if ( fardella_is_wholesale_customer( $user->ID ) ) : ?>
				<div class="wholesale-status-icon approved"><i class="fa-solid fa-store"></i></div>
				<p class="eyebrow dark">Cuenta aprobada</p>
				<h2>Todo lo que necesitás para tu local.</h2>
				<p>Hola, <?php echo esc_html( $user->display_name ); ?>. Ya estás viendo los precios mayoristas: recorré el catálogo, agregá productos al carrito y enviá tu pedido.</p>
				<div class="wholesale-account-actions"><a class="button button-accent" href="<?php echo esc_url( home_url( '/#productos' ) ); ?>">Ver catálogo <i class="fa-solid fa-arrow-right"></i></a><a class="button button-dark" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Mi cuenta</a></div>
			<?php elseif ( current_user_can( 'manage_woocommerce' ) ) : ?>
				<div class="wholesale-status-icon approved"><i class="fa-solid fa-user-gear"></i></div>
				<p class="eyebrow dark">Sesión de administración</p>
				<h2>Estás conectado como administrador.</h2>
				<p>Para probar una solicitud nueva, cerrá esta sesión o abrí la página en una ventana privada. Las solicitudes recibidas se aprueban desde el panel.</p>
				<div class="wholesale-account-actions"><a class="button button-accent" href="<?php echo esc_url( admin_url( 'admin.php?page=fardella-wholesale' ) ); ?>">Ver solicitudes <i class="fa-solid fa-arrow-right"></i></a><a class="button button-dark" href="<?php echo esc_url( wp_logout_url( home_url( '/mayoristas/' ) ) ); ?>">Cerrar sesión</a></div>
			<?php else : ?>
				<div class="wholesale-status-icon"><i class="fa-regular fa-clock"></i></div>
				<p class="eyebrow dark">Solicitud en revisión</p>
				<h2>Estamos validando tu comercio.</h2>
				<p>Te enviaremos un email cuando Fardella apruebe la cuenta. Mientras tanto, podés seguir navegando el catálogo público.</p>
				<a class="button button-dark" href="<?php echo esc_url( wp_logout_url( home_url( '/mayoristas/' ) ) ); ?>">Cerrar sesión</a>
			<?php endif; ?>
		</section>
	<?php else : ?>
		<section class="wholesale-access section-pad">
			<div class="wholesale-register">
				<p class="eyebrow dark">Nueva cuenta</p>
				<h2>Registrá tu comercio.</h2>
				<form method="post" class="wholesale-form">
					<?php wp_nonce_field( 'fardella_wholesale_register', 'fardella_wholesale_nonce' ); ?>
					<input type="hidden" name="fardella_wholesale_register" value="1">
					<label><span>Razón social *</span><input type="text" name="business_name" autocomplete="organization" required></label>
					<label><span>CUIT *</span><input type="text" name="cuit" inputmode="numeric" autocomplete="off" pattern="[0-9-]{11,13}" placeholder="30-12345678-9" required></label>
					<label><span>Email *</span><input type="email" name="email" autocomplete="email" required></label>
					<label><span>Teléfono *</span><input type="tel" name="phone" autocomplete="tel" required></label>
					<label class="form-wide"><span>Contraseña *</span><input type="password" name="password" autocomplete="new-password" minlength="8" required><small>Mínimo 8 caracteres.</small></label>
					<button class="button button-accent form-wide" type="submit">Enviar solicitud <i class="fa-solid fa-arrow-right"></i></button>
				</form>
			</div>
			<div class="wholesale-login">
				<p class="eyebrow">Ya soy cliente</p>
				<h2>Ingresá a tu cuenta.</h2>
				<p>Si tu comercio ya fue aprobado, iniciá sesión para ver valores mayoristas y continuar tus pedidos.</p>
				<?php
				wp_login_form(
					array(
						'redirect'       => home_url( '/mayoristas/' ),
						'label_username' => 'Email',
						'label_password' => 'Contraseña',
						'label_remember' => 'Recordarme',
						'label_log_in'   => 'Ingresar',
						'remember'       => true,
					)
				);
				?>
			</div>
		</section>
	<?php endif; ?>

</main>
<?php get_footer(); ?>

