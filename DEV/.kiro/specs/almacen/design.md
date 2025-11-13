# Design Document - Módulo de Almacén

## Overview

El módulo de Almacén es un sistema completo de gestión de entradas y salidas de almacén que permite el control de inventarios, trazabilidad de movimientos y generación de reportes según niveles de acceso. El sistema se integra con el framework CoffeeSoft y sigue la arquitectura MVC.

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer (JS)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   App Class  │  │ Warehouse    │  │  Report      │      │
│  │  (Main View) │  │   Output     │  │  Summary     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            ↓ AJAX
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer (PHP)                     │
│                    ctrl-almacen.php                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │   init   │  │    ls    │  │   add    │  │   edit   │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↓ SQL
┌─────────────────────────────────────────────────────────────┐
│                     Model Layer (PHP)                        │
│                     mdl-almacen.php                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  list    │  │  create  │  │  update  │  │  delete  │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│  warehouse_output | warehouse_input | product | product_class│
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend**: jQuery, CoffeeSoft Framework, TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Components**: Templates, Components, Complements (CoffeeSoft)

## Components and Interfaces

### Frontend Components (almacen.js)

#### 1. App Class (Main Module)
```javascript
class App extends Templates {
    - PROJECT_NAME: "almacen"
    - render(): Initializes the module
    - layout(): Creates primary layout with tabs
    - filterBar(): Date picker and filters
    - ls(): Lists warehouse outputs
    - addWarehouseOutput(): Opens modal to add output
    - editWarehouseOutput(id): Opens modal to edit output
    - deleteWarehouseOutput(id): Confirms and deletes output
    - showDescription(id): Displays description modal
}
```

#### 2. WarehouseReport Class
```javascript
class WarehouseReport extends App {
    - renderReport(): Displays warehouse summary
    - filterBarReport(): Date range and business unit filters
    - lsReport(): Lists consolidated report
    - exportToExcel(): Exports report to Excel
    - toggleDetails(warehouseId): Expands/collapses detail rows
}
```

#### 3. FileUpload Class
```javascript
class FileUpload extends App {
    - uploadFile(): Handles file upload (max 20MB)
    - listFiles(): Lists uploaded files
    - deleteFile(id): Removes uploaded file
}
```

### Backend Components

#### Controller (ctrl-almacen.php)
```php
class ctrl extends mdl {
    - init(): Returns filters (UDN, products, access level)
    - ls(): Lists warehouse outputs by date
    - addWarehouseOutput(): Creates new output
    - editWarehouseOutput(): Updates existing output
    - getWarehouseOutput(): Gets output by ID
    - deleteWarehouseOutput(): Deletes output and logs action
    - statusWarehouseOutput(): Changes output status
    - lsReport(): Gets consolidated report
    - exportReport(): Generates Excel export
    - uploadFile(): Handles file upload
    - getAccessLevel(): Returns user access level
}
```

#### Model (mdl-almacen.php)
```php
class mdl extends CRUD {
    - listWarehouseOutput($params): Lists outputs with filters
    - createWarehouseOutput($data): Inserts new output
    - updateWarehouseOutput($data): Updates output
    - getWarehouseOutputById($id): Gets single output
    - deleteWarehouseOutputById($id): Deletes output
    - listWarehouseReport($params): Gets report data
    - listProducts(): Gets product list
    - listProductClass(): Gets product classes
    - listBusinessUnits(): Gets UDN list
    - getBalance($params): Calculates balance
    - logAudit($data): Logs actions to audit_log
}
```

## Data Models

### Database Schema

#### Table: warehouse_output
```sql
CREATE TABLE warehouse_output (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    udn_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    operation_date DATE NOT NULL,
    user_id INT NOT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (udn_id) REFERENCES usuarios(idUDN)
);
```

#### Table: product
```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id)
);
```

#### Table: product_class
```sql
CREATE TABLE product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1
);
```

#### Table: file
```sql
CREATE TABLE file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    size_bytes INT,
    path TEXT,
    extension CHAR(5),
    operation_date DATE
);
```

#### Table: audit_log
```sql
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    record_id INT,
    name_table VARCHAR(255),
    name_user VARCHAR(50),
    name_udn VARCHAR(50),
    name_collaborator VARCHAR(255),
    action ENUM('create', 'update', 'delete', 'view'),
    change_items LONGTEXT,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Data Flow

#### Create Warehouse Output Flow
```
User Input → Validation → Controller (addWarehouseOutput) 
→ Model (createWarehouseOutput) → Database Insert 
→ Audit Log → Response → UI Update
```

#### View Report Flow
```
User Selects Date Range → Controller (lsReport) 
→ Model (listWarehouseReport + getBalance) 
→ Calculate Totals → Format Response → Render Table
```

## Error Handling

### Frontend Error Handling
```javascript
- Form validation before submission
- Required field validation
- Numeric field validation for amounts
- File size validation (max 20MB)
- Date range validation
- Display user-friendly error messages using alert()
```

### Backend Error Handling
```php
- Input sanitization using $this->util->sql()
- SQL injection prevention via prepared statements
- Transaction rollback on errors
- Detailed error logging
- Standardized error responses:
  {
    status: 500,
    message: "Error description"
  }
```

### Database Error Handling
```sql
- Foreign key constraints
- NOT NULL constraints on critical fields
- Default values for status fields
- Cascading deletes where appropriate
```

## Testing Strategy

### Unit Testing
- Test each controller method independently
- Test model CRUD operations
- Test data validation functions
- Test calculation functions (balances, totals)

### Integration Testing
- Test complete user flows (add → edit → delete)
- Test report generation with various filters
- Test file upload and retrieval
- Test access level restrictions

### User Acceptance Testing
- Level 1 users: Test capture and edit functionality
- Level 2 users: Test report viewing and Excel export
- Level 3 users: Test read-only access with filters
- Level 4 users: Test admin functions

### Test Cases

#### TC-001: Add Warehouse Output
```
Given: User is logged in with Level 1 access
When: User fills form with valid data and submits
Then: Output is created, table updates, success message displays
```

#### TC-002: Delete Warehouse Output
```
Given: User has an existing output record
When: User clicks delete and confirms
Then: Record is deleted, audit log is created, table updates
```

#### TC-003: View Report
```
Given: User is logged in with Level 2+ access
When: User selects date range and business unit
Then: Report displays with correct totals and balances
```

#### TC-004: Access Level Restriction
```
Given: User has Level 3 access
When: User attempts to edit a record
Then: Edit button is not visible/disabled
```

## Security Considerations

### Authentication & Authorization
- Session-based authentication
- Access level validation on every request
- Role-based UI rendering
- Server-side permission checks

### Data Protection
- SQL injection prevention via prepared statements
- XSS prevention via output escaping
- CSRF token validation
- File upload validation (type, size)

### Audit Trail
- Log all create, update, delete operations
- Store user ID, timestamp, and changed data
- Immutable audit log records

## Performance Considerations

### Database Optimization
- Indexes on foreign keys
- Indexes on date fields for filtering
- Composite indexes for common queries
- Query result caching where appropriate

### Frontend Optimization
- Lazy loading for large tables
- Pagination (15 records per page)
- Debouncing on search inputs
- Minimal DOM manipulation

## Design Decisions

### Why Tabs Instead of Separate Pages?
- Better UX with single-page navigation
- Faster transitions between views
- Maintains filter state across tabs
- Follows CoffeeSoft patterns

### Why Separate Classes for Report?
- Separation of concerns
- Easier maintenance
- Independent testing
- Code reusability

### Why Modal Forms?
- Consistent with CoffeeSoft patterns
- Better focus on data entry
- Prevents navigation loss
- Mobile-friendly

### Why Daily Totals Display?
- Immediate feedback for users
- Quick validation of data entry
- Meets business requirement
- Enhances user confidence

## Future Enhancements

- Real-time notifications for low stock
- Barcode scanning integration
- Mobile app for warehouse staff
- Advanced analytics dashboard
- Automated reorder suggestions
- Multi-warehouse support
