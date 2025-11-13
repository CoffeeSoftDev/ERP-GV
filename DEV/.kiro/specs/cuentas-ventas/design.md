# Design Document - Módulo de Cuentas de Ventas

## Overview

El módulo de Cuentas de Ventas es un sistema de gestión que permite administrar categorías de venta con sus respectivos permisos e impuestos. Se integra con el framework CoffeeSoft siguiendo la arquitectura MVC y utiliza TailwindCSS para la interfaz de usuario.

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer (JS)                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  cuenta-venta.js (extends Templates)                  │  │
│  │  - SalesAccountManager class                          │  │
│  │  - UI Components (tables, forms, modals)              │  │
│  │  - Event handlers                                     │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer (PHP)                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  ctrl-cuenta-venta.php (extends mdl)                  │  │
│  │  - init()                                             │  │
│  │  - lsSalesAccount()                                   │  │
│  │  - addSalesAccount()                                  │  │
│  │  - editSalesAccount()                                 │  │
│  │  - statusSalesAccount()                               │  │
│  │  - getSalesAccount()                                  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓ CRUD Operations
┌─────────────────────────────────────────────────────────────┐
│                      Model Layer (PHP)                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  mdl-cuenta-venta.php (extends CRUD)                  │  │
│  │  - listSalesAccount()                                 │  │
│  │  - createSalesAccount()                               │  │
│  │  - updateSalesAccount()                               │  │
│  │  - getSalesAccountById()                              │  │
│  │  - existsSalesAccountByName()                         │  │
│  │  - lsUDN()                                            │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│                   categoria_venta table                      │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend**: jQuery, CoffeeSoft Framework, TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **AJAX**: Fetch API (useFetch wrapper)
- **UI Components**: CoffeeSoft Components (createTable, createModalForm, swalQuestion)

## Components and Interfaces

### Frontend Components (cuenta-venta.js)

#### Class: SalesAccountManager (extends Templates)

**Properties:**
- `PROJECT_NAME`: "SalesAccount"
- `_link`: API endpoint (ctrl-cuenta-venta.php)
- `_div_modulo`: Root container ID

**Methods:**

1. **render()**
   - Initializes the module
   - Calls layout(), filterBar(), and lsSalesAccount()

2. **layout()**
   - Creates primary layout using `primaryLayout()`
   - Sets up container structure with filterBar and main container

3. **filterBar()**
   - Creates filter bar with UDN selector using `createfilterBar()`
   - Includes "Agregar nueva categoría" button
   - Triggers table refresh on UDN change

4. **lsSalesAccount()**
   - Fetches sales accounts data via AJAX
   - Renders table using `createTable()` with CoffeeSoft theme
   - Displays columns: Categoría, Descuento, Cortesía, IVA, IEPS, Hospedaje, Impuesto 0%, Acciones
   - Implements action buttons (Edit, Activate/Deactivate)

5. **addSalesAccount()**
   - Opens modal using `createModalForm()`
   - Form fields: UDN, nombre, permisos (checkboxes), impuestos (checkboxes)
   - Displays validation warning message
   - Handles form submission and success callback

6. **editSalesAccount(id)**
   - Fetches existing data via `getSalesAccount()`
   - Opens modal with pre-filled data using `autofill`
   - Displays warning about Soft-Restaurant synchronization
   - Handles update submission

7. **statusSalesAccount(id, currentStatus)**
   - Shows confirmation dialog using `swalQuestion()`
   - Different messages for activate/deactivate actions
   - Updates status via AJAX
   - Refreshes table on success

8. **jsonSalesAccount()**
   - Returns form field configuration array
   - Defines input types, labels, classes, and validation rules

### Controller Layer (ctrl-cuenta-venta.php)

#### Class: ctrl (extends mdl)

**Methods:**

1. **init()**
   - Returns initial data for filters
   - Fetches UDN list via `lsUDN()`
   - Returns: `['udn' => array]`

2. **lsSalesAccount()**
   - Receives: `$_POST['udn']`
   - Calls `listSalesAccount()` from model
   - Formats data for table display
   - Returns: `['row' => array, 'thead' => string]`

3. **addSalesAccount()**
   - Receives: Form data via POST
   - Validates category name uniqueness via `existsSalesAccountByName()`
   - Calls `createSalesAccount()` if valid
   - Returns: `['status' => int, 'message' => string]`

4. **editSalesAccount()**
   - Receives: `$_POST['id']` + form data
   - Calls `updateSalesAccount()`
   - Returns: `['status' => int, 'message' => string]`

5. **getSalesAccount()**
   - Receives: `$_POST['id']`
   - Calls `getSalesAccountById()`
   - Returns: `['status' => int, 'message' => string, 'data' => array]`

6. **statusSalesAccount()**
   - Receives: `$_POST['id']`, `$_POST['active']`
   - Toggles active status (0/1)
   - Calls `updateSalesAccount()`
   - Returns: `['status' => int, 'message' => string]`

### Model Layer (mdl-cuenta-venta.php)

#### Class: mdl (extends CRUD)

**Properties:**
- `$bd`: Database prefix
- `$util`: Utileria instance

**Methods:**

1. **listSalesAccount($array)**
   - Parameters: `[$udn_id]`
   - Uses `_Select()` with JOIN to UDN table
   - Returns: Array of sales accounts with all fields

2. **createSalesAccount($array)**
   - Parameters: Formatted data array from `$util->sql()`
   - Uses `_Insert()` method
   - Returns: Boolean success status

3. **updateSalesAccount($array)**
   - Parameters: Formatted data array with WHERE clause
   - Uses `_Update()` method
   - Returns: Boolean success status

4. **getSalesAccountById($array)**
   - Parameters: `[$id]`
   - Uses `_Select()` with WHERE clause
   - Returns: Single record array

5. **existsSalesAccountByName($array)**
   - Parameters: `[$name, $udn_id]`
   - Uses `_Select()` to check duplicates
   - Returns: Integer count

6. **lsUDN()**
   - No parameters
   - Uses `_Select()` to fetch active UDNs
   - Returns: Array of UDN options for select dropdown

## Data Models

### Database Schema

#### Table: categoria_venta

```sql
CREATE TABLE categoria_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    permiso_descuento TINYINT(1) DEFAULT 0,
    permiso_cortesia TINYINT(1) DEFAULT 0,
    impuesto_iva TINYINT(1) DEFAULT 0,
    impuesto_ieps TINYINT(1) DEFAULT 0,
    impuesto_hospedaje TINYINT(1) DEFAULT 0,
    impuesto_cero TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    UNIQUE KEY unique_category_per_udn (udn_id, nombre)
);
```

### Data Flow

#### Create Sales Account Flow

```
User Input (Modal Form)
    ↓
Frontend Validation
    ↓
AJAX POST to ctrl-cuenta-venta.php (opc: 'addSalesAccount')
    ↓
Controller: addSalesAccount()
    ↓
Check existsSalesAccountByName()
    ↓ (if not exists)
Model: createSalesAccount()
    ↓
Database INSERT
    ↓
Return success response
    ↓
Frontend: Close modal, refresh table, show success alert
```

#### Update Status Flow

```
User clicks Activate/Deactivate button
    ↓
Frontend: Show swalQuestion() confirmation
    ↓
User confirms
    ↓
AJAX POST to ctrl-cuenta-venta.php (opc: 'statusSalesAccount')
    ↓
Controller: statusSalesAccount()
    ↓
Model: updateSalesAccount()
    ↓
Database UPDATE (toggle activo field)
    ↓
Return success response
    ↓
Frontend: Refresh table, show success alert
```

## Error Handling

### Frontend Error Handling

1. **AJAX Errors**
   - Catch network errors in useFetch()
   - Display user-friendly error messages using `alert()` component
   - Log errors to console for debugging

2. **Validation Errors**
   - Client-side validation before form submission
   - Required field validation
   - Display inline error messages

3. **Server Response Errors**
   - Check `response.status` code
   - Display appropriate messages based on error type
   - Handle 409 (duplicate), 500 (server error), 404 (not found)

### Backend Error Handling

1. **Database Errors**
   - Try-catch blocks in CRUD operations
   - Return standardized error responses
   - Log errors for debugging

2. **Validation Errors**
   - Check for duplicate entries before INSERT
   - Validate required fields
   - Return status 409 for conflicts

3. **Data Integrity**
   - Foreign key constraints in database
   - Transaction support for critical operations
   - Rollback on failure

### Error Response Format

```php
[
    'status' => 500,  // HTTP-like status code
    'message' => 'Error descriptivo para el usuario',
    'error' => 'Technical error details (optional)'
]
```

## Testing Strategy

### Unit Testing

1. **Model Layer Tests**
   - Test CRUD operations independently
   - Mock database connections
   - Verify SQL query generation
   - Test validation methods

2. **Controller Layer Tests**
   - Test each endpoint with valid/invalid data
   - Verify response format
   - Test error handling
   - Mock model layer

### Integration Testing

1. **Frontend-Backend Integration**
   - Test complete user flows (create, edit, delete)
   - Verify AJAX communication
   - Test form validation end-to-end
   - Verify table refresh after operations

2. **Database Integration**
   - Test with actual database
   - Verify foreign key constraints
   - Test transaction rollback
   - Verify data integrity

### UI Testing

1. **Component Rendering**
   - Verify table displays correctly
   - Test modal open/close
   - Verify form field rendering
   - Test responsive design

2. **User Interactions**
   - Test button clicks
   - Test form submissions
   - Test filter changes
   - Test confirmation dialogs

### Test Cases

#### Test Case 1: Create New Sales Account
- **Input**: Valid form data with unique name
- **Expected**: Success message, table refresh, modal close
- **Validation**: Database record created, all fields saved correctly

#### Test Case 2: Create Duplicate Sales Account
- **Input**: Form data with existing name for same UDN
- **Expected**: Error message, modal stays open
- **Validation**: No database record created

#### Test Case 3: Edit Sales Account
- **Input**: Modified form data for existing account
- **Expected**: Success message, table refresh with updated data
- **Validation**: Database record updated correctly

#### Test Case 4: Deactivate Sales Account
- **Input**: Click deactivate on active account
- **Expected**: Confirmation dialog, success message, visual state change
- **Validation**: Database activo field set to 0

#### Test Case 5: Filter by UDN
- **Input**: Select different UDN from dropdown
- **Expected**: Table refreshes with accounts for selected UDN
- **Validation**: Only accounts matching UDN displayed

### Manual Testing Checklist

- [ ] Verify all table columns display correctly
- [ ] Test create form with all field combinations
- [ ] Test edit form pre-fills data correctly
- [ ] Verify warning messages display appropriately
- [ ] Test activate/deactivate toggle functionality
- [ ] Verify UDN filter works correctly
- [ ] Test responsive design on mobile devices
- [ ] Verify all success/error messages display correctly
- [ ] Test with multiple concurrent users
- [ ] Verify Soft-Restaurant synchronization warnings

## UI/UX Considerations

### Visual Design

- Use CoffeeSoft corporativo theme for consistency
- Blue icons for active categories, red for inactive
- Clear visual separation between permissions and taxes columns
- Responsive table design with horizontal scroll on mobile

### User Feedback

- Loading indicators during AJAX operations
- Success/error alerts with appropriate icons
- Confirmation dialogs for destructive actions
- Inline validation messages

### Accessibility

- Proper label associations for form fields
- Keyboard navigation support
- ARIA labels for icon buttons
- Color contrast compliance

## Performance Considerations

- Lazy loading for large datasets
- Debounce filter changes to reduce AJAX calls
- Cache UDN list in frontend
- Optimize database queries with proper indexes
- Minimize DOM manipulations during table refresh
