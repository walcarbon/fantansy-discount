# 12 - Missing business decisions (final reduced set)

## Nota
Con la fuente de verdad actualizada, casi todas las decisiones de negocio quedaron cerradas.

## Permanecen abiertos (técnico-funcional)

| tipo | severidad | documentos afectados | evidencia concreta | por qué importa | qué falta/corregir | impacto |
|---|---|---|---|---|---|---|
| supuesto abierto | crítica | 17,16 | implementación técnica exacta del credit ledger | conciliación financiera | definir modelo técnico definitivo en Woo/custom tables | bloquea crédito producción |
| supuesto abierto | alta | 16,18 | authorization matrix endpoint-level no cerrada | aislamiento por rol | firmar matriz final ACL | bloquea hardening |
| supuesto abierto | media | 16 | journeys UX detallados bodeguero no cerrados | paridad funcional YITH FM | validar journeys y tiempos objetivo | bloquea salida YITH FM |

