# 00C - Plugin replacement matrix (final business-locked)

## Estados
- **REPLACE**: absorbido por `VC Profit Loss` final (plugin único).
- **KEEP**: permanece en stack objetivo/mediano plazo.
- **HYBRID**: convivencia temporal con salida condicionada.
- **REMOVE**: retiro simple o reemplazo mínimo no estratégico.

## Matriz final

| Plugin | Estado | Regla cerrada | Condición de salida |
|---|---|---|---|
| WooCommerce | KEEP | Core ecommerce/orders | N/A |
| VC Profit Loss (actual) | REPLACE | Refactor modular dentro del plugin único final | Paridad por módulos |
| Alpha Insights Pro | REPLACE | Reemplazo planificado con paridad MVP/F2/F3 | Aprobación David + dataset canónico |
| WooCommerce Dynamic Pricing & Discounts | REPLACE | Consolidar en pricing engine único | Pricing rulebook validado |
| Customer Specific Pricing | REPLACE | Consolidar en pricing engine único | Pricing rulebook validado |
| WooCommerce Role Based Methods | REPLACE | Arquitectura offline-first propia | Paridad checkout/métodos por rol |
| Custom Order Status Manager | REPLACE | Solo `ready-to-go` y `delivered` custom | State machine custom en producción |
| PDF Invoices + addons | REPLACE | Reemplazo planificado | Paridad documental + numeración secuencial |
| Product Sort and Display | REPLACE | Absorber en lógica propia/core | Validación merchandising |
| Min/Max Quantity plugin | REPLACE | Absorber mínimos/múltiplos en pricing engine | Validación catálogo/cart/checkout |
| Minimum Purchase Amount | REPLACE/ABSORB | Absorbido por rulebook final | Rulebook final aprobado |
| AutomateWoo | HYBRID | Mantener temporal | Mapping trigger-by-trigger completo |
| YITH Frontend Manager Premium | HYBRID | Mantener temporal para bodeguero | Paridad funcional bodega |
| Advanced Woo Search | KEEP | Valor UX actual confirmado | Re-evaluación tardía |
| Elementor | KEEP | Capa visual | N/A |
| Elementor Pro | KEEP | Capa visual | N/A |
| Astra Pro | KEEP | Theme/UX | N/A |
| WP Rocket | KEEP | Performance base | N/A |
| Wordfence | KEEP | Seguridad base | N/A |
| Essential Addons for Elementor | KEEP (temporal) | Uso puntual visual | Revisión tras estabilizar front |
| ACF Pro | KEEP (temporal) | Salida solo tras inventario ACF completo | Inventario + reemplazo llamadas |
| ACF Free | REMOVE candidato | No requerido por ACF Pro | Confirmar no uso residual |
| Clear Cart | REMOVE o REPLACE mínimo | No estratégico | Confirmar UX mínima |
| QuoteUp | REMOVE o REPLACE mínimo | Uso actual 0 | Confirmar retiro |
| Wishlist (YITH) | REMOVE o REPLACE mínimo | No aporta conversión hoy | Confirmar retiro o versión mínima |
| Customer Addresses Export | KEEP temporal (unknown) | Uso no cerrado | Inventario uso real |
| Show User Metadata | REMOVE | Tooling no crítico | Retiro temprano |

