# Implementation Plan - Foreign Currency Module

- [x] 1. Set up database structure


  - Create `foreign_currency` table with fields: id, udn_id, name, code, conversion_value, active, created_at, updated_at
  - Add foreign key constraint to udn table
  - Add unique composite index on (udn_id, name)
  - Add index on udn_id for query optimization
  - _Requirements: 1.1, 2.1, 3.1, 4.1_





- [ ] 2. Implement model layer (mdl-moneda.php)
  - [x] 2.1 Create base model class extending CRUD

    - Set up constructor with database connection and utility classes
    - Define $bd property for database prefix
    - _Requirements: 1.1, 2.1_
  
  - [x] 2.2 Implement currency listing method

    - Create `listCurrencies()` method with filter parameters (udn_id, payment_method)
    - Use `_Select()` with LEFT JOIN to udn table
    - Return formatted array with currency data
    - _Requirements: 1.1, 5.1_
  

  - [ ] 2.3 Implement currency retrieval method
    - Create `getCurrencyById()` method accepting id parameter
    - Use `_Select()` to fetch single record
    - Return currency data array
    - _Requirements: 3.1_

  
  - [ ] 2.4 Implement currency creation method
    - Create `createCurrency()` method accepting data array
    - Use `_Insert()` to add new record
    - Return boolean success status

    - _Requirements: 2.1, 2.3_
  
  - [ ] 2.5 Implement currency update method
    - Create `updateCurrency()` method accepting data array with id
    - Use `_Update()` to modify existing record

    - Return boolean success status
    - _Requirements: 3.1, 3.4_
  
  - [x] 2.6 Implement duplicate validation method



    - Create `existsCurrencyByName()` method accepting name and udn_id
    - Use `_Select()` to check for existing records
    - Return boolean indicating existence
    - _Requirements: 2.3_

  
  - [ ] 2.7 Implement filter data methods
    - Create `lsUDN()` method to fetch business units list
    - Create `lsPaymentMethods()` method to fetch payment methods

    - Return formatted arrays for select dropdowns
    - _Requirements: 1.2, 5.1_

- [ ] 3. Implement controller layer (ctrl-moneda.php)
  - [ ] 3.1 Create base controller class extending mdl
    - Set up session validation

    - Implement request routing based on $_POST['opc']
    - _Requirements: 1.1_
  
  - [ ] 3.2 Implement init method
    - Call `lsUDN()` and `lsPaymentMethods()` from model

    - Return array with filter data for frontend
    - _Requirements: 1.2, 5.1_
  
  - [ ] 3.3 Implement list currencies method
    - Extract filter parameters from $_POST
    - Call `listCurrencies()` from model
    - Format response with table rows including action buttons

    - Return JSON response with row data
    - _Requirements: 1.1, 1.3, 1.4, 5.1, 5.2_
  
  - [ ] 3.4 Implement get currency method
    - Extract id from $_POST
    - Call `getCurrencyById()` from model

    - Return JSON response with currency data and status code
    - _Requirements: 3.1_
  
  - [ ] 3.5 Implement add currency method
    - Validate required fields (name, code, conversion_value, udn_id)
    - Check for duplicate using `existsCurrencyByName()`

    - Set active status to 1 and created_at timestamp
    - Call `createCurrency()` from model
    - Return JSON response with status code and message



    - _Requirements: 2.1, 2.2, 2.3, 2.6, 6.3, 6.4_
  
  - [ ] 3.6 Implement edit currency method
    - Extract id and updated fields from $_POST

    - Validate exchange rate is greater than zero
    - Call `updateCurrency()` from model
    - Return JSON response with status code and success message
    - _Requirements: 3.1, 3.2, 3.4, 3.5, 6.3, 6.4_

  
  - [ ] 3.7 Implement toggle status method
    - Extract id and current status from $_POST
    - Toggle active field (1 to 0 or 0 to 1)
    - Call `updateCurrency()` from model

    - Return JSON response with status code and confirmation message
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_
  
  - [ ] 3.8 Create helper functions
    - Implement `renderStatus()` function to generate status badges
    - Implement action button array generation for table rows

    - _Requirements: 1.4, 4.7_

- [ ] 4. Implement frontend layer (moneda.js)
  - [ ] 4.1 Create AdminForeignCurrency class extending Templates
    - Set up constructor with api link and root container
    - Define PROJECT_NAME property
    - _Requirements: 1.1_

  
  - [ ] 4.2 Implement render method
    - Call layout(), filterBar(), and lsCurrencies() methods
    - Initialize the module interface
    - _Requirements: 1.1_
  
  - [x] 4.3 Implement layout method

    - Use `primaryLayout()` to create main container structure
    - Define filterBar and container sections
    - Add module header with title and description
    - _Requirements: 1.1_
  
  - [ ] 4.4 Implement filterBar method
    - Use `createfilterBar()` to render filter controls
    - Add UDN select dropdown with onchange event

    - Add payment method select dropdown
    - Add "+ Agregar nueva moneda extranjera" button
    - _Requirements: 1.2, 1.3, 5.1_
  
  - [ ] 4.5 Implement lsCurrencies method
    - Extract filter values from filterBar

    - Use `createTable()` with coffeesoft theme
    - Configure table with columns: Moneda extranjera, Símbolo, Tipo de cambio (MXN), Acciones
    - Set center and right alignment for specific columns
    - Pass filter data to backend via data parameter
    - _Requirements: 1.1, 1.4, 5.1, 5.2_
  
  - [x] 4.6 Implement addCurrency method

    - Use `createModalForm()` to display add form
    - Call `jsonCurrency()` to get form field definitions
    - Set data.opc to 'addCurrency'
    - Implement success callback to show confirmation and refresh table
    - Implement error callback to display error messages

    - _Requirements: 2.1, 2.2, 2.6_
  
  - [ ] 4.7 Implement editCurrency method
    - Make async call to backend with opc: 'getCurrency' and id
    - Use `createModalForm()` with autofill parameter

    - Call `jsonCurrency()` to get form field definitions
    - Add red warning message about impact of changes
    - Set data.opc to 'editCurrency'
    - Implement success callback to show green confirmation and refresh table
    - _Requirements: 3.1, 3.2, 3.4, 3.5_
  

  - [ ] 4.8 Implement toggleStatus method
    - Use `swalQuestion()` to show confirmation dialog
    - Display appropriate message based on current status (activate/deactivate)
    - Set data.opc to 'toggleStatus' with id and new status
    - Implement success callback to refresh table
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_


  
  - [ ] 4.9 Implement jsonCurrency method
    - Return array of form field definitions
    - Include input for "Nombre del concepto" (name)
    - Include input for "Símbolo de la moneda" (code)
    - Include input for "Tipo de cambio (MXN)" (conversion_value) with numeric validation
    - Add btn-submit for form submission
    - _Requirements: 2.1, 3.1, 6.1, 6.2, 6.5_
  
  - [ ] 4.10 Implement client-side validation
    - Validate required fields before submission
    - Validate exchange rate is numeric and greater than zero
    - Format exchange rate to 2 decimal places on display
    - Show inline error messages for validation failures
    - _Requirements: 2.2, 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 5. Integrate module into accounting administration interface
  - Verify module appears in correct tab navigation
  - Ensure proper routing from main index.php
  - Test integration with existing UDN and payment method filters
  - _Requirements: 1.1, 1.2_

- [ ] 6. Implement error handling and user feedback
  - Add try-catch blocks in controller methods
  - Implement proper HTTP status codes (200, 400, 404, 409, 500)
  - Display user-friendly error messages in frontend
  - Log errors to server error log
  - _Requirements: 2.3, 2.6, 3.2, 3.5, 4.2, 4.5, 6.3, 6.4_

- [ ] 7. Add data validation and security measures
  - Implement SQL injection prevention using prepared statements
  - Sanitize user input using $this->util->sql()
  - Validate session and user permissions in controller
  - Escape output in table rendering to prevent XSS
  - _Requirements: All requirements (security applies globally)_

- [ ] 8. Optimize performance
  - Add database indexes as defined in schema
  - Implement pagination if currency list exceeds 50 records
  - Cache filter dropdown data (UDN, payment methods) for 5 minutes
  - Use DataTables for client-side sorting and filtering
  - _Requirements: 1.1, 5.1_

- [ ]* 9. Create documentation
  - Document API endpoints and request/response formats
  - Create user guide for currency management
  - Document database schema and relationships
  - Add inline code comments for complex logic
  - _Requirements: All requirements_

- [ ]* 10. Perform testing
  - Execute unit tests for model methods
  - Execute unit tests for controller methods
  - Execute integration tests for complete CRUD flow
  - Perform manual UI testing with checklist
  - Test validation scenarios (empty fields, duplicates, invalid rates)
  - Test status change scenarios (activate/deactivate)
  - Test filter functionality
  - Verify inactive currencies excluded from dropdowns but visible in history
  - _Requirements: All requirements_
