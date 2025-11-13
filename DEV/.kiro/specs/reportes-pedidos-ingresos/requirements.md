# Requirements Document

## Introduction

Este documento especifica los requerimientos para el módulo **Reportes de Pedidos e Ingresos** dentro del sistema CoffeeSoft ERP. El módulo permitirá analizar el rendimiento de canales de venta y los ingresos generados por unidad de negocio, con vistas resumidas mensuales y anuales.

## Glossary

- **UDN**: Unidad de Negocio que agrupa las operaciones comerciales
- **Canal_Comunicacion**: Medio por el cual se reciben los pedidos (WhatsApp, Facebook, Llamada, etc.)
- **Resumen_Pedidos**: Reporte que muestra cantidad de órdenes por canal y período
- **Resumen_Ventas**: Reporte que muestra montos monetarios por canal y período
- **Bitacora_Ingresos**: Registro diario de ingresos por canal
- **KPI_Dashboard**: Indicadores clave de rendimiento del negocio
- **Filter_System**: Sistema de filtros por UDN, año, mes y tipo de reporte

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero filtrar los reportes por UDN, año, mes y tipo de reporte, para poder analizar información específica de períodos y unidades de negocio.

#### Acceptance Criteria

1. THE Filter_System SHALL mostrar una lista desplegable de UDN obtenida dinámicamente de la base de datos
2. THE Filter_System SHALL incluir selectores de año y mes para segmentar información por períodos específicos
3. THE Filter_System SHALL permitir seleccionar entre "Resumen de pedidos", "Resumen de ventas" y "Bitácora de ingresos"
4. WHEN el usuario cambia cualquier filtro, THE Filter_System SHALL actualizar automáticamente los datos mostrados
5. THE Filter_System SHALL mantener la selección del usuario durante la sesión

### Requirement 2

**User Story:** Como usuario del sistema, quiero ver un resumen de pedidos por canal de comunicación, para analizar qué canales generan más órdenes.

#### Acceptance Criteria

1. THE Resumen_Pedidos SHALL mostrar una tabla con columnas por cada Canal_Comunicacion disponible
2. THE Resumen_Pedidos SHALL incluir columnas para Mes, Llamada, WhatsApp, Facebook, Meep, Ecommerce, Uber, Otro y Total
3. THE Resumen_Pedidos SHALL calcular automáticamente el total sumando los valores por canal
4. THE Resumen_Pedidos SHALL resaltar visualmente la fila del mes actual o seleccionado
5. THE Resumen_Pedidos SHALL cargar datos dinámicamente desde el backend vía useFetch()

### Requirement 3

**User Story:** Como usuario del sistema, quiero ver un resumen de ventas por canal de comunicación, para analizar qué canales generan más ingresos monetarios.

#### Acceptance Criteria

1. THE Resumen_Ventas SHALL mostrar montos monetarios correspondientes a los pedidos de cada canal
2. THE Resumen_Ventas SHALL incluir formato de moneda con símbolo $, separadores y dos decimales
3. THE Resumen_Ventas SHALL permitir ordenar por monto total de mayor a menor
4. THE Resumen_Ventas SHALL incluir total general al final del reporte
5. THE Resumen_Ventas SHALL usar las mismas columnas que Resumen_Pedidos pero con valores monetarios

### Requirement 4

**User Story:** Como usuario del sistema, quiero ver una bitácora de ingresos diarios, para registrar y consultar los ingresos por fecha y canal.

#### Acceptance Criteria

1. THE Bitacora_Ingresos SHALL mostrar registros diarios de ingresos por canal
2. THE Bitacora_Ingresos SHALL incluir funcionalidad para agregar nuevos ingresos
3. THE Bitacora_Ingresos SHALL validar duplicidad de registros para la misma fecha y canal
4. THE Bitacora_Ingresos SHALL permitir editar y eliminar registros existentes
5. THE Bitacora_Ingresos SHALL mostrar totales por día y por canal

### Requirement 5

**User Story:** Como usuario del sistema, quiero ver KPIs y gráficas comparativas, para analizar el rendimiento general del negocio.

#### Acceptance Criteria

1. THE KPI_Dashboard SHALL mostrar total de pedidos al año, total de ingresos al año y cheque promedio
2. THE KPI_Dashboard SHALL calcular porcentaje de participación por canal
3. THE KPI_Dashboard SHALL incluir gráficas comparativas entre año actual vs anterior
4. THE KPI_Dashboard SHALL mostrar barras o líneas por canal en las gráficas
5. THE KPI_Dashboard SHALL permitir filtrar gráficas por mes o rango de fechas

### Requirement 6

**User Story:** Como usuario del sistema, quiero que el módulo mantenga la consistencia visual con CoffeeSoft ERP, para tener una experiencia de usuario coherente.

#### Acceptance Criteria

1. THE Filter_System SHALL usar los mismos estilos de filtros existentes en CoffeeSoft
2. THE Resumen_Pedidos SHALL usar colores corporativos: azul oscuro #103B60, verde #8CC63F, gris claro
3. THE Resumen_Ventas SHALL incluir badges o íconos para estatus y canales activos
4. THE KPI_Dashboard SHALL usar componentes reutilizables de CoffeeSoft (createTable, createForm)
5. WHERE se implementen nuevas funcionalidades, THE Filter_System SHALL mantener la arquitectura MVC del framework