# 02 - Mapa técnico

## 1) Estructura del proyecto (real auditada)

- `vc-profit-lost.php`: bootstrap/plugin header, validación WooCommerce, registro hooks de activación/desactivación.
- `autoload.php`: autoloader custom (namespace -> path) + carga helper procedural.
- `includes/`
  - `class-vc-profit-lost.php`: composición principal y registro de hooks.
  - `class-vcpl-loader.php`: registry de actions/filters.
  - `class-vcpl-role.php`: gestión de roles y ocultamiento en admin users.
  - `class-vcpl-form-handler.php`: alta/edición/borrado de usuarios y notices.
  - `class-vcpl-session-handler.php`: endpoints/account/dashboard/analytics/checkout vendor.
  - `class-vcpl-ajax.php`: endpoint AJAX para acciones previas al borrado.
  - `class-vcpl-product-meta.php`, `class-vcpl-product-config.php`, `class-vcpl-product-csv.php`: extensiones de producto.
  - `admin/`: admin assets, menu pages, profile fields, list tables, views.
  - `public/`: encolado de assets front.
  - `abstracts/`: base list table user.
- `helper/vcpl-functions.php`: capa procedural de utilidades y lógica de negocio (relaciones vendor/customer y métricas).
- `templates/myaccount/`: templates override para vendor dashboard/customers/orders/analytics.
- `assets/js` y `assets/css`: módulos front/admin (ESM) y estilos.

## 2) Módulos principales y responsabilidades

1. **Bootstrap/Dependency Gate**
   - Garantiza WooCommerce activo.
2. **Kernel/Hooks Orchestrator**
   - Inyecta todas las funcionalidades por hooks.
3. **Identity & Roles Domain**
   - Define actores del negocio + visibilidad en admin.
4. **User Management Domain**
   - CRUD users con metadatos de negocio.
5. **Vendor-Customer Relationship Domain**
   - Bidireccionalidad `customers` (vendor) <-> `vendor` (customer).
6. **Vendor Portal Domain (My Account)**
   - Endpoints y panel operativo vendor.
7. **Orders & Profit Analytics Domain**
   - Consultas Woo + cálculos comisión/profit.
8. **Product Rules Domain**
   - Campos custom producto y reglas de compra/visibilidad.
9. **Front/Admin UX Layer (JS/CSS)**
   - Validaciones, dependencias de campos, select2, AJAX.

## 3) Dependencias entre módulos

- `VC_Profit_Lost` depende de casi todos los servicios (alto acoplamiento central).
- `VCPL_Form_Handler` depende de helpers procedurales (`vcpl_*`) y de `VCPL_Role` indirectamente.
- `VCPL_Session_Handler` depende fuertemente de helpers procedurales + Woo APIs + templates.
- List tables dependen de `VCPL()` global para contexto de página/acción.
- Frontend JS depende de objetos localizados `vc`, `vci18n`, `customer_checkout`.

## 4) Punto de entrada por feature

- **Admin usuarios:** `admin.php?page=vendor|customer|shop_manager` -> `html-admin-page-users.php` -> list table / formularios.
- **Admin analytics:** `admin.php?page=analytics` -> `VCPL_Admin_List_Table_Analytics_Vendor`.
- **My Account vendor:** endpoints `customers`, `orders_vendor`, `analytics` añadidos en `init`.
- **Checkout vendor:** hooks checkout + JS auto-fill.
- **Producto:** hooks en editor producto y filtros de pricing/purchasable/qty.

## 5) Flujo de datos (resumen)

### 5.1 Alta/edición cliente por vendor
`Template customers.php` -> POST con nonce -> `VCPL_Form_Handler` -> `wp_insert_user/wp_update_user` -> `update_user_meta` -> sincronización vendor/customer metas -> notice + redirect.

### 5.2 Borrado vendor con clientes
UI delete -> selección estrategia -> AJAX `actions_before_delete_user` -> helper ejecuta `vcpl_change_vendor` o `wp_delete_user` cliente -> respuesta JSON con notices -> submit final delete.

### 5.3 Analytics vendor front
Filtros fecha/estado -> `wc_get_orders` por customer IDs en meta vendor -> mapeo filas + totales + cálculo comisión histórica.

### 5.4 Analytics admin global
Filtros tabla -> SQL directo sobre `wp_posts/wp_postmeta/wp_usermeta` -> obtiene página actual + luego recorre `wc_get_order` por fila para cálculos de totales/profit.

## 6) Puntos de acoplamiento fuerte

- Uso global de `VCPL()` en múltiples capas (service locator implícito).
- Helpers procedurales con side-effects compartidos.
- Dependencia fuerte en estructura legacy de tablas de pedidos Woo.
- Acople a slugs de página (`my-account`, `frontend-manager`).
- Acople a plugin/shortcode externo (`wcpdf_document_link`) y dominio `astra-addon`.

## 7) Extensiones sobre WordPress/WooCommerce

- Roles/capabilities custom.
- Menús y list tables admin custom.
- Endpoints/account menu de Woo.
- Checkout clásico extendido.
- Product admin fields + import/export meta.
- Filtros de visibilidad de precio y compra para guests.

## 8) Templates override detectados

- `templates/myaccount/dashboard.php`
- `templates/myaccount/customers.php`
- `templates/myaccount/orders-vendor.php`
- `templates/myaccount/analytics.php`

Se inyectan vía `woocommerce_locate_template` condicionado al rol `vendor`.

## 9) Librerías/frameworks usados

- PHP nativo + APIs WordPress/WooCommerce.
- jQuery + select2/selectWoo.
- Font Awesome Kit remoto.
- Sin Composer package manager real ni pipeline Node detectado.

## 10) Diagrama textual de flujos críticos

### A) CRUD de usuarios
`Admin/View` -> `Form POST + nonce` -> `Form_Handler(validate + sanitize)` -> `wp_insert_user/wp_update_user/wp_delete_user` -> `helpers relation sync` -> `session notice/wc notice` -> redirect.

### B) Checkout vendor asignado
`Checkout render` -> `customer_order select` -> `JS autofill` -> `checkout_process validate` -> `checkout_customer_id override` -> order queda en customer seleccionado -> `order-received redirect` a `orders_vendor`.

### C) Profit analytics
`Filtro` -> `wc_get_orders/SQL` -> `vcpl_get_commission_percent_by_date` -> `vcpl_get_my_commission_amount` -> render tabla y totales.

