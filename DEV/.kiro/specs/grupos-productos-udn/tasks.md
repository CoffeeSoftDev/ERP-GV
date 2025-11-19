# Implementation Plan

## Overview

Este plan de implementación desglosa el desarrollo de la funcionalidad "Grupos por UDN" en tareas incrementales y ejecutables. Cada tarea está diseñada para ser implementada de forma independiente, construyendo sobre las tareas anteriores.

---

## Tasks

- [x] 1. Implementar método listGrupos() en el modelo


  - Crear método `listGrupos($params)` en `mdl-productos-soft.php`
  - Implementar query SQL con LEFT JOIN entre `soft_grupo_productos` y `soft_productos`
  - Usar COUNT() para obtener cantidad de productos por grupo
  - Agregar soporte para filtro opcional por UDN
  - Usar GROUP BY para agrupar correctamente por id de grupo
  - Retornar array con estructura: `[id, grupoproductos, id_UDN, cantidad_productos]`
  - _Requirements: 8, 10_



- [ ] 2. Implementar método lsGrupos() en el controlador
  - Crear método `lsGrupos()` en `ctrl-productos-soft.php`
  - Obtener parámetro `udn` desde `$_POST` con valor por defecto 'all'
  - Validar que UDN sea numérica o 'all'
  - Llamar a `listGrupos()` del modelo con parámetros apropiados
  - Sanitizar datos de salida con `htmlspecialchars()`
  - Construir array de respuesta con estructura: `[status, grupos, total]`

  - Retornar respuesta en formato JSON
  - _Requirements: 7, 10_

- [ ] 3. Modificar método listProductos() para soportar filtro por grupo
  - Abrir archivo `mdl-productos-soft.php`
  - Localizar método `listProductos($params)`
  - Agregar condición WHERE para filtro por `id_grupo_productos`
  - Verificar que el parámetro `grupo` sea numérico antes de agregarlo a la query

  - Mantener compatibilidad con filtros existentes (udn, anio, mes)
  - Probar que la query funcione con y sin el filtro de grupo
  - _Requirements: 4, 10_

- [ ] 4. Modificar método lsProductos() para recibir parámetro grupo
  - Abrir archivo `ctrl-productos-soft.php`
  - Localizar método `lsProductos()`
  - Agregar obtención de parámetro `grupo` desde `$_POST` con valor por defecto 'all'


  - Agregar validación de que grupo sea numérico o 'all'
  - Pasar parámetro `grupo` al método `listProductos()` del modelo
  - Mantener compatibilidad con llamadas existentes sin parámetro grupo
  - _Requirements: 4, 10_

- [ ] 5. Agregar nueva pestaña "Grupos por UDN" en el layout
  - Abrir archivo `productos-soft.js`


  - Localizar método `layout()` de la clase `ProductosSoft`
  - Agregar nuevo objeto en el array `json` del método `tabLayout()`
  - Configurar pestaña con: `id: "grupos-udn"`, `tab: "Grupos por UDN"`
  - Asignar evento `onClick: () => this.renderGruposUdn()`
  - Verificar que el sistema genere automáticamente el contenedor `container-grupos-udn`

  - _Requirements: 1, 10_

- [ ] 6. Implementar método renderGruposUdn() para inicializar la pestaña
  - Crear método `renderGruposUdn()` en la clase `ProductosSoft`
  - Llamar a `filterBarGrupos()` para crear la barra de filtros
  - Ejecutar `loadGruposCards()` después de un timeout de 100ms
  - Asegurar que el método sea accesible desde el evento onClick de la pestaña
  - _Requirements: 1, 2_



- [ ] 7. Implementar método filterBarGrupos() para crear barra de filtros
  - Crear método `filterBarGrupos()` en la clase `ProductosSoft`
  - Obtener referencia al contenedor `#container-grupos-udn`
  - Crear estructura HTML con dos divs: `filterBarGrupos` y `contentGrupos`
  - Usar `createfilterBar()` de CoffeeSoft para generar filtros
  - Agregar select de UDN con datos de `lsudn` y evento `onchange: "app.loadGruposCards()"`
  - Agregar botón "Regresar a Grupos" con clase `d-none` inicialmente
  - Configurar evento onClick del botón para ejecutar `loadGruposCards()`
  - _Requirements: 2, 6, 10_



- [ ] 8. Implementar método loadGruposCards() para obtener grupos del backend
  - Crear método `loadGruposCards()` en la clase `ProductosSoft`
  - Declarar método como `async` para usar await
  - Obtener valor de UDN seleccionada desde `#filterBarGrupos #udnGrupos`
  - Ocultar botón "Regresar" agregando clase `d-none`
  - Realizar petición AJAX con `useFetch()` usando `opc: 'lsGrupos'` y parámetro `udn`
  - Validar que la respuesta tenga `status: 200` y contenga array `grupos`
  - Llamar a `renderGruposCards()` pasando el array de grupos
  - Implementar manejo de errores con try-catch y mostrar alertas apropiadas
  - Mostrar mensaje informativo cuando no hay grupos disponibles
  - _Requirements: 2, 3, 11_




- [ ] 9. Implementar método renderGruposCards() para mostrar cards en grid
  - Crear método `renderGruposCards(grupos)` en la clase `ProductosSoft`
  - Obtener referencia al contenedor `#contentGrupos`
  - Crear estructura HTML con título, subtítulo y div `gruposGrid`
  - Aplicar clases TailwindCSS al grid: `grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3`
  - Iterar sobre el array de grupos con `forEach()`
  - Para cada grupo, crear una card con jQuery usando clases: `bg-white border-2 border-gray-200 rounded-lg p-3`
  - Agregar efectos hover: `hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer`
  - Mostrar nombre del grupo en `<h4>` con clase `font-bold text-sm text-gray-800 mb-2 text-center`
  - Mostrar cantidad de productos en `<span>` con clase `text-2xl font-bold text-blue-600`
  - Agregar evento click a cada card para ejecutar `showProductosByGrupo(grupo.id, grupo.valor)`
  - Agregar cada card al grid con `append()`
  - _Requirements: 3, 9, 12_

- [ ] 10. Implementar método showProductosByGrupo() para mostrar productos filtrados
  - Crear método `showProductosByGrupo(idGrupo, nombreGrupo)` en la clase `ProductosSoft`
  - Declarar método como `async` para usar await
  - Mostrar botón "Regresar" removiendo clase `d-none`
  - Obtener valor de UDN seleccionada desde `#filterBarGrupos #udnGrupos`
  - Realizar petición AJAX con `useFetch()` usando `opc: 'lsProductos'`, `grupo: idGrupo`, `udn: udn`
  - Validar que la respuesta tenga `status: 200`
  - Reemplazar contenido de `#contentGrupos` con nueva estructura HTML
  - Mostrar título con nombre del grupo y subtítulo "Productos del grupo"
  - Crear div `tableProductosGrupo` para contener la tabla
  - Usar `createTable()` de CoffeeSoft para renderizar tabla de productos
  - Configurar tabla con: `theme: 'corporativo'`, `datatable: true`, `pag: 15`
  - Configurar alineación de columnas: `center: [2]`, `right: [3, 4]`
  - Implementar manejo de errores con try-catch
  - _Requirements: 4, 5, 6, 11_

- [ ]* 11. Agregar validación de entrada en el backend
  - Abrir archivo `ctrl-productos-soft.php`
  - En método `lsGrupos()`, validar que `udn` sea numérico o 'all'
  - Retornar error 400 si la validación falla
  - En método `lsProductos()`, validar que `grupo` sea numérico o 'all'
  - Retornar error 400 si la validación falla
  - Agregar mensajes de error descriptivos en las respuestas
  - _Requirements: 7, 11_

- [ ]* 12. Implementar manejo de casos edge en el frontend
  - En `loadGruposCards()`, manejar caso cuando `response.grupos.length === 0`
  - Mostrar mensaje "No hay grupos disponibles para esta UDN" con ícono informativo
  - En `showProductosByGrupo()`, manejar caso cuando no hay productos en el grupo
  - Mostrar mensaje apropiado en lugar de tabla vacía
  - Agregar logs de consola para debugging (console.log, console.error)
  - _Requirements: 11_

- [ ]* 13. Optimizar queries SQL con índices
  - Conectar a la base de datos MySQL
  - Verificar si existe índice en `soft_grupo_productos(id_UDN)`
  - Crear índice si no existe: `CREATE INDEX idx_grupo_udn ON soft_grupo_productos(id_UDN)`
  - Verificar si existe índice en `soft_productos(id_grupo_productos)`
  - Crear índice si no existe: `CREATE INDEX idx_producto_grupo ON soft_productos(id_grupo_productos)`
  - Ejecutar EXPLAIN en las queries para verificar uso de índices
  - _Requirements: 11_

- [ ]* 14. Agregar logs de monitoreo en el backend
  - En método `lsGrupos()`, agregar log del tiempo de ejecución de la query
  - En método `lsProductos()`, agregar log cuando se usa filtro por grupo
  - Usar función `error_log()` de PHP para escribir logs
  - Incluir información relevante: timestamp, UDN, cantidad de resultados
  - _Requirements: 11_

- [ ]* 15. Implementar tests de integración
  - Crear archivo de test para flujo completo de navegación
  - Test 1: Seleccionar UDN → Verificar que se cargan grupos correctos
  - Test 2: Click en card de grupo → Verificar que se muestran productos filtrados
  - Test 3: Click en botón "Regresar" → Verificar que se vuelve a mostrar grid de cards
  - Test 4: Cambiar UDN → Verificar que se actualizan los grupos
  - Test 5: Filtro combinado UDN + Grupo → Verificar productos correctos
  - _Requirements: 11_

- [ ]* 16. Realizar pruebas de performance
  - Medir tiempo de carga de `lsGrupos()` con 50 grupos
  - Verificar que el tiempo sea menor a 2 segundos
  - Medir tiempo de carga de `lsProductos()` con 500 productos
  - Verificar que el tiempo sea menor a 3 segundos
  - Medir tiempo de renderizado de grid con 100 cards
  - Verificar que no haya lag visual perceptible
  - Documentar resultados y optimizar si es necesario
  - _Requirements: 11_

- [ ]* 17. Documentar código con comentarios
  - Agregar comentarios JSDoc en métodos JavaScript principales
  - Agregar comentarios PHPDoc en métodos PHP del controlador y modelo
  - Documentar parámetros de entrada y valores de retorno
  - Agregar ejemplos de uso en comentarios cuando sea apropiado
  - Mantener comentarios concisos y relevantes
  - _Requirements: 10_

- [ ]* 18. Crear documentación de usuario
  - Crear documento con capturas de pantalla del flujo de uso
  - Documentar cómo acceder a la pestaña "Grupos por UDN"
  - Explicar cómo filtrar por UDN
  - Explicar cómo navegar entre grupos y productos
  - Documentar el uso del botón "Regresar"
  - Incluir casos de uso comunes y tips
  - _Requirements: 1, 2, 3, 4, 6_

---

## Task Dependencies

```
1 (listGrupos modelo) → 2 (lsGrupos ctrl)
3 (modificar listProductos) → 4 (modificar lsProductos)
5 (agregar pestaña) → 6 (renderGruposUdn)
6 → 7 (filterBarGrupos)
7 → 8 (loadGruposCards)
2 → 8 (loadGruposCards necesita backend)
8 → 9 (renderGruposCards)
4 → 10 (showProductosByGrupo necesita backend modificado)
9 → 10 (showProductosByGrupo)

11-18 son tareas opcionales que pueden ejecutarse en paralelo después de completar 1-10
```

## Estimated Timeline

- **Tareas 1-4 (Backend):** 2-3 horas
- **Tareas 5-7 (Setup Frontend):** 1-2 horas
- **Tareas 8-10 (Lógica Frontend):** 3-4 horas
- **Tareas 11-18 (Opcionales):** 4-6 horas

**Total estimado (core):** 6-9 horas
**Total estimado (con opcionales):** 10-15 horas

## Testing Checklist

Después de completar las tareas core (1-10), verificar:

- [ ] La nueva pestaña "Grupos por UDN" aparece en el módulo
- [ ] El select de UDN muestra todas las unidades de negocio
- [ ] Al seleccionar una UDN, se cargan los grupos correspondientes
- [ ] Las cards de grupos se muestran en un grid responsive
- [ ] Cada card muestra el nombre del grupo y cantidad de productos
- [ ] Al hacer clic en una card, se muestran los productos del grupo
- [ ] La tabla de productos muestra solo productos del grupo seleccionado
- [ ] El botón "Regresar" aparece cuando se visualizan productos
- [ ] Al hacer clic en "Regresar", se vuelve a mostrar el grid de cards
- [ ] Los filtros de UDN se mantienen al navegar entre vistas
- [ ] No hay errores en la consola del navegador
- [ ] No hay errores en los logs del servidor PHP

## Rollback Instructions

Si es necesario revertir los cambios:

1. **Revertir cambios en productos-soft.js:**
   ```javascript
   // En método layout(), eliminar el objeto de la pestaña "Grupos por UDN"
   // Comentar o eliminar métodos: renderGruposUdn, filterBarGrupos, 
   // loadGruposCards, renderGruposCards, showProductosByGrupo
   ```

2. **Revertir cambios en ctrl-productos-soft.php:**
   ```php
   // Comentar o eliminar método lsGrupos()
   // Revertir modificaciones en lsProductos() (eliminar soporte para parámetro grupo)
   ```

3. **Revertir cambios en mdl-productos-soft.php:**
   ```php
   // Comentar o eliminar método listGrupos()
   // Revertir modificaciones en listProductos() (eliminar filtro por grupo)
   ```

4. **Eliminar índices creados (opcional):**
   ```sql
   DROP INDEX idx_grupo_udn ON soft_grupo_productos;
   DROP INDEX idx_producto_grupo ON soft_productos;
   ```

## Notes

- Las tareas marcadas con `*` son opcionales y se enfocan en optimización, testing y documentación
- Las tareas 1-10 son el core funcional y deben completarse en orden
- Se recomienda hacer commits de git después de cada tarea completada
- Probar cada tarea individualmente antes de continuar con la siguiente
- Mantener comunicación con el equipo sobre el progreso
- Documentar cualquier decisión de diseño que difiera del plan original
