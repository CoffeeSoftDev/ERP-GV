# Requirements Document - Módulo de Cuentas de Mayor

## Introduction

El módulo de Cuentas de Mayor es un componente del sistema de contabilidad que permite gestionar el catálogo contable por unidad de negocio. Este módulo facilita la administración de cuentas de mayor, subcuentas, tipos de compra y formas de pago, manteniendo la trazabilidad contable y permitiendo activar/desactivar registros sin afectar el histórico.

## Glossary

- **System**: El módulo de Cuentas de Mayor dentro del sistema de contabilidad CoffeeSoft
- **User**: Usuario del sistema con permisos para gestionar cuentas contables
- **Administrator**: Usuario con permisos completos para editar y cambiar estados de cuentas
- **Business_Unit**: Unidad de negocio (UDN) a la cual pertenecen las cuentas contables
- **Major_Account**: Cuenta de mayor del catálogo contable
- **Account_Status**: Estado de la cuenta (activo/inactivo)
- **Historical_Record**: Registro contable histórico que no debe ser afectado por cambios de estado

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a la interfaz del módulo de cuentas de mayor con pestañas visibles y filtros por unidad de negocio, para gestionar de forma estructurada las cuentas de mayor y sus subniveles.

#### Acceptance Criteria

1. WHEN THE User accesses the module, THE System SHALL display a tabbed interface with four tabs: "Cuenta de mayor", "Subcuenta de mayor", "Tipos de compra", and "Formas de pago"

2. WHEN THE User views the interface, THE System SHALL display a Business_Unit filter with a default option selected

3. WHEN THE User selects the "Cuenta de mayor" tab, THE System SHALL display a table with columns for Major_Account name and action buttons (edit, activate, deactivate)

4. WHEN THE User views the "Cuenta de mayor" tab, THE System SHALL display a button labeled "Agregar nueva cuenta de mayor"

5. WHEN THE User clicks the "Agregar nueva cuenta de mayor" button, THE System SHALL open a modal dialog for creating a new Major_Account

### Requirement 2

**User Story:** Como usuario del sistema, quiero registrar una nueva cuenta de mayor vinculada a una unidad de negocio, para mantener actualizado el catálogo contable del sistema.

#### Acceptance Criteria

1. WHEN THE User opens the new Major_Account modal, THE System SHALL display a form with Business_Unit field (read-only) and Major_Account name field (editable)

2. WHEN THE User enters a Major_Account name, THE System SHALL validate that the name is not duplicated within the same Business_Unit

3. IF THE Major_Account name already exists, THEN THE System SHALL display an error message and prevent submission

4. WHEN THE User clicks the "Guardar" button with valid data, THE System SHALL create the new Major_Account record

5. WHEN THE Major_Account is successfully created, THE System SHALL close the modal and refresh the table to display the new record

### Requirement 3

**User Story:** Como administrador, quiero editar el nombre de una cuenta de mayor existente, para actualizar o corregir información registrada.

#### Acceptance Criteria

1. WHEN THE Administrator clicks the edit icon for a Major_Account, THE System SHALL open a modal with the current Major_Account data

2. WHEN THE edit modal opens, THE System SHALL display the Business_Unit field as read-only and the Major_Account name field as editable

3. WHEN THE Administrator modifies the Major_Account name, THE System SHALL validate that the new name is not duplicated

4. WHEN THE Administrator clicks "Guardar" with valid changes, THE System SHALL update the Major_Account record

5. WHEN THE update is successful, THE System SHALL close the modal and refresh the table to display the updated information

### Requirement 4

**User Story:** Como administrador, quiero activar o desactivar las cuentas de mayor, para controlar qué registros están disponibles para captura sin afectar la contabilidad histórica.

#### Acceptance Criteria

1. WHEN THE Administrator views the Major_Account table, THE System SHALL display a toggle switch for each Major_Account to control Account_Status

2. WHEN THE Administrator clicks to deactivate a Major_Account, THE System SHALL display a confirmation modal with the message "La cuenta mayor ya no estará disponible, pero seguirá reflejándose en los registros contables"

3. WHEN THE Administrator clicks to activate a Major_Account, THE System SHALL display a confirmation modal with the message "La cuenta mayor ya estará disponible, para la captura de información"

4. WHEN THE confirmation modal is displayed, THE System SHALL provide "Continuar" and "Cancelar" buttons

5. WHEN THE Administrator clicks "Continuar", THE System SHALL update the Account_Status and refresh the table

6. WHEN THE Account_Status is changed, THE System SHALL maintain all Historical_Record entries associated with the Major_Account

7. WHEN THE Major_Account is deactivated, THE System SHALL prevent the account from being used in new transactions while preserving existing Historical_Record data
