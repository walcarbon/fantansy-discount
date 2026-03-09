# 00B - Discovery questionnaire (respondido y trazable)

## Resumen
Este cuestionario consolida el discovery ya respondido por el negocio y deja trazabilidad de qué quedó:
- confirmado,
- inferido,
- abierto.

## A. Flujos críticos del negocio (respuestas)

### A1. Vendedores y clientes
- Vendedores activos: Lim, Armando, Yenny, David (David también admin).
- Alta de cliente: frontend vendor con campos básicos (`username`, `email`, `first_name`, `last_name`, `tax_id`, `password`).
- Direcciones: se completan principalmente al montar la orden en checkout.
- Regla de aislamiento: vendedor no ve/edita clientes ajenos; admin sí.

### A2. Pedidos/checkout
- Flujo operativo: tienda frontend -> checkout normal.
- Campo crítico: `Customer assign order` (dropdown de clientes del vendedor) + autofill de direcciones.
- Estado inicial pedido: `pending payment`.
- Estados administrativos usados: Pending payment, Processing, Ready To Go, Delivered, On hold, Completed, Cancelled, Refunded, Failed, temporary, Draft.
- Pagos actuales: sin pasarela online; flujo offline/manual (check/COD/offline y cambio de estado al recibir pago).
- Shipping actual: Florida flat rate $5.

### A3. Comisiones y métricas
- Comisión actual: manual por brackets y factores por vendedor/cliente.
- KPI requeridos vendedor/negocio: ventas brutas/netas, margen, comisión, ticket promedio, clientes activos.
- Ventanas de análisis deseadas: diario/semanal/mensual/anual (pagos semanales).

### A4. Inventario/bodega/devoluciones
- Bodeguero opera desde frontend con YITH Frontend Manager.
- Admin opera backend Woo + hoja manual (import/export).
- Requerimiento confirmado: movimientos formalizados (entradas/salidas/devoluciones/ajustes).
- Devoluciones hoy: Woo + nota de crédito manual; inventario real se desvirtúa al reimportar plantilla.
- Ubicación: una sola.

### A5. P&L / financiero
- Egresos requeridos: costo producto, flete, ads, comisiones, salarios, overhead.
- Vistas requeridas: por período, vendedor, categoría, canal.

## B. Plugins prioritarios (respuestas)

### Alpha Insights Pro
- Se confirma uso de: profit reporting, COGS, expense management, ad integration, traffic analysis, report builder drag-drop, export CSV/PDF/live share, P&L intelligence, métricas por producto/cliente/canal/campaña/período.
- Comparativas temporales: **no** requeridas como prioridad.
- Usuario principal: dueño (1-2 veces al mes), quiere mantener todo y ampliar.

### AutomateWoo
- Workflows actuales críticos:
  1) seguimiento factura no cancelada (1 mes),
  2) notificación de cambio de status a bodeguero/admin/customer,
  3) alerta a vendedor por cliente inactivo 60 días,
  4) notificación de orden nueva a bodeguero.

### Pricing / role methods
- Dynamic Pricing es el principal.
- Customer Specific Pricing para clientes especiales.
- Reglas futuras deseadas: volumen, free shipping selectivo, shipping diferenciado.
- Role Based Methods necesario hoy por flujo de pago offline + restricciones por rol.

### YITH Frontend Manager
- Uso actual: básicamente bodeguero (stock/devoluciones; futuro cupones).
- No superposición directa confirmada con módulos críticos de VC Profit Loss.

### Wishlist / QuoteUp
- Wishlist: no aporta conversión hoy; si queda, sería multi-dispositivo/logueado sin social.
- QuoteUp: uso actual cero.

### Invoices
- Requerimientos confirmados: packing slips, notas de crédito, numeración fiscal, adjuntos email; deseo de mayor formalidad legal/fiscal.

### Search
- Advanced Woo Search se valora por AJAX, sugerencias, typo/misspelling y búsqueda por SKU.

## C. Plugins visuales/base
- Elementor/Pro + Astra: capa visual principal, sin lógica de negocio crítica.
- WP Rocket: configuración básica.
- Wordfence: uso básico.
- ACF Pro: usado para datos de negocio (precios/descuentos específicos).
- Essential Addons: uso puntual (Advanced Tabs).

## D. Google Sheets sync
- Preferencia: Woo fuente de verdad + Sheet espejo operativo (bidireccional posible).
- Tab objetivo: `CATALOGO PAGINA`.
- Identificador clave: SKU.
- Sync Woo -> Sheet: venta/refund/cancelación/ajuste/importación/alta producto/cambio precio.
- Sync Sheet -> Woo: stock/precio/alta/baja/categoría.
- Frecuencia objetivo: tiempo real (tolerancia operativa <= 5 min).
- Conflicto: priorizar SKU Woo en caso de discrepancia.
- Variables: sí, soportar.
- Edición Sheet: solo admin.
- Backorders permitidos.
- Problema actual crítico: importación masiva sobrescribe todo el inventario.

## Abiertos mínimos
1. Modelo exacto de nota de crédito (wallet, cupón, saldo contable).
2. Regla exacta de brackets por vendedor/cliente (fórmula formal).
3. Alcance legal/fiscal por país para facturación PDF.
4. Decisión final wishlist/quoteup (retirar ya o dejar en freeze).

