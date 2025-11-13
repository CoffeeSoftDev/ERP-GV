# Implementation Plan - Módulo de Almacén

## Task List

- [x] 1. Set up project structure and database schema


  - Create directory structure for almacen module
  - Create database tables: warehouse_output, product, product_class, file
  - Add foreign key constraints and indexes
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1_





- [ ] 2. Implement backend model layer (mdl-almacen.php)
- [ ] 2.1 Create base model structure
  - Extend CRUD class

  - Configure database connection
  - Set up utility methods
  - _Requirements: 1.1, 2.1_

- [ ] 2.2 Implement warehouse output CRUD methods
  - Code listWarehouseOutput() with date filters
  - Code createWarehouseOutput() with validation

  - Code updateWarehouseOutput() with audit logging
  - Code getWarehouseOutputById() for single record retrieval
  - Code deleteWarehouseOutputById() with soft delete
  - _Requirements: 1.4, 2.1, 2.3, 3.1_


- [ ] 2.3 Implement filter and lookup methods
  - Code listProducts() for product dropdown
  - Code listProductClass() for classification filter
  - Code listBusinessUnits() for UDN filter
  - _Requirements: 1.3, 2.1, 4.2_


- [ ] 2.4 Implement report and balance methods
  - Code listWarehouseReport() with date range and UDN filters
  - Code getBalance() to calculate inputs vs outputs
  - Code getDailyTotal() for sum of outputs




  - _Requirements: 1.2, 5.1, 5.3_

- [ ] 2.5 Implement audit and file methods
  - Code logAudit() for tracking changes

  - Code createFile() for file upload records
  - Code listFiles() for file retrieval
  - Code deleteFileById() for file removal
  - _Requirements: 3.2, 1.6_

- [ ] 3. Implement backend controller layer (ctrl-almacen.php)
- [x] 3.1 Create base controller structure

  - Extend mdl class
  - Implement init() method for filters
  - Set up error handling
  - _Requirements: 1.1, 4.1_

- [x] 3.2 Implement warehouse output operations

  - Code ls() to list outputs with formatting
  - Code addWarehouseOutput() with validation
  - Code editWarehouseOutput() with permission check
  - Code getWarehouseOutput() for edit form
  - Code deleteWarehouseOutput() with confirmation

  - _Requirements: 1.4, 2.1, 2.3, 2.4, 3.1_

- [ ] 3.3 Implement report operations
  - Code lsReport() to generate consolidated report
  - Code exportReport() for Excel generation
  - Format currency and date values

  - Calculate totals and balances
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [x] 3.4 Implement file operations



  - Code uploadFile() with size validation (max 20MB)
  - Code listFiles() with filters
  - Code deleteFile() with permission check
  - _Requirements: 1.6_


- [ ] 3.5 Implement access level logic
  - Code getAccessLevel() to determine user permissions
  - Add permission checks to all operations
  - Return appropriate error messages for unauthorized access
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_


- [ ] 3.6 Create helper functions
  - Code dropdown() for action buttons
  - Code renderStatus() for status badges
  - Code formatSpanishDate() for date formatting
  - Code evaluar() for currency formatting

  - _Requirements: 1.4, 2.5_

- [ ] 4. Implement frontend main module (almacen.js)
- [ ] 4.1 Create App class structure
  - Extend Templates class
  - Set PROJECT_NAME to "almacen"

  - Implement constructor with api link
  - _Requirements: 1.1_

- [ ] 4.2 Implement layout methods
  - Code render() to initialize module
  - Code layout() with primaryLayout and tabLayout
  - Create tabs: "Salidas", "Concentrado", "Archivos"

  - _Requirements: 1.1, 1.3_

- [ ] 4.3 Implement filter bar
  - Code filterBar() with date picker
  - Add business unit selector

  - Add product filter
  - Integrate dataPicker component
  - _Requirements: 1.5, 5.2_

- [ ] 4.4 Implement warehouse output list
  - Code ls() using createTable component

  - Configure table with columns: Almacén, Monto, Descripción, Acciones
  - Add edit and delete buttons
  - Display daily total above table
  - _Requirements: 1.2, 1.4, 2.3_



- [ ] 4.5 Implement add warehouse output
  - Code addWarehouseOutput() using createModalForm
  - Create jsonWarehouseOutput() with form fields
  - Add product selector (required)

  - Add amount input with numeric validation (required)
  - Add description textarea (required)
  - _Requirements: 1.3, 2.1, 2.2_

- [x] 4.6 Implement edit warehouse output

  - Code editWarehouseOutput(id) with async data fetch
  - Use createModalForm with autofill
  - Reuse jsonWarehouseOutput() for form structure
  - _Requirements: 2.4_

- [x] 4.7 Implement delete warehouse output

  - Code deleteWarehouseOutput(id) using swalQuestion
  - Display confirmation modal
  - Show success/error message after deletion
  - Refresh table after successful deletion
  - _Requirements: 3.1, 3.2, 3.4_


- [ ] 4.8 Implement description modal
  - Code showDescription(id) to display description
  - Use bootbox or custom modal


  - Format description text

  - _Requirements: 3.3_

- [ ] 5. Implement warehouse report module
- [x] 5.1 Create WarehouseReport class

  - Extend App class
  - Set up report-specific properties
  - _Requirements: 5.1_

- [ ] 5.2 Implement report layout
  - Code renderReport() to display report interface

  - Code filterBarReport() with date range and UDN filters
  - Add "Export to Excel" button
  - _Requirements: 5.2, 5.4_

- [x] 5.3 Implement report table

  - Code lsReport() using createTable
  - Configure columns: Almacén, Entradas (green), Salidas (red), Saldo
  - Add expandable rows for detail view
  - Display totals row at bottom
  - _Requirements: 5.1, 5.3, 5.5_

- [ ] 5.4 Implement Excel export
  - Code exportToExcel() to trigger backend export
  - Handle file download
  - Show loading indicator during export
  - _Requirements: 5.4_

- [ ] 5.5 Implement toggle details
  - Code toggleDetails(warehouseId) to expand/collapse rows
  - Fetch detailed movements on expand
  - Display movement history in nested table
  - _Requirements: 5.5_

- [ ] 6. Implement file upload module
- [ ] 6.1 Create FileUpload class
  - Extend App class
  - Set up file upload properties
  - _Requirements: 1.6_

- [ ] 6.2 Implement file upload interface
  - Code uploadFile() with drag-and-drop support
  - Add file size validation (max 20MB)
  - Add file type validation
  - Display upload progress
  - _Requirements: 1.6_

- [ ] 6.3 Implement file list
  - Code listFiles() to display uploaded files
  - Show file name, size, upload date
  - Add delete button for each file
  - _Requirements: 1.6_

- [ ] 6.4 Implement file deletion
  - Code deleteFile(id) with confirmation
  - Remove file from server and database
  - _Requirements: 1.6_

- [ ] 7. Implement access level restrictions
- [ ] 7.1 Configure Level 1 (Captura) access
  - Show "Salidas de almacén" tab only
  - Enable add, edit, delete buttons
  - Restrict to current day data
  - _Requirements: 4.1, 4.6_

- [ ] 7.2 Configure Level 2 (Gerencia) access
  - Show "Concentrado" tab
  - Enable report viewing and Excel export
  - Disable edit/delete operations
  - _Requirements: 4.2, 4.6_

- [ ] 7.3 Configure Level 3 (Contabilidad) access
  - Show all tabs in read-only mode
  - Enable UDN filter
  - Disable all modification operations
  - _Requirements: 4.3, 4.6_

- [ ] 7.4 Configure Level 4 (Administración) access
  - Show all tabs with full access
  - Enable product class management
  - Enable module lock/unlock
  - _Requirements: 4.4, 4.6_

- [ ] 8. Implement UI components and styling
- [ ] 8.1 Style warehouse output table
  - Apply CoffeeSoft theme (corporativo)
  - Add hover effects on rows
  - Style action buttons
  - _Requirements: 1.4_

- [ ] 8.2 Style report table
  - Color-code Entradas (green background)
  - Color-code Salidas (red/orange background)
  - Style totals row with bold text
  - Add expand/collapse icons
  - _Requirements: 5.1_

- [ ] 8.3 Style modals
  - Apply consistent modal styling
  - Add form field icons
  - Style submit buttons
  - _Requirements: 2.1, 3.1_

- [ ] 8.4 Add responsive design
  - Ensure mobile compatibility
  - Adjust table columns for small screens
  - Stack form fields on mobile
  - _Requirements: 1.1_

- [ ] 9. Integration and testing
- [ ] 9.1 Test warehouse output operations
  - Test add operation with valid data
  - Test edit operation with existing record
  - Test delete operation with confirmation
  - Test form validation for required fields
  - _Requirements: 2.1, 2.3, 2.4, 3.1_

- [ ] 9.2 Test report generation
  - Test report with various date ranges
  - Test report with different UDN filters
  - Test balance calculations
  - Test Excel export functionality
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 9.3 Test access level restrictions
  - Test Level 1 user permissions
  - Test Level 2 user permissions
  - Test Level 3 user permissions
  - Test Level 4 user permissions
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 9.4 Test file upload
  - Test file upload with valid file (< 20MB)
  - Test file upload with oversized file (> 20MB)
  - Test file deletion
  - _Requirements: 1.6_

- [ ] 9.5 Test audit logging
  - Verify audit log entries for create operations
  - Verify audit log entries for update operations
  - Verify audit log entries for delete operations
  - _Requirements: 3.2_

- [ ] 10. Documentation and deployment
- [ ] 10.1 Create user documentation
  - Document Level 1 user workflows
  - Document Level 2 user workflows
  - Document Level 3 user workflows
  - Document Level 4 user workflows
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 10.2 Create technical documentation
  - Document API endpoints
  - Document database schema
  - Document component structure
  - _Requirements: All_

- [ ] 10.3 Prepare deployment package
  - Bundle all files
  - Create deployment checklist
  - Prepare database migration scripts
  - _Requirements: All_
