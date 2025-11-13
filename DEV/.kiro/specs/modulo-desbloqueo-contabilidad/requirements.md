# Requirements Document

## Introduction

El módulo de Desbloqueo de Módulos permite a los usuarios administrar las solicitudes de apertura de módulos operativos del sistema contable. Los usuarios pueden consultar el estado de los módulos, registrar motivos de desbloqueo y gestionar los horarios de cierre mensual por unidad de negocio (UDN). Este módulo se integra en la carpeta `contabilidad/administrador` y utiliza el pivote admin como base arquitectónica.

## Glossary

- **System**: El módulo de Desbloqueo de Módulos dentro del sistema CoffeeSoft
- **User**: Usuario autorizado del sistema con permisos para gestionar desbloqueos
- **UDN**: Unidad de Negocio (Business Unit)
- **Module**: Módulo operativo del sistema (Ventas, Clientes, Compras, etc.)
- **Unlock Request**: Solicitud de apertura de un módulo bloqueado
- **Lock Status**: Estado de bloqueo/desbloqueo de un módulo
- **Close Time**: Hora límite de cierre mensual para operaciones

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a la interfaz principal del módulo de desbloqueo, para visualizar las solicitudes de apertura y realizar acciones administrativas sobre ellas

#### Acceptance Criteria

1. WHEN the User accesses the module, THE System SHALL display a header section with a back button, user welcome message, and current date
2. WHEN the User views the main interface, THE System SHALL render a tab navigation with five tabs: "Desbloqueo de módulos", "Cuenta de ventas", "Formas de pago", "Clientes", and "Compras"
3. WHEN the User selects the "Desbloqueo de módulos" tab, THE System SHALL display a table with columns: UDN, Fecha solicitada, Módulo, Motivo, and Bloquear
4. WHEN the User views the unlock table, THE System SHALL display action buttons "Desbloquear módulo" and "Horario de cierre mensual" above the table
5. WHEN the User views a table row, THE System SHALL display a lock/unlock icon (candado) indicating the current status

### Requirement 2

**User Story:** Como usuario autorizado, quiero registrar una solicitud de apertura de módulo, para permitir la corrección o captura adicional antes del cierre contable

#### Acceptance Criteria

1. WHEN the User clicks "Desbloquear módulo" button, THE System SHALL display a modal form with fields: Fecha solicitada, Unidad de negocio, Módulo, and Motivo de apertura
2. WHEN the User submits the unlock form with empty required fields, THE System SHALL display validation error messages
3. WHEN the User submits a valid unlock request, THE System SHALL save the request to the database with status "Pendiente"
4. WHEN the unlock request is saved successfully, THE System SHALL refresh the main table and display a success message
5. WHEN the unlock request is saved, THE System SHALL assign a timestamp for operation_date

### Requirement 3

**User Story:** Como usuario del sistema, quiero bloquear un módulo previamente desbloqueado, para restablecer el control de acceso después de completar las correcciones necesarias

#### Acceptance Criteria

1. WHEN the User clicks the lock icon on an unlocked module row, THE System SHALL update the lock_status to "locked" in the database
2. WHEN the lock status is updated, THE System SHALL change the icon from unlocked to locked state
3. WHEN the lock operation completes, THE System SHALL refresh the table row without reloading the entire page
4. WHEN the lock operation fails, THE System SHALL display an error message and maintain the current state
5. WHEN the User locks a module, THE System SHALL record the lock_date timestamp

### Requirement 4

**User Story:** Como administrador del sistema, quiero actualizar la hora de cierre mensual por mes, para controlar el horario límite de operación de cada módulo

#### Acceptance Criteria

1. WHEN the User clicks "Horario de cierre mensual" button, THE System SHALL display a modal with a month selector and time input field
2. WHEN the User views the close time modal, THE System SHALL display a table showing all months with their configured close times
3. WHEN the User selects a future month and enters a valid time, THE System SHALL enable the "Actualizar hora de cierre" button
4. WHEN the User attempts to edit a past month, THE System SHALL disable the time input field
5. WHEN the User saves a close time update, THE System SHALL persist the change to the database and refresh the close time table

### Requirement 5

**User Story:** Como usuario del sistema, quiero visualizar el historial de desbloqueos por UDN, para auditar las operaciones realizadas en cada unidad de negocio

#### Acceptance Criteria

1. WHEN the User views the unlock table, THE System SHALL display all unlock requests ordered by date descending
2. WHEN the User filters by UDN, THE System SHALL display only unlock requests for the selected business unit
3. WHEN the User views a table row, THE System SHALL display the unlock_date and lock_date if applicable
4. WHEN the User views the reason column, THE System SHALL display the complete lock_reason text
5. WHEN the table contains more than 15 records, THE System SHALL implement pagination with 15 rows per page

### Requirement 6

**User Story:** Como desarrollador del sistema, quiero que el módulo siga la arquitectura MVC del framework CoffeeSoft, para mantener la consistencia y facilitar el mantenimiento

#### Acceptance Criteria

1. THE System SHALL implement the frontend using a JavaScript class extending Templates from CoffeeSoft
2. THE System SHALL implement the controller in `ctrl/ctrl-unlock-modules.php` extending the mdl class
3. THE System SHALL implement the model in `mdl/mdl-unlock-modules.php` extending the CRUD class
4. THE System SHALL use TailwindCSS for all styling and layout components
5. THE System SHALL follow the naming conventions: ls() for listings, add() for creation, edit() for updates, and status() for state changes

### Requirement 7

**User Story:** Como usuario del sistema, quiero que la interfaz sea responsive y accesible, para poder gestionar desbloqueos desde diferentes dispositivos

#### Acceptance Criteria

1. WHEN the User accesses the module from a mobile device, THE System SHALL display a responsive layout adapting to screen size
2. WHEN the User interacts with form elements, THE System SHALL provide clear visual feedback for focus states
3. WHEN the User views tables on small screens, THE System SHALL implement horizontal scrolling or column stacking
4. WHEN the User submits forms, THE System SHALL display loading indicators during async operations
5. WHEN the System displays error messages, THE System SHALL use accessible color contrast ratios and clear messaging
