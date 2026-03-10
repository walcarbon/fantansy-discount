# 00A - Plugin landscape real (2 capas de verdad)

## Capa 1 vs Capa 2
- **Capa 1 (repo):** plugin custom `VC Profit Loss` (código disponible y auditado).
- **Capa 2 (producción):** stack real de plugins activos y procesos manuales fuera del repo.

## Estado de evidencia
- **Confirmado por código:** flujos vendor/customer/checkout/dashboard del plugin custom.
- **Confirmado por contexto/respuestas:** uso real de plugins y procesos operativos.
- **Inferencia probable:** acoples secundarios no visibles en este repo.
- **Incertidumbre abierta:** detalles internos de configuración de plugins externos.

---

## Matriz consolidada (con tus respuestas)

| Plugin | Estado de uso real | Valor negocio real hoy | Decisión preliminar | Evidencia |
|---|---|---|---|---|
| WooCommerce | Crítico | Núcleo transaccional | KEEP | Confirmado por contexto |
| VC Profit Loss (custom) | Crítico diario | Core operativo vendedores/clientes/checkout/dashboard | KEEP + REFACTOR profundo | Confirmado por código + contexto |
| Alpha Insights Pro | Uso mensual por dueño | Core analítico/P&L con alcance amplio | REPLACE WITH CUSTOM (objetivo explícito) | Confirmado por contexto |
| YITH Frontend Manager Premium | Uso acotado (bodeguero) | Operación inventario/devoluciones frontend | REPLACE WITH CUSTOM (gradual) | Confirmado por contexto |
| Woo Dynamic Pricing & Discounts | Importante | Pricing principal | CONSOLIDAR/REPLACE gradual | Confirmado por contexto |
| Customer Specific Pricing | Uso específico | Descuentos especiales por cliente | CONSOLIDAR en motor propio | Confirmado por contexto |
| Woo Role Based Methods | Uso actual | Métodos por rol (offline flows) | REPLACE WITH CUSTOM backend | Confirmado por contexto |
| PDF Invoices core + addons | Necesario | Facturas/packing slips/notas de crédito/numeración | KEEP temporal, luego evaluar reemplazo | Confirmado por contexto |
| AutomateWoo | Uso activo (pocos workflows críticos) | Notificaciones operativas y reactivación | KEEP temporal / posible absorción parcial | Confirmado por contexto |
| Custom Order Status Manager | Uso real (Ready-to-go, Delivered) | Logística/reportes | KEEP temporal / absorber en custom luego | Confirmado por contexto |
| Advanced Woo Search | Uso real UX | Búsqueda AJAX + SKU + typo tolerance | KEEP temporal o custom search posterior | Confirmado por contexto |
| YITH Wishlist | Bajo valor actual | No aporta conversión hoy | REMOVE candidato | Confirmado por contexto |
| Product Enquiry/QuoteUp | Uso actual nulo | Futuro potencial, no crítico hoy | REMOVE candidato (o desactivar) | Confirmado por contexto |
| Min/Max Quantity plugin | No definido en detalle | Potencial superposición | UNKNOWN -> posible REMOVE tras consolidar pricing/qty | Inferencia probable |
| Minimum Purchase Amount | No reglas activas hoy | Futuro potencial | UNKNOWN -> posible REMOVE | Confirmado por contexto |
| ACF + ACF Pro | ACF Pro con datos negocio | Soporte técnico de metadatos | KEEP PRO temporal / evaluar free | Confirmado por contexto |
| Elementor + Pro + Astra Pro | Core visual | Frontend completo visual | KEEP | Confirmado por contexto |
| Essential Addons Elementor | Uso puntual (Advanced tabs) | Soporte visual menor | KEEP temporal / posible REMOVE | Confirmado por contexto |
| WP Rocket | Base performance | Aceleración general | KEEP | Confirmado por contexto |
| Wordfence | Uso básico | Seguridad base | KEEP | Confirmado por contexto |
| Product Sort and Display | No confirmado uso crítico | Merchandising | REMOVE candidato | Inferencia probable |
| Clear Cart | Sospecha uso accidental usuario | No core | REMOVE candidato o controlar UX | Confirmado por contexto |
| Show User Metadata | Soporte técnico | No core negocio | REMOVE candidato | Confirmado por contexto |
| Customer Addresses (export) | Uso no detallado | Operativo secundario | UNKNOWN | Incertidumbre abierta |

---

## Hallazgos funcionales críticos confirmados
1. Vendedores crean clientes en frontend y no deben ver clientes ajenos.
2. Flujo de orden: tienda normal -> checkout -> dropdown `Customer assign order`.
3. Estados clave custom: `Ready-to-go`, `Delivered`.
4. Inventario actual se rompe por reimportación masiva de hoja (sobrescribe todo).
5. Deseo estratégico: Woo como fuente de verdad y Sheet como espejo, con SLA <= 5 min.

