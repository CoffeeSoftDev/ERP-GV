# Requirements Document

## Introduction

El módulo de Administrador de Productos permite gestionar el catálogo de productos y servicios asociados a cada unidad de negocio (UDN) dentro del sistema de pedidos. Los administradores podrán crear, modificar, consultar y dar de baja productos de manera estructurada, manteniendo la integridad de los datos y la trazabilidad de las operaciones.

## Glossary

- **System**: Módulo de Administrador de Productos
- **Administrator**: Usuario con permisos para gestionar productos
- **Product**: Artículo o servicio registrado en el catálogo
- **UDN**: Unidad de Negocio (Business Unit)
- **Active Status**: Estado que indica si un producto está disponible (1) o dado de baja (0)
- **Service Flag**: Indicador booleano que determina si el producto es un servicio
- **Product Table**: Tabla principal que muestra el listado de productos
- **Filter Bar**: Barra de controles para filtrar productos por UDN y estado
- **Modal Form**: Ventana emergente para agregar o editar productos

## Requirements

### Requirement 1

**User Story:** As an Administrator, I want to access a dedicated "Product Administrator" tab within the orders module, so that I can manage products in a structured way.

#### Acceptance Criteria

1. WHEN the Administrator accesses the orders module, THE System SHALL display a tab labeled "Administrador de Productos"
2. WHEN the Administrator clicks on the "Administrador de Productos" tab, THE System SHALL render the product management interface within 2 seconds
3. THE System SHALL display the product management interface with a Filter Bar and a Product Table
4. THE System SHALL maintain the tab state when navigating between different sections of the orders module

### Requirement 2

**User Story:** As an Administrator, I want to view a table with all products and their details, so that I can quickly review the product catalog.

#### Acceptance Criteria

1. THE System SHALL display a Product Table with the following columns: ID, Nombre, Descripción, Es Servicio, Unidad de Negocio, Estado
2. WHEN displaying the UDN column, THE System SHALL show the UDN name instead of the udn_id numeric value
3. WHEN displaying the Es Servicio column, THE System SHALL show "Sí" for true values and "No" for false values
4. WHEN displaying the Estado column, THE System SHALL show a visual badge indicating "Activo" or "Inactivo"
5. THE System SHALL display action buttons (Editar, Eliminar) for each product row
6. THE System SHALL support pagination with a default of 15 rows per page
7. THE System SHALL display a total count of products matching the current filters

### Requirement 3

**User Story:** As an Administrator, I want to filter products by UDN and status, so that I can focus on specific product segments.

#### Acceptance Criteria

1. THE System SHALL display a Filter Bar with two dropdown controls: UDN selector and Status selector
2. WHEN the Administrator changes the UDN filter, THE System SHALL refresh the Product Table within 1 second showing only products matching the selected UDN
3. WHEN the Administrator changes the Status filter, THE System SHALL refresh the Product Table within 1 second showing only products with the selected active status
4. THE System SHALL populate the UDN dropdown with all available business units from the udn table
5. THE System SHALL provide status options: "Disponibles" (active=1) and "No disponibles" (active=0)
6. THE System SHALL maintain filter selections when performing CRUD operations

### Requirement 4

**User Story:** As an Administrator, I want to add new products to the catalog, so that I can expand the available offerings.

#### Acceptance Criteria

1. THE System SHALL display a "Nuevo Producto" button in the Filter Bar
2. WHEN the Administrator clicks "Nuevo Producto", THE System SHALL open a Modal Form within 500 milliseconds
3. THE System SHALL display input fields in the Modal Form for: nombre, descripcion, es_servicio, udn_id, active
4. THE System SHALL mark nombre, udn_id, and active as required fields
5. WHEN the Administrator submits the form with valid data, THE System SHALL insert a new record into the producto table
6. WHEN the Administrator submits the form with invalid data, THE System SHALL display validation error messages without closing the modal
7. WHEN the product is successfully created, THE System SHALL display a success message and refresh the Product Table
8. WHEN the product creation fails, THE System SHALL display an error message with details

### Requirement 5

**User Story:** As an Administrator, I want to edit existing products, so that I can update product information as needed.

#### Acceptance Criteria

1. THE System SHALL display an "Editar" button for each product row in the Product Table
2. WHEN the Administrator clicks "Editar", THE System SHALL retrieve the product data by ID and open a Modal Form within 1 second
3. THE System SHALL pre-fill the Modal Form with the current product data
4. THE System SHALL allow modification of all product fields except the ID
5. WHEN the Administrator submits the form with valid changes, THE System SHALL update the producto table record
6. WHEN the update is successful, THE System SHALL display a success message and refresh the Product Table
7. WHEN the update fails, THE System SHALL display an error message without closing the modal

### Requirement 6

**User Story:** As an Administrator, I want to deactivate products instead of permanently deleting them, so that I can maintain data integrity and history.

#### Acceptance Criteria

1. THE System SHALL display an "Eliminar" button for each product row in the Product Table
2. WHEN the Administrator clicks "Eliminar", THE System SHALL display a confirmation dialog with the product name
3. WHEN the Administrator confirms deletion, THE System SHALL update the active field to 0 instead of deleting the record
4. THE System SHALL NOT physically delete records from the producto table
5. WHEN the deactivation is successful, THE System SHALL display a success message and refresh the Product Table
6. WHEN the deactivation fails, THE System SHALL display an error message
7. THE System SHALL allow reactivation of deactivated products through the edit functionality

### Requirement 7

**User Story:** As an Administrator, I want real-time feedback on my actions, so that I know whether operations succeeded or failed.

#### Acceptance Criteria

1. WHEN any CRUD operation completes successfully, THE System SHALL display a success notification within 500 milliseconds
2. WHEN any CRUD operation fails, THE System SHALL display an error notification with a descriptive message within 500 milliseconds
3. THE System SHALL automatically dismiss success notifications after 3 seconds
4. THE System SHALL require manual dismissal of error notifications
5. THE System SHALL refresh the Product Table automatically after successful create, update, or delete operations
6. THE System SHALL maintain the current page and filter state after table refresh operations

### Requirement 8

**User Story:** As an Administrator, I want the interface to follow CoffeeSoft design standards, so that it integrates seamlessly with the rest of the system.

#### Acceptance Criteria

1. THE System SHALL use TailwindCSS for all styling
2. THE System SHALL extend the Templates class from CoffeeSoft framework
3. THE System SHALL use CoffeeSoft components: createTable, createModalForm, createfilterBar, swalQuestion
4. THE System SHALL follow the corporativo theme for tables
5. THE System SHALL use the standard CoffeeSoft color palette: #103B60 (primary), #8CC63F (success), #EAEAEA (neutral)
6. THE System SHALL be fully responsive and functional on desktop and tablet devices
7. THE System SHALL follow the naming convention: admin-productos.js, ctrl-admin-productos.php, mdl-admin-productos.php
