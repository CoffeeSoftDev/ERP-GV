# Design Document

## Overview

Este documento describe el diseño técnico para las mejoras del componente de historial de métricas de redes sociales. Las mejoras se implementarán modificando la clase `RegisterSocialNetWork` existente, específicamente los métodos relacionados con el historial de métricas, manteniendo la arquitectura MVC del framework CoffeeSoft.

## Architecture

### Component Structure
```
RegisterSocialNetWork (Clase existente)
├── layoutCaptureForm() - Modificado para incluir filtro de fecha
├── loadHistory() - Modificado para manejar filtros y contador
├── createHistoryFilter() - Nuevo método para filtro de fecha
├── updateHistoryTitle() - Nuevo método para título dinámico
└── filterHistoryByMonth() - Nuevo método para filtrado
```

### Data Flow
```
User Selection → Date Filter → Filter Logic → Update Display → Update Counter
                                    ↓
                            Backend API Call (opcional)
                                    ↓
                            Render Filtered Cards
```

## Components and Interfaces

### 1. History Filter Component

**Ubicación:** Dentro del contenedor del historial, antes de las métricas
**Funcionalidad:** Filtro dropdown para selección de mes

```javascript
createHistoryFilter() {
    // Genera select con opciones: "Todas", "Enero", "Febrero", etc.
    // Integrado con el diseño existente de CoffeeSoft
}
```

### 2. Metrics Counter Component

**Ubicación:** Parte superior derecha del encabezado del historial
**Funcionalidad:** Muestra conteo dinámico de métricas visibles

```javascript
updateMetricsCounter(count) {
    // Actualiza el contador con formato "X métricas"
    // Estilo consistente con el diseño actual
}
```

### 3. Scroll Area Enhancement

**Implementación:** CSS y estructura HTML modificada
**Características:**
- `min-height: 400px`
- `max-height: 600px` 
- `overflow-y: auto`
- Scroll suave y responsivo

### 4. Dynamic Title Component

**Funcionalidad:** Título que se actualiza con el año seleccionado
**Formato:** "Historial de Métricas - Año YYYY"

## Data Models

### Filter State Object
```javascript
historyFilter = {
    selectedMonth: null, // null = "Todas", 1-12 = mes específico
    selectedYear: 2024,  // Año del filtro principal
    totalMetrics: 0,     // Contador total
    filteredMetrics: 0   // Contador filtrado
}
```

### Metric Card Data (Existente - Sin cambios)
```javascript
metricItem = {
    id: number,
    network: string,
    date: string,
    color: string,
    icon: string,
    metrics: Array<{name: string, value: string}>
}
```

## Error Handling

### 1. Filter Errors
- **Escenario:** Error al filtrar métricas
- **Manejo:** Mostrar todas las métricas y mensaje de advertencia
- **Fallback:** Resetear filtro a "Todas"

### 2. Counter Update Errors
- **Escenario:** Error al actualizar contador
- **Manejo:** Mostrar "-- métricas" como placeholder
- **Logging:** Registrar error en consola

### 3. Scroll Area Issues
- **Escenario:** Problemas de renderizado en diferentes navegadores
- **Manejo:** CSS fallbacks para compatibilidad
- **Responsive:** Ajustes para dispositivos móviles

## Testing Strategy

### 1. Unit Testing (Opcional)
- Pruebas de filtrado por mes
- Validación de contador de métricas
- Verificación de actualización de título

### 2. Integration Testing
- Interacción entre filtro y visualización
- Sincronización con filtros principales (UDN, Año)
- Compatibilidad con funciones existentes (editar, eliminar)

### 3. UI/UX Testing
- Responsividad en diferentes tamaños de pantalla
- Funcionalidad de scroll en diversos navegadores
- Consistencia visual con el diseño existente

## Implementation Details

### CSS Modifications
```css
.history-container {
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
}

.history-filter {
    margin-bottom: 1rem;
    /* Estilos consistentes con CoffeeSoft */
}

.metrics-counter {
    font-size: 0.875rem;
    color: #6B7280;
    font-weight: 500;
}
```

### JavaScript Structure
```javascript
// Método principal modificado
loadHistory() {
    // 1. Cargar datos del backend
    // 2. Aplicar filtros locales
    // 3. Renderizar cards filtradas
    // 4. Actualizar contador
    // 5. Actualizar título
}

// Nuevos métodos de soporte
createHistoryFilter()
filterHistoryByMonth(month)
updateMetricsCounter(count)
updateHistoryTitle(year)
```

### Backend Integration
- **Sin cambios requeridos:** El filtrado se realizará del lado del cliente
- **Optimización futura:** Posible implementación de filtrado en el backend para mejor performance
- **API existente:** Mantiene compatibilidad con `apiGetHistoryMetrics`

## Performance Considerations

### Client-Side Filtering
- **Ventaja:** Respuesta inmediata sin llamadas al servidor
- **Limitación:** Puede ser lento con grandes volúmenes de datos (>1000 métricas)
- **Optimización:** Implementar paginación si es necesario

### Memory Management
- **Scroll Virtual:** Considerar para grandes datasets
- **DOM Cleanup:** Remover elementos no visibles del DOM
- **Event Listeners:** Cleanup apropiado en cambios de vista

## Security Considerations

### Input Validation
- Validación de selección de mes (1-12 o null)
- Sanitización de datos de entrada
- Prevención de XSS en elementos dinámicos

### Data Integrity
- Verificación de consistencia en contadores
- Validación de estructura de datos de métricas
- Manejo seguro de estados de filtro