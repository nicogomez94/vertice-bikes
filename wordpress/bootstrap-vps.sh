#!/usr/bin/env bash
set -euo pipefail

SLUG="fardella"
BASE_DIR="/var/www/sites/$SLUG"
REPO_DIR="$BASE_DIR/repo"
APP_DIR="$BASE_DIR/app"
THEME_NAME="fardella-theme"
REPO_URL="https://github.com/nicogomez94/vertice-bikes.git"
BRANCH="main"
DOMAIN="fardella.149.34.224.87.sslip.io"
FUTURE_DOMAINS="fardella.com.ar www.fardella.com.ar"
DB_NAME="fardella_wp"
DB_USER="fardella_wp"
ADMIN_USER="fardella_admin"
ADMIN_EMAIL="info@fardella.com.ar"
CREDENTIALS_FILE="/root/.fardella-credentials"
NGINX_SITE="/etc/nginx/sites-available/$SLUG"
DEPLOY_SCRIPT="/usr/local/bin/deploy-$SLUG.sh"
PHP_SOCKET="/run/php/php8.3-fpm.sock"

fail() {
  echo "ERROR: $*" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "Este script requiere root"
[ -S "$PHP_SOCKET" ] || fail "No existe $PHP_SOCKET"

if [ -e "$BASE_DIR" ] && [ ! -f "$BASE_DIR/.fardella-managed" ]; then
  fail "$BASE_DIR ya existe y no pertenece a este despliegue"
fi

echo "== Preparando directorios aislados =="
mkdir -p "$BASE_DIR" "$APP_DIR"
touch "$BASE_DIR/.fardella-managed"

echo "== Sincronizando repositorio =="
if [ ! -d "$REPO_DIR/.git" ]; then
  git clone --branch "$BRANCH" --single-branch "$REPO_URL" "$REPO_DIR"
else
  git -C "$REPO_DIR" pull --ff-only
fi

echo "== Generando credenciales del sitio =="
if [ ! -f "$CREDENTIALS_FILE" ]; then
  DB_PASS="$(openssl rand -hex 20)"
  WP_ADMIN_PASS="$(openssl rand -base64 30 | tr -d '\n/=+' | cut -c1-28)"
  {
    printf 'DB_PASS=%s\n' "$DB_PASS"
    printf 'WP_ADMIN_PASS=%s\n' "$WP_ADMIN_PASS"
    printf 'WP_ADMIN_USER=%s\n' "$ADMIN_USER"
    printf 'WP_ADMIN_URL=https://%s/wp-admin/\n' "$DOMAIN"
  } > "$CREDENTIALS_FILE"
  chmod 600 "$CREDENTIALS_FILE"
fi
# shellcheck disable=SC1090
source "$CREDENTIALS_FILE"

echo "== Creando base y usuario dedicados =="
mysql --protocol=socket <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "== Instalando WordPress core en webroot nuevo =="
if [ ! -d "$APP_DIR/wp-admin" ] || [ ! -d "$APP_DIR/wp-includes" ] || [ ! -f "$APP_DIR/index.php" ]; then
  wp core download --path="$APP_DIR" --force --allow-root
fi

if [ ! -f "$APP_DIR/wp-config.php" ]; then
  wp config create \
    --path="$APP_DIR" \
    --dbname="$DB_NAME" \
    --dbuser="$DB_USER" \
    --dbpass="$DB_PASS" \
    --dbhost="localhost" \
    --dbcharset="utf8mb4" \
    --skip-check \
    --allow-root
  wp config set DISALLOW_FILE_EDIT true --raw --path="$APP_DIR" --allow-root
  wp config set WP_DEBUG false --raw --path="$APP_DIR" --allow-root
fi

mkdir -p "$APP_DIR/wp-content/uploads" "$APP_DIR/wp-content/cache"
chown -R root:www-data "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 775 {} +
find "$APP_DIR" -type f -exec chmod 664 {} +
chown -R www-data:www-data "$APP_DIR/wp-content"
chown www-data:www-data "$APP_DIR/wp-config.php"
chmod 640 "$APP_DIR/wp-config.php"

WP="sudo -u www-data wp --path=$APP_DIR"

if ! $WP core is-installed >/dev/null 2>&1; then
  $WP core install \
    --url="http://$DOMAIN" \
    --title="Fardella S.A." \
    --admin_user="$ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email
fi

echo "== Instalando WooCommerce y tema Fardella =="
if ! $WP plugin is-installed woocommerce >/dev/null 2>&1; then
  $WP plugin install woocommerce
fi
$WP plugin activate woocommerce

rsync -a --delete "$REPO_DIR/wordpress/$THEME_NAME/" "$APP_DIR/wp-content/themes/$THEME_NAME/"
chown -R root:www-data "$APP_DIR/wp-content/themes/$THEME_NAME"
find "$APP_DIR/wp-content/themes/$THEME_NAME" -type d -exec chmod 775 {} +
find "$APP_DIR/wp-content/themes/$THEME_NAME" -type f -exec chmod 664 {} +
$WP theme activate "$THEME_NAME"

echo "== Configurando WordPress y WooCommerce en español =="
$WP language core install es_ES || true
$WP site switch-language es_ES || $WP option update WPLANG es_ES
$WP option update timezone_string America/Argentina/Buenos_Aires
$WP option update date_format 'd/m/Y'
$WP option update time_format 'H:i'
$WP option update permalink_structure '/%postname%/'
$WP option delete rewrite_rules || true
$WP option update woocommerce_currency ARS
$WP option update woocommerce_default_country 'AR:B'
$WP option update woocommerce_currency_pos left_space
$WP option update woocommerce_enable_myaccount_registration yes
$WP option update woocommerce_enable_guest_checkout yes
$WP option update fardella_wholesale_email "$ADMIN_EMAIL"

create_page() {
  local title="$1"
  local slug="$2"
  local content="$3"
  local page_id
  page_id="$($WP post list --post_type=page --name="$slug" --field=ID --format=ids | awk '{print $1}')"
  if [ -z "$page_id" ]; then
    page_id="$($WP post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" --post_content="$content" --porcelain)"
  fi
  printf '%s' "$page_id"
}

HOME_ID="$(create_page 'Inicio' 'inicio' '')"
SHOP_ID="$(create_page 'Tienda' 'tienda' '')"
CART_ID="$(create_page 'Carrito' 'carrito' '[woocommerce_cart]')"
CHECKOUT_ID="$(create_page 'Finalizar pedido' 'finalizar-pedido' '[woocommerce_checkout]')"
ACCOUNT_ID="$(create_page 'Mi cuenta' 'mi-cuenta' '[woocommerce_my_account]')"
WHOLESALE_ID="$(create_page 'Mayoristas' 'mayoristas' '')"

$WP option update show_on_front page
$WP option update page_on_front "$HOME_ID"
$WP option update woocommerce_shop_page_id "$SHOP_ID"
$WP option update woocommerce_cart_page_id "$CART_ID"
$WP option update woocommerce_checkout_page_id "$CHECKOUT_ID"
$WP option update woocommerce_myaccount_page_id "$ACCOUNT_ID"
$WP option update woocommerce_cod_settings '{"enabled":"yes","title":"Pedido sujeto a confirmación","description":"Enviá el pedido y el equipo de Fardella confirmará disponibilidad, pago y entrega.","instructions":"Nos comunicaremos para coordinar las condiciones comerciales.","enable_for_methods":[],"enable_for_virtual":"yes"}' --format=json

$WP post delete "$($WP post list --post_type=post --name=hola-mundo --field=ID --format=ids)" --force 2>/dev/null || true
$WP post delete "$($WP post list --post_type=page --name=pagina-de-ejemplo --field=ID --format=ids)" --force 2>/dev/null || true

echo "== Importando catálogo inicial =="
$WP eval 'fardella_seed_catalog();'
$WP cache flush || true

echo "== Configurando nginx aislado =="
if [ -e "$NGINX_SITE" ] && ! grep -q "FARDELLA_MANAGED" "$NGINX_SITE"; then
  fail "$NGINX_SITE existe y no pertenece a Fardella"
fi

cat > "$NGINX_SITE" <<NGINX
# FARDELLA_MANAGED
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN $FUTURE_DOMAINS;

    root $APP_DIR;
    index index.php index.html;
    client_max_body_size 64m;

    access_log /var/log/nginx/fardella.access.log;
    error_log /var/log/nginx/fardella.error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:$PHP_SOCKET;
        fastcgi_read_timeout 120;
    }

    location ~* \.(?:css|js|jpg|jpeg|gif|png|webp|svg|ico|woff|woff2|ttf|eot)\$ {
        expires 30d;
        access_log off;
        try_files \$uri =404;
    }

    location ~ /\. {
        deny all;
    }

    location = /xmlrpc.php {
        deny all;
    }
}
NGINX

ln -sfn "$NGINX_SITE" "/etc/nginx/sites-enabled/$SLUG"
nginx -t
systemctl reload nginx

echo "== Habilitando HTTPS para el dominio técnico =="
if ! certbot certificates 2>/dev/null | grep -q "Certificate Name: $DOMAIN"; then
  certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos -m "$ADMIN_EMAIL" --redirect
fi
$WP option update home "https://$DOMAIN"
$WP option update siteurl "https://$DOMAIN"

cat > "$DEPLOY_SCRIPT" <<'DEPLOY'
#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="/var/www/sites/fardella/repo"
APP_DIR="/var/www/sites/fardella/app"
THEME_NAME="fardella-theme"
BACKUP_DIR="/var/backups/wordpress-fardella/$(date +%Y%m%d-%H%M%S)"

mkdir -p "$BACKUP_DIR"
cp -a "$APP_DIR/wp-config.php" "$BACKUP_DIR/wp-config.php"
cp -a /etc/nginx/sites-available/fardella "$BACKUP_DIR/nginx-site"

git -C "$REPO_DIR" pull --ff-only
rsync -a --delete "$REPO_DIR/wordpress/$THEME_NAME/" "$APP_DIR/wp-content/themes/$THEME_NAME/"
chown -R root:www-data "$APP_DIR/wp-content/themes/$THEME_NAME"
find "$APP_DIR/wp-content/themes/$THEME_NAME" -type d -exec chmod 775 {} +
find "$APP_DIR/wp-content/themes/$THEME_NAME" -type f -exec chmod 664 {} +
sudo -u www-data wp --path="$APP_DIR" cache flush || true
nginx -t
systemctl reload php8.3-fpm
systemctl reload nginx
curl -fsSI https://fardella.149.34.224.87.sslip.io/ >/dev/null
echo "Deploy Fardella OK. Backup: $BACKUP_DIR"
DEPLOY
chmod +x "$DEPLOY_SCRIPT"

for rc_file in /root/.bashrc /root/.zshrc; do
  [ -e "$rc_file" ] || [ "$rc_file" = "/root/.bashrc" ] || continue
  touch "$rc_file"
  grep -q "alias dirfardella=" "$rc_file" || echo "alias dirfardella='cd $REPO_DIR'" >> "$rc_file"
  grep -q "alias redeployfardella=" "$rc_file" || echo "alias redeployfardella='$DEPLOY_SCRIPT'" >> "$rc_file"
done

chown -R root:www-data "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 775 {} +
find "$APP_DIR" -type f -exec chmod 664 {} +
chown -R www-data:www-data "$APP_DIR/wp-content/uploads" "$APP_DIR/wp-content/cache"
chown www-data:www-data "$APP_DIR/wp-config.php"
chmod 640 "$APP_DIR/wp-config.php"

echo "== Smoke tests =="
curl -fsSI "https://$DOMAIN/" >/dev/null
curl -fsS "https://$DOMAIN/wp-json/" | head -c 120 >/dev/null
curl -fsSI "https://$DOMAIN/wp-admin/" >/dev/null
$WP core is-installed
$WP plugin is-active woocommerce
$WP theme status "$THEME_NAME" >/dev/null
nginx -t

echo "Fardella WordPress desplegado en https://$DOMAIN/"

