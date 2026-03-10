# 18 - Order state machine and side effects (technical-locked)

## Flujo objetivo
`pending -> processing -> ready-to-go -> delivered -> completed`

## Estados custom
- `ready-to-go`
- `delivered`

## Transiciones y efectos

| From | To | Permitida | Side-effects obligatorios |
|---|---|---|---|
| pending | processing | Sí | Marca orden como `sold` para comisión semanal |
| processing | ready-to-go | Sí | Descuenta stock; evento logístico; notificaciones (vendedor/admin) |
| ready-to-go | delivered | Sí | Marca entrega operativa; notificación pedido entregado |
| delivered | completed | Sí | Cierre administrativo |
| pending/processing/ready-to-go | refunded (final) | Sí | Reposición stock inmediata + nota crédito + ledger crédito |

## Reglas cerradas
1. Comisión nace en `processing`.
2. `ready-to-go` y `delivered` son operativos/logísticos.
3. `delivered` no reemplaza `completed`.
4. No devoluciones parciales.

## Impacto por dominio
- **Stock:** baja en `ready-to-go`, sube en `refunded` final.
- **Comisión:** base en `processing`; ajustes por devolución semana siguiente.
- **Dashboards/reportes:** deben distinguir venta (`processing`) de operación logística (`ready-to-go`, `delivered`).

## Requisito técnico
- Implementación idempotente de side-effects por transición (evitar doble descuento de stock o doble comisión).

