# Design Document - Módulo de Archivos

## Overview

El módulo de Archivos es un sistema de gestión documental integrado al ERP CoffeeSoft que permite consultar, descargar y eliminar archivos organizados por módulos (Ventas, Compras, Almacén, Tesorería). Implementa control de acceso basado en roles con tres niveles: Captura, Gerencia y Contabilidad/Dirección.

**Tecnologías:**
- Frontend: JavaScript (CoffeeSoft Framework), jQuery, TailwindCSS
- Backend: PHP 7.4+
- Base de datos: MySQL
- Arquitectura: MVC (Model-View-Controller)

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   archivos.js│  │ Components   │  │  Templates   │     │
│  │   (App class)│  │  (CoffeeSoft)│  │  (Layouts)   │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            ↓ AJAX
┌─────────────────────────────────────────────────────────────┐
│                    Controller Layer                         │
│  ┌──────────────────────────────────────────────────────┐  │
│  │           ctrl-archivos.php                          │  │
│  │  - init()         - getFile()                        │  │
│  │  - ls()           - deleteFile()                     │  │
│  │  - downloadFile() - logFileAction()                  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                      Model Layer                            │
│  ┌──────────────────────────────────────────────────────┐  │
│  │           mdl-archivos.php                           │  │
│  │  - listFiles()        - getFileById()                │  │
│  │  - deleteFileById()   - createFileLog()              │  │
│  │  - lsModules()        - lsUDN()                      │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │   file   │  │   udn    │  │ usuarios │  │file_logs │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Access Control Flow

```
User Login → Session Validation → Role Detection
                                        ↓
                    ┌───────────────────┴───────────────────┐
                    ↓                   ↓                   ↓
              [Captura]           [Gerencia]    [Contabilidad/Dirección]
                    ↓                   ↓                   ↓
            Single Date         Date Range          Date Range + UDN
            View/Download       View/Download       View/Download/Delete
            Delete              Download Daily      Full Access
```

## Components and Interfaces

### Frontend Components (archivos.js)

#### Class: App (extends Templates)

**Properties:**
- `PROJECT_NAME`: "archivos"
- `_link`: "ctrl/ctrl-archivos.php"
- `_div_modulo`: "root"

**Methods:**

```javascript
render()
// Inicializa el módulo completo
// Llama a layout(), filterBar(), lsFiles()

layout()
// Crea estructura principal con primaryLayout
// Incluye header con título "📁 Módulo de Archivos"
// Renderiza tarjetas de totales por módulo

filterBar()
// Genera barra de filtros con:
// - Selector de rango de fechas (dataPicker)
// - Dropdown de módulos
// - Dropdown de UDN (condicional según rol)
// - Botón "Buscar"

lsFiles()
// Lista archivos en tabla con createTable
// Columnas: Fecha subida, Módulo, Subido por, Nombre, Tipo/Tamaño, Acciones
// Aplica filtros de fecha, módulo y UDN

downloadFile(id)
// Descarga archivo mediante enlace seguro
// Valida sesión y genera token temporal

deleteFile(id)
// Muestra confirmación con swalQuestion
// Ejecuta eliminación y actualiza tabla
// Registra acción en logs

viewFile(id)
// Abre archivo en modal o nueva pestaña
// Soporta previsualización de PDF e imágenes
```

### Backend Components

#### Controller: ctrl-archivos.php

**Class: ctrl (extends mdl)**

**Methods:**

```php
init()
// Retorna datos iniciales:
// - Lista de módulos (lsModules)
// - Lista de UDN (lsUDN)
// - Nivel de acceso del usuario (getUserLevel)

ls()
// Lista archivos con filtros
// Parámetros: fi, ff, module, udn
// Retorna: array con rows para tabla
// Incluye: fecha, módulo, usuario, nombre, tamaño, acciones

getFile()
// Obtiene datos de un archivo específico
// Parámetros: id
// Retorna: datos completos del archivo

downloadFile()
// Genera enlace de descarga seguro
// Valida sesión y permisos
// Registra acción en logs
// Retorna: URL temporal con token

deleteFile()
// Elimina archivo del sistema
// Valida permisos según rol
// Registra acción en logs
// Retorna: status y message

logFileAction()
// Registra acciones sobre archivos
// Parámetros: file_id, action, user_id
// Retorna: confirmación de registro
```

#### Model: mdl-archivos.php

**Class: mdl (extends CRUD)**

**Properties:**
- `$bd`: "rfwsmqex_contabilidad."
- `$util`: Instancia de Utileria

**Methods:**

```php
listFiles($array)
// Consulta archivos con filtros
// Parámetros: [fi, ff, module, udn, user_level]
// JOIN con usuarios y udn
// Retorna: array de archivos

getFileById($array)
// Obtiene archivo por ID
// Parámetros: [id]
// Retorna: datos del archivo

deleteFileById($array)
// Elimina archivo por ID
// Parámetros: [id]
// Retorna: boolean

createFileLog($array)
// Crea registro de acción
// Parámetros: [file_id, user_id, action, date]
// Retorna: boolean

lsModules()
// Lista módulos disponibles
// Retorna: [Ventas, Compras, Almacén, Tesorería]

lsUDN()
// Lista unidades de negocio
// Retorna: array de UDN

getUserLevel($array)
// Obtiene nivel de acceso del usuario
// Parámetros: [user_id]
// Retorna: nivel (1=Captura, 2=Gerencia, 3=Contabilidad)
```

## Data Models

### Table: file

```sql
CREATE TABLE file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATETIME NOT NULL,
    size_bytes BIGINT NOT NULL,
    path VARCHAR(500) NOT NULL,
    extension VARCHAR(10) NOT NULL,
    operation_date DATE NOT NULL,
    module ENUM('Ventas', 'Compras', 'Almacén', 'Tesorería') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_module (module),
    INDEX idx_operation_date (operation_date),
    INDEX idx_udn_id (udn_id)
);
```

### Table: file_logs

```sql
CREATE TABLE file_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT NOT NULL,
    action ENUM('download', 'view', 'delete') NOT NULL,
    action_date DATETIME NOT NULL,
    ip_address VARCHAR(45),
    FOREIGN KEY (file_id) REFERENCES file(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES usuarios(id),
    INDEX idx_file_id (file_id),
    INDEX idx_action_date (action_date)
);
```

### Table: udn (existing)

```sql
-- Tabla existente en el sistema
-- Campos relevantes:
-- id, UDN, Abreviatura, Estado
```

### Table: usuarios (existing)

```sql
-- Tabla existente en el sistema
-- Campos relevantes:
-- id, user, usr_perfil (nivel de acceso)
```

## Error Handling

### Frontend Error Handling

```javascript
// Manejo de errores en peticiones AJAX
try {
    const response = await useFetch({
        url: api,
        data: { opc: 'deleteFile', id: fileId }
    });
    
    if (response.status === 200) {
        alert({ icon: "success", text: response.message });
        this.lsFiles();
    } else {
        alert({ icon: "error", text: response.message });
    }
} catch (error) {
    alert({ 
        icon: "error", 
        text: "Error de conexión. Intente nuevamente." 
    });
}
```

### Backend Error Handling

```php
// Validación de permisos
function deleteFile() {
    $status = 500;
    $message = 'Error al eliminar archivo';
    
    try {
        // Validar nivel de acceso
        $userLevel = $this->getUserLevel([$_SESSION['user_id']]);
        
        if ($userLevel < 1) {
            return [
                'status' => 403,
                'message' => 'No tiene permisos para eliminar archivos'
            ];
        }
        
        // Validar existencia del archivo
        $file = $this->getFileById([$_POST['id']]);
        
        if (!$file) {
            return [
                'status' => 404,
                'message' => 'Archivo no encontrado'
            ];
        }
        
        // Eliminar archivo físico
        $filePath = $file['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Eliminar registro de BD
        $delete = $this->deleteFileById([$_POST['id']]);
        
        if ($delete) {
            // Registrar acción
            $this->createFileLog([
                'file_id' => $_POST['id'],
                'user_id' => $_SESSION['user_id'],
                'action' => 'delete',
                'date' => date('Y-m-d H:i:s')
            ]);
            
            $status = 200;
            $message = 'Archivo eliminado correctamente';
        }
        
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error interno: ' . $e->getMessage();
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

### Error Codes

- **200**: Operación exitosa
- **403**: Permisos insuficientes
- **404**: Archivo no encontrado
- **500**: Error interno del servidor

## Testing Strategy

### Unit Tests

**Frontend Tests (Jest + jQuery):**

```javascript
describe('App - archivos.js', () => {
    test('filterBar debe generar selector de fechas', () => {
        const app = new App(api, 'root');
        app.filterBar();
        expect($('#calendar').length).toBe(1);
    });
    
    test('deleteFile debe mostrar confirmación', () => {
        const app = new App(api, 'root');
        spyOn(window, 'swalQuestion');
        app.deleteFile(1);
        expect(window.swalQuestion).toHaveBeenCalled();
    });
});
```

**Backend Tests (PHPUnit):**

```php
class CtrlArchivosTest extends TestCase {
    public function testLsReturnsFiles() {
        $_POST = [
            'opc' => 'ls',
            'fi' => '2025-01-01',
            'ff' => '2025-12-31'
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->ls();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('row', $result);
    }
    
    public function testDeleteFileRequiresPermissions() {
        $_SESSION['user_id'] = 999; // Usuario sin permisos
        $_POST = ['opc' => 'deleteFile', 'id' => 1];
        
        $ctrl = new ctrl();
        $result = $ctrl->deleteFile();
        
        $this->assertEquals(403, $result['status']);
    }
}
```

### Integration Tests

**Test Scenario 1: Flujo completo de eliminación**

```
1. Usuario con nivel Captura inicia sesión
2. Accede al módulo de archivos
3. Selecciona fecha específica
4. Visualiza lista de archivos
5. Hace clic en botón eliminar
6. Confirma eliminación en modal
7. Sistema elimina archivo y actualiza tabla
8. Sistema registra acción en logs
```

**Test Scenario 2: Control de acceso por nivel**

```
1. Usuario con nivel Gerencia inicia sesión
2. Accede al módulo de archivos
3. Intenta eliminar un archivo
4. Sistema valida permisos
5. Sistema muestra mensaje de error (si no tiene permiso)
6. Usuario solo puede descargar archivos
```

### Manual Testing Checklist

- [ ] Verificar carga de totales por módulo
- [ ] Probar selector de rango de fechas
- [ ] Validar filtro por módulo
- [ ] Validar filtro por UDN (usuarios con acceso)
- [ ] Probar descarga de archivos
- [ ] Probar eliminación con confirmación
- [ ] Verificar registro de acciones en logs
- [ ] Validar permisos por nivel de acceso
- [ ] Probar previsualización de archivos
- [ ] Verificar responsividad en móviles

## Security Considerations

### Authentication & Authorization

```php
// Validación de sesión en cada petición
session_start();
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 401,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

// Validación de permisos por nivel
function validateAccess($requiredLevel) {
    $userLevel = $this->getUserLevel([$_SESSION['user_id']]);
    return $userLevel >= $requiredLevel;
}
```

### File Download Security

```php
// Generar token temporal para descarga
function downloadFile() {
    $fileId = $_POST['id'];
    $userId = $_SESSION['user_id'];
    
    // Validar permisos
    if (!$this->validateAccess(1)) {
        return ['status' => 403, 'message' => 'Sin permisos'];
    }
    
    // Generar token temporal (válido 5 minutos)
    $token = bin2hex(random_bytes(32));
    $expiry = time() + 300;
    
    // Guardar token en sesión
    $_SESSION['download_tokens'][$token] = [
        'file_id' => $fileId,
        'user_id' => $userId,
        'expiry' => $expiry
    ];
    
    // Retornar URL con token
    return [
        'status' => 200,
        'url' => "download.php?token=$token"
    ];
}
```

### SQL Injection Prevention

```php
// Uso de prepared statements en todas las consultas
function listFiles($array) {
    return $this->_Select([
        'table' => "{$this->bd}file",
        'values' => "file.*, usuarios.user, udn.UDN",
        'leftjoin' => [
            "{$this->bd}usuarios" => "file.user_id = usuarios.id",
            "{$this->bd}udn" => "file.udn_id = udn.id"
        ],
        'where' => 'file.operation_date BETWEEN ? AND ? AND file.module = ?',
        'data' => $array // Parámetros sanitizados automáticamente
    ]);
}
```

### XSS Prevention

```javascript
// Sanitización de datos en frontend
function renderFileName(name) {
    return $('<div>').text(name).html(); // Escapa HTML
}

// Uso de métodos seguros de jQuery
$('#fileName').text(fileName); // En lugar de .html()
```

## Performance Optimization

### Database Indexing

```sql
-- Índices para optimizar consultas frecuentes
CREATE INDEX idx_file_operation_date ON file(operation_date);
CREATE INDEX idx_file_module ON file(module);
CREATE INDEX idx_file_udn_id ON file(udn_id);
CREATE INDEX idx_file_composite ON file(operation_date, module, udn_id);
```

### Frontend Optimization

```javascript
// Paginación en tabla para grandes volúmenes
this.createTable({
    conf: { datatable: true, pag: 25 }, // 25 registros por página
    // ...
});

// Debounce en filtros de búsqueda
let searchTimeout;
$('#searchInput').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        app.lsFiles();
    }, 500);
});
```

### Caching Strategy

```php
// Cache de listas estáticas (módulos, UDN)
function init() {
    if (!isset($_SESSION['cache_modules'])) {
        $_SESSION['cache_modules'] = $this->lsModules();
    }
    
    return [
        'modules' => $_SESSION['cache_modules'],
        'udn' => $this->lsUDN()
    ];
}
```

## Deployment Considerations

### File Structure

```
contabilidad/
├── captura/
│   ├── index.php
│   ├── ctrl/
│   │   └── ctrl-archivos.php
│   ├── mdl/
│   │   └── mdl-archivos.php
│   └── js/
│       └── archivos.js
├── uploads/
│   ├── ventas/
│   ├── compras/
│   ├── almacen/
│   └── tesoreria/
└── src/
    └── js/
        ├── coffeSoft.js
        └── plugins.js
```

### Environment Configuration

```php
// config.php
define('UPLOAD_PATH', '/var/www/contabilidad/uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'png', 'xlsx', 'docx']);
```

### Database Migration

```sql
-- Script de migración
-- 1. Crear tabla file
-- 2. Crear tabla file_logs
-- 3. Crear índices
-- 4. Insertar datos de prueba (opcional)
```

## Future Enhancements

1. **Búsqueda avanzada**: Búsqueda por nombre de archivo, contenido (OCR)
2. **Versionado**: Mantener historial de versiones de archivos
3. **Compartir archivos**: Generar enlaces públicos temporales
4. **Notificaciones**: Alertas cuando se suben nuevos archivos
5. **Compresión**: Comprimir archivos automáticamente para ahorrar espacio
6. **Integración con cloud**: Almacenamiento en AWS S3 o Google Cloud Storage
