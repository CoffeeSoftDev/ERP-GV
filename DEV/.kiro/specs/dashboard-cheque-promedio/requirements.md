# Requirements Document

## Introduction

El **Dashboard de Cheque Promedio** es un módulo analítico que se integrará al sistema de Ventas de CoffeeSoft ERP. Su propósito es proporcionar métricas detalladas sobre el comportamiento del cheque promedio, ventas y clientes, permitiendo comparativas anuales y análisis por categorías para facilitar la toma de decisiones estratégicas.

## Glossary

- **Dashboard**: Interfaz visual que presenta métricas clave de negocio en tiempo real
- **Cheque Promedio**: Valor promedio de consumo calculado como total_venta / total_clientes
- **UDN (Unidad de Negocio)**: Entidad organizacional que representa una sucursal o división operativa
- **KPI (Key Performance Indicator)**: Indicador clave de rendimiento que mide el desempeño de un proceso
- **Sistema CoffeeSoft**: Framework ERP que gestiona operaciones de ventas, inventario y análisis
- **Comparativa Anual**: Análisis que contrasta métricas del período actual contra el mismo período del año anterior
- **Categoría**: Clasificación de productos o servicios (Hospedaje, AyB, Alimentos, Bebidas, etc.)
- **Período**: Rango temporal definido por año y mes para análisis de datos

## Requirements

### Requirement 1

**User Story:** Como gerente de ventas, quiero visualizar el cheque promedio del mes actual comparado con el año anterior, para evaluar el desempeño de mi unidad de negocio.

#### Acceptance Criteria

1. WHEN el usuario accede al dashboard, THE Sistema CoffeeSoft SHALL mostrar cuatro KPI cards con ventas del día anterior, ventas del mes, total de clientes y cheque promedio
2. WHEN el usuario selecciona una UDN y período, THE Sistema CoffeeSoft SHALL calcular el cheque promedio dividiendo el total de ventas entre el total de clientes del período
3. WHEN el cheque promedio es calculado, THE Sistema CoffeeSoft SHALL mostrar el porcentaje de variación comparado con el mismo mes del año anterior
4. WHERE el cheque promedio aumenta más del 5%, THE Sistema CoffeeSoft SHALL mostrar un indicador visual positivo en color verde
5. WHERE el cheque promedio disminuye más del 5%, THE Sistema CoffeeSoft SHALL mostrar un indicador visual negativo en color rojo

### Requirement 2

**User Story:** Como analista de datos, quiero filtrar las métricas por UDN, mes y año, para analizar el comportamiento de diferentes períodos y sucursales.

#### Acceptance Criteria

1. THE Sistema CoffeeSoft SHALL proporcionar un filterBar con selectores de UDN, mes y año
2. WHEN el usuario cambia cualquier filtro, THE Sistema CoffeeSoft SHALL actualizar automáticamente todos los gráficos y KPIs del dashboard
3. THE Sistema CoffeeSoft SHALL cargar por defecto el mes actual y el año actual en los filtros
4. WHEN el usuario selecciona una UDN, THE Sistema CoffeeSoft SHALL filtrar las categorías disponibles según la configuración de esa unidad de negocio
5. THE Sistema CoffeeSoft SHALL validar que todos los filtros estén seleccionados antes de ejecutar consultas al backend

### Requirement 3

**User Story:** Como director comercial, quiero ver gráficos comparativos de cheque promedio por categoría, para identificar qué líneas de producto tienen mejor desempeño.

#### Acceptance Criteria

1. THE Sistema CoffeeSoft SHALL mostrar un gráfico de barras comparando el cheque promedio por categoría entre dos años
2. WHEN la UDN es tipo Hotel (id=1), THE Sistema CoffeeSoft SHALL mostrar categorías Hospedaje, AyB y Diversos
3. WHEN la UDN es tipo Restaurante (id=5), THE Sistema CoffeeSoft SHALL mostrar categorías Alimentos, Bebidas, Guarniciones, Sales y Domicilio
4. WHEN la UDN es tipo mixto, THE Sistema CoffeeSoft SHALL mostrar categorías Alimentos y Bebidas
5. THE Sistema CoffeeSoft SHALL etiquetar cada barra con el valor formateado en moneda

### Requirement 4

**User Story:** Como gerente operativo, quiero analizar las ventas por día de la semana, para optimizar la asignación de recursos en días de mayor demanda.

#### Acceptance Criteria

1. THE Sistema CoffeeSoft SHALL generar un gráfico de barras con ventas promedio agrupadas por día de la semana (Lunes a Domingo)
2. THE Sistema CoffeeSoft SHALL comparar las ventas de cada día de la semana entre el año actual y el año anterior
3. THE Sistema CoffeeSoft SHALL mostrar un ranking de días con mejor promedio de ventas en el mes seleccionado
4. WHEN el usuario visualiza el ranking, THE Sistema CoffeeSoft SHALL incluir el número de ocurrencias y total de clientes por día
5. THE Sistema CoffeeSoft SHALL resaltar visualmente el día con mejor desempeño con un indicador especial

### Requirement 5

**User Story:** Como administrador del sistema, quiero que el dashboard se integre como una pestaña adicional en el módulo de Ventas, para mantener la consistencia de navegación.

#### Acceptance Criteria

1. THE Sistema CoffeeSoft SHALL agregar una pestaña "Dashboard Cheque Promedio" en el módulo de Ventas
2. WHEN el usuario hace clic en la pestaña, THE Sistema CoffeeSoft SHALL renderizar el dashboard sin recargar la página completa
3. THE Sistema CoffeeSoft SHALL mantener el estado de los filtros al cambiar entre pestañas del módulo
4. THE Sistema CoffeeSoft SHALL seguir la arquitectura MVC con archivos separados: JS frontend, controlador PHP y modelo PHP
5. THE Sistema CoffeeSoft SHALL usar componentes reutilizables de la librería CoffeeSoft (createTable, infoCard, linearChart, barChart)

### Requirement 6

**User Story:** Como usuario del sistema, quiero que el dashboard cargue rápidamente y muestre estados de carga, para tener una experiencia fluida.

#### Acceptance Criteria

1. WHEN el dashboard inicia la carga de datos, THE Sistema CoffeeSoft SHALL mostrar un skeleton loader en las KPI cards
2. IF la consulta al backend falla, THEN THE Sistema CoffeeSoft SHALL mostrar un mensaje de error descriptivo
3. THE Sistema CoffeeSoft SHALL validar que todos los filtros requeridos estén completos antes de realizar peticiones
4. WHEN los datos son obtenidos exitosamente, THE Sistema CoffeeSoft SHALL renderizar todos los componentes visuales en menos de 2 segundos
5. THE Sistema CoffeeSoft SHALL implementar caché de consultas frecuentes para mejorar el rendimiento

### Requirement 7

**User Story:** Como analista financiero, quiero ver tendencias de ventas diarias en un gráfico lineal, para identificar patrones de comportamiento en el mes.

#### Acceptance Criteria

1. THE Sistema CoffeeSoft SHALL generar un gráfico lineal con ventas diarias del mes seleccionado
2. THE Sistema CoffeeSoft SHALL superponer dos líneas: una para el año actual y otra para el año anterior
3. WHEN el usuario pasa el cursor sobre un punto, THE Sistema CoffeeSoft SHALL mostrar un tooltip con fecha completa, día de la semana y valor de venta
4. THE Sistema CoffeeSoft SHALL permitir filtrar el gráfico por categoría específica (Alimentos, Bebidas, etc.)
5. THE Sistema CoffeeSoft SHALL usar colores corporativos CoffeeSoft: azul #103B60 para año actual y verde #8CC63F para año anterior
