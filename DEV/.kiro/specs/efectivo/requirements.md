# Requirements Document - Módulo de Efectivo

## Introduction

El módulo de Efectivo es un submódulo del sistema de contabilidad que permite gestionar operaciones en efectivo, registrando movimientos, cierres y retiros, con la posibilidad de activar o desactivar el flujo de efectivo por unidad de negocio.

## Glossary

- **System**: El módulo de gestión de efectivo dentro del sistema de contabilidad CoffeeSoft
- **Cash_Concept**: Concepto o categoría de efectivo (ej: Efectivo, Monedas extranjeras, Bancos)
- **Cash_Movement**: Movimiento de entrada o salida de efectivo
- **UDN**: Unidad de Negocio (Business Unit)
- **Cash_Flow**: Flujo de efectivo activo o inactivo
- **Cash_Closure**: Cierre de caja o efectivo de una unidad de negocio
- **Operation_Type**: Tipo de operación (Suma/Entrada o Resta/Salida)

## Requirements

### Requirement 1

**User Story:** As a system user, I want to access the cash management interface, so that I can view and register operations related to physical money handling

#### Acceptance Criteria

1. WHEN the user accesses the cash module, THE System SHALL display a table with columns: Business Unit, Available Amount, Last Withdrawal, and Status
2. WHEN the interface loads, THE System SHALL display a "New Cash Movement" button to register income or withdrawals
3. WHEN the user views the interface, THE System SHALL provide filter options for business unit, responsible user, and date
4. WHEN a cash concept record is displayed, THE System SHALL show edit and deactivate icons for each record
5. WHERE the user has appropriate permissions, THE System SHALL enable the action buttons for editing and status changes

### Requirement 2

**User Story:** As an authorized user, I want to register a cash inflow or outflow movement, so that I can keep the cash fund control updated

#### Acceptance Criteria

1. WHEN the user clicks "New Cash Movement", THE System SHALL display a modal form with fields: Business Unit, Movement Type, Amount, and Description
2. WHEN the user enters an amount, THE System SHALL validate that the amount is numeric and greater than zero
3. IF the amount is not valid, THEN THE System SHALL display an error message indicating the validation failure
4. WHEN the user selects a movement type, THE System SHALL restrict the options to "Entrada" (Income) or "Salida" (Withdrawal)
5. WHEN the user saves a valid movement, THE System SHALL automatically update the available amount for the business unit
6. WHEN the save operation completes successfully, THE System SHALL display a confirmation message to the user

### Requirement 3

**User Story:** As an administrator, I want to modify the data of a registered movement, so that I can correct capture errors or adjust amounts in a controlled manner

#### Acceptance Criteria

1. WHEN the administrator clicks the edit icon, THE System SHALL display a modal form pre-loaded with the movement data
2. WHEN the form loads, THE System SHALL allow editing of amount and description fields
3. IF the user modifies the movement type, THEN THE System SHALL display a visual warning about the change
4. WHEN the administrator saves changes, THE System SHALL validate all fields before updating
5. WHEN the update completes successfully, THE System SHALL display a confirmation message
6. WHEN the modal closes, THE System SHALL refresh the movements table with updated data

### Requirement 4

**User Story:** As an administrator, I want to perform cash closure for a business unit, so that I can consolidate movements and generate a final accounting record

#### Acceptance Criteria

1. WHEN the administrator views the main interface, THE System SHALL display a "Close Cash" button
2. WHEN the administrator clicks "Close Cash", THE System SHALL request confirmation and display a summary of pending movements
3. WHEN the administrator confirms the closure, THE System SHALL generate an automatic closure record with date and time
4. WHEN the closure completes successfully, THE System SHALL display the message: "El cierre de efectivo se realizó con éxito. No se permitirán más movimientos hasta el siguiente periodo."
5. WHEN a cash closure exists, THE System SHALL prevent new movements from being registered until the next period
6. WHEN the closure is saved, THE System SHALL update the status of all related movements to "closed"

### Requirement 5

**User Story:** As an administrator, I want to activate or deactivate the option to register cash movements, so that I can maintain operational control by business unit or season

#### Acceptance Criteria

1. WHEN the administrator deactivates cash flow, THE System SHALL display the message: "El flujo de efectivo se ha deshabilitado temporalmente para esta unidad."
2. WHEN the administrator activates cash flow, THE System SHALL display the message: "El flujo de efectivo está habilitado nuevamente para registrar movimientos."
3. WHEN cash flow is deactivated, THE System SHALL prevent users from creating new movements for that business unit
4. WHEN the status changes, THE System SHALL update the toggle button to indicate the current state (active/inactive)
5. WHILE cash flow is inactive, THE System SHALL continue to display historical records in the accounting system
6. WHEN the status is toggled, THE System SHALL log the change with user, date, and time information

### Requirement 6

**User Story:** As an administrator, I want to manage cash concepts (categories), so that I can organize different types of cash operations

#### Acceptance Criteria

1. WHEN the administrator accesses the cash concepts section, THE System SHALL display a table with columns: Concept Name, Operation Type, Description, and Actions
2. WHEN the administrator clicks "Add New Concept", THE System SHALL display a modal form with fields: Business Unit, Concept Name, Operation Type, and Description
3. WHEN the administrator saves a new concept, THE System SHALL validate that the concept name is unique within the business unit
4. IF a duplicate concept name exists, THEN THE System SHALL display an error message and prevent creation
5. WHEN the administrator edits a concept, THE System SHALL allow modification of all fields except the business unit
6. WHEN the administrator toggles concept status, THE System SHALL update the active/inactive state and display appropriate confirmation messages
7. WHILE a concept is inactive, THE System SHALL continue to display it in historical records but prevent its use in new movements
