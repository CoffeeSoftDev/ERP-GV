# Design Document

## Overview

Este documento describe el diseño técnico para actualizar el filterBar del Dashboard de Ventas en el módulo KPI de Marketing. La solución implementará un DateRangePicker flexible y un select de comparación de años, siguiendo los patrones establecidos en el módulo de análisis de ventas (`kpi/src/js/analisis-de-ventas.js`).

## Architecture

### Componentes Principales

```
┌─────────────────────────────────────────────────────────┐
│                    Dashboard Layout                      │
│  ┌───────────────────────────────────────────────────┐  │
│  │              FilterBar Component                   │  │
│  │  ┌──────────┐ ┌──────────────┐ ┌──────────────┐  │  │
│  │  │   UDN    │ │ DateRangePicker│ │  Year Select │  │  │
│  │  │  Select  │ │  (Período 1)   │ │ (Comparación)│  │  │
│  │  └──────────┘ └──────────────┘ └──────────────┘  │  │
│  └───────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────┐  │
│  │                  KPI Cards                         │  │
│  └───────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────┐  │
│  │              Charts & Graphics                     │  │
│  │  • Cheque Promedio  • Ventas Diarias              │  │
│  │  • Comparativas     • Rankings                     │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Flujo de Datos

```
Usuario selecciona filtros
        ↓
DateRangePicker.onSelect() / Select.onChange()
        ↓
dashboard.renderDashboard()
        ↓
Extrae valores: { udn, fi, ff, yearComparison }
        ↓
useFetch() → ctrl-ingresos-dashboard.php
        ↓
Backend procesa: apiPromediosDiarios
        ↓
Retorna: { dashboard, barras, linear, barDays, topWeek }
        ↓
Actualiza componentes visuales:
  • showCards()
  • chequeComparativo()
  • comparativaIngresosDiarios()
  • ventasPorDiaSemana()
  • topDiasSemana()
```

## Components and Interfaces

### 1. FilterBar Component

**Ubicación:** `kpi/marketing/ventas/src/js/dashboard.js` → método `filterBar()`

**Estructura Actual:**
```javascript
filterBar() {
    this.createfilterBar({
        parent: `filterBar`,
        data: [
            { opc: "select", id: "udn", ... },
            { opc: "div", id: "containerPeriodo1", html: '<input type="month" .../>' },
            { opc: "div", id: "containerPeriodo2", html: '<input type="month" .../>' }
        ]
    });
}
```

**Estructura Nueva:**
```javascript
filterBar() {
    this.createfilterBar({
        parent: `filterBar`,
        data: [
            {
                opc: "select",
                id: "udn",
                lbl: "UDN",
                class: "col-sm-3",
                data: lsudn,
                onchange: `dashboard.renderDashboard()`
            },
            {
                opc: "input-calendar",
                id: "dateRangePicker",
                lbl: "Período de consulta",
                class: "col-sm-4"
            },
            {
                opc: "select",
                id: "yearComparison",
                lbl: "Comparar con año",
                class: "col-sm-3",
                data: this.generateYearOptions(),
                onchange: `dashboard.renderDashboard()`
            }
        ]
    });
    
    // Inicializar DateRangePicker
    this.initDateRangePicker();
}
```

### 2. DateRangePicker Initialization

**Método nuevo:** `initDateRangePicker()`

```javascript
initDateRangePicker() {
    dataPicker({
        parent: "dateRangePicker",
        type: "all",
        startDate: moment().subtract(7, "days"),
        endDate: moment().subtract(1, "days"),
        ranges: {
            "Última semana": [
                moment().subtract(7, "days"), 
                moment().subtract(1, "days")
            ],
            "Últimas 2 semanas": [
                moment().subtract(14, "days"), 
                moment().subtract(1, "days")
            ],
            "Últimas 3 semanas": [
                moment().subtract(21, "days"), 
                moment().subtract(1, "days")
            ],
            "Últimas 4 semanas": [
                moment().subtract(28, "days"), 
                moment().subtract(1, "days")
            ],
            "Mes Actual": [
                moment().startOf("month"), 
                moment().subtract(1, "days")
            ],
            "Mes Anterior": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month")
            ],
            "Año actual": [
                moment().startOf("year"), 
                moment().subtract(1, "days")
            ],
            "Año anterior": [
                moment().subtract(1, "year").startOf("year"),
                moment().subtract(1, "year").endOf("year")
            ]
        },
        onSelect: (start, end) => {
            this.renderDashboard();
        }
    });
}
```

### 3. Year Options Generator

**Método nuevo:** `generateYearOptions()`

```javascript
generateYearOptions() {
    const currentYear = moment().year();
    const years = [];
    
    for (let i = 0; i < 5; i++) {
        const year = currentYear - i;
        years.push({
            id: year,
            valor: year.toString()
        });
    }
    
    return years;
}
```

### 4. Updated renderDashboard Method

**Modificaciones en:** `renderDashboard()`

```javascript
async renderDashboard() {
    // Obtener valores de filtros
    const udn = $('#filterBar #udn').val();
    const dateRange = getDataRangePicker('dateRangePicker');
    const yearComparison = $('#filterBar #yearComparison').val();
    
    // Extraer año y mes del período seleccionado
    const startDate = moment(dateRange.fi);
    const anio1 = startDate.year();
    const mes1 = startDate.month() + 1;
    
    // Usar el año de comparación seleccionado
    const anio2 = parseInt(yearComparison);
    const mes2 = mes1; // Mismo mes pero del año de comparación
    
    // Petición al backend
    let mkt = await useFetch({
        url: api_dashboard,
        data: {
            opc: "apiPromediosDiarios",
            udn: udn,
            fi: dateRange.fi,
            ff: dateRange.ff,
            anio1: anio1,
            mes1: mes1,
            anio2: anio2,
            mes2: mes2
        }
    });
    
    // Actualizar componentes visuales
    this.showCards(mkt.dashboard);
    this.chequeComparativo({
        data: mkt.barras.dataset,
        anioA: anio2,
        anioB: anio1
    });
    // ... resto de actualizaciones
}
```

## Data Models

### Request Payload (Frontend → Backend)

```javascript
{
    opc: "apiPromediosDiarios",
    udn: "1",                    // ID de la unidad de negocio
    fi: "2025-01-01",           // Fecha inicio del rango
    ff: "2025-01-31",           // Fecha fin del rango
    anio1: 2025,                // Año del período de consulta
    mes1: 1,                    // Mes del período de consulta
    anio2: 2024,                // Año de comparación
    mes2: 1                     // Mes de comparación
}
```

### Response Payload (Backend → Frontend)

```javascript
{
    dashboard: {
        ventaDia: {
            titulo: "Venta del día de ayer",
            valor: "$15,234.50",
            fecha: "14 de enero, 2025",
            color: "text-green-600"
        },
        ventaMes: { ... },
        Clientes: { ... },
        ChequePromedio: { ... }
    },
    barras: {
        dataset: {
            labels: ["A&B", "Alimentos", "Bebidas"],
            A: [5000, 3200, 1800],
            B: [4700, 3000, 1600]
        },
        anioA: 2025,
        anioB: 2024
    },
    linear: {
        labels: ["Sem 1", "Sem 2", "Sem 3", "Sem 4"],
        datasets: [
            {
                label: "2025",
                data: [12000, 15000, 13500, 16000],
                borderColor: "#103B60"
            },
            {
                label: "2024",
                data: [11000, 14000, 12500, 15000],
                borderColor: "#8CC63F"
            }
        ]
    },
    barDays: { ... },
    topWeek: [ ... ]
}
```

## Error Handling

### Frontend Validation

```javascript
async renderDashboard() {
    try {
        // Validar que existan valores en los filtros
        const udn = $('#filterBar #udn').val();
        if (!udn) {
            console.warn('UDN no seleccionada');
            return;
        }
        
        const dateRange = getDataRangePicker('dateRangePicker');
        if (!dateRange || !dateRange.fi || !dateRange.ff) {
            console.error('Rango de fechas inválido');
            alert({
                icon: "error",
                text: "Por favor selecciona un rango de fechas válido"
            });
            return;
        }
        
        // Validar que la fecha de inicio sea menor que la fecha de fin
        if (moment(dateRange.fi).isAfter(moment(dateRange.ff))) {
            alert({
                icon: "error",
                text: "La fecha de inicio debe ser anterior a la fecha de fin"
            });
            return;
        }
        
        // Realizar petición
        let mkt = await useFetch({
            url: api_dashboard,
            data: { ... }
        });
        
        // Validar respuesta
        if (!mkt || !mkt.dashboard) {
            throw new Error('Respuesta inválida del servidor');
        }
        
        // Actualizar componentes
        this.showCards(mkt.dashboard);
        // ...
        
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
        alert({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los datos del dashboard. Por favor intenta nuevamente."
        });
    }
}
```

### Backend Error Handling

El backend (`ctrl-ingresos-dashboard.php`) debe validar:

1. Que las fechas estén en formato válido
2. Que el rango de fechas no exceda límites razonables (ej: máximo 1 año)
3. Que la UDN exista en la base de datos
4. Que los años de comparación sean válidos

```php
function apiPromediosDiarios() {
    // Validar fechas
    if (empty($_POST['fi']) || empty($_POST['ff'])) {
        return [
            'status' => 400,
            'message' => 'Fechas requeridas',
            'dashboard' => null
        ];
    }
    
    // Validar formato de fechas
    $fi = DateTime::createFromFormat('Y-m-d', $_POST['fi']);
    $ff = DateTime::createFromFormat('Y-m-d', $_POST['ff']);
    
    if (!$fi || !$ff) {
        return [
            'status' => 400,
            'message' => 'Formato de fecha inválido',
            'dashboard' => null
        ];
    }
    
    // Continuar con la lógica...
}
```

## Testing Strategy

### Unit Tests

1. **Test DateRangePicker Initialization**
   - Verificar que se inicializa con el rango correcto (últimos 7 días)
   - Verificar que los rangos predefinidos funcionan correctamente
   - Verificar que el evento `onSelect` se dispara

2. **Test Year Options Generator**
   - Verificar que genera 5 años correctamente
   - Verificar que el año actual está incluido
   - Verificar formato de datos (id, valor)

3. **Test renderDashboard Data Extraction**
   - Verificar extracción correcta de UDN
   - Verificar extracción correcta de fechas del DateRangePicker
   - Verificar extracción correcta del año de comparación

### Integration Tests

1. **Test FilterBar → Backend Communication**
   - Seleccionar diferentes rangos de fechas
   - Verificar que los datos enviados al backend son correctos
   - Verificar que la respuesta se procesa correctamente

2. **Test Chart Updates**
   - Cambiar filtros y verificar que todos los gráficos se actualizan
   - Verificar que las etiquetas de años son correctas
   - Verificar que los colores de las series son consistentes

3. **Test UDN Change**
   - Cambiar UDN y verificar que se mantienen los filtros de fecha
   - Verificar que las categorías se actualizan correctamente

### Manual Testing Checklist

- [ ] El DateRangePicker se muestra correctamente al cargar la página
- [ ] Los rangos predefinidos funcionan correctamente
- [ ] El select de años muestra los últimos 5 años
- [ ] Al cambiar el rango de fechas, los gráficos se actualizan automáticamente
- [ ] Al cambiar el año de comparación, los gráficos se actualizan
- [ ] Al cambiar la UDN, se mantienen los filtros de fecha
- [ ] Los gráficos comparativos muestran las etiquetas de años correctas
- [ ] El sistema maneja correctamente rangos de fechas personalizados
- [ ] Los mensajes de error se muestran cuando hay problemas
- [ ] El rendimiento es aceptable (carga en menos de 2 segundos)

## Design Decisions

### 1. Uso de DateRangePicker en lugar de inputs tipo month

**Decisión:** Implementar DateRangePicker con rangos predefinidos

**Razones:**
- Mayor flexibilidad para el usuario (puede seleccionar cualquier rango)
- Consistencia con el módulo de análisis de ventas
- Mejor UX con opciones predefinidas comunes
- Permite análisis de períodos no alineados a meses completos

**Alternativas consideradas:**
- Mantener inputs tipo month: Rechazado por falta de flexibilidad
- Usar dos DatePickers separados: Rechazado por complejidad innecesaria

### 2. Select de año en lugar de segundo DateRangePicker

**Decisión:** Usar un select con años predefinidos para comparación

**Razones:**
- Simplifica la interfaz (un solo DateRangePicker)
- Facilita comparaciones año contra año del mismo período
- Reduce la complejidad de cálculos en el backend
- Mejora la claridad de los gráficos comparativos

**Alternativas consideradas:**
- Dos DateRangePickers independientes: Rechazado por complejidad
- Checkbox para activar/desactivar comparación: Considerado para fase 2

### 3. Actualización automática vs botón de búsqueda

**Decisión:** Actualización automática al cambiar filtros

**Razones:**
- Mejor experiencia de usuario (resultados inmediatos)
- Consistencia con el comportamiento actual del dashboard
- Reduce clics necesarios para obtener información

**Consideraciones:**
- Implementar debouncing si hay problemas de rendimiento
- Mostrar indicador de carga durante las peticiones

### 4. Manejo de rangos de diferente duración

**Decisión:** Permitir rangos de cualquier duración, mostrar advertencias visuales

**Razones:**
- Máxima flexibilidad para el usuario
- Útil para análisis específicos (ej: comparar una semana promocional)
- Las advertencias educan al usuario sobre interpretación correcta

**Implementación:**
- Calcular duración de ambos períodos
- Si difieren en más de 3 días, mostrar badge de advertencia
- Incluir duración en tooltips de gráficos

## Performance Considerations

### Frontend Optimization

1. **Debouncing de eventos**
   ```javascript
   let renderTimeout;
   function debouncedRender() {
       clearTimeout(renderTimeout);
       renderTimeout = setTimeout(() => {
           dashboard.renderDashboard();
       }, 300);
   }
   ```

2. **Cancelación de peticiones anteriores**
   ```javascript
   let currentRequest;
   async renderDashboard() {
       if (currentRequest) {
           currentRequest.abort();
       }
       currentRequest = useFetch({ ... });
   }
   ```

3. **Lazy loading de gráficos**
   - Cargar primero KPI cards
   - Luego cargar gráficos en orden de prioridad

### Backend Optimization

1. **Caché de consultas frecuentes**
   - Cachear resultados de rangos comunes (mes actual, mes anterior)
   - Invalidar caché al final del día

2. **Índices de base de datos**
   - Asegurar índices en columnas de fecha
   - Índice compuesto en (udn, fecha)

3. **Paginación de datos**
   - Limitar resultados a máximo 365 días
   - Agregar datos por semana si el rango es mayor a 3 meses

## Accessibility

- Todos los inputs deben tener labels descriptivos
- El DateRangePicker debe ser navegable por teclado
- Los gráficos deben incluir texto alternativo
- Los colores deben tener suficiente contraste (WCAG AA)
- Mensajes de error deben ser claros y accionables

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

Dependencias:
- jQuery 3.x
- Moment.js 2.x
- DateRangePicker 3.x
- Chart.js 3.x
