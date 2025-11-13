# Design Document - Módulo de Cuentas de Mayor

## Overview

El módulo de Cuentas de Mayor implementa un sistema de gestión contable con arquitectura MVC utilizando el framework CoffeeSoft. El diseño se basa en el patrón establecido en los pivotes de administración, con una interfaz de pestañas que permite gestionar múltiples entidades contables (cuentas de mayor, subcuentas, tipos de compra y formas de pago) desde una única vista.

## Architecture

### Technology Stack
- **Frontend**: JavaScript (jQuery) + CoffeeSoft Framework + TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Framework**: CoffeeSoft (Clases: Templates, Components, Complements)

### Design Pattern
- **MVC Architecture**: Modelo-Vista-Controlador
- **Component-Based**: Uso de componentes reutilizables de CoffeeSoft
- **Tab-Based Navigation**: Interfaz multi-entidad con pestañas

### File Structure
```
cuentas-mayor/
├── index.php                          # Vista principal con <div id="root">
├── ctrl/
│   └── ctrl-cuentamayor.php          # Controlador principal
├── mdl/
│   └── mdl-cuentamayor.php           # Modelo de acceso a datos
└── js/
    └── cuentamayor.js                # Lógica frontend (extiende Templates)
```

## Components and Interfaces

### Frontend Components (cuentamayor.js)

#### Class Structure
```javascript
class App extends Templates {
    constructor(link, div_modulo)
    PROJECT_NAME = "cuentamayor"
}

class SubAccount extends App {
    // Gestión de subcuentas de mayor
}

class PurchaseType extends App {
    // Gestión de tipos de compra
}

class PaymentMethod extends App {
    // Gestión de formas de pago
}
```

#### Main Methods

**App Class (Cuenta de Mayor)**
- `render()`: Inicializa el módulo completo
- `layout()`: Crea estructura con primaryLayout y tabLayout
- `filterBar()`: Filtro de unidad de negocio
- `lsCuentaMayor()`: Lista cuentas de mayor con createTable
- `addCuentaMayor()`: Modal para agregar cuenta (createModalForm)
- `editCuentaMayor(id)`: Modal para editar cuenta (createModalForm)
- `statusCuentaMayor(id, active)`: Cambiar estado con swalQuestion
- `jsonCuentaMayor()`: Estructura del formulario

**SubAccount Class**
- `lsSubcuenta()`: Lista subcuentas
- `filterBarSubcuenta()`: Filtros específicos
- `addSubcuenta()`: Crear subcuenta
- `editSubcuenta(id)`: Editar subcuenta
- `statusSubcuenta(id, active)`: Cambiar estado

**PurchaseType Class**
- `lsTipoCompra()`: Lista tipos de compra
- `filterBarTipoCompra()`: Filtros específicos
- `addTipoCompra()`: Crear tipo
- `editTipoCompra(id)`: Editar tipo
- `statusTipoCompra(id, active)`: Cambiar estado

**PaymentMethod Class**
- `lsFormaPago()`: Lista formas de pago
- `filterBarFormaPago()`: Filtros específicos
- `addFormaPago()`: Crear forma de pago
- `editFormaPago(id)`: Editar forma de pago
- `statusFormaPago(id, active)`: Cambiar estado

### CoffeeSoft Components Used

1. **primaryLayout**: Estructura base con filterBar y container
2. **tabLayout**: Navegación entre entidades (4 pestañas)
3. **createfilterBar**: Filtro de unidad de negocio
4. **createTable**: Tablas con datos del backend
5. **createModalForm**: Formularios de agregar/editar
6. **swalQuestion**: Confirmaciones de activar/desactivar

### Backend Components

#### Controller (ctrl-cuentamayor.php)

**Class Structure**
```php
class ctrl extends mdl {
    // Métodos del controlador
}
```

**Methods**
- `init()`: Retorna listas para filtros (unidades de negocio)
- `lsCuentaMayor()`: Lista cuentas de mayor con formato para tabla
- `getCuentaMayor()`: Obtiene una cuenta por ID
- `addCuentaMayor()`: Crea nueva cuenta con validación de duplicados
- `editCuentaMayor()`: Actualiza cuenta existente
- `statusCuentaMayor()`: Cambia estado activo/inactivo
- `lsSubcuenta()`: Lista subcuentas
- `addSubcuenta()`: Crea subcuenta
- `editSubcuenta()`: Actualiza subcuenta
- `statusSubcuenta()`: Cambia estado de subcuenta
- `lsTipoCompra()`: Lista tipos de compra
- `addTipoCompra()`: Crea tipo de compra
- `editTipoCompra()`: Actualiza tipo de compra
- `statusTipoCompra()`: Cambia estado de tipo de compra
- `lsFormaPago()`: Lista formas de pago
- `addFormaPago()`: Crea forma de pago
- `editFormaPago()`: Actualiza forma de pago
- `statusFormaPago()`: Cambia estado de forma de pago

**Helper Functions**
- `renderStatus($active)`: Genera badge HTML para estado
- `dropdown($id, $active)`: Genera opciones de acciones

#### Model (mdl-cuentamayor.php)

**Class Structure**
```php
class mdl extends CRUD {
    protected $util;
    public $bd;
}
```

**Methods - Cuenta de Mayor (product_class)**
- `listProductClass($array)`: Consulta cuentas con JOIN a unidades de negocio
- `getProductClassById($id)`: Obtiene cuenta específica
- `existsProductClassByName($array)`: Valida duplicados
- `createProductClass($array)`: Inserta nueva cuenta
- `updateProductClass($array)`: Actualiza cuenta

**Methods - Subcuenta (product)**
- `listProduct($array)`: Consulta subcuentas con JOIN a product_class
- `getProductById($id)`: Obtiene subcuenta específica
- `existsProductByName($array)`: Valida duplicados
- `createProduct($array)`: Inserta subcuenta
- `updateProduct($array)`: Actualiza subcuenta

**Methods - Tipo de Compra**
- `listTipoCompra($array)`: Consulta tipos de compra
- `getTipoCompraById($id)`: Obtiene tipo específico
- `existsTipoCompraByName($array)`: Valida duplicados
- `createTipoCompra($array)`: Inserta tipo
- `updateTipoCompra($array)`: Actualiza tipo

**Methods - Forma de Pago**
- `listFormaPago($array)`: Consulta formas de pago
- `getFormaPagoById($id)`: Obtiene forma específica
- `existsFormaPagoByName($array)`: Valida duplicados
- `createFormaPago($array)`: Inserta forma
- `updateFormaPago($array)`: Actualiza forma

**Utility Methods**
- `lsUDN()`: Lista unidades de negocio para filtros

## Data Models

### Database Schema

#### Table: product_class (Cuenta de Mayor)
```sql
CREATE TABLE product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES unidades_negocio(id),
    UNIQUE KEY unique_name_udn (name, udn_id)
);
```

#### Table: product (Subcuenta de Mayor)
```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clase_insumo_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (clase_insumo_id) REFERENCES product_class(id),
    UNIQUE KEY unique_name_class (name, clase_insumo_id)
);
```

#### Table: tipos_compra (Tipos de Compra)
```sql
CREATE TABLE tipos_compra (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES unidades_negocio(id),
    UNIQUE KEY unique_nombre_udn (nombre, udn_id)
);
```

#### Table: formas_pago (Formas de Pago)
```sql
CREATE TABLE formas_pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES unidades_negocio(id),
    UNIQUE KEY unique_nombre_udn (nombre, udn_id)
);
```

### Data Flow

#### Create Flow
1. User clicks "Agregar nueva cuenta de mayor"
2. Frontend opens modal with createModalForm
3. User fills form with UDN (read-only) and nombre
4. Frontend validates and sends POST to ctrl-cuentamayor.php (opc: 'addCuentaMayor')
5. Controller validates duplicate with existsCuentaMayorByName()
6. If valid, controller calls createCuentaMayor()
7. Model executes _Insert with prepared statement
8. Controller returns status 200 or error
9. Frontend closes modal and refreshes table

#### Update Flow
1. User clicks edit icon
2. Frontend calls getCuentaMayor() to fetch data
3. Modal opens with autofill data
4. User modifies nombre field
5. Frontend sends POST (opc: 'editCuentaMayor')
6. Controller calls updateCuentaMayor()
7. Model executes _Update
8. Frontend refreshes table

#### Status Change Flow
1. User clicks toggle switch
2. Frontend shows swalQuestion with confirmation message
3. User confirms action
4. Frontend sends POST (opc: 'statusCuentaMayor', active: 0/1)
5. Controller calls updateCuentaMayor() with new status
6. Model updates active field
7. Frontend refreshes table
8. Historical records remain unchanged

## Error Handling

### Frontend Validation
- Required field validation (nombre)
- Empty string validation
- Form submission prevention on invalid data

### Backend Validation
- Duplicate name validation per UDN
- SQL injection prevention (prepared statements)
- Data type validation
- Foreign key constraint validation

### Error Messages
- **Duplicate**: "Ya existe una cuenta con ese nombre en esta unidad de negocio"
- **Empty field**: "El nombre de la cuenta es obligatorio"
- **Database error**: "Error al guardar la información. Intente nuevamente"
- **Not found**: "No se encontró la cuenta solicitada"

### Status Messages
- **Activate**: "La cuenta mayor ya estará disponible, para la captura de información"
- **Deactivate**: "La cuenta mayor ya no estará disponible, pero seguirá reflejándose en los registros contables"

## Testing Strategy

### Unit Tests
- Model methods (CRUD operations)
- Controller validation logic
- Duplicate detection
- Status change logic

### Integration Tests
- Complete create flow (frontend → controller → model → database)
- Complete update flow
- Status change flow
- Filter by UDN functionality

### UI Tests
- Tab navigation
- Modal open/close
- Form validation
- Table refresh after operations
- Filter functionality

### Test Cases

#### TC-001: Create Major Account
- **Given**: User is on "Cuenta de mayor" tab
- **When**: User clicks "Agregar nueva cuenta de mayor"
- **Then**: Modal opens with UDN field (read-only) and nombre field (editable)

#### TC-002: Duplicate Validation
- **Given**: Account "Activo fijo" exists for UDN "Fogaza"
- **When**: User tries to create another "Activo fijo" for same UDN
- **Then**: System shows error and prevents creation

#### TC-003: Edit Account
- **Given**: Account exists with id=1
- **When**: User clicks edit icon and changes nombre
- **Then**: System updates record and refreshes table

#### TC-004: Deactivate Account
- **Given**: Active account exists
- **When**: User clicks toggle to deactivate
- **Then**: System shows confirmation modal with message about historical records

#### TC-005: Filter by UDN
- **Given**: Multiple accounts exist for different UDNs
- **When**: User selects UDN "Fogaza" in filter
- **Then**: Table shows only accounts for "Fogaza"

### Performance Tests
- Table load time with 100+ records
- Filter response time
- Modal open/close speed
- Database query optimization

## Security Considerations

### Authentication
- User must be authenticated to access module
- Session validation on each request

### Authorization
- Only administrators can edit and change status
- Regular users can only view

### Data Protection
- SQL injection prevention (prepared statements)
- XSS prevention (HTML escaping)
- CSRF token validation
- Input sanitization

### Audit Trail
- Log all create/update/status change operations
- Track user who made changes
- Track timestamp of changes

## Design Decisions

### Why Tab-Based Interface?
- Allows managing multiple related entities in single view
- Reduces navigation complexity
- Follows established pattern in CoffeeSoft projects
- Improves user experience

### Why Soft Delete (active flag)?
- Preserves historical accounting records
- Allows reactivation if needed
- Maintains referential integrity
- Complies with accounting regulations

### Why Duplicate Validation per UDN?
- Same account name can exist in different business units
- Prevents confusion within same UDN
- Maintains data integrity

### Why Read-Only UDN in Forms?
- Prevents accidental changes to business unit
- Maintains data consistency
- UDN is determined by user's context

## Future Enhancements

### Phase 2 Features
- Bulk import/export of accounts
- Account hierarchy visualization
- Advanced search and filtering
- Account usage reports
- Audit log viewer

### Phase 3 Features
- Account templates
- Multi-language support
- Custom fields per UDN
- Integration with external accounting systems
