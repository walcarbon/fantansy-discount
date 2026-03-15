# 03 - Auditoría de compatibilidad WordPress/WooCommerce

## Limitación de verificación externa
Intenté consultar endpoints oficiales para versiones/changelogs en tiempo real y el entorno devolvió `403 Forbidden`. Por ello, la parte de “última estable vigente hoy” se reporta como **hipótesis informada**.

Aun con esa limitación, sí se auditaron patrones de compatibilidad técnica contra prácticas modernas de WooCommerce/WordPress.

---

## Hallazgos (estrictos)

### 1) SQL directo a `posts/postmeta` para pedidos (riesgo HPOS)
- **Severidad:** crítica.
- **Evidencia:** `VCPL_Admin_List_Table_Analytics_Vendor` consulta `{$wpdb->posts}` + `_customer_user` + `_order_total` en SQL manual.
- **Archivos:** `includes/admin/list-tables/class-vcpl-admin-list-table-analytics-vendor.php`.
- **Por qué está desactualizado:** HPOS desacopla pedido de `wp_posts/wp_postmeta`; SQL legacy rompe compatibilidad funcional y de rendimiento.
- **Práctica moderna recomendada:** `wc_get_orders`, `WC_Order_Query`, data stores y tablas HPOS vía CRUD.
- **Complejidad corrección:** alta.
- **Riesgo regresión:** alto (reporting, filtros, paginado).

### 2) Método hookeado inexistente (`change_login_required_message`)
- **Severidad:** crítica.
- **Evidencia:** hook registrado en `class-vc-profit-lost.php` pero no existe método en `VCPL_Session_Handler`.
- **Archivos:** `includes/class-vc-profit-lost.php`, `includes/class-vcpl-session-handler.php`.
- **Por qué está desactualizado/frágil:** puede provocar error fatal cuando Woo aplique el filtro.
- **Recomendación:** implementar método o remover hook.
- **Complejidad:** baja.
- **Riesgo regresión:** bajo.

### 3) `flush_rewrite_rules()` en cada `init`
- **Severidad:** alta.
- **Evidencia:** llamado dentro de `add_endpoints()` ejecutado por hook `init`.
- **Archivo:** `includes/class-vcpl-session-handler.php`.
- **Por qué es incorrecto:** flush por request degrada severamente rendimiento y puede impactar concurrencia.
- **Recomendación moderna:** flush solo en activación/desactivación/version bump.
- **Complejidad:** baja.
- **Riesgo regresión:** bajo.

### 4) Checkout solo clásico; no extensión para Cart/Checkout Blocks
- **Severidad:** alta.
- **Evidencia:** customización basada en hooks de checkout clásico + JS DOM (`#customer_order`) sin integración Store API/Blocks.
- **Archivos:** `includes/class-vcpl-session-handler.php`, `includes/public/class-vcpl-public.php`, `assets/js/modules/vcpl-customer-checkout.js`.
- **Por qué está desactualizado:** ecosistema Woo migra a Blocks; funcionalidades pueden no aplicar en checkout block-based.
- **Recomendación:** implementar integración Blocks + validación server-side vía Store API extensibility.
- **Complejidad:** alta.
- **Riesgo regresión:** medio-alto.

### 5) Dependencia de jQuery/DOM legacy para lógicas críticas
- **Severidad:** media.
- **Evidencia:** múltiples módulos jQuery para validación/autofill/dep fields.
- **Archivos:** `assets/js/*.js`.
- **Por qué es frágil:** difícil mantener con UI modernas y bloques React de Woo.
- **Recomendación:** migrar gradualmente a scripts por contexto, sin hardcode DOM.
- **Complejidad:** media.
- **Riesgo:** medio.

### 6) Hook `woocommerce_checkout_billing` usado como filtro pero imprime HTML
- **Severidad:** alta.
- **Evidencia:** `customer_fields()` hace `printf` y no retorna estructura esperada de campos billing.
- **Archivo:** `includes/class-vcpl-session-handler.php`.
- **Por qué es frágil:** contrato del hook no respetado; puede romper compatibilidad con otros plugins/checkout flows.
- **Recomendación:** usar hooks de render correctos (`woocommerce_after_checkout_billing_form` etc.) o retorno de campos si aplica.
- **Complejidad:** media.
- **Riesgo:** medio.

### 7) CRUD WooCommerce correcto en partes, inconsistente en otras
- **Severidad:** media.
- **Evidencia:** sí usa `wc_get_orders`/`WC_Order`; pero mezcla con SQL directo y shortcode para PDF.
- **Archivos:** `includes/class-vcpl-session-handler.php`, `includes/admin/list-tables/class-vcpl-admin-list-table-analytics-vendor.php`.
- **Por qué está desactualizado:** mezcla estrategias impide portabilidad HPOS.
- **Recomendación:** unificar capa de acceso a pedidos en servicios basados en CRUD.
- **Complejidad:** alta.
- **Riesgo:** medio-alto.

### 8) Sesiones PHP manuales en wp-admin
- **Severidad:** media.
- **Evidencia:** `session_start()` en vistas y form handler.
- **Archivos:** `includes/admin/views/html-admin-page-users.php`, `includes/class-vcpl-form-handler.php`.
- **Por qué es frágil:** no estándar WP; conflictos con cache, headers y escala.
- **Recomendación:** usar `add_settings_error`, transients/flash patterns WP.
- **Complejidad:** media.
- **Riesgo:** bajo-medio.

### 9) Compatibilidad PHP moderno: tipado mixto y coerciones riesgosas
- **Severidad:** media.
- **Evidencia:** firmas tipadas que retornan tipos inconsistentes (`float` devolviendo string format), uso extensivo de `mixed`, `extract`, `@` suppress.
- **Archivos:** `helper/vcpl-functions.php`, `includes/class-vcpl-form-handler.php`, `includes/admin/views/html-delete-user.php`.
- **Por qué está desactualizado:** con strictness alto puede generar warnings/errores.
- **Recomendación:** tipado consistente, eliminar supresores y coerciones implícitas.
- **Complejidad:** media.
- **Riesgo:** medio.

### 10) Dependencias indirectas no declaradas (Astra addon / WCPDF)
- **Severidad:** media.
- **Evidencia:** filtro de `gettext` para dominio `astra-addon`; shortcode `[wcpdf_document_link]`.
- **Archivos:** `includes/class-vcpl-session-handler.php`.
- **Por qué es frágil:** sin plugin/tema esperado, funcionalidad parcial o rota.
- **Recomendación:** detección explícita de dependencia + fallback.
- **Complejidad:** baja.
- **Riesgo:** medio.

---

## Compatibilidad con WordPress estable actual (hipótesis)
- Probablemente ejecuta en versiones modernas, pero con deuda técnica importante en:
  - manejo de sesiones,
  - nonces/caps inconsistentes,
  - rendering/escaping legacy,
  - arquitectura acoplada.

## Compatibilidad con WooCommerce estable actual (hipótesis)
- Riesgo mayor en:
  - HPOS (alta probabilidad de incompatibilidad en analytics admin),
  - Cart/Checkout Blocks (funcionalidad de checkout vendor no portable),
  - performance por consultas intensivas `limit=-1`.

## Plantillas override desactualizadas
- No se detecta carpeta de override de templates core de Woo en versión-tag típica; sí templates custom de endpoints. Riesgo medio por dependencia de estructura My Account y filtros.

