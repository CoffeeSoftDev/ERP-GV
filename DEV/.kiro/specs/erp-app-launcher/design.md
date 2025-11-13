# Design Document

## Overview

El módulo de Lanzador de Aplicaciones ERP es una interfaz moderna y minimalista que centraliza el acceso a todos los módulos del sistema. Utiliza un diseño basado en tarjetas (cards) con iconos representativos, descripciones breves y badges de estado. La interfaz es completamente responsive y se adapta a diferentes tamaños de pantalla.

## Architecture

### Frontend Architecture

```
┌─────────────────────────────────────────┐
│         index.php (Entry Point)         │
│              <div id="root">            │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│        launcher.js (Main App)           │
│  ┌───────────────────────────────────┐  │
│  │  Class App extends Templates      │  │
│  │  - render()                       │  │
│  │  - layout()                       │  │
│  │  - loadModules()                  │  │
│  │  - filterModules()                │  │
│  │  - renderModuleCards()            │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│     ctrl-launcher.php (Controller)      │
│  - init()                               │
│  - getModules()                         │
│  - logAccess()                          │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│      mdl-launcher.php (Model)           │
│  - listModules()                        │
│  - getModulesByUser()                   │
│  - createAccessLog()                    │
└─────────────────────────────────────────┘
```

### Backend Architecture

El backend sigue el patrón MVC de CoffeeSoft:

- **Controlador (ctrl-launcher.php)**: Maneja las peticiones del frontend, valida permisos y coordina la lógica de negocio
- **Modelo (mdl-launcher.php)**: Gestiona el acceso a datos, consultas a la base de datos y operaciones CRUD
- **Vista (index.php)**: Punto de entrada HTML que carga los recursos necesarios

## Components and Interfaces

### 1. App Launcher Component

**Responsabilidad**: Componente principal que orquesta toda la interfaz del lanzador

**Métodos**:
- `render()`: Inicializa la interfaz completa
- `layout()`: Construye la estructura HTML base
- `loadModules()`: Obtiene los módulos disponibles desde el backend
- `filterModules(searchTerm)`: Filtra módulos según término de búsqueda
- `renderModuleCards(modules)`: Renderiza las tarjetas de módulos
- `navigateToModule(moduleUrl)`: Navega al módulo seleccionado

**Propiedades**:
```javascript
{
  PROJECT_NAME: "launcher",
  modules: [],
  filteredModules: [],
  systemStatus: "online"
}
```

### 2. Module Card Component

**Estructura HTML**:
```html
<div class="module-card bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition cursor-pointer">
  <div class="icon-container w-16 h-16 mb-4">
    <i class="[icon-class] text-4xl text-[color]"></i>
  </div>
  <h3 class="text-xl font-semibold mb-2">[Module Name]</h3>
  <p class="text-gray-600 text-sm mb-3">[Description]</p>
  <span class="badge px-2 py-1 rounded text-xs">[Status]</span>
</div>
```

**Estados**:
- **Nuevo**: Badge verde con texto "Nuevo"
- **Legacy**: Badge gris con texto "Legacy"
- **Sin badge**: Módulos estándar

### 3. Search Bar Component

**Estructura**:
```html
<div class="search-container mb-6">
  <input 
    type="text" 
    id="searchModules" 
    placeholder="Buscar aplicaciones..."
    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500"
  />
</div>
```

**Funcionalidad**:
- Búsqueda en tiempo real (keyup event)
- Filtrado case-insensitive
- Actualización automática del contador de módulos

### 4. System Status Component

**Estructura**:
```html
<div class="system-status flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
  <div class="flex items-center gap-2">
    <span class="status-dot w-3 h-3 rounded-full bg-green-500"></span>
    <span class="text-sm font-medium">Online</span>
  </div>
  <span class="text-sm text-gray-600">Estado del Sistema</span>
</div>
```

## Data Models

### Module Model

```javascript
{
  id: number,
  name: string,
  description: string,
  icon: string,          // Font Awesome class
  iconColor: string,     // Tailwind color class
  url: string,
  status: string,        // "nuevo" | "legacy" | null
  order: number,
  active: boolean
}
```

### User Session Model

```javascript
{
  userId: number,
  username: string,
  role: string,
  permissions: string[],
  udn: number
}
```

### Access Log Model

```javascript
{
  id: number,
  userId: number,
  moduleId: number,
  accessDate: datetime,
  ipAddress: string
}
```

## Database Schema

### Table: erp_modules

```sql
CREATE TABLE erp_modules (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255),
  icon VARCHAR(50),
  icon_color VARCHAR(50),
  url VARCHAR(255) NOT NULL,
  status VARCHAR(20),
  order_index INT DEFAULT 0,
  active TINYINT(1) DEFAULT 1,
  date_created DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Table: erp_module_permissions

```sql
CREATE TABLE erp_module_permissions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  module_id INT NOT NULL,
  user_id INT NOT NULL,
  can_access TINYINT(1) DEFAULT 1,
  FOREIGN KEY (module_id) REFERENCES erp_modules(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Table: erp_access_logs

```sql
CREATE TABLE erp_access_logs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  module_id INT NOT NULL,
  access_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (module_id) REFERENCES erp_modules(id)
);
```

## Error Handling

### Frontend Error Handling

1. **No modules available**:
   - Mostrar mensaje: "No hay módulos disponibles"
   - Sugerir contactar al administrador

2. **Network errors**:
   - Mostrar alerta con `alert({ icon: "error", text: "Error de conexión" })`
   - Reintentar automáticamente después de 3 segundos

3. **Search no results**:
   - Mostrar mensaje: "No se encontraron módulos que coincidan con tu búsqueda"
   - Mantener el input visible para nueva búsqueda

### Backend Error Handling

1. **Database connection errors**:
   - Retornar `{ status: 500, message: "Error de conexión a base de datos" }`
   - Registrar error en logs del servidor

2. **Permission denied**:
   - Retornar `{ status: 403, message: "No tienes permisos para acceder a este módulo" }`
   - Registrar intento de acceso no autorizado

3. **Invalid session**:
   - Retornar `{ status: 401, message: "Sesión inválida o expirada" }`
   - Redirigir al login

## Testing Strategy

### Unit Tests

1. **Frontend Tests**:
   - Test de filtrado de módulos
   - Test de renderizado de tarjetas
   - Test de navegación a módulos
   - Test de búsqueda en tiempo real

2. **Backend Tests**:
   - Test de consulta de módulos por usuario
   - Test de validación de permisos
   - Test de registro de accesos
   - Test de manejo de sesiones

### Integration Tests

1. **Flujo completo de acceso**:
   - Login → Lanzador → Selección de módulo → Navegación
   
2. **Flujo de búsqueda**:
   - Búsqueda → Filtrado → Selección → Navegación

3. **Flujo de permisos**:
   - Verificar que solo se muestran módulos permitidos
   - Verificar que se bloquea acceso a módulos no autorizados

### Manual Testing

1. **Responsive Design**:
   - Probar en desktop (1920x1080)
   - Probar en tablet (768x1024)
   - Probar en mobile (375x667)

2. **Browser Compatibility**:
   - Chrome (última versión)
   - Firefox (última versión)
   - Edge (última versión)

3. **User Experience**:
   - Velocidad de carga
   - Fluidez de animaciones
   - Claridad de información

## Design Patterns

### 1. Module Pattern (JavaScript)

Cada clase encapsula su lógica y expone solo métodos públicos necesarios.

### 2. MVC Pattern (Backend)

Separación clara entre Modelo, Vista y Controlador siguiendo estándares de CoffeeSoft.

### 3. Observer Pattern (Search)

El input de búsqueda observa cambios y notifica al componente para actualizar la vista.

### 4. Factory Pattern (Module Cards)

Generación dinámica de tarjetas de módulos basada en configuración.

## Security Considerations

1. **Authentication**: Verificar sesión activa antes de mostrar módulos
2. **Authorization**: Validar permisos de usuario para cada módulo
3. **Input Validation**: Sanitizar términos de búsqueda
4. **SQL Injection Prevention**: Usar prepared statements en todas las consultas
5. **XSS Prevention**: Escapar output HTML en descripciones de módulos
6. **CSRF Protection**: Validar tokens en peticiones POST
7. **Audit Logging**: Registrar todos los accesos a módulos

## Performance Optimization

1. **Lazy Loading**: Cargar iconos y recursos solo cuando sean visibles
2. **Caching**: Cachear lista de módulos en sesión del usuario
3. **Debouncing**: Aplicar debounce a la búsqueda (300ms)
4. **Minification**: Minificar CSS y JS en producción
5. **CDN**: Servir recursos estáticos desde CDN
6. **Database Indexing**: Índices en columnas de búsqueda frecuente

## UI/UX Design

### Color Palette

- **Primary**: `#103B60` (Azul corporativo CoffeeSoft)
- **Secondary**: `#8CC63F` (Verde acción)
- **Background**: `#F3F4F6` (Gris claro)
- **Cards**: `#FFFFFF` (Blanco)
- **Text Primary**: `#1F2937` (Gris oscuro)
- **Text Secondary**: `#6B7280` (Gris medio)

### Typography

- **Headings**: Font-weight 600-700, tamaños 24px-32px
- **Body**: Font-weight 400, tamaño 14px-16px
- **Badges**: Font-weight 500, tamaño 12px

### Spacing

- **Grid Gap**: 24px entre tarjetas
- **Card Padding**: 24px interno
- **Section Margins**: 32px entre secciones

### Animations

- **Hover**: Transform scale(1.02) + shadow-xl (200ms ease)
- **Click**: Transform scale(0.98) (100ms ease)
- **Search**: Fade in/out de resultados (300ms ease)
