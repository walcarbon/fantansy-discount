# 16 - Blockers before implementation (reduced, post-technical-standards)

## Veredicto de entrada a Ola 0
**LISTO PARA IMPLEMENTACIÓN CONTROLADA DE OLA 0** (iniciar Gate 0 de ejecución).

## Bloqueantes que quedaron cerrados por estándar técnico
1. HPOS-safe obligatorio para órdenes/refunds/estados (sin SQL legacy directo).
2. Credit ledger en tabla custom, movimientos inmutables y tipos mínimos definidos.
3. Dataset canónico Alpha (3 meses) + owner de validación (David) + KPIs MVP definidos.
4. ACL base por rol/capacidad definida.
5. Sync Woo-first con SKU, deduplicación, retries (>=3), DLQ y modo puente auditado.
6. Plantilla estándar de rollback obligatoria.

## Bloqueantes reales que siguen abiertos (implementación/evidencia)
1. **Inventario técnico por plugin completado con evidencia** (options/meta/tables/cron/emails/templates/webhooks/side-effects).
2. **Mapeo endpoint-level final de ACL contra endpoints reales del código actual** (matriz base ya definida).
3. **Especificación de columnas reales del dataset Alpha con expected outputs numéricos concretos** (estructura definida, faltan valores de validación).
4. **Inventario/mapeo trigger-by-trigger actual de AutomateWoo desde entorno real** (matriz base ya definida).
5. **Matriz de templates/overrides/emails PDF realmente usados hoy** (estructura MVP definida).

## Qué NO bloquea iniciar Ola 0
- La construcción de módulos nuevos (pricing/commission/state/sync/ledger) **sí puede arrancar** en staging bajo feature flags.

