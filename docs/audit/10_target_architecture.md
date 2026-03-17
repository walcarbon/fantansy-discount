# 10 - Target architecture (final)

## Principio rector
`VC Profit Loss` final concentra dominios de negocio; WooCommerce permanece core transaccional.

## Dominios dentro de VC Profit Loss final
1. Identity & Authorization
2. Vendor Operations
3. Checkout Offline Core
4. Pricing Engine
5. Commission Engine
6. Order State Machine
7. Warehouse & Inventory Ledger
8. Returns & Credit Ledger
9. Woo-first Sync (Sheet Adapter)
10. Analytics & P&L
11. Document Engine (PDF)
12. Automation Engine

## Contratos críticos cerrados
- Comisión nace en `processing`; liquidación semanal lunes-domingo.
- Stock descuenta en `ready-to-go`; devolución repone inmediato.
- `delivered` es operativo; luego `completed` cierra flujo.
- Woo-first sync; conflicto gana Woo; SKU como clave.

## Roles objetivo
- admin
- vendor
- bodeguero
- customer by vendor
- customer by shop
- customer by wholesaler

## Integraciones
- Google Sheets (`Nueva Plantilla Productos Woocommerce` / `CATALOGO PAGINA`) como espejo operativo controlado.

## Compatibilidad técnica
- Woo CRUD first.
- HPOS-safe.
- Evaluación explícita Store API / Blocks.
- Observabilidad y trazabilidad por evento.

