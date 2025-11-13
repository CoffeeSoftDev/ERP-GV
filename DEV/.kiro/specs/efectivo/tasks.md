# Implementation Plan - Módulo de Efectivo

## Task List

- [x] 1. Setup database structure




  - [ ] 1.1 Create cash_concept table with indexes and constraints
    - Create table with fields: id, udn_id, name, operation_type, description, active, date_creation
    - Add foreign key constraint to udn table
    - Add unique constraint on (udn_id, name)

    - _Requirements: 1.1, 6.3_
  
  - [ ] 1.2 Create cash_movement table with relationships
    - Create table with fields: id, udn_id, concept_id, movement_type, amount, description, user_id, date_creation, active
    - Add foreign key constraints to udn, cash_concept, and users tables

    - Add indexes on foreign keys for performance
    - _Requirements: 2.1, 2.5_
  




  - [ ] 1.3 Create cash_closure table for cash closures
    - Create table with fields: id, udn_id, total_amount, closure_date, user_id, notes
    - Add foreign key constraints to udn and users tables

    - _Requirements: 4.3_

- [ ] 2. Implement backend model (mdl-efectivo.php)
  - [ ] 2.1 Create base model structure extending CRUD
    - Implement constructor with $bd and $util properties
    - Require CRUD and Utileria classes
    - _Requirements: All_

  
  - [ ] 2.2 Implement cash concept CRUD methods
    - Create listConceptos() for filtered listing
    - Create getConceptoById() for single record retrieval
    - Create createConcepto() for inserting new concepts
    - Create updateConcepto() for updating existing concepts
    - Create existsConceptoByName() for duplicate validation

    - _Requirements: 6.1, 6.2, 6.3, 6.5_
  
  - [ ] 2.3 Implement cash movement CRUD methods
    - Create listMovimientos() for filtered listing with joins
    - Create getMovimientoById() for single record retrieval

    - Create createMovimiento() for inserting new movements
    - Create updateMovimiento() for updating existing movements
    - Create getAvailableAmount() to calculate current balance
    - _Requirements: 2.1, 2.5, 3.1, 3.4_



  
  - [ ] 2.4 Implement filter data methods
    - Create lsUDN() to get business units list
    - Create lsOperationType() to get operation types

    - Create lsStatus() to get status options
    - _Requirements: 1.3_
  
  - [x] 2.5 Implement cash closure methods

    - Create createClosure() to register cash closure
    - Create getLastClosure() to check if closure exists
    - Create lockMovements() to prevent new movements after closure
    - _Requirements: 4.3, 4.5_

- [ ] 3. Implement backend controller (ctrl-efectivo.php)
  - [x] 3.1 Create controller structure extending model

    - Implement class ctrl extending mdl
    - Require mdl-efectivo.php
    - _Requirements: All_
  
  - [ ] 3.2 Implement init() method for filter initialization
    - Call lsUDN(), lsOperationType(), lsStatus()

    - Return array with filter data
    - _Requirements: 1.3_
  
  - [ ] 3.3 Implement cash concept controller methods
    - Create lsConceptos() to list concepts with table formatting
    - Create getConcepto() to retrieve single concept

    - Create addConcepto() with duplicate validation
    - Create editConcepto() to update concept
    - Create statusConcepto() to toggle active status



    - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6_
  
  - [ ] 3.4 Implement cash movement controller methods
    - Create lsMovimientos() to list movements with table formatting

    - Create getMovimiento() to retrieve single movement
    - Create addMovimiento() with amount validation
    - Create editMovimiento() to update movement
    - _Requirements: 2.1, 2.2, 2.5, 3.1, 3.4_
  

  - [ ] 3.5 Implement cash closure controller method
    - Create closeCash() to process closure
    - Validate no pending movements
    - Calculate total amount

    - Lock future movements
    - _Requirements: 4.1, 4.2, 4.3, 4.4_
  
  - [ ] 3.6 Create helper functions
    - Implement renderStatus() for status badges

    - Implement dropdown() for action menus
    - _Requirements: 1.4_

- [ ] 4. Implement frontend main class (efectivo.js - App)
  - [x] 4.1 Create App class extending Templates

    - Set PROJECT_NAME to "efectivo"
    - Initialize constructor with link and div_modulo
    - _Requirements: All_
  
  - [x] 4.2 Implement layout structure

    - Create render() method to initialize interface
    - Create layout() method with primaryLayout
    - Implement tabLayout with "Conceptos" and "Movimientos" tabs
    - _Requirements: 1.1_

  

  - [ ] 4.3 Implement filter bar for concepts
    - Create filterBarConceptos() with UDN and status filters
    - Add "Nuevo Concepto" button

    - _Requirements: 1.3_
  
  - [ ] 4.4 Implement concept listing
    - Create lsConceptos() using createTable
    - Configure table with corporativo theme

    - Display columns: Concepto, Tipo, Descripción, Estado, Acciones
    - _Requirements: 1.2, 6.1_
  
  - [ ] 4.5 Implement add concept functionality
    - Create addConcepto() with createModalForm

    - Create jsonConcepto() with form structure
    - Implement success callback to refresh table
    - _Requirements: 6.2_
  
  - [ ] 4.6 Implement edit concept functionality
    - Create editConcepto(id) with async data fetch

    - Use createModalForm with autofill
    - Implement success callback
    - _Requirements: 6.5_
  
  - [ ] 4.7 Implement status toggle for concepts
    - Create statusConcepto(id, active) with swalQuestion

    - Show appropriate confirmation messages
    - Refresh table on success
    - _Requirements: 6.6, 5.1, 5.2_

- [x] 5. Implement frontend movement class (efectivo.js - CashMovement)



  - [ ] 5.1 Create CashMovement class extending App
    - Initialize with proper inheritance
    - _Requirements: 2.1, 3.1_
  
  - [x] 5.2 Implement filter bar for movements

    - Create filterBarMovimientos() with UDN, date range, and concept filters
    - Add "Nuevo Movimiento" button
    - Integrate dataPicker component
    - _Requirements: 1.3_
  

  - [ ] 5.3 Implement movement listing
    - Create lsMovimientos() using createTable
    - Display columns: Fecha, Concepto, Tipo, Monto, Usuario, Acciones
    - Format amounts with formatPrice()
    - _Requirements: 1.2_

  
  - [ ] 5.4 Implement add movement functionality
    - Create addMovimiento() with createModalForm
    - Create jsonMovimiento() with form structure



    - Implement amount validation (numeric, > 0)
    - Show success message and refresh table
    - _Requirements: 2.1, 2.2, 2.3, 2.5, 2.6_
  
  - [x] 5.5 Implement edit movement functionality

    - Create editMovimiento(id) with async data fetch
    - Use createModalForm with autofill
    - Show warning if movement type changes
    - Implement success callback

    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [ ] 5.6 Implement cash closure functionality
    - Create closeCashFlow() with swalQuestion

    - Show summary of pending movements
    - Display confirmation message on success
    - Disable movement creation after closure
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_




- [ ] 6. Implement UI components and styling
  - [ ] 6.1 Configure table themes
    - Apply corporativo theme to tables
    - Set center alignment for status columns

    - Set right alignment for amount columns
    - _Requirements: 1.1_
  
  - [ ] 6.2 Implement modal forms styling
    - Use TailwindCSS classes for forms

    - Apply consistent spacing and colors
    - Add validation error styling
    - _Requirements: 2.1, 3.1, 6.2_
  
  - [ ] 6.3 Create status badges
    - Implement active/inactive badges with colors
    - Use green for active (#8CC63F)
    - Use red for inactive
    - _Requirements: 1.2, 5.1, 5.2_
  
  - [ ] 6.4 Implement action buttons and dropdowns
    - Create edit and status toggle buttons
    - Use icon-pencil and icon-toggle icons
    - Apply hover effects
    - _Requirements: 1.4_

- [ ] 7. Integration and wiring
  - [ ] 7.1 Connect frontend to backend API
    - Configure api variable to point to ctrl-efectivo.php
    - Implement useFetch calls for all operations
    - Handle success and error responses
    - _Requirements: All_
  
  - [ ] 7.2 Implement data flow for concepts
    - Wire add, edit, list, and status operations
    - Ensure table refresh after each operation
    - _Requirements: 6.1, 6.2, 6.5, 6.6_
  
  - [ ] 7.3 Implement data flow for movements
    - Wire add, edit, and list operations
    - Update available amount after each movement
    - _Requirements: 2.1, 2.5, 3.1, 3.4_
  
  - [ ] 7.4 Implement cash closure flow
    - Connect closure button to backend



    - Lock movements after successful closure
    - Display appropriate messages
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ] 8. Validation and error handling
  - [ ] 8.1 Implement frontend validations
    - Validate amount is numeric and > 0
    - Validate required fields
    - Show validation errors to user
    - _Requirements: 2.2, 2.3_
  

  - [ ] 8.2 Implement backend validations
    - Validate duplicate concept names
    - Validate amount format and value
    - Return appropriate status codes (200, 400, 409, 500)
    - _Requirements: 2.2, 6.3_
  
  - [ ] 8.3 Implement error messages
    - Create Spanish error messages
    - Display user-friendly alerts
    - Log errors for debugging
    - _Requirements: All_

- [ ] 9. Testing and quality assurance
  - [ ]* 9.1 Test concept CRUD operations
    - Test create, read, update, and status toggle
    - Verify duplicate validation
    - Test with different UDNs
    - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6_
  
  - [ ]* 9.2 Test movement CRUD operations
    - Test create and edit movements
    - Verify amount calculations
    - Test with different movement types
    - _Requirements: 2.1, 2.5, 3.1, 3.4_
  
  - [ ]* 9.3 Test cash closure process
    - Test closure with pending movements
    - Verify movement locking after closure
    - Test closure message display
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [ ]* 9.4 Test UI responsiveness
    - Test on different screen sizes
    - Verify table pagination
    - Test modal behavior
    - _Requirements: 1.1_
  
  - [ ]* 9.5 Test error scenarios
    - Test with invalid data
    - Test duplicate entries
    - Test permission errors
    - _Requirements: All_

- [ ] 10. Documentation and deployment
  - [ ]* 10.1 Document API endpoints
    - Document all controller methods
    - Specify request/response formats
    - _Requirements: All_
  
  - [ ]* 10.2 Create user guide
    - Document how to add concepts
    - Document how to register movements
    - Document cash closure process
    - _Requirements: All_
  
  - [ ] 10.3 Deploy to production
    - Upload files to server
    - Run database migrations
    - Configure permissions
    - Test in production environment
    - _Requirements: All_
