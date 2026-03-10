# 00E - Unknowns and assumptions (pre-roadmap)

## Supuestos explícitos

1. El plugin `VC Profit Loss` es el núcleo operativo diario de vendedores.
   - Estado: **confirmado por contexto** y consistente con código del repo.
2. El reemplazo total de Alpha Insights Pro es objetivo principal.
   - Estado: **confirmado por contexto**.
3. El owner acepta alternativa sin Sheets si existe UI amigable sin costo API.
   - Estado: **confirmado por contexto**.
4. La reconstrucción se hará en staging/subdominio con stack similar.
   - Estado: **confirmado por contexto**.

## Incertidumbres críticas abiertas (deben cerrarse antes del roadmap final)

1. Fórmula exacta de comisión por vendedor/cliente/bracket (regla formal versionable).
2. Modelo final de crédito a favor (cupón vs wallet vs credit memo contable).
3. Requisitos legales/fiscales exactos del PDF (jurisdicción, numeración, formato tributario).
4. Conjunto exacto de columnas de `CATALOGO PAGINA` y su semántica campo a campo.
5. Política definitiva de conflicto en sync bidireccional Woo<->Sheet (aunque Woo sea preferente).
6. Inventario en variaciones: SKU por variación, parent-child y comportamiento de backorders.
7. Qué reglas reales están activas hoy en Min/Max y Minimum Purchase (si alguna).
8. Si `Customer Addresses` tiene procesos críticos no documentados.
9. Definición de “canal” para P&L (vendedor, campaña, origen, etc.) y cómo se captura.
10. Nivel de granularidad de gastos (manual, importados, integrados por API).

## Riesgos si no se aclaran
- Rebuild incompleto de comisiones y P&L.
- Discrepancias de inventario y financieras tras migración.
- Riesgo legal en facturación documental.
- Sobrecosto por retrabajo de integraciones.

## Clasificación de inferencias usadas
- **Confirmado por código:** mecanismos de customer assignment y vendor dashboards en custom plugin.
- **Confirmado por contexto/captura:** uso real de plugins externos y procesos operativos descritos.
- **Inferencia probable:** superposición entre plugins de pricing/qty y parte del custom plugin.
- **Incertidumbre abierta:** configuraciones internas detalladas de plugins externos.

