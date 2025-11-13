# Implementation Plan - Módulo de Salidas de Almacén

- [x] 1. Set up database structure


  - Create warehouse_output table with fields: id, insumo_id, amount, description, operation_date, active
  - Add foreign key constraint to insumo table
  - Create indexes on insumo_id, active, and operation_date fields
  - _Requirements: 6.1, 6.2, 7.1, 7.2_




- [ ] 2. Implement Model Layer (mdl-salidas-almacen.php)
- [ ] 2.1 Create base model structure
  - Extend CRUD class
  - Initialize $bd and $util properties

  - Configure database connection
  - _Requirements: 7.3, 7.4_

- [x] 2.2 Implement warehouse list query

  - Create lsWarehouses() method to fetch active warehouses from insumo table
  - Return array with id and name fields
  - _Requirements: 1.1, 2.2_

- [x] 2.3 Implement warehouse outputs list query

  - Create listWarehouseOutputs() method with JOIN to insumo table
  - Filter by active status
  - Return warehouse name, amount, description, operation_date
  - _Requirements: 1.3, 6.3_


- [ ] 2.4 Implement total calculation query
  - Create calculateTotalOutputs() method
  - Sum amount field where active = 1
  - Return formatted decimal value
  - _Requirements: 5.1, 5.5, 6.4_


- [ ] 2.5 Implement create operation
  - Create createWarehouseOutput() method using _Insert
  - Validate required fields: insumo_id, amount
  - Set operation_date to current timestamp

  - Set active to 1 by default
  - _Requirements: 2.6, 7.4_

- [ ] 2.6 Implement read operation
  - Create getWarehouseOutputById() method using _Select

  - Return single record with warehouse details
  - _Requirements: 3.2_




- [ ] 2.7 Implement update operation
  - Create updateWarehouseOutput() method using _Update
  - Allow modification of insumo_id, amount, description
  - Preserve operation_date
  - _Requirements: 3.4_


- [ ] 2.8 Implement delete operation
  - Create deleteWarehouseOutputById() method using _Update
  - Set active = 0 instead of physical deletion

  - _Requirements: 4.5, 6.2_

- [ ] 3. Implement Controller Layer (ctrl-salidas-almacen.php)
- [ ] 3.1 Create base controller structure
  - Extend mdl class
  - Require model file

  - Set up session validation
  - _Requirements: 7.3_

- [ ] 3.2 Implement init method
  - Call lsWarehouses() from model

  - Return warehouse list for select dropdown
  - _Requirements: 2.2_

- [ ] 3.3 Implement list method
  - Call listWarehouseOutputs() from model
  - Format data for table display

  - Build action buttons array (edit, delete)
  - Return row array with formatted amounts using evaluar()
  - _Requirements: 1.3, 1.5_

- [x] 3.4 Implement get method

  - Receive id from $_POST
  - Call getWarehouseOutputById() from model
  - Return status 200 with data or 500 on error
  - _Requirements: 3.2_


- [ ] 3.5 Implement add method
  - Validate required fields from $_POST
  - Set operation_date to current datetime
  - Call createWarehouseOutput() from model



  - Return status 200 on success or 500 on error
  - _Requirements: 2.5, 2.6, 2.7_

- [ ] 3.6 Implement edit method
  - Receive id and updated fields from $_POST

  - Call updateWarehouseOutput() from model
  - Return status 200 on success or 500 on error
  - _Requirements: 3.4, 3.5, 3.6_

- [x] 3.7 Implement delete method

  - Receive id from $_POST
  - Call deleteWarehouseOutputById() from model
  - Return status 200 on success or 500 on error
  - _Requirements: 4.5, 4.6, 4.7_




- [ ] 3.8 Implement total calculation method
  - Call calculateTotalOutputs() from model
  - Format result with currency formatting

  - Return total value
  - _Requirements: 5.1, 5.6_

- [ ] 4. Implement Frontend Base Structure (salidas-almacen.js)
- [x] 4.1 Create App class

  - Extend Templates class from CoffeeSoft
  - Set PROJECT_NAME to "warehouseOutputs"
  - Initialize api variable pointing to ctrl-salidas-almacen.php
  - _Requirements: 1.1_




- [ ] 4.2 Implement render method
  - Call layout() method
  - Call filterBar() method if needed

  - Initialize DashboardWarehouse and AdminWarehouse instances
  - _Requirements: 1.1_

- [ ] 4.3 Implement layout method
  - Use primaryLayout() from CoffeeSoft
  - Create filterBar and container sections

  - Set up tab navigation structure
  - _Requirements: 1.1, 1.4_

- [x] 5. Implement Dashboard Component



- [ ] 5.1 Create DashboardWarehouse class
  - Extend App class
  - Implement renderDashboard() method
  - _Requirements: 1.2, 5.1_


- [ ] 5.2 Implement total display
  - Fetch total from backend using useFetch
  - Display in summary card with currency format
  - Use formatPrice() function for formatting
  - _Requirements: 1.2, 5.2, 5.6_

- [x] 5.3 Implement total update mechanism

  - Create updateTotal() method
  - Call after add/edit/delete operations
  - Refresh display without page reload
  - _Requirements: 5.3, 5.4, 5.5_


- [ ] 6. Implement Table Management Component
- [ ] 6.1 Create AdminWarehouse class
  - Extend App class
  - Implement lsWarehouseOutputs() method
  - _Requirements: 1.3_




- [ ] 6.2 Implement table display
  - Use createTable() from CoffeeSoft
  - Configure columns: Almacén, Monto, Descripción, Acciones
  - Set theme to 'corporativo'
  - Enable DataTables pagination

  - _Requirements: 1.3, 1.5_

- [ ] 6.3 Implement action buttons
  - Add edit icon with onclick to editWarehouseOutput(id)
  - Add delete icon with onclick to deleteWarehouseOutput(id)

  - Format as dropdown or separate buttons
  - _Requirements: 1.5_

- [ ] 7. Implement Add Functionality
- [x] 7.1 Implement addWarehouseOutput method



  - Create modal form using createModalForm()
  - Set modal title to "NUEVA SALIDA DE ALMACÉN"
  - Call jsonWarehouseOutput() for form structure
  - _Requirements: 2.1_


- [ ] 7.2 Implement form JSON structure
  - Create jsonWarehouseOutput() method
  - Add select field for warehouse (insumo_id)
  - Add numeric input for amount with currency format
  - Add textarea for description (optional)

  - Add submit button "Guardar salida de almacén"
  - _Requirements: 2.2, 2.3, 2.4_

- [ ] 7.3 Implement form validation
  - Mark insumo_id and amount as required




  - Validate amount is positive number
  - Prevent submission if validation fails
  - _Requirements: 2.5_

- [x] 7.4 Implement form submission

  - Send data to backend with opc: 'addWarehouseOutput'
  - Handle success response with alert
  - Refresh table on success
  - Update total display
  - Close modal

  - _Requirements: 2.6, 2.7, 2.8_

- [ ] 8. Implement Edit Functionality
- [ ] 8.1 Implement editWarehouseOutput method
  - Fetch existing data using useFetch with opc: 'getWarehouseOutput'

  - Create modal form using createModalForm()
  - Set modal title to "EDITAR SALIDA DE ALMACÉN"
  - _Requirements: 3.1, 3.2_

- [x] 8.2 Implement form pre-population

  - Use autofill parameter with fetched data
  - Pre-select warehouse in dropdown
  - Pre-fill amount and description fields
  - _Requirements: 3.2, 3.3_

- [ ] 8.3 Implement update submission
  - Send updated data with opc: 'editWarehouseOutput'
  - Include id in request
  - Handle success/error responses
  - Refresh table and total on success
  - _Requirements: 3.4, 3.5, 3.6, 3.7_

- [ ] 9. Implement Delete Functionality
- [ ] 9.1 Implement deleteWarehouseOutput method
  - Use swalQuestion() for confirmation modal
  - Set title to "ELIMINAR SALIDA DE ALMACÉN"
  - Display confirmation message
  - _Requirements: 4.1, 4.2, 4.3_

- [ ] 9.2 Implement delete confirmation
  - Add "Continuar" and "Cancelar" buttons
  - Cancel closes modal without action
  - Continue sends delete request
  - _Requirements: 4.4, 4.5_

- [ ] 9.3 Implement delete execution
  - Send request with opc: 'deleteWarehouseOutput'
  - Handle success/error responses
  - Refresh table and total on success
  - Display appropriate messages
  - _Requirements: 4.6, 4.7, 4.8, 4.9_

- [ ] 10. Integration and Polish
- [ ] 10.1 Integrate with main navigation
  - Add "Salidas de almacén" tab to contabilidad module
  - Ensure proper routing to salidas-almacen.js
  - Test navigation flow
  - _Requirements: 1.1_

- [ ] 10.2 Implement error handling
  - Add try-catch blocks in critical sections
  - Display user-friendly error messages
  - Log errors for debugging
  - _Requirements: 7.5_

- [ ] 10.3 Add loading states
  - Show loading indicator during AJAX requests
  - Disable buttons during operations
  - Prevent duplicate submissions
  - _Requirements: 2.7, 3.6, 4.7_

- [ ] 10.4 Implement responsive design
  - Test on mobile devices
  - Ensure table is scrollable on small screens
  - Verify modal forms work on mobile
  - _Requirements: 1.1_

- [ ] 10.5 Add data formatting
  - Format amounts with currency symbol
  - Format dates in Spanish locale
  - Ensure consistent number formatting
  - _Requirements: 1.3, 5.6_
