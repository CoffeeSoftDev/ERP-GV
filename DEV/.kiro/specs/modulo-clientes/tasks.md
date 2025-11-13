# Implementation Plan - Módulo de Clientes

## Task List

- [x] 1. Set up database structure and core tables



  - Create `clients` table with fields: id, name, phone, email, udn_id, current_balance, active, date_create, date_update
  - Create `credit_movements` table with fields: id, client_id, udn_id, movement_type, payment_method, amount, previous_balance, new_balance, description, capture_date, created_by, updated_by, date_create, date_update, active
  - Create `movement_audit_log` table with fields: id, movement_id, client_id, action, user_id, action_date, old_data, new_data
  - Add indexes for performance optimization: idx_client, idx_capture_date, idx_movement_type, idx_active, idx_udn
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 6.1_

- [ ] 2. Implement data model layer (mdl-clientes.php)
  - [x] 2.1 Create base model class extending CRUD


    - Initialize database connection with `$this->bd = "rfwsmqex_contabilidad."`
    - Initialize utility class `$this->util = new Utileria()`
    - _Requirements: 1.1, 2.1_


  - [ ] 2.2 Implement client management methods
    - Write `listClients($filters)` to retrieve active clients by UDN
    - Write `getClientById($id)` to get client details
    - Write `getClientBalance($clientId)` to retrieve current balance

    - _Requirements: 2.1, 2.2, 4.1_

  - [ ] 2.3 Implement movement management methods
    - Write `listMovements($filters)` to retrieve movements by date and filters
    - Write `createMovement($data)` to insert new movement record
    - Write `updateMovement($data)` to update existing movement
    - Write `deleteMovementById($id)` to soft delete movement

    - Write `getMovementById($id)` to retrieve movement details
    - _Requirements: 2.1, 2.7, 3.1, 3.2, 4.1_

  - [ ] 2.4 Implement balance calculation methods
    - Write `calculateClientBalance($clientId, $date)` to calculate balance at specific date


    - Write `getConsolidatedReport($filters)` to generate consolidated data by date range
    - Write `updateClientBalance($data)` to update client's current balance
    - _Requirements: 2.7, 4.5, 6.3_

  - [x] 2.5 Implement filter and utility methods


    - Write `lsMovementTypes()` to get movement type options
    - Write `lsPaymentMethods()` to get payment method options
    - Write `logMovementAction($data)` to log audit trail

    - _Requirements: 1.5, 2.1, 3.5_

- [ ] 3. Implement controller layer (ctrl-clientes.php)
  - [x] 3.1 Create base controller class extending mdl

    - Set up session validation
    - Initialize controller with required dependencies
    - _Requirements: 1.1, 5.6_

  - [ ] 3.2 Implement initialization method
    - Write `init()` to load clients, movement types, and payment methods for filters

    - Return data structure for frontend consumption
    - _Requirements: 1.1, 2.1_

  - [ ] 3.3 Implement movement listing method
    - Write `ls()` to retrieve movements for selected date

    - Format data for table display with proper columns
    - Generate action buttons (view, edit, delete) based on user permissions
    - Calculate and return daily totals (consumptions, cash payments, bank payments)
    - _Requirements: 1.4, 1.5, 1.6, 5.1_

  - [ ] 3.4 Implement consolidated report method
    - Write `lsConcentrado()` to generate consolidated report by date range
    - Calculate initial balance, total consumptions, total payments, and final balance per client

    - Format data with color-coded columns for consumptions (green) and payments (orange)
    - _Requirements: 6.1, 6.2, 6.3, 6.8_

  - [ ] 3.5 Implement movement creation method
    - Write `addMovimiento()` to create new movement
    - Validate required fields (client_id, movement_type, amount)
    - Validate payment method based on movement type (N/A for consumo, Efectivo/Banco for payments)

    - Calculate new balance based on movement type
    - Update client balance after successful creation
    - Log audit entry
    - _Requirements: 2.1, 2.4, 2.5, 2.6, 2.7, 2.8_


  - [ ] 3.6 Implement movement editing method
    - Write `editMovimiento()` to update existing movement
    - Validate movement data
    - Recalculate client balance
    - Update movement record


    - Log audit entry
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ] 3.7 Implement movement retrieval method
    - Write `getMovimiento()` to retrieve movement details by ID
    - Include client information, movement data, and audit information
    - Calculate and return financial summary (previous balance, amount, new balance)


    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ] 3.8 Implement movement deletion method
    - Write `deleteMovimiento()` to soft delete movement

    - Log deletion action with user and timestamp
    - Recalculate client balance after deletion
    - _Requirements: 3.4, 3.5, 3.6_

  - [x] 3.9 Implement utility methods

    - Write `dropdown($id, $status)` to generate action buttons
    - Write `renderMovementType($type)` to render movement type badges
    - Write `renderPaymentMethod($method)` to render payment method badges
    - Write `calculateBalance($clientId)` to get current balance
    - Write `validateMovementType()` to validate movement type and payment method combination
    - _Requirements: 1.4, 2.4, 2.5_


- [ ] 4. Implement frontend application (clientes.js)
  - [ ] 4.1 Create main App class extending Templates
    - Initialize with `PROJECT_NAME = "clientes"`
    - Set up API link to `ctrl/ctrl-clientes.php`
    - Implement constructor with link and div_modulo parameters

    - _Requirements: 1.1_

  - [ ] 4.2 Implement initialization and layout methods
    - Write `init()` to initialize module and load initial data
    - Write `render()` to orchestrate layout and component rendering
    - Write `layout()` to create primary layout with tab structure (Dashboard, Movimientos, Concentrado)
    - _Requirements: 1.1, 1.3_


  - [ ] 4.3 Implement dashboard tab
    - Write `renderDashboard()` to display dashboard with KPI cards
    - Create three info cards: Total consumos, Total pagos efectivo, Total pagos banco
    - Add buttons: "Concentrado de clientes" and "Registrar nuevo movimiento"
    - Update cards automatically when date changes
    - _Requirements: 1.2, 1.3, 1.6_


  - [ ] 4.4 Implement movements tab
    - Write `filterBarMovimientos()` to create filter bar with date picker and movement type filter
    - Write `lsMovimientos()` to display movements table with columns: Cliente, Tipo de movimiento, Método de pago, Monto, Acciones
    - Implement table with CoffeeSoft's `createTable()` component
    - Add action buttons: view details, edit, delete
    - _Requirements: 1.4, 1.5, 1.6, 5.1_


  - [ ] 4.5 Implement movement registration form
    - Write `addMovimiento()` to show modal form for new movement
    - Write `jsonMovimiento()` to define form structure with fields: Cliente, Deuda actual (readonly), Tipo de movimiento, Método de pago, Cantidad, Descripción
    - Implement dynamic payment method behavior: disable for "Consumo", enable for "Abono parcial" and "Pago total"
    - Validate all required fields before submission

    - Show success/error messages after submission
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.8, 2.9_

  - [ ] 4.6 Implement movement editing functionality
    - Write `editMovimiento(id)` to show modal form with pre-filled data
    - Fetch movement data using `getMovimiento()` API call
    - Use same form structure as add form with autofill
    - Prevent changing client association

    - Update table after successful edit
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ] 4.7 Implement movement details view
    - Write `viewMovimiento(id)` to show modal with complete movement information
    - Display client information section
    - Display movement details section (type, payment method, description)
    - Display financial summary section (previous balance, amount, new balance)
    - Display audit information (updated by, date/time)
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ] 4.8 Implement movement deletion
    - Write `deleteMovimiento(id)` to show confirmation dialog
    - Use CoffeeSoft's `swalQuestion()` component
    - Show message: "¿Está seguro de querer eliminar el movimiento a crédito?"
    - Update table after successful deletion
    - _Requirements: 3.4, 3.5, 3.6, 3.7, 3.8_

  - [ ] 4.9 Implement consolidated report tab
    - Write `filterBarConcentrado()` to create filter bar with date range picker
    - Write `lsConcentrado()` to display consolidated report table
    - Show columns: Cliente, Saldo inicial, Consumos (green), Pagos (orange), Saldo final
    - Implement expandable rows to show individual movement details
    - Add "Exportar a Excel" button
    - Update data automatically when date range changes
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8_

  - [ ] 4.10 Implement utility methods
    - Write `calculateNewBalance()` to calculate balance after movement
    - Write `updateDashboardTotals()` to refresh dashboard cards
    - Write `validateMovementForm()` to validate form data before submission
    - _Requirements: 2.6, 4.5_

- [ ] 5. Implement access level control
  - [x] 5.1 Implement session-based access validation


    - Add access level validation in controller `init()` method
    - Store user access level in session
    - _Requirements: 5.1, 5.6_


  - [ ] 5.2 Implement level 1 (Captura) restrictions
    - Restrict to current date movements only
    - Allow register, modify, and view operations
    - Hide consolidated report and export options

    - _Requirements: 5.2_

  - [ ] 5.3 Implement level 2 (Gerencia) permissions
    - Allow access to consolidated report
    - Enable export to Excel functionality

    - Allow viewing all date ranges
    - _Requirements: 5.3_

  - [ ] 5.4 Implement level 3 (Contabilidad/Dirección) permissions
    - Add UDN filter to all views
    - Disable edit and delete operations



    - Allow viewing and exporting only
    - _Requirements: 5.4_

  - [x] 5.5 Implement level 4 (Administración) permissions


    - Enable client management functionality
    - Add module lock/unlock controls
    - Allow all operations across all UDNs
    - _Requirements: 5.5_

  - [ ] 5.6 Implement dynamic UI rendering based on access level
    - Hide/show buttons based on user permissions
    - Disable form fields for read-only users
    - Show appropriate error messages for unauthorized actions
    - _Requirements: 5.6, 5.7_

- [ ] 6. Implement Sales module integration
  - Create sync method to update sales totals when movements are created/updated/deleted
  - Implement real-time synchronization of consumption and payment totals
  - Add validation to ensure data consistency between modules
  - _Requirements: 1.7, 6.9_

- [ ] 7. Implement Excel export functionality
  - Create export method in controller to generate Excel file from consolidated report
  - Include all columns: Cliente, Saldo inicial, Consumos, Pagos, Saldo final
  - Format cells with proper currency formatting
  - Add date range and generation timestamp to export
  - _Requirements: 6.6_

- [ ] 8. Create entry point file (index.php)
  - Set up HTML structure with `<div id="root"></div>`
  - Include CoffeeSoft framework scripts: `coffeSoft.js` and `plugins.js`
  - Include module script: `clientes.js`
  - Initialize application on document ready
  - _Requirements: 1.1_

- [ ] 9. Integration and end-to-end testing
  - Test complete movement registration flow (add → update balance → sync with sales)
  - Test movement editing flow (edit → recalculate balance → update)
  - Test movement deletion flow (delete → recalculate balance → log audit)
  - Test consolidated report generation with various date ranges
  - Test Excel export functionality
  - Test access level restrictions for all user types
  - Verify real-time synchronization with sales module
  - Test form validation for all edge cases
  - _Requirements: All_
