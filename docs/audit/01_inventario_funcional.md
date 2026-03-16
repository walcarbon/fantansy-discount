# 01 - Inventario funcional

## Lista numerada de funcionalidades detectadas (muy detallada)

1. **Bootstrap del plugin y validación de dependencia WooCommerce**
   - **Objetivo de negocio:** impedir ejecución sin WooCommerce y guiar instalación/activación.
   - **Ámbito:** backend (admin notices) + bootstrap global.
   - **Flujo:** `plugins_loaded` -> carga textdomain -> valida `did_action('woocommerce_loaded')` -> si falla muestra notice con CTA de instalar/activar WooCommerce.
   - **Archivos:** `vc-profit-lost.php`.
   - **Hooks/funciones:** `add_action('plugins_loaded')`, `admin_notices`, `get_plugins`, `_is_woocommerce_installed()`.
   - **Dependencia:** WordPress core + WooCommerce core.
   - **Estado:** implementada.
   - **Observaciones/huecos:** usa helper `_is_woocommerce_installed()` y lógica de redirect con `$_SERVER`; hay inconsistencias y hook `admin_init` anidado dentro de función de redirect.

2. **Orquestación de módulos por loader custom**
   - **Objetivo:** centralizar registro de hooks admin/public.
   - **Ámbito:** ambos.
   - **Flujo:** `VC_Profit_Lost->__construct()` crea servicios -> `define_admin_hooks()`/`define_public_hooks()` -> `run()` registra `add_action`/`add_filter`.
   - **Archivos:** `includes/class-vc-profit-lost.php`, `includes/class-vcpl-loader.php`, `autoload.php`.
   - **Hooks/funciones:** wrapper `VCPL_Loader::add_action/add_filter/run`.
   - **Dependencia:** WordPress core.
   - **Estado:** implementada parcial.
   - **Observaciones:** `instance()` no es singleton real (devuelve `new self()` siempre); autoload typo `vcpl_autolaod`.

3. **Gestión de roles custom y ocultamiento en Users nativo**
   - **Objetivo:** separar actores del negocio (vendor/customer by vendor/customer by shop/wholesale) del flujo nativo WP.
   - **Ámbito:** backend + permisos globales.
   - **Flujo:** `init` crea roles (excepto `shop_manager`) y elimina rol `customer`; filtra listas de roles y users table de `/wp-admin/users.php`.
   - **Archivos:** `includes/class-vcpl-role.php`, `includes/class-vc-profit-lost.php`.
   - **Hooks:** `init`, `editable_roles`, `pre_get_users`, `views_users`.
   - **Dependencia:** WordPress core.
   - **Estado:** implementada con riesgo.
   - **Observaciones:** borrar rol `customer` es decisión invasiva y riesgosa para WooCommerce y terceros.

4. **Backoffice custom de usuarios (menús top-level)**
   - **Objetivo:** administrar Vendors, Customers, Shop Managers y Analytics desde menús dedicados.
   - **Ámbito:** backend.
   - **Flujo:** `admin_menu` -> builder registra 4 top-level pages -> cada una renderiza vista propia.
   - **Archivos:** `includes/admin/class-vcpl-admin-menu-page.php`, `includes/class-vcpl-build-menu-page.php`, vistas en `includes/admin/views/`.
   - **Hooks:** `admin_menu`.
   - **Dependencia:** WordPress core.
   - **Estado:** implementada.
   - **Observaciones:** capacidades fijadas en `manage_options` (solo admins).

5. **Listados custom de Vendors/Customers/Shop Managers con filtros y bulk delete**
   - **Objetivo:** operar usuarios de negocio con columnas de negocio (commission, salary, vendor, total spend).
   - **Ámbito:** backend.
   - **Flujo:** vistas invocan clase de list-table según página -> `prepare_items()` -> filtros por fecha, búsqueda, ordenación, acciones masivas.
   - **Archivos:**
     - `includes/abstracts/abstract-class-vcpl-admin-list-table-user.php`
     - `includes/admin/list-tables/class-vcpl-admin-list-table-vendor.php`
     - `includes/admin/list-tables/class-vcpl-admin-list-table-customer.php`
     - `includes/admin/list-tables/class-vcpl-admin-list-table-shop-manager.php`
     - `includes/admin/views/html-admin-page-users.php`
   - **Hooks/filtros:** `vcpl_user_table_*` y derivados por tipo.
   - **Dependencia:** WordPress core + WooCommerce (`wc_get_customer_total_spent`).
   - **Estado:** implementada.
   - **Observaciones:** mezcla SQL/`get_users`; uso de `$_SERVER['PHP_SELF']` en forms.

6. **Formulario alta/edición/borrado de usuarios de negocio (admin + frontend vendor)**
   - **Objetivo:** CRUD completo de usuarios con metadatos extendidos.
   - **Ámbito:** ambos.
   - **Flujo:** formularios (`action=add_user|edit_user|delete_user`) + nonce -> `VCPL_Form_Handler` en `admin_init` y `template_redirect`.
   - **Archivos:**
     - `includes/class-vcpl-form-handler.php`
     - `includes/admin/views/html-new-user.php`
     - `includes/admin/views/html-edit-profile.php`
     - `includes/admin/views/html-delete-user.php`
     - `templates/myaccount/customers.php`
   - **Hooks:** `admin_init`, `template_redirect`, acciones custom `vcpl_save_new_user`, `vcpl_update_profile`.
   - **Dependencia:** WordPress core + WooCommerce notices.
   - **Estado:** implementada parcial.
   - **Observaciones:** usa `session_start()` para notices en admin; lógica de borrado compleja y frágil; no siempre valida capacidades en capa handler.

7. **Perfil extendido por rol (meta fields dinámicos)**
   - **Objetivo:** capturar datos de negocio: tax_id_ein, vendor asignado, salary, commission, address billing/shipping.
   - **Ámbito:** backend (admin forms) y parcialmente frontend vendor.
   - **Flujo:** filtros `vcpl_new_user_fields`/`vcpl_edit_profile_fields` construyen secciones y campos con `woocommerce_form_field`.
   - **Archivos:** `includes/admin/class-vcpl-admin-profile.php`.
   - **Hooks/filtros:** múltiples `vcpl_*_meta_fields`.
   - **Dependencia:** WooCommerce checkout fields + WordPress user meta.
   - **Estado:** implementada.
   - **Observaciones:** gran cantidad de HTML en clase PHP; fuerte acoplamiento presentación-lógica.

8. **Asignación de clientes a vendor y cambio de vendor**
   - **Objetivo:** mantener cartera de clientes por vendedor.
   - **Ámbito:** ambos.
   - **Flujo:** al guardar user meta se manipulan metas `customers` (vendor) y `vendor` (customer) con helpers `vcpl_set_my_customer_id`, `vcpl_delete_my_customer_id`, `vcpl_change_vendor`.
   - **Archivos:** `helper/vcpl-functions.php`, `includes/class-vcpl-form-handler.php`.
   - **Dependencia:** WordPress usermeta.
   - **Estado:** implementada.
   - **Observaciones:** no hay transacciones/locking; potencial inconsistencia bidireccional.

9. **Acciones previas al borrado de vendor (reasignar/borrar clientes) por AJAX**
   - **Objetivo:** evitar pérdida accidental de cartera al eliminar vendor.
   - **Ámbito:** backend.
   - **Flujo:** vista delete muestra opciones radio -> JS dispara `wp_ajax_actions_before_delete_user` -> ejecuta `change_vendor` o `delete_customer` y retorna notices HTML.
   - **Archivos:**
     - `includes/class-vcpl-ajax.php`
     - `helper/vcpl-functions.php`
     - `assets/js/modules/vcpl-delete-user.js`
     - `includes/admin/views/html-delete-user.php`
   - **Hooks/endpoints:** `wp_ajax_actions_before_delete_user`.
   - **Dependencia:** WordPress AJAX + WooCommerce form helpers.
   - **Estado:** implementada parcial.
   - **Observaciones:** respuesta mezcla lógica y HTML renderizado; validaciones de capacidad ausentes en endpoint AJAX.

10. **Dashboard custom de vendor en My Account**
   - **Objetivo:** mostrar KPIs de ventas, profit semanal y top clientes.
   - **Ámbito:** frontend (cuenta de vendor).
   - **Flujo:** override template dashboard para role `vendor` -> render cards y tablas vía filtros `vcpl_dashboard_vendor_cards` y `vcpl_analysis_tables`.
   - **Archivos:** `templates/myaccount/dashboard.php`, `includes/class-vcpl-session-handler.php`, `helper/vcpl-functions.php`.
   - **Dependencia:** WooCommerce orders API + usermeta.
   - **Estado:** implementada.
   - **Observaciones:** cálculos con `wc_get_orders(limit=-1)` (costoso en volumen).

11. **Endpoints custom de My Account para vendors**
   - **Objetivo:** UX dedicada para gestionar customers, orders y analytics.
   - **Ámbito:** frontend.
   - **Flujo:** `init` añade endpoints (`customers`, `orders_vendor`, `analytics`) + query vars + menú account custom + contenido por endpoint.
   - **Archivos:** `includes/class-vcpl-session-handler.php`, `templates/myaccount/*.php`.
   - **Hooks:** `add_rewrite_endpoint`, `query_vars`, `woocommerce_account_*_endpoint`, `the_title`, `woocommerce_account_menu_items`.
   - **Dependencia:** WooCommerce My Account.
   - **Estado:** implementada con riesgo.
   - **Observaciones:** ejecuta `flush_rewrite_rules()` en cada request (grave).

12. **Restricciones de navegación por rol en My Account/front**
   - **Objetivo:** impedir acceso de roles no permitidos y limitar menú vendor.
   - **Ámbito:** frontend.
   - **Flujo:** `template_redirect` redirige shop_manager y usuarios no permitidos; bloquea endpoints nativos para vendor (`downloads`, `orders`, `edit-address`).
   - **Archivos:** `includes/class-vcpl-session-handler.php`.
   - **Hooks:** `template_redirect`.
   - **Dependencia:** WooCommerce + WP roles.
   - **Estado:** implementada.
   - **Observaciones:** múltiples redirects sin `wp_safe_redirect`; acople a slugs fijos (`frontend-manager`).

13. **Checkout extendido para vendors (asignar pedido a cliente existente)**
   - **Objetivo:** permitir que vendor compre en nombre de cliente asignado.
   - **Ámbito:** frontend checkout.
   - **Flujo:** se inyecta select `customer_order` en billing -> validación requerida -> filtro `woocommerce_checkout_customer_id` sustituye customer del pedido -> JS rellena billing/shipping desde objeto localizado.
   - **Archivos:**
     - `includes/class-vcpl-session-handler.php`
     - `includes/public/class-vcpl-public.php`
     - `assets/js/modules/vcpl-customer-checkout.js`
   - **Hooks:** `woocommerce_checkout_billing`, `woocommerce_checkout_process`, `woocommerce_checkout_customer_id`.
   - **Dependencia:** WooCommerce checkout clásico (shortcode/template, no blocks).
   - **Estado:** implementada parcial.
   - **Observaciones:** no integra Checkout Blocks/Store API; método `update_customer_billing_shipping` existe pero no está hookeado.

14. **Cambio de textos de checkout y redirección post-compra vendor**
   - **Objetivo:** adaptar UX para caso “vendor creando orden”.
   - **Ámbito:** frontend.
   - **Flujo:** `gettext` cambia “Customer information” (dominio `astra-addon`) -> tras `order-received` redirige a endpoint `orders_vendor` y agrega notice.
   - **Archivos:** `includes/class-vcpl-session-handler.php`.
   - **Hooks:** `gettext`, `template_redirect`.
   - **Dependencia:** WooCommerce + tema/plugin Astra addon (acople implícito).
   - **Estado:** implementada parcial.
   - **Observaciones:** hardcode de dominio textual externo; susceptible a roturas por traducciones/tema.

15. **Analítica de órdenes y profit para vendor (frontend)**
   - **Objetivo:** listar órdenes filtrables y profit por comisión.
   - **Ámbito:** frontend.
   - **Flujo:** formularios de filtro fecha/estado -> `wc_get_orders` por clientes asignados -> tablas orders/analytics con totales.
   - **Archivos:** `templates/myaccount/orders-vendor.php`, `templates/myaccount/analytics.php`, `includes/class-vcpl-session-handler.php`, `helper/vcpl-functions.php`.
   - **Dependencia:** WooCommerce orders API.
   - **Estado:** implementada.
   - **Observaciones:** recomputa consultas múltiples veces por render (ineficiencia).

16. **Analítica global admin vendor-profit (tabla optimizada SQL)**
   - **Objetivo:** dar visibilidad global por vendor/customer/order/profit con filtros.
   - **Ámbito:** backend.
   - **Flujo:** `VCPL_Admin_List_Table_Analytics_Vendor` construye SQL directo contra `posts/postmeta/usermeta/users`; calcula totales; pagina.
   - **Archivos:** `includes/admin/list-tables/class-vcpl-admin-list-table-analytics-vendor.php`, `includes/admin/views/html-admin-page-analytics.php`.
   - **Dependencia:** WordPress DB schema legacy de WooCommerce.
   - **Estado:** implementada con alto riesgo.
   - **Observaciones:** probable incompatibilidad con HPOS al no usar CRUD/Order Data Store.

17. **Metadatos de producto custom en admin y persistencia**
   - **Objetivo:** guardar ubicación y múltiplo mínimo de compra por producto.
   - **Ámbito:** backend producto + impacto frontend carrito.
   - **Flujo:** hooks en pestañas inventario/pricing -> campos `_product_location` y `_items_per_products` -> guardado en `woocommerce_admin_process_product_object`.
   - **Archivos:** `includes/class-vcpl-product-meta.php`.
   - **Dependencia:** WooCommerce product admin.
   - **Estado:** implementada.

18. **Import/Export CSV de metadatos custom de producto**
   - **Objetivo:** soportar migración masiva de campos custom.
   - **Ámbito:** backend.
   - **Flujo:** añade meta keys al export e import mapping por defecto.
   - **Archivos:** `includes/class-vcpl-product-csv.php`.
   - **Dependencia:** WooCommerce CSV importer/exporter.
   - **Estado:** implementada.

19. **Reglas de visibilidad/precio/compra para usuarios no logueados**
   - **Objetivo:** catálogo B2B-like (precios solo para autenticados).
   - **Ámbito:** frontend tienda/producto/cart.
   - **Flujo:** `woocommerce_get_price_html` muestra “Login to view prices”; `woocommerce_is_purchasable` devuelve false para guests.
   - **Archivos:** `includes/class-vcpl-product-config.php`.
   - **Dependencia:** WooCommerce catálogo.
   - **Estado:** implementada.

20. **Control de cantidad en loop/single según múltiplo por producto**
   - **Objetivo:** forzar compra en múltiplos de `_items_per_products`.
   - **Ámbito:** frontend producto/listing.
   - **Flujo:** filtro `woocommerce_quantity_input_args` ajusta `min/step`; JS corrige `data-quantity` en botón add-to-cart.
   - **Archivos:** `includes/class-vcpl-product-config.php`, `assets/js/modules/vcpl-product-config.js`.
   - **Dependencia:** WooCommerce quantity inputs.
   - **Estado:** implementada parcial.
   - **Observaciones:** validación final server-side insuficiente para escenarios no JS/API.

21. **Carga de assets admin/frontend y módulos ESM**
   - **Objetivo:** habilitar UI dinámica (media, validaciones, dependencias de campos, select2, AJAX).
   - **Ámbito:** ambos.
   - **Flujo:** enqueue CSS/JS + `script_loader_tag` para `type="module"` + localización (`vc`, `vci18n`, `customer_checkout`).
   - **Archivos:** `includes/admin/class-vcpl-admin.php`, `includes/public/class-vcpl-public.php`, `assets/js/*`, `assets/css/*`.
   - **Dependencia:** WP scripts + Woo scripts + Font Awesome remoto.
   - **Estado:** implementada.
   - **Observaciones:** no existe pipeline build (`package.json` ausente); módulos parecen código fuente sin bundling.

22. **Soporte de imagen de perfil de usuario con Media Library**
   - **Objetivo:** avatar personalizado para usuarios gestionados en plugin.
   - **Ámbito:** backend.
   - **Flujo:** campo hidden `img_profile` + botones upload/remove -> JS con `wp.media`.
   - **Archivos:** `includes/admin/class-vcpl-admin-profile.php`, `assets/js/modules/vcpl-media-profile.js`, `helper/vcpl-functions.php`.
   - **Dependencia:** WordPress media API.
   - **Estado:** implementada.

23. **Integración tentativa con PDF de pedido por shortcode**
   - **Objetivo:** exponer descarga PDF por orden en tabla `orders_vendor`.
   - **Ámbito:** frontend.
   - **Flujo:** celda `pdf` construye enlace con shortcode `[wcpdf_document_link order_id="..."]`.
   - **Archivos:** `includes/class-vcpl-session-handler.php`.
   - **Dependencia:** plugin externo de PDF (hipótesis fuerte).
   - **Estado:** parcial/no confirmada.
   - **Observaciones:** sin plugin proveedor, link puede romperse o devolver vacío.

24. **Histórico de comisión/salario por fecha y cálculo de profit por orden**
   - **Objetivo:** calcular profit histórico aunque comisión cambie en el tiempo.
   - **Ámbito:** backend + frontend analíticas.
   - **Flujo:** `update_user_meta` guarda arreglos `[{value,date}]`; helpers calculan comisión más cercana por fecha de orden y monto de comisión.
   - **Archivos:** `includes/class-vcpl-form-handler.php`, `helper/vcpl-functions.php`.
   - **Dependencia:** usermeta + Woo orders.
   - **Estado:** implementada con huecos.
   - **Observaciones:** selección “fecha más cercana” puede ser semánticamente incorrecta (debería ser comisión vigente <= fecha).

25. **Notice management por sesión PHP en admin**
   - **Objetivo:** persistir mensajes tras redirect en formularios admin.
   - **Ámbito:** backend.
   - **Flujo:** `notice_form()` inicia sesión y guarda HTML en `$_SESSION['notice']`; vista users lo imprime.
   - **Archivos:** `includes/class-vcpl-form-handler.php`, `includes/admin/views/html-admin-page-users.php`.
   - **Dependencia:** PHP session.
   - **Estado:** implementada con riesgo.
   - **Observaciones:** uso de sesiones en WP admin no recomendado por compatibilidad cache/proxies.

---

## Detección específica solicitada

- **Custom Post Types:** no detectados en el repositorio.
- **Taxonomías custom:** no detectadas.
- **Shortcodes custom:** no se registran shortcodes propios; sí uso de shortcode externo `[wcpdf_document_link]`.
- **Widgets:** no detectados.
- **Bloques Gutenberg custom:** no detectados.
- **Páginas admin custom:** sí (`vendor`, `customer`, `shop_manager`, `analytics`).
- **Settings pages:** no hay settings API clásica; la gestión se hace vía páginas custom y formularios de usuario.
- **Metaboxes:** no metaboxes WP clásicos; sí campos en producto vía hooks WooCommerce admin product data.
- **AJAX actions:** `wp_ajax_actions_before_delete_user`.
- **Endpoints REST:** no detectados.
- **Webhooks:** no detectados.
- **Cron jobs / scheduled actions:** no detectados.
- **Tablas custom:** no detectadas.
- **Options/transients/meta:** uso intensivo de `user_meta` y `post_meta`; options/transients no relevantes.
- **Personalizaciones de producto:** campos `_product_location`, `_items_per_products`; export/import CSV.
- **Personalizaciones de carrito:** indirectas por cantidad mínima/múltiplos.
- **Personalizaciones de checkout:** selección cliente para vendor + reasignación `customer_id` + auto-fill campos.
- **My account:** endpoints, menú y templates custom.
- **Lógica cupones/precios/fees/impuestos:** solo precios visibles y purchasable para no logueados; no fees/impuestos/cupones custom.
- **Métodos de pago/shipping custom:** no detectados.
- **Emails custom:** no detectados.
- **Importadores/exportadores:** sí, extensión de import/export productos Woo.
- **ERP/CRM/logística/pagos/facturación/analytics/marketing:** no integraciones explícitas salvo posible PDF invoices.
- **SEO:** no detectado.
- **Cache/performance:** no implementación explícita; hay patrones costosos.
- **Seguridad:** hay nonces en varios flujos, pero checks de capacidad y escaping son inconsistentes.
- **Multiidioma/multimoneda:** no implementación explícita.
- **Integraciones API externas:** Font Awesome Kit; posible shortcode plugin externo.
- **Scripts JS críticos y build:** JS modular ESM sin pipeline formal detectado.
- **TODO/FIXME visibles:** no TODOs clásicos; sí comments `FIX` y debug logs en analíticas admin, `console.log` en checkout module.

---

## Funcionalidades probablemente faltantes o incompletas

1. **Compatibilidad Checkout Blocks / Store API:** no hay extensión blocks (`IntegrationInterface`, `ExtendSchema`, etc.).
2. **Compatibilidad HPOS real para analíticas admin:** SQL legacy a tablas posts/postmeta.
3. **Hook faltante `change_login_required_message`:** registrado pero método no existe.
4. **Activación/desactivación robusta:** hooks están vacíos/comentados, no migraciones ni flush controlado.
5. **Control de capacidades granular por operación (CRUD/AJAX):** parcial.
6. **Manejo de errores y observabilidad estructurada:** limitado, sin logger central.
7. **Test suite:** ausente.

