# Design Document - Módulo de Formas de Pago

## Overview

El módulo de Formas de Pago es un componente del sistema de administración contable que permite gestionar los métodos de pago disponibles para todas las unidades de negocio. Utiliza la arquitectura MVC del framework CoffeeSoft con componentes reutilizables basados en jQuery y TailwindCSS.

## Architecture

### System Components

```
contabilidad/administrador/
├── formasPago.js           # Frontend - Clase principal que extiende Templates
├── ctrl/
│   └── ctrl-formasPago.php # Controlador - Lógica de negocio
└── mdl/
    └── mdl-formasPago.php  # Modelo - Acceso a datos
```

### Technology Stack

- **Frontend**: jQuery, TailwindCSS, CoffeeSoft Framework
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Framework**: CoffeeSoft (Complements → Components → Templates)

## Components and Interfaces

### Frontend Component (formasPago.js)

#### Class Structure

```javascript
class FormasPago extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "formasPago";
    }
}
```

#### Key Methods

1. **render()**: Inicializa el módulo completo
2. **layout()**: Crea la estructura visual usando `primaryLayout`
3. **filterBar()**: Genera la barra de filtros (si es necesaria)
4. **lsFormasPago()**: Lista todas las formas de pago en tabla
5. **addFormaPago()**: Muestra modal para agregar nueva forma de pago
6. **editFormaPago(id)**: Muestra modal para editar forma de pago existente
7. **statusFormaPago(id, active)**: Activa o desactiva una forma de pago
8. **jsonFormaPago()**: Define la estructura del formulario

#### Component Integration

- **createTable()**: Para listar formas de pago con paginación
- **createModalForm()**: Para modales de agregar/editar
- **swalQuestion()**: Para confirmaciones de activar/desactivar
- **tabLayout()**: Para integración con otras pestañas del módulo

### Backend Controller (ctrl-formasPago.php)

#### Class Structure

```php
class ctrl extends mdl {
    // Métodos del controlador
}
```

#### API Endpoints (via opc parameter)

1. **init()**: Retorna datos iniciales (si hay filtros)
2. **lsFormasPago()**: Lista formas de pago con formato para tabla
3. **getFormaPago()**: Obtiene datos de una forma de pago específica
4. **addFormaPago()**: Crea nueva forma de pago
5. **editFormaPago()**: Actualiza forma de pago existente
6. **statusFormaPago()**: Cambia estado activo/inactivo

#### Response Format

```php
return [
    'status' => 200,  // 200: success, 500: error, 409: conflict
    'message' => 'Mensaje descriptivo',
    'data' => [...],  // Datos opcionales
    'row' => [...]    // Para tablas
];
```

### Backend Model (mdl-formasPago.php)

#### Class Structure

```php
class mdl extends CRUD {
    protected $util;
    public $bd;
    
    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_contabilidad.";
    }
}
```

#### Database Methods

1. **listFormasPago($array)**: Consulta todas las formas de pago
2. **getFormaPagoById($id)**: Obtiene una forma de pago por ID
3. **createFormaPago($array)**: Inserta nueva forma de pago
4. **updateFormaPago($array)**: Actualiza forma de pago
5. **existsFormaPagoByName($array)**: Valida duplicados por nombre

## Data Models

### Database Schema

#### Table: payment_methods

```sql
CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Field Descriptions

- **id**: Identificador único de la forma de pago
- **name**: Nombre de la forma de pago (Efectivo, Transferencia, etc.)
- **active**: Estado (1 = activo, 0 = inactivo)
- **date_creation**: Fecha de creación del registro
- **date_updated**: Fecha de última actualización

### Data Flow

```
User Action → Frontend (formasPago.js)
    ↓
useFetch() → Backend Controller (ctrl-formasPago.php)
    ↓
Model Methods → Database (payment_methods table)
    ↓
JSON Response → Frontend Update
```

## Error Handling

### Validation Rules

1. **Name Required**: El nombre de la forma de pago es obligatorio
2. **Name Unique**: No se permiten nombres duplicados
3. **Active Status**: Solo valores 0 o 1 permitidos

### Error Messages

```javascript
// Frontend
{
    409: "Ya existe una forma de pago con ese nombre",
    500: "Error al guardar la forma de pago",
    200: "Forma de pago guardada correctamente"
}
```

### Error Handling Strategy

1. **Frontend Validation**: Validación de campos requeridos antes de enviar
2. **Backend Validation**: Validación de duplicados y datos en controlador
3. **Database Constraints**: UNIQUE constraint en campo name
4. **User Feedback**: Mensajes claros usando `alert()` de CoffeeSoft

## Testing Strategy

### Unit Tests

1. **Model Tests**
   - Validar inserción de formas de pago
   - Validar detección de duplicados
   - Validar actualización de estados

2. **Controller Tests**
   - Validar respuestas JSON correctas
   - Validar manejo de errores
   - Validar formato de datos para tabla

### Integration Tests

1. **Frontend-Backend Integration**
   - Validar flujo completo de agregar forma de pago
   - Validar flujo completo de editar forma de pago
   - Validar flujo completo de cambiar estado

2. **Database Integration**
   - Validar constraints de base de datos
   - Validar transacciones correctas

### User Acceptance Tests

1. **Scenario 1**: Agregar nueva forma de pago
   - Abrir modal
   - Ingresar nombre válido
   - Guardar y verificar en tabla

2. **Scenario 2**: Validar duplicados
   - Intentar agregar nombre existente
   - Verificar mensaje de error

3. **Scenario 3**: Editar forma de pago
   - Seleccionar forma de pago existente
   - Modificar nombre
   - Guardar y verificar cambios

4. **Scenario 4**: Activar/Desactivar
   - Seleccionar forma de pago
   - Cambiar estado
   - Verificar confirmación y actualización

## UI/UX Considerations

### Visual Design

- **Theme**: Corporativo (azul #003360)
- **Icons**: 
  - ✏️ Editar (icon-pencil)
  - 🔴 Desactivar (icon-toggle-off)
  - 🟢 Activar (icon-toggle-on)
- **Buttons**: TailwindCSS con colores corporativos

### User Interactions

1. **Table Actions**: Botones inline por cada fila
2. **Modals**: Bootbox modals con formularios CoffeeSoft
3. **Confirmations**: SweetAlert2 para acciones críticas
4. **Feedback**: Mensajes de éxito/error después de cada acción

### Responsive Design

- Tabla responsive con scroll horizontal en móviles
- Modales adaptables a diferentes tamaños de pantalla
- Botones con tamaño adecuado para touch

## Integration Points

### Tab Navigation

El módulo se integra con otras pestañas del administrador:
- Cuenta de mayor
- Subcuenta de mayor
- Tipos de compra
- **Formas de pago** (activa)

### Purchase Module Integration

Las formas de pago activas están disponibles para:
- Filtros en módulo de compras
- Captura de nuevas compras
- Reportes y consultas

## Performance Considerations

1. **Table Pagination**: DataTables con 15 registros por página
2. **AJAX Requests**: Uso de `useFetch()` asíncrono
3. **DOM Updates**: Actualización parcial sin recargar página completa
4. **Database Indexes**: Index en campo `name` para búsquedas rápidas

## Security Considerations

1. **SQL Injection Prevention**: Uso de prepared statements en CRUD
2. **XSS Prevention**: Sanitización de inputs con `$this->util->sql()`
3. **Session Management**: Validación de sesión en controlador
4. **Access Control**: Solo usuarios administradores pueden gestionar formas de pago
