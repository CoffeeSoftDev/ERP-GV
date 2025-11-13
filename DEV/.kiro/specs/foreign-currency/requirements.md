# Requirements Document - Foreign Currency Module

## Introduction

This document defines the requirements for the Foreign Currency Administration module within the CoffeeSoft accounting system. The module enables users to manage foreign currencies used by the organization, including configuration of currency names, symbols, and exchange rates (MXN), as well as the ability to activate or deactivate currencies for accounting purposes.

## Glossary

- **System**: The CoffeeSoft accounting system
- **Foreign_Currency**: A currency entity with name, symbol, exchange rate, and status
- **UDN**: Business unit (Unidad de Negocio)
- **Exchange_Rate**: Conversion value from foreign currency to MXN
- **Active_Status**: Boolean state indicating if currency is available for data capture
- **Administrator**: User with permissions to manage foreign currencies
- **Accounting_Record**: Historical financial transaction record

## Requirements

### Requirement 1: Display Foreign Currency Administration Interface

**User Story:** As a system user, I want to access the foreign currency administration view, so that I can visualize, add, edit, or deactivate currencies used in the organization.

#### Acceptance Criteria

1. WHEN the user navigates to the foreign currency module, THE System SHALL display a table with columns: "Moneda extranjera", "Símbolo", "Tipo de cambio (MXN)", and action buttons
2. WHEN the interface loads, THE System SHALL display filter controls for business unit and payment method
3. WHEN the interface loads, THE System SHALL display a button labeled "+ Agregar nueva moneda extranjera"
4. WHEN a currency row is displayed, THE System SHALL show edit and activate/deactivate icons for each record
5. WHERE the user has administrator permissions, THE System SHALL enable all action buttons

### Requirement 2: Register New Foreign Currency

**User Story:** As an administrator, I want to register a new foreign currency with its exchange rate, so that I can use it in accounting operations and monetary conversions.

#### Acceptance Criteria

1. WHEN the administrator clicks "+ Agregar nueva moneda extranjera", THE System SHALL display a modal form
2. WHEN the form is displayed, THE System SHALL include input fields for "Nombre del concepto", "Símbolo de la moneda", and "Tipo de cambio (MXN)"
3. WHEN the administrator attempts to submit, THE System SHALL validate that all required fields are completed
4. IF any required field is empty, THEN THE System SHALL display a validation error message
5. WHEN the administrator submits valid data, THE System SHALL save the currency record with active status set to 1
6. WHEN the save operation succeeds, THE System SHALL display a success confirmation message
7. WHEN the save operation completes, THE System SHALL refresh the currency table to show the new record

### Requirement 3: Edit Existing Foreign Currency

**User Story:** As an administrator, I want to modify the values of an existing currency (name, symbol, exchange rate), so that I can keep the conversion information up to date.

#### Acceptance Criteria

1. WHEN the administrator clicks the edit icon for a currency, THE System SHALL display a modal form pre-filled with current currency values
2. WHEN the edit form is displayed, THE System SHALL show a red warning message: "**Importante: Los cambios afectarán a todas las unidades. Confirme que los retiros de efectivo se hayan realizado antes de actualizar el tipo de cambio (MXN)."
3. WHEN the administrator modifies field values, THE System SHALL validate that exchange rate is greater than zero
4. WHEN the administrator submits valid changes, THE System SHALL update the currency record in the database
5. WHEN the update operation succeeds, THE System SHALL display a green success message: "La moneda se actualizó con éxito. La información futura se calculará según la configuración actual de la moneda."
6. WHEN the update completes, THE System SHALL refresh the currency table to reflect the changes

### Requirement 4: Activate or Deactivate Foreign Currency

**User Story:** As an administrator, I want to activate or deactivate foreign currencies in the system, so that I can control which currencies are available for data capture without affecting historical records.

#### Acceptance Criteria

1. WHEN the administrator clicks the deactivate toggle for an active currency, THE System SHALL display a confirmation dialog
2. WHEN deactivating, THE System SHALL show the message: "La moneda extranjera ya no estará disponible, pero seguirá reflejándose en los registros contables."
3. WHEN the administrator confirms deactivation, THE System SHALL update the active field to 0
4. WHEN the administrator clicks the activate toggle for an inactive currency, THE System SHALL display a confirmation dialog
5. WHEN activating, THE System SHALL show the message: "La moneda extranjera estará disponible para captura de información."
6. WHEN the administrator confirms activation, THE System SHALL update the active field to 1
7. WHEN the status change completes, THE System SHALL update the toggle button to reflect the current state
8. WHILE a currency is inactive, THE System SHALL exclude it from currency selection dropdowns in data entry forms
9. WHILE a currency is inactive, THE System SHALL continue to display it in historical accounting records

### Requirement 5: Filter Foreign Currencies by Business Unit

**User Story:** As a system user, I want to filter foreign currencies by business unit, so that I can view only the currencies relevant to a specific UDN.

#### Acceptance Criteria

1. WHEN the user selects a business unit from the filter dropdown, THE System SHALL display only currencies associated with that UDN
2. WHEN the filter is applied, THE System SHALL maintain the current sort order of the table
3. WHEN the user clears the filter, THE System SHALL display all currencies across all business units

### Requirement 6: Validate Exchange Rate Format

**User Story:** As an administrator, I want the system to validate exchange rate input, so that only valid numeric values are accepted.

#### Acceptance Criteria

1. WHEN the administrator enters an exchange rate value, THE System SHALL accept only numeric characters and decimal point
2. IF the administrator enters a non-numeric character, THEN THE System SHALL prevent the input
3. WHEN the administrator submits the form, THE System SHALL validate that exchange rate is greater than zero
4. IF exchange rate is zero or negative, THEN THE System SHALL display an error message and prevent submission
5. WHEN a valid exchange rate is entered, THE System SHALL format the display to two decimal places
