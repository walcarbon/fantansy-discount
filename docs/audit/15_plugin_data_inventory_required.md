# 15 - Plugin data inventory required (final checklist)

## Plantilla obligatoria por plugin
- Plugin/version
- Tablas custom
- Meta keys (post/user/order/term)
- Options/transients
- Cron/Action Scheduler jobs
- Hooks críticos y side-effects
- Endpoints externos/webhooks
- Artefactos (templates/pdf/layouts)
- Dependencia de licencia
- Estrategia migración (map/transform/backfill)
- Plan rollback

## Orden de inventario (cerrado)
1. Alpha Insights Pro
2. Dynamic Pricing + Customer Specific Pricing
3. WooCommerce Role Based Methods
4. Custom Order Status Manager
5. PDF Invoices suite
6. YITH Frontend Manager
7. AutomateWoo
8. ACF Pro (+Free si hay uso)
9. Advanced Woo Search
10. Plugins de bajo riesgo (wishlist/quoteup/clear-cart/sort display)

## Inventario ACF obligatorio antes de salida
- Field groups
- Options pages
- Repeater/Flexible content
- ACF blocks
- JSON/PHP exports
- llamadas: `get_field`, `the_field`, `have_rows`, `get_sub_field`
- hooks `acf/*`

## Gate de completitud
- 100% plugins objetivo inventariados.
- 100% mapeos de datos aprobados.
- Backfill ensayado en staging.
- Rollback ensayado.

