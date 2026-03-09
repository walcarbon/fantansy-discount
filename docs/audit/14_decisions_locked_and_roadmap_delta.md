# 14 - Decisions locked and roadmap delta (final)

## Decisiones bloqueadas (resumen ejecutivo)
- Comisión semanal lunes-domingo; nace en `processing`.
- Bracket reemplaza % base (no suma), configurable por vendedor.
- Devolución impacta comisión en semana siguiente.
- Crédito cliente: ledger interno + nota de crédito; parcial; no vence.
- Pricing: específico cliente > rol > base; mínimos/múltiplos globales.
- Stock: descuenta `ready-to-go`; repone inmediato en devolución.
- Estados custom: `ready-to-go`, `delivered`; flujo completo termina en `completed`.
- Checkout/pagos: offline-first.
- Woo-first sync con Sheet; conflicto gana Woo; restricciones de campos cerradas.
- Roles finales cerrados (sin shop manager).
- Licencias premium disponibles en staging y producción.

## Delta sobre versión anterior
1. Semana de comisión formalizada (lunes-domingo).
2. Regla de cambio de % “aplica inmediato” cerrada.
3. Manejo explícito de comisión negativa: ajuste manual.
4. Sheet puede crear solo productos simples.
5. Sheet puede despublicar, no borrar.
6. Devoluciones permitidas desde pending/processing/ready-to-go.
7. Bodeguero no modifica precios/stock final; reporta diferencias al admin.
8. Automatizaciones mínimas obligatorias definidas (5).
9. Alpha MVP KPI set y aprobador (David) definidos.
10. PDFs MVP + campos + distribución de email definidos.

