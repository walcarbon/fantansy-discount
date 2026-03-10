# 20 - Automation parity matrix (minimum survivability)

## Automatizaciones mínimas obligatorias en plugin único

| ID | Automatización | Trigger | Destinatarios | Estado |
|---|---|---|---|---|
| A-01 | Aviso a bodeguero por orden nueva | order_created | Bodeguero | Obligatoria |
| A-02 | Aviso a vendedor y admin al pasar a `ready-to-go` | order_status=ready-to-go | Vendedor, Admin | Obligatoria |
| A-03 | Aviso devolución / saldo a favor | refund_final + credit_created | Cliente, Admin, Vendedor | Obligatoria |
| A-04 | Aviso de bajo stock | stock <= threshold | Admin, Bodeguero | Obligatoria |
| A-05 | Aviso pedido entregado | order_status=delivered | Cliente, Admin, Vendedor | Obligatoria |

## Regla de coexistencia
- AutomateWoo queda **HYBRID temporal** hasta mapping trigger-by-trigger y paridad verificada.

## Campos mínimos de auditoría
- automation_id, trigger_event, order_id/customer_id, recipients, send_status, attempts, sent_at.

