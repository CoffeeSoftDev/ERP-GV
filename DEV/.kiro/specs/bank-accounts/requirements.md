# Requirements Document - Bank Accounts Module

## Introduction

This document defines the requirements for the Bank Accounts Administration module within the CoffeeSoft accounting system. The module enables users to manage banks and bank accounts, providing tools to register new financial institutions, create linked accounts, and control their availability within the accounting system.

## Glossary

- **System**: The CoffeeSoft accounting system
- **Bank**: A financial institution entity with name and status
- **Bank_Account**: An account entity linked to a bank with account details
- **UDN**: Business unit (Unidad de Negocio)
- **Account_Alias**: Optional friendly name for a bank account
- **Last_4_Digits**: Last four digits of the bank account number
- **Active_Status**: Boolean state indicating if account is available for operations
- **Administrator**: User with permissions to manage banks and accounts
- **Accounting_Record**: Historical financial transaction record

## Requirements

### Requirement 1: Display Bank Accounts Administration Interface

**User Story:** As a system user, I want to access the bank accounts administration view, so that I can manage active financial institutions and their linked accounts.

#### Acceptance Criteria

1. WHEN the user navigates to the bank accounts module, THE System SHALL display a table with columns: "Banco", "Nombre de la cuenta", "Últimos 4 dígitos", and action buttons
2. WHEN the interface loads, THE System SHALL display filter controls for business unit and payment method
3. WHEN the interface loads, THE System SHALL display buttons labeled "Agregar nuevo banco" and "Agregar nueva cuenta de banco"
4. WHEN an account row is displayed, THE System SHALL show edit and activate/deactivate icons for each record
5. WHERE the user has administrator permissions, THE System SHALL enable all action buttons

### Requirement 2: Register New Bank

**User Story:** As an administrator, I want to register a new bank within the system, so that I can associate bank accounts and use them in financial processes.

#### Acceptance Criteria

1. WHEN the administrator clicks "Agregar nuevo banco", THE System SHALL display a modal form
2. WHEN the form is displayed, THE System SHALL include a required input field for "Nombre del banco"
3. WHEN the administrator attempts to submit, THE System SHALL validate that the bank name is not empty
4. WHEN the administrator submits a bank name, THE System SHALL validate that no duplicate exists
5. IF a duplicate bank name exists, THEN THE System SHALL display an error message and prevent submission
6. WHEN the administrator submits valid data, THE System SHALL save the bank record with active status set to 1
7. WHEN the save operation succeeds, THE System SHALL display a success confirmation message

### Requirement 3: Register New Bank Account

**User Story:** As an administrator, I want to register a new account associated with a bank, so that I can maintain structured control of accounts by institution and business unit.

#### Acceptance Criteria

1. WHEN the administrator clicks "Agregar nueva cuenta de banco", THE System SHALL display a modal form
2. WHEN the form is displayed, THE System SHALL include fields for: UDN (auto-assigned), Bank (dynamic select), Account alias (optional), Last 4 digits
3. WHEN the form loads, THE System SHALL populate the bank select dropdown with active banks
4. WHEN the administrator enters last 4 digits, THE System SHALL validate that only numeric characters are accepted
5. IF non-numeric characters are entered in last 4 digits, THEN THE System SHALL display a validation error
6. WHEN the administrator submits valid data, THE System SHALL save the account record with active status set to 1
7. WHEN the save operation succeeds, THE System SHALL display a visual success confirmation

### Requirement 4: Edit Existing Bank Account

**User Story:** As an administrator, I want to modify the data of an existing bank account, so that I can keep financial records up to date.

#### Acceptance Criteria

1. WHEN the administrator clicks the edit icon for an account, THE System SHALL display a modal form pre-filled with current account data
2. WHEN the edit form is displayed, THE System SHALL allow modification of bank, alias, and last 4 digits
3. WHEN the administrator modifies the last 4 digits, THE System SHALL validate that only numeric characters are entered
4. IF the validation fails, THEN THE System SHALL display an error message and prevent submission
5. WHEN the administrator submits valid changes, THE System SHALL update the account record in the database
6. WHEN the update operation succeeds, THE System SHALL display a success message
7. WHEN the update completes, THE System SHALL refresh the accounts table to reflect the changes

### Requirement 5: Activate or Deactivate Bank Account

**User Story:** As an administrator, I want to activate or deactivate bank accounts in the system, so that I can control operational availability without deleting historical data.

#### Acceptance Criteria

1. WHEN the administrator clicks the deactivate toggle for an active account, THE System SHALL display a confirmation dialog
2. WHEN deactivating, THE System SHALL show the message: "La cuenta bancaria ya no estará disponible, pero seguirá reflejándose en los registros contables."
3. WHEN the administrator confirms deactivation, THE System SHALL update the active field to 0
4. WHEN the administrator clicks the activate toggle for an inactive account, THE System SHALL display a confirmation dialog
5. WHEN activating, THE System SHALL show the message: "La cuenta estará disponible para captura de información."
6. WHEN the administrator confirms activation, THE System SHALL update the active field to 1
7. WHEN the status change completes, THE System SHALL update the toggle button to reflect the current state
8. WHILE an account is inactive, THE System SHALL exclude it from account selection dropdowns in data entry forms
9. WHILE an account is inactive, THE System SHALL continue to display it in historical accounting records

### Requirement 6: Filter Bank Accounts by Business Unit

**User Story:** As a system user, I want to filter bank accounts by business unit, so that I can view only the accounts relevant to a specific UDN.

#### Acceptance Criteria

1. WHEN the user selects a business unit from the filter dropdown, THE System SHALL display only accounts associated with that UDN
2. WHEN the filter is applied, THE System SHALL maintain the current sort order of the table
3. WHEN the user clears the filter, THE System SHALL display all accounts across all business units

### Requirement 7: Validate Last 4 Digits Format

**User Story:** As an administrator, I want the system to validate the last 4 digits input, so that only valid numeric values are accepted.

#### Acceptance Criteria

1. WHEN the administrator enters last 4 digits, THE System SHALL accept only numeric characters
2. IF the administrator enters a non-numeric character, THEN THE System SHALL prevent the input
3. WHEN the administrator submits the form, THE System SHALL validate that last 4 digits contains exactly 4 numeric characters
4. IF the length is not 4 characters, THEN THE System SHALL display an error message and prevent submission
5. WHEN valid last 4 digits are entered, THE System SHALL format the display with leading zeros if necessary
