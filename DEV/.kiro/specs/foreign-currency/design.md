# Design Document - Foreign Currency Module

## Overview

The Foreign Currency module is a sub-module within the CoffeeSoft accounting system that enables administrators to manage foreign currencies used across business units. The module follows the MVC architecture pattern established by CoffeeSoft framework, utilizing jQuery for frontend interactions, PHP for backend logic, and MySQL for data persistence.

The module integrates with the existing accounting administration interface, appearing as a tab alongside other administrative functions (Desbloqueo de módulos, Cuentas de ventas, Formas de pago, Clientes, Compras, Proveedores).

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Layer (JS)                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  moneda.js (extends Templates from CoffeeSoft)       │  │
│  │  - AdminForeignCurrency class                        │  │
│  │  - UI rendering and event handling                   │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↕ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer (PHP)                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  ctrl-moneda.php (extends mdl)                       │  │
│  │  - Request routing and validation                    │  │
│  │  - Business logic orchestration                      │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↕ Method calls
┌─────────────────────────────────────────────────────────────┐
│                     Model Layer (PHP)                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  mdl-moneda.php (extends CRUD)                       │  │
│  │  - Database operations                               │  │
│  │  - Data access methods                               │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↕ SQL queries
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer (MySQL)                    │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Table: foreign_currency                             │  │
│  │  - id, udn_id, name, code, conversion_value, active  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### File Structure

```
contabilidad/
├── administrador/
│   ├── moneda.js                    # Frontend JavaScript
│   ├── ctrl/
│   │   └── ctrl-moneda.php          # Controller
│   └── mdl/
│       └── mdl-moneda.php           # Model
└── index.php                        # Main entry point (existing)
```

## Components and Interfaces

### Frontend Component (moneda.js)

**Class: AdminForeignCurrency extends Templates**

The main class responsible for rendering the UI and handling user interactions.

**Key Methods:**

```javascript
class AdminForeignCurrency extends Templates {
    constructor(link, div_modulo)
    render()                          // Initialize and render the module
    layout()                          // Create primary layout structure
    filterBar()                       // Render filter controls
    lsCurrencies()                    // Display currency table
    addCurrency()                     // Show add currency modal
    editCurrency(id)                  // Show edit currency modal
    toggleStatus(id, currentStatus)   // Activate/deactivate currency
    jsonCurrency()                    // Return form field definitions
}
```

**UI Components Used:**
- `primaryLayout()` - Main container structure
- `createfilterBar()` - Filter controls for UDN and payment method
- `createTable()` - Currency listing table with CoffeeSoft theme
- `createModalForm()` - Add/edit currency forms
- `swalQuestion()` - Confirmation dialogs for status changes

### Controller (ctrl-moneda.php)

**Class: ctrl extends mdl**

Handles HTTP requests and orchestrates business logic.

**Methods:**

```php
class ctrl extends mdl {
    function init()                   // Load initial data (UDN list, payment methods)
    function lsCurrencies()           // List currencies with filters
    function getCurrency()            // Get single currency by ID
    function addCurrency()            // Create new currency
    function editCurrency()           // Update existing currency
    function toggleStatus()           // Change currency active status
}
```

**Request/Response Flow:**

```
POST /ctrl-moneda.php
{
    "opc": "lsCurrencies",
    "udn": 1,
    "payment_method": "foreign"
}

Response:
{
    "row": [
        {
            "id": 1,
            "Moneda": "Dólar",
            "Símbolo": "USD",
            "Tipo de cambio": "$20.00",
            "a": [edit_button, toggle_button]
        }
    ]
}
```

### Model (mdl-moneda.php)

**Class: mdl extends CRUD**

Provides data access methods using the CoffeeSoft CRUD base class.

**Methods:**

```php
class mdl extends CRUD {
    function listCurrencies($filters)           // Get currencies with filters
    function getCurrencyById($id)               // Get single currency
    function createCurrency($data)              // Insert new currency
    function updateCurrency($data)              // Update currency
    function existsCurrencyByName($name, $udn)  // Check for duplicates
    function lsUDN()                            // Get business units for filter
    function lsPaymentMethods()                 // Get payment methods for filter
}
```

**Database Operations:**
- Uses `_Select()` for queries with filters and joins
- Uses `_Insert()` for creating records
- Uses `_Update()` for modifications
- Uses `_Read()` for custom SQL queries

## Data Models

### Database Schema

**Table: foreign_currency**

```sql
CREATE TABLE foreign_currency (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    conversion_value DECIMAL(10,2) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    UNIQUE KEY unique_currency_per_udn (udn_id, name)
);
```

**Field Descriptions:**
- `id`: Primary key
- `udn_id`: Foreign key to business unit table
- `name`: Currency name (e.g., "Dólar", "Quetzal")
- `code`: Currency symbol/code (e.g., "USD", "GTQ")
- `conversion_value`: Exchange rate to MXN
- `active`: Status flag (1=active, 0=inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Last modification timestamp

**Indexes:**
- Primary key on `id`
- Foreign key on `udn_id`
- Unique composite key on `(udn_id, name)` to prevent duplicate currencies per UDN

### Data Validation Rules

**Frontend Validation:**
- Currency name: Required, max 100 characters
- Currency code: Required, max 10 characters
- Exchange rate: Required, numeric, > 0, max 2 decimal places

**Backend Validation:**
- Duplicate check: Verify currency name doesn't exist for the same UDN
- Exchange rate: Must be positive decimal value
- UDN: Must exist in udn table
- SQL injection prevention: Use prepared statements via CRUD methods

## Error Handling

### Frontend Error Handling

**Validation Errors:**
```javascript
// Display inline validation messages
if (!name || !code || !conversion_value) {
    alert({
        icon: "error",
        text: "Todos los campos son obligatorios"
    });
    return;
}

if (conversion_value <= 0) {
    alert({
        icon: "error",
        text: "El tipo de cambio debe ser mayor a cero"
    });
    return;
}
```

**AJAX Error Handling:**
```javascript
this.useFetch({
    url: api,
    data: { opc: 'addCurrency', ... },
    success: (response) => {
        if (response.status === 200) {
            alert({ icon: "success", text: response.message });
            this.lsCurrencies();
        } else {
            alert({ icon: "error", text: response.message });
        }
    },
    error: (error) => {
        alert({
            icon: "error",
            text: "Error de conexión. Intente nuevamente."
        });
    }
});
```

### Backend Error Handling

**Controller Error Responses:**
```php
function addCurrency() {
    $status = 500;
    $message = 'Error al agregar moneda';
    
    try {
        // Validate required fields
        if (empty($_POST['name']) || empty($_POST['code']) || empty($_POST['conversion_value'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }
        
        // Check for duplicates
        $exists = $this->existsCurrencyByName([$_POST['name'], $_POST['udn_id']]);
        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe una moneda con ese nombre para esta unidad de negocio'
            ];
        }
        
        // Create currency
        $create = $this->createCurrency($this->util->sql($_POST));
        if ($create) {
            $status = 200;
            $message = 'Moneda agregada correctamente';
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

**Model Error Handling:**
```php
function createCurrency($data) {
    try {
        return $this->_Insert([
            'table' => "{$this->bd}foreign_currency",
            'values' => $data['values'],
            'data' => $data['data']
        ]);
    } catch (PDOException $e) {
        error_log("Database error in createCurrency: " . $e->getMessage());
        return false;
    }
}
```

### Error Response Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | Success | Operation completed successfully |
| 400 | Bad Request | Missing or invalid required fields |
| 404 | Not Found | Currency ID doesn't exist |
| 409 | Conflict | Duplicate currency name for UDN |
| 500 | Server Error | Database or system error |

## Testing Strategy

### Unit Testing

**Frontend Tests (using Jest or similar):**
```javascript
describe('AdminForeignCurrency', () => {
    test('should validate required fields', () => {
        const currency = new AdminForeignCurrency(api, 'root');
        expect(currency.validateForm({})).toBe(false);
        expect(currency.validateForm({
            name: 'Dólar',
            code: 'USD',
            conversion_value: 20.00
        })).toBe(true);
    });
    
    test('should format exchange rate to 2 decimals', () => {
        const currency = new AdminForeignCurrency(api, 'root');
        expect(currency.formatExchangeRate(20)).toBe('20.00');
        expect(currency.formatExchangeRate(20.5)).toBe('20.50');
    });
});
```

**Backend Tests (using PHPUnit):**
```php
class CtrlMonedaTest extends TestCase {
    public function testAddCurrencySuccess() {
        $_POST = [
            'opc' => 'addCurrency',
            'name' => 'Dólar',
            'code' => 'USD',
            'conversion_value' => 20.00,
            'udn_id' => 1
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addCurrency();
        
        $this->assertEquals(200, $result['status']);
    }
    
    public function testAddCurrencyDuplicate() {
        // Setup: Create existing currency
        // ...
        
        $_POST = [
            'opc' => 'addCurrency',
            'name' => 'Dólar', // Duplicate
            'code' => 'USD',
            'conversion_value' => 20.00,
            'udn_id' => 1
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addCurrency();
        
        $this->assertEquals(409, $result['status']);
    }
}
```

### Integration Testing

**Test Scenarios:**

1. **Complete CRUD Flow:**
   - Create new currency → Verify in database
   - List currencies → Verify correct data returned
   - Edit currency → Verify changes persisted
   - Toggle status → Verify status updated
   - Filter by UDN → Verify correct filtering

2. **Validation Flow:**
   - Submit empty form → Verify error message
   - Submit negative exchange rate → Verify error
   - Submit duplicate currency → Verify conflict error

3. **Status Change Flow:**
   - Deactivate currency → Verify not in selection dropdowns
   - Verify inactive currency still in historical records
   - Reactivate currency → Verify available again

### Manual Testing Checklist

**UI Testing:**
- [ ] Table displays correctly with all columns
- [ ] Filters work correctly (UDN, payment method)
- [ ] Add button opens modal with empty form
- [ ] Edit button opens modal with pre-filled data
- [ ] Toggle buttons reflect current status
- [ ] Confirmation dialogs display correct messages
- [ ] Success/error messages display appropriately

**Functional Testing:**
- [ ] Can create new currency with valid data
- [ ] Cannot create duplicate currency for same UDN
- [ ] Can edit existing currency
- [ ] Edit warning message displays correctly
- [ ] Can deactivate active currency
- [ ] Can activate inactive currency
- [ ] Inactive currencies excluded from dropdowns
- [ ] Inactive currencies visible in historical records

**Data Integrity Testing:**
- [ ] Exchange rate stored with 2 decimal precision
- [ ] Timestamps updated correctly
- [ ] Foreign key constraints enforced
- [ ] Unique constraint prevents duplicates

## Design Decisions and Rationales

### 1. Use of CoffeeSoft Framework Components

**Decision:** Utilize existing CoffeeSoft components (createTable, createModalForm, swalQuestion) rather than custom implementations.

**Rationale:**
- Maintains consistency with existing modules
- Reduces development time
- Ensures compatibility with system theme and styling
- Leverages tested and proven components

### 2. Soft Delete vs Hard Delete

**Decision:** Implement status toggle (active/inactive) rather than hard delete.

**Rationale:**
- Preserves historical accounting records
- Allows reactivation if needed
- Prevents orphaned foreign key references
- Complies with accounting audit requirements

### 3. Unique Constraint on (udn_id, name)

**Decision:** Allow same currency name across different UDNs but prevent duplicates within same UDN.

**Rationale:**
- Different business units may have different exchange rates
- Prevents accidental duplicate entries
- Maintains data integrity per business unit

### 4. Exchange Rate Validation

**Decision:** Validate exchange rate > 0 on both frontend and backend.

**Rationale:**
- Prevents invalid data entry
- Frontend validation provides immediate feedback
- Backend validation ensures data integrity
- Zero or negative rates would break calculations

### 5. Warning Message on Edit

**Decision:** Display prominent warning about impact of exchange rate changes.

**Rationale:**
- Informs users of system-wide impact
- Encourages verification before changes
- Reduces risk of unintended consequences
- Meets business requirement for cash withdrawal confirmation

### 6. Separate Add and Edit Forms

**Decision:** Use distinct modal forms for add and edit operations.

**Rationale:**
- Edit form requires warning message not needed in add form
- Clearer user intent and workflow
- Easier to maintain separate validation rules if needed
- Follows CoffeeSoft pattern established in other modules

## Security Considerations

**SQL Injection Prevention:**
- All database queries use prepared statements via CRUD methods
- User input sanitized through `$this->util->sql()`

**XSS Prevention:**
- Output escaped in table rendering
- Modal forms use jQuery element creation (automatic escaping)

**CSRF Protection:**
- Session validation in controller
- POST requests only for data modifications

**Access Control:**
- Verify user has administrator role before allowing modifications
- Check session validity on each request

## Performance Considerations

**Database Optimization:**
- Index on `udn_id` for fast filtering
- Composite unique index serves as query optimization
- Limit result sets with pagination if needed

**Frontend Optimization:**
- Use DataTables for client-side sorting/filtering
- Lazy load modal forms (create on demand)
- Cache UDN and payment method lists

**Caching Strategy:**
- Consider caching currency list per UDN (5-minute TTL)
- Invalidate cache on create/update/delete operations
