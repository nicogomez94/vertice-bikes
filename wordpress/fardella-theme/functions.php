<?php
/**
 * Funciones del tema Fardella S.A.
 *
 * @package Fardella
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FARDELLA_THEME_VERSION', '1.4.0' );

function fardella_theme_setup() {
	load_theme_textdomain( 'fardella', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
}
add_action( 'after_setup_theme', 'fardella_theme_setup' );

function fardella_enqueue_assets() {
	wp_enqueue_style(
		'fardella-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'fardella-font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);
	wp_enqueue_style(
		'fardella-site',
		get_template_directory_uri() . '/assets/css/site.css',
		array( 'fardella-fonts', 'fardella-font-awesome' ),
		FARDELLA_THEME_VERSION
	);
	wp_enqueue_script(
		'fardella-site',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		FARDELLA_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'fardella_enqueue_assets' );

function fardella_register_wholesale_roles() {
	$customer      = get_role( 'customer' );
	$customer_caps = $customer ? $customer->capabilities : array( 'read' => true );

	if ( ! get_role( 'fardella_wholesale_pending' ) ) {
		add_role(
			'fardella_wholesale_pending',
			'Solicitud mayorista pendiente',
			array( 'read' => true )
		);
	}

	if ( ! get_role( 'fardella_wholesale_customer' ) ) {
		add_role(
			'fardella_wholesale_customer',
			'Cliente mayorista aprobado',
			$customer_caps
		);
	}
}
add_action( 'init', 'fardella_register_wholesale_roles' );

function fardella_is_wholesale_customer( $user_id = 0 ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	return $user && in_array( 'fardella_wholesale_customer', (array) $user->roles, true );
}

function fardella_public_markup() {
	$value = get_option( 'fardella_public_markup', '' );
	return is_numeric( $value ) && (float) $value > 0 ? (float) $value : 0.0;
}

function fardella_apply_role_price( $price, $product ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $price;
	}

	$wholesale = $product->get_meta( '_fardella_wholesale_price', true );
	if ( '' === $wholesale || ! is_numeric( $wholesale ) ) {
		return $price;
	}

	if ( fardella_is_wholesale_customer() ) {
		return (string) wc_format_decimal( $wholesale );
	}

	$markup = fardella_public_markup();
	if ( $markup > 0 ) {
		return (string) wc_format_decimal( (float) $wholesale * ( 1 + ( $markup / 100 ) ) );
	}

	return '';
}
add_filter( 'woocommerce_product_get_price', 'fardella_apply_role_price', 20, 2 );
add_filter( 'woocommerce_product_get_regular_price', 'fardella_apply_role_price', 20, 2 );
add_filter( 'woocommerce_product_variation_get_price', 'fardella_apply_role_price', 20, 2 );
add_filter( 'woocommerce_product_variation_get_regular_price', 'fardella_apply_role_price', 20, 2 );

function fardella_price_html( $html, $product ) {
	$wholesale = $product->get_meta( '_fardella_wholesale_price', true );
	if ( '' === $wholesale ) {
		return $html;
	}

	if ( ! fardella_is_wholesale_customer() && 0.0 === fardella_public_markup() ) {
		return '<span class="price-on-request">Ingresá para ver tu precio</span>';
	}

	$label = fardella_is_wholesale_customer() ? 'Precio mayorista + IVA' : 'Precio al público';
	return $html . '<small class="fardella-price-label">' . esc_html( $label ) . '</small>';
}
add_filter( 'woocommerce_get_price_html', 'fardella_price_html', 30, 2 );

function fardella_add_to_cart_label() {
	return 'Añadir al carrito';
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'fardella_add_to_cart_label' );
add_filter( 'woocommerce_product_add_to_cart_text', 'fardella_add_to_cart_label' );

add_filter( 'woocommerce_show_page_title', '__return_false' );

function fardella_wholesale_product_field() {
	woocommerce_wp_text_input(
		array(
			'id'                => '_fardella_wholesale_price',
			'label'             => 'Precio mayorista',
			'description'       => 'Visible únicamente para clientes mayoristas aprobados. Ingresar sin símbolo de moneda.',
			'desc_tip'          => true,
			'type'              => 'number',
			'custom_attributes' => array(
				'step' => '0.01',
				'min'  => '0',
			),
		)
	);
}
add_action( 'woocommerce_product_options_pricing', 'fardella_wholesale_product_field' );

function fardella_save_wholesale_product_field( $product ) {
	if ( isset( $_POST['_fardella_wholesale_price'] ) ) {
		$product->update_meta_data(
			'_fardella_wholesale_price',
			wc_format_decimal( wp_unslash( $_POST['_fardella_wholesale_price'] ) )
		);
	}
}
add_action( 'woocommerce_admin_process_product_object', 'fardella_save_wholesale_product_field' );

function fardella_registration_notice_email() {
	$email = sanitize_email( get_option( 'fardella_wholesale_email', 'info@fardella.com.ar' ) );
	return $email ?: get_option( 'admin_email' );
}

function fardella_handle_wholesale_registration() {
	if ( empty( $_POST['fardella_wholesale_register'] ) ) {
		return;
	}

	if ( ! isset( $_POST['fardella_wholesale_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fardella_wholesale_nonce'] ) ), 'fardella_wholesale_register' ) ) {
		wp_die( esc_html__( 'La sesión venció. Volvé a intentar.', 'fardella' ) );
	}

	$reason   = sanitize_text_field( wp_unslash( $_POST['business_name'] ?? '' ) );
	$cuit     = preg_replace( '/\D+/', '', wp_unslash( $_POST['cuit'] ?? '' ) );
	$email    = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$password = (string) wp_unslash( $_POST['password'] ?? '' );
	$errors   = array();

	if ( '' === $reason ) {
		$errors[] = 'Ingresá la razón social.';
	}
	if ( 11 !== strlen( $cuit ) ) {
		$errors[] = 'Ingresá un CUIT válido de 11 dígitos.';
	}
	if ( ! is_email( $email ) ) {
		$errors[] = 'Ingresá un email válido.';
	} elseif ( email_exists( $email ) ) {
		$errors[] = 'Ya existe una cuenta con ese email.';
	}
	if ( '' === $phone ) {
		$errors[] = 'Ingresá un teléfono.';
	}
	if ( strlen( $password ) < 8 ) {
		$errors[] = 'La contraseña debe tener al menos 8 caracteres.';
	}

	if ( $errors ) {
		set_transient( 'fardella_registration_' . wp_get_session_token(), $errors, MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'solicitud', 'error', wp_get_referer() ?: home_url( '/mayoristas/' ) ) );
		exit;
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $email,
			'user_email'   => $email,
			'user_pass'    => $password,
			'display_name' => $reason,
			'role'         => 'fardella_wholesale_pending',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		set_transient( 'fardella_registration_' . wp_get_session_token(), array( $user_id->get_error_message() ), MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'solicitud', 'error', home_url( '/mayoristas/' ) ) );
		exit;
	}

	update_user_meta( $user_id, 'billing_company', $reason );
	update_user_meta( $user_id, 'fardella_cuit', $cuit );
	update_user_meta( $user_id, 'billing_phone', $phone );
	update_user_meta( $user_id, 'fardella_wholesale_status', 'pending' );

	$subject = sprintf( 'Nueva solicitud mayorista: %s', $reason );
	$message = "Se recibió una nueva solicitud mayorista.\n\n";
	$message .= "Razón social: {$reason}\nCUIT: {$cuit}\nEmail: {$email}\nTeléfono: {$phone}\n\n";
	$message .= 'Revisar y aprobar: ' . admin_url( 'user-edit.php?user_id=' . $user_id );
	wp_mail( fardella_registration_notice_email(), $subject, $message );

	wp_safe_redirect( add_query_arg( 'solicitud', 'enviada', home_url( '/mayoristas/' ) ) );
	exit;
}
add_action( 'init', 'fardella_handle_wholesale_registration' );

function fardella_registration_messages() {
	if ( 'enviada' === ( $_GET['solicitud'] ?? '' ) ) {
		return array( 'success' => 'Recibimos tu solicitud. Te avisaremos por email cuando la cuenta esté aprobada.' );
	}

	if ( 'error' === ( $_GET['solicitud'] ?? '' ) ) {
		$errors = get_transient( 'fardella_registration_' . wp_get_session_token() );
		delete_transient( 'fardella_registration_' . wp_get_session_token() );
		return array( 'errors' => $errors ?: array( 'No pudimos procesar la solicitud.' ) );
	}

	return array();
}

function fardella_user_wholesale_fields( $user ) {
	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}
	$status = get_user_meta( $user->ID, 'fardella_wholesale_status', true );
	?>
	<h2>Cuenta mayorista Fardella</h2>
	<table class="form-table" role="presentation">
		<tr><th><label for="fardella_cuit">CUIT</label></th><td><input type="text" class="regular-text" id="fardella_cuit" name="fardella_cuit" value="<?php echo esc_attr( get_user_meta( $user->ID, 'fardella_cuit', true ) ); ?>"></td></tr>
		<tr>
			<th><label for="fardella_wholesale_status">Estado mayorista</label></th>
			<td>
				<select id="fardella_wholesale_status" name="fardella_wholesale_status">
					<option value="none" <?php selected( $status, 'none' ); ?>>No mayorista</option>
					<option value="pending" <?php selected( $status, 'pending' ); ?>>Pendiente</option>
					<option value="approved" <?php selected( $status, 'approved' ); ?>>Aprobado</option>
					<option value="rejected" <?php selected( $status, 'rejected' ); ?>>Rechazado</option>
				</select>
				<p class="description">Al aprobar, el usuario podrá ver precios mayoristas y comprar con su cuenta.</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'fardella_user_wholesale_fields' );
add_action( 'edit_user_profile', 'fardella_user_wholesale_fields' );

function fardella_save_user_wholesale_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	$status = sanitize_key( wp_unslash( $_POST['fardella_wholesale_status'] ?? 'none' ) );
	$cuit   = preg_replace( '/\D+/', '', wp_unslash( $_POST['fardella_cuit'] ?? '' ) );
	$old    = get_user_meta( $user_id, 'fardella_wholesale_status', true );
	$user   = new WP_User( $user_id );

	update_user_meta( $user_id, 'fardella_cuit', $cuit );
	update_user_meta( $user_id, 'fardella_wholesale_status', $status );

	if ( 'approved' === $status ) {
		$user->set_role( 'fardella_wholesale_customer' );
		if ( 'approved' !== $old ) {
			wp_mail(
				$user->user_email,
				'Tu cuenta mayorista Fardella fue aprobada',
				'Tu cuenta ya está habilitada. Ingresá en ' . home_url( '/mi-cuenta/' ) . ' para ver precios mayoristas y armar pedidos.'
			);
		}
	} elseif ( 'pending' === $status ) {
		$user->set_role( 'fardella_wholesale_pending' );
	} elseif ( 'rejected' === $status || 'none' === $status ) {
		$user->set_role( 'customer' );
	}
}
add_action( 'personal_options_update', 'fardella_save_user_wholesale_fields' );
add_action( 'edit_user_profile_update', 'fardella_save_user_wholesale_fields' );

function fardella_wholesale_settings_menu() {
	add_submenu_page(
		'woocommerce',
		'Fardella Mayoristas',
		'Fardella Mayoristas',
		'manage_woocommerce',
		'fardella-wholesale',
		'fardella_wholesale_settings_page'
	);
}
add_action( 'admin_menu', 'fardella_wholesale_settings_menu', 40 );

function fardella_register_settings() {
	register_setting( 'fardella_wholesale', 'fardella_public_markup', array( 'sanitize_callback' => 'floatval' ) );
	register_setting( 'fardella_wholesale', 'fardella_wholesale_email', array( 'sanitize_callback' => 'sanitize_email' ) );
}
add_action( 'admin_init', 'fardella_register_settings' );

function fardella_wholesale_settings_page() {
	?>
	<div class="wrap">
		<h1>Fardella Mayoristas</h1>
		<p>Configurá el precio público y el email que recibe las nuevas solicitudes. Los precios mayoristas se cargan en cada producto.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'fardella_wholesale' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th><label for="fardella_public_markup">Recargo para precio público (%)</label></th>
					<td><input type="number" min="0" step="0.01" id="fardella_public_markup" name="fardella_public_markup" value="<?php echo esc_attr( get_option( 'fardella_public_markup', '' ) ); ?>"><p class="description">Mientras quede vacío o en cero, el público deberá iniciar sesión para ver su precio y nunca verá el valor mayorista.</p></td>
				</tr>
				<tr>
					<th><label for="fardella_wholesale_email">Email de aprobaciones</label></th>
					<td><input type="email" class="regular-text" id="fardella_wholesale_email" name="fardella_wholesale_email" value="<?php echo esc_attr( get_option( 'fardella_wholesale_email', 'info@fardella.com.ar' ) ); ?>"></td>
				</tr>
			</table>
			<?php submit_button( 'Guardar configuración' ); ?>
		</form>
	</div>
	<?php
}

function fardella_cart_count_fragment( $fragments ) {
	$fragments['span.cart-count'] = '<span class="cart-count" data-cart-count>' . esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'fardella_cart_count_fragment' );

function fardella_woocommerce_layout() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'wp', 'fardella_woocommerce_layout' );

require_once get_template_directory() . '/inc/catalog.php';

