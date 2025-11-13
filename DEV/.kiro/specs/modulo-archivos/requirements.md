# Requirements Document

## Introduction

El módulo de Archivos es un sistema de gestión documental que permite administrar, visualizar y controlar archivos relacionados con los distintos módulos del sistema contable (Ventas, Compras, Salidas de almacén, Pagos a proveedor, etc.). Proporciona una interfaz centralizada para la carga, eliminación y filtrado de archivos por tipo de módulo.

## Glossary

- **System**: El sistema contable CoffeeSoft
- **User**: Usuario autenticado del sistema con permisos de gestión de archivos
- **File**: Documento digital almacenado en el sistema
- **Module**: Categoría funcional del sistema (Ventas, Compras, Clientes, etc.)
- **Dashboard**: Interfaz principal del módulo de archivos
- **Filter**: Mecanismo de búsqueda y visualización selectiva de archivos
- **Upload**: Proceso de carga de archivos al sistema
- **Delete**: Proceso de eliminación de archivos del sistema

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a la interfaz principal del módulo de archivos con pestañas organizadas por módulo, para visualizar rápidamente los archivos disponibles en cada sección del sistema.

#### Acceptance Criteria

1. WHEN THE User accesses the files module, THE System SHALL display a dashboard with organized tabs for each module category
2. THE System SHALL display tabs for Ventas, Clientes, Compras, Salidas de almacén, Pagos a proveedor, and Archivos
3. THE System SHALL show the total count of files for each category in the dashboard cards
4. THE System SHALL display a general table with columns: Módulo, Subido por, Nombre del archivo, Tipo/Tamaño, and Acciones
5. THE System SHALL include action buttons for view, download, and delete operations in each table row
6. THE System SHALL include a dropdown filter labeled "Mostrar todas los archivos" with options for each module

### Requirement 2

**User Story:** Como usuario del sistema, quiero poder eliminar un archivo mediante una ventana de confirmación, para evitar eliminar archivos de forma accidental.

#### Acceptance Criteria

1. THE System SHALL display a delete icon in the actions column of the files table
2. WHEN THE User clicks the delete icon, THE System SHALL open a confirmation modal
3. THE System SHALL display the message "¿Está seguro de querer eliminar el archivo?" in the modal
4. THE System SHALL provide "Continuar" and "Cancelar" buttons in the confirmation modal
5. WHEN THE User clicks "Continuar", THE System SHALL execute the deleteArchivo function and remove the file from the database
6. WHEN THE User clicks "Continuar", THE System SHALL refresh the files table without reloading the entire page
7. WHEN THE User clicks "Cancelar", THE System SHALL close the modal without performing any deletion
8. THE System SHALL display a success message after successful file deletion

### Requirement 3

**User Story:** Como usuario del sistema, quiero filtrar los archivos según el módulo seleccionado, para facilitar la búsqueda y gestión de archivos específicos.

#### Acceptance Criteria

1. THE System SHALL provide a dropdown menu labeled "Mostrar todas los archivos" in the filter bar
2. THE System SHALL populate the dropdown with options for each module category
3. WHEN THE User selects a module from the dropdown, THE System SHALL filter the table to display only files from that module
4. THE System SHALL update the file counters in the dashboard cards based on the selected filter
5. THE System SHALL maintain the selected filter until the user changes it
6. THE System SHALL display all files when "Mostrar todas los archivos" option is selected
7. THE System SHALL update the table dynamically without full page reload when filter changes

### Requirement 4

**User Story:** Como usuario del sistema, quiero visualizar información detallada de cada archivo en la tabla, para identificar rápidamente el origen y características de los documentos.

#### Acceptance Criteria

1. THE System SHALL display the module name in the "Módulo" column for each file
2. THE System SHALL display the uploader's name in the "Subido por" column for each file
3. THE System SHALL display the file name in the "Nombre del archivo" column
4. THE System SHALL display the file type and size in the "Tipo/Tamaño" column formatted as "PDF / XXX KB"
5. THE System SHALL display action icons (view, edit, delete) in the "Acciones" column
6. THE System SHALL use consistent styling following CoffeeSoft theme guidelines
7. THE System SHALL display the current capture date in the header section

### Requirement 5

**User Story:** Como usuario del sistema, quiero ver contadores de archivos por categoría en el dashboard, para tener una visión general rápida de la distribución de documentos.

#### Acceptance Criteria

1. THE System SHALL display a card for "Archivos totales" showing the total count of all files
2. THE System SHALL display a card for "Archivos de ventas" showing the count of sales-related files
3. THE System SHALL display a card for "Archivos de compras" showing the count of purchase-related files
4. THE System SHALL display a card for "Archivos de proveedores" showing the count of supplier-related files
5. THE System SHALL display a card for "Archivos de almacén" showing the count of warehouse-related files
6. THE System SHALL update all counters dynamically when files are added or deleted
7. THE System SHALL use icons and color coding consistent with CoffeeSoft design system
