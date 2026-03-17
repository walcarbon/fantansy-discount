# 19 - Sync contract Woo-first Sheet mirror (technical-locked)

## Principios cerrados
- WooCommerce = fuente principal.
- Google Sheet (`Nueva Plantilla Productos Woocommerce` / `CATALOGO PAGINA`) = espejo operativo/backup.
- Clave canónica: SKU.
- Conflicto: gana Woo.
- No bidireccionalidad libre.

## Woo -> Sheet eventos mínimos
- Product create/update.
- Stock change.
- Eventos que alteren stock.
- Refund/devolución que altere stock.

## Sheet -> Woo permitido
- Solo columnas autorizadas y validación SKU estricta.
- Puede crear productos nuevos **solo simples**.
- Puede despublicar, no borrar.

## Ownership por campo

### Editable desde Sheet
- descripción larga/corta
- stock
- precio regular/rebajado
- categorías/tags
- imágenes (URL)
- estado publicado/borrador
- visibilidad catálogo
- costo
- peso/dimensiones
- atributos informativos (no variaciones)
- ubicación bodega
- mínimo por producto

### NO editable desde Sheet
- SKU
- backorders
- variaciones

## Idempotencia obligatoria
- Dedup key estable por evento: `source + sku + event_type + source_event_id/version`.

## Retries obligatorios
- mínimo 3 intentos con backoff exponencial.

## DLQ obligatorio
- tabla/log custom de failed events con payload, error, intentos y estado.

## Modo puente obligatorio (coexistencia importación manual)
- toda importación masiva debe quedar auditada y validada antes de aplicar.

## Campos mínimos de auditoría por evento
- event_id, dedup_key, direction, sku, actor, changed_fields, old_values, new_values, status, attempts, processed_at.

