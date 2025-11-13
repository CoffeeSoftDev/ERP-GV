# Implementation Plan - Módulo de Formas de Pago

- [x] 1. Set up database structure


  - Create `payment_methods` table with fields: id, name, active, date_creation, date_updated
  - Add UNIQUE constraint on name field
  - Add index on name field for performance
  - Insert initial test data (Efectivo, Transferencia, Tarjeta de débito, Tarjeta de crédito)
  - _Requirements: 1.1, 2.3, 4.4_




- [ ] 2. Implement data model (mdl-formasPago.php)
  - [ ] 2.1 Create base model class structure
    - Extend CRUD class
    - Initialize $bd property with "rfwsmqex_contabilidad."

    - Initialize $util property with Utileria instance
    - _Requirements: 1.2, 2.1_

  - [ ] 2.2 Implement listFormasPago() method
    - Use _Select() to query all payment methods
    - Include fields: id, name, active, date_creation

    - Order by id DESC
    - Accept active status filter parameter
    - _Requirements: 1.2, 1.3_


  - [ ] 2.3 Implement getFormaPagoById() method
    - Use _Select() with WHERE id = ?
    - Return single payment method record
    - _Requirements: 3.2_


  - [ ] 2.4 Implement existsFormaPagoByName() method
    - Use _Select() to check for duplicate names
    - Case-insensitive comparison using LOWER()
    - Return boolean result
    - _Requirements: 2.3_


  - [ ] 2.5 Implement createFormaPago() method
    - Use _Insert() to add new payment method
    - Accept array with values and data



    - Return insertion result
    - _Requirements: 2.5_

  - [ ] 2.6 Implement updateFormaPago() method
    - Use _Update() to modify payment method
    - Accept array with values, where, and data

    - Return update result
    - _Requirements: 3.3, 4.4_

- [ ] 3. Implement controller (ctrl-formasPago.php)
  - [ ] 3.1 Create base controller class structure
    - Extend mdl class
    - Add session_start()

    - Validate $_POST['opc'] exists
    - Require mdl-formasPago.php
    - _Requirements: 1.1_

  - [x] 3.2 Implement lsFormasPago() method

    - Call model's listFormasPago() with active filter
    - Build $__row array with formatted data
    - Add edit button with onclick: "formasPago.editFormaPago(id)"
    - Add status toggle button with onclick: "formasPago.statusFormaPago(id, active)"
    - Return array with 'row' key for table rendering
    - _Requirements: 1.2, 1.3, 1.4_

  - [x] 3.3 Implement getFormaPago() method

    - Receive id from $_POST
    - Call model's getFormaPagoById()
    - Return status 200 with data if found, 500 if error
    - _Requirements: 3.2_


  - [ ] 3.4 Implement addFormaPago() method
    - Validate name is not empty
    - Check for duplicates using existsFormaPagoByName()
    - Set date_creation to current timestamp
    - Set active to 1 by default
    - Call model's createFormaPago() with sanitized data

    - Return status 200 on success, 409 if duplicate, 500 on error
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 3.5 Implement editFormaPago() method



    - Receive id and name from $_POST
    - Call model's updateFormaPago() with sanitized data
    - Return status 200 on success, 500 on error
    - _Requirements: 3.3, 3.4, 3.5_

  - [x] 3.6 Implement statusFormaPago() method

    - Receive id and active status from $_POST
    - Toggle active value (1 to 0 or 0 to 1)
    - Call model's updateFormaPago()
    - Return status 200 on success, 500 on error

    - _Requirements: 4.4, 4.5, 4.6_

  - [ ] 3.7 Add controller instantiation and routing
    - Create $obj = new ctrl()
    - Call dynamic method based on $_POST['opc']

    - Echo JSON encoded response
    - _Requirements: 1.1_

- [ ] 4. Implement frontend JavaScript (formasPago.js)
  - [ ] 4.1 Create base class structure
    - Define api variable pointing to ctrl-formasPago.php
    - Create FormasPago class extending Templates
    - Set PROJECT_NAME to "formasPago"
    - Initialize class on document ready
    - _Requirements: 1.1_


  - [ ] 4.2 Implement render() method
    - Call layout() method
    - Call lsFormasPago() method
    - _Requirements: 1.1_

  - [ ] 4.3 Implement layout() method
    - Use primaryLayout() to create main structure

    - Define parent as "container-formasPago" (from tab)
    - Create filterBar and container sections
    - _Requirements: 1.1_

  - [ ] 4.4 Implement lsFormasPago() method
    - Use createTable() component
    - Set parent to container section
    - Configure data with opc: "lsFormasPago"
    - Enable coffeesoft mode
    - Configure DataTables with 15 rows per page

    - Set theme to 'corporativo'
    - Set title to "📋 Formas de Pago"
    - Set subtitle to "Gestiona los métodos de pago disponibles"
    - _Requirements: 1.2, 1.3_

  - [ ] 4.5 Implement addFormaPago() method
    - Use createModalForm() component
    - Set bootbox title to "Nueva forma de pago"
    - Configure data with opc: "addFormaPago"

    - Use jsonFormaPago() for form structure
    - Handle success response to refresh table
    - Display success/error alerts
    - _Requirements: 2.1, 2.2, 2.5, 2.6_




  - [ ] 4.6 Implement editFormaPago(id) method
    - Make async call to getFormaPago with id
    - Use createModalForm() component
    - Set bootbox title to "Editar forma de pago"
    - Configure data with opc: "editFormaPago" and id

    - Use autofill with retrieved data
    - Use jsonFormaPago() for form structure
    - Handle success response to refresh table
    - Display success/error alerts




    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

  - [ ] 4.7 Implement statusFormaPago(id, active) method
    - Use swalQuestion() component
    - Set dynamic title based on active status

    - Set message for deactivate: "La forma de pago ya no estará disponible para capturar o filtrar las compras de todas las unidades de negocio"
    - Set message for activate: "La forma de pago estará disponible para capturar y filtrar las compras en todas las unidades de negocio"
    - Configure data with opc: "statusFormaPago", id, and toggled active value
    - Handle success response to refresh table
    - Display confirmation message
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_


  - [ ] 4.8 Implement jsonFormaPago() method
    - Return array with form field definitions
    - Add input field for name (required)
    - Add btn-submit with text "Guardar"
    - _Requirements: 2.2, 3.2_


- [ ] 5. Integrate with tab navigation
  - [ ] 5.1 Add "Formas de pago" tab to main admin interface
    - Use tabLayout() component
    - Set tab id to "formasPago"
    - Set tab label to "💳 Formas de pago"
    - Set onClick to initialize FormasPago module
    - _Requirements: 1.1_

  - [ ] 5.2 Add "Agregar nuevo método de pago" button
    - Position button in filterBar or header section
    - Set onclick to "formasPago.addFormaPago()"
    - Style with CoffeeSoft button classes
    - _Requirements: 1.4_

- [ ] 6. Wire everything together
  - [ ] 6.1 Verify all file paths are correct
    - Check ctrl path: contabilidad/administrador/ctrl/ctrl-formasPago.php
    - Check mdl path: contabilidad/administrador/mdl/mdl-formasPago.php
    - Check js path: contabilidad/administrador/formasPago.js
    - _Requirements: 1.1_

  - [ ] 6.2 Test complete CRUD flow
    - Test adding new payment method
    - Test editing existing payment method
    - Test activating payment method
    - Test deactivating payment method
    - Test duplicate name validation
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [ ] 6.3 Verify table updates without page reload
    - Confirm table refreshes after add
    - Confirm table refreshes after edit
    - Confirm table refreshes after status change
    - _Requirements: 2.5, 3.4, 4.5_

  - [ ] 6.4 Verify error handling and user feedback
    - Test duplicate name error message
    - Test success messages for all operations
    - Test confirmation dialogs for status changes
    - _Requirements: 2.3, 2.4, 4.2, 4.3, 4.6_
