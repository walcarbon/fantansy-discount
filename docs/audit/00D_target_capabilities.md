# 00D - Target capabilities (final alignment)

| Capacidad | Regla final | Módulo destino en VC Profit Loss final |
|---|---|---|
| Comisiones | Semanal (lunes-domingo), base en `processing`, % por vendedor y vigencia, brackets opcionales por vendedor, devolución descuenta semana siguiente | Commission Engine |
| Pricing | Precedencia: específico cliente > rol (CBV 5%, CBS 3%, CW 10%) > base; mínimos/múltiplos globales | Pricing Engine |
| Checkout offline | Flujo actual offline; arquitectura final offline-first | Checkout Offline Core |
| Estados orden | `pending -> processing -> ready-to-go -> delivered -> completed`; custom: ready-to-go, delivered | Order State Machine |
| Stock | Descuento en `ready-to-go`; reposición inmediata en devolución | Inventory Core + State Machine |
| Devoluciones | Permitidas desde pending/processing/ready-to-go; no parciales | Returns Module |
| Crédito cliente | Ledger interno por cliente + nota de crédito, parcial, sin vencimiento, auto/manual admin | Credit Ledger Module |
| Vendor ops | Alta clientes frontend + aislamiento vendedor-cliente + órdenes para clientes | Vendor Operations |
| Bodega | Entradas/salidas/devoluciones/ajustes/ubicaciones/faltantes; bodeguero opera en Woo | Warehouse Module |
| Woo↔Sheet | Woo-first; Sheet espejo backup; conflicto gana Woo; validación/log SKU; edición secundaria controlada | Sync Module |
| Sheet new product | Puede crear solo producto simple | Sync Module |
| Sheet restrictions | No SKU/backorders/variaciones | Sync Module guardrails |
| Analytics/P&L | Reemplazo Alpha por fases MVP/F2/F3 | Analytics & P&L |
| PDFs | MVP: factura, packing slip, nota entrega, nota crédito + secuencial + matriz emails | Document Engine |
| Automatizaciones | Deben sobrevivir 5 mínimas definidas; AutomateWoo híbrido hasta paridad | Automation Engine |
| Roles | admin, vendor, bodeguero, customer by vendor/shop/wholesaler (sin shop manager) | Identity & Authorization |

## Abiertos técnicos que persisten
- Authorization matrix endpoint-capability.
- Runbooks rollback por plugin/capacidad.
- Inventario técnico por plugin (meta/options/tablas/cron/webhooks).
- Journeys UX detallados bodeguero.
- Dataset canónico Alpha con expected outputs.
- Schema/idempotencia técnica final del sync Woo-first.
- Implementación técnica final del credit ledger.

