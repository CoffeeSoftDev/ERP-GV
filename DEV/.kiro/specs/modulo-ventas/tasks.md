# Implementation Plan - Módulo de Ventas

- [ ] 1. Setup project structure and database
- [x] 1.1 Create directory structure for ventas module


  - Create folders: ctrl/, mdl/, js/ inside contabilidad/captura/
  - Create index.php with root div and CoffeeSoft includes
  - _Requirements: 1.1, 1.2, 1.3_



- [ ] 1.2 Create database tables for sales module
  - Execute SQL scripts to create all 10 tables (daily_closure, detail_sale_category, detail_discount_courtesy, detail_cash_concept, detail_bank_account, detail_credit_customer, sale_category, discount_courtesy, cash_concept, bank_account, customer)


  - Add foreign key constraints and indexes
  - _Requirements: 2.1, 3.1, 7.1, 8.1, 9.1, 10.1_

- [x] 1.3 Create base model file mdl-ventas.php



  - Extend CRUD class
  - Configure database connection with $this->bd = "rfwsmqex_contabilidad."
  - Include _CRUD.php and _Utileria.php
  - _Requirements: All_



- [ ] 1.4 Create base model file mdl-admin.php
  - Extend CRUD class
  - Configure database connection
  - Include required dependencies
  - _Requirements: 7.1, 8.1, 9.1, 10.1_


- [ ] 2. Implement admin module for configuration entities
- [ ] 2.1 Create ctrl-admin.php controller
  - Implement init() method to load UDN filters
  - Implement lsSaleCategories() to list categories with status
  - Implement addSaleCategory() with validation

  - Implement editSaleCategory() with update logic
  - Implement statusSaleCategory() to toggle active status
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ] 2.2 Implement sale category methods in mdl-admin.php
  - Create listSaleCategories($array) using _Select

  - Create getSaleCategoryById($id) for single record
  - Create createSaleCategory($array) using _Insert
  - Create updateSaleCategory($array) using _Update
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 2.3 Implement discount/courtesy management in ctrl-admin.php

  - Implement lsDiscounts() to list discounts with tax configuration
  - Implement addDiscount() with tax fields validation
  - Implement editDiscount() with update logic
  - Implement statusDiscount() to toggle active status
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_


- [ ] 2.4 Implement discount/courtesy methods in mdl-admin.php
  - Create listDiscounts($array) using _Select
  - Create getDiscountById($id) for single record
  - Create createDiscount($array) using _Insert
  - Create updateDiscount($array) using _Update
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_


- [ ] 2.5 Implement cash concept management in ctrl-admin.php
  - Implement lsCashConcepts() to list concepts by UDN
  - Implement addCashConcept() with operation_type validation
  - Implement editCashConcept() with update logic
  - Implement statusCashConcept() to toggle active status

  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ] 2.6 Implement cash concept methods in mdl-admin.php
  - Create listCashConcepts($array) using _Select with UDN filter
  - Create getCashConceptById($id) for single record
  - Create createCashConcept($array) using _Insert
  - Create updateCashConcept($array) using _Update
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_



- [ ] 2.7 Implement customer management in ctrl-admin.php
  - Implement lsCustomers() to list customers with balance

  - Implement addCustomer() with initial balance
  - Implement editCustomer() with update logic
  - Implement updateCustomerBalance() for credit operations
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6_

- [ ] 2.8 Implement customer methods in mdl-admin.php
  - Create listCustomers($array) using _Select with balance
  - Create getCustomerById($id) for single record
  - Create createCustomer($array) using _Insert

  - Create updateCustomer($array) using _Update
  - Create updateCustomerBalance($array) for balance updates
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5, 10.6_

- [ ] 3. Implement daily sales capture functionality
- [ ] 3.1 Create ctrl-ventas.php controller with init method
  - Implement init() to load UDN list, sale categories, discounts, and date filters

  - Return data structure for frontend initialization
  - _Requirements: 1.1, 2.1_

- [ ] 3.2 Implement sale capture methods in ctrl-ventas.php
  - Implement saveDailySale() to receive sale data from frontend
  - Validate required fields (operation_date, udn_id)
  - Calculate totals: total_sale_without_tax, subtotal, tax, total_sale

  - Call model methods to insert daily_closure record
  - Call model methods to insert detail_sale_category records
  - Call model methods to insert detail_discount_courtesy records
  - Return success/error response with closure_id
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ] 3.3 Implement sale data methods in mdl-ventas.php
  - Create createDailySale($array) using _Insert for daily_closure table
  - Create createSaleDetail($array) using _Insert for detail_sale_category
  - Create createDiscountDetail($array) using _Insert for detail_discount_courtesy
  - Create getDailySaleById($id) using _Select to retrieve closure data
  - Create getSaleDetails($closureId) to get all category details

  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ] 3.4 Implement Soft-Restaurant integration in ctrl-ventas.php
  - Create loadSoftRestaurant() method to import daily report
  - Parse Soft-Restaurant data format
  - Map categories and amounts to system structure
  - Return formatted data for frontend

  - _Requirements: 2.6_

- [ ] 4. Implement payment forms functionality
- [ ] 4.1 Implement payment forms methods in ctrl-ventas.php
  - Implement savePaymentForms() to receive payment data
  - Validate closure exists and is not locked

  - Calculate total_received: (cash + foreign_currency + banks + credits) - credit_payments
  - Calculate difference: total_sale - total_received
  - Update daily_closure with payment totals
  - Call model methods to insert detail_cash_concept records
  - Call model methods to insert detail_bank_account records
  - Call model methods to insert detail_credit_customer records
  - Update customer balances for credit operations
  - Return success/error response with totals

  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

- [ ] 4.2 Implement payment data methods in mdl-ventas.php
  - Create updateDailySale($array) using _Update for daily_closure

  - Create createCashDetail($array) using _Insert for detail_cash_concept
  - Create createBankDetail($array) using _Insert for detail_bank_account
  - Create createCreditDetail($array) using _Insert for detail_credit_customer
  - Create getPaymentDetails($closureId) to retrieve all payment details
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_


- [ ] 4.3 Implement currency conversion in ctrl-ventas.php
  - Create convertCurrency() method to convert foreign currency to MXN
  - Support USD, EUR conversion rates
  - Return converted amount
  - _Requirements: 3.5_


- [ ] 5. Implement daily closure validation and locking
- [ ] 5.1 Implement closure validation in ctrl-ventas.php
  - Create closeDailyOperation() method
  - Validate total_sale matches total_received (within threshold)
  - Check all required fields are completed
  - Lock closure by setting status = 'closed'
  - Prevent further edits to locked closures
  - Return validation result
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_


- [ ] 5.2 Implement closure locking in mdl-ventas.php
  - Create lockClosure($id) using _Update to set status = 'closed'
  - Create validateClosureStatus($id) to check if closure is locked
  - _Requirements: 6.4, 6.5_

- [x] 5.3 Implement closure listing in ctrl-ventas.php

  - Create lsDailySales() to list closures by date range and UDN
  - Format data for table display with totals and status
  - Include difference highlighting (red if != 0)
  - Return row data for createTable component
  - _Requirements: 6.1_


- [ ] 5.4 Implement closure query methods in mdl-ventas.php
  - Create listDailySales($array) using _Select with date range filter
  - Include JOIN with employee and UDN tables
  - Order by operation_date DESC
  - _Requirements: 6.1_


- [ ] 6. Implement file upload functionality
- [ ] 6.1 Implement file upload in ctrl-ventas.php
  - Create uploadFiles() method to handle multiple file uploads
  - Validate file size (max 20MB per file)
  - Validate file extensions (.pdf, .xml, .jpg, .png)
  - Generate unique filename with timestamp
  - Move files to upload directory
  - Store file metadata in database (file_name, file_path, closure_id)
  - Return success/error response with file list
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 6.2 Implement file deletion in ctrl-ventas.php


  - Create deleteFile() method to remove uploaded file
  - Validate file belongs to closure
  - Delete file from filesystem
  - Delete file record from database
  - Return success/error response
  - _Requirements: 4.5_

- [x] 6.3 Create file storage table in database


  - Create closure_files table with fields: id, closure_id, file_name, file_path, file_type, file_size, uploaded_at
  - Add foreign key to daily_closure
  - _Requirements: 4.3, 4.4_

- [ ] 7. Implement turn control for Quinta Tabachines
- [ ] 7.1 Implement turn-specific methods in ctrl-ventas.php
  - Create getTurnSummary() to query sales by turn
  - Filter by date range and turn (matutino, vespertino, nocturno)
  - Calculate totals per turn
  - Return summary data for display
  - _Requirements: 5.1, 5.2, 5.3_

- [ ] 7.2 Implement turn query methods in mdl-ventas.php
  - Create getTurnSummary($array) using _Select with turn filter
  - Group by turn and operation_date
  - Calculate SUM of totals per turn
  - _Requirements: 5.2_

- [ ]* 7.3 Implement turn report export in ctrl-ventas.php
  - Create exportTurnReport() to generate Excel/PDF
  - Format turn summary data
  - Include turn details (jefe de turno, suites rentadas)
  - Return file download response
  - _Requirements: 5.3_

- [ ] 8. Implement frontend JavaScript module
- [x] 8.1 Create ventas.js with App class structure



  - Extend Templates class from CoffeeSoft
  - Define PROJECT_NAME = "ventas"
  - Implement constructor with api link and root div
  - Implement render() method to initialize module
  - Implement layout() method using primaryLayout
  - Implement layoutTabs() with tab configuration for Ventas, Clientes, Compras, etc.
  - Implement headerBar() with welcome message and date selector
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 8.2 Create SaleCapture class in ventas.js
  - Extend App class
  - Implement render() to display sale form
  - Implement layout() using form component
  - Implement filterBarSale() with date selector
  - Implement jsonSaleForm() with dynamic groups for categories, discounts, taxes
  - Implement calculateSubtotal() to auto-calculate subtotal
  - Implement calculateTaxes() to auto-calculate total taxes
  - Implement calculateTotal() to auto-calculate total_sale
  - Implement saveDailySale() to send data to backend
  - Implement loadSoftRestaurant() to import data
  - Display totals in colored boxes (green, blue, red)
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ] 8.3 Create PaymentForms class in ventas.js
  - Extend App class
  - Implement render() to display payment forms
  - Implement layout() using form component
  - Implement jsonPaymentForm() with dynamic groups for cash, banks, credits, foreign currency
  - Implement calculateCash() to sum cash concepts
  - Implement calculateBanks() to sum bank deposits
  - Implement calculateCredits() to calculate net credits
  - Implement calculateForeignCurrency() to convert and sum foreign currency
  - Implement calculateTotalReceived() to calculate total received
  - Implement calculateDifference() to show difference
  - Implement savePaymentForms() to send data to backend
  - Display difference in red if != 0
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

- [ ] 8.4 Create AdminModule class in ventas.js
  - Extend App class
  - Implement render() with admin tabs
  - Implement layout() using tabLayout for categories, discounts, concepts, customers
  - Implement lsSaleCategories() using createTable
  - Implement addSaleCategory() using createModalForm
  - Implement editSaleCategory(id) using createModalForm with autofill
  - Implement statusSaleCategory(id) using swalQuestion
  - Implement lsDiscounts() using createTable
  - Implement addDiscount() using createModalForm
  - Implement editDiscount(id) using createModalForm with autofill
  - Implement statusDiscount(id) using swalQuestion
  - Implement lsCashConcepts() using createTable
  - Implement addCashConcept() using createModalForm
  - Implement editCashConcept(id) using createModalForm with autofill
  - Implement statusCashConcept(id) using swalQuestion
  - Implement lsCustomers() using createTable
  - Implement addCustomer() using createModalForm
  - Implement editCustomer(id) using createModalForm with autofill
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 8.5, 9.1, 9.2, 9.3, 9.4, 9.5, 10.1, 10.2, 10.3, 10.4, 10.5_

- [ ]* 8.5 Create TurnControl class in ventas.js
  - Extend App class
  - Implement render() to display turn form
  - Implement layout() with turn-specific fields
  - Implement jsonTurnForm() with turn selector, jefe de turno, suites rentadas
  - Implement lsTurnSummary() using createTable
  - Implement exportTurnReport() to download Excel/PDF
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 8.6 Implement file upload component in ventas.js
  - Create uploadFiles() method using input-file component
  - Validate file size and format on frontend
  - Display upload progress
  - Show list of uploaded files with download/delete buttons
  - Implement deleteFile(id) with confirmation
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

- [ ] 9. Implement validation and error handling
- [ ] 9.1 Add frontend form validation
  - Validate required fields before submission
  - Validate numeric formats for amounts
  - Validate date formats
  - Display validation errors to user
  - _Requirements: All_

- [ ] 9.2 Add backend input validation
  - Validate POST data exists and is not empty
  - Validate data types (numeric, date, string)
  - Validate foreign key references exist
  - Return descriptive error messages
  - _Requirements: All_

- [ ] 9.3 Implement calculation validation
  - Validate subtotal = sum(categories) - sum(discounts)
  - Validate total_sale = subtotal + taxes
  - Validate total_received = cash + banks + credits - payments
  - Validate difference = total_sale - total_received
  - Alert user if calculations don't match
  - _Requirements: 2.4, 2.5, 3.6, 3.7_

- [ ] 9.4 Implement closure locking validation
  - Check closure status before allowing edits
  - Display error if closure is locked
  - Disable form fields for locked closures
  - _Requirements: 6.4, 6.5_

- [ ] 10. Create index.php entry point
- [ ] 10.1 Create index.php with HTML structure
  - Include DOCTYPE and HTML5 structure
  - Add TailwindCSS CDN link
  - Add jQuery CDN link
  - Include CoffeeSoft framework: src/js/coffeSoft.js
  - Include CoffeeSoft plugins: src/js/plugins.js
  - Include ventas.js module
  - Create root div: `<div id="root"></div>`
  - Initialize app on document ready
  - _Requirements: 1.1, 1.3_

- [ ] 11. Integration and end-to-end testing
- [ ]* 11.1 Test complete sale capture flow
  - Enter sale data with categories, discounts, taxes
  - Verify calculations are correct
  - Save daily sale
  - Verify database records created
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ]* 11.2 Test complete payment forms flow
  - Enter payment data with cash, banks, credits
  - Verify total received calculation
  - Verify difference calculation
  - Save payment forms
  - Verify database records created
  - Verify customer balance updated
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

- [ ]* 11.3 Test closure validation and locking
  - Complete sale and payment forms
  - Verify totals match
  - Lock closure
  - Verify form is disabled
  - Attempt to edit locked closure
  - Verify error message displayed
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ]* 11.4 Test file upload functionality
  - Upload multiple files
  - Verify file size validation
  - Verify file format validation
  - Download uploaded file
  - Delete uploaded file
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

- [ ]* 11.5 Test admin module CRUD operations
  - Create, edit, and deactivate sale categories
  - Create, edit, and deactivate discounts
  - Create, edit, and deactivate cash concepts
  - Create, edit customers
  - Verify all changes persist in database
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 8.5, 9.1, 9.2, 9.3, 9.4, 9.5, 10.1, 10.2, 10.3, 10.4, 10.5_

- [ ]* 11.6 Test turn control functionality (Tabachines)
  - Enter turn-specific data
  - View turn summary
  - Export turn report
  - Verify report contains correct data
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 12. Documentation and deployment
- [ ]* 12.1 Create database migration scripts
  - Write SQL scripts for table creation
  - Write SQL scripts for indexes
  - Write SQL scripts for initial data
  - Document migration order
  - _Requirements: All_

- [ ]* 12.2 Configure deployment environment
  - Set database connection parameters
  - Configure file upload directory permissions
  - Set session timeout
  - Configure error logging
  - _Requirements: All_

- [ ]* 12.3 Create user documentation
  - Document daily closure process
  - Document admin configuration
  - Document turn control (Tabachines)
  - Create troubleshooting guide
  - _Requirements: All_
