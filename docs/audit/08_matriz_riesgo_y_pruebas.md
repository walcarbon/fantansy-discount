# 08 - Matriz de riesgo y pruebas (definitiva)

| Riesgo | Severidad | Mitigación | Gate |
|---|---|---|---|
| Paridad Alpha incompleta | Crítica | Dataset canónico 3 meses + expected outputs | Aprobación David |
| Pricing precedence rota | Crítica | Rulebook + test matrix e2e | 100% casos aprobados |
| Comisión semanal incorrecta | Crítica | Tests por semana/vigencia/brackets/devoluciones | Cierre semanal sin diferencias |
| Side-effects de estados rotos | Crítica | State machine idempotente | Stock y comisión consistentes |
| Drift Woo↔Sheet | Alta | Woo-first + ownership + logging SKU | 0 conflictos sin resolver |
| Crédito ledger inconsistente | Alta | Modelo ledger + pruebas contables | Saldos cliente conciliados |
| Ruptura checkout offline | Alta | Contrato offline + pruebas vendedor | Flujo estable en producción |
| Pérdida de aislamiento vendedor | Alta | Authorization matrix + pruebas ACL | 0 accesos cruzados |
| Fallo documental PDF | Alta | Paridad campos + secuencial + email matrix | Validación legal y operativa |
| Fallas al retirar híbridos | Media/Alta | Mapping trigger-by-trigger + paridad bodega | Cutover sin incidentes críticos |

## Pruebas mínimas por dominio
- Pricing: precedencia + mínimos/múltiplos en catálogo/cart/checkout/order.
- Comisión: lunes-domingo, cambio vigencia inmediato, bracket reemplaza base, devolución semana siguiente.
- Estados: pending->processing->ready-to-go->delivered->completed + side-effects.
- Inventario/returns: no parciales; reposición inmediata en devolución.
- Sync: creación simple product desde sheet + bloqueos SKU/backorders/variaciones.
- PDF: 4 documentos MVP + campos obligatorios + distribución de emails.
- Automatizaciones mínimas (5) con trazabilidad.

## Bloqueantes de QA
1. Dataset canónico + expected outputs.
2. Authorization matrix cerrada.
3. Runbooks rollback ensayados.
4. Inventario técnico por plugin completado.

