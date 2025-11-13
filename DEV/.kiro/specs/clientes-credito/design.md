# Design Document - Módulo de Clientes con Movimientos a Crédito

## Overview

Sistema de gestión de cuentas a crédito para clientes en unidades de negocio, implementado siguiendo la arquitectura MVC de CoffeeSoft. El módulo permite registrar consumos, anticipos y pagos con actualización automática de saldos y vinculación al sistema de corte diario.

## Architecture

### Technology Stack
- **Frontend**: JavaScript (jQuery) + CoffeeSoft Framework + TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL (rfwsmqex_contabilidad)
- **Framework**: CoffeeSoft MVC Pattern

### File Structure
```
contabilidad/
├── captura/
│   ├── clientes.js                    # Frontend principal
│   ├── ctrl/
│   │   └── ctrl-clientes.php          # Controlador
│   └── mdl/
│       └── mdl-clientes.php           # Modelo
└── index.php                          # Punto de entrada (ya existe)
```

### Database Schema

#### Table: customer
```sql
CREATE TABLE customer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    balance DECIMAL(12,2) DEFAULT 0.00,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### Table: detail_credit_customer
```sql
CREATE TABLE detail_credit_customer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    movement_type VARCHAR(20) NOT NULL,  -- 'Consumo a crédito', 'Anticipo', 'Pago total'
    method_pay VARCHAR(100),              -- 'Efectivo', 'Banco', 'N/A'
    amount DECIMAL(15,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100),
    FOREIGN KEY (customer_id) REFERENCES customer(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

## Components and Interfaces

### Frontend Components (clientes.js)

#### Class: App (extends Templates)
```javascript
class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "clientes";
    }
    
    // Métodos principales
    render()              // Inicializa el módulo
    layout()              // Crea estructura de pestañas
    filterBar()           // Barra de filtros
    lsMovements()         // Lista movimientos de crédito
    addMovement()         // Modal nuevo movimiento
    editMovement(id)      // Modal editar movimiento
    viewDetail(id)        // Modal ver detalle
    deleteMovement(id)    // Confirmación eliminar
    jsonMovement()        // Estructura del formulario
}
```

#### Class: CustomerManager (extends App)
```javascript
class CustomerManager extends App {
    lsCustomers()         // Lista clientes
    addCustomer()         // Modal nuevo cliente
    editCustomer(id)      // Modal editar cliente
    statusCustomer(id)    // Activar/desactivar cliente
}
```

### Backend Components

#### Controller (ctrl-clientes.php)
```php
class ctrl extends mdl {
    // Inicialización
    init()                      // Carga filtros (UDN, tipos de movimiento)
    
    // Movimientos
    lsMovements()               // Lista movimientos con filtros
    getMovement()               // Obtiene detalle de movimiento
    addMovement()               // Crea nuevo movimiento
    editMovement()              // Actualiza movimiento
    deleteMovement()            // Elimina movimiento
    
    // Clientes
    lsCustomers()               // Lista clientes
    getCustomer()               // Obtiene datos de cliente
    addCustomer()               // Crea nuevo cliente
    editCustomer()              // Actualiza cliente
    statusCustomer()            // Cambia estado activo/inactivo
}
```

#### Model (mdl-clientes.php)
```php
class mdl extends CRUD {
    // Movimientos
    listMovements($filters)              // Consulta movimientos con joins
    getMovementById($id)                 // Obtiene movimiento específico
    createMovement($data)                // Inserta movimiento
    updateMovement($data)                // Actualiza movimiento
    deleteMovementById($id)              // Elimina movimiento
    
    // Clientes
    listCustomers($filters)              // Consulta clientes
    getCustomerById($id)                 // Obtiene cliente específico
    createCustomer($data)                // Inserta cliente
    updateCustomer($data)                // Actualiza cliente
    updateCustomerBalance($id, $amount)  // Actualiza saldo
    existsCustomerByName($name, $udn)    // Valida duplicados
    
    // Auxiliares
    lsUDN()                              // Lista unidades de negocio
    lsMovementTypes()                    // Lista tipos de movimiento
    lsPaymentMethods()                   // Lista formas de pago
    getCurrentDailyClosure($udn)         // Obtiene corte activo
}
```

## Data Models

### Movement Data Flow

#### Create Movement (Consumo a crédito)
```
Input: {
    customer_id: 5,
    movement_type: "Consumo a crédito",
    amount: 782.00,
    description: "Consumo del día"
}

Process:
1. Validate customer exists and is active
2. Get current daily_closure_id
3. Insert into detail_credit_customer
4. Update customer.balance += amount
5. Return new balance

Output: {
    status: 200,
    message: "Movimiento registrado",
    new_balance: 2282.00
}
```

#### Create Movement (Pago total)
```
Input: {
    customer_id: 5,
    movement_type: "Pago total",
    method_pay: "Banco",
    amount: 1500.00
}

Process:
1. Validate amount <= current balance
2. Get current daily_closure_id
3. Insert into detail_credit_customer
4. Update customer.balance -= amount
5. Return new balance

Output: {
    status: 200,
    message: "Pago registrado",
    new_balance: 0.00
}
```

### Customer Balance Calculation
```
Current Balance = Initial Balance 
                + SUM(Consumos a crédito)
                - SUM(Anticipos)
                - SUM(Pagos totales)
```

## Error Handling

### Validation Rules

#### Movement Creation
- Customer must exist and be active
- Daily closure must be active for current UDN
- Amount must be > 0
- For "Pago total": amount <= current balance
- For "Consumo a crédito": method_pay = "N/A"
- For "Anticipo" or "Pago total": method_pay required

#### Customer Management
- Name cannot be empty
- Name must be unique per UDN
- Cannot delete customer with pending balance
- Cannot deactivate customer with active movements

### Error Responses
```php
// Customer not found
['status' => 404, 'message' => 'Cliente no encontrado']

// No active daily closure
['status' => 400, 'message' => 'No hay corte diario activo']

// Insufficient balance
['status' => 400, 'message' => 'El monto excede la deuda actual']

// Duplicate customer
['status' => 409, 'message' => 'Ya existe un cliente con ese nombre']
```

## Testing Strategy

### Unit Tests
- Model CRUD operations
- Balance calculation logic
- Validation rules

### Integration Tests
- Movement creation flow
- Balance update consistency
- Daily closure linkage

### UI Tests
- Form validation
- Modal interactions
- Table filtering
- Real-time balance updates

### Test Scenarios

#### Scenario 1: Complete Credit Cycle
```
1. Create customer "Marina Chiapas" with balance 0
2. Add consumption 1500.00 → balance = 1500.00
3. Add consumption 782.00 → balance = 2282.00
4. Add advance 782.00 → balance = 1500.00
5. Add total payment 1500.00 → balance = 0.00
```

#### Scenario 2: Error Handling
```
1. Attempt payment 2000.00 with balance 1500.00 → Error
2. Attempt movement without active closure → Error
3. Attempt duplicate customer name → Error
```

## UI/UX Design

### Dashboard Layout
```
┌─────────────────────────────────────────────────────────────┐
│ 📊 Módulo de Clientes - Movimientos a Crédito              │
├─────────────────────────────────────────────────────────────┤
│ [+ Registrar nuevo movimiento] [Filtros ▼]                 │
├─────────────────────────────────────────────────────────────┤
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│ │ Consumos     │ │ Pagos/Ant.   │ │ Pagos/Ant.   │        │
│ │ $ 1,233.31   │ │ Efectivo     │ │ Banco        │        │
│ └──────────────┘ └──────────────┘ └──────────────┘        │
├─────────────────────────────────────────────────────────────┤
│ Cliente          │ Tipo          │ Forma Pago │ Monto     │
│ American Express │ Consumo       │ N/A        │ $ 419.31  │
│ API              │ Anticipo      │ Banco      │ $ 26.00   │
│ Marina Chiapas   │ Consumo       │ N/A        │ $ 782.00  │
└─────────────────────────────────────────────────────────────┘
```

### Modal: New Movement
```
┌─────────────────────────────────────────────────────────────┐
│ NUEVO MOVIMIENTO DE CRÉDITO                            [X]  │
├─────────────────────────────────────────────────────────────┤
│ Nombre del Cliente                                          │
│ [Select Customer ▼]                                         │
│                                                              │
│ Deuda actual          Tipo de movimiento                    │
│ $ 1,500.00           [Select Type ▼]                        │
│                                                              │
│ Forma de pago         Cantidad                              │
│ [Select Method ▼]    $ [0.00]                              │
│                                                              │
│ Descripción del movimiento (opcional)                       │
│ [Text area...]                                              │
│                                                              │
│              [Editar movimiento del crédito]                │
└─────────────────────────────────────────────────────────────┘
```

### Modal: View Detail
```
┌─────────────────────────────────────────────────────────────┐
│ DETALLE DEL MOVIMIENTO A CRÉDITO                       [X]  │
│ Actualizado por última vez: 01/12/2025, Por: Carolina      │
├─────────────────────────────────────────────────────────────┤
│ INFORMACIÓN DEL CLIENTE                                     │
│ Nombre del cliente: Marina Chiapas                          │
│                                                              │
│ DETALLES DEL MOVIMIENTO                                     │
│ Tipo de movimiento: Consumo a crédito                       │
│ Método de Pago: N/A (No aplica)                            │
│                                                              │
│ DESCRIPCIÓN                                                  │
│ Ninguna                                                      │
│                                                              │
│ RESUMEN FINANCIERO                                           │
│ Deuda actual:        $ 1,500.00                             │
│ Consumo a crédito:   $ 782.00                               │
│ Nueva deuda:         $ 2,282.00                             │
└─────────────────────────────────────────────────────────────┘
```

## Performance Considerations

### Database Optimization
- Index on customer_id, daily_closure_id
- Index on movement_type for filtering
- Composite index on (customer_id, created_at) for history queries

### Caching Strategy
- Cache UDN list (rarely changes)
- Cache movement types and payment methods
- Real-time balance calculation (no cache)

### Query Optimization
```sql
-- Optimized movement list query
SELECT 
    dcm.id,
    c.name as customer_name,
    dcm.movement_type,
    dcm.method_pay,
    dcm.amount,
    dcm.created_at
FROM detail_credit_customer dcm
INNER JOIN customer c ON dcm.customer_id = c.id
INNER JOIN daily_closure dc ON dcm.daily_closure_id = dc.id
WHERE dc.udn_id = ?
    AND dcm.movement_type LIKE ?
ORDER BY dcm.created_at DESC
LIMIT 50;
```

## Security Considerations

### Input Validation
- Sanitize all user inputs using `$this->util->sql()`
- Validate numeric amounts (positive, max 2 decimals)
- Validate movement types against whitelist
- Prevent SQL injection via prepared statements

### Access Control
- Verify user session before any operation
- Restrict operations to user's assigned UDN
- Log all balance modifications with user info

### Data Integrity
- Use database transactions for balance updates
- Validate daily closure is active before movements
- Prevent negative balances (except for payments)
- Audit trail via updated_by field

## Integration Points

### Daily Closure System
- Movements must link to active daily_closure_id
- Balance changes reflected in closure totals
- Closure completion locks associated movements

### UDN System
- Customers belong to specific UDN
- Movements filtered by UDN context
- Multi-UDN support via foreign keys

## Deployment Notes

### Database Migration
```sql
-- Run in order:
1. Create customer table
2. Create detail_credit_customer table
3. Add indexes
4. Insert test data (optional)
```

### Configuration
- Update database name in mdl-clientes.php: `$this->bd = "rfwsmqex_contabilidad.";`
- Verify CoffeeSoft framework files are loaded
- Ensure user has permissions on customer tables

### Rollback Plan
- Backup customer and detail_credit_customer tables
- Keep old balance values before updates
- Transaction rollback on errors
