# Requirements Document - Módulo de Compras

## Introduction

El módulo de Compras permite capturar, consultar y administrar las diferentes compras realizadas por la unidad de negocio, aplicando filtros dinámicos según clase de insumo o departamento, tipo de compra, proveedor y forma de pago. El sistema actualiza los totales en tiempo real y controla los niveles de acceso según perfil del usuario.

## Glossary

- **Sistema**: Aplicación web de gestión de compras integrada al ERP CoffeeSoft
- **Usuario**: Persona con acceso al sistema según su nivel de permisos
- **Compra**: Registro de adquisición de productos o servicios
- **Fondo Fijo**: Tipo de compra con presupuesto limitado y control de saldo
- **Compra Corporativa**: Adquisición pagada con métodos de pago empresariales
- **Compra a Crédito**: Adquisición con pago diferido a proveedores
- **UDN**: Unidad de Negocio
- **Clase de Insumo**: Categoría de producto (Alimentos, Bebidas, Gastos operativos, etc.)
- **Proveedor**: Entidad que suministra productos o servicios
- **Método de Pago**: Forma de pago (Efectivo, Tarjeta de crédito, Transferencia, etc.)

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder al módulo de compras con sus pestañas y componentes principales, para registrar, consultar y administrar las compras realizadas en la unidad de negocio.

#### Acceptance Criteria

1. WHEN el usuario accede al módulo, THE Sistema SHALL mostrar la interfaz principal con pestañas de navegación (Compras, Concentrado)
2. WHILE el usuario visualiza el módulo, THE Sistema SHALL mostrar los totales de compras generales, por tipo de compra y saldo del fondo fijo
3. WHEN el usuario modifica la fecha o agrega registros, THE Sistema SHALL actualizar en tiempo real la tabla de compras
4. THE Sistema SHALL incluir botones de acción "Subir archivos de compras" y "Registrar nueva compra"
5. THE Sistema SHALL mantener visible en todo momento la suma total de compras y el saldo actual del fondo fijo

### Requirement 2

**User Story:** Como usuario con acceso de captura, quiero registrar una nueva compra en el sistema, para mantener actualizada la información financiera y de insumos.

#### Acceptance Criteria

1. WHEN el usuario hace clic en "Registrar nueva compra", THE Sistema SHALL mostrar un formulario modal con los campos requeridos
2. THE Sistema SHALL incluir los campos: Categoría de producto, Producto, Tipo de compra, Proveedor al contado, Número de factura, Subtotal, Impuesto, Descripción
3. WHEN el usuario selecciona una clase de insumo, THE Sistema SHALL mostrar únicamente los productos relacionados a esa categoría
4. WHEN el usuario selecciona tipo "Corporativo", THE Sistema SHALL desplegar las formas de pago disponibles (Efectivo, Tarjeta de crédito, Transferencia)
5. WHEN el usuario selecciona tipo "Crédito", THE Sistema SHALL mostrar los proveedores asociados con crédito activo
6. THE Sistema SHALL ocultar los campos no aplicables hasta que se cumplan las condiciones de selección
7. WHEN el usuario guarda la compra, THE Sistema SHALL validar todos los campos requeridos y actualizar la tabla en tiempo real

### Requirement 3

**User Story:** Como usuario del sistema, quiero editar o eliminar compras registradas, para corregir errores o actualizar la información registrada.

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de editar, THE Sistema SHALL mostrar un modal con los datos de la compra precargados
2. THE Sistema SHALL permitir modificar todos los campos de la compra
3. WHILE el módulo está bloqueado por contabilidad, THE Sistema SHALL restringir la modificación de monto y tipo de compra cuando exista reembolso
4. WHEN el usuario hace clic en eliminar, THE Sistema SHALL mostrar un mensaje de confirmación antes de proceder
5. WHEN el usuario confirma la eliminación, THE Sistema SHALL eliminar el registro y actualizar la tabla en tiempo real
6. THE Sistema SHALL respetar las restricciones de reembolsos de fondo fijo al editar o eliminar

### Requirement 4

**User Story:** Como usuario del sistema, quiero filtrar las compras registradas según tipo y método de pago, para consultar fácilmente la información específica de cada tipo de compra.

#### Acceptance Criteria

1. THE Sistema SHALL incluir un filtro principal de tipo de compra con opciones: Fondo fijo, Corporativo, Crédito
2. WHEN el usuario selecciona "Corporativo", THE Sistema SHALL mostrar el filtro de método de pago
3. WHILE el tipo de compra no es "Corporativo", THE Sistema SHALL mantener oculto el filtro de método de pago
4. WHEN el usuario aplica filtros, THE Sistema SHALL actualizar dinámicamente la tabla mostrando solo las compras que coincidan
5. THE Sistema SHALL mantener los totales actualizados según los filtros aplicados

### Requirement 5

**User Story:** Como usuario con nivel de gerencia, quiero visualizar un concentrado de compras dentro de un rango de fechas, para analizar los gastos generales y balance del fondo fijo.

#### Acceptance Criteria

1. WHEN el usuario accede a la pestaña "Concentrado", THE Sistema SHALL mostrar una tabla comparativa por clase de producto y día
2. THE Sistema SHALL incluir columnas de subtotales, impuestos y totales diarios
3. THE Sistema SHALL permitir filtrar por rango de fechas y tipo de compra
4. THE Sistema SHALL mostrar saldo inicial, salidas del fondo fijo y saldo final del período
5. WHEN el usuario hace clic en "Exportar", THE Sistema SHALL generar un archivo Excel con los resultados mostrados

### Requirement 6

**User Story:** Como administrador del sistema, quiero gestionar los niveles de acceso del módulo de compras, para asegurar que cada usuario opere según su rol y permisos definidos.

#### Acceptance Criteria

1. THE Sistema SHALL configurar cuatro niveles de acceso: Captura, Gerencia, Dirección, Contabilidad
2. WHILE el usuario tiene nivel "Captura", THE Sistema SHALL limitar las funciones de edición y eliminación
3. WHILE el usuario tiene nivel "Contabilidad", THE Sistema SHALL permitir bloquear o desbloquear el módulo
4. WHEN el módulo está bloqueado, THE Sistema SHALL restringir la modificación de monto y tipo de compra cuando haya reembolso
5. THE Sistema SHALL validar permisos antes de ejecutar cualquier operación de escritura
