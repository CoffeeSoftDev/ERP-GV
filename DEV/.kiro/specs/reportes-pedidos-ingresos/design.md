# Design Document

## Overview

Este documento describe el diseño técnico para el módulo **Reportes de Pedidos e Ingresos** dentro del sistema CoffeeSoft ERP. El módulo seguirá la arquitectura MVC del framework, implementando controladores, modelos y vistas JavaScript para analizar el rendimiento de canales de venta e ingresos por unidad de negocio.

## Architecture

### Project Structure
```
pedidos/reportes/
├── index.php                    # Punto de entrada principal
├── ctrl/
│   └── ctrl-reportes.php       # Controlador principal
├── mdl/
│   └── mdl-reportes.php        # Modelo de datos
└── src/js/
    └── reportes.js             # Frontend JavaScript
```

### Class Structure
```
App (Clase principal)
├── render() - Layout principal y tabs
├── filterBar() - Filtros principales (UDN, Año, Mes, Tipo)
├── renderResumenPedidos() - Reporte de cantidad de pedidos
├── renderResumenVentas() - Reporte de montos de ventas
├── renderBitacoraIngresos() - Bitácora diaria de ingresos
├── renderKPIDashboard() - Indicadores y gráficas
└── jsonIngreso() - Formulario para nuevos ingresos
```

## Components and Interfaces

### 1. Filter System Component

**Ubicación:** Barra superior del módulo
**Funcionalidad:** Filtros dinámicos para segmentar datos

```javascript
filterBar() {
    this.createfilterBar({
        parent: `filterBar${this.PROJECT_NAME}`,
        data: [
            {
                opc: "select",
                id: "udn",
                lbl: "Unidad de Negocio",
                data: lsUDN,
                onchange: `app.updateReports()`
            },
            {
                opc: "select", 
                id: "año",
                lbl: "Año",
                data: years,
                onchange: `app.updateReports()`
            },
            {
                opc: "select",
                id: "mes", 
                lbl: "Mes",
                data: months,
                onchange: `app.updateReports()`
            },
            {
                opc: "select",
                id: "tipoReporte",
                lbl: "Tipo de Reporte",
                data: reportTypes,
                onchange: `app.changeReportType()`
            }
        ]
    });
}
```

### 2. Reports Table Component

**Implementación:** Tablas dinámicas con scroll y formato monetario
**Características:**
- Columnas dinámicas basadas en canales de comunicación
- Cálculo automático de totales
- Formato de moneda para reportes de ventas
- Resaltado de filas actuales

```javascript
renderResumenPedidos() {
    this.createTable({
        parent: `container${this.PROJECT_NAME}`,
        idFilterBar: `filterBar${this.PROJECT_NAME}`,
        data: { opc: "lsResumenPedidos" },
        coffeesoft: true,
        conf: { datatable: true, pag: 12 },
        attr: {
            id: "tbResumenPedidos",
            theme: 'corporativo',
            title: '📊 Resumen de Pedidos por Canal',
            center: [1,2,3,4,5,6,7,8],
            right: [8]
        }
    });
}
```

### 3. KPI Dashboard Component

**Funcionalidad:** Indicadores clave y gráficas comparativas
**Elementos:**
- Cards con KPIs principales
- Gráficas de barras/líneas
- Comparativas año actual vs anterior

```javascript
renderKPIDashboard() {
    this.infoCard({
        parent: "kpiContainer",
        theme: "light",
        json: [
            {
                title: "Total Pedidos Año",
                data: { value: kpiData.totalPedidos, color: "text-[#103B60]" }
            },
            {
                title: "Total Ingresos Año", 
                data: { value: kpiData.totalIngresos, color: "text-[#8CC63F]" }
            },
            {
                title: "Cheque Promedio",
                data: { value: kpiData.chequePromedio, color: "text-blue-600" }
            }
        ]
    });
}
```

## Data Models

### Database Schema (Based on Requirements)
```sql
-- Tabla principal de pedidos
pedidos_orders (
    id INT PRIMARY KEY,
    udn_id INT,
    canal_comunicacion VARCHAR(50),
    fecha_pedido DATE,
    monto_total DECIMAL(10,2),
    estado VARCHAR(20),
    created_at TIMESTAMP
)

-- Tabla de ingresos diarios
pedidos_ingresos_diarios (
    id INT PRIMARY KEY,
    udn_id INT,
    canal_comunicacion VARCHAR(50),
    fecha DATE,
    monto DECIMAL(10,2),
    cantidad_pedidos INT,
    created_at TIMESTAMP
)

-- Tabla de canales de comunicación
pedidos_canales (
    id INT PRIMARY KEY,
    nombre VARCHAR(50),
    icono VARCHAR(50),
    color VARCHAR(7),
    active TINYINT(1)
)
```

### API Response Structure
```json
{
    "status": 200,
    "message": "Datos obtenidos correctamente",
    "data": {
        "resumenPedidos": [
            {
                "mes": "Enero",
                "llamada": 45,
                "whatsapp": 120,
                "facebook": 30,
                "meep": 15,
                "ecommerce": 80,
                "uber": 25,
                "otro": 10,
                "total": 325
            }
        ],
        "kpis": {
            "totalPedidos": 3250,
            "totalIngresos": 125000.50,
            "chequePromedio": 38.46
        }
    }
}
```

## Error Handling

### 1. Data Loading Errors
- **Escenario:** Error al cargar datos de reportes
- **Manejo:** Mostrar mensaje de error y tabla vacía
- **Fallback:** Reintentar carga automáticamente

### 2. Filter Validation
- **Escenario:** Selección de filtros inválidos
- **Manejo:** Validar rangos de fechas y UDN existentes
- **Logging:** Registrar errores de validación

### 3. Chart Rendering Issues
- **Escenario:** Problemas al renderizar gráficas
- **Manejo:** Mostrar mensaje alternativo con datos tabulares
- **Compatibility:** Fallbacks para navegadores antiguos

## Testing Strategy

### 1. Unit Testing (Opcional)
- Pruebas de cálculos de totales y promedios
- Validación de filtros y rangos de fechas
- Verificación de formato de moneda

### 2. Integration Testing
- Interacción entre filtros y reportes
- Sincronización de datos entre diferentes vistas
- Compatibilidad con API backend

### 3. Performance Testing
- Carga de grandes volúmenes de datos
- Tiempo de respuesta de consultas complejas
- Optimización de renderizado de tablas

## Implementation Details

### Backend Architecture (PHP)

#### Controller Methods
```php
class ctrl extends mdl {
    function init() {
        return [
            'udn' => $this->lsUDN(),
            'canales' => $this->lsCanales(),
            'años' => $this->lsAños()
        ];
    }
    
    function lsResumenPedidos() {
        // Lógica para resumen de pedidos por canal
    }
    
    function lsResumenVentas() {
        // Lógica para resumen de ventas por canal  
    }
    
    function lsBitacoraIngresos() {
        // Lógica para bitácora diaria
    }
    
    function addIngreso() {
        // Agregar nuevo ingreso diario
    }
}
```

#### Model Methods
```php
class mdl extends CRUD {
    function listPedidosByCanal($filters) {
        // Consulta agrupada por canal y período
    }
    
    function listVentasByCanal($filters) {
        // Consulta de montos por canal y período
    }
    
    function listIngresosDiarios($filters) {
        // Consulta de ingresos diarios
    }
    
    function createIngreso($data) {
        // Insertar nuevo ingreso
    }
    
    function getKPIData($filters) {
        // Calcular KPIs principales
    }
}
```

### Frontend Architecture (JavaScript)

#### Main Class Structure
```javascript
class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Reportes";
        this.currentReportType = "pedidos";
    }
    
    render() {
        this.layout();
        this.filterBar();
        this.renderCurrentReport();
    }
    
    changeReportType() {
        const tipo = $('#tipoReporte').val();
        this.currentReportType = tipo;
        this.renderCurrentReport();
    }
    
    renderCurrentReport() {
        switch(this.currentReportType) {
            case 'pedidos':
                this.renderResumenPedidos();
                break;
            case 'ventas':
                this.renderResumenVentas();
                break;
            case 'bitacora':
                this.renderBitacoraIngresos();
                break;
        }
    }
}
```

## Security Considerations

### Input Validation
- Validación de rangos de fechas válidos
- Sanitización de parámetros de filtros
- Verificación de permisos por UDN

### Data Integrity
- Validación de duplicidad en ingresos diarios
- Verificación de consistencia en totales
- Auditoría de cambios en registros

### Performance Optimization
- Índices en tablas por fecha y UDN
- Caché de consultas frecuentes
- Paginación para grandes datasets

## Visual Design Guidelines

### Color Scheme (CoffeeSoft Corporate)
- **Primary Blue:** #103B60 (Headers, titles)
- **Action Green:** #8CC63F (Success states, totals)
- **Light Gray:** #F8F9FA (Backgrounds)
- **Text Gray:** #6C757D (Secondary text)

### Component Styling
- Tables: Striped rows, hover effects
- Cards: Rounded corners, subtle shadows
- Buttons: Corporate colors, consistent sizing
- Charts: Matching color palette, clear legends