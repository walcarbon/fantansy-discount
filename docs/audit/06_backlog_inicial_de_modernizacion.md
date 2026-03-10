# 06 - Backlog inicial de modernización (priorizado)

## Escala usada
- **Prioridad:** P0 crítica, P1 alta, P2 media, P3 baja.
- **Impacto:** Alto / Medio / Bajo.
- **Esfuerzo:** S (días), M (1-2 semanas), L (3-6 semanas), XL (>6 semanas).
- **Riesgo:** técnico/negocio de ejecutar el cambio.

---

## P0 - Problemas críticos y quick wins obligatorios

### Tarea 1: Corregir callback inexistente en hook Woo
- **Prioridad:** P0
- **Impacto:** Alto (evita fatal).
- **Esfuerzo:** S
- **Riesgo:** Bajo
- **Área:** session hooks
- **Dependencias:** ninguna
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** checkout/login con distintos roles

### Tarea 2: Eliminar `flush_rewrite_rules()` de `init`
- **Prioridad:** P0
- **Impacto:** Alto (performance)
- **Esfuerzo:** S
- **Riesgo:** Bajo
- **Área:** endpoints/account routing
- **Dependencias:** mover flush a activación/desactivación
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** regeneración enlaces permanentes + endpoints account

### Tarea 3: Blindaje de capacidades en AJAX y handlers de usuarios
- **Prioridad:** P0
- **Impacto:** Alto (seguridad)
- **Esfuerzo:** M
- **Riesgo:** Medio
- **Área:** user CRUD / ajax
- **Dependencias:** definir matriz de permisos por rol
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** pruebas de permisos con roles no admin

### Tarea 4: Remover logs/debug residuales en producción
- **Prioridad:** P0
- **Impacto:** Medio
- **Esfuerzo:** S
- **Riesgo:** Bajo
- **Área:** analytics admin / js checkout
- **Dependencias:** ninguna
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** sanity de UI y consola

---

## P1 - Compatibilidad obligatoria WooCommerce moderno

### Tarea 5: Refactor analytics admin para HPOS-safe (sin SQL legacy)
- **Prioridad:** P1
- **Impacto:** Muy alto
- **Esfuerzo:** L
- **Riesgo:** Alto
- **Área:** reporting admin
- **Dependencias:** diseño de servicio de consultas de órdenes por CRUD
- **Migración datos:** no (si solo lectura)
- **Ventana mantenimiento:** posiblemente sí (si hay cambios de índice/cache)
- **QA manual:** validación de filtros, paginación, totales, comparación contra datos reales

### Tarea 6: Adaptación de checkout vendor a Cart/Checkout Blocks
- **Prioridad:** P1
- **Impacto:** Alto
- **Esfuerzo:** L/XL
- **Riesgo:** Alto
- **Área:** checkout
- **Dependencias:** definir estrategia dual classic/blocks
- **Migración datos:** no
- **Ventana mantenimiento:** no necesariamente
- **QA manual:** checkout completo en classic y blocks

### Tarea 7: Normalizar hooks de checkout (evitar usar filtros para imprimir HTML)
- **Prioridad:** P1
- **Impacto:** Alto
- **Esfuerzo:** M
- **Riesgo:** Medio
- **Área:** checkout render/validation
- **Dependencias:** tarea 6
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** rendering campos + validación + creación orden

---

## P1/P2 - Refactors recomendados de arquitectura

### Tarea 8: Convertir `VCPL()->instance()` en singleton real o DI container simple
- **Prioridad:** P2
- **Impacto:** Medio
- **Esfuerzo:** M
- **Riesgo:** Medio
- **Área:** kernel/boot
- **Dependencias:** auditoría de side effects
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** revisar no duplicidad hooks

### Tarea 9: Extraer dominio Vendor-Customer a servicios/repositorios
- **Prioridad:** P2
- **Impacto:** Alto
- **Esfuerzo:** L
- **Riesgo:** Medio-alto
- **Área:** helpers procedural
- **Dependencias:** tarea 8
- **Migración datos:** no inmediata
- **Ventana mantenimiento:** no
- **QA manual:** reasignación/borrado/consistencia metas

### Tarea 10: Reemplazar sesiones PHP por sistema de notices WP/WC
- **Prioridad:** P2
- **Impacto:** Medio
- **Esfuerzo:** M
- **Riesgo:** Medio
- **Área:** UX admin notices
- **Dependencias:** ninguna
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** flujo de errores/éxitos post-redirect

### Tarea 11: Reforzar escaping/sanitización contextual y eliminar `extract/@`
- **Prioridad:** P1
- **Impacto:** Alto
- **Esfuerzo:** L
- **Riesgo:** Medio
- **Área:** seguridad transversal
- **Dependencias:** ninguna
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** smoke tests forms/tables/endpoints

---

## P2 - Deuda técnica postergable

### Tarea 12: Pipeline de build front/admin (npm + bundling + lint)
- **Prioridad:** P2
- **Impacto:** Medio
- **Esfuerzo:** M
- **Riesgo:** Bajo-medio
- **Área:** assets JS/CSS
- **Dependencias:** definición entorno CI
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** carga de scripts en admin/front

### Tarea 13: Internacionalización y consistencia text domains
- **Prioridad:** P2
- **Impacto:** Medio
- **Esfuerzo:** M
- **Riesgo:** Bajo
- **Área:** UX/i18n
- **Dependencias:** barrido de strings hardcode
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** idioma alterno

### Tarea 14: Declarar y validar dependencias opcionales (WCPDF/Astra)
- **Prioridad:** P2
- **Impacto:** Medio
- **Esfuerzo:** S/M
- **Riesgo:** Bajo
- **Área:** integraciones externas
- **Dependencias:** inventario final de stack productivo
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** fallback sin plugin externo

---

## P1/P2 - Calidad y QA

### Tarea 15: Introducir tests automáticos mínimos (unit + integration)
- **Prioridad:** P1
- **Impacto:** Alto
- **Esfuerzo:** L
- **Riesgo:** Medio
- **Área:** calidad global
- **Dependencias:** modularización básica
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual:** validar paridad con comportamiento legacy

### Tarea 16: Crear suite de regresión manual para flujos críticos
- **Prioridad:** P1
- **Impacto:** Alto
- **Esfuerzo:** M
- **Riesgo:** Bajo
- **Área:** QA
- **Dependencias:** ninguna
- **Migración datos:** no
- **Ventana mantenimiento:** no
- **QA manual específico:**
  - CRUD vendor/customer/shop-manager,
  - reasignación y borrado con clientes,
  - checkout vendor->cliente,
  - analytics front/admin,
  - restricciones por rol.

---

## Tareas de investigación manual (obligatorias)

1. Confirmar versión objetivo real de WordPress/WooCommerce en producción.
2. Confirmar si el sitio usa HPOS habilitado y Checkout Blocks activos.
3. Confirmar plugins externos requeridos (`wcpdf`, Astra addon u otros).
4. Medir rendimiento real de consultas de analytics en base de datos productiva.
5. Validar reglas de negocio de comisión (fecha más cercana vs vigente histórica).

