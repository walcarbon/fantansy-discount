# 11 - Roadmap consistency review (final)

## Estado de consistencia
- `00C`, `00D`, `07`, `08`, `09`, `10` ya están alineados con decisiones cerradas.
- Se eliminaron ambigüedades en REPLACE/HYBRID/KEEP de plugins críticos.
- Se fijaron side-effects de estados, comisiones y Woo-first sync.

## Hallazgos que permanecen (no de negocio, sí técnicos)

| tipo | severidad | documentos afectados | evidencia concreta | por qué importa | qué falta/corregir | impacto |
|---|---|---|---|---|---|---|
| supuesto abierto | crítica | 16,17 | modelo técnico credit ledger pendiente de implementación exacta | afecta contabilidad/saldos | cerrar diseño técnico final | bloquea módulo crédito |
| hueco de migración | crítica | 16,15 | inventario técnico por plugin incompleto | riesgo pérdida datos/config | completar inventario técnico | bloquea salida plugins |
| hueco de migración | crítica | 16,08 | dataset Alpha con expected outputs no formalizado | sin paridad no hay sustitución segura | congelar dataset+baseline | bloquea salida Alpha |
| supuesto abierto | alta | 16 | authorization matrix por endpoint no cerrada | requisito de aislamiento | cerrar ACL matrix | bloquea hardening |
| hueco de migración | alta | 16 | runbooks rollback no ensayados | alto MTTR en incidentes | crear y probar runbooks | bloquea despliegue controlado |
| hueco de migración | alta | 19,16 | schema/idempotencia final sync Woo-first no cerrado | riesgo drift/sobrescritura | cerrar contrato técnico | bloquea sync productivo |
| supuesto abierto | media | 16 | journeys UX bodeguero detallados pendientes | riesgo adopción operativa | aterrizar journeys | afecta salida YITH FM |

