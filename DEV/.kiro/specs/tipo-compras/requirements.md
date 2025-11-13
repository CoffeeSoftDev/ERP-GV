# Requirements Document - Módulo Tipos de Compra

## Introduction

El módulo de **Tipos de Compra** es un submódulo dentro del sistema de Contabilidad que permite administrar los diferentes tipos de compra utilizados en las unidades de negocio. Este módulo facilita la creación, edición, activación y desactivación de tipos de compra, asegurando que la información esté actualizada y disponible para su uso en la captura y filtrado de compras.

## Glossary

- **System**: Módulo de Tipos de Compra dentro del sistema de Contabilidad
- **Purchase_Type**: Tipo de compra (ej: Corporativo, Fondo fijo, Crédito)
- **User**: Usuario administrador del sistema
- **UDN**: Unidad de Negocio
- **Active_Status**: Estado activo (1) o inactivo (0) de un tipo de compra
- **Modal**: Ventana emergente para captura o confirmación de datos
- **Database**: Base de datos MySQL con tabla `tipo_compra`

## Requirements

### Requirement 1

**User Story:** Como administrador del sistema, quiero visualizar todos los tipos de compra registrados en una tabla clara, para tener una vista general de los tipos disponibles y su estado.

#### Acceptance Criteria

1. WHEN THE User accede al módulo de Tipos de Compra, THE System SHALL mostrar una tabla con las columnas: "Tipo de compra", "Editar" y "Activar/Desactivar"
2. THE System SHALL mostrar el nombre de cada tipo de compra en la primera columna
3. THE System SHALL mostrar un ícono de edición (lápiz) en la columna "Editar" para cada registro
4. THE System SHALL mostrar un toggle switch en la columna "Activar/Desactivar" que refleje el estado actual del tipo de compra
5. THE System SHALL aplicar estilos visuales diferenciados para registros activos e inactivos

### Requirement 2

**User Story:** Como administrador del sistema, quiero agregar nuevos tipos de compra mediante un modal, para mantener actualizado el catálogo de tipos disponibles.

#### Acceptance Criteria

1. WHEN THE User hace clic en el botón "Agregar nuevo tipo de compra", THE System SHALL mostrar un modal con el título "NUEVO TIPO DE COMPRA"
2. THE System SHALL incluir en el modal un campo de texto con label "Nombre del tipo de compra" y placeholder "Ej: Corporativo, Fondo fijo, Crédito"
3. THE System SHALL incluir un botón "Guardar" en el modal
4. WHEN THE User ingresa un nombre y hace clic en "Guardar", THE System SHALL validar que el campo no esté vacío
5. THE System SHALL validar que no exista un tipo de compra con el mismo nombre
6. IF el nombre ya existe, THEN THE System SHALL mostrar un mensaje de error indicando que el tipo de compra ya está registrado
7. IF la validación es exitosa, THEN THE System SHALL crear el nuevo tipo de compra con estado activo (1)
8. THE System SHALL cerrar el modal y actualizar la tabla automáticamente
9. THE System SHALL mostrar un mensaje de éxito confirmando la creación

### Requirement 3

**User Story:** Como administrador del sistema, quiero editar el nombre de un tipo de compra existente, para corregir errores o actualizar la nomenclatura.

#### Acceptance Criteria

1. WHEN THE User hace clic en el ícono de edición de un tipo de compra, THE System SHALL mostrar un modal con el título "EDITAR TIPO DE COMPRA"
2. THE System SHALL prellenar el campo de texto con el nombre actual del tipo de compra
3. THE System SHALL incluir un botón "Guardar" en el modal
4. WHEN THE User modifica el nombre y hace clic en "Guardar", THE System SHALL validar que el campo no esté vacío
5. THE System SHALL validar que no exista otro tipo de compra con el mismo nombre
6. IF la validación es exitosa, THEN THE System SHALL actualizar el nombre del tipo de compra
7. THE System SHALL cerrar el modal y actualizar la tabla automáticamente
8. THE System SHALL mostrar un mensaje de éxito confirmando la actualización

### Requirement 4

**User Story:** Como administrador del sistema, quiero desactivar un tipo de compra, para que no esté disponible en los filtros y capturas de compras sin eliminarlo permanentemente.

#### Acceptance Criteria

1. WHEN THE User hace clic en el toggle switch de un tipo de compra activo, THE System SHALL mostrar un modal de confirmación con el título "DESACTIVAR TIPO DE COMPRA"
2. THE System SHALL mostrar un ícono de advertencia en el modal
3. THE System SHALL mostrar el mensaje "El tipo de compra ya no estará disponible para capturar o filtrar las compras de todas las unidades de negocio"
4. THE System SHALL incluir botones "Continuar" y "Cancelar" en el modal
5. WHEN THE User hace clic en "Continuar", THE System SHALL cambiar el estado del tipo de compra a inactivo (0)
6. THE System SHALL cerrar el modal y actualizar la tabla automáticamente
7. THE System SHALL aplicar estilos visuales diferenciados al registro desactivado
8. THE System SHALL mostrar un mensaje de éxito confirmando la desactivación

### Requirement 5

**User Story:** Como administrador del sistema, quiero reactivar un tipo de compra previamente desactivado, para que vuelva a estar disponible en el sistema.

#### Acceptance Criteria

1. WHEN THE User hace clic en el toggle switch de un tipo de compra inactivo, THE System SHALL mostrar un modal de confirmación con el título "ACTIVAR TIPO DE COMPRA"
2. THE System SHALL mostrar un ícono de advertencia en el modal
3. THE System SHALL mostrar el mensaje "El tipo de compra estará disponible para capturar o filtrar las compras de todas las unidades de negocio"
4. THE System SHALL incluir botones "Continuar" y "Cancelar" en el modal
5. WHEN THE User hace clic en "Continuar", THE System SHALL cambiar el estado del tipo de compra a activo (1)
6. THE System SHALL cerrar el modal y actualizar la tabla automáticamente
7. THE System SHALL aplicar estilos visuales diferenciados al registro activado
8. THE System SHALL mostrar un mensaje de éxito confirmando la activación

### Requirement 6

**User Story:** Como administrador del sistema, quiero que la interfaz del módulo sea consistente con el resto del sistema, para mantener una experiencia de usuario uniforme.

#### Acceptance Criteria

1. THE System SHALL usar el framework CoffeeSoft para todos los componentes visuales
2. THE System SHALL aplicar TailwindCSS para los estilos
3. THE System SHALL usar el tema "corporativo" para las tablas
4. THE System SHALL seguir la estructura de pestañas (tabs) del módulo de Administración
5. THE System SHALL incluir un header con título "📦 Tipos de Compra" y subtítulo descriptivo
6. THE System SHALL ser completamente responsive y adaptable a diferentes tamaños de pantalla
