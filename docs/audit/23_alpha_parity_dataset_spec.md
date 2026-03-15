# 23 - Alpha parity dataset spec (MVP/F2/F3)

## Ventana congelada
- Últimos **3 meses calendario completos**.

## Owner de validación
- **Admin David**.

## Reportes/KPIs MVP obligatorios
1. Sales Overview
2. Orders
3. Product Report
4. Customers
5. Profit & Loss Statement

## Especificación de dataset canónico

| KPI/Reporte | Fuente de datos | Fórmula | Columnas congeladas | Granularidad | Expected output | Owner |
|---|---|---|---|---|---|---|
| Sales Overview | Woo orders/refunds | Definir | Definir | día/semana/mes | Definir | David |
| Orders | Woo orders | Definir | Definir | order-level + periodo | Definir | David |
| Product Report | Woo order items/products | Definir | Definir | SKU/producto/periodo | Definir | David |
| Customers | Woo customers/orders | Definir | Definir | customer/periodo | Definir | David |
| P&L Statement | Woo + costos/gastos | Definir | Definir | periodo/canal/vendedor | Definir | David |

## Reglas técnicas
- El expected output debe ser numérico y reproducible.
- Versionar dataset (`dataset_version`) y fecha de congelamiento.
- Toda discrepancia > umbral acordado requiere RCA.

## Abierto técnico (bloqueante)
- Completar columnas/fórmulas/expected values exactos por KPI con extracción real.

