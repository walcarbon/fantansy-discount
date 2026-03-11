# 24 - Rollback runbook template (estándar obligatorio)

## Encabezado
- Capacidad/Plugin afectado:
- Release/commit:
- Entorno:
- Responsable técnico:
- Responsable negocio:

## 1. Alcance
- Qué cambia
- Qué no cambia
- Dependencias impactadas

## 2. Feature flag / interruptor
- Nombre flag:
- Estado esperado ON/OFF:
- Procedimiento de conmutación:

## 3. Backup previo
- DB snapshot:
- Archivos/config:
- Export de settings:
- Evidencia de backup:

## 4. Criterio go/no-go
- Señales de éxito:
- Señales de abort:
- Umbral de error permitido:

## 5. Pasos de reversión
1. Desactivar flag/cutover.
2. Restaurar configuración previa.
3. Restaurar datos si aplica.
4. Rehabilitar flujo legacy.

## 6. Validación posterior
- Smoke tests mínimos:
- Métricas a revisar (errores, pedidos, stock, emails):
- Aprobación final:

## 7. Comunicación
- A quién notificar:
- Mensaje de incidente:
- Cierre post-mortem:

