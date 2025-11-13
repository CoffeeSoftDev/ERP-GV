# Design Document - Módulo de Clientes

## Overview

El Módulo de Clientes es un sistema de gestión de créditos integrado al ERP CoffeeSoft. Utiliza arquitectura MVC con frontend JavaScript (jQuery + TailwindCSS), controladores PHP y modelos con acceso a base de datos MySQL. El sistema se sincroniza en tiempo real con el módulo de Ventas y soporta múltiples niveles de acceso.

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  clientes.js │  │ Templates    │  │ Components   │     │
│  │  (App Class) │──│ (CoffeeSoft) │──│ (CoffeeSoft) │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer (PHP)                    │
│  ┌──────────────────────────────────────────────────────┐  │
│  │           ctrl-clientes.php                          │  │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │  │
│  │  │ init() │ │  ls()  │ │ add()  │ │ edit() │       │  │
│  │  └────────┘ └────────┘ └────────┘ └────────┘       │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                     Model Layer (PHP)                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │            mdl-clientes.php                          │  │
│  │  ┌──────────────┐ ┌──────────────┐ ┌─────────────┐ │  │
│  │  │ listMovements│ │createMovement│ │updateBalance│ │  │
│  │  └──────────────┘ └──────────────┘ └─────────────┘ │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer (MySQL)                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   clients    │  │  movements   │  │  audit_log   │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
```

### Integration with Sales Module

```
┌─────────────────┐         ┌─────────────────┐
│  Sales Module   │◄───────►│ Clients Module  │
│                 │  Sync   │                 │
│ - Daily totals  │  Real   │ - Consumptions  │
│ - Transactions  │  Time   │ - Payments      │
└─────────────────┘         └─────────────────┘
```

## Components and Interfaces

### Frontend Components (clientes.js)

#### Class: App (extends Templates)

**Purpose:** Main application controller for the Clients module

**Properties:**
- `PROJECT_NAME`: "clientes"
- `_link`: "ctrl/ctrl-clientes.php"
- `_div_modulo`: "root"

**Methods:**

```javascript
// Initialization
init()                    // Initialize module
render()                  // Render main layout
layout()                  // Create primary layout with tabs

// Filter Bar
filterBarMovimientos()    // Create filter bar for movements table
filterBarConcentrado()    // Create filter bar for consolidated report

// Data Display
lsMovimientos()          // List daily movements
lsConcentrado()          // Show consolidated report by client

// CRUD Operations
addMovimiento()          // Show form to add new movement
editMovimiento(id)       // Show form to edit movement
viewMovimiento(id)       // Show movement details
deleteMovimiento(id)     // Delete movement with confirmation

// Form Configuration
jsonMovimiento()         // Return JSON structure for movement form

// Utilities
calculateNewBalance()    // Calculate new balance after movement
updateDashboardTotals()  // Update dashboard cards
```

#### Tab Structure

```javascript
tabLayout({
    json: [
        { id: "dashboard", tab: "Dashboard", active: true },
        { id: "movimientos", tab: "Movimientos" },
        { id: "concentrado", tab: "Concentrado" }
    ]
})
```

### Backend Components

#### Controller: ctrl-clientes.php

**Class:** ctrl (extends mdl)

**Methods:**

```php
// Initialization
init()                          // Load filters (clients, movement types, payment methods)

// Data Retrieval
ls()                           // List movements for selected date
lsConcentrado()                // Get consolidated report by date range

// CRUD Operations
addMovimiento()                // Create new movement
editMovimiento()               // Update existing movement
getMovimiento()                // Get movement by ID
deleteMovimiento()             // Delete movement (soft delete)

// Business Logic
calculateBalance($clientId)    // Calculate current client balance
validateMovementType()         // Validate movement type and payment method
syncWithSales()               // Sync totals with sales module

// Utilities
dropdown($id, $status)        // Generate action buttons
renderMovementType($type)     // Render movement type badge
renderPaymentMethod($method)  // Render payment method badge
```

#### Model: mdl-clientes.php

**Class:** mdl (extends CRUD)

**Properties:**
- `$bd`: "rfwsmqex_contabilidad."
- `$util`: Utileria instance

**Methods:**

```php
// Client Management
listClients($filters)                    // Get active clients
getClientById($id)                       // Get client details
getClientBalance($clientId)              // Get current balance

// Movement Management
listMovements($filters)                  // Get movements by filters
createMovement($data)                    // Insert new movement
updateMovement($data)                    // Update movement
deleteMovementById($id)                  // Delete movement
getMovementById($id)                     // Get movement details

// Balance Calculations
calculateClientBalance($clientId, $date) // Calculate balance at date
getConsolidatedReport($filters)          // Get consolidated data

// Filters
lsMovementTypes()                        // Get movement types
lsPaymentMethods()                       // Get payment methods

// Audit
logMovementAction($data)                 // Log movement actions
```

## Data Models

### Database Schema

#### Table: clients

```sql
CREATE TABLE clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    udn_id INT NOT NULL,
    current_balance DECIMAL(10,2) DEFAULT 0.00,
    active TINYINT(1) DEFAULT 1,
    date_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_update DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_udn (udn_id),
    INDEX idx_active (active)
);
```

#### Table: credit_movements

```sql
CREATE TABLE credit_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    udn_id INT NOT NULL,
    movement_type ENUM('consumo', 'abono_parcial', 'pago_total', 'anticipo') NOT NULL,
    payment_method ENUM('n/a', 'efectivo', 'banco') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    previous_balance DECIMAL(10,2) NOT NULL,
    new_balance DECIMAL(10,2) NOT NULL,
    description TEXT,
    capture_date DATE NOT NULL,
    created_by INT NOT NULL,
    updated_by INT,
    date_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_update DATETIME ON UPDATE CURRENT_TIMESTAMP,
    active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    INDEX idx_client (client_id),
    INDEX idx_capture_date (capture_date),
    INDEX idx_movement_type (movement_type),
    INDEX idx_active (active)
);
```

#### Table: movement_audit_log

```sql
CREATE TABLE movement_audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movement_id INT NOT NULL,
    client_id INT NOT NULL,
    action ENUM('create', 'update', 'delete') NOT NULL,
    user_id INT NOT NULL,
    action_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    old_data JSON,
    new_data JSON,
    INDEX idx_movement (movement_id),
    INDEX idx_action_date (action_date)
);
```

### Data Flow Diagrams

#### Add Movement Flow

```
User Input → Validate Form → Check Client Balance
                ↓
        Create Movement Record
                ↓
        Update Client Balance
                ↓
        Log Audit Entry
                ↓
        Sync with Sales Module
                ↓
        Return Success Response
```

#### Calculate Balance Flow

```
Get Client ID + Date Range
        ↓
Fetch Initial Balance
        ↓
Sum All Consumptions (+)
        ↓
Sum All Payments (-)
        ↓
Calculate Final Balance
        ↓
Return Balance Object
```

## Error Handling

### Frontend Error Handling

```javascript
// Form Validation
validateMovementForm() {
    if (!clientId) return { valid: false, message: "Seleccione un cliente" };
    if (!amount || amount <= 0) return { valid: false, message: "Ingrese un monto válido" };
    if (movementType !== 'consumo' && !paymentMethod) {
        return { valid: false, message: "Seleccione un método de pago" };
    }
    return { valid: true };
}

// AJAX Error Handling
async addMovimiento() {
    try {
        const response = await useFetch({
            url: this._link,
            data: { opc: 'addMovimiento', ...formData }
        });
        
        if (response.status === 200) {
            alert({ icon: "success", text: response.message });
            this.lsMovimientos();
        } else {
            alert({ icon: "error", text: response.message });
        }
    } catch (error) {
        alert({ icon: "error", text: "Error de conexión con el servidor" });
    }
}
```

### Backend Error Handling

```php
function addMovimiento() {
    $status = 500;
    $message = 'Error al registrar el movimiento';
    
    try {
        // Validate required fields
        if (empty($_POST['client_id']) || empty($_POST['amount'])) {
            return [
                'status' => 400,
                'message' => 'Faltan campos obligatorios'
            ];
        }
        
        // Validate movement type and payment method
        if ($_POST['movement_type'] !== 'consumo' && $_POST['payment_method'] === 'n/a') {
            return [
                'status' => 400,
                'message' => 'Debe seleccionar un método de pago válido'
            ];
        }
        
        // Get current balance
        $currentBalance = $this->getClientBalance([$_POST['client_id']]);
        
        // Calculate new balance
        $newBalance = $this->calculateNewBalance(
            $currentBalance,
            $_POST['movement_type'],
            $_POST['amount']
        );
        
        // Create movement
        $_POST['previous_balance'] = $currentBalance;
        $_POST['new_balance'] = $newBalance;
        $_POST['capture_date'] = date('Y-m-d');
        $_POST['created_by'] = $_SESSION['user_id'];
        
        $create = $this->createMovement($this->util->sql($_POST));
        
        if ($create) {
            // Update client balance
            $this->updateClientBalance([
                'id' => $_POST['client_id'],
                'current_balance' => $newBalance
            ]);
            
            // Log audit
            $this->logMovementAction([
                'movement_id' => $create,
                'client_id' => $_POST['client_id'],
                'action' => 'create',
                'user_id' => $_SESSION['user_id']
            ]);
            
            $status = 200;
            $message = 'Movimiento registrado correctamente';
        }
        
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error del servidor: ' . $e->getMessage();
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

## Testing Strategy

### Unit Tests

#### Frontend Tests
- Form validation logic
- Balance calculation functions
- Date range selection
- Filter functionality

#### Backend Tests
- Movement CRUD operations
- Balance calculation accuracy
- Payment method validation
- Audit log creation

### Integration Tests

1. **Movement Registration Flow**
   - Create movement → Update balance → Verify sync with sales

2. **Consolidated Report Generation**
   - Filter by date range → Calculate totals → Export to Excel

3. **Access Level Validation**
   - Test each user level permissions
   - Verify UI elements visibility

### Test Cases

```javascript
// Test Case 1: Add Consumption Movement
describe('Add Consumption Movement', () => {
    it('should set payment method to N/A automatically', () => {
        const formData = {
            client_id: 1,
            movement_type: 'consumo',
            amount: 500
        };
        expect(getPaymentMethod(formData)).toBe('n/a');
    });
    
    it('should increase client balance', () => {
        const currentBalance = 1000;
        const amount = 500;
        const newBalance = calculateBalance(currentBalance, 'consumo', amount);
        expect(newBalance).toBe(1500);
    });
});

// Test Case 2: Add Payment Movement
describe('Add Payment Movement', () => {
    it('should require payment method selection', () => {
        const formData = {
            client_id: 1,
            movement_type: 'pago_total',
            amount: 500,
            payment_method: ''
        };
        expect(validateForm(formData).valid).toBe(false);
    });
    
    it('should decrease client balance', () => {
        const currentBalance = 1000;
        const amount = 500;
        const newBalance = calculateBalance(currentBalance, 'pago_total', amount);
        expect(newBalance).toBe(500);
    });
});
```

### Manual Testing Checklist

- [ ] Dashboard displays correct totals
- [ ] Filter by movement type works correctly
- [ ] Date picker updates data automatically
- [ ] Add movement form validates all fields
- [ ] Consumption movement locks payment method to N/A
- [ ] Payment movements require payment method selection
- [ ] Edit movement preserves client association
- [ ] Delete movement shows confirmation dialog
- [ ] View details shows complete information
- [ ] Consolidated report calculates correctly
- [ ] Export to Excel generates valid file
- [ ] Access levels restrict actions properly
- [ ] Sync with sales module works in real-time

## Security Considerations

### Authentication & Authorization

```php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Access level validation
function validateAccessLevel($requiredLevel) {
    $userLevel = $_SESSION['access_level'];
    if ($userLevel < $requiredLevel) {
        return [
            'status' => 403,
            'message' => 'No tiene permisos para realizar esta acción'
        ];
    }
    return ['status' => 200];
}
```

### Input Validation

```php
// SQL Injection Prevention
$data = $this->util->sql($_POST); // Uses prepared statements

// XSS Prevention
$clientName = htmlspecialchars($_POST['client_name'], ENT_QUOTES, 'UTF-8');

// Amount Validation
if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
    return ['status' => 400, 'message' => 'Monto inválido'];
}
```

### Audit Trail

All critical operations are logged:
- Movement creation
- Movement updates
- Movement deletions
- Balance modifications

## Performance Optimization

### Database Indexes

```sql
-- Optimize frequent queries
CREATE INDEX idx_client_date ON credit_movements(client_id, capture_date);
CREATE INDEX idx_udn_date ON credit_movements(udn_id, capture_date);
CREATE INDEX idx_active_date ON credit_movements(active, capture_date);
```

### Caching Strategy

```javascript
// Cache client list for session
let cachedClients = null;

async function getClients() {
    if (cachedClients) return cachedClients;
    
    cachedClients = await useFetch({
        url: api,
        data: { opc: 'init' }
    });
    
    return cachedClients;
}
```

### Query Optimization

```php
// Use single query for consolidated report instead of multiple queries
function getConsolidatedReport($filters) {
    $query = "
        SELECT 
            c.id,
            c.name,
            SUM(CASE WHEN cm.movement_type = 'consumo' THEN cm.amount ELSE 0 END) as total_consumptions,
            SUM(CASE WHEN cm.movement_type IN ('abono_parcial', 'pago_total') THEN cm.amount ELSE 0 END) as total_payments,
            (SELECT current_balance FROM clients WHERE id = c.id) as final_balance
        FROM clients c
        LEFT JOIN credit_movements cm ON c.id = cm.client_id
        WHERE cm.capture_date BETWEEN ? AND ?
        AND cm.active = 1
        GROUP BY c.id
    ";
    
    return $this->_Read($query, [$filters['fi'], $filters['ff']]);
}
```

## Deployment Considerations

### File Structure

```
contabilidad/
├── captura/
│   ├── clientes.js          # Frontend application
│   └── index.php            # Entry point
├── ctrl/
│   └── ctrl-clientes.php    # Controller
└── mdl/
    └── mdl-clientes.php     # Model
```

### Dependencies

- jQuery 3.x
- TailwindCSS 2.x
- CoffeeSoft Framework (coffeSoft.js, plugins.js)
- PHP 7.4+
- MySQL 5.7+

### Environment Variables

```php
// Database configuration
$this->bd = "rfwsmqex_contabilidad.";

// Session configuration
session_start();
$_SESSION['udn'] = $_POST['udn']; // Current business unit
```
