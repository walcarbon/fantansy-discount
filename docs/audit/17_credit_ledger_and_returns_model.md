# 17 - Credit ledger and returns model (technical-locked)

## Estándar obligatorio
- Implementación en **tabla custom** del plugin único.
- Ledger **inmutable** (no saldo editable directo).
- Nota de crédito = documento operativo/contable, **no** reemplaza ledger.

## Tipos mínimos de movimiento
- `credit_created`
- `credit_applied`
- `credit_adjusted`
- `credit_reversed`

## Tabla propuesta (v1)

### `wp_vcpl_credit_ledger`
| Campo | Tipo | Regla |
|---|---|---|
| id | bigint PK | autoincrement |
| customer_id | bigint | obligatorio |
| movement_type | varchar(32) | enum lógico (4 tipos mínimos) |
| amount | decimal(18,6) | positivo o negativo según tipo |
| currency | varchar(8) | obligatorio |
| order_id | bigint nullable | referencia Woo order |
| refund_id | bigint nullable | referencia refund Woo |
| credit_note_number | varchar(64) nullable | vínculo documental |
| pdf_document_id | varchar(128) nullable | referencia documento generado |
| actor_user_id | bigint nullable | responsable (admin/sistema) |
| reason_code | varchar(64) nullable | auditoría |
| reason_note | text nullable | auditoría |
| created_at_gmt | datetime | obligatorio |
| source_event_key | varchar(128) | deduplicación origen |
| is_void | tinyint(1) default 0 | reversión lógica si aplica |

## Cálculo de saldo disponible
`SUM(amount)` sobre movimientos válidos del cliente (no void), con moneda consistente.

## Reglas funcionales cerradas
1. Crédito nace solo por devolución de factura cerrada.
2. Uso parcial permitido.
3. Sin vencimiento.
4. Aplicación automática o manual solo admin.
5. Auto-aplicación sugiere monto editable.
6. Devolución incrementa inventario inmediato.

## Flujo de devolución/credito
1. Refund final (sin parciales) desde estado permitido.
2. Reingreso de stock inmediato.
3. Generar nota de crédito PDF.
4. Insert `credit_created` en ledger.
5. Al aplicar saldo en nueva compra, insertar `credit_applied`.
6. Ajustes manuales controlados: `credit_adjusted` / `credit_reversed`.

## Controles técnicos mínimos
- Idempotencia por `source_event_key`.
- Restricción de no-aplicar crédito a otro cliente.
- Auditoría completa por actor y timestamp.

