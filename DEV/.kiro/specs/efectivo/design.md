# Design Document - Módulo de Efectivo

## Overview

El módulo de Efectivo es un submódulo del sistema de contabilidad que gestiona operaciones de efectivo mediante una arquitectura MVC basada en el framework CoffeeSoft. El sistema permite registrar movimientos, realizar cierres de caja y controlar el flujo de efectivo por unidad de negocio.

## Architecture

### Technology Stack
- **Frontend**: JavaScript (jQuery) + CoffeeSoft Framework + TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Framework**: CoffeeSoft (Complements → Components → Templates)

### File Structure
```
contabilidad/administrador/
├── efectivo.js              # Frontend principal (extiende Templates)
├── ctrl/
│   └── ctrl-efectivo.php    # Controlador PHP
└── mdl/
    └── mdl-efectivo.php     # Modelo PHP (extiende CRUD)
```

### Design Pattern
- **MVC Architecture**: Separación clara entre Modelo, Vista y Controlador
- **Component-Based**: Uso de componentes reutilizables de CoffeeSoft
- **Pivot-Based**: Basado en el pivote "admin" existente

## Components and Interfaces

### Frontend Components (efectivo.js)

#### Class: App (extends Templates)
Clase principal que gestiona la interfaz del módulo de efectivo.

**Properties:**
- `PROJECT_NAME`: "efectivo"
- `_link`: "ctrl/ctrl-efectivo.php"
- `_div_modulo`: "root"

**Methods:**
- `render()`: Inicializa el layout y componentes
- `layout()`: Crea la estructura de pestañas (Conceptos, Movimientos)
- `filterBar()`: Barra de filtros con UDN, fecha y estado
- `lsConceptos()`: Lista conceptos de efectivo en tabla
- `addConcepto()`: Modal para agregar nuevo concepto
- `editConcepto(id)`: Modal para editar concepto existente
- `statusConcepto(id, active)`: Activar/desactivar concepto
- `jsonConcepto()`: Estructura del formulario de conceptos

#### Class: CashMovement (extends App)
Clase para gestionar movimientos de efectivo.

**Methods:**
- `lsMovimientos()`: Lista movimientos de efectivo
- `addMovimiento()`: Modal para registrar nuevo movimiento
- `editMovimiento(id)`: Modal para editar movimiento
- `closeCashFlow()`: Realizar cierre de efectivo
- `jsonMovimiento()`: Estructura del formulario de movimientos

### Backend Components

#### Controlador (ctrl-efectivo.php)

**Class: ctrl (extends mdl)**

**Methods:**
- `init()`: Inicializa filtros (UDN, tipos de operación, estados)
- `lsConceptos()`: Lista conceptos con filtros aplicados
- `getConcepto()`: Obtiene un concepto por ID
- `addConcepto()`: Crea nuevo concepto de efectivo
- `editConcepto()`: Actualiza concepto existente
- `statusConcepto()`: Cambia estado activo/inactivo
- `lsMovimientos()`: Lista movimientos de efectivo
- `getMovimiento()`: Obtiene un movimiento por ID
- `addMovimiento()`: Registra nuevo movimiento
- `editMovimiento()`: Actualiza movimiento existente
- `closeCash()`: Realiza cierre de efectivo

**Helper Functions:**
- `renderStatus($status)`: Renderiza badge de estado
- `dropdown($id, $status)`: Genera menú de acciones

#### Modelo (mdl-efectivo.php)

**Class: mdl (extends CRUD)**

**Properties:**
- `$bd`: Nombre de la base de datos
- `$util`: Instancia de Utileria

**Methods - Conceptos:**
- `listConceptos($array)`: Lista conceptos con filtros
- `getConceptoById($id)`: Obtiene concepto por ID
- `createConcepto($array)`: Inserta nuevo concepto
- `updateConcepto($array)`: Actualiza concepto
- `existsConceptoByName($array)`: Valida nombre único

**Methods - Movimientos:**
- `listMovimientos($array)`: Lista movimientos con filtros
- `getMovimientoById($id)`: Obtiene movimiento por ID
- `createMovimiento($array)`: Inserta nuevo movimiento
- `updateMovimiento($array)`: Actualiza movimiento
- `getAvailableAmount($udn_id)`: Obtiene monto disponible

**Methods - Filtros:**
- `lsUDN()`: Lista unidades de negocio
- `lsOperationType()`: Lista tipos de operación
- `lsStatus()`: Lista estados

## Data Models

### Database Schema

#### Table: cash_concept
```sql
CREATE TABLE cash_concept (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    operation_type ENUM('suma', 'resta') NOT NULL,
    description TEXT,
    active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    UNIQUE KEY unique_concept (udn_id, name)
);
```

#### Table: cash_movement
```sql
CREATE TABLE cash_movement (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    concept_id INT NOT NULL,
    movement_type ENUM('entrada', 'salida') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    user_id INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (concept_id) REFERENCES cash_concept(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### Table: cash_closure
```sql
CREATE TABLE cash_closure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    closure_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    notes TEXT,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Data Flow

#### Add Cash Concept Flow
```
User Input → Frontend Validation → createModalForm() → 
POST {opc: 'addConcepto'} → ctrl-efectivo.php → 
existsConceptoByName() → createConcepto() → 
Response {status: 200/409} → Alert → Refresh Table
```

#### Add Cash Movement Flow
```
User Input → Frontend Validation → createModalForm() → 
POST {opc: 'addMovimiento'} → ctrl-efectivo.php → 
getAvailableAmount() → createMovimiento() → 
Update Available Amount → Response {status: 200} → 
Alert → Refresh Table
```

#### Cash Closure Flow
```
User Click → swalQuestion() → Confirm → 
POST {opc: 'closeCash'} → ctrl-efectivo.php → 
Calculate Total → createClosure() → 
Lock Movements → Response {status: 200} → 
Alert → Refresh Interface
```

## Error Handling

### Frontend Validation
- **Amount Validation**: Must be numeric and greater than 0
- **Required Fields**: All mandatory fields must be filled
- **Duplicate Names**: Check before submission

### Backend Validation
- **Duplicate Concept**: Return status 409 with message
- **Invalid Amount**: Return status 400 with validation error
- **Database Errors**: Return status 500 with generic error message
- **Permission Errors**: Return status 403 with access denied message

### Error Messages (Spanish)
```javascript
{
    409: "Ya existe un concepto con ese nombre.",
    400: "El monto debe ser mayor a 0.",
    500: "Error al procesar la solicitud.",
    403: "No tienes permisos para realizar esta acción."
}
```

## Testing Strategy

### Unit Tests
- **Model Tests**: Validate CRUD operations for concepts and movements
- **Controller Tests**: Test all endpoints with valid/invalid data
- **Validation Tests**: Test amount validation, duplicate detection

### Integration Tests
- **Complete Flow**: Test full cycle from UI to database
- **Cash Closure**: Test closure process and movement locking
- **Status Toggle**: Test activation/deactivation of concepts

### UI Tests
- **Form Validation**: Test all form validations
- **Table Rendering**: Test table display with different data sets
- **Modal Behavior**: Test modal open/close and data loading

### Test Data
```javascript
// Valid Concept
{
    udn_id: 1,
    name: "Efectivo Ventas",
    operation_type: "suma",
    description: "Efectivo recibido por ventas"
}

// Valid Movement
{
    udn_id: 1,
    concept_id: 1,
    movement_type: "entrada",
    amount: 1500.00,
    description: "Venta del día"
}
```

## UI/UX Design

### Layout Structure
```
┌─────────────────────────────────────────────────┐
│ 💵 Administrador de Efectivo                    │
├─────────────────────────────────────────────────┤
│ [Conceptos] [Movimientos]                       │
├─────────────────────────────────────────────────┤
│ Filtros: [UDN ▼] [Estado ▼] [+ Nuevo]          │
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │ Concepto    │ Tipo      │ Estado │ Acciones │ │
│ ├─────────────────────────────────────────────┤ │
│ │ Efectivo    │ Suma      │ Activo │ [✏️] [🔴] │ │
│ │ Retiro      │ Resta     │ Activo │ [✏️] [🔴] │ │
│ └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

### Color Scheme (TailwindCSS)
- **Primary**: `#103B60` (Azul corporativo)
- **Success**: `#8CC63F` (Verde acción)
- **Background**: `#1F2A37` (Fondo oscuro)
- **Text**: `#FFFFFF` (Texto claro)
- **Border**: `#EAEAEA` (Gris claro)

### Component Themes
- **Tables**: `theme: 'corporativo'`
- **Forms**: `theme: 'light'`
- **Modals**: Bootbox con estilos personalizados

## Security Considerations

### Input Sanitization
- All user inputs sanitized using `$this->util->sql()`
- SQL injection prevention through prepared statements
- XSS prevention through proper escaping

### Access Control
- User permissions validated on each operation
- UDN-based access restrictions
- Session validation on all requests

### Data Validation
- Server-side validation for all inputs
- Amount validation (numeric, positive)
- Unique constraint enforcement

## Performance Optimization

### Database Optimization
- Indexes on foreign keys (udn_id, concept_id, user_id)
- Unique index on (udn_id, name) for concepts
- Efficient queries using CRUD methods

### Frontend Optimization
- Lazy loading of tables with pagination
- Debounced search inputs
- Cached filter data (UDN, types)

### Caching Strategy
- Cache UDN list in session
- Cache operation types (static data)
- Refresh cache on data changes

## Deployment Considerations

### File Locations
```
/contabilidad/administrador/
├── efectivo.js
├── ctrl/ctrl-efectivo.php
└── mdl/mdl-efectivo.php
```

### Database Migration
```sql
-- Run migration script to create tables
-- Add foreign key constraints
-- Create indexes
-- Insert default operation types
```

### Configuration
- Update routing in main application
- Add menu entry for "Efectivo" module
- Configure permissions for user roles

## Future Enhancements

### Phase 2 Features
- Export cash movements to Excel/PDF
- Cash flow reports and analytics
- Multi-currency support
- Automated reconciliation
- Email notifications for closures

### Scalability
- Support for multiple business units
- Historical data archiving
- Advanced filtering and search
- Audit trail for all operations
