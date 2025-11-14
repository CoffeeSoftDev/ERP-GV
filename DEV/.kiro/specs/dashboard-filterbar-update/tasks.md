# Implementation Plan

## Overview

Este plan de implementación detalla las tareas necesarias para actualizar el filterBar del Dashboard de Ventas, reemplazando los inputs tipo `month` por un DateRangePicker flexible y agregando un select de comparación de años.

## Tasks

- [x] 1. Preparar estructura y dependencias


  - Verificar que el archivo `kpi/marketing/ventas/src/js/dashboard.js` existe y es accesible
  - Confirmar que las dependencias están disponibles: jQuery, Moment.js, DateRangePicker, Chart.js
  - Verificar que la función global `dataPicker()` está disponible en el proyecto
  - Verificar que la función global `getDataRangePicker()` está disponible
  - _Requirements: 1.1, 4.1, 4.2_



- [ ] 2. Implementar método generateYearOptions()
  - Crear método `generateYearOptions()` en la clase `FinanceDashboard`
  - El método debe generar un array con los últimos 5 años en formato `[{id: year, valor: "year"}]`
  - Establecer el año actual como primer elemento del array


  - Retornar el array de opciones
  - _Requirements: 2.2, 2.4_

- [ ] 3. Actualizar método filterBar()
  - Modificar el método `filterBar()` en la clase `FinanceDashboard`
  - Reemplazar el div `containerPeriodo1` con input tipo month por un componente `input-calendar`
  - Configurar el `input-calendar` con id `dateRangePicker`, label "Período de consulta", class "col-sm-4"
  - Reemplazar el div `containerPeriodo2` por un select con id `yearComparison`
  - Configurar el select con label "Comparar con año", class "col-sm-3"


  - Asignar `this.generateYearOptions()` como data del select
  - Configurar `onchange: "dashboard.renderDashboard()"` en el select
  - Mantener el select de UDN en la primera posición sin cambios
  - _Requirements: 1.1, 2.1, 3.1, 3.2_

- [ ] 4. Implementar método initDateRangePicker()
  - Crear método `initDateRangePicker()` en la clase `FinanceDashboard`
  - Llamar a la función global `dataPicker()` con parent: "dateRangePicker"
  - Configurar startDate como `moment().subtract(7, "days")`
  - Configurar endDate como `moment().subtract(1, "days")`
  - Implementar objeto `ranges` con las opciones predefinidas:
    - "Última semana": últimos 7 días
    - "Últimas 2 semanas": últimos 14 días
    - "Últimas 3 semanas": últimos 21 días
    - "Últimas 4 semanas": últimos 28 días


    - "Mes Actual": desde inicio del mes hasta ayer
    - "Mes Anterior": mes completo anterior
    - "Año actual": desde inicio del año hasta ayer
    - "Año anterior": año completo anterior


  - Configurar callback `onSelect: (start, end) => { this.renderDashboard(); }`
  - _Requirements: 1.2, 1.3, 1.4_

- [ ] 5. Actualizar método layout() para inicializar DateRangePicker
  - Modificar el método `layout()` en la clase `FinanceDashboard`
  - Agregar llamada a `this.initDateRangePicker()` después de la llamada a `this.filterBar()`
  - Asegurar que la inicialización ocurre dentro del setTimeout existente (después de 500ms)


  - _Requirements: 1.1, 1.4_

- [ ] 6. Refactorizar método renderDashboard() - Extracción de datos
  - Modificar el método `renderDashboard()` en la clase `FinanceDashboard`
  - Reemplazar la extracción de `periodo1` y `periodo2` por llamada a `getDataRangePicker('dateRangePicker')`



  - Extraer `dateRange.fi` y `dateRange.ff` del resultado
  - Calcular `anio1` y `mes1` desde `moment(dateRange.fi)`
  - Obtener `yearComparison` desde `$('#filterBar #yearComparison').val()`


  - Asignar `anio2 = parseInt(yearComparison)` y `mes2 = mes1`
  - _Requirements: 2.1, 2.2, 5.1, 5.2_

- [ ] 7. Refactorizar método renderDashboard() - Actualizar peticiones AJAX
  - Modificar las peticiones `useFetch()` en `renderDashboard()`
  - Agregar parámetros `fi: dateRange.fi` y `ff: dateRange.ff` al objeto data
  - Actualizar parámetros `anio1`, `mes1`, `anio2`, `mes2` con los valores calculados

  - Aplicar cambios tanto a la petición de `api_dashboard` como a la de `api`
  - _Requirements: 4.4, 5.1, 6.1, 6.2_

- [ ] 8. Actualizar método renderDashboard() - Manejo de respuestas
  - Verificar que los métodos de actualización de gráficos reciben los años correctos
  - Actualizar llamada a `chequeComparativo()` para usar `anioA: anio2` y `anioB: anio1`

  - Asegurar que todos los componentes visuales se actualizan con los datos correctos
  - _Requirements: 2.3, 5.2, 6.4_

- [ ] 9. Implementar validación de datos en renderDashboard()
  - Agregar bloque try-catch al método `renderDashboard()`

  - Validar que UDN tiene valor antes de continuar
  - Validar que `dateRange` existe y tiene propiedades `fi` y `ff`
  - Validar que la fecha de inicio es anterior a la fecha de fin usando Moment.js
  - Mostrar alertas con `alert({ icon: "error", text: "..." })` en caso de errores
  - Agregar console.error para logging de errores
  - _Requirements: 5.4, 6.1, 6.2_



- [ ] 10. Eliminar código obsoleto
  - Remover las líneas que establecen valores por defecto para `periodo1` y `periodo2`
  - Remover las líneas que calculan `currentYear`, `currentMonth`, `lastYear` si ya no se usan
  - Remover las líneas que hacen `$('#containerPeriodo1').removeClass()` y `$('#containerPeriodo2').removeClass()`
  - Limpiar cualquier código comentado relacionado con los inputs tipo month

  - _Requirements: 4.3_

- [ ] 11. Actualizar método showGraphicsCategory() si es necesario
  - Revisar si el método `showGraphicsCategory()` necesita ajustes
  - Verificar que los gráficos se muestran/ocultan correctamente con los nuevos filtros
  - Asegurar que no hay referencias a los elementos eliminados
  - _Requirements: 5.2_


- [ ] 12. Verificar integración con métodos de gráficos
  - Revisar que `chequeComparativo()` recibe correctamente los parámetros `anioA` y `anioB`
  - Verificar que `comparativaIngresosDiarios()` funciona con los nuevos datos
  - Confirmar que `ventasPorDiaSemana()` se actualiza correctamente
  - Verificar que `topDiasSemana()` y `topChequePromedioSemanal()` funcionan

  - _Requirements: 5.2, 6.4_

- [ ] 13. Actualizar backend si es necesario
  - Revisar el archivo `ctrl-ingresos-dashboard.php`
  - Verificar que el método `apiPromediosDiarios` acepta parámetros `fi` y `ff`
  - Asegurar que el backend calcula correctamente las métricas con rangos de fechas personalizados

  - Implementar validación de fechas en el backend
  - Agregar manejo de errores para fechas inválidas
  - _Requirements: 4.4, 6.1, 6.2_

- [ ] 14. Probar funcionalidad básica
  - Cargar el dashboard y verificar que el DateRangePicker se muestra correctamente

  - Verificar que el rango predeterminado es los últimos 7 días
  - Probar cada una de las opciones predefinidas del DateRangePicker
  - Verificar que el select de años muestra los últimos 5 años
  - Confirmar que el año predeterminado es el año anterior
  - _Requirements: 1.1, 1.2, 1.4, 2.1, 2.3_


- [ ] 15. Probar actualización automática de gráficos
  - Seleccionar diferentes rangos de fechas y verificar que los gráficos se actualizan
  - Cambiar el año de comparación y verificar que los gráficos se actualizan
  - Cambiar la UDN y verificar que se mantienen los filtros de fecha
  - Verificar que no hay múltiples peticiones simultáneas
  - _Requirements: 5.1, 5.2, 5.3, 3.1_



- [ ] 16. Probar validaciones y manejo de errores
  - Intentar seleccionar un rango de fechas inválido (inicio después de fin)
  - Verificar que se muestran mensajes de error apropiados
  - Probar con UDN no seleccionada
  - Verificar comportamiento cuando el backend retorna error
  - _Requirements: 5.4, 6.1, 6.2_

- [ ] 17. Probar casos edge
  - Seleccionar un rango de fechas muy amplio (más de 1 año)
  - Seleccionar un rango de un solo día
  - Probar con fechas en el futuro
  - Verificar comportamiento al cambiar rápidamente entre filtros
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 18. Verificar consistencia visual
  - Confirmar que las etiquetas de años en los gráficos son correctas
  - Verificar que los colores de las series son consistentes (azul para período 1, verde para período 2)
  - Asegurar que los tooltips muestran información correcta
  - Verificar que las KPI cards se actualizan con los datos correctos
  - _Requirements: 6.4_

- [ ] 19. Optimizar rendimiento
  - Verificar que la carga inicial del dashboard es menor a 2 segundos
  - Confirmar que las actualizaciones de filtros son fluidas
  - Revisar la consola del navegador para errores o warnings
  - Verificar que no hay memory leaks al cambiar filtros repetidamente
  - _Requirements: 5.1, 5.2_

- [ ] 20. Documentar cambios
  - Agregar comentarios en el código explicando la lógica del DateRangePicker
  - Documentar el formato esperado de los parámetros en `renderDashboard()`
  - Actualizar cualquier documentación técnica existente
  - Crear notas de release si es necesario
  - _Requirements: 4.1, 4.2, 4.3_

## Notes

- **Orden de ejecución:** Las tareas deben ejecutarse en orden secuencial, especialmente las tareas 1-10 que modifican el código principal
- **Testing:** Las tareas 14-19 son de testing y pueden ejecutarse en paralelo una vez completadas las tareas de implementación
- **Backend:** La tarea 13 puede requerir coordinación con el equipo de backend si hay cambios necesarios en el controlador PHP
- **Rollback:** Mantener una copia de respaldo del archivo `dashboard.js` antes de comenzar las modificaciones
- **Compatibilidad:** Asegurar que los cambios no afectan otros dashboards que puedan usar componentes similares

## Success Criteria

La implementación se considerará exitosa cuando:

1. ✅ El DateRangePicker se muestra correctamente con todas las opciones predefinidas
2. ✅ El select de años muestra los últimos 5 años y funciona correctamente
3. ✅ Los gráficos se actualizan automáticamente al cambiar cualquier filtro
4. ✅ Las validaciones de datos funcionan y muestran mensajes de error apropiados
5. ✅ El rendimiento es aceptable (carga en menos de 2 segundos)
6. ✅ No hay errores en la consola del navegador
7. ✅ Todos los tests manuales pasan exitosamente
8. ✅ El código sigue las convenciones de CoffeeSoft
