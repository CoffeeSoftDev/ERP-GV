# Design Document

## Overview

El módulo de Administrador de Productos es una aplicación web de gestión CRUD que permite a los administradores mantener el catálogo de productos asociados a unidades de negocio. La arquitectura sigue el patrón MVC de CoffeeSoft con separación clara entre presentación (JS), lógica de negocio (CTRL) y acceso a datos (MDL).

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Browser (Cliente)                         │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  admin-productos.js (Frontend - Templates Class)       │ │
│  │  - App (main class)                                    │ │
│  │  - Layout management                                   │ │
│  │  - Event handlers                                      │ │
│  │  - CoffeeSoft components integration                  │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                    Server (PHP)                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  ctrl-admin-productos.php (Controller)                 │ │
│  │  - init()                                              │ │
│  │  - lsProductos()                                       │ │
│  │  - getProducto()                                       │ │
│  │  - addProducto()                                       │ │
│  │  - editProducto()                                      │ │
│  │  - statusProducto()                                    │ │
│  └────────────────────────────────────────────────────────┘ │
│                            ↕                                 │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  mdl-admin-productos.php (Model - CRUD Class)          │ │
│  │  - listProductos()                                     │ │
│  │  - getProductoById()                                   │ │
│  │  - createProducto()                                    │ │
│  │  - updateProducto()                                    │ │
│  │  - existsProductoByName()                              │ │
│  │  - lsUDN()                                             │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↕ SQL
┌─────────────────────────────────────────────────────────────┐
│                    Database (MySQL)                          │
│  - producto (id, nombre, descripcion, es_servicio,          │
│              udn_id, active)                                 │
│  - udn (idUDN, UDN, Abreviatura, Stado)                    │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend**: jQuery, TailwindCSS, CoffeeSoft Framework
- **Backend**: PHP 7.4+, CoffeeSoft CRUD Class
- **Database**: MySQL 5.7+
- **Communication**: AJAX (Fetch API)
- **UI Components**: CoffeeSoft Templates, Components

## Components and Interfaces

### Frontend Components (admin-productos.js)

#### Class: App extends Templates

**Properties:**
- `PROJECT_NAME`: "AdminProductos"
- `_link`: API endpoint (ctrl-admin-productos.php)
- `_div_modulo`: Root container ID

**Methods:**

##### `render()`
Initializes the module by calling layout, filterBar, and lsProductos methods.

##### `layout()`
Creates the primary layout structure using CoffeeSoft's primaryLayout component:
- Parent container: "container-productos" (from tab)
- FilterBar container: "filterBarAdminProductos"
- Table container: "containerAdminProductos"

##### `filterBar()`
Generates filter controls using createfilterBar:
- UDN selector (populated from init())
- Status selector (Disponibles/No disponibles)
- "Nuevo Producto" button

##### `lsProductos()`
Renders the product table using createTable:
- Fetches data via opc: "lsProductos"
- Displays columns: ID, Nombre, Descripción, Es Servicio, UDN, Estado, Acciones
- Implements pagination (15 rows)
- Theme: corporativo

##### `addProducto()`
Opens modal form for creating new products:
- Uses createModalForm
- Fields: nombre, descripcion, es_servicio (checkbox), udn_id (select), active (checkbox)
- Validation: required fields
- Success callback refreshes table

##### `async editProducto(id)`
Opens modal form for editing existing products:
- Fetches product data via opc: "getProducto"
- Pre-fills form with autofill
- Same fields as addProducto
- Success callback refreshes table

##### `statusProducto(id, active)`
Handles product deactivation:
- Uses swalQuestion for confirmation
- Sends opc: "statusProducto" with toggled active value
- Success callback refreshes table

##### `jsonProducto()`
Returns form field configuration array for modal forms.

### Backend Components

#### Controller (ctrl-admin-productos.php)

**Class: ctrl extends mdl**

##### `init()`
Returns initialization data:
```php
return [
    'udn' => $this->lsUDN(),
    'status' => [
        ['id' => 1, 'valor' => 'Disponibles'],
        ['id' => 0, 'valor' => 'No disponibles']
    ]
];
```

##### `lsProductos()`
Returns formatted product list:
- Retrieves products filtered by status and UDN
- Formats rows with action buttons
- Returns: `['row' => $__row, 'ls' => $ls]`

##### `getProducto()`
Returns single product data:
- Input: `$_POST['id']`
- Returns: `['status' => 200/404, 'message' => string, 'data' => array]`

##### `addProducto()`
Creates new product:
- Validates uniqueness by name
- Inserts record with date_creation
- Returns: `['status' => 200/409/500, 'message' => string]`

##### `editProducto()`
Updates existing product:
- Input: `$_POST['id']` + product fields
- Returns: `['status' => 200/500, 'message' => string]`

##### `statusProducto()`
Toggles product active status:
- Input: `$_POST['id']`, `$_POST['active']`
- Returns: `['status' => 200/500, 'message' => string]`

#### Model (mdl-admin-productos.php)

**Class: mdl extends CRUD**

**Properties:**
- `$bd`: "rfwsmqex_pedidos."
- `$util`: Utileria instance

##### `listProductos($array)`
```php
return $this->_Select([
    'table' => $this->bd . 'producto',
    'values' => "producto.id, producto.nombre, producto.descripcion, 
                 producto.es_servicio, udn.UDN, producto.active",
    'leftjoin' => [$this->bd . 'udn' => 'producto.udn_id = udn.idUDN'],
    'where' => 'producto.active = ? AND producto.udn_id = ?',
    'order' => ['DESC' => 'producto.id'],
    'data' => $array
]);
```

##### `getProductoById($id)`
```php
return $this->_Select([
    'table' => $this->bd . 'producto',
    'values' => '*',
    'where' => 'id = ?',
    'data' => [$id]
])[0];
```

##### `createProducto($array)`
```php
return $this->_Insert([
    'table' => $this->bd . 'producto',
    'values' => $array['values'],
    'data' => $array['data']
]);
```

##### `updateProducto($array)`
```php
return $this->_Update([
    'table' => $this->bd . 'producto',
    'values' => $array['values'],
    'where' => $array['where'],
    'data' => $array['data']
]);
```

##### `existsProductoByName($array)`
```php
$query = "SELECT id FROM {$this->bd}producto 
          WHERE LOWER(nombre) = LOWER(?) AND active = 1";
return count($this->_Read($query, $array)) > 0;
```

##### `lsUDN()`
```php
return $this->_Select([
    'table' => $this->bd . 'udn',
    'values' => 'idUDN as id, UDN as valor',
    'where' => 'Stado = 1',
    'order' => ['ASC' => 'UDN']
]);
```

## Data Models

### Database Schema

#### Table: producto
```sql
CREATE TABLE producto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    es_servicio TINYINT(1) DEFAULT 0,
    udn_id INT NOT NULL,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
);
```

#### Table: udn
```sql
CREATE TABLE udn (
    idUDN INT PRIMARY KEY AUTO_INCREMENT,
    UDN VARCHAR(100) NOT NULL,
    Abreviatura VARCHAR(10),
    Stado TINYINT(1) DEFAULT 1
);
```

### Data Flow

#### Create Product Flow
```
User clicks "Nuevo Producto"
    ↓
Modal form opens with empty fields
    ↓
User fills: nombre, descripcion, es_servicio, udn_id, active
    ↓
Form validation (required: nombre, udn_id, active)
    ↓
AJAX POST: { opc: "addProducto", ...fields }
    ↓
ctrl-admin-productos.php::addProducto()
    ↓
Check existsProductoByName()
    ↓
If exists: return status 409
If not exists: createProducto()
    ↓
Return status 200/500 + message
    ↓
Frontend: show alert + refresh table
```

#### Edit Product Flow
```
User clicks "Editar" button
    ↓
AJAX GET: { opc: "getProducto", id: X }
    ↓
ctrl-admin-productos.php::getProducto()
    ↓
mdl::getProductoById(id)
    ↓
Return product data
    ↓
Modal form opens with autofill data
    ↓
User modifies fields
    ↓
AJAX POST: { opc: "editProducto", id: X, ...fields }
    ↓
ctrl-admin-productos.php::editProducto()
    ↓
mdl::updateProducto()
    ↓
Return status 200/500 + message
    ↓
Frontend: show alert + refresh table
```

#### Delete (Deactivate) Product Flow
```
User clicks "Eliminar" button
    ↓
SweetAlert confirmation dialog
    ↓
User confirms
    ↓
AJAX POST: { opc: "statusProducto", id: X, active: 0 }
    ↓
ctrl-admin-productos.php::statusProducto()
    ↓
mdl::updateProducto() with active = 0
    ↓
Return status 200/500 + message
    ↓
Frontend: show alert + refresh table
```

## Error Handling

### Frontend Error Handling

**Validation Errors:**
- Required field validation before form submission
- Display inline error messages for invalid inputs
- Prevent form submission until all validations pass

**AJAX Errors:**
- Catch network errors and display user-friendly messages
- Handle timeout scenarios (> 30 seconds)
- Retry mechanism for failed requests (optional)

**User Feedback:**
- Success: Green alert with checkmark icon (auto-dismiss 3s)
- Error: Red alert with X icon (manual dismiss)
- Warning: Yellow alert for confirmations

### Backend Error Handling

**Database Errors:**
- Catch PDO exceptions in CRUD operations
- Log errors to error_log
- Return generic error messages to frontend (no SQL details)

**Validation Errors:**
- Check for duplicate product names
- Validate foreign key constraints (udn_id exists)
- Validate data types and lengths

**Response Format:**
```php
[
    'status' => 200|400|404|409|500,
    'message' => 'Descriptive message',
    'data' => [] // optional
]
```

**HTTP Status Codes:**
- 200: Success
- 400: Bad request (validation error)
- 404: Resource not found
- 409: Conflict (duplicate entry)
- 500: Server error

## Testing Strategy

### Unit Testing

**Frontend (Manual Testing):**
- Test each method in isolation using browser console
- Mock AJAX responses to test UI behavior
- Verify form validation logic
- Test event handlers (click, change, submit)

**Backend (PHPUnit):**
- Test each controller method with various inputs
- Test model CRUD operations with test database
- Verify validation logic (existsProductoByName)
- Test error handling scenarios

### Integration Testing

**End-to-End Flows:**
1. **Create Product Flow:**
   - Open modal → Fill form → Submit → Verify table refresh
   - Test with valid data (expect success)
   - Test with duplicate name (expect 409 error)
   - Test with missing required fields (expect validation error)

2. **Edit Product Flow:**
   - Click edit → Verify data loads → Modify → Submit → Verify update
   - Test with valid changes (expect success)
   - Test with invalid data (expect validation error)

3. **Delete Product Flow:**
   - Click delete → Confirm → Verify status change
   - Test deactivation (expect active = 0)
   - Verify product still exists in database

4. **Filter Flow:**
   - Change UDN filter → Verify table updates
   - Change status filter → Verify table updates
   - Combine filters → Verify correct results

### User Acceptance Testing

**Test Scenarios:**
1. Administrator can view all products for their UDN
2. Administrator can filter products by status
3. Administrator can create a new product successfully
4. Administrator receives error when creating duplicate product
5. Administrator can edit product details
6. Administrator can deactivate a product
7. Deactivated products appear in "No disponibles" filter
8. All operations provide clear feedback messages
9. Table refreshes automatically after CRUD operations
10. Interface is responsive on tablet and desktop

### Performance Testing

**Metrics:**
- Page load time: < 2 seconds
- Table refresh time: < 1 second
- Modal open time: < 500 milliseconds
- AJAX response time: < 1 second (average)

**Load Testing:**
- Test with 100+ products in table
- Test with 10+ concurrent users
- Verify pagination performance

## Security Considerations

**Input Validation:**
- Sanitize all user inputs using `$this->util->sql()`
- Validate data types and lengths
- Prevent SQL injection via prepared statements

**Authentication:**
- Verify user session before processing requests
- Check user permissions for CRUD operations

**Data Integrity:**
- Use foreign key constraints
- Implement soft deletes (active flag)
- Validate business rules (unique product names per UDN)

## UI/UX Design

### Color Scheme (CoffeeSoft Corporativo)
- Primary: #103B60 (dark blue)
- Success: #8CC63F (green)
- Danger: #DC3545 (red)
- Warning: #FFC107 (yellow)
- Neutral: #EAEAEA (light gray)
- Background: #FFFFFF (white)

### Typography
- Headers: 2xl, semibold
- Body: base, regular
- Labels: sm, medium

### Layout Structure
```
┌─────────────────────────────────────────────────────────┐
│  📦 Administrador de Productos                          │
│  Gestiona productos, categorías y clientes.            │
├─────────────────────────────────────────────────────────┤
│  [Tabs: Productos | Categorías | Clientes]             │
├─────────────────────────────────────────────────────────┤
│  Filter Bar:                                            │
│  [UDN Dropdown ▼] [Estado Dropdown ▼] [Nuevo Producto] │
├─────────────────────────────────────────────────────────┤
│  Product Table:                                         │
│  ┌───┬─────────┬──────────────┬────────┬─────┬────────┐│
│  │ID │ Nombre  │ Descripción  │Servicio│ UDN │ Estado ││
│  ├───┼─────────┼──────────────┼────────┼─────┼────────┤│
│  │ 1 │Producto1│ Desc...      │  No    │UDN1 │Activo  ││
│  │   │         │              │        │     │[Edit][X]│
│  └───┴─────────┴──────────────┴────────┴─────┴────────┘│
│  Showing 1-15 of 50                    [< 1 2 3 4 5 >] │
└─────────────────────────────────────────────────────────┘
```

### Modal Form Design
```
┌─────────────────────────────────────────┐
│  Agregar Producto                    [X]│
├─────────────────────────────────────────┤
│  Nombre del Producto *                  │
│  [_________________________________]    │
│                                         │
│  Descripción                            │
│  [_________________________________]    │
│  [_________________________________]    │
│                                         │
│  Unidad de Negocio *                    │
│  [Seleccionar UDN ▼]                    │
│                                         │
│  ☐ Es un servicio                       │
│  ☑ Activo                               │
│                                         │
│         [Cancelar]  [Guardar Producto]  │
└─────────────────────────────────────────┘
```

## Integration Points

### CoffeeSoft Framework Integration

**Required Components:**
- `Templates` class (base class for App)
- `createTable()` - Product listing
- `createModalForm()` - Add/Edit forms
- `createfilterBar()` - Filter controls
- `swalQuestion()` - Delete confirmation
- `useFetch()` - AJAX communication

**Required Utilities:**
- `formatSpanishDate()` - Date formatting
- `evaluar()` - Number formatting
- `alert()` - User notifications

### Module Integration

**Parent Module:** Pedidos (Orders)
**Integration Method:** Tab within existing module

**Tab Configuration:**
```javascript
{
    id: "productos",
    tab: "Administrador de Productos",
    onClick: () => adminProductos.render()
}
```

**Shared Resources:**
- UDN data (from parent module)
- Session management
- Authentication state

## Deployment Considerations

**File Structure:**
```
kpi/marketing/
├── admin-productos.js
├── ctrl/
│   └── ctrl-admin-productos.php
└── mdl/
    └── mdl-admin-productos.php
```

**Dependencies:**
- CoffeeSoft framework (src/js/coffeSoft.js)
- jQuery 3.x
- TailwindCSS 2.x
- SweetAlert2
- Bootbox

**Configuration:**
- Database connection (via _Conect.php)
- Session configuration
- Error logging settings

**Migration Steps:**
1. Create database tables (producto, udn)
2. Deploy PHP files (ctrl, mdl)
3. Deploy JS file
4. Update parent module to include new tab
5. Test all CRUD operations
6. Verify permissions and access control
