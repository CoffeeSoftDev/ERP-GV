# Design Document

## Overview

El módulo de Archivos es un sistema de gestión documental integrado en el sistema contable CoffeeSoft. Proporciona una interfaz centralizada para administrar archivos relacionados con diferentes módulos del sistema (Ventas, Compras, Clientes, Salidas de almacén, Pagos a proveedor).

El diseño sigue la arquitectura MVC del framework CoffeeSoft, utilizando componentes reutilizables de la librería coffeSoft.js y siguiendo los patrones establecidos en el pivote de administración.

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer (JS)                      │
│  ┌──────────────────┐  ┌──────────────────┐                │
│  │  DashboardFiles  │  │   AdminFiles     │                │
│  │   (extends App)  │  │  (extends App)   │                │
│  └──────────────────┘  └──────────────────┘                │
│           │                      │                           │
│           └──────────┬───────────┘                           │
│                      │                                       │
│              ┌───────▼────────┐                             │
│              │   Templates    │                             │
│              │   Components   │                             │
│              └────────────────┘                             │
└─────────────────────────────────────────────────────────────┘
                       │
                       │ AJAX (useFetch)
                       │
┌─────────────────────▼─────────────────────────────────────┐
│                  Controller Layer (PHP)                    │
│  ┌──────────────────────────────────────────────────────┐ │
│  │              ctrl-archivos.php                       │ │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌──────────────┐ │ │
│  │  │ init() │ │  ls()  │ │ get()  │ │ deleteFile() │ │ │
│  │  └────────┘ └────────┘ └────────┘ └──────────────┘ │ │
│  └──────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                       │
                       │ SQL Queries
                       │
┌─────────────────────▼─────────────────────────────────────┐
│                   Model Layer (PHP)                        │
│  ┌──────────────────────────────────────────────────────┐ │
│  │              mdl-archivos.php                        │ │
│  │  ┌──────────────┐ ┌──────────────┐ ┌─────────────┐ │ │
│  │  │ listFiles()  │ │ getFileById()│ │ deleteFile()│ │ │
│  │  └──────────────┘ └──────────────┘ └─────────────┘ │ │
│  │  ┌──────────────┐ ┌──────────────┐                 │ │
│  │  │ lsModules()  │ │ getFileCounts()                │ │
│  │  └──────────────┘ └──────────────┘                 │ │
│  └──────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                       │
                       │
┌─────────────────────▼─────────────────────────────────────┐
│                   Database Layer (MySQL)                   │
│  ┌──────────────────────────────────────────────────────┐ │
│  │                    files table                       │ │
│  │  ┌────┐ ┌────────┐ ┌─────┐ ┌───────────┐           │ │
│  │  │ id │ │udn_id  │ │ src │ │file_name  │           │ │
│  │  └────┘ └────────┘ └─────┘ └───────────┘           │ │
│  │  ┌─────────────┐ ┌──────────────────┐              │ │
│  │  │description  │ │date_created_at   │              │ │
│  │  └─────────────┘ └──────────────────┘              │ │
│  └──────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### Component Hierarchy

```
App (extends Templates)
├── DashboardFiles
│   ├── layout()
│   ├── renderDashboard()
│   ├── showCards()
│   └── filterBarDashboard()
│
└── AdminFiles
    ├── layout()
    ├── filterBarFiles()
    ├── lsFiles()
    ├── deleteFile(id)
    └── viewFile(id)
```

## Components and Interfaces

### Frontend Components (archivos.js)

#### Class: App
**Extends:** Templates  
**Purpose:** Clase principal que gestiona la estructura del módulo

**Properties:**
- `PROJECT_NAME`: "archivos"
- `_link`: URL del controlador (ctrl-archivos.php)
- `_div_modulo`: Contenedor raíz ("root")

**Methods:**
- `render()`: Inicializa el módulo y renderiza componentes
- `layout()`: Crea la estructura principal con tabs

#### Class: DashboardFiles
**Extends:** App  
**Purpose:** Gestiona el dashboard con métricas y contadores

**Methods:**

```javascript
render()
// Renderiza el dashboard completo

layout()
// Crea la estructura del dashboard con:
// - Header con título y fecha
// - Cards de contadores por módulo
// - Filtro de búsqueda

filterBarDashboard()
// Crea barra de filtros con:
// - Select de módulos
// - Input de búsqueda por nombre

showCards(data)
// Muestra tarjetas con contadores:
// - Archivos totales
// - Archivos de ventas
// - Archivos de compras
// - Archivos de proveedores
// - Archivos de almacén
```

#### Class: AdminFiles
**Extends:** App  
**Purpose:** Gestiona la tabla de archivos y operaciones CRUD

**Methods:**

```javascript
layout()
// Crea estructura con:
// - FilterBar
// - Container para tabla

filterBarFiles()
// Crea filtros con:
// - Select de módulos
// - Botón de actualizar

lsFiles()
// Lista archivos en tabla con:
// - Módulo
// - Subido por
// - Nombre del archivo
// - Tipo/Tamaño
// - Acciones (ver, descargar, eliminar)

deleteFile(id)
// Elimina archivo con confirmación modal
// Usa: swalQuestion()

viewFile(id)
// Abre archivo en nueva ventana

downloadFile(id)
// Descarga archivo al dispositivo
```

### Backend Components

#### Controller (ctrl-archivos.php)

**Class:** ctrl extends mdl

**Methods:**

```php
init()
// Retorna:
// - Lista de módulos (lsModules)
// - Contadores de archivos por módulo

ls()
// Parámetros: $_POST['module'], $_POST['search']
// Retorna: Array de archivos con formato para tabla
// Estructura:
// [
//   'row' => [
//     'id' => int,
//     'Módulo' => string,
//     'Subido por' => string,
//     'Nombre del archivo' => string,
//     'Tipo/Tamaño' => string,
//     'dropdown' => array
//   ]
// ]

getFile()
// Parámetros: $_POST['id']
// Retorna: Datos completos del archivo

deleteFile()
// Parámetros: $_POST['id']
// Retorna: status y message
// Elimina archivo físico y registro de BD

getFileCounts()
// Retorna: Contadores de archivos por módulo
// [
//   'total' => int,
//   'ventas' => int,
//   'compras' => int,
//   'proveedores' => int,
//   'almacen' => int
// ]
```

**Helper Functions:**

```php
dropdown($id)
// Genera opciones de acciones:
// - Ver archivo
// - Descargar archivo
// - Eliminar archivo

formatFileSize($bytes)
// Convierte bytes a formato legible (KB, MB)

getModuleName($moduleId)
// Retorna nombre del módulo según ID
```

#### Model (mdl-archivos.php)

**Class:** mdl extends CRUD

**Properties:**
- `$bd`: "rfwsmqex_contabilidad."
- `$util`: Instancia de Utileria

**Methods:**

```php
listFiles($array)
// Parámetros: [module_filter, search_term]
// Query: SELECT con JOIN a tabla de usuarios
// Retorna: Array de archivos con información completa

getFileById($array)
// Parámetros: [id]
// Retorna: Datos completos de un archivo

deleteFileById($array)
// Parámetros: [id]
// Elimina registro de la base de datos

lsModules()
// Retorna: Lista de módulos disponibles
// [
//   ['id' => 1, 'valor' => 'Ventas'],
//   ['id' => 2, 'valor' => 'Compras'],
//   ...
// ]

getFileCountsByModule()
// Retorna: Contadores agrupados por módulo
// Query: SELECT COUNT(*) GROUP BY module

getFilesByModule($array)
// Parámetros: [module_id]
// Retorna: Archivos filtrados por módulo
```

## Data Models

### Database Schema

#### Table: files

```sql
CREATE TABLE files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    src VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    description TEXT,
    date_created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    module_id INT NOT NULL,
    uploaded_by INT NOT NULL,
    file_size INT,
    file_type VARCHAR(50),
    active TINYINT DEFAULT 1,
    
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (module_id) REFERENCES modules(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    
    INDEX idx_module (module_id),
    INDEX idx_udn (udn_id),
    INDEX idx_date (date_created_at)
);
```

#### Table: modules (Reference)

```sql
CREATE TABLE modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1
);

-- Data
INSERT INTO modules (id, name) VALUES
(1, 'Ventas'),
(2, 'Clientes'),
(3, 'Compras'),
(4, 'Salidas de almacén'),
(5, 'Pagos a proveedor'),
(6, 'Archivos');
```

### Data Flow

#### Upload File Flow
```
User → Frontend (uploadFile) → Controller (addFile) → Model (createFile) → Database
                                      ↓
                                File System (save physical file)
                                      ↓
                                Return success/error
```

#### Delete File Flow
```
User → Frontend (deleteFile) → SweetAlert Confirmation
                                      ↓
                                Controller (deleteFile)
                                      ↓
                                Model (deleteFileById)
                                      ↓
                                File System (delete physical file)
                                      ↓
                                Database (delete record)
                                      ↓
                                Return success → Refresh table
```

#### Filter Files Flow
```
User → Select Module → Frontend (filterBarFiles) → Controller (ls) → Model (listFiles)
                                                                            ↓
                                                                    Filter by module_id
                                                                            ↓
                                                                    Return filtered data
                                                                            ↓
                                                            Frontend (update table)
```

## Error Handling

### Frontend Error Handling

```javascript
// En useFetch
try {
    const response = await useFetch({
        url: api,
        data: { opc: 'ls' }
    });
    
    if (response.status !== 200) {
        alert({
            icon: "error",
            text: response.message || "Error al cargar archivos"
        });
    }
} catch (error) {
    alert({
        icon: "error",
        text: "Error de conexión con el servidor"
    });
}
```

### Backend Error Handling

```php
// En ctrl-archivos.php
function deleteFile() {
    $status = 500;
    $message = 'Error al eliminar el archivo';
    
    try {
        $id = $_POST['id'];
        
        // Obtener información del archivo
        $file = $this->getFileById([$id]);
        
        if (!$file) {
            return [
                'status' => 404,
                'message' => 'Archivo no encontrado'
            ];
        }
        
        // Eliminar archivo físico
        $filePath = '../../' . $file['src'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Eliminar registro de BD
        $delete = $this->deleteFileById([$id]);
        
        if ($delete) {
            $status = 200;
            $message = 'Archivo eliminado correctamente';
        }
        
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error: ' . $e->getMessage();
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

### Validation Rules

**Frontend Validation:**
- File name: Required, max 255 characters
- File size: Max 10MB
- File type: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
- Module selection: Required

**Backend Validation:**
- File existence before delete
- User permissions
- File path security (prevent directory traversal)
- Database constraints

## Testing Strategy

### Unit Tests

**Frontend Tests (Jest):**
```javascript
describe('AdminFiles', () => {
    test('should render files table', () => {
        const admin = new AdminFiles(api, 'root');
        admin.lsFiles();
        expect($('#tbFiles').length).toBe(1);
    });
    
    test('should filter files by module', () => {
        const admin = new AdminFiles(api, 'root');
        $('#filterBarFiles #module').val('1').trigger('change');
        expect(admin.lsFiles).toHaveBeenCalled();
    });
    
    test('should show confirmation modal on delete', () => {
        const admin = new AdminFiles(api, 'root');
        admin.deleteFile(1);
        expect($('.swal2-container').length).toBe(1);
    });
});
```

**Backend Tests (PHPUnit):**
```php
class CtrlArchivosTest extends TestCase {
    public function testListFiles() {
        $_POST = ['opc' => 'ls', 'module' => '1'];
        $ctrl = new ctrl();
        $result = $ctrl->ls();
        
        $this->assertArrayHasKey('row', $result);
        $this->assertIsArray($result['row']);
    }
    
    public function testDeleteFile() {
        $_POST = ['opc' => 'deleteFile', 'id' => 1];
        $ctrl = new ctrl();
        $result = $ctrl->deleteFile();
        
        $this->assertEquals(200, $result['status']);
    }
}
```

### Integration Tests

**Test Scenarios:**
1. Upload file → Verify in database → Verify physical file exists
2. Delete file → Verify removed from database → Verify physical file deleted
3. Filter by module → Verify correct files displayed
4. View file → Verify file opens correctly
5. Download file → Verify file downloads with correct name

### User Acceptance Testing

**Test Cases:**
1. Usuario puede ver dashboard con contadores correctos
2. Usuario puede filtrar archivos por módulo
3. Usuario puede eliminar archivo con confirmación
4. Usuario puede ver archivo en nueva ventana
5. Usuario puede descargar archivo
6. Contadores se actualizan después de eliminar archivo

## Security Considerations

### Authentication & Authorization
- Verificar sesión activa antes de cualquier operación
- Validar permisos de usuario para eliminar archivos
- Registrar usuario que sube cada archivo

### File Security
- Validar tipo de archivo en backend
- Sanitizar nombres de archivo
- Almacenar archivos fuera del directorio web público
- Prevenir directory traversal attacks
- Limitar tamaño de archivos

### SQL Injection Prevention
- Usar prepared statements en todas las queries
- Validar y sanitizar todos los inputs
- Usar métodos de CRUD class que ya implementan protección

### XSS Prevention
- Escapar output en frontend
- Validar y sanitizar nombres de archivo
- No ejecutar código de archivos subidos

## Performance Optimization

### Frontend Optimization
- Lazy loading de archivos en tabla
- Paginación de resultados (15 registros por página)
- Caché de contadores en localStorage
- Debounce en búsqueda por nombre

### Backend Optimization
- Índices en columnas de búsqueda frecuente
- Query optimization con EXPLAIN
- Limitar resultados con LIMIT
- Usar COUNT(*) eficientemente para contadores

### File Storage Optimization
- Organizar archivos en subdirectorios por año/mes
- Comprimir archivos grandes automáticamente
- Implementar CDN para archivos estáticos (futuro)

## Design Decisions

### Why Tabs Instead of Single Table?
- Mejor organización visual por módulo
- Facilita navegación rápida
- Reduce carga inicial de datos
- Mejora UX al separar contextos

### Why Confirmation Modal for Delete?
- Previene eliminaciones accidentales
- Cumple con mejores prácticas de UX
- Proporciona feedback claro al usuario
- Permite cancelar operación fácilmente

### Why Store File Path Instead of Binary?
- Mejor performance en queries
- Facilita backup y migración
- Permite servir archivos directamente
- Reduce tamaño de base de datos

### Why Separate Dashboard and Admin Classes?
- Separación de responsabilidades
- Facilita mantenimiento
- Permite reutilización de componentes
- Sigue patrón de pivote admin

## Future Enhancements

1. **File Versioning**: Mantener historial de versiones de archivos
2. **Bulk Operations**: Eliminar múltiples archivos a la vez
3. **Advanced Search**: Búsqueda por fecha, tamaño, tipo
4. **File Preview**: Vista previa de PDFs e imágenes sin descargar
5. **Drag & Drop Upload**: Interfaz de carga más intuitiva
6. **File Sharing**: Compartir archivos con otros usuarios
7. **Audit Log**: Registro de todas las operaciones sobre archivos
8. **Cloud Storage**: Integración con S3 o similar
