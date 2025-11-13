# Requirements Document - Actualización del Modelo de Banco

## Introduction

Este documento define los requisitos para actualizar el modelo de datos del módulo de banco en el sistema de contabilidad. La actualización incluye la modificación de la estructura de las tablas `banks` y `bank_accounts` para mejorar la gestión de cuentas bancarias y su relación con las unidades de negocio.

## Glossary

- **System**: Sistema de contabilidad CoffeeSoft
- **Bank**: Entidad bancaria (BBVA, Santander, Banamex, etc.)
- **Bank_Account**: Cuenta bancaria asociada a un banco y unidad de negocio
- **UDN**: Unidad de Negocio (Business Unit)
- **Payment_Method**: Forma de pago (Efectivo, Transferencia, Tarjeta, etc.)
- **Active_Status**: Estado de activación (1=activo, 0=inactivo)
- **Account_Alias**: Nombre o alias personalizado de la cuenta bancaria
- **Last_Four_Digits**: Últimos 4 dígitos de la cuenta bancaria

## Requirements

### Requirement 1: Gestión de Bancos

**User Story:** Como administrador del sistema, quiero gestionar el catálogo de bancos disponibles, para poder asociar cuentas bancarias a cada banco registrado.

#### Acceptance Criteria

1. THE System SHALL almacenar la información de bancos en la tabla `banks` con los campos: id, name, active, created_at, updated_at
2. WHEN un administrador registra un nuevo banco, THE System SHALL validar que el nombre del banco no exista previamente
3. THE System SHALL permitir activar o desactivar bancos sin eliminar sus registros históricos
4. THE System SHALL mantener un índice en el campo `name` para optimizar búsquedas
5. THE System SHALL establecer `active` con valor por defecto 1 al crear un nuevo banco

### Requirement 2: Gestión de Cuentas Bancarias

**User Story:** Como administrador del sistema, quiero registrar y gestionar cuentas bancarias asociadas a bancos y unidades de negocio, para poder realizar el seguimiento contable de transacciones.

#### Acceptance Criteria

1. THE System SHALL almacenar la información de cuentas bancarias en la tabla `bank_accounts` con los campos: id, account_alias, last_four_digits, payment_method_id, active, created_at, updated_at, bank_id, udn_id
2. WHEN un administrador registra una cuenta bancaria, THE System SHALL requerir obligatoriamente: bank_id y last_four_digits
3. THE System SHALL validar que last_four_digits contenga exactamente 4 dígitos numéricos
4. THE System SHALL permitir asignar un alias opcional (account_alias) a cada cuenta bancaria
5. THE System SHALL permitir asociar una forma de pago (payment_method_id) de manera opcional

### Requirement 3: Relaciones entre Entidades

**User Story:** Como desarrollador del sistema, quiero establecer relaciones correctas entre las tablas banks, bank_accounts, payment_methods y udn, para mantener la integridad referencial de los datos.

#### Acceptance Criteria

1. THE System SHALL establecer una relación de clave foránea entre bank_accounts.bank_id y banks.id
2. THE System SHALL establecer una relación de clave foránea entre bank_accounts.payment_method_id y payment_methods.id
3. THE System SHALL establecer una relación de clave foránea entre bank_accounts.udn_id y udn.idUDN
4. THE System SHALL permitir que payment_method_id sea NULL en bank_accounts
5. THE System SHALL mantener índices en las columnas de claves foráneas para optimizar consultas

### Requirement 4: Filtrado y Consulta de Cuentas Bancarias

**User Story:** Como usuario del sistema, quiero filtrar cuentas bancarias por unidad de negocio, forma de pago y estado, para visualizar únicamente la información relevante.

#### Acceptance Criteria

1. WHEN un usuario selecciona una unidad de negocio, THE System SHALL mostrar únicamente las cuentas bancarias asociadas a esa UDN
2. WHEN un usuario selecciona una forma de pago, THE System SHALL mostrar únicamente las cuentas bancarias asociadas a ese método de pago
3. WHEN un usuario selecciona un estado (activo/inactivo), THE System SHALL mostrar únicamente las cuentas bancarias con ese estado
4. THE System SHALL permitir aplicar múltiples filtros simultáneamente
5. THE System SHALL mostrar todas las cuentas activas por defecto cuando no se apliquen filtros

### Requirement 5: Visualización de Información de Cuentas

**User Story:** Como usuario del sistema, quiero visualizar la información completa de cada cuenta bancaria en un formato claro, para identificar rápidamente las cuentas disponibles.

#### Acceptance Criteria

1. THE System SHALL mostrar el nombre del banco asociado a cada cuenta
2. THE System SHALL mostrar el alias de la cuenta si existe, o "Sin alias" si no está definido
3. THE System SHALL mostrar los últimos 4 dígitos de la cuenta en formato "****XXXX"
4. THE System SHALL mostrar el nombre de la unidad de negocio asociada
5. THE System SHALL mostrar la forma de pago asociada o "N/A" si no está definida
6. THE System SHALL mostrar el estado de la cuenta con indicadores visuales (badge verde para activa, rojo para inactiva)

### Requirement 6: Cambio de Estado de Cuentas

**User Story:** Como administrador del sistema, quiero activar o desactivar cuentas bancarias, para controlar su disponibilidad sin perder el historial contable.

#### Acceptance Criteria

1. WHEN un administrador desactiva una cuenta bancaria, THE System SHALL cambiar el campo active a 0
2. WHEN un administrador activa una cuenta bancaria, THE System SHALL cambiar el campo active a 1
3. THE System SHALL solicitar confirmación antes de cambiar el estado de una cuenta
4. WHEN una cuenta es desactivada, THE System SHALL mostrar el mensaje "La cuenta bancaria ya no estará disponible, pero seguirá reflejándose en los registros contables"
5. WHEN una cuenta es activada, THE System SHALL mostrar el mensaje "La cuenta estará disponible para captura de información"
6. THE System SHALL mantener el historial de transacciones asociadas a cuentas desactivadas

### Requirement 7: Validación de Datos

**User Story:** Como sistema, quiero validar los datos ingresados en el registro y edición de cuentas bancarias, para garantizar la integridad de la información.

#### Acceptance Criteria

1. THE System SHALL validar que bank_id sea un valor numérico válido y exista en la tabla banks
2. THE System SHALL validar que last_four_digits contenga exactamente 4 caracteres numéricos
3. THE System SHALL validar que payment_method_id sea un valor numérico válido si se proporciona
4. THE System SHALL validar que udn_id sea un valor numérico válido si se proporciona
5. THE System SHALL retornar mensajes de error descriptivos cuando las validaciones fallen

### Requirement 8: Timestamps y Auditoría

**User Story:** Como administrador del sistema, quiero registrar automáticamente las fechas de creación y actualización de bancos y cuentas bancarias, para mantener un historial de cambios.

#### Acceptance Criteria

1. THE System SHALL establecer automáticamente created_at con la fecha y hora actual al crear un banco
2. THE System SHALL establecer automáticamente created_at con la fecha y hora actual al crear una cuenta bancaria
3. THE System SHALL actualizar automáticamente updated_at cada vez que se modifique un banco
4. THE System SHALL actualizar automáticamente updated_at cada vez que se modifique una cuenta bancaria
5. THE System SHALL usar el formato DATETIME para los campos de timestamp
