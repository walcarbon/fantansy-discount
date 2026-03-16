# 07 - Plan maestro (definitivo, business-locked)

## Gate 0 (pre-implementación obligatoria)
1. Pricing rulebook final.
2. Commission rulebook final (lunes-domingo, vigencia, brackets).
3. State machine técnica completa con side-effects idempotentes.
4. Dataset canónico Alpha (3 meses) + expected outputs.
5. Authorization matrix final por rol/capacidad/endpoint.
6. Inventario técnico por plugin.
7. Runbooks rollback por plugin/capacidad.
8. Contrato técnico sync Woo-first (schema + ownership + idempotencia).

## Ola 1 — Núcleo transaccional de operación
- Implementar Pricing Engine único.
- Implementar Commission Engine semanal.
- Implementar state machine custom (`ready-to-go`, `delivered`).
- Integrar side-effects de stock/comisión/reportes.

## Ola 2 — Inventario, devoluciones y crédito
- Warehouse module + inventory ledger.
- Returns flow (sin parciales) desde pending/processing/ready-to-go.
- Credit ledger por cliente + nota de crédito.

## Ola 3 — Sync Woo-first con Sheet
- Push Woo->Sheet por evento.
- Push Sheet->Woo controlado por ownership.
- Crear simple products desde Sheet.
- Bloqueos: SKU/backorders/variaciones no editables desde Sheet.

## Ola 4 — Analytics/P&L y documentos
- Alpha parity MVP (5 reportes críticos) -> Fase 2 -> Fase 3.
- Document engine MVP (factura/packing slip/nota entrega/nota crédito).
- Numeración secuencial y matriz de emails obligatoria.

## Ola 5 — Salida progresiva de plugins REPLACE
- Dynamic Pricing + Customer Specific Pricing.
- Role Based Methods.
- Custom Order Status Manager.
- Product Sort and Display.
- Min/Max y Minimum Purchase (absorbidos).
- PDF suite (tras paridad legal/técnica).
- Alpha (tras paridad aprobada David).

## Ola 6 — Salida de HYBRID + deuda de plataforma
- AutomateWoo (tras mapping trigger-by-trigger).
- YITH Frontend Manager (tras paridad bodega).
- Evaluar salida ACF Pro solo con inventario/cobertura total.

## Reglas de secuencia (hard constraints)
- No se apaga Alpha sin paridad certificada.
- No se apaga PDF suite sin paridad documental + numeración.
- No se apaga YITH FM sin paridad funcional bodeguero.
- No se apaga AutomateWoo sin paridad de triggers mínimos.

