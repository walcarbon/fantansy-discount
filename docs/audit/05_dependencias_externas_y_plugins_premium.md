# 05 - Dependencias externas y plugins premium/comerciales

## Limitación
No fue posible validar estado de mantenimiento online en endpoints oficiales desde este entorno (restricción de red). Se reporta estado como inferencia basada en evidencia de código.

## Matriz de dependencias

## 1) WooCommerce (core)
- **Nombre:** WooCommerce.
- **Evidencia:** bloqueo de carga si no está activo; uso intensivo de APIs/classes/hooks Woo.
- **Archivos:** `vc-profit-lost.php`, `includes/*`, `templates/myaccount/*`.
- **Estado:** activo/mandatorio.
- **Lock-in:** alto (plugin depende totalmente del ecosistema Woo).
- **Licencia/endpoint externo:** no de licencia comercial, sí actualizaciones WP.org.
- **Compatibilidad probable actual:** parcial (riesgos HPOS/Blocks).
- **Recomendación:** priorizar hardening de compatibilidad Woo moderno.

## 2) WordPress core
- **Nombre:** WordPress.
- **Evidencia:** APIs globales WP en todo el plugin.
- **Estado:** activo/mandatorio.
- **Lock-in:** total por naturaleza.
- **Recomendación:** alinear coding standards y patrones actuales.

## 3) SelectWoo / select2
- **Nombre:** selectWoo (bundled por WooCommerce) y uso de `select2()`.
- **Evidencia:** `wp_enqueue_script('selectWoo')`, `$('.js-select2-multiple').select2()`.
- **Archivos:** `includes/admin/class-vcpl-admin.php`, `includes/public/class-vcpl-public.php`, `assets/js/vcpl-admin.js`.
- **Estado:** activo.
- **Lock-in:** medio.
- **Desactualización probable:** media (API jQuery legacy).
- **Recomendación:** encapsular usos y prever reemplazo gradual.

## 4) Font Awesome Kit remoto
- **Nombre:** Font Awesome Kit (`kit.fontawesome.com`).
- **Evidencia:** enqueue script remoto con token hardcodeado.
- **Archivo:** `includes/public/class-vcpl-public.php`.
- **Estado:** activo.
- **Lock-in:** medio (depende de endpoint/cuenta externa).
- **Dependencia licencia/endpoint:** sí (servicio externo).
- **Desactualización/riesgo:** medio (privacidad/CSP/performance/disponibilidad).
- **Recomendación:** reemplazar por assets locales versionados o iconos nativos.

## 5) WooCommerce PDF Invoices & Packing Slips (hipótesis fuerte)
- **Nombre:** probable plugin “WooCommerce PDF Invoices & Packing Slips”.
- **Evidencia:** shortcode `[wcpdf_document_link order_id="..."]`.
- **Archivo:** `includes/class-vcpl-session-handler.php`.
- **Estado:** parcialmente implementado/no confirmado.
- **Lock-in:** medio-alto para feature de descarga PDF.
- **Licencia/endpoint:** depende del plugin (puede tener extensiones premium).
- **Compatibilidad probable:** incierta.
- **Recomendación:** declarar dependencia explícita y fallback cuando no exista shortcode.

## 6) Astra Addon (acople indirecto)
- **Nombre:** Astra Addon (hipótesis por text domain).
- **Evidencia:** filtro `gettext` condiciona por dominio `astra-addon`.
- **Archivo:** `includes/class-vcpl-session-handler.php`.
- **Estado:** residual/condicional.
- **Lock-in:** medio (UX textual depende de tercero).
- **Desactualización probable:** media.
- **Recomendación:** evitar manipulación por `gettext` + usar hooks de checkout más robustos.

---

## Integraciones SaaS / pagos / logística / facturación / marketing

- **Pasarelas de pago custom:** no detectadas.
- **Shipping custom/logística:** no detectadas.
- **ERP/CRM/facturación externa:** no detectadas explícitamente.
- **Analytics SaaS:** no detectado.
- **Antifraude/CDN/licensing managers:** no detectado en código.

**Nota:** ausencia en repositorio no descarta dependencias en entorno productivo (plugins externos instalados fuera de este código).

