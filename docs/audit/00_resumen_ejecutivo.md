# 00 - Resumen ejecutivo

## Alcance auditado
Se auditó el repositorio completo disponible. El alcance real **no es un eCommerce completo de WordPress**, sino un **plugin custom** (`Vc Profit Lost`) que extiende WooCommerce para operar un modelo de vendedores y clientes asignados por vendedor.

## Qué hace hoy el proyecto (en alto nivel)
- Crea y fuerza un esquema de roles custom (`vendor`, `cbv`, `cbs`, `cw`) y oculta estos roles de la UI nativa de usuarios de WordPress.
- Implementa un backoffice propio en admin (`Vendors`, `Customers`, `Shop Managers`, `Analytics`) con `WP_List_Table` custom, formularios CRUD de usuarios y lógica de reasignación/borrado de clientes.
- Añade endpoints custom en `My Account` de WooCommerce para vendedores: `customers`, `orders_vendor`, `analytics` y dashboard custom.
- Implementa una capa de reporting (ventas, top clientes, comisión/profit), tanto en front (vendor) como admin global.
- Modifica producto y catálogo (oculta precios y bloquea compra para no logueados; configura cantidades mínimas por producto).
- Extiende import/export de productos para metadatos `_product_location` y `_items_per_products`.
- Extiende checkout para que un vendor pueda crear pedidos a nombre de un cliente asignado.

## Estado general
**Funciona como MVP legacy y altamente acoplado, con riesgo operativo alto** por:
- incompatibilidades probables con HPOS (consulta SQL directa a `posts/postmeta` para pedidos),
- callback inexistente registrado en hooks (`change_login_required_message`),
- `flush_rewrite_rules()` en cada `init`,
- uso de `session_start()` manual en admin,
- sanitización/escapado inconsistente y acceso directo a superglobales en múltiples puntos.

## Riesgos críticos inmediatos
1. **Compatibilidad WooCommerce/HPOS comprometida** en analíticas de admin por SQL directo legacy de órdenes.
2. **Posible fatal error** al ejecutar filtro `woocommerce_login_required_message` por método no implementado.
3. **Impacto de rendimiento severo** por flush de rewrite rules en cada request.
4. **DX/maintainability baja**: singleton mal implementado (`instance()` devuelve `new self()`), side-effects y arquitectura híbrida procedural/OOP.

## Dependencias externas detectadas
- WooCommerce (hard dependency, plugin no arranca sin él).
- WordPress core APIs.
- SelectWoo/select2 (dependencia transitiva desde WooCommerce y uso directo JS).
- Font Awesome Kit remoto (`https://kit.fontawesome.com/...`).
- Posible dependencia funcional de **WooCommerce PDF Invoices & Packing Slips** por shortcode `[wcpdf_document_link]` en listado de órdenes vendor.
- Acople indirecto a Astra Addon vía filtro de `gettext` apuntando al dominio `astra-addon`.

## Estado de compatibilidad con versiones actuales
No fue posible consultar endpoints oficiales online desde este entorno (restricción de red: `403 Forbidden`).

Por tanto:
- la comparación con “última estable vigente” queda marcada como **hipótesis informada**,
- pero sí se identificaron patrones objetivamente frágiles/deprecables frente a WooCommerce moderno (HPOS, Blocks, Store API-first).

## Recomendación inmediata
Antes de refactor mayor:
1. Fase de hardening de estabilidad (callbacks rotos, rewrite flush, validaciones y nonces/caps).
2. Fase de compatibilidad WooCommerce moderno (HPOS/Blocks/Store API).
3. Fase de modernización arquitectónica (dominios, servicios, repositorios, tests).
