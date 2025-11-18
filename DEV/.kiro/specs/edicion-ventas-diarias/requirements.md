# Requirements Document

## Introduction

Este documento define los requisitos para agregar funcionalidad de edición a la tabla de ventas diarias en el módulo de ventas. La funcionalidad permitirá a los usuarios editar los registros de ventas capturados previamente, manteniendo la integridad de los datos y proporcionando una experiencia de usuario fluida.

## Glossary

- **Sistema de Ventas**: El módulo completo de gestión de ventas diarias que incluye captura, consulta y edición de registros
- **Registro de Venta**: Un registro individual que contiene información de ventas para una fecha específica, incluyendo categorías como alimentos, bebidas, hospedaje, etc.
- **UDN (Unidad de Negocio)**: Identificador de la unidad de negocio (1=Hotel, 5=Restaurante, otros=Mixto)
- **Tabla de Ventas**: Componente visual que muestra los registros de ventas en formato tabular
- **Modal de Edición**: Ventana emergente que permite modificar los datos de un registro de venta
- **Backend**: Servidor PHP que procesa las peticiones y gestiona la base de datos

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema de ventas, quiero poder editar los registros de ventas capturados previamente, para corregir errores o actualizar información.

#### Acceptance Criteria

1. WHEN el usuario visualiza la tabla de ventas en modo "Captura de ventas", THE Sistema de Ventas SHALL mostrar un botón de edición en cada fila que tenga estado "Capturado"
2. WHEN el usuario hace clic en el botón de edición, THE Sistema de Ventas SHALL abrir un modal con los datos actuales del registro
3. WHEN el modal de edición se abre, THE Sistema de Ventas SHALL cargar automáticamente todos los campos con los valores existentes del registro
4. WHEN el usuario modifica los datos y guarda, THE Sistema de Ventas SHALL validar que todos los campos numéricos sean válidos
5. IF la validación es exitosa, THEN THE Sistema de Ventas SHALL actualizar el registro en la base de datos y refrescar la tabla

### Requirement 2

**User Story:** Como usuario del sistema, quiero que el botón de edición solo aparezca en registros capturados, para evitar confusión con registros pendientes.

#### Acceptance Criteria

1. WHEN un registro tiene estado "Capturado", THE Sistema de Ventas SHALL mostrar un botón de edición con ícono de lápiz
2. WHEN un registro tiene estado "Pendiente", THE Sistema de Ventas SHALL NO mostrar el botón de edición
3. THE Sistema de Ventas SHALL aplicar estilos visuales consistentes al botón de edición (color azul, ícono icon-pencil)
4. WHEN el usuario pasa el cursor sobre el botón de edición, THE Sistema de Ventas SHALL mostrar un efecto hover

### Requirement 3

**User Story:** Como usuario del sistema, quiero que el modal de edición muestre los campos correctos según la UDN seleccionada, para mantener la consistencia con el proceso de captura.

#### Acceptance Criteria

1. WHEN la UDN es 1 (Hotel), THE Sistema de Ventas SHALL mostrar campos: noHabitaciones, Hospedaje, AyB, Diversos
2. WHEN la UDN es 5 (Restaurante), THE Sistema de Ventas SHALL mostrar campos: noHabitaciones, alimentos, bebidas, guarniciones, sales, domicilio
3. WHEN la UDN es otra, THE Sistema de Ventas SHALL mostrar campos: noHabitaciones, alimentos, bebidas
4. THE Sistema de Ventas SHALL etiquetar cada campo con su nombre descriptivo en español
5. THE Sistema de Ventas SHALL aplicar formato de moneda a los campos numéricos

### Requirement 4

**User Story:** Como desarrollador del sistema, quiero que el backend valide y procese correctamente las actualizaciones, para mantener la integridad de los datos.

#### Acceptance Criteria

1. THE Backend SHALL recibir el id_venta, fecha, udn y todos los campos modificados
2. THE Backend SHALL validar que el id_venta exista en la base de datos
3. THE Backend SHALL validar que todos los campos numéricos sean mayores o iguales a cero
4. IF la validación falla, THEN THE Backend SHALL retornar status 400 con mensaje de error descriptivo
5. IF la validación es exitosa, THEN THE Backend SHALL actualizar el registro y retornar status 200

### Requirement 5

**User Story:** Como usuario del sistema, quiero recibir retroalimentación visual después de editar un registro, para confirmar que la operación fue exitosa.

#### Acceptance Criteria

1. WHEN la actualización es exitosa, THE Sistema de Ventas SHALL mostrar una alerta de éxito con mensaje "Registro actualizado correctamente"
2. WHEN la actualización falla, THE Sistema de Ventas SHALL mostrar una alerta de error con el mensaje específico del error
3. WHEN la actualización es exitosa, THE Sistema de Ventas SHALL cerrar el modal automáticamente
4. WHEN la actualización es exitosa, THE Sistema de Ventas SHALL refrescar la tabla para mostrar los datos actualizados
5. THE Sistema de Ventas SHALL mantener los filtros actuales (mes, año, UDN) después de la actualización

### Requirement 6

**User Story:** Como usuario del sistema, quiero que el modal de edición tenga una interfaz clara y fácil de usar, para minimizar errores de captura.

#### Acceptance Criteria

1. THE Sistema de Ventas SHALL mostrar el título del modal como "Editar Venta - [Fecha]"
2. THE Sistema de Ventas SHALL agrupar los campos visualmente con labels descriptivos
3. THE Sistema de Ventas SHALL aplicar validación en tiempo real para campos numéricos
4. THE Sistema de Ventas SHALL incluir un botón "Guardar" y un botón "Cancelar"
5. WHEN el usuario hace clic en "Cancelar", THE Sistema de Ventas SHALL cerrar el modal sin guardar cambios
