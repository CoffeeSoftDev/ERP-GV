# Implementation Plan - Módulo de Cuentas de Ventas

- [x] 1. Set up database schema and initial configuration


  - Create `categoria_venta` table with all required fields (id, udn_id, nombre, permiso_descuento, permiso_cortesia, impuesto_iva, impuesto_ieps, impuesto_hospedaje, impuesto_cero, activo, fecha_creacion, fecha_modificacion)
  - Add foreign key constraint to udn table
  - Create unique index on (udn_id, nombre) to prevent duplicates
  - Insert sample data for testing
  - _Requirements: 1.1, 2.6_



- [ ] 2. Implement Model Layer (mdl-cuenta-venta.php)
  - [ ] 2.1 Create base model class structure
    - Extend CRUD class
    - Initialize $bd and $util properties

    - Require configuration files (_CRUD.php, _Utileria.php)
    - _Requirements: 1.1, 2.1_
  
  - [ ] 2.2 Implement data retrieval methods
    - Write `listSalesAccount()` method using _Select() with JOIN to udn table

    - Write `getSalesAccountById()` method for single record retrieval
    - Write `lsUDN()` method to fetch business units for filter dropdown
    - _Requirements: 1.1, 1.2, 3.2_

  
  - [ ] 2.3 Implement data validation methods
    - Write `existsSalesAccountByName()` method to check for duplicate names per UDN
    - _Requirements: 2.6_
  


  - [ ] 2.4 Implement data modification methods
    - Write `createSalesAccount()` method using _Insert()
    - Write `updateSalesAccount()` method using _Update()
    - _Requirements: 2.4, 3.4_


- [ ] 3. Implement Controller Layer (ctrl-cuenta-venta.php)
  - [x] 3.1 Create base controller class structure


    - Extend mdl class
    - Add session_start() and POST validation
    - Implement dynamic method calling via $_POST['opc']
    - _Requirements: 1.1, 2.1_

  
  - [ ] 3.2 Implement initialization endpoint
    - Write `init()` method to return UDN list for filter
    - _Requirements: 1.1_
  
  - [x] 3.3 Implement list endpoint

    - Write `lsSalesAccount()` method to fetch and format data for table
    - Build row array with formatted columns (Categoría, Permisos, Impuestos, Acciones)
    - Add action buttons (Edit, Activate/Deactivate) to each row
    - _Requirements: 1.2, 4.6_

  
  - [ ] 3.4 Implement create endpoint
    - Write `addSalesAccount()` method
    - Validate uniqueness using `existsSalesAccountByName()`
    - Call `createSalesAccount()` if validation passes

    - Return standardized response with status and message
    - _Requirements: 2.4, 2.5, 2.6_
  
  - [ ] 3.5 Implement read endpoint
    - Write `getSalesAccount()` method to fetch single record by ID
    - Return data for form pre-filling
    - _Requirements: 3.2_


  
  - [ ] 3.6 Implement update endpoint
    - Write `editSalesAccount()` method
    - Call `updateSalesAccount()` with formatted data

    - Return standardized response
    - _Requirements: 3.4, 3.5_
  
  - [ ] 3.7 Implement status toggle endpoint
    - Write `statusSalesAccount()` method

    - Toggle activo field (0/1)
    - Call `updateSalesAccount()` with new status
    - Return standardized response
    - _Requirements: 4.2, 4.4, 4.5_

- [x] 4. Implement Frontend JavaScript (cuenta-venta.js)

  - [ ] 4.1 Create base class structure
    - Create SalesAccountManager class extending Templates
    - Initialize PROJECT_NAME, _link, and _div_modulo properties
    - Implement constructor
    - _Requirements: 1.1_
  
  - [x] 4.2 Implement layout methods

    - Write `render()` method to orchestrate initialization
    - Write `layout()` method using `primaryLayout()` component
    - Create container structure with filterBar and main container
    - _Requirements: 1.1_
  
  - [ ] 4.3 Implement filter bar
    - Write `filterBar()` method using `createfilterBar()` component


    - Add UDN select dropdown with default value "Baos"
    - Add "Agregar nueva categoría" button
    - Wire onchange event to refresh table
    - _Requirements: 1.1, 1.3, 1.4_
  
  - [ ] 4.4 Implement table display
    - Write `lsSalesAccount()` method using `createTable()` component
    - Configure table with corporativo theme
    - Define columns: Categoría, Descuento, Cortesía, IVA, IEPS, Hospedaje, Impuesto 0%, Acciones
    - Add action buttons (Edit, Activate/Deactivate) with onclick handlers
    - Configure center and right alignment for specific columns
    - _Requirements: 1.2, 4.6_
  



  - [ ] 4.5 Implement create functionality
    - Write `addSalesAccount()` method using `createModalForm()` component
    - Write `jsonSalesAccount()` method with form field definitions
    - Add fields: UDN select, nombre input, permisos checkboxes, impuestos checkboxes
    - Add validation warning message about Soft-Restaurant
    - Implement success callback to refresh table and show alert
    - _Requirements: 2.1, 2.2, 2.3, 2.5_
  
  - [ ] 4.6 Implement edit functionality
    - Write `editSalesAccount(id)` async method
    - Fetch existing data using `useFetch()` with opc: 'getSalesAccount'

    - Open modal with `createModalForm()` using autofill option
    - Add warning message about Soft-Restaurant synchronization
    - Implement success callback to refresh table
    - _Requirements: 3.1, 3.2, 3.3, 3.5_
  

  - [x] 4.7 Implement status toggle functionality

    - Write `statusSalesAccount(id, currentStatus)` method using `swalQuestion()` component
    - Show different confirmation messages for activate vs deactivate
    - Send AJAX request with opc: 'statusSalesAccount'
    - Implement success callback to refresh table and show alert
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 5. Create main HTML entry point


  - Create or update `contabilidad/administrador/index.php`
  - Add tab for "Cuentas de ventas" in existing tab layout
  - Include script tag for cuenta-venta.js
  - Ensure CoffeeSoft framework scripts are loaded (coffeSoft.js, plugins.js)
  - Add container div with id="root" or appropriate parent
  - _Requirements: 1.1_

- [ ] 6. Implement helper functions
  - Create `renderStatus()` function to display active/inactive badges
  - Create `renderCheckbox()` function to display permission/tax checkboxes in table
  - Add these functions after controller class in ctrl-cuenta-venta.php
  - _Requirements: 1.2, 4.6_

- [ ] 7. Integration and wiring
  - Initialize SalesAccountManager instance on document ready
  - Call render() method to start the module
  - Verify all AJAX endpoints are correctly wired
  - Test UDN filter triggers table refresh
  - Test all button onclick handlers work correctly
  - _Requirements: 1.1, 1.4_

- [ ] 8. Implement error handling and validation
  - Add client-side validation for required fields
  - Add server-side validation for duplicate names
  - Implement error message display using alert() component
  - Add try-catch blocks in controller methods
  - Return standardized error responses
  - _Requirements: 2.6_

- [ ] 9. Add visual feedback and UX improvements
  - Implement loading indicators during AJAX operations
  - Add success/error alerts with appropriate icons
  - Style active/inactive icons (blue for active, red for inactive)
  - Ensure responsive table design
  - Add hover effects on action buttons
  - _Requirements: 4.5, 4.6_

- [ ]* 10. Testing and quality assurance
  - [ ]* 10.1 Test create functionality
    - Test creating new category with valid data
    - Test duplicate name validation
    - Verify warning message displays
    - Verify table refreshes after creation
    - _Requirements: 2.4, 2.5, 2.6_
  
  - [ ]* 10.2 Test edit functionality
    - Test editing existing category
    - Verify form pre-fills correctly
    - Verify warning message displays
    - Verify table updates after edit
    - _Requirements: 3.2, 3.4, 3.5_
  
  - [ ]* 10.3 Test status toggle functionality
    - Test deactivating active category
    - Test activating inactive category
    - Verify confirmation messages display correctly
    - Verify visual state changes (icon colors)
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [ ]* 10.4 Test filter functionality
    - Test UDN filter changes table data
    - Verify default UDN selection
    - Test with multiple UDNs
    - _Requirements: 1.1, 1.4_
  
  - [ ]* 10.5 Test error scenarios
    - Test with invalid data
    - Test with network errors
    - Test with database errors
    - Verify error messages display correctly
    - _Requirements: 2.6_
