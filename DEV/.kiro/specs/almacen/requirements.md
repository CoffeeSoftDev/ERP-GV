# Requirements Document - Módulo de Almacén

## Introduction

El módulo de Almacén permite la captura, consulta, modificación y control de las salidas de almacén, así como la visualización de balances e historial de movimientos según el nivel de acceso del usuario. El sistema garantiza la trazabilidad de los movimientos de insumos y la validación mediante archivos de respaldo.

## Glossary

- **System**: Módulo de Almacén del sistema CoffeeSoft
- **User**: Usuario del sistema con diferentes niveles de acceso
- **Warehouse_Output**: Salida de almacén registrada en el sistema
- **Warehouse_Input**: Entrada de almacén registrada en el sistema
- **Supply**: Insumo o producto del almacén
- **Business_Unit**: Unidad de negocio (UDN)
- **Access_Level**: Nivel de permisos del usuario (Captura, Gerencia, Contabilidad, Administración)
- **Balance**: Diferencia entre entradas y salidas de almacén
- **Backup_File**: Archivo de respaldo asociado a movimientos de almacén

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a la interfaz del módulo de Almacén con pestañas organizadas, para visualizar el total de salidas, registrar nuevas salidas y gestionar archivos asociados.

#### Acceptance Criteria

1. WHEN the user accesses the Warehouse module, THE System SHALL display an organized interface with tabs for different functionalities
2. THE System SHALL display the total amount of warehouse outputs for the current day
3. THE System SHALL include action buttons for "Warehouse Summary", "Upload Warehouse Files", and "Register New Warehouse Output"
4. THE System SHALL display a table with columns: Warehouse, Amount, Description, Edit and Delete buttons
5. WHEN the user changes the selected date, THE System SHALL automatically update the table with outputs for that date
6. THE System SHALL display the sum total of all outputs for the selected day at all times
7. WHERE file upload is enabled, THE System SHALL allow uploading backup files with a maximum size of 20 MB

### Requirement 2

**User Story:** Como usuario de nivel captura, quiero registrar y modificar las salidas de almacén, para mantener actualizada la información de los movimientos diarios de cada unidad de negocio.

#### Acceptance Criteria

1. THE System SHALL display a form with fields: Warehouse (selector with supply list), Quantity (numeric field), and Description (text field)
2. THE System SHALL require all form fields to be filled before submission
3. WHEN the user saves a warehouse output, THE System SHALL automatically update the daily outputs table
4. THE System SHALL allow editing existing records through a modal with the same fields
5. WHEN the user saves or updates data, THE System SHALL display a visual confirmation message

### Requirement 3

**User Story:** Como usuario del sistema, quiero eliminar salidas de almacén o visualizar la descripción detallada, para mantener el control y claridad sobre los movimientos registrados.

#### Acceptance Criteria

1. WHEN the user attempts to delete a record, THE System SHALL display a confirmation modal before deletion
2. WHEN a record is deleted, THE System SHALL log the user, date, and amount of the deleted output in the audit log
3. THE System SHALL include an action to view the record description in an informative modal
4. WHEN an action is completed, THE System SHALL display a success or error message based on the result

### Requirement 4

**User Story:** Como administrador del sistema, quiero definir los niveles de acceso del módulo de Almacén, para controlar qué operaciones puede realizar cada usuario según su rol.

#### Acceptance Criteria

1. THE System SHALL configure Level 1 (Capture) access to allow capturing, modifying, and querying daily warehouse outputs
2. THE System SHALL configure Level 2 (Management) access to allow querying daily summaries and individual or general balances with Excel export option
3. THE System SHALL configure Level 3 (Accounting/Direction) access to allow filtering by business unit without modifying data
4. THE System SHALL configure Level 4 (Administration) access to allow managing supply classes, products, and locking/unlocking the module
5. THE System SHALL dynamically change the view and buttons based on the user's access level
6. WHEN the user has Level 1 access, THE System SHALL display the module name as "Salidas de almacén"
7. WHEN the user has Level 2 or higher access, THE System SHALL display the module name as "Almacén"

### Requirement 5

**User Story:** Como usuario de nivel gerencia o superior, quiero visualizar el concentrado de entradas y salidas del almacén, para analizar los balances diarios e históricos por unidad de negocio.

#### Acceptance Criteria

1. THE System SHALL display a table with balances per warehouse, separating Inputs and Outputs in different columns with distinct colors
2. THE System SHALL allow selecting a configurable date range
3. THE System SHALL display general totals for inputs, outputs, and final balance
4. THE System SHALL include an "Export to Excel" button
5. THE System SHALL allow expanding the table to show detailed movements for each warehouse
