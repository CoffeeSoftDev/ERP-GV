# Design Document - Módulo de Ventas

## Overview

El módulo de Ventas es un sistema web desarrollado con el framework CoffeeSoft que permite la captura, consulta y validación de ventas diarias por unidad de negocio. El sistema implementa una arquitectura MVC (Modelo-Vista-Controlador) con frontend en JavaScript/jQuery, backend en PHP, y base de datos MySQL.

El diseño se basa en el pivote "Admin" de CoffeeSoft, adaptado para manejar múltiples entidades relacionadas con el proceso de ventas: categorías, descuentos, conceptos de efectivo, cuentas bancarias, clientes con crédito, y el cierre diario.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Frontend Layer                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  ventas.js   │  │ Components   │  │  Templates   │      │
│  │  (App Class) │  │  CoffeeSoft  │  │  CoffeeSoft  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ AJAX/Fetch
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Controller Layer                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ctrl-ventas.php│ │ctrl-admin.php│  │  Utilities   │      │
│  │  (Business   │  │  (Config     │  │              │      │
│  │   Logic)     │  │   Entities)  │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ CRUD Operations
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                        Model Layer                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │mdl-ventas.php│  │mdl-admin.php │  │  _CRUD.php   │      │
│  │  (Data       │  │  (Config     │  │  (Base       │      │
│  │   Access)    │  │   Entities)  │  │   Class)     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ SQL Queries
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│                   MySQL - rfwsmqex_contabilidad              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Tables: daily_closure, detail_sale_category,        │   │
│  │  detail_discount_courtesy, detail_cash_concept,      │   │
│  │  detail_bank_account, detail_credit_customer,        │   │
│  │  sale_category, discount_courtesy, cash_concept,     │   │
│  │  bank_account, customer                              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend**: JavaScript ES6+, jQuery 3.x, TailwindCSS 2.x
- **Framework**: CoffeeSoft (Classes: Complements, Components, Templates)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Additional Libraries**: 
  - Moment.js (date handling)
  - Chart.js (optional for analytics)
  - DataTables (table pagination)
  - SweetAlert2 (alerts)
  - Bootbox (modals)

## Components and Interfaces

### Frontend Components

#### 1. App Class (Main Module)
```javascript
class App extends Templates {
    constructor(link, div_modulo)
    PROJECT_NAME = "ventas"
    
    Methods:
    - render()           // Initialize module
    - layout()           // Create tab structure
    - layoutTabs()       // Configure tabs
    - headerBar()        // Top navigation bar
}
```

#### 2. SaleCapture Class (Daily Sales)
```javascript
class SaleCapture extends App {
    Methods:
    - render()                    // Display sale form
    - layout()                    // Form structure
    - filterBarSale()             // Date selector
    - jsonSaleForm()              // Form fields definition
    - calculateSubtotal()         // Auto-calculate subtotal
    - calculateTaxes()            // Auto-calculate taxes
    - calculateTotal()            // Auto-calculate total sale
    - saveDailySale()             // Save to database
    - loadSoftRestaurant()        // Import from Soft-Restaurant
}
```

#### 3. PaymentForms Class (Income Forms)
```javascript
class PaymentForms extends App {
    Methods:
    - render()                    // Display payment forms
    - layout()                    // Form structure
    - jsonPaymentForm()           // Form fields definition
    - calculateCash()             // Sum cash concepts
    - calculateBanks()            // Sum bank deposits
    - calculateCredits()          // Calculate net credits
    - calculateForeignCurrency()  // Convert and sum foreign currency
    - calculateTotalReceived()    // Total received calculation
    - calculateDifference()       // Difference validation
    - savePaymentForms()          // Save to database
}
```

#### 4. AdminModule Class (Configuration)
```javascript
class AdminModule extends App {
    Methods:
    - render()                    // Display admin tabs
    - layout()                    // Admin structure
    - lsSaleCategories()          // List sale categories
    - addSaleCategory()           // Add new category
    - editSaleCategory(id)        // Edit category
    - statusSaleCategory(id)      // Toggle category status
    - lsDiscounts()               // List discounts/courtesies
    - addDiscount()               // Add new discount
    - editDiscount(id)            // Edit discount
    - statusDiscount(id)          // Toggle discount status
    - lsCashConcepts()            // List cash concepts
    - addCashConcept()            // Add new concept
    - editCashConcept(id)         // Edit concept
    - statusCashConcept(id)       // Toggle concept status
    - lsCustomers()               // List customers
    - addCustomer()               // Add new customer
    - editCustomer(id)            // Edit customer
    - updateCustomerBalance(id)   // Update credit balance
}
```

#### 5. TurnControl Class (Quinta Tabachines)
```javascript
class TurnControl extends App {
    Methods:
    - render()                    // Display turn form
    - layout()                    // Turn-specific structure
    - jsonTurnForm()              // Additional turn fields
    - lsTurnSummary()             // Daily summary by turn
    - exportTurnReport()          // Export to Excel/PDF
}
```

### Backend Controllers

#### ctrl-ventas.php
```php
class ctrl extends mdl {
    Methods:
    - init()                      // Initialize filters (UDN, dates)
    - lsDailySales()              // List daily closures
    - getDailySale()              // Get specific closure
    - saveDailySale()             // Save sale data
    - savePaymentForms()          // Save payment data
    - calculateTotals()           // Validate calculations
    - closeDailyOperation()       // Lock closure
    - uploadFiles()               // Handle file uploads
    - deleteFile()                // Remove uploaded file
    - getTurnSummary()            // Get turn data (Tabachines)
}
```

#### ctrl-admin.php
```php
class ctrl extends mdl {
    Methods:
    - init()                      // Initialize admin filters
    - lsSaleCategories()          // List categories
    - addSaleCategory()           // Create category
    - editSaleCategory()          // Update category
    - statusSaleCategory()        // Toggle category
    - lsDiscounts()               // List discounts
    - addDiscount()               // Create discount
    - editDiscount()              // Update discount
    - statusDiscount()            // Toggle discount
    - lsCashConcepts()            // List cash concepts
    - addCashConcept()            // Create concept
    - editCashConcept()           // Update concept
    - statusCashConcept()         // Toggle concept
    - lsCustomers()               // List customers
    - addCustomer()               // Create customer
    - editCustomer()              // Update customer
    - updateCustomerBalance()     // Update balance
}
```

### Backend Models

#### mdl-ventas.php
```php
class mdl extends CRUD {
    Methods:
    - listDailySales($array)              // Query daily_closure
    - getDailySaleById($id)               // Get closure by ID
    - createDailySale($array)             // Insert daily_closure
    - updateDailySale($array)             // Update daily_closure
    - createSaleDetail($array)            // Insert detail_sale_category
    - createDiscountDetail($array)        // Insert detail_discount_courtesy
    - createCashDetail($array)            // Insert detail_cash_concept
    - createBankDetail($array)            // Insert detail_bank_account
    - createCreditDetail($array)          // Insert detail_credit_customer
    - getSaleDetails($closureId)          // Get all sale details
    - getPaymentDetails($closureId)       // Get all payment details
    - lockClosure($id)                    // Set closure as locked
    - getTurnSummary($array)              // Query by turn
}
```

#### mdl-admin.php
```php
class mdl extends CRUD {
    Methods:
    - listSaleCategories($array)          // Query sale_category
    - getSaleCategoryById($id)            // Get category by ID
    - createSaleCategory($array)          // Insert sale_category
    - updateSaleCategory($array)          // Update sale_category
    - listDiscounts($array)               // Query discount_courtesy
    - getDiscountById($id)                // Get discount by ID
    - createDiscount($array)              // Insert discount_courtesy
    - updateDiscount($array)              // Update discount_courtesy
    - listCashConcepts($array)            // Query cash_concept
    - getCashConceptById($id)             // Get concept by ID
    - createCashConcept($array)           // Insert cash_concept
    - updateCashConcept($array)           // Update cash_concept
    - listBankAccounts($array)            // Query bank_account
    - listCustomers($array)               // Query customer
    - getCustomerById($id)                // Get customer by ID
    - createCustomer($array)              // Insert customer
    - updateCustomer($array)              // Update customer
    - updateCustomerBalance($array)       // Update balance
}
```

## Data Models

### Database Schema

#### daily_closure
```sql
CREATE TABLE daily_closure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    employee_id INT,
    operation_date DATE NOT NULL,
    total_sale_without_tax DECIMAL(12,2),
    total_sale DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    tax DECIMAL(12,2),
    cash DECIMAL(12,2),
    bank DECIMAL(12,2),
    foreing_currency DECIMAL(12,2),
    credit_consumer DECIMAL(12,2),
    credit_payment DECIMAL(12,2),
    total_received DECIMAL(12,2),
    difference DECIMAL(12,2),
    turn ENUM('matutino', 'vespertino', 'nocturno'),
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (employee_id) REFERENCES employee(id)
);
```

#### detail_sale_category
```sql
CREATE TABLE detail_sale_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_category_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    total DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    tax_iva DECIMAL(12,2),
    tax_ieps DECIMAL(12,2),
    tax_hospedaje DECIMAL(12,2),
    FOREIGN KEY (sale_category_id) REFERENCES sale_category(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

#### detail_discount_courtesy
```sql
CREATE TABLE detail_discount_courtesy (
    id INT PRIMARY KEY AUTO_INCREMENT,
    discount_courtesy_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    total DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    tax_iva DECIMAL(12,2),
    tax_ieps DECIMAL(12,2),
    tax_hospedaje DECIMAL(12,2),
    FOREIGN KEY (discount_courtesy_id) REFERENCES discount_courtesy(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

#### detail_cash_concept
```sql
CREATE TABLE detail_cash_concept (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cash_concept_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    amount DECIMAL(12,2),
    FOREIGN KEY (cash_concept_id) REFERENCES cash_concept(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

#### detail_bank_account
```sql
CREATE TABLE detail_bank_account (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bank_account_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    name VARCHAR(50),
    code CHAR(5),
    active TINYINT DEFAULT 1,
    FOREIGN KEY (bank_account_id) REFERENCES bank_account(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

#### detail_credit_customer
```sql
CREATE TABLE detail_credit_customer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    daily_closure_id INT NOT NULL,
    movement_type VARCHAR(20),
    method_pay VARCHAR(100),
    amount VARCHAR(15),
    FOREIGN KEY (customer_id) REFERENCES customer(id),
    FOREIGN KEY (daily_closure_id) REFERENCES daily_closure(id)
);
```

#### sale_category
```sql
CREATE TABLE sale_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### discount_courtesy
```sql
CREATE TABLE discount_courtesy (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(80),
    tax_iva TINYINT DEFAULT 0,
    tax_ieps TINYINT DEFAULT 0,
    tax_hospedaje TINYINT DEFAULT 0,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### cash_concept
```sql
CREATE TABLE cash_concept (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50),
    operation_type ENUM('suma', 'resta'),
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### bank_account
```sql
CREATE TABLE bank_account (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(100),
    account_number VARCHAR(20),
    bank_name VARCHAR(50),
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### customer
```sql
CREATE TABLE customer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50),
    balance DECIMAL(12,2) DEFAULT 0.00,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

### Data Flow Diagrams

#### Sale Capture Flow
```
User Input (Categories, Discounts, Taxes)
    ↓
Frontend Validation & Calculation
    ↓
AJAX Request to ctrl-ventas.php (opc: saveDailySale)
    ↓
Controller Validation
    ↓
Model: createDailySale() → Insert daily_closure
    ↓
Model: createSaleDetail() → Insert detail_sale_category (foreach category)
    ↓
Model: createDiscountDetail() → Insert detail_discount_courtesy (foreach discount)
    ↓
Response: { status: 200, message: "Venta guardada", id: closure_id }
    ↓
Frontend: Display success alert
```

#### Payment Forms Flow
```
User Input (Cash, Banks, Credits, Foreign Currency)
    ↓
Frontend Calculation (Total Received, Difference)
    ↓
AJAX Request to ctrl-ventas.php (opc: savePaymentForms)
    ↓
Controller: Validate difference threshold
    ↓
Model: updateDailySale() → Update daily_closure totals
    ↓
Model: createCashDetail() → Insert detail_cash_concept (foreach concept)
    ↓
Model: createBankDetail() → Insert detail_bank_account (foreach bank)
    ↓
Model: createCreditDetail() → Insert detail_credit_customer (foreach credit)
    ↓
Model: updateCustomerBalance() → Update customer balance
    ↓
Response: { status: 200, message: "Formas de pago guardadas" }
    ↓
Frontend: Display totals summary (green/blue/red boxes)
```

## Error Handling

### Frontend Error Handling

1. **Form Validation**
   - Required fields validation before submission
   - Numeric format validation for amounts
   - Date format validation
   - File size and format validation

2. **Calculation Errors**
   - Division by zero protection
   - Negative amount validation
   - Difference threshold alerts

3. **AJAX Error Handling**
   ```javascript
   useFetch({
       url: api,
       data: { opc: 'saveDailySale', ... },
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

### Backend Error Handling

1. **Input Validation**
   ```php
   if (empty($_POST['operation_date'])) {
       return [
           'status' => 400,
           'message' => 'La fecha de operación es requerida'
       ];
   }
   ```

2. **Database Errors**
   ```php
   try {
       $create = $this->createDailySale($data);
       if ($create) {
           return ['status' => 200, 'message' => 'Guardado exitoso'];
       }
   } catch (Exception $e) {
       return ['status' => 500, 'message' => 'Error en base de datos'];
   }
   ```

3. **Business Logic Validation**
   ```php
   // Validate closure is not already locked
   if ($closure['status'] === 'closed') {
       return [
           'status' => 403,
           'message' => 'El corte ya está cerrado y no puede modificarse'
       ];
   }
   ```

### Error Response Format

All backend responses follow this structure:
```php
return [
    'status' => 200|400|403|404|500,
    'message' => 'Descriptive message',
    'data' => [] // Optional
];
```

## Testing Strategy

### Unit Testing

#### Frontend Tests
- Component rendering tests
- Calculation logic tests (subtotal, taxes, totals)
- Form validation tests
- Currency conversion tests

#### Backend Tests
- Controller method tests
- Model CRUD operation tests
- Validation logic tests
- Calculation accuracy tests

### Integration Testing

1. **Sale Capture Flow**
   - Test complete sale entry and save
   - Verify database records creation
   - Validate calculation accuracy

2. **Payment Forms Flow**
   - Test payment entry and save
   - Verify customer balance updates
   - Validate difference calculation

3. **Admin Module Flow**
   - Test CRUD operations for all entities
   - Verify cascade effects (category deactivation)

### User Acceptance Testing

1. **Daily Operations**
   - Complete daily closure process
   - Verify totals match expectations
   - Test file upload functionality

2. **Turn Control (Tabachines)**
   - Test turn-specific data entry
   - Verify turn summary reports
   - Test export functionality

3. **Admin Configuration**
   - Test category management
   - Test discount/courtesy configuration
   - Test customer credit management

### Test Data

```sql
-- Sample UDN
INSERT INTO udn (id, name) VALUES (1, 'Raos Lunes');

-- Sample Sale Categories
INSERT INTO sale_category (udn_id, name) VALUES 
(1, 'Alimentos'),
(1, 'Bebidas'),
(1, 'Diversos');

-- Sample Discounts
INSERT INTO discount_courtesy (udn_id, name, tax_iva) VALUES 
(1, 'Descuento Alimentos', 1),
(1, 'Cortesía Bebidas', 1);

-- Sample Cash Concepts
INSERT INTO cash_concept (udn_id, name, operation_type) VALUES 
(1, 'Propina', 'suma'),
(1, 'Vales', 'suma'),
(1, 'Dólar', 'suma');

-- Sample Customer
INSERT INTO customer (udn_id, name, balance) VALUES 
(1, 'Cliente Crédito Test', 5000.00);
```

## Performance Considerations

1. **Database Optimization**
   - Indexes on foreign keys
   - Indexes on operation_date for faster queries
   - Composite index on (udn_id, operation_date)

2. **Frontend Optimization**
   - Lazy loading for large tables
   - Debounce on calculation functions
   - Cache UDN and category data

3. **Backend Optimization**
   - Prepared statements for all queries
   - Batch inserts for detail tables
   - Transaction management for multi-table operations

## Security Considerations

1. **Authentication & Authorization**
   - Session validation on all requests
   - UDN-based access control
   - Role-based permissions (admin vs user)

2. **Input Sanitization**
   - SQL injection prevention (prepared statements)
   - XSS prevention (htmlspecialchars)
   - File upload validation (type, size, extension)

3. **Data Integrity**
   - Foreign key constraints
   - Transaction rollback on errors
   - Closure locking mechanism

## Deployment Notes

1. **File Structure**
   ```
   contabilidad/
   ├── captura/
   │   ├── index.php
   │   ├── ctrl/
   │   │   ├── ctrl-ventas.php
   │   │   └── ctrl-admin.php
   │   ├── mdl/
   │   │   ├── mdl-ventas.php
   │   │   └── mdl-admin.php
   │   └── js/
   │       └── ventas.js
   ```

2. **Database Setup**
   - Run migration scripts in order
   - Create indexes
   - Insert initial configuration data

3. **Configuration**
   - Set database connection in _CRUD.php
   - Configure file upload directory
   - Set session timeout

4. **Dependencies**
   - Ensure CoffeeSoft framework is loaded
   - Verify jQuery and TailwindCSS are available
   - Check PHP extensions (PDO, mysqli)
