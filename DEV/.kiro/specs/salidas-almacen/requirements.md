# Requirements Document - Módulo de Salidas de Almacén

## Introduction

El módulo de Salidas de Almacén permite registrar, visualizar, editar y eliminar movimientos de salida de inventario del sistema contable. Proporciona un control detallado de las operaciones de almacén con un resumen total y gestión completa de registros.

## Glossary

- **System**: Sistema de contabilidad CoffeeSoft
- **User**: Usuario del sistema con permisos de gestión de almacén
- **Warehouse_Output**: Registro de salida de almacén
- **Warehouse**: Almacén o categoría de inventario
- **Amount**: Monto o cantidad monetaria de la salida
- **Operation_Date**: Fecha en que se realizó la operación
- **Active_Status**: Estado activo/inactivo del registro

## Requirements

### Requirement 1: Visualización del módulo principal

**User Story:** Como usuario del sistema, quiero acceder al módulo de salidas de almacén con una tabla y resumen total, para visualizar todas las salidas registradas y administrar sus operaciones.

#### Acceptance Criteria

1. WHEN THE User accesses the warehouse outputs module, THE System SHALL display a tab interface with "Salidas de almacén" section
2. THE System SHALL display the total sum of all warehouse outputs in a highlighted summary card
3. THE System SHALL render a table with columns: Almacén, Monto, Descripción, and Acciones
4. THE System SHALL display action buttons "Subir archivos de almacén" and "Registrar nueva salida de almacén" in the interface
5. WHERE a warehouse output record exists, THE System SHALL display edit and delete action icons for each row

### Requirement 2: Registro de nueva salida de almacén

**User Story:** Como usuario del sistema, quiero registrar una nueva salida de almacén mediante un formulario, para mantener un control actualizado de los movimientos de inventario.

#### Acceptance Criteria

1. WHEN THE User clicks "Registrar nueva salida de almacén" button, THE System SHALL display a modal form with title "NUEVA SALIDA DE ALMACÉN"
2. THE System SHALL render a select field labeled "Almacén" with placeholder "Selecciona el almacén"
3. THE System SHALL render a numeric input field labeled "Cantidad" with currency format and default value "0.00"
4. THE System SHALL render a textarea field labeled "Descripción (opcional)" with placeholder text
5. IF any required field is empty, THEN THE System SHALL prevent form submission and display validation messages
6. WHEN THE User clicks "Guardar salida de almacén" button with valid data, THE System SHALL save the record to database table warehouse_output
7. WHEN the save operation succeeds, THE System SHALL display a success message and refresh the main table
8. THE System SHALL close the modal form after successful registration

### Requirement 3: Edición de salida de almacén

**User Story:** Como usuario del sistema, quiero editar los datos de una salida registrada, para corregir o actualizar la información existente.

#### Acceptance Criteria

1. WHEN THE User clicks the edit icon for a warehouse output record, THE System SHALL display a modal form with title "EDITAR SALIDA DE ALMACÉN"
2. THE System SHALL pre-populate the form fields with current values: warehouse name, amount, and description
3. THE System SHALL allow modification of fields: Almacén, Cantidad, and Descripción
4. WHEN THE User clicks "Editar salida de almacén" button, THE System SHALL update the record in warehouse_output table
5. IF the update operation succeeds, THEN THE System SHALL display a success message
6. IF the update operation fails, THEN THE System SHALL display an error message
7. THE System SHALL refresh the main table with updated data after successful edit
8. THE System SHALL close the modal form after the operation completes

### Requirement 4: Eliminación de salida de almacén

**User Story:** Como usuario del sistema, quiero eliminar una salida de almacén del registro, para mantener el control y limpieza del historial de movimientos.

#### Acceptance Criteria

1. WHEN THE User clicks the delete icon for a warehouse output record, THE System SHALL display a confirmation modal with title "ELIMINAR SALIDA DE ALMACÉN"
2. THE System SHALL display the question "¿Esta seguro de querer eliminar la salida de almacén?"
3. THE System SHALL provide two action buttons: "Continuar" and "Cancelar"
4. WHEN THE User clicks "Cancelar" button, THE System SHALL close the modal without deleting the record
5. WHEN THE User clicks "Continuar" button, THE System SHALL permanently delete the record from warehouse_output table
6. IF the delete operation succeeds, THEN THE System SHALL display a success message
7. IF the delete operation fails, THEN THE System SHALL display an error message
8. THE System SHALL refresh the main table after successful deletion
9. THE System SHALL update the total sum display after deletion

### Requirement 5: Cálculo y visualización del total

**User Story:** Como usuario del sistema, quiero ver el total general de salidas de almacén, para conocer el monto acumulado de movimientos.

#### Acceptance Criteria

1. THE System SHALL calculate the sum of all active warehouse output amounts
2. THE System SHALL display the total in format "$ X,XXX.XX" in the summary card
3. WHEN a new warehouse output is registered, THE System SHALL recalculate and update the total display
4. WHEN a warehouse output is edited, THE System SHALL recalculate and update the total display
5. WHEN a warehouse output is deleted, THE System SHALL recalculate and update the total display
6. THE System SHALL display the total with currency formatting using formatPrice() function

### Requirement 6: Gestión de estado de registros

**User Story:** Como usuario del sistema, quiero que los registros tengan un estado activo/inactivo, para mantener un historial sin eliminar físicamente los datos.

#### Acceptance Criteria

1. THE System SHALL store an active status field in warehouse_output table
2. WHEN a record is deleted, THE System SHALL set active status to 0 instead of physical deletion
3. THE System SHALL display only records with active status equal to 1 in the main table
4. THE System SHALL exclude inactive records from total sum calculation
5. THE System SHALL maintain operation_date timestamp for all records

### Requirement 7: Integración con base de datos

**User Story:** Como desarrollador del sistema, quiero que el módulo se integre correctamente con la base de datos, para garantizar la persistencia y consistencia de los datos.

#### Acceptance Criteria

1. THE System SHALL use table warehouse_output with fields: id, insumo_id, amount, description, operation_date, active
2. THE System SHALL establish foreign key relationship between warehouse_output.insumo_id and insumo table
3. THE System SHALL use prepared statements for all database operations to prevent SQL injection
4. THE System SHALL validate data types before database insertion: amount as decimal, dates as valid timestamps
5. THE System SHALL handle database connection errors gracefully with user-friendly messages
6. THE System SHALL use transactions for operations that modify multiple records
