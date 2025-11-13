# Requirements Document - Módulo de Clientes con Movimientos a Crédito

## Introduction

Sistema para gestionar clientes con cuentas a crédito en unidades de negocio, permitiendo registrar consumos, anticipos y pagos totales con control de deuda en tiempo real. El módulo se integra con el sistema de corte diario y mantiene un historial detallado de movimientos financieros.

## Glossary

- **System**: Módulo de Clientes con Movimientos a Crédito
- **Customer**: Cliente registrado con capacidad de cuenta a crédito
- **Credit Movement**: Transacción financiera (consumo, anticipo o pago)
- **Balance**: Saldo actual de deuda del cliente
- **Daily Closure**: Corte diario de operaciones por turno
- **UDN**: Unidad de Negocio
- **Movement Type**: Tipo de transacción (consumo a crédito, anticipo, pago total)
- **Payment Method**: Forma de pago (efectivo, banco, N/A para consumos)

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero visualizar un panel con los movimientos de crédito de los clientes, para gestionar consumos, anticipos y pagos de manera centralizada.

#### Acceptance Criteria

1. WHEN THE System loads, THE System SHALL display a dashboard with three summary cards showing total consumptions, total payments/advances in cash, and total payments/advances by bank transfer
2. WHEN THE System displays the movements table, THE System SHALL show columns for Customer Name, Movement Type, Payment Method, Amount, and Actions
3. WHEN THE user selects a filter option, THE System SHALL display only movements matching the selected type (credit consumptions or payments/advances)
4. WHEN THE System displays a movement row, THE System SHALL provide action buttons for View Detail, Edit, and Delete
5. WHEN THE user clicks the "Registrar nuevo movimiento" button, THE System SHALL open a modal form for creating a new credit movement

### Requirement 2

**User Story:** Como usuario del sistema, quiero registrar un nuevo movimiento de crédito (consumo, anticipo o pago total), para mantener actualizada la cuenta del cliente.

#### Acceptance Criteria

1. WHEN THE user opens the new movement modal, THE System SHALL display a form with fields for Customer Name, Current Debt, Movement Type, Payment Method, Amount, and optional Description
2. WHEN THE user selects a customer, THE System SHALL automatically load and display the customer's current debt balance
3. WHEN THE user selects "Consumo a crédito" as movement type, THE System SHALL set Payment Method to "N/A" and disable the payment method field
4. WHEN THE user selects "Anticipo" or "Pago total" as movement type, THE System SHALL enable the Payment Method field with options "Efectivo" and "Banco"
5. WHEN THE user enters an amount for "Pago total", THE System SHALL validate that the amount does not exceed the current debt
6. WHEN THE user submits the form with valid data, THE System SHALL create the movement record, update the customer balance, and link it to the current daily closure
7. WHEN THE System successfully creates a movement, THE System SHALL display a success message and refresh the movements table
8. WHEN THE user enters an amount for "Consumo a crédito", THE System SHALL add the amount to the customer's current debt
9. WHEN THE user enters an amount for "Anticipo" or "Pago total", THE System SHALL subtract the amount from the customer's current debt

### Requirement 3

**User Story:** Como usuario del sistema, quiero eliminar un movimiento de crédito existente, para corregir errores o duplicados en los registros.

#### Acceptance Criteria

1. WHEN THE user clicks the delete button on a movement, THE System SHALL display a confirmation modal with the message "¿Está seguro de querer eliminar el movimiento a crédito?"
2. WHEN THE confirmation modal is displayed, THE System SHALL provide two buttons: "Continuar" and "Cancelar"
3. WHEN THE user clicks "Continuar", THE System SHALL delete the movement record, reverse the balance change on the customer account, and remove the row from the table
4. WHEN THE user clicks "Cancelar", THE System SHALL close the modal without making any changes
5. WHEN THE System successfully deletes a movement, THE System SHALL display a success message and refresh the movements table

### Requirement 4

**User Story:** Como usuario del sistema, quiero ver el detalle completo de un movimiento de crédito, para revisar la información financiera y el impacto en la deuda del cliente.

#### Acceptance Criteria

1. WHEN THE user clicks the view detail button on a movement, THE System SHALL display a modal with complete movement information
2. WHEN THE detail modal is displayed, THE System SHALL show Customer Information section with customer name
3. WHEN THE detail modal is displayed, THE System SHALL show Movement Details section with Movement Type and Payment Method
4. WHEN THE detail modal is displayed, THE System SHALL show Description section with optional movement notes
5. WHEN THE detail modal is displayed, THE System SHALL show Financial Summary section with Current Debt, Movement Amount (positive for consumptions, negative for payments), and New Debt
6. WHEN THE movement type is "Consumo a crédito", THE System SHALL display the calculation: New Debt = Current Debt + Consumption Amount
7. WHEN THE movement type is "Anticipo" or "Pago total", THE System SHALL display the calculation: New Debt = Current Debt - Payment Amount
8. WHEN THE detail modal is displayed, THE System SHALL show the last update timestamp and user who made the change

### Requirement 5

**User Story:** Como usuario del sistema, quiero editar un movimiento de crédito existente, para corregir información incorrecta manteniendo la integridad de los saldos.

#### Acceptance Criteria

1. WHEN THE user clicks the edit button on a movement, THE System SHALL open a modal form pre-filled with the current movement data
2. WHEN THE edit form is displayed, THE System SHALL allow modification of Movement Type, Payment Method, Amount, and Description
3. WHEN THE user changes the amount, THE System SHALL recalculate and display the new debt balance in real-time
4. WHEN THE user submits the edited form, THE System SHALL validate that the new data maintains financial consistency
5. WHEN THE System successfully updates a movement, THE System SHALL adjust the customer balance accordingly and refresh the movements table

### Requirement 6

**User Story:** Como administrador del sistema, quiero que los movimientos de crédito se vinculen automáticamente al corte diario activo, para mantener la trazabilidad financiera por turno.

#### Acceptance Criteria

1. WHEN THE System creates a new credit movement, THE System SHALL automatically link it to the current active daily closure record
2. WHEN THE System links a movement to a daily closure, THE System SHALL store the daily_closure_id foreign key in the detail_credit_customer table
3. WHEN THE daily closure is completed, THE System SHALL include all credit movements in the financial summary
4. WHEN THE System displays movement details, THE System SHALL show the associated daily closure information including date and turn
5. WHEN NO active daily closure exists, THE System SHALL prevent creation of new credit movements and display an error message

### Requirement 7

**User Story:** Como usuario del sistema, quiero gestionar el catálogo de clientes con capacidad de crédito, para controlar qué clientes pueden realizar consumos a crédito.

#### Acceptance Criteria

1. WHEN THE user accesses the customers management section, THE System SHALL display a table with all registered customers showing Name, Current Balance, and Status
2. WHEN THE user clicks "Add Customer", THE System SHALL display a form with fields for Customer Name and UDN selection
3. WHEN THE user creates a new customer, THE System SHALL initialize the balance to 0.00 and set active status to 1
4. WHEN THE user edits a customer, THE System SHALL allow modification of Name and UDN but not the balance (balance is modified only through movements)
5. WHEN THE user deactivates a customer, THE System SHALL set active status to 0 and prevent new credit movements for that customer
6. WHEN THE System displays the customer list, THE System SHALL show only customers belonging to the selected UDN
