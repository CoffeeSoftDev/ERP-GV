# Requirements Document

## Introduction

El módulo de **Lanzador de Aplicaciones ERP** es una interfaz de acceso centralizada que reemplaza la navegación tradicional por menú lateral. Permite a los usuarios acceder a los diferentes módulos del sistema ERP (KPI, Producción, Contabilidad, CostSys) mediante tarjetas visuales interactivas que se muestran inmediatamente después del inicio de sesión.

## Glossary

- **ERP System**: Sistema de Planificación de Recursos Empresariales (Enterprise Resource Planning)
- **App Launcher**: Interfaz de lanzamiento de aplicaciones que muestra módulos disponibles
- **Module Card**: Tarjeta visual que representa un módulo del sistema
- **User Session**: Sesión activa del usuario autenticado
- **Module Access**: Permiso para acceder a un módulo específico del sistema

## Requirements

### Requirement 1

**User Story:** Como usuario del ERP, quiero ver un lanzador de aplicaciones después de iniciar sesión, para acceder rápidamente a los módulos disponibles.

#### Acceptance Criteria

1. WHEN the User Session is authenticated, THE App Launcher SHALL display the available modules
2. THE App Launcher SHALL render Module Cards in a grid layout with responsive design
3. THE App Launcher SHALL include a search bar to filter modules by name
4. THE App Launcher SHALL display module status indicators (Nuevo, Legacy, Online)
5. THE App Launcher SHALL show the total count of available modules

### Requirement 2

**User Story:** Como usuario del ERP, quiero hacer clic en una tarjeta de módulo, para navegar directamente a la funcionalidad correspondiente.

#### Acceptance Criteria

1. WHEN the User clicks on a Module Card, THE ERP System SHALL navigate to the corresponding module URL
2. THE Module Card SHALL display visual feedback on hover state
3. THE Module Card SHALL include an icon, title, description, and status badge
4. IF the module is marked as "Nuevo", THEN THE Module Card SHALL display a green badge
5. IF the module is marked as "Legacy", THEN THE Module Card SHALL display a gray badge

### Requirement 3

**User Story:** Como usuario del ERP, quiero buscar módulos por nombre, para encontrar rápidamente la aplicación que necesito.

#### Acceptance Criteria

1. WHEN the User types in the search bar, THE App Launcher SHALL filter Module Cards in real-time
2. THE App Launcher SHALL match search terms against module names and descriptions
3. THE App Launcher SHALL display "No se encontraron módulos" message when no results match
4. THE App Launcher SHALL update the module count based on filtered results
5. THE search functionality SHALL be case-insensitive

### Requirement 4

**User Story:** Como administrador del sistema, quiero configurar qué módulos están disponibles para cada usuario, para controlar el acceso según roles y permisos.

#### Acceptance Criteria

1. THE ERP System SHALL retrieve module list from backend based on User Session permissions
2. THE App Launcher SHALL only display modules where Module Access is granted
3. THE backend SHALL return module configuration including: id, name, description, icon, url, status
4. THE App Launcher SHALL handle empty module lists gracefully
5. THE ERP System SHALL log module access attempts for audit purposes

### Requirement 5

**User Story:** Como usuario del ERP, quiero ver el estado del sistema en el lanzador, para saber si todos los servicios están operativos.

#### Acceptance Criteria

1. THE App Launcher SHALL display system status indicator (Online/Offline)
2. THE App Launcher SHALL show version number of the ERP System
3. THE App Launcher SHALL display the count of available modules
4. THE status indicator SHALL update in real-time if system state changes
5. THE App Launcher SHALL use green color for "Online" status and red for "Offline"
