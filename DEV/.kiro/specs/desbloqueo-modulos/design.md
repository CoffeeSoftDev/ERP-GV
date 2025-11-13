# Design Document

## Overview

El módulo de Desbloqueo de Módulos es un sistema administrativo que permite gestionar el acceso a módulos operativos del ERP mediante solicitudes de apertura controladas. El sistema registra auditoría completa de operaciones y permite configurar horarios de cierre mensual por unidad de negocio.

**Tecnologías:**
- Frontend: JavaScript (CoffeeSoft Framework), jQuery, TailwindCSS
- Backend: PHP (MVC Pattern)
- Base de datos: MySQL
- Componentes: Templates, createTable, createModalForm, tabLayout

## Architecture

### Patrón MVC

```
┌─────────────────────────────────────────────────────────────┐
│                         FRONTEND (JS)                        │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  App (extends Templates)                              │  │
│  │  - render()                                           │  │
│  │  - layout()                                           │  │
│  │  - lsModulesUnlocked()                               │  │
│  │  - addUnlockRequest()                                │  │
│  │  - updateCloseTime()                                 │  │
│  │  - toggleLockStatus()                                │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLER (ctrl-admin.php)               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  ctrl extends mdl                                     │  │
│  │  - init()                                             │  │
│  │  - lsModulesUnlocked()                               │  │
│  │  - addUnlockRequest()                                │  │
│  │  - getUnlockRequest()                                │  │
│  │  - toggleLockStatus()                                │  │
│  │  - lsCloseTime()                                     │  │
│  │  - updateCloseTime()                                 │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                      MODEL (mdl-admin.php)                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  mdl extends CRUD                                     │  │
│  │  - listModulesUnlocked()                             │  │
│  │  - createUnlockRequest()                             │  │
│  │  - getUnlockRequestById()                            │  │
│  │  - updateModuleStatus()                              │  │
│  │  - listCloseTime()                                   │  │
│  │  - updateCloseTimeByMonth()                          │  │
│  │  - lsUDN()                                           │  │
│  │  - lsModules()                                       │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                         DATABASE (MySQL)                     │
│  - module_unlock                                             │
│  - udn                                                       │
│  - module                                                    │
│  - close_time (nueva tabla)                                 │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de Datos

1. **Carga Inicial:**
   - Frontend llama `init()` → Backend retorna listas de UDN y Módulos
   - Frontend renderiza tabs y tabla principal

2. **Desbloqueo de Módulo:**
   - Usuario abre modal → Selecciona UDN, Módulo, Fecha, Motivo
   - Frontend valida campos → Envía `addUnlockRequest()`
   - Backend inserta registro → Retorna status 200
   - Frontend actualiza tabla sin recargar

3. **Cambio de Estado:**
   - Usuario hace clic en candado → Frontend envía `toggleLockStatus()`
   - Backend actualiza campo `active` → Retorna nuevo estado
   - Frontend actualiza ícono en tiempo real

4. **Configuración de Horario:**
   - Usuario abre modal de horarios → Selecciona mes y hora
   - Frontend valida mes futuro → Envía `updateCloseTime()`
   - Backend actualiza/inserta registro → Retorna confirmación
   - Frontend actualiza tabla de horarios

## Components and Interfaces

### Frontend Components (JS)

#### Class App extends Templates

```javascript
class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "unlockModules";
    }

    // Métodos principales
    render()                    // Inicializa layout y carga datos
    layout()                    // Crea estructura de tabs
    layoutHeader()              // Renderiza encabezado con usuario y fecha
    
    // Gestión de módulos desbloqueados
    lsModulesUnlocked()        // Tabla principal con módulos desbloqueados
    addUnlockRequest()         // Modal para solicitar apertura
    toggleLockStatus(id, active) // Cambiar estado de bloqueo
    
    // Gestión de horarios
    lsCloseTime()              // Tabla de horarios de cierre
    updateCloseTime()          // Modal para actualizar horario
    
    // Utilidades
    jsonUnlockForm()           // JSON del formulario de apertura
    jsonCloseTimeForm()        // JSON del formulario de horarios
}
```

#### Componentes CoffeeSoft Utilizados

1. **tabLayout()** - Navegación entre pestañas
   - Desbloqueo de módulos (activa por defecto)
   - Cuenta de ventas
   - Formas de pago
   - Clientes
   - Compras

2. **createTable()** - Tabla de módulos desbloqueados
   - Columnas: UDN, Fecha solicitada, Módulo, Motivo, Bloquear
   - Tema: corporativo
   - DataTables: true
   - Paginación: 15 registros

3. **createModalForm()** - Formularios modales
   - Modal de apertura de módulo
   - Modal de horario de cierre mensual

4. **createfilterBar()** - Barra de filtros (opcional)
   - Filtro por UDN
   - Filtro por rango de fechas

### Backend Interfaces

#### Controller (ctrl-admin.php)

```php
class ctrl extends mdl {
    
    // Inicialización
    function init()
    // Returns: ['udn' => array, 'modules' => array]
    
    // Gestión de módulos desbloqueados
    function lsModulesUnlocked()
    // Input: $_POST['udn'] (opcional)
    // Returns: ['row' => array, 'thead' => string]
    
    function addUnlockRequest()
    // Input: $_POST['date_requested', 'udn_id', 'module_id', 'lock_reason']
    // Returns: ['status' => int, 'message' => string]
    
    function toggleLockStatus()
    // Input: $_POST['id', 'active']
    // Returns: ['status' => int, 'message' => string]
    
    // Gestión de horarios
    function lsCloseTime()
    // Returns: ['row' => array]
    
    function updateCloseTime()
    // Input: $_POST['month', 'close_time']
    // Returns: ['status' => int, 'message' => string]
}
```

#### Model (mdl-admin.php)

```php
class mdl extends CRUD {
    
    // Consultas de módulos desbloqueados
    function listModulesUnlocked($array)
    // Query: SELECT con JOIN a udn y module
    // WHERE: active = 1
    // ORDER BY: unlock_date DESC
    
    function createUnlockRequest($array)
    // INSERT INTO module_unlock
    
    function updateModuleStatus($array)
    // UPDATE module_unlock SET active = ?, operation_date = NOW()
    
    // Consultas de horarios
    function listCloseTime()
    // SELECT * FROM close_time ORDER BY month ASC
    
    function updateCloseTimeByMonth($array)
    // UPDATE close_time SET close_time = ? WHERE month = ?
    // Si no existe: INSERT INTO close_time
    
    // Listas para filtros
    function lsUDN()
    // SELECT id, UDN as valor FROM udn WHERE active = 1
    
    function lsModules()
    // SELECT id, name as valor FROM module WHERE active = 1
}
```

## Data Models

### Tabla: module_unlock

```sql
CREATE TABLE module_unlock (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    udn_id INT(11) NOT NULL,
    module_id INT(11) NOT NULL,
    unlock_date DATE NOT NULL,
    lock_date DATE NULL,
    lock_reason TEXT NOT NULL,
    operation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    active TINYINT(1) NOT NULL DEFAULT 1,
    
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (module_id) REFERENCES module(id),
    
    INDEX idx_active (active),
    INDEX idx_unlock_date (unlock_date),
    INDEX idx_udn_module (udn_id, module_id)
);
```

**Campos:**
- `id`: Identificador único
- `udn_id`: Relación con unidad de negocio
- `module_id`: Relación con módulo operativo
- `unlock_date`: Fecha de solicitud de apertura
- `lock_date`: Fecha de cierre del módulo (NULL si está desbloqueado)
- `lock_reason`: Motivo de la apertura
- `operation_date`: Timestamp de última operación
- `active`: Estado (1 = desbloqueado, 0 = bloqueado)

### Tabla: udn (existente)

```sql
-- Tabla existente en el sistema
udn (
    idUDN INT(11) PRIMARY KEY,
    UDN VARCHAR(50),
    Abreviatura VARCHAR(5),
    Estado INT(11),
    Antiguedad INT(5),
    udn_patron INT(11)
)
```

### Tabla: module (nueva)

```sql
CREATE TABLE module (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    active TINYINT(1) NOT NULL DEFAULT 1,
    
    UNIQUE KEY uk_name (name)
);

-- Datos iniciales
INSERT INTO module (name, description) VALUES
('Ventas', 'Módulo de gestión de ventas'),
('Compras', 'Módulo de gestión de compras'),
('Clientes', 'Módulo de gestión de clientes'),
('Formas de pago', 'Módulo de formas de pago'),
('Cuenta de ventas', 'Módulo de cuentas de ventas'),
('Almacén', 'Módulo de gestión de almacén'),
('Proveedores', 'Módulo de gestión de proveedores');
```

### Tabla: close_time (nueva)

```sql
CREATE TABLE close_time (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    month TINYINT(2) NOT NULL,
    close_time TIME NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(100),
    
    UNIQUE KEY uk_month (month)
);

-- Datos iniciales (horario por defecto: 23:59)
INSERT INTO close_time (month, close_time) VALUES
(1, '23:59:00'), (2, '23:59:00'), (3, '23:59:00'),
(4, '23:59:00'), (5, '23:59:00'), (6, '23:59:00'),
(7, '23:59:00'), (8, '23:59:00'), (9, '23:59:00'),
(10, '23:59:00'), (11, '23:59:00'), (12, '23:59:00');
```

## Error Handling

### Frontend Validation

```javascript
// Validación de formulario de apertura
addUnlockRequest() {
    // Validar campos obligatorios
    if (!date_requested || !udn_id || !module_id || !lock_reason) {
        alert({
            icon: "warning",
            title: "Campos incompletos",
            text: "Por favor complete todos los campos obligatorios"
        });
        return false;
    }
    
    // Validar fecha no sea futura
    if (date_requested > today) {
        alert({
            icon: "error",
            text: "La fecha solicitada no puede ser futura"
        });
        return false;
    }
}

// Validación de horario de cierre
updateCloseTime() {
    const selectedMonth = parseInt($('#month').val());
    const currentMonth = new Date().getMonth() + 1;
    
    // Solo permitir editar mes actual o futuros
    if (selectedMonth < currentMonth) {
        alert({
            icon: "info",
            title: "Mes no editable",
            text: "Solo puede editar horarios de meses futuros o del mes actual"
        });
        return false;
    }
}
```

### Backend Error Handling

```php
// Manejo de errores en controlador
function addUnlockRequest() {
    $status = 500;
    $message = 'Error al registrar solicitud';
    
    try {
        // Validar que no exista solicitud activa para mismo módulo y UDN
        $exists = $this->existsActiveUnlock([
            $_POST['udn_id'],
            $_POST['module_id']
        ]);
        
        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe una solicitud activa para este módulo y UDN'
            ];
        }
        
        $_POST['operation_date'] = date('Y-m-d H:i:s');
        $_POST['active'] = 1;
        
        $create = $this->createUnlockRequest($this->util->sql($_POST));
        
        if ($create) {
            $status = 200;
            $message = 'Solicitud registrada correctamente';
        }
        
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error del servidor: ' . $e->getMessage();
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

### Database Constraints

```sql
-- Constraint para evitar duplicados activos
ALTER TABLE module_unlock
ADD CONSTRAINT uk_active_unlock 
UNIQUE (udn_id, module_id, active);

-- Trigger para validar fechas
DELIMITER $$
CREATE TRIGGER validate_unlock_dates
BEFORE INSERT ON module_unlock
FOR EACH ROW
BEGIN
    IF NEW.unlock_date > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de desbloqueo no puede ser futura';
    END IF;
END$$
DELIMITER ;
```

## Testing Strategy

### Unit Tests

**Frontend (JS):**
```javascript
// Test: Validación de campos obligatorios
test('addUnlockRequest valida campos vacíos', () => {
    const result = app.validateUnlockForm({
        date_requested: '',
        udn_id: 1,
        module_id: 2,
        lock_reason: 'Test'
    });
    expect(result).toBe(false);
});

// Test: Cambio de estado de bloqueo
test('toggleLockStatus actualiza ícono correctamente', () => {
    app.toggleLockStatus(1, 1);
    const icon = $('#row-1 .lock-icon');
    expect(icon.hasClass('icon-lock-open')).toBe(true);
});
```

**Backend (PHP):**
```php
// Test: Crear solicitud de desbloqueo
function testAddUnlockRequest() {
    $_POST = [
        'date_requested' => '2025-01-15',
        'udn_id' => 1,
        'module_id' => 2,
        'lock_reason' => 'Corrección de datos'
    ];
    
    $ctrl = new ctrl();
    $result = $ctrl->addUnlockRequest();
    
    $this->assertEquals(200, $result['status']);
}

// Test: Validar duplicados
function testPreventDuplicateUnlock() {
    // Crear primera solicitud
    $this->testAddUnlockRequest();
    
    // Intentar crear duplicado
    $result = $ctrl->addUnlockRequest();
    $this->assertEquals(409, $result['status']);
}
```

### Integration Tests

```javascript
// Test: Flujo completo de desbloqueo
describe('Flujo de desbloqueo de módulo', () => {
    it('debe registrar solicitud y actualizar tabla', async () => {
        // 1. Abrir modal
        app.addUnlockRequest();
        expect($('#modalUnlock').is(':visible')).toBe(true);
        
        // 2. Llenar formulario
        $('#date_requested').val('2025-01-15');
        $('#udn_id').val(1);
        $('#module_id').val(2);
        $('#lock_reason').val('Test');
        
        // 3. Enviar formulario
        $('#formUnlock').submit();
        
        // 4. Verificar respuesta
        await waitFor(() => {
            expect($('#tbUnlock tbody tr').length).toBeGreaterThan(0);
        });
    });
});
```

### Manual Testing Checklist

**Historia 1: Interfaz Principal**
- [ ] Verificar que se muestra el encabezado con usuario y fecha
- [ ] Verificar que se muestran las 5 pestañas correctamente
- [ ] Verificar que la tabla muestra las columnas correctas
- [ ] Verificar que los botones principales están visibles
- [ ] Verificar que los íconos de candado se muestran correctamente

**Historia 2: Apertura de Módulo**
- [ ] Verificar que el modal se abre al hacer clic en "Desbloquear módulo"
- [ ] Verificar que todos los campos están presentes
- [ ] Verificar validación de campos vacíos
- [ ] Verificar que se registra correctamente en BD
- [ ] Verificar que la tabla se actualiza sin recargar

**Historia 3: Horario de Cierre**
- [ ] Verificar que el modal se abre correctamente
- [ ] Verificar que se muestran los 12 meses
- [ ] Verificar que no se pueden editar meses pasados
- [ ] Verificar que se actualiza correctamente en BD
- [ ] Verificar que la tabla refleja los cambios

## Design Decisions

### 1. Uso de Tabs en lugar de Páginas Separadas

**Decisión:** Implementar navegación por tabs para las diferentes secciones.

**Razón:** 
- Mejora la experiencia de usuario al evitar recargas de página
- Mantiene el contexto del usuario (fecha, filtros)
- Facilita la navegación rápida entre módulos relacionados

**Alternativa considerada:** Páginas separadas con menú lateral
**Por qué se descartó:** Mayor complejidad de navegación y pérdida de contexto

### 2. Estado de Bloqueo con Toggle en Tabla

**Decisión:** Permitir cambiar el estado directamente desde la tabla con un clic en el ícono.

**Razón:**
- Acción rápida y directa
- Feedback visual inmediato
- Reduce pasos del usuario

**Alternativa considerada:** Modal de confirmación para cada cambio
**Por qué se descartó:** Demasiados pasos para una acción frecuente

### 3. Validación de Meses Pasados en Horarios

**Decisión:** Bloquear la edición de horarios de meses pasados.

**Razón:**
- Mantiene integridad de auditoría
- Evita modificaciones retroactivas
- Cumple con requisitos contables

**Alternativa considerada:** Permitir edición con registro de auditoría
**Por qué se descartó:** Riesgo de inconsistencias en reportes históricos

### 4. Tabla Separada para Horarios de Cierre

**Decisión:** Crear tabla `close_time` independiente en lugar de campo en `module`.

**Razón:**
- Permite configuración mensual específica
- Facilita auditoría de cambios
- Escalable para futuras configuraciones por UDN

**Alternativa considerada:** Campo único en tabla module
**Por qué se descartó:** No permite granularidad mensual

### 5. Uso de CoffeeSoft Framework

**Decisión:** Basar toda la interfaz en componentes del framework CoffeeSoft.

**Razón:**
- Consistencia visual con el resto del sistema
- Componentes probados y mantenidos
- Reduce tiempo de desarrollo
- Facilita mantenimiento futuro

**Componentes clave utilizados:**
- `createTable()` para tablas dinámicas
- `createModalForm()` para formularios
- `tabLayout()` para navegación
- `swalQuestion()` para confirmaciones
