# Requirements Document - Módulo de Ventas

## Introduction

El módulo de Ventas es un sistema integral para la captura, consulta y verificación de las ventas diarias por unidad de negocio. Permite registrar las diferentes formas de ingreso (efectivo, bancos, créditos, monedas extranjeras) y asegurar la correspondencia entre el total de venta y el total recibido. El sistema incluye funcionalidades específicas para el control por turnos en unidades como Quinta Tabachines.

## Glossary

- **System**: El módulo de Ventas dentro del sistema de contabilidad CoffeeSoft
- **UDN**: Unidad de Negocio (restaurante, hotel, etc.)
- **Daily Closure**: Corte del día - registro final de ventas y formas de ingreso
- **Sale Category**: Categoría de venta (Alimentos, Bebidas, Diversos, Desechables)
- **Discount**: Descuento aplicado a la venta
- **Courtesy**: Cortesía otorgada sin cargo
- **Tax**: Impuesto aplicado (IVA, IEPS, Hospedaje)
- **Cash Concept**: Concepto de efectivo (propina, vales, dólar, etc.)
- **Bank Account**: Cuenta bancaria para depósitos
- **Customer Credit**: Crédito otorgado a clientes
- **Foreign Currency**: Moneda extranjera (USD, EUR, etc.)
- **Turn**: Turno de trabajo (Matutino, Vespertino, Nocturno)
- **Employee**: Empleado o jefe de turno responsable del corte

## Requirements

### Requirement 1: Interfaz Principal del Módulo

**User Story:** Como usuario del sistema, quiero acceder a una interfaz organizada con pestañas y selector de fecha, para navegar eficientemente entre las diferentes funcionalidades del módulo de ventas.

#### Acceptance Criteria

1. WHEN THE System loads, THE System SHALL display a welcome message with the user name and business unit
2. WHEN THE System loads, THE System SHALL display a date selector in the top right corner
3. WHEN THE System loads, THE System SHALL display a "Menú principal" button that redirects to the main dashboard
4. WHEN THE System loads, THE System SHALL display navigation tabs for Ventas, Clientes, Compras, Salidas de almacén, Pagos a proveedor, and Archivos
5. WHEN a user clicks on a tab, THE System SHALL display the corresponding module content

### Requirement 2: Captura de Venta Diaria

**User Story:** Como encargado de unidad de negocio, quiero registrar las ventas del día por categoría, descuentos, cortesías e impuestos, para obtener un resumen automático del total de venta y facilitar el cierre diario.

#### Acceptance Criteria

1. WHEN THE user accesses the Ventas tab, THE System SHALL display a form with three dynamic groups: Sale Categories, Discounts and Courtesies, and Taxes
2. WHEN THE user enters sale amounts by category, THE System SHALL calculate the total sale without taxes automatically
3. WHEN THE user enters discount and courtesy amounts, THE System SHALL subtract these from the subtotal
4. WHEN THE user enters tax amounts, THE System SHALL calculate the total taxes (IVA + IEPS + Hospedaje)
5. WHEN all amounts are entered, THE System SHALL calculate the final total sale as (Subtotal + Total Taxes)
6. WHERE the UDN has Soft-Restaurant integration enabled, THE System SHALL provide a button to load the daily report automatically
7. WHEN calculations are complete, THE System SHALL enable the "Guardar la venta del día" button
8. WHEN THE user saves the daily sale, THE System SHALL insert a record into the daily_closure table with all calculated totals

### Requirement 3: Registro de Formas de Ingreso

**User Story:** Como cajero o responsable de caja, quiero registrar las formas de ingreso del día (efectivo, bancos, créditos, etc.), para verificar que el total recibido coincida con el total de venta diaria.

#### Acceptance Criteria

1. WHEN THE user accesses the payment forms section, THE System SHALL display four dynamic groups: Cash, Foreign Currencies, Bank Accounts, and Customer Credits
2. WHEN THE user enters cash amounts by concept, THE System SHALL sum all cash concept amounts
3. WHEN THE user enters foreign currency amounts, THE System SHALL convert them to MXN using the current exchange rate
4. WHEN THE user enters bank deposit amounts, THE System SHALL sum all bank account deposits
5. WHEN THE user enters customer credit amounts, THE System SHALL calculate (Credits consumed - Credit payments) as net credit
6. WHEN all payment forms are entered, THE System SHALL calculate Total Received as (Cash + Foreign Currency + Banks + Net Credits)
7. WHEN Total Received is calculated, THE System SHALL display the difference between Total Sale and Total Received
8. IF the difference is not zero, THEN THE System SHALL highlight the difference in red color
9. WHEN THE user saves the payment forms, THE System SHALL insert records into detail_cash_concept, detail_bank_account, and detail_credit_customer tables

### Requirement 4: Carga de Archivos de Comprobante

**User Story:** Como usuario del sistema, quiero subir archivos que respalden los registros del formulario, para mantener evidencia de cada venta capturada.

#### Acceptance Criteria

1. WHEN THE user clicks "Subir archivos de venta", THE System SHALL display a file upload dialog
2. WHEN THE user selects files, THE System SHALL validate that each file is maximum 20 MB
3. WHEN THE user selects files, THE System SHALL validate that file formats are .pdf, .xml, .jpg, or .png
4. WHEN files are uploaded, THE System SHALL associate each file with the operation date and UDN
5. WHEN files are uploaded, THE System SHALL display a list of uploaded files with download and delete options
6. WHEN THE user clicks delete on a file, THE System SHALL remove the file from storage and database

### Requirement 5: Control por Turno (Quinta Tabachines)

**User Story:** Como jefe de turno, quiero capturar la información de ventas separadas por turno, para tener control del desempeño de cada turno y generar reportes específicos.

#### Acceptance Criteria

1. WHERE the UDN is Quinta Tabachines, THE System SHALL display additional fields for Turn, Turn Manager, and Total Suites Rented
2. WHEN THE user selects a turn, THE System SHALL filter the form to capture sales specific to that turn
3. WHEN THE user selects a date range, THE System SHALL display a daily summary by turn
4. WHEN THE user clicks "Exportar", THE System SHALL generate an Excel or PDF report with the turn summary
5. WHEN turn sales are saved, THE System SHALL include the turn information in the daily_closure table

### Requirement 6: Validación y Cierre Diario

**User Story:** Como administrador o gerente de unidad, quiero verificar que el total de venta coincida con el total recibido, para garantizar la exactitud de la información registrada antes de cerrar el día.

#### Acceptance Criteria

1. WHEN all sale and payment data is entered, THE System SHALL display a final summary with Total Sale, Total Received, and Difference
2. IF the difference is not zero, THEN THE System SHALL highlight the difference in red color
3. WHEN THE user clicks "Registrar corte del día", THE System SHALL validate that all required fields are completed
4. WHEN THE daily closure is saved, THE System SHALL lock the form to prevent further edits
5. WHEN THE daily closure is saved, THE System SHALL update the daily_closure table with status "closed"
6. WHEN THE daily closure is saved, THE System SHALL display a success message with the closure ID

### Requirement 7: Gestión de Categorías de Venta

**User Story:** Como administrador del sistema, quiero configurar las categorías de venta disponibles por UDN, para adaptar el sistema a las necesidades específicas de cada unidad de negocio.

#### Acceptance Criteria

1. WHEN THE administrator accesses the admin module, THE System SHALL display a list of active sale categories
2. WHEN THE administrator clicks "Nueva categoría", THE System SHALL display a form to create a new sale category
3. WHEN THE administrator saves a new category, THE System SHALL insert a record into the sale_category table
4. WHEN THE administrator edits a category, THE System SHALL update the sale_category table
5. WHEN THE administrator deactivates a category, THE System SHALL set active = 0 in the sale_category table

### Requirement 8: Gestión de Descuentos y Cortesías

**User Story:** Como administrador del sistema, quiero configurar los tipos de descuentos y cortesías disponibles, para estandarizar los conceptos utilizados en todas las unidades de negocio.

#### Acceptance Criteria

1. WHEN THE administrator accesses the admin module, THE System SHALL display a list of active discount and courtesy types
2. WHEN THE administrator clicks "Nuevo descuento/cortesía", THE System SHALL display a form with fields for name and tax configuration
3. WHEN THE administrator saves a new discount/courtesy, THE System SHALL insert a record into the discount_courtesy table
4. WHEN THE administrator edits a discount/courtesy, THE System SHALL update the discount_courtesy table
5. WHEN THE administrator deactivates a discount/courtesy, THE System SHALL set active = 0 in the discount_courtesy table

### Requirement 9: Gestión de Conceptos de Efectivo

**User Story:** Como administrador del sistema, quiero configurar los conceptos de efectivo disponibles por UDN, para controlar los diferentes tipos de ingresos en efectivo (propina, vales, dólar, etc.).

#### Acceptance Criteria

1. WHEN THE administrator accesses the admin module, THE System SHALL display a list of active cash concepts
2. WHEN THE administrator clicks "Nuevo concepto", THE System SHALL display a form with fields for name, operation type (suma/resta), and UDN
3. WHEN THE administrator saves a new cash concept, THE System SHALL insert a record into the cash_concept table
4. WHEN THE administrator edits a cash concept, THE System SHALL update the cash_concept table
5. WHEN THE administrator deactivates a cash concept, THE System SHALL set active = 0 in the cash_concept table

### Requirement 10: Gestión de Clientes con Crédito

**User Story:** Como administrador del sistema, quiero gestionar el catálogo de clientes que tienen cuenta a crédito, para controlar los consumos y pagos de cada cliente.

#### Acceptance Criteria

1. WHEN THE administrator accesses the Clientes tab, THE System SHALL display a list of active customers with their current balance
2. WHEN THE administrator clicks "Nuevo cliente", THE System SHALL display a form with fields for name, balance, and UDN
3. WHEN THE administrator saves a new customer, THE System SHALL insert a record into the customer table
4. WHEN THE administrator edits a customer, THE System SHALL update the customer table
5. WHEN THE administrator deactivates a customer, THE System SHALL set active = 0 in the customer table
6. WHEN a customer credit is used in a daily closure, THE System SHALL update the customer balance automatically
