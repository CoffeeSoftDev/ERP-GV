# Requirements Document - Módulo de Clientes

## Introduction

El Módulo de Clientes es un sistema integral para la gestión de créditos otorgados a clientes activos de cada unidad de negocio. Permite registrar, consultar, modificar y eliminar movimientos de crédito (consumos, abonos parciales y pagos totales), visualizar balances individuales y generales, y mantener sincronización con el módulo de Ventas.

## Glossary

- **Sistema**: Módulo de Clientes del sistema de contabilidad CoffeeSoft
- **Usuario**: Persona autenticada con acceso al sistema según su nivel de permisos
- **Cliente**: Persona o entidad con crédito activo en una unidad de negocio
- **Movimiento**: Registro de consumo, abono parcial o pago total asociado a un cliente
- **UDN**: Unidad de Negocio (sucursal o punto de venta)
- **Saldo**: Deuda actual de un cliente
- **Concentrado**: Reporte consolidado de consumos y pagos por cliente en un rango de fechas
- **Método de Pago**: Forma de pago del movimiento (N/A, Efectivo, Banco)
- **Tipo de Movimiento**: Clasificación del movimiento (Consumo, Abono parcial, Pago total, Anticipo)

## Requirements

### Requirement 1 - Interfaz inicial del módulo

**User Story:** Como usuario del sistema, quiero acceder a la interfaz del módulo de Clientes con pestañas organizadas, para visualizar los consumos y pagos de crédito registrados, así como el balance general del día.

#### Acceptance Criteria

1. WHEN el usuario accede al módulo de Clientes, THE Sistema SHALL renderizar la interfaz con pestañas organizadas (Dashboard, Movimientos, Concentrado).

2. THE Sistema SHALL mostrar tres tarjetas informativas con: total de consumos del día, total de pagos en efectivo y total de pagos en banco.

3. THE Sistema SHALL incluir dos botones principales: "Concentrado de clientes" y "Registrar nuevo movimiento".

4. THE Sistema SHALL mostrar una tabla con las columnas: Cliente, Tipo de movimiento, Método de pago, Monto y Botones de acción.

5. WHEN el usuario selecciona un filtro de tipo de movimiento, THE Sistema SHALL actualizar la tabla mostrando únicamente los registros que coincidan con el tipo seleccionado.

6. WHEN el usuario cambia la fecha de captura, THE Sistema SHALL actualizar automáticamente todos los totales y la tabla de movimientos.

7. THE Sistema SHALL sincronizar los totales de consumos y pagos con el módulo de Ventas en tiempo real.

### Requirement 2 - Registro de movimientos de crédito

**User Story:** Como usuario de nivel captura, quiero registrar nuevos movimientos asociados al crédito de un cliente, para mantener actualizado su saldo y reflejar los consumos o pagos realizados durante el día.

#### Acceptance Criteria

1. WHEN el usuario hace clic en "Registrar nuevo movimiento", THE Sistema SHALL mostrar un formulario modal con los campos: Cliente, Deuda actual, Tipo de movimiento, Método de pago, Cantidad y Descripción.

2. THE Sistema SHALL mostrar el campo "Deuda actual" en modo solo lectura con el saldo actual del cliente seleccionado.

3. THE Sistema SHALL ofrecer tres opciones en el selector "Tipo de movimiento": Consumo, Abono parcial y Pago total.

4. WHEN el usuario selecciona "Consumo" como tipo de movimiento, THE Sistema SHALL establecer automáticamente el método de pago como "N/A" y deshabilitar su edición.

5. WHEN el usuario selecciona "Abono parcial" o "Pago total", THE Sistema SHALL habilitar el selector de método de pago con las opciones: Efectivo y Banco.

6. THE Sistema SHALL validar que todos los campos obligatorios estén completos antes de permitir el envío del formulario.

7. WHEN el usuario envía el formulario con datos válidos, THE Sistema SHALL registrar el movimiento en la base de datos y actualizar el saldo del cliente.

8. WHEN el registro es exitoso, THE Sistema SHALL mostrar un mensaje de confirmación y actualizar automáticamente la tabla de movimientos.

9. IF el registro falla por datos inválidos, THEN THE Sistema SHALL mostrar un mensaje de error específico indicando el problema.

### Requirement 3 - Edición y eliminación de movimientos

**User Story:** Como usuario del sistema, quiero editar o eliminar movimientos de crédito registrados, para mantener la información de los clientes precisa y actualizada.

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de editar de un movimiento, THE Sistema SHALL mostrar un modal con el formulario precargado con los datos actuales del movimiento.

2. THE Sistema SHALL permitir modificar todos los campos del movimiento excepto el cliente asociado.

3. WHEN el usuario guarda los cambios, THE Sistema SHALL actualizar el registro en la base de datos y recalcular el saldo del cliente.

4. WHEN el usuario hace clic en el botón de eliminar, THE Sistema SHALL mostrar un diálogo de confirmación con el mensaje "¿Está seguro de querer eliminar el movimiento a crédito?".

5. WHEN el usuario confirma la eliminación, THE Sistema SHALL registrar la fecha, usuario y cliente del movimiento eliminado en el log de auditoría.

6. THE Sistema SHALL eliminar el registro de la base de datos y actualizar el saldo del cliente.

7. WHEN la operación de edición o eliminación es exitosa, THE Sistema SHALL mostrar un mensaje de confirmación.

8. IF la operación falla, THEN THE Sistema SHALL mostrar un mensaje de error descriptivo.

### Requirement 4 - Detalle y visualización de movimientos

**User Story:** Como usuario del sistema, quiero consultar el detalle completo de un movimiento de crédito, para revisar la información del cliente, el tipo de movimiento, método de pago y saldo actualizado.

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de ver detalle de un movimiento, THE Sistema SHALL mostrar un modal con la información completa del movimiento.

2. THE Sistema SHALL mostrar en el modal: Nombre del cliente, Tipo de movimiento, Método de pago y Descripción.

3. THE Sistema SHALL mostrar un resumen financiero con: Deuda actual, Consumo o Pago (según el tipo), y Nueva deuda calculada.

4. THE Sistema SHALL mostrar información de auditoría: "Actualizado por [nombre_usuario]" y fecha/hora de la última modificación.

5. THE Sistema SHALL calcular y mostrar la nueva deuda según la fórmula:
   - Si es consumo: Nueva deuda = Deuda actual + Cantidad
   - Si es pago: Nueva deuda = Deuda actual - Cantidad

### Requirement 5 - Niveles de acceso del módulo

**User Story:** Como administrador del sistema, quiero definir los niveles de acceso del módulo de Clientes, para controlar qué acciones puede realizar cada usuario según su rol.

#### Acceptance Criteria

1. THE Sistema SHALL implementar cuatro niveles de acceso: Captura, Gerencia, Contabilidad/Dirección y Administración.

2. WHEN un usuario de nivel Captura accede al módulo, THE Sistema SHALL permitir registrar, modificar y consultar únicamente movimientos del día actual.

3. WHEN un usuario de nivel Gerencia accede al módulo, THE Sistema SHALL permitir consultar el concentrado y balances con opción de exportar a Excel.

4. WHEN un usuario de nivel Contabilidad/Dirección accede al módulo, THE Sistema SHALL permitir filtrar por unidad de negocio sin permitir modificar registros.

5. WHEN un usuario de nivel Administración accede al módulo, THE Sistema SHALL permitir gestionar clientes y controlar el bloqueo/desbloqueo del módulo.

6. THE Sistema SHALL validar el nivel de acceso del usuario autenticado antes de renderizar cada componente del módulo.

7. THE Sistema SHALL ocultar o deshabilitar opciones según el nivel de acceso del usuario.

### Requirement 6 - Concentrado y balances por cliente

**User Story:** Como usuario de nivel gerencia o superior, quiero visualizar el concentrado de consumos y pagos de los clientes dentro de un rango de fechas, para obtener el balance individual y general de las unidades de negocio.

#### Acceptance Criteria

1. WHEN el usuario hace clic en "Concentrado de clientes", THE Sistema SHALL mostrar una tabla con balances por cliente.

2. THE Sistema SHALL mostrar columnas diferenciadas por color: Consumos (verde) y Pagos (naranja/rojo).

3. THE Sistema SHALL mostrar para cada cliente: Saldo inicial, Total de consumos, Total de pagos y Saldo final.

4. THE Sistema SHALL permitir seleccionar un rango de fechas personalizado mediante un selector de calendario.

5. WHEN el usuario modifica el rango de fechas, THE Sistema SHALL actualizar automáticamente todos los datos del concentrado.

6. THE Sistema SHALL incluir un botón "Exportar a Excel" que genere un archivo descargable con los datos del concentrado.

7. THE Sistema SHALL mostrar filas expandibles por cliente que muestren el detalle de movimientos individuales.

8. THE Sistema SHALL calcular y mostrar totales generales: Total de consumos, Total de pagos y Balance general.

9. THE Sistema SHALL sincronizar los datos del concentrado con el módulo de Ventas para garantizar consistencia.
