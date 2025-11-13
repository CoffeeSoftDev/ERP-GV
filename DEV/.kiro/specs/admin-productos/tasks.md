# Implementation Plan

- [x] 1. Set up project structure and database foundation


  - Create directory structure in kpi/marketing/ for admin-productos module
  - Verify database tables producto and udn exist with correct schema
  - Ensure foreign key relationship between producto.udn_id and udn.idUDN
  - _Requirements: 1.1, 2.1_



- [ ] 2. Implement data access layer (Model)
  - [ ] 2.1 Create mdl-admin-productos.php with base structure
    - Extend CRUD class and configure database connection ($bd = "rfwsmqex_pedidos.")
    - Initialize Utileria instance for SQL sanitization

    - _Requirements: 2.1, 2.2_

  - [ ] 2.2 Implement product listing method
    - Write listProductos() method with LEFT JOIN to udn table
    - Support filtering by active status and udn_id

    - Order results by id DESC
    - _Requirements: 2.1, 2.2, 2.3, 3.2_

  - [x] 2.3 Implement product retrieval method

    - Write getProductoById() method to fetch single product
    - Return all fields for editing
    - _Requirements: 4.2, 5.2_


  - [ ] 2.4 Implement product creation method
    - Write createProducto() method using _Insert
    - Handle all product fields including date_creation
    - _Requirements: 4.5_


  - [ ] 2.5 Implement product update method
    - Write updateProducto() method using _Update
    - Support partial updates via util->sql()

    - _Requirements: 5.5_

  - [x] 2.6 Implement validation methods

    - Write existsProductoByName() to check for duplicate names
    - Use case-insensitive comparison with LOWER()

    - _Requirements: 4.6_

  - [ ] 2.7 Implement UDN listing method
    - Write lsUDN() to fetch active business units

    - Return id and valor format for select dropdowns
    - _Requirements: 3.4_

- [x] 3. Implement business logic layer (Controller)

  - [ ] 3.1 Create ctrl-admin-productos.php with base structure
    - Extend mdl class and validate $_POST['opc']
    - Implement dynamic method calling pattern
    - _Requirements: 1.1_

  - [x] 3.2 Implement initialization endpoint

    - Write init() method to return UDN list and status options
    - Format data for frontend dropdowns
    - _Requirements: 3.4, 3.5_

  - [x] 3.3 Implement product listing endpoint

    - Write lsProductos() method to format table rows
    - Transform es_servicio boolean to "Sí"/"No" text
    - Add action buttons (Editar, Eliminar) to each row
    - Return row array and raw data
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_


  - [ ] 3.4 Implement product retrieval endpoint
    - Write getProducto() method to fetch single product by id
    - Return status 200 on success, 404 on not found
    - Include descriptive message

    - _Requirements: 5.2_

  - [x] 3.5 Implement product creation endpoint


    - Write addProducto() method with duplicate name validation
    - Set date_creation to current timestamp
    - Return status 200 on success, 409 on duplicate, 500 on error

    - _Requirements: 4.5, 4.6, 4.7, 4.8_

  - [ ] 3.6 Implement product update endpoint
    - Write editProducto() method to update existing product
    - Use util->sql() for sanitization
    - Return status 200 on success, 500 on error

    - _Requirements: 5.5, 5.6, 5.7_

  - [ ] 3.7 Implement product status toggle endpoint
    - Write statusProducto() method to toggle active field

    - Support both activation and deactivation
    - Return status 200 on success, 500 on error
    - _Requirements: 6.3, 6.4, 6.5, 6.6_

- [x] 4. Implement presentation layer (Frontend)

  - [ ] 4.1 Create admin-productos.js with base structure
    - Define api variable pointing to ctrl-admin-productos.php
    - Create App class extending Templates
    - Set PROJECT_NAME to "AdminProductos"
    - Initialize app instance in document ready
    - _Requirements: 1.1, 8.2_


  - [ ] 4.2 Implement main render method
    - Write render() method to orchestrate initialization
    - Call layout(), filterBar(), and lsProductos() in sequence
    - _Requirements: 1.2_

  - [ ] 4.3 Implement layout structure
    - Write layout() method using primaryLayout component

    - Configure parent as "container-productos" (from tab)
    - Create filterBarAdminProductos and containerAdminProductos sections
    - _Requirements: 1.3, 8.3_

  - [ ] 4.4 Implement filter bar
    - Write filterBar() method using createfilterBar component
    - Add UDN dropdown populated from init() call
    - Add Status dropdown with Disponibles/No disponibles options

    - Add "Nuevo Producto" button triggering addProducto()
    - Configure onchange events to refresh table
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

  - [ ] 4.5 Implement product listing
    - Write lsProductos() method using createTable component
    - Configure data fetch with opc: "lsProductos"
    - Set coffeesoft: true and theme: 'corporativo'

    - Configure pagination with 15 rows per page
    - Define column alignment (center for status, right for actions)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [ ] 4.6 Implement product creation form
    - Write addProducto() method using createModalForm component

    - Configure modal title "Agregar Producto"
    - Call jsonProducto() for form fields
    - Set data with opc: "addProducto"
    - Implement success callback to show alert and refresh table
    - Implement error callback to show error message
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.7, 4.8_

  - [ ] 4.7 Implement product editing form
    - Write async editProducto(id) method

    - Fetch product data via useFetch with opc: "getProducto"
    - Open createModalForm with autofill data
    - Configure modal title "Editar Producto"
    - Set data with opc: "editProducto" and id
    - Implement success callback to show alert and refresh table
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.6, 5.7_



  - [ ] 4.8 Implement product deactivation
    - Write statusProducto(id, active) method using swalQuestion
    - Configure confirmation dialog with product name
    - Set data with opc: "statusProducto", id, and toggled active value

    - Implement success callback to show alert and refresh table
    - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6_

  - [ ] 4.9 Implement form field configuration
    - Write jsonProducto() method returning field array
    - Define input for nombre (required, text)
    - Define textarea for descripcion (optional)

    - Define checkbox for es_servicio (default unchecked)
    - Define select for udn_id (required, populated from init)
    - Define checkbox for active (default checked)
    - Define submit button "Guardar Producto"
    - _Requirements: 4.3, 4.4, 5.4_

  - [x] 4.10 Implement real-time feedback

    - Configure success alerts with green theme and auto-dismiss (3s)
    - Configure error alerts with red theme and manual dismiss
    - Ensure table refresh after all CRUD operations
    - Maintain filter state after refresh
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

- [ ] 5. Integrate module into parent system
  - Add "Administrador de Productos" tab to pedidos module tabLayout
  - Configure tab with id: "productos" and onClick: () => adminProductos.render()
  - Ensure container-productos is created by tab system
  - Verify module loads within 2 seconds on tab click
  - _Requirements: 1.1, 1.2, 1.4_

- [ ] 6. Implement styling and responsive design
  - Apply TailwindCSS classes following CoffeeSoft corporativo theme
  - Use color palette: #103B60 (primary), #8CC63F (success), #EAEAEA (neutral)
  - Ensure table is responsive on desktop and tablet
  - Verify modal forms are centered and properly sized
  - Test filter bar layout on different screen sizes
  - _Requirements: 8.1, 8.4, 8.5, 8.6_

- [ ] 7. Implement error handling and validation
  - Add frontend validation for required fields (nombre, udn_id, active)
  - Display inline error messages for invalid inputs
  - Implement backend validation for duplicate names
  - Handle database errors gracefully with user-friendly messages
  - Log errors to error_log without exposing SQL details
  - _Requirements: 4.6, 4.8, 5.7, 6.6, 7.1, 7.2_

- [ ] 8. Verify data integrity and business rules
  - Confirm soft delete implementation (active flag toggle)
  - Verify foreign key constraint between producto and udn
  - Test unique product name validation per UDN
  - Ensure date_creation is set automatically on insert
  - Verify UDN name displays instead of ID in table
  - _Requirements: 2.2, 6.3, 6.4, 6.7_

- [ ]* 9. Performance optimization
  - Verify table loads within 1 second with 100+ products
  - Ensure modal opens within 500 milliseconds
  - Test AJAX response times average under 1 second
  - Implement efficient SQL queries with proper indexing
  - _Requirements: 1.2, 4.2, 7.1_

- [ ]* 10. End-to-end testing
  - Test complete create product flow (open modal → fill → submit → verify)
  - Test edit product flow (click edit → modify → submit → verify)
  - Test delete product flow (click delete → confirm → verify status change)
  - Test filter combinations (UDN + Status)
  - Verify all success/error messages display correctly
  - Test with multiple concurrent users
  - _Requirements: All requirements_
