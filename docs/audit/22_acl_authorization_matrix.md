# 22 - ACL authorization matrix (endpoint/capability baseline)

## Principios cerrados
- admin: acceso total.
- vendor: solo sus clientes/pedidos/dashboard; crear órdenes para sus clientes.
- bodeguero: operación logística/bodega; sin acceso a pricing/comisiones/config.
- customer roles: self-service mínimo.

## Matriz base por dominio

| Dominio | Admin | Vendor | Bodeguero | Customer roles |
|---|---|---|---|---|
| Config global plugin | Full | No | No | No |
| Pricing global | Full | Read-only final price | No | No |
| Comisiones | Full | Read own summary | No | No |
| Clientes | Full | CRUD solo propios | No | Self-only profile |
| Órdenes | Full | Crear/ver solo propias | Ver operativas asignadas | Ver propias |
| Estado `ready-to-go` | Full | No (salvo excepción futura) | Sí | No |
| Estado `delivered` | Full | Sí según flujo operativo | Sí según flujo operativo | No |
| Inventario global | Full | No | Operativo limitado | No |
| Ubicaciones bodega | Full | Read-only según necesidad | Full operativo | No |
| Sync Woo↔Sheet | Full | No | No | No |
| Credit ledger | Full | Read limitado según pedido/cliente | No | Read own credit |
| PDFs/docs | Full | Read own docs | Packing ops docs | Own docs |

## Endpoints/capabilities a cerrar técnicamente (bloqueante)
1. Lista exacta de endpoints actuales del plugin custom.
2. Capability mapping exacto por endpoint.
3. Negative tests por endpoint (acceso denegado esperado).

