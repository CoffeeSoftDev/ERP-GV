# Requirements Document - Módulo de Cuentas de Ventas

## Introduction

El módulo de Cuentas de Ventas permite gestionar las categorías o cuentas de venta del sistema, con opciones para asignar permisos (descuento, cortesía) e impuestos aplicables (IVA, IEPS, Hospedaje, 0%), además de habilitar o deshabilitar dichas categorías según las configuraciones del sistema Soft-Restaurant.

## Glossary

- **System**: El módulo de Cuentas de Ventas dentro del sistema CoffeeSoft
- **SalesCategory**: Categoría o cuenta de venta registrada en el sistema
- **UDN**: Unidad de Negocio (Business Unit)
- **Permission**: Permiso asignado a una categoría (descuento o cortesía)
- **Tax**: Impuesto aplicable a una categoría (IVA, IEPS, Hospedaje, 0%)
- **SoftRestaurant**: Sistema externo de punto de venta
- **User**: Usuario administrador del sistema
- **ActiveStatus**: Estado activo/inactivo de una categoría

## Requirements

### Requirement 1

**User Story:** As a User, I want to access the sales accounts module interface with organized tabs, so that I can manage categories and sales permissions associated with each business unit

#### Acceptance Criteria

1. WHEN THE User accesses the module, THE System SHALL display a filter for "Unidad de negocio" with default value "Baos"
2. WHEN THE User views the main interface, THE System SHALL display a table with columns: "Categoría de venta", "Permisos (Descuento, Cortesía)", "Impuestos (IVA, IEPS, Hospedaje, 0%)", and "Acciones (Editar / Desactivar / Activar)"
3. WHEN THE User views the interface, THE System SHALL display a button labeled "Agregar nueva categoría" that opens a modal
4. WHEN THE User selects a different UDN from the filter, THE System SHALL refresh the table with categories specific to that UDN

### Requirement 2

**User Story:** As a User, I want to register a new sales category, so that I can define permissions and apply specific taxes in the system

#### Acceptance Criteria

1. WHEN THE User clicks "Agregar nueva categoría", THE System SHALL display a modal titled "Nueva cuenta de ventas"
2. WHEN THE modal is displayed, THE System SHALL show fields for: "Unidad de negocio", "Nombre de la cuenta", "Permisos" (permitir descuento, permitir cortesía), and "Impuestos aplicables" (IVA, IEPS, Hospedaje, Impuesto al 0%)
3. WHEN THE modal is displayed, THE System SHALL show a validation message: "Antes de registrar una nueva categoría de venta, es importante que exista en el Soft-Restaurant de la unidad de negocio"
4. WHEN THE User submits valid data, THE System SHALL save the new SalesCategory to the database
5. WHEN THE SalesCategory is saved successfully, THE System SHALL update the table automatically and close the modal
6. WHEN THE User submits a category name that already exists for the selected UDN, THE System SHALL display an error message and prevent duplicate creation

### Requirement 3

**User Story:** As a User, I want to modify the permissions and taxes of an existing category, so that I can keep the fiscal and operational information updated

#### Acceptance Criteria

1. WHEN THE User clicks the "Editar" action button, THE System SHALL display a modal titled "Editar categoría de venta"
2. WHEN THE edit modal is displayed, THE System SHALL pre-fill fields with current values: "Nombre de la categoría", "Permisos", and "Impuestos aplicables"
3. WHEN THE edit modal is displayed, THE System SHALL show a warning message: "Antes de cambiar el nombre de la categoría, es importante que el cambio sea realizado con anticipación en el Soft-Restaurant de la unidad de negocio"
4. WHEN THE User submits updated data, THE System SHALL update the SalesCategory in the database
5. WHEN THE update is successful, THE System SHALL refresh the table with updated information and close the modal

### Requirement 4

**User Story:** As a User, I want to activate or deactivate a sales category, so that I can control which accounts can be used in daily sales without affecting accounting records

#### Acceptance Criteria

1. WHEN THE User clicks the deactivate button for an active category, THE System SHALL display a confirmation message: "Esta categoría no podrá usarse en las ventas diarias, pero seguirá reflejándose en los registros contables"
2. WHEN THE User confirms deactivation, THE System SHALL update the ActiveStatus to inactive in the database
3. WHEN THE User clicks the activate button for an inactive category, THE System SHALL display a confirmation message: "Antes de activar la categoría de venta nuevamente, es importante que también se active en el Soft-Restaurant"
4. WHEN THE User confirms activation, THE System SHALL update the ActiveStatus to active in the database
5. WHEN THE ActiveStatus changes, THE System SHALL update the visual state with appropriate icons (blue for active, red for inactive)
6. WHEN THE table is displayed, THE System SHALL show active categories with a blue icon and inactive categories with a red icon
