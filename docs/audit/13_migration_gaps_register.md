# 13 - Migration gaps register (final)

| tipo | severidad | documentos afectados | evidencia concreta | por qué importa | qué falta/corregir | impacto |
|---|---|---|---|---|---|---|
| hueco de migración | crítica | 15,16 | inventario técnico por plugin no finalizado | no se puede migrar seguro | ejecutar inventario per-plugin | bloquea REPLACE |
| hueco de migración | crítica | 08,16 | dataset Alpha + expected outputs no cerrado | no paridad verificable | baseline 3 meses y expected metrics | bloquea salida Alpha |
| hueco de migración | alta | 19,16 | contrato técnico sync Woo-first no finalizado | riesgo drift/sobrescritura | schema+validación+idempotencia+retry | bloquea sync productivo |
| hueco de migración | alta | 18,16 | state machine técnica no aterrizada en comandos idempotentes | side-effects inconsistentes | especificar transición/compensación | bloquea salida status plugin |
| hueco de migración | alta | 20,16 | mapping trigger-by-trigger AutomateWoo no completo | pérdida automatizaciones críticas | completar matriz paridad automatizaciones | bloquea salida AutomateWoo |
| hueco de migración | alta | 21,16 | paridad documental PDF no validada al 100% | riesgo operativo/legal | validar campos, secuencial y emails | bloquea salida PDF suite |
| hueco de migración | media | 16 | runbooks rollback no ensayados | alto MTTR | ejecutar simulacros rollback | bloquea implementación controlada |
| hueco de migración | media | 16 | journeys bodeguero no detallados | adopción baja | cerrar UX operativa | afecta salida YITH FM |

