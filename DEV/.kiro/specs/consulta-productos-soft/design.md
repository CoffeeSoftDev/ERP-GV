# Design Document

## Overview

El módulo de consulta de productos Soft Restaurant será una aplicación web que permitirá visualizar, buscar y filtrar productos almacenados en la base de datos. El diseño sigue la arquitectura MVC (Model-View-Controller) existente en el proyecto, utilizando PHP para el backend y JavaScript para la interacción del cliente.

La aplicación se integrará en la ruta `DEV/kpi/marketing/ventas/soft/` y seguirá los patrones de diseño establecidos en otros módulos del sistema, como el módulo de ventas existente.

## Architecture

### High-Level Architecture

```
┌─────────────────┐
│   Browser       │
│  (JavaScript)   │
└────────┬────────┘
         │ AJAX
         ▼
┌─────────────────┐
│  Controller     │
│  (ctrl-productos│
│   -soft.php)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│    Model        │
│ (mdl-productos  │
│   -soft.php)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Database      │
│  (MySQL/PDO)    │
└─────────────────┘
```

### Directory Structure

```
DEV/kpi/marketing/ventas/soft/
├── ctrl/
│   └── ctrl-productos-soft.php    # Controlador principal
├── mdl/
│   └── mdl-productos-soft.php     # Modelo de datos
├── layout/
│   ├── head.php                   # Meta tags y CSS
│   ├── core-libraries.php         # Librerías JS core
│   └── script.php                 # Scripts adicionales
├── src/
│   └── js/
│       └── productos-soft.js      # Lógica del cliente
└── index.php                      # Vista principal
```

### Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL con PDO
- **Frontend**: HTML5, JavaScript (ES6+), CoffeeSoft Framework
- **CSS**: Tailwind CSS (integrado en el framework)
- **AJAX**: Fetch API
- **Plugins**: CoffeeSoft.js, complementos.js

## Components and Interfaces

### 1. Database Layer (Model)

**File**: `mdl/mdl-productos-soft.php`

**Class**: `mdl extends CRUD`

**Methods**:

```php
class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct()
    // Inicializa utilidades y prefijo de base de datos

    public function listProductos($array)
    // Lista productos con filtros opcionales
    // @param array $array [udn_id (opcional), search (opcional)]
    // @return array Lista de productos con información completa

    public function getProductoById($id)
    // Obtiene un producto específico por ID
    // @param int $id ID del producto
    // @return array Datos del producto

    public function lsUDN()
    // Lista todas las UDN activas
    // @return array Lista de UDN con id y nombre

    public function getGrupoProducto($id_grupo)
    // Obtiene el nombre del grupo de producto
    // @param int $id_grupo ID del grupo
    // @return string Nombre del grupo

    public function getCostSysData($id_producto)
    // Obtiene datos de costos desde soft_costsys
    // @param int $id_producto ID del producto
    // @return array Datos de costos y recetas
}
```

**SQL Queries**:

```sql
-- Query principal para listar productos
SELECT 
    sp.id_Producto,
    sp.clave_producto_soft,
    sp.descripcion,
    sp.id_grupo_productos,
    sp.id_udn,
    sp.costo,
    sp.id_costys,
    sc.fecha,
    sc.id_soft_productos,
    sc.id_costsys_recetas,
    u.UDN as udn_nombre,
    u.idUDN as id_udn
FROM rfwsmqex_gvsl_finanzas.soft_productos AS sp
LEFT JOIN rfwsmqex_gvsl_finanzas.soft_costsys AS sc 
    ON sp.id_Producto = sc.id_soft_productos
INNER JOIN rfwsmqex_gvsl.udn AS u 
    ON sp.id_udn = u.idUDN
WHERE 1=1
    [AND u.idUDN = ?]
    [AND (sp.clave_producto_soft LIKE ? 
          OR sp.descripcion LIKE ? 
          OR grupo_nombre LIKE ?)]
ORDER BY sp.descripcion ASC
```

### 2. Business Logic Layer (Controller)

**File**: `ctrl/ctrl-productos-soft.php`

**Class**: `ctrl extends mdl`

**Methods**:

```php
class ctrl extends mdl {
    
    public function init()
    // Inicializa datos para la vista
    // @return array ['udn' => lista de UDN]

    public function lsProductos()
    // Lista productos con filtros aplicados
    // @return array ['row' => datos formateados, 'thead' => encabezados]

    public function getProducto()
    // Obtiene detalles de un producto específico
    // @return array ['status', 'message', 'data']

    private function formatProductRow($producto)
    // Formatea una fila de producto para la tabla
    // @param array $producto Datos del producto
    // @return array Fila formateada con HTML

    private function formatMoney($value)
    // Formatea valores monetarios
    // @param float $value Valor a formatear
    // @return string Valor formateado con símbolo $
}
```

**Request/Response Format**:

```javascript
// Request
POST /ctrl/ctrl-productos-soft.php
Content-Type: application/x-www-form-urlencoded

opc=lsProductos&udn=1&search=cafe

// Response
{
    "status": 200,
    "row": [
        {
            "id": 1,
            "Clave": "CAFE001",
            "Descripción": "Café Americano",
            "Grupo": "Bebidas",
            "UDN": "Hotel Varoch",
            "Costo": "$ 15.50",
            "Precio Venta": "$ 35.00",
            "Precio Licencia": "$ 32.00"
        }
    ],
    "thead": ["Clave", "Descripción", "Grupo", "UDN", "Costo", "Precio Venta", "Precio Licencia"]
}
```

### 3. Presentation Layer (View)

**File**: `index.php`

**Structure**:

```php
<?php
    require_once('layout/head.php');
    require_once('layout/core-libraries.php');
?>

<!-- CoffeeSoft Framework -->
<script src="https://plugins.erp-varoch.com/coffee-lib/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<body>
    <?php require_once('../../../../layout/navbar.php'); ?>

    <main>
        <section id="sidebar"></section>

        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>KPI</li>
                    <li class='breadcrumb-item text-uppercase text-muted'>Marketing</li>
                    <li class='breadcrumb-item text-uppercase text-muted'>Ventas</li>
                    <li class='breadcrumb-item fw-bold active'>Productos Soft</li>
                </ol>
            </nav>

            <div class="main-container" id="root"></div>

            <script src="src/js/productos-soft.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>
</html>
```

### 4. Client-Side Logic (JavaScript)

**File**: `src/js/productos-soft.js`

**Structure**:

```javascript
const productosSoft = {
    // Estado de la aplicación
    state: {
        udn: 'all',
        search: '',
        currentPage: 1,
        itemsPerPage: 25,
        productos: []
    },

    // Inicialización
    init: async function() {
        await this.loadInitialData();
        this.render();
        this.attachEventListeners();
    },

    // Cargar datos iniciales
    loadInitialData: async function() {
        const response = await fetch('ctrl/ctrl-productos-soft.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'opc=init'
        });
        const data = await response.json();
        this.state.udnList = data.udn;
        await this.loadProductos();
    },

    // Cargar productos
    loadProductos: async function() {
        const params = new URLSearchParams({
            opc: 'lsProductos',
            udn: this.state.udn,
            search: this.state.search
        });

        const response = await fetch('ctrl/ctrl-productos-soft.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params
        });

        const data = await response.json();
        this.state.productos = data.row;
        this.render();
    },

    // Renderizar interfaz
    render: function() {
        const html = `
            <div class="card">
                <div class="card-header">
                    <h3>Productos Soft Restaurant</h3>
                </div>
                <div class="card-body">
                    ${this.renderFilters()}
                    ${this.renderTable()}
                    ${this.renderPagination()}
                </div>
            </div>
        `;
        document.getElementById('root').innerHTML = html;
    },

    // Renderizar filtros
    renderFilters: function() { /* ... */ },

    // Renderizar tabla
    renderTable: function() { /* ... */ },

    // Renderizar paginación
    renderPagination: function() { /* ... */ },

    // Event listeners
    attachEventListeners: function() {
        // Búsqueda
        document.getElementById('search').addEventListener('input', 
            this.debounce((e) => {
                this.state.search = e.target.value;
                this.loadProductos();
            }, 300)
        );

        // Filtro UDN
        document.getElementById('udn-filter').addEventListener('change', (e) => {
            this.state.udn = e.target.value;
            this.loadProductos();
        });
    },

    // Utilidades
    debounce: function(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    productosSoft.init();
});
```

## Data Models

### soft_productos Table

```sql
CREATE TABLE soft_productos (
    id_Producto INT PRIMARY KEY AUTO_INCREMENT,
    clave_producto_soft VARCHAR(50),
    descripcion VARCHAR(255),
    id_grupo_productos INT,
    id_udn INT,
    costo DECIMAL(10,2),
    id_costys INT,
    status TINYINT DEFAULT 1,
    fecha DATETIME,
    FOREIGN KEY (id_udn) REFERENCES udn(idUDN),
    FOREIGN KEY (id_grupo_productos) REFERENCES recetas(idreceta)
);
```

### soft_costsys Table

```sql
CREATE TABLE soft_costsys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATETIME,
    id_soft_productos INT,
    id_costsys_recetas INT,
    id_homologado INT,
    fecha_creacion DATETIME,
    FOREIGN KEY (id_soft_productos) REFERENCES soft_productos(id_Producto)
);
```

### recetas Table (for product groups)

```sql
CREATE TABLE recetas (
    idreceta INT PRIMARY KEY AUTO_INCREMENT,
    id_UDN INT,
    id_Subclasificacion INT,
    Nombre VARCHAR(255),
    precioVenta DECIMAL(10,2),
    rendimiento DECIMAL(10,2),
    fecha DATETIME,
    Clasificacion VARCHAR(100)
);
```

### udn Table

```sql
CREATE TABLE udn (
    idUDN INT PRIMARY KEY AUTO_INCREMENT,
    UDN VARCHAR(100),
    Stado TINYINT DEFAULT 1
);
```

## Error Handling

### Backend Error Handling

```php
try {
    // Operación de base de datos
    $result = $this->listProductos($params);
    
    return [
        'status' => 200,
        'message' => 'Datos obtenidos correctamente',
        'data' => $result
    ];
} catch (PDOException $e) {
    $this->writeToLog("Error en listProductos: " . $e->getMessage());
    
    return [
        'status' => 500,
        'message' => 'Error al obtener los productos',
        'data' => null
    ];
}
```

### Frontend Error Handling

```javascript
try {
    const response = await fetch(url, options);
    
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    
    if (data.status !== 200) {
        this.showError(data.message);
        return;
    }
    
    // Procesar datos exitosos
    this.processData(data);
    
} catch (error) {
    console.error('Error:', error);
    this.showError('Error al cargar los datos. Por favor, intente nuevamente.');
}
```

### Error Messages

- **Connection Error**: "Error de conexión con el servidor. Verifique su conexión a internet."
- **No Data**: "No se encontraron productos que coincidan con los criterios de búsqueda."
- **Database Error**: "Error al consultar la base de datos. Contacte al administrador."
- **Invalid Parameters**: "Parámetros de búsqueda inválidos."

## Testing Strategy

### Unit Testing

**Backend Tests** (Manual/PHPUnit):

1. **Model Tests**:
   - Test `listProductos()` con diferentes filtros
   - Test `getProductoById()` con ID válido e inválido
   - Test `lsUDN()` retorna lista correcta
   - Test queries SQL con datos de prueba

2. **Controller Tests**:
   - Test `init()` retorna estructura correcta
   - Test `lsProductos()` con diferentes parámetros POST
   - Test formateo de datos monetarios
   - Test manejo de errores

**Frontend Tests** (Manual/Jest):

1. **JavaScript Tests**:
   - Test inicialización del módulo
   - Test filtrado de productos
   - Test búsqueda con debounce
   - Test paginación
   - Test renderizado de tabla

### Integration Testing

1. **End-to-End Flow**:
   - Usuario accede a la página → Datos se cargan correctamente
   - Usuario filtra por UDN → Tabla se actualiza
   - Usuario busca producto → Resultados filtrados aparecen
   - Usuario cambia de página → Paginación funciona

2. **Database Integration**:
   - Verificar conexión a base de datos
   - Verificar queries retornan datos esperados
   - Verificar joins entre tablas funcionan correctamente

### Performance Testing

1. **Load Testing**:
   - Medir tiempo de carga con 100 productos
   - Medir tiempo de carga con 1000 productos
   - Verificar que la carga inicial sea < 3 segundos

2. **Query Optimization**:
   - Verificar uso de índices en columnas de búsqueda
   - Medir tiempo de ejecución de queries
   - Optimizar queries lentas (> 1 segundo)

### Manual Testing Checklist

- [ ] Página carga correctamente
- [ ] Navbar y breadcrumbs se muestran
- [ ] Tabla de productos se renderiza
- [ ] Filtro de UDN funciona
- [ ] Búsqueda funciona
- [ ] Paginación funciona
- [ ] Formato de precios es correcto
- [ ] Responsive design funciona en móvil
- [ ] Manejo de errores funciona
- [ ] No hay errores en consola del navegador

## Security Considerations

### Input Validation

```php
// Validar parámetros POST
$udn = filter_input(INPUT_POST, 'udn', FILTER_VALIDATE_INT);
$search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);

if ($udn === false || $udn < 0) {
    return ['status' => 400, 'message' => 'UDN inválida'];
}
```

### SQL Injection Prevention

- Usar prepared statements con PDO (ya implementado en CRUD class)
- Nunca concatenar valores de usuario directamente en queries
- Validar todos los inputs antes de usarlos en queries

### XSS Prevention

```php
// Escapar output HTML
function escapeHtml($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
```

### Authentication & Authorization

- Verificar sesión de usuario antes de mostrar datos
- Implementar control de acceso basado en roles
- Validar permisos para acceder al módulo

## Performance Optimization

### Database Optimization

```sql
-- Índices recomendados
CREATE INDEX idx_soft_productos_udn ON soft_productos(id_udn);
CREATE INDEX idx_soft_productos_descripcion ON soft_productos(descripcion);
CREATE INDEX idx_soft_productos_clave ON soft_productos(clave_producto_soft);
CREATE INDEX idx_soft_productos_grupo ON soft_productos(id_grupo_productos);
```

### Frontend Optimization

1. **Lazy Loading**: Cargar productos en páginas de 25 items
2. **Debouncing**: Aplicar debounce de 300ms en búsqueda
3. **Caching**: Cachear lista de UDN (no cambia frecuentemente)
4. **Minification**: Minificar JavaScript en producción

### Backend Optimization

1. **Query Optimization**: Usar LIMIT en queries
2. **Connection Pooling**: Reutilizar conexiones PDO
3. **Response Compression**: Habilitar gzip en servidor
4. **Caching**: Implementar cache de resultados frecuentes

## Deployment Considerations

### File Permissions

```bash
# Permisos recomendados
chmod 755 DEV/kpi/marketing/ventas/soft/
chmod 644 DEV/kpi/marketing/ventas/soft/*.php
chmod 644 DEV/kpi/marketing/ventas/soft/src/js/*.js
```

### Environment Configuration

- Verificar que la base de datos `rfwsmqex_erp` esté accesible
- Verificar credenciales de conexión en `_Conect.php`
- Verificar que las tablas necesarias existan
- Verificar permisos de usuario de base de datos

### Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Dependencies

- PHP 7.4 o superior
- MySQL 5.7 o superior
- PDO extension habilitada
- CoffeeSoft Framework cargado
- Tailwind CSS disponible
