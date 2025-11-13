# Requirements Document

## Introduction

El módulo de Desbloqueo de Módulos permite a los administradores gestionar solicitudes de apertura de módulos operativos que han sido cerrados por el sistema contable. Incluye la administración de horarios de cierre mensual por unidad de negocio y el registro de motivos de desbloqueo para auditoría.

## Glossary

- **Sistema**: Módulo de Desbloqueo de Módulos
- **UDN**: Unidad de Negocio
- **Módulo Operativo**: Sección funcional del sistema (Ventas, Compras, Clientes, etc.)
- **Usuario Administrador**: Usuario con permisos para desbloquear módulos
- **Solicitud de Apertura**: Petición registrada para desbloquear un módulo cerrado
- **Horario de Cierre Mensual**: Hora límite configurada para el cierre automático de módulos por mes
- **Estado de Bloqueo**: Condición actual del módulo (bloqueado/desbloqueado)

## Requirements

### Requirement 1: Interfaz Principal del Módulo

**User Story:** Como usuario del sistema, quiero acceder a una interfaz principal del módulo de desbloqueo, para visualizar las solicitudes de apertura y realizar acciones administrativas sobre ellas.

#### Acceptance Criteria

1. WHEN THE Usuario accede al módulo, THE Sistema SHALL mostrar un encabezado con botón de regreso al menú principal, nombre del usuario y fecha actual
2. THE Sistema SHALL mostrar un tab con las pestañas: Desbloqueo de módulos, Cuenta de ventas, Formas de pago, Clientes, Compras
3. WHEN THE Usuario selecciona la pestaña "Desbloqueo de módulos", THE Sistema SHALL mostrar una tabla con columnas: UDN, Fecha solicitada, Módulo, Motivo, Bloquear
4. THE Sistema SHALL mostrar botones principales "Desbloquear módulo" y "Horario de cierre mensual" en la parte superior de la tabla
5. THE Sistema SHALL mostrar un ícono de candado en cada fila indicando el estado de bloqueo del módulo

### Requirement 2: Registro de Solicitud de Apertura de Módulo

**User Story:** Como usuario autorizado, quiero registrar una solicitud de apertura de módulo, para permitir la corrección o captura adicional antes del cierre contable.

#### Acceptance Criteria

1. WHEN THE Usuario hace clic en "Desbloquear módulo", THE Sistema SHALL mostrar un modal con el título "APERTURA DE MÓDULO"
2. THE Sistema SHALL incluir en el modal los campos: Fecha solicitada, Unidad de negocio (UDN), Módulo, Motivo de apertura
3. WHEN THE Usuario hace clic en "Continuar", THE Sistema SHALL validar que todos los campos obligatorios estén completos
4. IF algún campo obligatorio está vacío, THEN THE Sistema SHALL mostrar un mensaje de error indicando los campos faltantes
5. WHEN la validación es exitosa, THE Sistema SHALL registrar la solicitud en la base de datos con estado "Pendiente"
6. WHEN la solicitud se registra exitosamente, THE Sistema SHALL actualizar la tabla principal mostrando el nuevo registro
7. THE Sistema SHALL cerrar el modal automáticamente después de registrar la solicitud

### Requirement 3: Configuración de Hora de Cierre Mensual

**User Story:** Como administrador del sistema, quiero actualizar la hora de cierre mensual por mes, para controlar el horario límite de operación de cada módulo.

#### Acceptance Criteria

1. WHEN THE Administrador hace clic en "Horario de cierre mensual", THE Sistema SHALL mostrar un modal con el título "Hora de cierre mensual"
2. THE Sistema SHALL incluir en el modal un selector de mes y un campo de hora
3. THE Sistema SHALL mostrar una tabla con columnas: Mes, Hora de cierre
4. WHEN THE Administrador selecciona un mes y hora, THE Sistema SHALL habilitar el botón "Actualizar hora de cierre"
5. WHEN THE Administrador hace clic en "Actualizar hora de cierre", THE Sistema SHALL validar que la hora sea válida
6. THE Sistema SHALL permitir editar únicamente la hora de cierre de meses futuros o del mes actual
7. IF el mes seleccionado es pasado, THEN THE Sistema SHALL deshabilitar la edición y mostrar un mensaje informativo
8. WHEN la actualización es exitosa, THE Sistema SHALL reflejar el cambio en la tabla de horarios
9. THE Sistema SHALL registrar la fecha y usuario que realizó la modificación para auditoría

### Requirement 4: Gestión de Estado de Bloqueo

**User Story:** Como usuario administrador, quiero cambiar el estado de bloqueo de un módulo, para controlar el acceso a módulos operativos según las necesidades del negocio.

#### Acceptance Criteria

1. WHEN THE Usuario hace clic en el ícono de candado en una fila, THE Sistema SHALL cambiar el estado de bloqueo del módulo
2. IF el módulo está desbloqueado, THEN THE Sistema SHALL bloquearlo y actualizar el ícono a candado cerrado
3. IF el módulo está bloqueado, THEN THE Sistema SHALL desbloquearlo y actualizar el ícono a candado abierto
4. THE Sistema SHALL registrar la fecha y hora de la operación en el campo operation_date
5. THE Sistema SHALL actualizar el campo active en la base de datos (1 = desbloqueado, 0 = bloqueado)
6. THE Sistema SHALL reflejar el cambio inmediatamente en la tabla sin recargar la página

### Requirement 5: Visualización de Módulos Desbloqueados

**User Story:** Como usuario del sistema, quiero visualizar todos los módulos desbloqueados actualmente, para tener visibilidad de qué módulos están disponibles para operación.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar en la tabla principal únicamente los módulos con estado active = 1
2. THE Sistema SHALL ordenar los registros por fecha solicitada de forma descendente
3. WHEN no existen módulos desbloqueados, THE Sistema SHALL mostrar un mensaje "No hay módulos desbloqueados actualmente"
4. THE Sistema SHALL actualizar la tabla automáticamente cada vez que se registra una nueva solicitud
5. THE Sistema SHALL mostrar el nombre de la UDN correspondiente a cada registro mediante relación con la tabla udn
