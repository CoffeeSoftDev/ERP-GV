# Design Document

## Overview

Este documento describe el diseño técnico para implementar la funcionalidad de visualización de grupos de productos por UDN en formato de tarjetas (cards) dentro del módulo de productos Soft Restaurant. La solución se integra con la arquitectura MVC existente de CoffeeSoft y reutiliza componentes ya implementados.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (JavaScript)                     │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  ProductosSoft Class (extends Templates)               │ │
│  │  ├─ render()                                            │ │
│  │  ├─ layout() → tabLayout con nueva pestaña             │ │
│  │  ├─ filterBarGrupos() → Select UDN                     │ │
│  │  ├─ loadGruposCards() → Petición AJAX                  │ │
│  │  ├─ renderGruposCards(grupos) → Grid de Cards          │ │
│  │  └─ showProductosByGrupo(id) → Tabla filtrada          │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Backend (PHP - Controlador)                │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  ctrl Class (extends mdlProductosSoft)                 │ │
│  │  ├─ init() → Retorna lsUDN() y lsGrupos()             │ │
│  │  ├─ lsGrupos() → Procesa $_POST['udn']                │ │
│  │  └─ lsProductos() → Filtra por $_POST['grupo']        │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                    Backend (PHP - Modelo)                    │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  mdlProductosSoft Class (extends CRUD)                 │ │
│  │  ├─ listGrupos($params) → Query con COUNT y JOIN      │ │
│  │  └─ listProductos($params) → Query con filtro grupo   │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                      Base de Datos MySQL                     │
│  ├─ soft_grupo_productos (id, grupoproductos, id_UDN)      │
│  ├─ soft_productos (id, descripcion, id_grupo_productos)   │
│  └─ udn (id_UDN, nombre)                                    │
└─────────────────────────────────────────────────────────────┘
```

### Component Flow

1. **Inicialización:**
   - Usuario accede a la pestaña "Grupos por UDN"
   - Sistema renderiza filterBar con select de UDN
   - Sistema carga grupos de la UDN por defecto

2. **Selección de UDN:**
   - Usuario selecciona UDN del dropdown
   - Frontend ejecuta `loadGruposCards()`
   - Backend procesa `lsGrupos()` con filtro UDN
   - Frontend renderiza cards en grid responsive

3. **Selección de Grupo:**
   - Usuario hace clic en una card
   - Frontend ejecuta `showProductosByGrupo(id)`
   - Backend procesa `lsProductos()` con filtro grupo_id
   - Frontend renderiza tabla de productos
   - Botón "Regresar" se hace visible

4. **Regreso a Grupos:**
   - Usuario hace clic en "Regresar"
   - Frontend ejecuta `loadGruposCards()`
   - Sistema vuelve a mostrar grid de cards
   - Botón "Regresar" se oculta

## Components and Interfaces

### Frontend Components

#### 1. Tab Layout Extension

**Ubicación:** `productos-soft.js` → método `layout()`

**Modificación:**
```javascript
this.tabLayout({
    parent: `container${this.PROJECT_NAME}`,
    id: "tabsProductos",
    theme: "light",
    type: "short",
    json: [
        // ... tabs existentes
        {
            id: "grupos-udn",
            tab: "Grupos por UDN",
            onClick: () => this.renderGruposUdn()
        }
    ]
});
```

**Propósito:** Agregar nueva pestaña sin afectar las existentes.

#### 2. FilterBar para Grupos

**Método:** `filterBarGrupos()`

**Estructura:**
```javascript
filterBarGrupos() {
    const container = $("#container-grupos-udn");
    container.html(`
        <div id="filterBarGrupos" class="mb-3"></div>
        <div id="contentGrupos"></div>
    `);

    this.createfilterBar({
        parent: "filterBarGrupos",
        data: [
            {
                opc: "select",
                id: "udnGrupos",
                lbl: "Unidad de Negocio",
                class: "col-sm-3",
                data: lsudn,
                onchange: "app.loadGruposCards()"
            },
            {
                opc: "button",
                id: "btnVolverGrupos",
                text: "Regresar a Grupos",
                class: "col-sm-2 d-none",
                onClick: () => this.loadGruposCards()
            }
        ]
    });
}
```

**Características:**
- Select de UDN con evento onchange
- Botón "Regresar" inicialmente oculto (d-none)
- Contenedor dinámico para cards/tabla

#### 3. Load Grupos Cards

**Método:** `loadGruposCards()`

**Flujo:**
```javascript
async loadGruposCards() {
    const udn = $("#filterBarGrupos #udnGrupos").val();
    
    // Ocultar botón regresar
    $("#btnVolverGrupos").addClass('d-none');
    
    // Petición AJAX
    const response = await useFetch({
        url: this._link,
        data: {
            opc: 'lsGrupos',
            udn: udn
        }
    });
    
    if (response && response.status === 200) {
        this.renderGruposCards(response.grupos);
    }
}
```

**Responsabilidades:**
- Obtener UDN seleccionada
- Realizar petición al backend
- Delegar renderizado a `renderGruposCards()`
- Manejar errores de conexión

#### 4. Render Grupos Cards

**Método:** `renderGruposCards(grupos)`

**Estructura HTML:**
```javascript
renderGruposCards(grupos) {
    const container = $("#contentGrupos");
    container.html(`
        <div class="px-4 py-3">
            <h3 class="text-xl font-bold text-[#103B60] mb-2">Grupos de Productos</h3>
            <p class="text-gray-600 mb-4">Selecciona un grupo para ver sus productos</p>
            <div id="gruposGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
        </div>
    `);

    const grid = $("#gruposGrid");
    
    grupos.forEach(grupo => {
        const card = $(`
            <div class="bg-white border-2 border-gray-200 rounded-lg p-3 
                        hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer">
                <h4 class="font-bold text-sm text-gray-800 mb-2 text-center">${grupo.valor}</h4>
                <div class="text-center">
                    <span class="text-2xl font-bold text-blue-600">${grupo.cantidad_productos}</span>
                    <p class="text-xs text-gray-500">productos</p>
                </div>
            </div>
        `);
        
        card.on('click', () => {
            this.showProductosByGrupo(grupo.id, grupo.valor);
        });
        
        grid.append(card);
    });
}
```

**Características:**
- Grid responsive (2/4/6 columnas)
- Cards con hover effects
- Click handler en cada card
- Diseño limpio con TailwindCSS

#### 5. Show Productos By Grupo

**Método:** `showProductosByGrupo(idGrupo, nombreGrupo)`

**Flujo:**
```javascript
async showProductosByGrupo(idGrupo, nombreGrupo) {
    // Mostrar botón regresar
    $("#btnVolverGrupos").removeClass('d-none');
    
    const udn = $("#filterBarGrupos #udnGrupos").val();
    
    // Petición AJAX
    const response = await useFetch({
        url: this._link,
        data: {
            opc: 'lsProductos',
            grupo: idGrupo,
            udn: udn
        }
    });
    
    if (response && response.status === 200) {
        const container = $("#contentGrupos");
        container.html(`
            <div class="px-4 py-3">
                <h3 class="text-xl font-bold text-[#103B60] mb-2">${nombreGrupo}</h3>
                <p class="text-gray-600 mb-4">Productos del grupo</p>
                <div id="tableProductosGrupo"></div>
            </div>
        `);
        
        this.createTable({
            parent: "tableProductosGrupo",
            data: { opc: 'lsProductos', grupo: idGrupo, udn: udn },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbProductosGrupo",
                theme: 'corporativo',
                center: [2],
                right: [3, 4]
            }
        });
    }
}
```

**Responsabilidades:**
- Mostrar botón "Regresar"
- Obtener productos filtrados por grupo
- Renderizar tabla con createTable()
- Mantener filtro de UDN activo

### Backend Components

#### 1. Controlador - lsGrupos()

**Ubicación:** `ctrl-productos-soft.php`

**Implementación:**
```php
function lsGrupos() {
    $__row = [];
    
    $udn = $_POST['udn'] ?? 'all';
    
    $params = [];
    if ($udn !== 'all') {
        $params['udn'] = $udn;
    }
    
    $ls = $this->listGrupos($params);
    
    foreach ($ls as $key) {
        $__row[] = [
            'id' => $key['id'],
            'valor' => htmlspecialchars($key['grupoproductos'], ENT_QUOTES, 'UTF-8'),
            'cantidad_productos' => intval($key['cantidad_productos'])
        ];
    }
    
    return [
        'status' => 200,
        'grupos' => $__row,
        'total' => count($__row)
    ];
}
```

**Características:**
- Manejo de parámetro opcional 'udn'
- Sanitización de datos con htmlspecialchars
- Estructura de respuesta estandarizada
- Conversión de tipos (intval)

#### 2. Controlador - lsProductos() Modificado

**Modificación:** Agregar soporte para filtro por grupo

```php
function lsProductos() {
    $__row = [];
    
    $udn = $_POST['udn'] ?? 'all';
    $grupo = $_POST['grupo'] ?? 'all';  // NUEVO
    $anio = $_POST['anio'] ?? '';
    $mes = $_POST['mes'] ?? '';

    $params = [];
    if ($udn !== 'all') {
        $params['udn'] = $udn;
    }
    if ($grupo !== 'all') {  // NUEVO
        $params['grupo'] = $grupo;
    }
    if (!empty($anio)) {
        $params['anio'] = $anio;
    }
    if (!empty($mes)) {
        $params['mes'] = $mes;
    }

    $ls = $this->listProductos($params);
    
    // ... resto del código existente
}
```

#### 3. Modelo - listGrupos()

**Ubicación:** `mdl-productos-soft.php`

**Implementación:**
```php
function listGrupos($params = []) {
    $whereConditions = [];
    $data = [];
    
    if (isset($params['udn']) && $params['udn'] !== 'all') {
        $whereConditions[] = "g.id_UDN = ?";
        $data[] = $params['udn'];
    }
    
    $whereClause = !empty($whereConditions) 
        ? "WHERE " . implode(" AND ", $whereConditions) 
        : "";
    
    $query = "
        SELECT 
            g.id,
            g.grupoproductos,
            g.id_UDN,
            COUNT(p.id) as cantidad_productos
        FROM {$this->bd}soft_grupo_productos g
        LEFT JOIN {$this->bd}soft_productos p ON g.id = p.id_grupo_productos
        $whereClause
        GROUP BY g.id, g.grupoproductos, g.id_UDN
        ORDER BY g.grupoproductos ASC
    ";
    
    return $this->_Read($query, $data);
}
```

**Características:**
- Query con LEFT JOIN para incluir grupos sin productos
- COUNT para obtener cantidad de productos
- Filtro dinámico por UDN
- GROUP BY para agrupar correctamente
- ORDER BY alfabético

#### 4. Modelo - listProductos() Modificado

**Modificación:** Agregar soporte para filtro por grupo

```php
function listProductos($params = []) {
    $whereConditions = ["p.activo = 1"];
    $data = [];
    
    if (isset($params['udn']) && $params['udn'] !== 'all') {
        $whereConditions[] = "p.id_UDN = ?";
        $data[] = $params['udn'];
    }
    
    // NUEVO: Filtro por grupo
    if (isset($params['grupo']) && $params['grupo'] !== 'all') {
        $whereConditions[] = "p.id_grupo_productos = ?";
        $data[] = $params['grupo'];
    }
    
    if (isset($params['anio']) && !empty($params['anio'])) {
        $whereConditions[] = "YEAR(p.fecha) = ?";
        $data[] = $params['anio'];
    }
    
    if (isset($params['mes']) && !empty($params['mes'])) {
        $whereConditions[] = "MONTH(p.fecha) = ?";
        $data[] = $params['mes'];
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    $query = "
        SELECT 
            p.id,
            p.descripcion,
            g.grupoproductos,
            p.costo,
            p.precio_venta,
            p.cantidad_vendida
        FROM {$this->bd}soft_productos p
        LEFT JOIN {$this->bd}soft_grupo_productos g ON p.id_grupo_productos = g.id
        WHERE $whereClause
        ORDER BY p.descripcion ASC
    ";
    
    return $this->_Read($query, $data);
}
```

## Data Models

### Database Schema

#### Tabla: soft_grupo_productos
```sql
CREATE TABLE soft_grupo_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grupoproductos VARCHAR(255) NOT NULL,
    id_UDN INT NOT NULL,
    activo TINYINT DEFAULT 1,
    INDEX idx_udn (id_UDN),
    INDEX idx_activo (activo)
);
```

#### Tabla: soft_productos
```sql
CREATE TABLE soft_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(255) NOT NULL,
    id_grupo_productos INT,
    id_UDN INT NOT NULL,
    costo DECIMAL(10,2),
    precio_venta DECIMAL(10,2),
    cantidad_vendida INT DEFAULT 0,
    activo TINYINT DEFAULT 1,
    fecha DATE,
    INDEX idx_grupo (id_grupo_productos),
    INDEX idx_udn (id_UDN),
    INDEX idx_activo (activo),
    FOREIGN KEY (id_grupo_productos) REFERENCES soft_grupo_productos(id)
);
```

### Data Flow Models

#### Request: lsGrupos
```json
{
    "opc": "lsGrupos",
    "udn": "1"
}
```

#### Response: lsGrupos
```json
{
    "status": 200,
    "grupos": [
        {
            "id": 1,
            "valor": "Bebidas",
            "cantidad_productos": 45
        },
        {
            "id": 2,
            "valor": "Alimentos",
            "cantidad_productos": 120
        }
    ],
    "total": 2
}
```

#### Request: lsProductos (con filtro grupo)
```json
{
    "opc": "lsProductos",
    "udn": "1",
    "grupo": "1",
    "anio": "2024",
    "mes": "11"
}
```

#### Response: lsProductos
```json
{
    "row": [
        {
            "id": 1,
            "Descripción": "Coca Cola 600ml",
            "Grupo": "Bebidas",
            "Homologación": { "html": "...", "class": "..." },
            "Costo": { "html": "$ 15.00", "class": "text-end" },
            "Precio Venta": { "html": "$ 25.00", "class": "text-end" }
        }
    ],
    "ls": [...]
}
```

## Error Handling

### Frontend Error Handling

```javascript
async loadGruposCards() {
    const udn = $("#filterBarGrupos #udnGrupos").val();
    
    try {
        const response = await useFetch({
            url: this._link,
            data: { opc: 'lsGrupos', udn: udn }
        });
        
        if (response && response.status === 200) {
            if (response.grupos.length === 0) {
                $("#contentGrupos").html(`
                    <div class="text-center py-8">
                        <i class="icon-info-circle text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600">No hay grupos disponibles para esta UDN</p>
                    </div>
                `);
            } else {
                this.renderGruposCards(response.grupos);
            }
        } else {
            alert({
                icon: 'error',
                title: 'Error al cargar grupos',
                text: response.message || 'Ocurrió un error inesperado'
            });
        }
    } catch (error) {
        console.error('Error en loadGruposCards:', error);
        alert({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
}
```

### Backend Error Handling

```php
function lsGrupos() {
    $udn = $_POST['udn'] ?? 'all';
    
    if ($udn !== 'all' && !is_numeric($udn)) {
        return [
            'status' => 400,
            'message' => 'UDN inválida',
            'grupos' => []
        ];
    }
    
    $params = [];
    if ($udn !== 'all') {
        $params['udn'] = $udn;
    }
    
    $ls = $this->listGrupos($params);
    
    if ($ls === false) {
        return [
            'status' => 500,
            'message' => 'Error al consultar grupos',
            'grupos' => []
        ];
    }
    
    $__row = [];
    foreach ($ls as $key) {
        $__row[] = [
            'id' => $key['id'],
            'valor' => htmlspecialchars($key['grupoproductos'], ENT_QUOTES, 'UTF-8'),
            'cantidad_productos' => intval($key['cantidad_productos'])
        ];
    }
    
    return [
        'status' => 200,
        'grupos' => $__row,
        'total' => count($__row)
    ];
}
```

## Testing Strategy

### Unit Tests

#### Frontend Tests
1. **Test: renderGruposCards()**
   - Input: Array de grupos vacío
   - Expected: Mensaje "No hay grupos disponibles"

2. **Test: renderGruposCards()**
   - Input: Array con 3 grupos
   - Expected: 3 cards renderizadas en el grid

3. **Test: showProductosByGrupo()**
   - Input: idGrupo válido
   - Expected: Tabla de productos renderizada, botón "Regresar" visible

#### Backend Tests
1. **Test: lsGrupos() con UDN específica**
   - Input: `{ udn: 1 }`
   - Expected: Solo grupos de UDN 1

2. **Test: lsGrupos() con UDN 'all'**
   - Input: `{ udn: 'all' }`
   - Expected: Todos los grupos

3. **Test: listGrupos() con LEFT JOIN**
   - Expected: Incluir grupos con 0 productos

### Integration Tests

1. **Test: Flujo completo de navegación**
   - Seleccionar UDN → Ver cards → Click en card → Ver productos → Regresar

2. **Test: Cambio de UDN**
   - Seleccionar UDN 1 → Ver grupos → Cambiar a UDN 2 → Verificar actualización

3. **Test: Filtro combinado**
   - Seleccionar UDN → Click en grupo → Verificar productos filtrados por ambos

### Performance Tests

1. **Test: Carga de 50 grupos**
   - Expected: Tiempo < 2 segundos

2. **Test: Carga de 500 productos por grupo**
   - Expected: Tiempo < 3 segundos

3. **Test: Renderizado de grid con 100 cards**
   - Expected: Sin lag visual

## Security Considerations

### Input Validation

```php
// Validación de UDN
if ($udn !== 'all' && !is_numeric($udn)) {
    return ['status' => 400, 'message' => 'UDN inválida'];
}

// Validación de grupo
if ($grupo !== 'all' && !is_numeric($grupo)) {
    return ['status' => 400, 'message' => 'Grupo inválido'];
}
```

### SQL Injection Prevention

```php
// Uso de prepared statements con _Read()
$query = "SELECT * FROM tabla WHERE id = ?";
$result = $this->_Read($query, [$id]);
```

### XSS Prevention

```php
// Sanitización de output
$valor = htmlspecialchars($key['grupoproductos'], ENT_QUOTES, 'UTF-8');
```

### CSRF Protection

```php
// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
```

## Performance Optimization

### Database Optimization

1. **Índices recomendados:**
```sql
CREATE INDEX idx_grupo_udn ON soft_grupo_productos(id_UDN, activo);
CREATE INDEX idx_producto_grupo ON soft_productos(id_grupo_productos, activo);
CREATE INDEX idx_producto_udn ON soft_productos(id_UDN, activo);
```

2. **Query optimization:**
   - Usar LEFT JOIN en lugar de múltiples queries
   - Limitar campos en SELECT (no usar SELECT *)
   - Usar COUNT() en lugar de contar en PHP

### Frontend Optimization

1. **Lazy loading de cards:**
   - Renderizar solo cards visibles inicialmente
   - Cargar más al hacer scroll (si hay muchos grupos)

2. **Debounce en filtros:**
   - Evitar múltiples peticiones al cambiar UDN rápidamente

3. **Cache de datos:**
   - Guardar grupos en variable local
   - Evitar re-consultar al regresar de productos

## Deployment Considerations

### Files to Modify

1. **Frontend:**
   - `kpi/marketing/ventas/soft/src/js/productos-soft.js`
     - Agregar métodos: `renderGruposUdn()`, `filterBarGrupos()`, `loadGruposCards()`, `renderGruposCards()`, `showProductosByGrupo()`
     - Modificar método: `layout()` para agregar nueva pestaña

2. **Backend:**
   - `kpi/marketing/ventas/soft/ctrl/ctrl-productos-soft.php`
     - Agregar método: `lsGrupos()`
     - Modificar método: `lsProductos()` para soportar filtro por grupo
   
   - `kpi/marketing/ventas/soft/mdl/mdl-productos-soft.php`
     - Agregar método: `listGrupos()`
     - Modificar método: `listProductos()` para soportar filtro por grupo

### Database Changes

No se requieren cambios en la estructura de la base de datos. Las tablas `soft_grupo_productos` y `soft_productos` ya existen.

**Índices recomendados (opcional):**
```sql
CREATE INDEX idx_grupo_udn ON soft_grupo_productos(id_UDN) IF NOT EXISTS;
CREATE INDEX idx_producto_grupo ON soft_productos(id_grupo_productos) IF NOT EXISTS;
```

### Rollback Plan

1. **Revertir cambios en layout():**
   - Eliminar pestaña "Grupos por UDN" del array json en tabLayout

2. **Eliminar métodos agregados:**
   - Frontend: Comentar o eliminar métodos nuevos
   - Backend: Comentar o eliminar métodos nuevos

3. **Restaurar versión anterior:**
   - Usar control de versiones (git) para revertir commits

### Monitoring

1. **Logs a implementar:**
   - Tiempo de respuesta de `lsGrupos()`
   - Tiempo de respuesta de `lsProductos()` con filtro grupo
   - Errores de conexión a base de datos

2. **Métricas a monitorear:**
   - Cantidad de peticiones por minuto
   - Tiempo promedio de carga de cards
   - Tasa de errores en peticiones AJAX

## Design Decisions

### ¿Por qué cards en lugar de tabla para grupos?

**Decisión:** Usar cards visuales en grid responsive.

**Razones:**
- Mejor experiencia visual y más intuitiva
- Facilita identificación rápida de grupos
- Permite mostrar cantidad de productos de forma prominente
- Responsive por naturaleza (grid adapta columnas)
- Consistente con tendencias modernas de UI/UX

### ¿Por qué no usar un modal para productos?

**Decisión:** Reemplazar contenido en el mismo contenedor.

**Razones:**
- Evita complejidad de gestión de modales
- Mejor para tablas grandes con muchos productos
- Permite usar DataTables con todas sus funcionalidades
- Botón "Regresar" es más intuitivo que cerrar modal
- Mantiene contexto visual (filterBar siempre visible)

### ¿Por qué LEFT JOIN en lugar de INNER JOIN?

**Decisión:** Usar LEFT JOIN en `listGrupos()`.

**Razones:**
- Incluye grupos sin productos (cantidad = 0)
- Evita ocultar grupos recién creados
- Permite identificar grupos vacíos para limpieza
- Más flexible para futuras funcionalidades

### ¿Por qué no usar componente existente createItemCard()?

**Decisión:** Crear renderizado custom de cards.

**Razones:**
- `createItemCard()` está diseñado para navegación con enlaces
- Necesitamos click handlers personalizados
- Diseño específico para mostrar cantidad de productos
- Mayor control sobre estilos y comportamiento
- Evita overhead de componente genérico

## Future Enhancements

1. **Búsqueda de grupos:**
   - Agregar campo de búsqueda en filterBar
   - Filtrar cards en tiempo real

2. **Estadísticas en cards:**
   - Mostrar total de ventas del grupo
   - Mostrar producto más vendido
   - Indicador visual de rendimiento

3. **Ordenamiento de grupos:**
   - Por nombre (alfabético)
   - Por cantidad de productos
   - Por total de ventas

4. **Exportación de datos:**
   - Exportar productos de un grupo a Excel
   - Generar PDF con información del grupo

5. **Gestión de grupos:**
   - Crear/editar/eliminar grupos desde la interfaz
   - Asignar productos a grupos
   - Mover productos entre grupos

6. **Visualización avanzada:**
   - Gráficos de ventas por grupo
   - Comparativa entre grupos
   - Tendencias temporales
