# Implementation Plan

- [x] 1. Crear estructura base del proyecto


  - Crear directorio `pedidos/reportes/` con subdirectorios `ctrl/`, `mdl/`, `src/js/`
  - Crear archivo `index.php` con contenedor root y imports de scripts
  - Establecer estructura MVC básica siguiendo convenciones CoffeeSoft
  - _Requirements: 6.5_


- [ ] 2. Implementar modelo de datos (MDL)
  - [ ] 2.1 Crear archivo `mdl-reportes.php` con clase base
    - Extender clase CRUD y configurar propiedades base ($bd, $util)
    - Implementar método `lsUDN()` para obtener unidades de negocio
    - Implementar método `lsCanales()` para obtener canales de comunicación

    - _Requirements: 1.1, 2.2_

  - [ ] 2.2 Implementar consultas para resumen de pedidos
    - Crear método `listPedidosByCanal()` con GROUP BY por mes y canal
    - Implementar cálculo de totales por fila y columna

    - Optimizar consulta con índices por fecha y UDN
    - _Requirements: 2.1, 2.3, 2.5_

  - [ ] 2.3 Implementar consultas para resumen de ventas
    - Crear método `listVentasByCanal()` con SUM de montos por canal

    - Implementar formato de moneda en consulta SQL
    - Agregar ordenamiento por monto total descendente
    - _Requirements: 3.1, 3.3, 3.4_

  - [x] 2.4 Implementar consultas para bitácora de ingresos

    - Crear método `listIngresosDiarios()` con filtros por fecha
    - Implementar método `createIngreso()` para nuevos registros
    - Agregar validación de duplicidad por fecha y canal
    - _Requirements: 4.1, 4.3, 4.4_

  - [x] 2.5 Implementar cálculo de KPIs

    - Crear método `getKPIData()` para indicadores principales
    - Calcular total pedidos, total ingresos y cheque promedio
    - Implementar cálculo de porcentaje de participación por canal
    - _Requirements: 5.1, 5.2_


- [ ] 3. Implementar controlador (CTRL)
  - [ ] 3.1 Crear archivo `ctrl-reportes.php` con clase base
    - Extender clase mdl y configurar métodos principales
    - Implementar método `init()` para datos iniciales de filtros
    - Configurar estructura de respuesta JSON estándar

    - _Requirements: 1.4, 6.5_

  - [ ] 3.2 Implementar endpoints para reportes
    - Crear método `lsResumenPedidos()` que procese filtros y retorne datos
    - Crear método `lsResumenVentas()` con formato de moneda

    - Crear método `lsBitacoraIngresos()` con paginación
    - _Requirements: 2.5, 3.5, 4.5_

  - [ ] 3.3 Implementar gestión de ingresos
    - Crear método `addIngreso()` para nuevos registros diarios
    - Implementar método `editIngreso()` para modificar registros

    - Agregar método `deleteIngreso()` con validaciones
    - _Requirements: 4.2, 4.4_

  - [ ] 3.4 Implementar endpoint para KPIs y gráficas
    - Crear método `getKPIDashboard()` que retorne indicadores

    - Implementar datos para gráficas comparativas año actual vs anterior
    - Configurar respuesta con estructura para Chart.js
    - _Requirements: 5.1, 5.3, 5.4_

- [x] 4. Implementar frontend JavaScript

  - [ ] 4.1 Crear archivo `reportes.js` con clase App principal
    - Extender Templates y configurar PROJECT_NAME como "Reportes"
    - Implementar constructor con propiedades para filtros actuales
    - Crear método `render()` que inicialice layout y filtros
    - _Requirements: 6.5_


  - [ ] 4.2 Implementar sistema de filtros
    - Crear método `filterBar()` con selects para UDN, año, mes y tipo
    - Implementar método `updateReports()` que recargue datos al cambiar filtros
    - Agregar método `changeReportType()` para cambiar entre vistas

    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [ ] 4.3 Implementar vista de resumen de pedidos
    - Crear método `renderResumenPedidos()` usando createTable()
    - Configurar columnas dinámicas basadas en canales disponibles
    - Implementar resaltado de fila actual y cálculo de totales

    - _Requirements: 2.1, 2.2, 2.3, 2.4_

  - [ ] 4.4 Implementar vista de resumen de ventas
    - Crear método `renderResumenVentas()` con formato monetario
    - Configurar ordenamiento por monto total

    - Agregar total general al final de la tabla
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ] 4.5 Implementar bitácora de ingresos
    - Crear método `renderBitacoraIngresos()` con tabla editable
    - Implementar método `addIngreso()` con formulario modal

    - Agregar funcionalidad de editar y eliminar registros
    - _Requirements: 4.1, 4.2, 4.4, 4.5_

- [ ] 5. Implementar dashboard de KPIs y gráficas
  - [x] 5.1 Crear componente de KPIs principales

    - Implementar método `renderKPIDashboard()` con cards de indicadores
    - Mostrar total pedidos, total ingresos y cheque promedio
    - Agregar cálculo y visualización de porcentaje por canal
    - _Requirements: 5.1, 5.2_

  - [x] 5.2 Implementar gráficas comparativas

    - Integrar Chart.js para gráficas de barras y líneas
    - Crear comparativas año actual vs anterior por canal
    - Implementar filtros por mes o rango para las gráficas
    - _Requirements: 5.3, 5.4, 5.5_



- [ ] 6. Aplicar diseño visual CoffeeSoft
  - [ ] 6.1 Implementar colores corporativos
    - Aplicar paleta de colores: #103B60, #8CC63F, grises corporativos
    - Configurar temas 'corporativo' en tablas y componentes
    - Agregar badges e íconos para canales y estados
    - _Requirements: 6.2, 6.3_

  - [ ] 6.2 Configurar layout y navegación
    - Implementar tabs para diferentes tipos de reportes
    - Crear layout responsivo con primaryLayout()
    - Configurar headerBar con título y navegación
    - _Requirements: 6.1, 6.4_

- [ ] 7. Integrar funcionalidades avanzadas
  - [ ] 7.1 Implementar formulario de nuevos ingresos
    - Crear método `jsonIngreso()` con campos para fecha, canal y monto
    - Implementar validaciones de duplicidad en frontend
    - Agregar selección de canal dinámica desde base de datos
    - _Requirements: 4.2, 4.3_

  - [ ] 7.2 Configurar actualización automática de reportes
    - Conectar todos los filtros con recarga automática de datos
    - Implementar persistencia de filtros durante la sesión
    - Agregar indicadores de carga durante las consultas
    - _Requirements: 1.4, 1.5_

- [ ]* 8. Optimizaciones de rendimiento
  - Implementar caché local por UDN, año y mes
  - Optimizar consultas SQL con índices apropiados
  - Agregar paginación para datasets grandes
  - _Requirements: 2.5, 3.5_

- [ ]* 9. Validaciones y manejo de errores
  - Implementar validaciones de duplicidad de registros diarios
  - Agregar manejo de errores en consultas complejas
  - Crear fallbacks para problemas de renderizado de gráficas
  - _Requirements: 4.3_