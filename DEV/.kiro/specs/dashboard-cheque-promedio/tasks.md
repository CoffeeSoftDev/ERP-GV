# Implementation Plan

## Overview

Este plan de implementación detalla las tareas necesarias para desarrollar el **Dashboard de Cheque Promedio** siguiendo la arquitectura MVC de CoffeeSoft. Las tareas están organizadas en orden secuencial, construyendo incrementalmente desde la base de datos hasta la interfaz de usuario.

---

## Tasks

- [x] 1. Configurar estructura base del proyecto


  - Crear archivos principales siguiendo convenciones CoffeeSoft
  - Establecer rutas y conexiones entre capas MVC
  - _Requirements: 5.4, 5.5_

- [x] 1.1 Crear archivo modelo PHP


  - Crear `kpi/marketing/ventas/mdl/mdl-dashboard-cheque-promedio.php`
  - Extender clase CRUD y configurar propiedades `$bd` y `$util`
  - Incluir archivos de configuración `_CRUD.php` y `_Utileria.php`
  - _Requirements: 5.4_



- [ ] 1.2 Crear archivo controlador PHP
  - Crear `kpi/marketing/ventas/ctrl/ctrl-dashboard-cheque-promedio.php`
  - Extender clase del modelo y configurar headers CORS



  - Implementar estructura base con instanciación y llamada dinámica
  - _Requirements: 5.4_

- [ ] 1.3 Crear archivo JavaScript frontend
  - Crear `kpi/marketing/ventas/src/js/dashboard-cheque-promedio.js`
  - Crear clase `DashboardChequePromedio` extendiendo `Templates`
  - Configurar variable global `dashboardChequePromedio` y API endpoint
  - _Requirements: 5.4_

- [ ] 2. Implementar capa de modelo (mdl-dashboard-cheque-promedio.php)
  - Desarrollar métodos de consulta a base de datos usando clase CRUD
  - Implementar lógica de agregación y cálculos
  - _Requirements: 1.1, 1.2, 1.3, 3.1, 4.1_

- [ ] 2.1 Implementar consultas de filtros
  - Crear método `lsUDN()` para obtener lista de unidades de negocio
  - Crear método `lsCategoriasByUDN($array)` para categorías filtradas por UDN
  - _Requirements: 2.1, 2.4_

- [ ] 2.2 Implementar consultas de KPIs principales
  - Crear método `getVentasDia($array)` para ventas del día anterior
  - Crear método `getVentasMes($array)` para ventas del mes actual
  - Crear método `getClientesMes($array)` para total de clientes del mes
  - Crear método `getChequePromedioMes($array)` calculando total_venta / total_clientes
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2.3 Implementar consultas comparativas anuales
  - Crear método `listChequePromedioDashboard($array)` con datos del año actual y anterior
  - Incluir cálculo de variación porcentual entre períodos
  - Incluir determinación de tendencia (positiva/negativa/estable)
  - _Requirements: 1.3, 1.4, 1.5_

- [ ] 2.4 Implementar consultas de cheque promedio por categoría
  - Crear método `listChequePromedioByCategory($array)` con agrupación por categoría
  - Implementar lógica condicional para UDN tipo Hotel (Hospedaje, AyB, Diversos)
  - Implementar lógica condicional para UDN tipo Restaurante (Alimentos, Bebidas, etc.)
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [ ] 2.5 Implementar consultas de análisis semanal
  - Crear método `listVentasPorDiaSemana($array)` agrupando por DAYOFWEEK
  - Crear método `listTopDiasMes($array)` ordenando por ventas DESC con LIMIT 5
  - Crear método `listTopDiasSemanaPromedio($array)` con AVG por día de semana
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 2.6 Implementar consultas de tendencias diarias
  - Crear método `listIngresosDiariosComparativos($array)` con datos día por día
  - Incluir comparativa entre año actual y año anterior
  - Permitir filtrado opcional por categoría específica
  - _Requirements: 7.1, 7.2, 7.4_

- [ ] 3. Implementar capa de controlador (ctrl-dashboard-cheque-promedio.php)
  - Crear métodos API que procesen requests y llamen al modelo
  - Implementar validaciones de entrada y manejo de errores
  - _Requirements: 2.5, 6.2, 6.3_

- [ ] 3.1 Implementar método init()
  - Llamar a `lsUDN()` y `lsCategoriasByUDN()` del modelo
  - Retornar array con datos de filtros para inicialización del frontend
  - _Requirements: 2.1_

- [ ] 3.2 Implementar apiChequePromedioDashboard()
  - Validar parámetros requeridos (udn, anio, mes)
  - Validar rangos válidos (año 2020-2026, mes 1-12, UDN 1-5)
  - Llamar a métodos del modelo para obtener KPIs
  - Calcular variación porcentual y determinar tendencia
  - Retornar estructura con status 200 y data formateada
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 6.2_

- [ ] 3.3 Implementar apiChequePromedioByCategory()
  - Validar parámetros de entrada
  - Llamar a `listChequePromedioByCategory()` del modelo
  - Formatear respuesta con labels, datasets A y B, años
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 3.4 Implementar apiVentasPorDiaSemana()
  - Validar parámetros de entrada
  - Llamar a `listVentasPorDiaSemana()` del modelo
  - Formatear respuesta con labels de días, dataA, dataB, años
  - _Requirements: 4.1, 4.2_

- [ ] 3.5 Implementar apiTopDiasMes() y apiTopDiasSemanaPromedio()
  - Validar parámetros de entrada
  - Llamar a métodos correspondientes del modelo
  - Formatear respuesta con estructura para componente de ranking
  - _Requirements: 4.3, 4.4, 4.5_

- [ ] 3.6 Implementar apiComparativaIngresosDiarios()
  - Validar parámetros de entrada incluyendo categoría opcional
  - Llamar a `listIngresosDiariosComparativos()` del modelo
  - Formatear respuesta con labels, tooltip, datasets para gráfico lineal
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ] 3.7 Implementar funciones auxiliares del controlador
  - Crear función `formatChequePromedio($valor)` para formato monetario
  - Crear función `calculateVariacion($actual, $anterior)` para % de cambio
  - Crear función `getTendencia($variacion)` retornando 'positiva', 'negativa' o 'estable'
  - _Requirements: 1.4, 1.5_

- [ ] 4. Implementar estructura base del frontend (dashboard-cheque-promedio.js)
  - Crear layout principal y sistema de navegación
  - Integrar con módulo de Ventas existente
  - _Requirements: 5.1, 5.2, 5.3_

- [ ] 4.1 Configurar inicialización y variables globales
  - Declarar variable global `dashboardChequePromedio`
  - Configurar API endpoint apuntando a `ctrl-dashboard-cheque-promedio.php`
  - Inicializar instancia en bloque `$(async () => {...})`
  - _Requirements: 5.4_

- [ ] 4.2 Implementar constructor y método render()
  - Configurar constructor con `PROJECT_NAME = "dashboardChequePromedio"`
  - Implementar método `render()` llamando a `layout()`
  - _Requirements: 5.4_

- [ ] 4.3 Implementar método layout()
  - Crear estructura HTML usando `dashboardComponent()`
  - Definir contenedores para filterBar, KPI cards y gráficos
  - Configurar grid de 2 columnas para gráficos
  - _Requirements: 5.5_

- [ ] 4.4 Integrar pestaña en módulo de Ventas
  - Modificar `kpi-ventas.js` en clase `App.layout()`
  - Agregar objeto de pestaña en `tabLayout()` con id "dashboardChequePromedio"
  - Configurar onClick para llamar a `dashboardChequePromedio.renderDashboard()`
  - _Requirements: 5.1, 5.2_

- [ ] 5. Implementar barra de filtros y validaciones
  - Crear filterBar con selectores de UDN, mes y año
  - Implementar validaciones antes de consultas
  - _Requirements: 2.1, 2.2, 2.3, 2.5, 6.3_

- [ ] 5.1 Implementar método filterBarDashboard()
  - Usar `createfilterBar()` con selectores de UDN, mes y año
  - Configurar valores por defecto (mes actual, año actual)
  - Configurar onChange para llamar a `renderDashboard()`
  - _Requirements: 2.1, 2.2_

- [ ] 5.2 Implementar método validateFilters()
  - Validar que UDN esté seleccionado
  - Validar que período esté completo
  - Retornar true/false y mostrar error si falla
  - _Requirements: 2.5, 6.3_

- [ ] 5.3 Implementar método handleCategoryChange(udn)
  - Filtrar categorías disponibles según UDN seleccionado
  - Actualizar select de categorías dinámicamente
  - _Requirements: 2.4, 3.2, 3.3, 3.4_

- [ ] 6. Implementar renderizado de KPI cards
  - Crear método para mostrar 4 cards principales
  - Implementar cálculos de tendencias y colores
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 6.1 Implementar método showCards(data)
  - Validar que data no sea null
  - Usar componente `infoCard()` con theme "light"
  - Crear array json con 4 cards: ventaDia, ventaMes, Clientes, ChequePromedio
  - Incluir íconos emoji para cada card
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 6.2 Implementar métodos auxiliares de cards
  - Crear `parseNumericValue(value)` para convertir strings monetarios a números
  - Crear `getDateDescription()` retornando fecha de ayer en formato DD/MM/YYYY
  - Crear `getMonthDescription()` retornando mes actual en formato "Período: MMMM YYYY"
  - _Requirements: 1.1, 1.2_

- [ ] 6.3 Implementar lógica de tendencias
  - Crear `getChequePromedioTrend(valor, data)` con lógica de variación
  - Mostrar flechas ↗️ ↘️ → según porcentaje de cambio
  - Crear `getChequePromedioColor(valor, data)` retornando clases de color
  - Usar verde #8CC63F para positivo, rojo para negativo, azul #103B60 para estable
  - _Requirements: 1.4, 1.5_

- [ ] 7. Implementar método principal renderDashboard()
  - Orquestar carga de datos y renderizado de componentes
  - Implementar manejo de estados de carga y errores
  - _Requirements: 2.2, 6.1, 6.4_

- [ ] 7.1 Implementar validación y loading state
  - Llamar a `validateFilters()` al inicio
  - Llamar a `showLoadingState()` antes de fetch
  - Llamar a `handleCategoryChange()` para filtrar categorías
  - _Requirements: 2.5, 6.1, 6.3_

- [ ] 7.2 Implementar llamadas a API
  - Hacer fetch a `apiChequePromedioDashboard` con parámetros de filtros
  - Capturar respuesta y validar status 200
  - Implementar try-catch para manejo de errores
  - _Requirements: 6.2, 6.4_

- [ ] 7.3 Implementar renderizado de componentes
  - Llamar a `showCards(mkt.dashboard)` con datos de KPIs
  - Llamar a `renderCharts(mkt)` con datos de gráficos
  - _Requirements: 1.1, 1.2, 1.3, 3.1, 4.1, 7.1_

- [ ] 8. Implementar gráficos comparativos
  - Crear métodos para cada tipo de gráfico usando Chart.js
  - Aplicar paleta de colores corporativa
  - _Requirements: 3.1, 3.5, 4.1, 4.2, 7.1, 7.2, 7.5_

- [ ] 8.1 Implementar método chequeComparativo(options)
  - Crear gráfico de barras usando Chart.js tipo "bar"
  - Configurar datasets con año actual (#103B60) y año anterior (#8CC63F)
  - Mostrar valores formateados sobre cada barra
  - Configurar tooltip con formato de moneda
  - _Requirements: 3.1, 3.5_

- [ ] 8.2 Implementar método comparativaIngresosDiarios(options)
  - Usar componente `linearChart()` de CoffeeSoft
  - Configurar título dinámico con meses y años comparados
  - Pasar data con labels, datasets y tooltip
  - _Requirements: 7.1, 7.2, 7.5_

- [ ] 8.3 Implementar método ventasPorDiaSemana(data)
  - Usar componente `barChart()` de CoffeeSoft
  - Configurar labels con días de la semana en español
  - Configurar dataA (año actual) y dataB (año anterior)
  - _Requirements: 4.1, 4.2_

- [ ] 8.4 Implementar método topDiasSemana(options)
  - Crear lista visual con ranking de días
  - Usar colores diferenciados por posición (verde, azul, morado, naranja, gris)
  - Mostrar círculos numerados con posición
  - Incluir datos de día, fecha, clientes y total
  - _Requirements: 4.3, 4.4, 4.5_

- [ ] 8.5 Implementar método renderCharts(mkt)
  - Llamar a `chequeComparativo()` con mkt.barras
  - Llamar a `comparativaIngresosDiarios()` con mkt.linear
  - Llamar a `ventasPorDiaSemana()` con mkt.barDays
  - Llamar a `topDiasSemana()` con mkt.topWeek
  - _Requirements: 3.1, 4.1, 7.1_

- [ ] 9. Implementar estados de UI (loading y errores)
  - Crear métodos para feedback visual al usuario
  - Mejorar experiencia durante carga de datos
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 9.1 Implementar método showLoadingState()
  - Crear skeleton loader con 4 cards animadas
  - Usar clases Tailwind para animación pulse
  - Inyectar en contenedor `#cardDashboard`
  - _Requirements: 6.1_

- [ ] 9.2 Implementar método showError(message)
  - Crear mensaje de error con fondo rojo claro
  - Mostrar ícono de advertencia y mensaje descriptivo
  - Inyectar en contenedor `#cardDashboard`
  - _Requirements: 6.2_

- [ ] 10. Integrar y probar flujo completo
  - Verificar integración entre todas las capas
  - Validar funcionamiento end-to-end
  - _Requirements: 5.2, 5.3, 6.4_

- [ ] 10.1 Verificar inicialización del módulo
  - Confirmar que pestaña aparece en módulo de Ventas
  - Verificar que filtros se cargan correctamente
  - Validar que valores por defecto se establecen
  - _Requirements: 5.1, 5.2, 2.1_

- [ ] 10.2 Probar flujo de renderizado completo
  - Seleccionar diferentes UDN y verificar cambio de categorías
  - Cambiar períodos y verificar actualización de datos
  - Validar que todos los gráficos se renderizan correctamente
  - _Requirements: 2.2, 2.4, 6.4_

- [ ] 10.3 Validar manejo de errores
  - Probar con filtros incompletos y verificar mensaje de error
  - Simular error de API y verificar mensaje de error
  - Validar que loading state aparece durante carga
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 10.4 Verificar cálculos y comparativas
  - Validar que cheque promedio se calcula correctamente (total_venta / total_clientes)
  - Verificar que variaciones porcentuales son correctas
  - Confirmar que tendencias (positiva/negativa) se muestran adecuadamente
  - _Requirements: 1.2, 1.3, 1.4, 1.5_

- [ ]* 11. Optimización y refinamiento
  - Mejorar rendimiento y experiencia de usuario
  - Implementar mejoras opcionales
  - _Requirements: 6.4_

- [ ]* 11.1 Implementar caché de consultas
  - Agregar caché estático en controlador para consultas frecuentes
  - Usar cacheKey basado en "ingresos_{udn}_{year}_{mes}"
  - _Requirements: 6.4_

- [ ]* 11.2 Optimizar consultas SQL
  - Revisar índices en tabla softrestaurant_ventas
  - Agregar índice compuesto en (udn, fecha) si no existe
  - _Requirements: 6.4_

- [ ]* 11.3 Agregar tooltips informativos
  - Implementar método `getTooltipText(cardId)` con descripciones
  - Agregar íconos de ayuda en títulos de cards
  - _Requirements: 1.1, 1.2, 1.3_

- [ ]* 11.4 Implementar exportación de datos
  - Agregar botón para exportar datos a Excel/CSV
  - Implementar método en controlador para generar archivo
  - _Requirements: N/A (mejora opcional)_

---

## Notes

- **Orden de Implementación**: Las tareas deben ejecutarse en orden secuencial (1 → 2 → 3 → 4...) para garantizar que las dependencias estén disponibles.
- **Tareas Opcionales**: Las tareas marcadas con `*` son opcionales y pueden omitirse para un MVP funcional.
- **Testing**: Aunque no hay tareas específicas de testing, se recomienda probar cada método después de implementarlo.
- **Referencias**: Usar como referencia los archivos existentes `ctrl-ingresos.php` y `kpi-ventas.js` para mantener consistencia.
- **Componentes CoffeeSoft**: Consultar documentación de componentes (`infoCard`, `linearChart`, `barChart`, `createfilterBar`) antes de implementar.
