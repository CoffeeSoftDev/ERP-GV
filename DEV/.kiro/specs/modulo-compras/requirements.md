# Requirements Document - Módulo de Compras

## Introduction

<<<<<<< HEAD
El módulo de Compras es un sistema integral para la gestión y administración de compras empresariales, clasificadas por tipo (fondo fijo, corporativo, crédito). Permite registrar, visualizar, editar y eliminar compras, con control de proveedores, productos, categorías y métodos de pago.
=======
El módulo de compras es un sistema integral para la gestión y control de las compras realizadas por la empresa. Permite registrar, visualizar, editar y administrar compras clasificadas por tipo (fondo fijo, corporativo, crédito), facilitando el control de gastos y la trazabilidad de las operaciones de compra.
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806

## Glossary

- **System**: Módulo de Compras del sistema de contabilidad CoffeeSoft
- **User**: Usuario del sistema con permisos para gestionar compras
<<<<<<< HEAD
- **Purchase**: Registro de compra con información de producto, proveedor, montos y clasificación
- **Purchase_Type**: Clasificación de compra (Fondo fijo, Corporativo, Crédito)
- **Product_Category**: Categoría del producto comprado (Gastos de administración, Gastos operativos, etc.)
- **Provider**: Proveedor o entidad que emite la factura
- **Payment_Method**: Método de pago utilizado (Efectivo, Tarjeta de crédito, Tarjeta de débito, Transferencia)

## Requirements

### Requirement 1: Interfaz Principal del Módulo
=======
- **Purchase**: Registro de una compra realizada por la empresa
- **Product_Class**: Categoría principal de productos (cuenta mayor contable)
- **Product**: Subcuenta específica dentro de una categoría de productos
- **Purchase_Type**: Clasificación de la compra (Fondo fijo, Corporativo, Crédito)
- **Method_Pay**: Forma de pago utilizada para la compra
- **Supplier**: Proveedor que suministra productos o servicios
- **UDN**: Unidad de Negocio
- **Invoice**: Número de factura o ticket de la compra

## Requirements

### Requirement 1
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806

**User Story:** Como usuario del sistema, quiero acceder al módulo de compras con una interfaz que muestre el resumen total y las listas de compras por tipo, para visualizar de forma clara el estado de las compras y acceder fácilmente a las opciones de gestión.

#### Acceptance Criteria

<<<<<<< HEAD
1. WHEN THE User accede al módulo de compras, THE System SHALL mostrar cuatro tarjetas con los totales: "Total de compras", "Total compras de fondo fijo", "Total compras a crédito" y "Total compras de corporativo"

2. THE System SHALL mostrar dos botones principales: "Subir archivos de compras" y "Registrar nueva compra" en la parte superior de la interfaz

3. THE System SHALL incluir un filtro desplegable con las opciones: "Mostrar todas las compras", "Compras de fondo fijo", "Compras de corporativo" y "Compras a crédito"

4. THE System SHALL mostrar una tabla con las columnas: "FOLIO", "CLASE PRODUCTO", "PRODUCTO", "TIPO DE COMPRA", "TOTAL" y columna de acciones

5. WHEN THE User selecciona un filtro de tipo de compra, THE System SHALL actualizar la tabla mostrando únicamente las compras del tipo seleccionado

6. THE System SHALL mostrar tres íconos de acción por cada fila: ícono de ojo (ver detalle), ícono de lápiz (editar) e ícono de papelera (eliminar)

### Requirement 2: Registro de Nueva Compra
=======
1. WHEN the User accesses the Purchase module, THE System SHALL display a dashboard with total purchases summary cards
2. WHEN the dashboard loads, THE System SHALL show four summary cards displaying total general purchases, fixed fund purchases, credit purchases, and corporate purchases
3. WHEN the User views the main interface, THE System SHALL display two action buttons labeled "Subir archivos de compras" and "Registrar nueva compra"
4. WHEN the User interacts with the filter dropdown, THE System SHALL allow filtering purchases by purchase type
5. WHEN the purchase table renders, THE System SHALL display columns for Folio, Product Class, Product, Purchase Type, Total, and Actions
6. WHEN the User views action icons in the table, THE System SHALL provide three action options: view detail, edit, and delete purchase

### Requirement 2
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806

**User Story:** Como usuario del sistema, quiero registrar una nueva compra en el sistema mediante un formulario dinámico, para mantener actualizado el control de gastos y clasificar las compras según su tipo.

#### Acceptance Criteria

<<<<<<< HEAD
1. WHEN THE User hace clic en "Registrar nueva compra", THE System SHALL mostrar un modal con el formulario de registro

2. THE System SHALL incluir los campos obligatorios: "Categoría de producto", "Producto", "Tipo de compra", "Proveedor al contado y No de factura", "Subtotal" e "Impuesto"

3. THE System SHALL incluir el campo opcional: "Descripción de la compra"

4. WHEN THE User selecciona "Tipo de compra", THE System SHALL mostrar el campo "Método de pago" con las opciones correspondientes al tipo seleccionado

5. WHEN THE User selecciona "Corporativo" como tipo de compra, THE System SHALL mostrar las opciones de método de pago: "Efectivo", "Tarjeta de débito", "Tarjeta de crédito", "Transferencia" y "Almacén del área compras"

6. THE System SHALL calcular automáticamente el campo "Total" sumando "Subtotal" más "Impuesto"

7. WHEN THE User deja campos obligatorios vacíos y hace clic en "Guardar compra", THE System SHALL mostrar mensajes de validación indicando los campos faltantes

8. WHEN THE User completa todos los campos obligatorios y hace clic en "Guardar compra", THE System SHALL registrar la compra en la base de datos

9. WHEN THE System registra exitosamente una compra, THE System SHALL mostrar un mensaje de confirmación y actualizar la tabla principal

10. WHEN THE System registra exitosamente una compra, THE System SHALL actualizar los totales mostrados en las tarjetas superiores

### Requirement 3: Visualización de Detalle de Compra

**User Story:** Como usuario del sistema, quiero visualizar el detalle completo de una compra seleccionada, para consultar la información del producto, tipo, método de pago y totales.

#### Acceptance Criteria

1. WHEN THE User hace clic en el ícono de ojo de una compra, THE System SHALL mostrar un modal con el título "DETALLE DE COMPRA"

2. THE System SHALL mostrar la sección "INFORMACIÓN DEL PRODUCTO" con los campos: "Categoría de producto", "Producto", "Tipo de compra" y "Método de pago"

3. THE System SHALL mostrar la sección "INFORMACIÓN DE FACTURACIÓN" con el campo "Número de Ticket/Factura"

4. THE System SHALL mostrar la sección "DESCRIPCIÓN" con el texto descriptivo de la compra

5. THE System SHALL mostrar la sección "RESUMEN FINANCIERO" con los campos: "Subtotal", "Impuesto" y "Total"

6. THE System SHALL mostrar en el encabezado del modal la información: "Actualizado por última vez: [fecha], Por: [nombre_usuario]"

7. THE System SHALL formatear los montos con el símbolo de moneda y dos decimales (ej: $ 1,012.00)

### Requirement 4: Edición de Compra
=======
1. WHEN the User clicks "Registrar nueva compra", THE System SHALL display a modal form with all required purchase fields
2. WHEN the form loads, THE System SHALL include fields for Product Category, Product, Purchase Type, Supplier/Invoice, Subtotal, Tax, and Description
3. WHEN the User selects a Purchase Type, THE System SHALL adapt the form flow according to the selected type (Fixed Fund, Corporate, Credit)
4. WHEN the User attempts to save, THE System SHALL validate all required fields before submission
5. WHEN the User clicks "Guardar compra" with valid data, THE System SHALL register the purchase and update the main table
6. WHEN the purchase is saved successfully, THE System SHALL display a visual confirmation message

### Requirement 3
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806

**User Story:** Como usuario del sistema, quiero editar los datos de una compra existente, para corregir o actualizar información registrada previamente.

#### Acceptance Criteria

<<<<<<< HEAD
1. WHEN THE User hace clic en el ícono de lápiz de una compra, THE System SHALL mostrar un modal con el título "EDITAR COMPRA"

2. THE System SHALL prellenar todos los campos del formulario con los datos actuales de la compra seleccionada

3. THE System SHALL permitir editar los campos: "Categoría de producto", "Producto", "Tipo de compra", "Método de pago", "Número o folio de ticket o factura", "Subtotal", "Impuesto" y "Descripción de la compra"

4. WHEN THE User modifica el "Subtotal" o "Impuesto", THE System SHALL recalcular automáticamente el "Total"

5. WHEN THE User deja campos obligatorios vacíos, THE System SHALL mostrar mensajes de validación antes de permitir guardar

6. WHEN THE User hace clic en "Editar compra" con datos válidos, THE System SHALL actualizar el registro en la base de datos

7. WHEN THE System actualiza exitosamente una compra, THE System SHALL mostrar un mensaje de confirmación y actualizar la tabla principal

8. WHEN THE System actualiza exitosamente una compra, THE System SHALL recalcular y actualizar los totales en las tarjetas superiores si el tipo de compra o monto cambió

### Requirement 5: Eliminación de Compra
=======
1. WHEN the User clicks the edit icon for a purchase, THE System SHALL display a modal form with current purchase data
2. WHEN the edit form loads, THE System SHALL populate all fields with existing purchase information
3. WHEN the User modifies data, THE System SHALL allow editing of category, product, purchase type, payment method, subtotal, tax, and description fields
4. WHEN the User clicks "Editar compra", THE System SHALL validate the modified data before saving
5. WHEN validation passes, THE System SHALL update the purchase record in the database
6. WHEN the update completes, THE System SHALL display a success confirmation message

### Requirement 4
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806

**User Story:** Como usuario del sistema, quiero eliminar una compra del registro, para mantener la base de datos limpia y actualizada.

#### Acceptance Criteria

<<<<<<< HEAD
1. WHEN THE User hace clic en el ícono de papelera de una compra, THE System SHALL mostrar un modal de confirmación con el título "ELIMINAR COMPRA"

2. THE System SHALL mostrar el mensaje "¿Esta seguro de querer eliminar la compra?" con un ícono de interrogación

3. THE System SHALL mostrar dos botones: "Continuar" (azul) y "Cancelar" (blanco con borde rojo)

4. WHEN THE User hace clic en "Cancelar", THE System SHALL cerrar el modal sin realizar cambios

5. WHEN THE User hace clic en "Continuar", THE System SHALL eliminar el registro de la base de datos de forma permanente

6. WHEN THE System elimina exitosamente una compra, THE System SHALL mostrar un mensaje de confirmación

7. WHEN THE System elimina exitosamente una compra, THE System SHALL actualizar la tabla principal removiendo la fila eliminada

8. WHEN THE System elimina exitosamente una compra, THE System SHALL recalcular y actualizar los totales en las tarjetas superiores

### Requirement 6: Filtrado de Compras por Tipo

**User Story:** Como usuario del sistema, quiero filtrar las compras por tipo (fondo fijo, corporativo, crédito o todas), para visualizar únicamente las compras que me interesan en un momento dado.

#### Acceptance Criteria

1. THE System SHALL mostrar un selector desplegable con las opciones: "Mostrar todas las compras", "Compras de fondo fijo", "Compras de corporativo" y "Compras a crédito"

2. WHEN THE User selecciona "Mostrar todas las compras", THE System SHALL mostrar todas las compras registradas en la tabla

3. WHEN THE User selecciona "Compras de fondo fijo", THE System SHALL mostrar únicamente las compras con tipo_compra_id correspondiente a fondo fijo

4. WHEN THE User selecciona "Compras de corporativo", THE System SHALL mostrar únicamente las compras con tipo_compra_id correspondiente a corporativo

5. WHEN THE User selecciona "Compras a crédito", THE System SHALL mostrar únicamente las compras con tipo_compra_id correspondiente a crédito

6. THE System SHALL mantener visible el total general en la tarjeta "Total de compras" independientemente del filtro aplicado

### Requirement 7: Cálculo y Visualización de Totales

**User Story:** Como usuario del sistema, quiero ver los totales de compras generales y por tipo en tiempo real, para tener un resumen financiero actualizado del estado de las compras.

#### Acceptance Criteria

1. THE System SHALL calcular y mostrar el "Total de compras" sumando todas las compras activas en la base de datos

2. THE System SHALL calcular y mostrar el "Total compras de fondo fijo" sumando únicamente las compras con tipo fondo fijo

3. THE System SHALL calcular y mostrar el "Total compras a crédito" sumando únicamente las compras con tipo crédito

4. THE System SHALL calcular y mostrar el "Total compras de corporativo" sumando únicamente las compras con tipo corporativo

5. WHEN THE User registra una nueva compra, THE System SHALL actualizar inmediatamente los totales correspondientes

6. WHEN THE User edita una compra existente, THE System SHALL recalcular los totales si el monto o tipo de compra cambió

7. WHEN THE User elimina una compra, THE System SHALL restar el monto eliminado de los totales correspondientes

8. THE System SHALL formatear todos los totales con el símbolo de moneda y dos decimales (ej: $ 13,826.13)

### Requirement 8: Gestión de Proveedores

**User Story:** Como usuario del sistema, quiero seleccionar proveedores de una lista predefinida al registrar compras, para mantener un catálogo consistente de proveedores.

#### Acceptance Criteria

1. THE System SHALL mostrar un selector desplegable "Proveedor" en el formulario de registro de compra

2. THE System SHALL cargar la lista de proveedores activos desde la tabla proveedores en la base de datos

3. WHEN THE User selecciona un proveedor, THE System SHALL asociar el proveedor_id con la compra

4. THE System SHALL permitir ingresar manualmente el número de factura o ticket en el campo "Proveedor al contado y No de factura"

5. THE System SHALL validar que el campo de proveedor no esté vacío antes de permitir guardar la compra

### Requirement 9: Gestión de Categorías y Productos

**User Story:** Como usuario del sistema, quiero seleccionar categorías de producto y productos relacionados al registrar compras, para clasificar correctamente los gastos.

#### Acceptance Criteria

1. THE System SHALL mostrar un selector desplegable "Categoría de producto" en el formulario de registro

2. THE System SHALL cargar las categorías desde la tabla clase_insumo en la base de datos

3. WHEN THE User selecciona una categoría, THE System SHALL cargar en el selector "Producto" únicamente los productos relacionados con esa categoría

4. THE System SHALL mostrar el selector "Producto" deshabilitado hasta que se seleccione una categoría

5. THE System SHALL validar que ambos campos (categoría y producto) estén seleccionados antes de permitir guardar

### Requirement 10: Validación de Campos Numéricos

**User Story:** Como usuario del sistema, quiero que los campos de montos validen automáticamente el formato numérico, para evitar errores de captura.

#### Acceptance Criteria

1. THE System SHALL validar que los campos "Subtotal" e "Impuesto" acepten únicamente valores numéricos

2. THE System SHALL formatear automáticamente los campos de monto con dos decimales al perder el foco

3. THE System SHALL mostrar el símbolo de moneda ($) antes de los campos de monto

4. WHEN THE User ingresa un valor no numérico en campos de monto, THE System SHALL mostrar un mensaje de error

5. THE System SHALL validar que el "Subtotal" sea mayor a cero antes de permitir guardar

6. THE System SHALL permitir que el campo "Impuesto" sea cero o mayor
=======
1. WHEN the User clicks the delete icon for a purchase, THE System SHALL display a confirmation modal
2. WHEN the confirmation modal appears, THE System SHALL show two options: "Continuar" and "Cancelar"
3. WHEN the User clicks "Continuar", THE System SHALL permanently delete the purchase record from the database
4. WHEN the deletion completes successfully, THE System SHALL display a success message
5. WHEN the deletion fails, THE System SHALL display an error message with details
6. WHEN the User clicks "Cancelar", THE System SHALL close the modal without deleting the record

### Requirement 5

**User Story:** Como usuario del sistema, quiero visualizar el detalle completo de una compra seleccionada, para consultar la información del producto, tipo, método de pago y totales.

#### Acceptance Criteria

1. WHEN the User clicks the view detail icon for a purchase, THE System SHALL display a detail modal with complete purchase information
2. WHEN the detail modal loads, THE System SHALL show product information including category and product name
3. WHEN displaying purchase details, THE System SHALL include purchase type and payment method information
4. WHEN showing financial information, THE System SHALL display subtotal, tax, and total amounts
5. WHEN the detail view renders, THE System SHALL include description field and invoice number
6. WHEN displaying metadata, THE System SHALL show the date and user who performed the last update

### Requirement 6

**User Story:** Como administrador del sistema, quiero gestionar las categorías de productos (product_class), para organizar las compras según las cuentas mayores contables.

#### Acceptance Criteria

1. WHEN the User accesses the product categories section, THE System SHALL display a list of all active product classes
2. WHEN the User creates a new product class, THE System SHALL require a unique name and description
3. WHEN the User edits a product class, THE System SHALL allow modification of name and description fields
4. WHEN the User deactivates a product class, THE System SHALL change its active status to inactive
5. WHEN a product class is inactive, THE System SHALL not display it in purchase registration forms

### Requirement 7

**User Story:** Como administrador del sistema, quiero gestionar los productos (product), para mantener actualizado el catálogo de subcuentas disponibles para compras.

#### Acceptance Criteria

1. WHEN the User accesses the products section, THE System SHALL display products grouped by their product class
2. WHEN the User creates a new product, THE System SHALL require selection of a product class, name, and description
3. WHEN the User edits a product, THE System SHALL allow modification of product class, name, and description
4. WHEN the User deactivates a product, THE System SHALL change its active status to inactive
5. WHEN a product is inactive, THE System SHALL not display it in purchase registration forms

### Requirement 8

**User Story:** Como administrador del sistema, quiero gestionar los proveedores (supplier), para mantener un registro actualizado de los proveedores con los que trabaja la empresa.

#### Acceptance Criteria

1. WHEN the User accesses the suppliers section, THE System SHALL display a list of all suppliers by UDN
2. WHEN the User creates a new supplier, THE System SHALL require name, RFC, phone, email, and UDN fields
3. WHEN the User edits a supplier, THE System SHALL allow modification of all supplier information including balance
4. WHEN displaying supplier information, THE System SHALL show current balance with the supplier
5. WHEN a supplier is deactivated, THE System SHALL change its active status to inactive

### Requirement 9

**User Story:** Como usuario del sistema, quiero que el sistema calcule automáticamente el total de la compra, para evitar errores de cálculo manual.

#### Acceptance Criteria

1. WHEN the User enters a subtotal value, THE System SHALL store the value as a decimal with 2 decimal places
2. WHEN the User enters a tax value, THE System SHALL store the value as a decimal with 2 decimal places
3. WHEN subtotal and tax are entered, THE System SHALL automatically calculate the total as subtotal plus tax
4. WHEN displaying monetary values, THE System SHALL format amounts with currency symbol and thousand separators
5. WHEN saving the purchase, THE System SHALL store subtotal, tax, and total as separate fields in the database

### Requirement 10

**User Story:** Como usuario del sistema, quiero filtrar las compras por tipo y fecha, para analizar los gastos de manera específica.

#### Acceptance Criteria

1. WHEN the User selects a purchase type filter, THE System SHALL display only purchases matching the selected type
2. WHEN the User selects "Mostrar todas las compras", THE System SHALL display all purchases regardless of type
3. WHEN the filter is applied, THE System SHALL update the summary cards to reflect filtered totals
4. WHEN the User changes the filter, THE System SHALL refresh the purchase table with filtered results
5. WHEN no purchases match the filter criteria, THE System SHALL display an appropriate message
>>>>>>> ebba68b5452f35b0a4bbd1da087c1aa15b436806
