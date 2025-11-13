# Requirements Document - Módulo de Archivos

## Introduction

El módulo de Archivos permite la consulta, descarga y eliminación de archivos subidos desde los módulos de Ventas, Compras, Almacén y Tesorería. El sistema organiza los documentos por fechas y módulos, garantizando integridad, disponibilidad y trazabilidad para usuarios con diferentes niveles de acceso.

## Glossary

- **System**: El módulo de gestión de archivos del ERP CoffeeSoft
- **User**: Persona autenticada que accede al módulo de archivos
- **File**: Documento digital almacenado en el sistema (PDF, imágenes, etc.)
- **Module**: Categoría de origen del archivo (Ventas, Compras, Almacén, Tesorería)
- **Access_Level**: Nivel de permisos del usuario (Captura, Gerencia, Contabilidad/Dirección)
- **UDN**: Unidad de Negocio (Business Unit)
- **Date_Range**: Rango de fechas para filtrar archivos
- **File_Action**: Operación sobre un archivo (descargar, ver, eliminar)

## Requirements

### Requirement 1

**User Story:** As a usuario del sistema, I want acceder a la interfaz general del módulo de archivos con pestañas organizadas, so that puedo visualizar el total de archivos por módulo y consultar sus detalles fácilmente

#### Acceptance Criteria

1. WHEN THE System carga el módulo de archivos, THE System SHALL mostrar una interfaz con pestañas para Ventas, Clientes, Compras, Salidas de almacén, Costos, Pagos a proveedor y Archivos
2. WHEN THE User accede a la pestaña Archivos, THE System SHALL mostrar tarjetas con totales de archivos por categoría (Ventas, Compras, Proveedores, Almacén)
3. WHEN THE User visualiza la interfaz, THE System SHALL incluir un selector de rango de fechas con formato DD/MM/YYYY
4. WHEN THE User selecciona un filtro de módulo, THE System SHALL actualizar la tabla mostrando solo archivos del módulo seleccionado
5. WHEN THE System muestra la tabla de archivos, THE System SHALL incluir columnas: Fecha subida, Módulo, Subido por, Nombre del archivo, Tipo/Tamaño
6. WHEN THE System renderiza cada fila de archivo, THE System SHALL incluir botones de acción: Ver (ícono ojo), Descargar (ícono descarga), Eliminar (ícono papelera)

### Requirement 2

**User Story:** As a administrador del sistema, I want definir y controlar los niveles de acceso de los usuarios, so that cada rol realice solo las operaciones permitidas según su perfil

#### Acceptance Criteria

1. WHEN THE User con nivel Captura accede al módulo, THE System SHALL permitir consultar, descargar y eliminar archivos de una fecha específica
2. WHEN THE User con nivel Gerencia accede al módulo, THE System SHALL permitir consultar archivos dentro de un rango de fechas y descargar concentrados diarios
3. WHEN THE User con nivel Contabilidad o Dirección accede al módulo, THE System SHALL permitir todas las funciones anteriores y filtrar por unidad de negocio
4. WHEN THE System valida permisos del User, THE System SHALL aplicar restricciones dinámicamente según el rol autenticado
5. WHEN THE System renderiza botones de acción, THE System SHALL mostrar u ocultar opciones según el Access_Level del User
6. IF THE User intenta realizar una acción no permitida, THEN THE System SHALL mostrar mensaje de error "No tiene permisos para esta operación"

### Requirement 3

**User Story:** As a usuario de nivel captura, I want poder descargar o eliminar archivos de los módulos correspondientes, so that mantengo actualizada la información sin afectar la integridad del sistema

#### Acceptance Criteria

1. WHEN THE User hace clic en el botón eliminar, THE System SHALL mostrar un modal de confirmación con el mensaje "¿Está seguro de querer eliminar el archivo?"
2. WHEN THE User confirma la eliminación, THE System SHALL registrar en logs: fecha, usuario y nombre del archivo eliminado
3. WHEN THE User descarga un archivo, THE System SHALL validar sesión activa y generar enlace seguro con token temporal
4. WHEN THE System completa una File_Action exitosamente, THE System SHALL mostrar mensaje de éxito con ícono verde
5. IF THE System falla al ejecutar una File_Action, THEN THE System SHALL mostrar mensaje de error descriptivo
6. WHEN THE User elimina un archivo, THE System SHALL actualizar automáticamente los contadores de totales por módulo

### Requirement 4

**User Story:** As a usuario del sistema, I want filtrar archivos por módulo y rango de fechas, so that encuentro rápidamente los documentos que necesito

#### Acceptance Criteria

1. WHEN THE User selecciona un módulo del dropdown, THE System SHALL filtrar la tabla mostrando solo archivos del módulo seleccionado
2. WHEN THE User selecciona un Date_Range, THE System SHALL actualizar la tabla con archivos dentro del rango especificado
3. WHEN THE System aplica filtros, THE System SHALL mantener visible el total de archivos encontrados
4. WHEN THE User limpia los filtros, THE System SHALL restaurar la vista completa de todos los archivos
5. WHEN THE System carga archivos filtrados, THE System SHALL ordenar por fecha de subida descendente por defecto

### Requirement 5

**User Story:** As a usuario con acceso a múltiples UDN, I want filtrar archivos por unidad de negocio, so that consulto solo documentos relevantes a mi área

#### Acceptance Criteria

1. WHEN THE User con nivel Contabilidad o Dirección accede al módulo, THE System SHALL mostrar selector de UDN
2. WHEN THE User selecciona una UDN, THE System SHALL filtrar archivos asociados a esa unidad de negocio
3. WHEN THE System aplica filtro de UDN, THE System SHALL actualizar contadores de totales por módulo
4. IF THE User no tiene acceso a múltiples UDN, THEN THE System SHALL ocultar el selector de UDN
5. WHEN THE System carga archivos por UDN, THE System SHALL validar permisos del User sobre cada unidad

### Requirement 6

**User Story:** As a usuario del sistema, I want visualizar información detallada de cada archivo, so that identifico rápidamente el documento que necesito

#### Acceptance Criteria

1. WHEN THE System muestra un archivo en la tabla, THE System SHALL incluir fecha de subida en formato DD/MM/YYYY
2. WHEN THE System muestra un archivo en la tabla, THE System SHALL incluir nombre del módulo de origen con badge de color
3. WHEN THE System muestra un archivo en la tabla, THE System SHALL incluir nombre del usuario que subió el archivo
4. WHEN THE System muestra un archivo en la tabla, THE System SHALL incluir nombre completo del archivo
5. WHEN THE System muestra un archivo en la tabla, THE System SHALL incluir tipo de archivo (extensión) y tamaño en KB o MB
6. WHEN THE User hace clic en el botón ver, THE System SHALL abrir el archivo en una nueva pestaña o modal de previsualización
