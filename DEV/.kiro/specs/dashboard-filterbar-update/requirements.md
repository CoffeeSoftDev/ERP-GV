# Requirements Document

## Introduction

Este documento define los requisitos para actualizar la barra de filtros (filterBar) del Dashboard de Ventas en el módulo de Marketing KPI. El objetivo es reemplazar los inputs de fecha tipo `month` por un componente DateRangePicker más flexible y agregar un select para comparación de años, siguiendo la metodología implementada en el módulo de análisis de ventas.

## Glossary

- **Dashboard**: Panel de control visual que muestra métricas y gráficos de ventas
- **FilterBar**: Barra de filtros ubicada en la parte superior del dashboard que permite al usuario seleccionar criterios de consulta
- **DateRangePicker**: Componente de selección de rango de fechas con opciones predefinidas (última semana, mes actual, etc.)
- **UDN**: Unidad de Negocio (Unit of Business)
- **Período de Consulta**: Rango de fechas principal que el usuario desea analizar
- **Período de Comparación**: Rango de fechas secundario para comparar con el período principal
- **API Endpoint**: Controlador PHP que procesa las peticiones del frontend (`ctrl-ingresos-dashboard.php`)

## Requirements

### Requirement 1

**User Story:** Como usuario del dashboard de ventas, quiero seleccionar rangos de fechas flexibles usando un DateRangePicker, para poder analizar períodos personalizados más allá de meses completos.

#### Acceptance Criteria

1. WHEN el usuario accede al dashboard, THE Sistema SHALL mostrar un DateRangePicker en lugar del input tipo `month` para el período de consulta
2. WHEN el usuario abre el DateRangePicker, THE Sistema SHALL mostrar opciones predefinidas: "Última semana", "Últimas 2 semanas", "Últimas 3 semanas", "Últimas 4 semanas", "Mes Actual", "Mes Anterior", "Año actual", "Año anterior"
3. WHEN el usuario selecciona un rango de fechas, THE Sistema SHALL actualizar automáticamente los gráficos y métricas del dashboard
4. WHEN el dashboard se carga por primera vez, THE Sistema SHALL establecer como rango predeterminado los últimos 7 días (desde hace 7 días hasta ayer)

### Requirement 2

**User Story:** Como usuario del dashboard, quiero poder comparar el período seleccionado con años anteriores específicos, para identificar tendencias y patrones de crecimiento.

#### Acceptance Criteria

1. WHEN el usuario accede al dashboard, THE Sistema SHALL mostrar un select con años disponibles para comparación
2. WHEN el usuario selecciona un año en el select de comparación, THE Sistema SHALL actualizar los gráficos comparativos mostrando datos del mismo período pero del año seleccionado
3. WHEN el dashboard se carga por primera vez, THE Sistema SHALL establecer como año de comparación predeterminado el año anterior al actual
4. WHERE el select de comparación está presente, THE Sistema SHALL incluir al menos los últimos 5 años como opciones

### Requirement 3

**User Story:** Como usuario del dashboard, quiero que el sistema mantenga la funcionalidad de filtro por UDN, para poder analizar ventas por unidad de negocio específica.

#### Acceptance Criteria

1. WHEN el usuario cambia la UDN seleccionada, THE Sistema SHALL mantener los rangos de fechas actuales y actualizar solo los datos correspondientes a la nueva UDN
2. WHEN el dashboard se carga, THE Sistema SHALL mostrar el select de UDN en la primera posición del filterBar
3. WHEN el usuario selecciona una UDN, THE Sistema SHALL actualizar las categorías disponibles en los filtros secundarios

### Requirement 4

**User Story:** Como desarrollador del sistema, quiero que el código siga la arquitectura CoffeeSoft y las convenciones establecidas, para mantener la consistencia y facilitar el mantenimiento.

#### Acceptance Criteria

1. WHEN se implementa el DateRangePicker, THE Sistema SHALL utilizar la misma función `dataPicker()` del módulo de análisis de ventas
2. WHEN se envían datos al backend, THE Sistema SHALL enviar fechas en formato `YYYY-MM-DD` para inicio (fi) y fin (ff)
3. WHEN se actualiza el filterBar, THE Sistema SHALL mantener la estructura de `createfilterBar()` del framework CoffeeSoft
4. WHEN se realizan peticiones AJAX, THE Sistema SHALL utilizar la función `useFetch()` del framework

### Requirement 5

**User Story:** Como usuario del dashboard, quiero que los gráficos se actualicen automáticamente al cambiar los filtros, para obtener resultados inmediatos sin necesidad de botones adicionales.

#### Acceptance Criteria

1. WHEN el usuario selecciona un nuevo rango de fechas en el DateRangePicker, THE Sistema SHALL ejecutar automáticamente `dashboard.renderDashboard()`
2. WHEN el usuario cambia el año de comparación, THE Sistema SHALL actualizar todos los gráficos comparativos sin recargar la página
3. WHEN el usuario cambia la UDN, THE Sistema SHALL actualizar todos los componentes visuales manteniendo los filtros de fecha actuales
4. IF una petición AJAX está en proceso, THEN THE Sistema SHALL cancelar peticiones anteriores para evitar conflictos de datos

### Requirement 6

**User Story:** Como usuario del dashboard, quiero que el sistema maneje correctamente los datos cuando selecciono rangos de fechas personalizados, para obtener métricas precisas independientemente del período elegido.

#### Acceptance Criteria

1. WHEN el usuario selecciona un rango de fechas personalizado, THE Sistema SHALL enviar las fechas exactas al backend sin redondear a meses completos
2. WHEN el backend recibe las fechas, THE Sistema SHALL calcular las métricas basándose en el rango exacto proporcionado
3. WHEN se comparan dos períodos de diferente duración, THE Sistema SHALL mostrar advertencias visuales indicando que los períodos no son equivalentes
4. WHEN se muestran gráficos comparativos, THE Sistema SHALL etiquetar claramente cada período con sus fechas exactas
