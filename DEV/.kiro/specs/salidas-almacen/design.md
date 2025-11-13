# Design Document - Módulo de Salidas de Almacén

## Overview

El módulo de Salidas de Almacén es un sistema de gestión de inventario que permite registrar, visualizar, editar y eliminar movimientos de salida de productos del almacén. Se integra dentro del sistema de contabilidad CoffeeSoft y sigue la arquitectura MVC con el framework CoffeeSoft para el frontend.

### Key Features

- Dashboard con resumen total de salidas
- Tabla interactiva con paginación y filtros
- Formularios modales para registro y edición
- Confirmación de eliminación con borrado lógico
- Cálculo automático de totales
- Integración con sistema de insumos/almacenes

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend Layer                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │  salidas-almacen.js (extends Templates)          │  │
│  │  - App (main controller)                         │  │
│  │  - DashboardWarehouse (summary display)          │  │
│  │  - AdminWarehouse (table management)             │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↓ AJAX (useFetch)
┌─────────────────────────────────────────────────────────┐
│                   Controller Layer                       │
│  ┌──────────────────────────────────────────────────┐  │
│  │  ctrl-salidas-almacen.php                        │  │
│  │  - init()                                        │  │
│  │  - lsWarehouseOutputs()                          │  │
│  │  - addWarehouseOutput()                          │  │
│  │  - editWarehouseOutput()                         │  │
│  │  - getWarehouseOutput()                          │  │
│  │  - deleteWarehouseOutput()                       │  │
│  │  - getTotalOutputs()                             │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↓ SQL Queries
┌─────────────────────────────────────────────────────────┐
│                     Model Layer                          │
│  ┌──────────────────────────────────────────────────┐  │
│  │  mdl-salidas-almacen.php (extends CRUD)          │  │
│  │  - listWarehouseOutputs()                        │  │
│  │  - createWarehouseOutput()                       │  │
│  │  - updateWarehouseOutput()                       │  │
│  │  - getWarehouseOutputById()                      │  │
│  │  - deleteWarehouseOutputById()                   │  │
│  │  - calculateTotalOutputs()                       │  │
│  │  - lsWarehouses()                                │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────┐
│                    Database Layer                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │  warehouse_output table                          │  │
│  │  insumo table (reference)                        │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```


### Technology Stack

- **Frontend**: jQuery, CoffeeSoft Framework, TailwindCSS
- **Backend**: PHP 7.4+, Custom CRUD Class
- **Database**: MySQL
- **UI Components**: CoffeeSoft Components (createTable, createModalForm, swalQuestion)
- **AJAX**: useFetch (Fetch API wrapper)

## Components and Interfaces

### Frontend Components

#### 1. App Class (Main Controller)

```javascript
class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "warehouseOutputs";
    }
    
    // Methods:
    // - render(): Initialize module
    // - layout(): Create primary layout structure
    // - filterBar(): Setup filter controls
}
```

**Responsibilities:**
- Initialize module and coordinate sub-components
- Manage layout and navigation
- Handle global state

#### 2. DashboardWarehouse Class

```javascript
class DashboardWarehouse extends App {
    // Methods:
    // - renderDashboard(): Display summary card with total
    // - calculateTotal(): Fetch and display total amount
    // - updateTotal(): Refresh total after CRUD operations
}
```

**Responsibilities:**
- Display total sum of warehouse outputs
- Update totals dynamically
- Format currency display

#### 3. AdminWarehouse Class

```javascript
class AdminWarehouse extends App {
    // Methods:
    // - lsWarehouseOutputs(): Display table with outputs
    // - addWarehouseOutput(): Show modal form for new record
    // - editWarehouseOutput(id): Show modal form with existing data
    // - deleteWarehouseOutput(id): Confirm and delete record
    // - jsonWarehouseOutput(): Define form fields structure
}
```

**Responsibilities:**
- Manage CRUD operations
- Handle table display and pagination
- Manage modal forms
- Validate user input

### Backend Components

#### 1. Controller (ctrl-salidas-almacen.php)

```php
class ctrl extends mdl {
    function init()
    function lsWarehouseOutputs()
    function addWarehouseOutput()
    function editWarehouseOutput()
    function getWarehouseOutput()
    function deleteWarehouseOutput()
    function getTotalOutputs()
}
```

**Responsibilities:**
- Route requests to appropriate model methods
- Format data for frontend consumption
- Handle business logic validation
- Return standardized JSON responses

#### 2. Model (mdl-salidas-almacen.php)

```php
class mdl extends CRUD {
    function listWarehouseOutputs($filters)
    function createWarehouseOutput($data)
    function updateWarehouseOutput($data)
    function getWarehouseOutputById($id)
    function deleteWarehouseOutputById($id)
    function calculateTotalOutputs($filters)
    function lsWarehouses()
}
```

**Responsibilities:**
- Execute database queries
- Data validation at DB level
- Handle relationships with insumo table
- Aggregate calculations


## Data Models

### Database Schema

#### warehouse_output Table

```sql
CREATE TABLE warehouse_output (
    id INT PRIMARY KEY AUTO_INCREMENT,
    insumo_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    operation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (insumo_id) REFERENCES insumo(id)
);
```

**Field Descriptions:**
- `id`: Primary key, auto-increment
- `insumo_id`: Foreign key to insumo table (warehouse/category)
- `amount`: Monetary amount of the output
- `description`: Optional text description
- `operation_date`: Timestamp of the operation
- `active`: Soft delete flag (1=active, 0=deleted)

#### insumo Table (Reference)

```sql
CREATE TABLE insumo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    -- other fields...
);
```

### Data Transfer Objects

#### WarehouseOutput DTO

```javascript
{
    id: number,
    insumo_id: number,
    warehouse_name: string,
    amount: decimal,
    description: string,
    operation_date: datetime,
    active: boolean
}
```

#### TableRow DTO (Frontend Display)

```javascript
{
    id: number,
    Almacén: string,
    Monto: string (formatted),
    Descripción: string,
    dropdown: array (action buttons)
}
```

## Error Handling

### Frontend Error Handling

1. **Form Validation Errors**
   - Display inline validation messages
   - Prevent form submission until valid
   - Use CoffeeSoft validation_form plugin

2. **AJAX Request Errors**
   ```javascript
   useFetch({
       url: api,
       data: { opc: 'addWarehouseOutput' },
       success: (response) => {
           if (response.status === 200) {
               alert({ icon: "success", text: response.message });
           } else {
               alert({ icon: "error", text: response.message });
           }
       },
       error: (error) => {
           alert({ icon: "error", text: "Error de conexión" });
       }
   });
   ```

3. **User Feedback**
   - Success messages with SweetAlert2
   - Error messages with specific details
   - Loading states during operations

### Backend Error Handling

1. **Database Errors**
   ```php
   try {
       $result = $this->createWarehouseOutput($data);
       return ['status' => 200, 'message' => 'Registro exitoso'];
   } catch (Exception $e) {
       return ['status' => 500, 'message' => 'Error al guardar'];
   }
   ```

2. **Validation Errors**
   ```php
   if (empty($_POST['insumo_id']) || empty($_POST['amount'])) {
       return ['status' => 400, 'message' => 'Campos requeridos faltantes'];
   }
   ```

3. **Response Format**
   ```php
   return [
       'status' => 200|400|500,
       'message' => 'Descriptive message',
       'data' => [] // optional
   ];
   ```


## Testing Strategy

### Unit Testing

#### Frontend Tests

1. **Component Rendering**
   - Test App.render() creates correct layout
   - Test DashboardWarehouse displays total correctly
   - Test AdminWarehouse renders table with data

2. **Form Validation**
   - Test required field validation
   - Test numeric amount validation
   - Test form submission with valid data

3. **CRUD Operations**
   - Test addWarehouseOutput() sends correct data
   - Test editWarehouseOutput() pre-fills form
   - Test deleteWarehouseOutput() shows confirmation

#### Backend Tests

1. **Controller Methods**
   - Test init() returns warehouse list
   - Test lsWarehouseOutputs() formats data correctly
   - Test addWarehouseOutput() validates input
   - Test editWarehouseOutput() updates record
   - Test deleteWarehouseOutput() sets active=0

2. **Model Methods**
   - Test listWarehouseOutputs() returns active records only
   - Test createWarehouseOutput() inserts data
   - Test updateWarehouseOutput() modifies existing record
   - Test calculateTotalOutputs() sums amounts correctly
   - Test foreign key constraints with insumo table

### Integration Testing

1. **End-to-End Workflows**
   - Test complete add workflow: form → controller → model → database
   - Test complete edit workflow: load data → modify → save
   - Test complete delete workflow: confirm → soft delete → refresh table

2. **Database Integration**
   - Test CRUD operations persist correctly
   - Test foreign key relationships work
   - Test soft delete doesn't affect totals

3. **UI Integration**
   - Test table updates after add/edit/delete
   - Test total recalculates after operations
   - Test modal forms open/close correctly

### Manual Testing Checklist

- [ ] Load module and verify table displays
- [ ] Verify total sum is correct
- [ ] Click "Registrar nueva salida" and fill form
- [ ] Submit form and verify success message
- [ ] Verify new record appears in table
- [ ] Verify total updates correctly
- [ ] Click edit icon and verify form pre-fills
- [ ] Modify data and save
- [ ] Verify changes appear in table
- [ ] Click delete icon and confirm
- [ ] Verify record is removed from table
- [ ] Verify total updates after deletion
- [ ] Test with empty database
- [ ] Test with large dataset (100+ records)
- [ ] Test form validation with invalid data
- [ ] Test concurrent operations

## Design Decisions and Rationales

### 1. Soft Delete vs Hard Delete

**Decision:** Use soft delete (active flag) instead of physical deletion

**Rationale:**
- Maintains audit trail of all operations
- Allows recovery of accidentally deleted records
- Preserves referential integrity
- Common accounting practice for financial records

### 2. Modal Forms vs Inline Editing

**Decision:** Use modal forms for add/edit operations

**Rationale:**
- Consistent with existing CoffeeSoft patterns (pivote admin)
- Focuses user attention on single task
- Prevents accidental edits
- Better mobile experience

### 3. Client-Side vs Server-Side Total Calculation

**Decision:** Calculate totals on server-side

**Rationale:**
- Ensures accuracy with database state
- Handles large datasets efficiently
- Prevents client-side manipulation
- Single source of truth

### 4. Separate Classes vs Single Class

**Decision:** Use separate classes (App, DashboardWarehouse, AdminWarehouse)

**Rationale:**
- Follows CoffeeSoft framework patterns
- Separation of concerns
- Easier to maintain and test
- Allows independent development of features

### 5. Foreign Key to insumo Table

**Decision:** Use insumo_id foreign key instead of storing warehouse name

**Rationale:**
- Maintains data normalization
- Allows centralized warehouse management
- Prevents data inconsistency
- Enables future reporting across modules

### 6. Decimal(10,2) for Amount

**Decision:** Use DECIMAL(10,2) instead of FLOAT

**Rationale:**
- Precise financial calculations
- Avoids floating-point rounding errors
- Standard for monetary values
- Supports amounts up to 99,999,999.99

## Security Considerations

1. **SQL Injection Prevention**
   - Use prepared statements in all queries
   - Validate and sanitize all inputs
   - Use CRUD class methods with parameterized queries

2. **XSS Prevention**
   - Escape output in HTML rendering
   - Use jQuery text() instead of html() for user data
   - Sanitize description field

3. **CSRF Protection**
   - Implement session validation
   - Verify user authentication before operations
   - Use POST for all data modifications

4. **Access Control**
   - Verify user permissions before displaying module
   - Check authorization for each CRUD operation
   - Log all modifications with user ID

5. **Data Validation**
   - Validate amount is positive number
   - Validate insumo_id exists in database
   - Limit description length
   - Validate date formats

## Performance Considerations

1. **Database Optimization**
   - Index on insumo_id for faster joins
   - Index on active flag for filtered queries
   - Index on operation_date for date-based reports

2. **Frontend Optimization**
   - Use DataTables pagination for large datasets
   - Lazy load warehouse list in select dropdown
   - Cache total calculation result

3. **Query Optimization**
   - Use single query for list with JOIN instead of multiple queries
   - Calculate total in same query as list when possible
   - Limit result set with pagination

4. **Caching Strategy**
   - Cache warehouse list (insumo) in frontend
   - Invalidate cache on add/edit/delete operations
   - Use browser localStorage for user preferences
