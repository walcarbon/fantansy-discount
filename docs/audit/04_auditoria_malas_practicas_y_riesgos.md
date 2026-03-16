# 04 - Auditoría de malas prácticas y riesgos

## 1) Seguridad

### Hallazgos
1. **Checks de capacidad incompletos en operaciones sensibles**
   - `delete_user` comprueba capacidad dentro de loop, pero otras acciones de alta/edición no validan explícitamente capacidad contextual en handler.
   - AJAX `actions_before_delete_user` valida nonce pero no valida capability específica.

2. **Acceso directo a superglobales**
   - Uso recurrente de `$_POST`, `$_SERVER`, `$_REQUEST` (aunque parte pasa por helper).

3. **Escaping inconsistente en salida HTML**
   - Se imprimen múltiples variables sin `esc_html/esc_attr/esc_url` homogéneo, especialmente en templates y notices HTML ensamblados.

4. **Uso de `extract()` y supresión de errores `@`**
   - Incrementa superficie de bugs/inyección lógica y dificulta revisión de seguridad.

5. **Redirecciones con `wp_redirect` en lugar de `wp_safe_redirect` en varios puntos**
   - Riesgo menor pero evitable.

### Riesgo agregado
Medio-alto.

---

## 2) Performance

### Hallazgos
1. `flush_rewrite_rules()` en cada `init` (crítico).
2. Múltiples consultas `wc_get_orders(limit=-1)` para métricas y tablas.
3. Re-cálculo redundante en templates (`self::get_table_*_rows()` invocado varias veces por render).
4. Analytics admin: SQL + loop posterior con `wc_get_order` (doble coste).
5. Posibles N+1 al obtener `get_userdata/get_user_meta` dentro de loops grandes.

### Riesgo agregado
Alto.

---

## 3) Mantenibilidad/arquitectura

### Hallazgos
1. **Arquitectura híbrida sin separación de dominio** (helpers procedurales + clases + vistas con lógica).
2. **`VC_Profit_Lost::instance()` no singleton real**; potencial de múltiples instancias y re-registro de hooks.
3. **Side effects en carga de archivos** (autoload incluye helper y comportamiento global).
4. **Clase `Session_Handler` sobrecargada** (routing, analytics, checkout, UI).
5. **Fuerte acoplamiento al estado global WP** (`global`, `VCPL()`, `$_REQUEST`).
6. **Hooks registrados en sitios discutibles** (filtros usados como acciones).

### Riesgo agregado
Alto.

---

## 4) DX (developer experience)

### Hallazgos
- Nombres inconsistentes/typos (`profil`, `analythics`, `vcprfitlost`, `vcpl_autolaod`).
- Sin documentación técnica interna real (README mínimo).
- Sin pipeline de build para ESM y sin control de versiones de assets.
- Debug logs y `console.log` residuales en código productivo.

### Riesgo agregado
Medio.

---

## 5) Deuda técnica

### Hallazgos
- Activator/deactivator vacíos.
- Código potencialmente muerto/incompleto:
  - `update_customer_billing_shipping()` no hookeado.
  - hooks comentados en activación/desactivación.
  - callback inexistente `change_login_required_message`.
- Lógica duplicada en construcción de tablas/filtros entre templates y classes.

### Riesgo agregado
Alto.

---

## 6) Calidad de código y estándares WP

### Hallazgos
- Sanitización parcial y no contextual.
- Internacionalización incompleta/inconsistente (text domains mixtos, strings hardcoded).
- Funciones largas y responsabilidades mezcladas.
- SQL manual donde existe API Woo robusta.

### Riesgo agregado
Medio-alto.

---

## 7) Cobertura de tests

- No se detectaron tests unitarios, integración o E2E.
- No hay CI visible ni scripts de quality gates.

**Riesgo:** alto (regresiones invisibles en refactors/upgrade Woo).

---

## 8) Resumen de riesgos críticos

1. HPOS incompatibilidad probable por SQL legacy en pedidos.
2. callback hook no implementado (riesgo fatal).
3. flush rewrite por request.
4. seguridad/capabilities no homogéneas en operaciones de usuarios.
5. ausencia total de tests.

