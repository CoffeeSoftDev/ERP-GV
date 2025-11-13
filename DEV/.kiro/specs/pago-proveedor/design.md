# Design Document - Módulo de Pagos a Proveedor

## Overview

El módulo de Pagos a Proveedor es un sistema de gestión CRUD que permite registrar, editar, eliminar y visualizar pagos realizados a proveedores. El módulo se integra al sistema de contabilidad existente y utiliza el framework CoffeeSoft con arquitectura MVC, siguiendo el patrón del pivote admin.

**Características principales:**
- Gestión completa de pagos a proveedores (CRUD)
- Clasificación por tipo de pago (Fondo fijo / Corporativo)
- Cálculo dinámico de totales consolidados
- Interfaz responsiva con TailwindCSS
- Validaciones en frontend y backend
- Confirmaciones para acciones destructivas

## Architecture

### Patrón Arquitectónico
El módulo sigue el patrón **MVC (Model-View-Controller)** del framework CoffeeSoft:

```
┌─────────────────────────────────────────────────────────────┐
│                         FRONTEND (View)                      │
│  pago-proveedor.js (extiende Templates de CoffeeSoft)       │
│  - App class: Layout, FilterBar, Tabla principal            │
│  - Componentes: createTable, createModalForm, swalQuestion  │
└────────────────────┬────────────────────────────────────────┘
                     │ AJAX (useFetch)
                     ↓
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLER (Lógica)                       │
│  ctrl-pago-proveedor.php (extiende mdl)                     │
│  - init(): Carga filtros iniciales                          │
│  - ls(): Lista pagos con totales                            │
│  - getPayment(): Obtiene pago por ID                        │
│  - addPayment(): Registra nuevo pago                        │
│  - editPayment(): Actualiza pago existente                  │
│  - deletePayment(): Elimina pago (soft delete)              │
└────────────────────┬────────────────────────────────────────┘
                     │ Consultas SQL
                     ↓
┌─────────────────────────────────────────────────────────────┐
│                      MODEL (Datos)                           │
│  mdl-pago-proveedor.php (extiende CRUD)                     │
│  - listPayments(): SELECT con JOIN a supplier               │
│  - getPaymentById(): SELECT por ID                          │
│  - createPayment(): INSERT nuevo registro                   │
│  - updatePayment(): UPDATE registro existente               │
│  - deletePaymentById(): UPDATE active = 0                   │
│  - lsSuppliers(): SELECT proveedores activos                │
│  - calculateTotals(): Suma por tipo de pago                 │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ↓
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL)                          │
│  - supplier_payment (pagos)                                 │
│  - supplier (proveedores)                                   │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de Datos

**Carga inicial:**
1. Usuario accede al módulo → `App.render()`
2. Frontend llama `init()` → Carga lista de proveedores
3. Frontend llama `ls()` → Carga tabla de pagos con totales
4. Sistema calcula y muestra totales por tipo

**Agregar pago:**
1. Usuario click "Registrar nuevo pago" → Modal form
2. Usuario llena formulario → Validación frontend
3. Submit → `addPayment()` en controlador
4. Controlador valida y llama `createPayment()` en modelo
5. Modelo inserta en DB → Retorna status
6. Frontend recarga tabla y actualiza totales

**Editar pago:**
1. Usuario click botón editar → `getPayment(id)`
2. Modal precarga datos → Usuario modifica
3. Submit → `editPayment()` en controlador
4. Controlador valida y llama `updatePayment()` en modelo
5. Modelo actualiza DB → Retorna status
6. Frontend recarga tabla y actualiza totales

**Eliminar pago:**
1. Usuario click botón eliminar → Modal confirmación
2. Usuario confirma → `deletePayment(id)`
3. Controlador llama `deletePaymentById()` (soft delete)
4. Modelo actualiza `active = 0` → Retorna status
5. Frontend recarga tabla y actualiza totales

## Components and Interfaces

### Frontend Components (pago-proveedor.js)

#### Class: App extends Templates

**Constructor:**
```javascript
constructor(link, div_modulo) {
    super(link, div_modulo);
    this.PROJECT_NAME = "pagoProveedor";
}
```

**Métodos principales:**

1. **render()**
   - Inicializa el módulo
   - Llama a `layout()`, `filterBar()`, `ls()`

2. **layout()**
   - Usa `primaryLayout()` de CoffeeSoft
   - Crea estructura: filterBar + container
   - Renderiza encabezado con fecha y usuario

3. **filterBar()**
   - Usa `createfilterBar()` de CoffeeSoft
   - Botón "Registrar nuevo pago a proveedor"
   - Botón "Subir archivos de proveedores"

4. **ls()**
   - Usa `createTable()` de CoffeeSoft
   - Muestra tabla con pagos
   - Renderiza cards de totales
   - Columnas: Proveedor, Tipo de Pago, Monto, Descripción, Acciones

5. **addPayment()**
   - Usa `createModalForm()` de CoffeeSoft
   - Campos: supplier_id (select), payment_type (select), amount (cifra), description (textarea)
   - Validación: campos obligatorios
   - Submit → `opc: 'addPayment'`

6. **editPayment(id)**
   - Llama `getPayment(id)` con `useFetch()`
   - Usa `createModalForm()` con `autofill`
   - Precarga datos del pago
   - Submit → `opc: 'editPayment'`

7. **deletePayment(id)**
   - Usa `swalQuestion()` de CoffeeSoft
   - Modal confirmación
   - Submit → `opc: 'deletePayment'`

8. **jsonPayment()**
   - Retorna estructura JSON del formulario
   - Define campos y validaciones

9. **showTotals(data)**
   - Renderiza 3 cards con totales
   - Total general, Fondo fijo, Corporativo
   - Usa `formatPrice()` para formato de moneda

### Backend Components

#### Controller (ctrl-pago-proveedor.php)

**Class: ctrl extends mdl**

**Métodos:**

1. **init()**
   ```php
   return [
       'suppliers' => $this->lsSuppliers([1]),
       'paymentTypes' => [
           ['id' => 'Fondo fijo', 'valor' => 'Fondo fijo'],
           ['id' => 'Corporativo', 'valor' => 'Corporativo']
       ]
   ];
   ```

2. **ls()**
   - Llama `listPayments([$_POST['udn']])`
   - Itera resultados y construye `$__row[]`
   - Calcula totales con `calculateTotals()`
   - Retorna `['row' => $__row, 'totals' => $totals]`

3. **getPayment()**
   - Recibe `$_POST['id']`
   - Llama `getPaymentById([$_POST['id']])`
   - Retorna `['status' => 200, 'data' => $payment]`

4. **addPayment()**
   - Recibe datos por POST
   - Agrega `operation_date = date('Y-m-d')`
   - Agrega `active = 1`
   - Llama `createPayment($this->util->sql($_POST))`
   - Retorna `['status' => 200/500, 'message' => '...']`

5. **editPayment()**
   - Recibe datos por POST con `id`
   - Llama `updatePayment($this->util->sql($_POST, 1))`
   - Retorna `['status' => 200/500, 'message' => '...']`

6. **deletePayment()**
   - Recibe `$_POST['id']`
   - Llama `deletePaymentById([$_POST['id']])`
   - Retorna `['status' => 200/500, 'message' => '...']`

**Funciones auxiliares:**

```php
function renderStatus($active) {
    return $active == 1 
        ? '<span class="badge bg-success">Activo</span>'
        : '<span class="badge bg-danger">Inactivo</span>';
}

function dropdown($id) {
    return [
        ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "app.editPayment($id)"],
        ['icon' => 'icon-trash', 'text' => 'Eliminar', 'onclick' => "app.deletePayment($id)"]
    ];
}
```

#### Model (mdl-pago-proveedor.php)

**Class: mdl extends CRUD**

**Propiedades:**
```php
public $util;
public $bd = "rfwsmqex_contabilidad.";
```

**Métodos:**

1. **listPayments($array)**
   ```php
   return $this->_Select([
       'table' => "{$this->bd}supplier_payment",
       'values' => "
           supplier_payment.id,
           supplier.name as supplier_name,
           supplier_payment.payment_type,
           supplier_payment.amount,
           supplier_payment.description,
           DATE_FORMAT(supplier_payment.operation_date, '%d/%m/%Y') as operation_date,
           supplier_payment.active
       ",
       'leftjoin' => [
           "{$this->bd}supplier" => "supplier_payment.supplier_id = supplier.id"
       ],
       'where' => 'supplier_payment.active = 1 AND supplier_payment.udn_id = ?',
       'order' => ['DESC' => 'supplier_payment.id'],
       'data' => $array
   ]);
   ```

2. **getPaymentById($array)**
   ```php
   return $this->_Select([
       'table' => "{$this->bd}supplier_payment",
       'values' => '*',
       'where' => 'id = ?',
       'data' => $array
   ])[0];
   ```

3. **createPayment($array)**
   ```php
   return $this->_Insert([
       'table' => "{$this->bd}supplier_payment",
       'values' => $array['values'],
       'data' => $array['data']
   ]);
   ```

4. **updatePayment($array)**
   ```php
   return $this->_Update([
       'table' => "{$this->bd}supplier_payment",
       'values' => $array['values'],
       'where' => 'id = ?',
       'data' => $array['data']
   ]);
   ```

5. **deletePaymentById($array)**
   ```php
   return $this->_Update([
       'table' => "{$this->bd}supplier_payment",
       'values' => 'active = ?',
       'where' => 'id = ?',
       'data' => [0, $array[0]]
   ]);
   ```

6. **lsSuppliers($array)**
   ```php
   return $this->_Select([
       'table' => "{$this->bd}supplier",
       'values' => 'id, name as valor',
       'where' => 'active = ?',
       'order' => ['ASC' => 'name'],
       'data' => $array
   ]);
   ```

7. **calculateTotals($array)**
   ```php
   $query = "
       SELECT 
           SUM(amount) as total_general,
           SUM(CASE WHEN payment_type = 'Fondo fijo' THEN amount ELSE 0 END) as total_fondo_fijo,
           SUM(CASE WHEN payment_type = 'Corporativo' THEN amount ELSE 0 END) as total_corporativo
       FROM {$this->bd}supplier_payment
       WHERE active = 1 AND udn_id = ?
   ";
   return $this->_Read($query, $array)[0];
   ```

## Data Models

### Database Schema

#### Tabla: supplier_payment

```sql
CREATE TABLE supplier_payment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT NOT NULL,
    payment_type VARCHAR(50) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    operation_date DATE NOT NULL,
    udn_id INT NOT NULL,
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    INDEX idx_supplier (supplier_id),
    INDEX idx_payment_type (payment_type),
    INDEX idx_operation_date (operation_date),
    INDEX idx_active (active),
    INDEX idx_udn (udn_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
- `id`: Identificador único del pago
- `supplier_id`: FK a tabla supplier
- `payment_type`: Tipo de pago ('Fondo fijo' o 'Corporativo')
- `amount`: Monto del pago (12 dígitos, 2 decimales)
- `description`: Descripción opcional del pago
- `operation_date`: Fecha de operación del pago
- `udn_id`: Unidad de negocio
- `active`: Estado del registro (1=activo, 0=eliminado)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de última actualización

#### Tabla: supplier (existente)

```sql
CREATE TABLE supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(191) NOT NULL,
    udn_id INT NOT NULL,
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_active (active),
    INDEX idx_udn (udn_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Relaciones

```
supplier (1) ──────< (N) supplier_payment
    │                        │
    └─ id                    └─ supplier_id
```

### Validaciones de Datos

**Frontend (JavaScript):**
- `supplier_id`: required, debe existir en lista de proveedores
- `payment_type`: required, debe ser 'Fondo fijo' o 'Corporativo'
- `amount`: required, numeric, > 0, formato decimal(12,2)
- `description`: optional, max 500 caracteres

**Backend (PHP):**
- Validar que `supplier_id` exista en tabla supplier
- Validar que `payment_type` sea valor válido
- Validar que `amount` sea numérico y > 0
- Sanitizar `description` para prevenir XSS
- Validar que `udn_id` corresponda al usuario

## Error Handling

### Frontend Errors

**Validación de formulario:**
```javascript
// En createModalForm con validación automática
json: [
    { opc: "select", id: "supplier_id", required: true },
    { opc: "select", id: "payment_type", required: true },
    { opc: "input", id: "amount", tipo: "cifra", required: true }
]
```

**Manejo de respuestas:**
```javascript
success: (response) => {
    if (response.status === 200) {
        alert({ icon: "success", text: response.message });
        this.ls(); // Recargar tabla
    } else {
        alert({ icon: "error", text: response.message });
    }
}
```

**Errores de red:**
```javascript
useFetch({
    url: api,
    data: { opc: 'ls' },
    error: (error) => {
        alert({ 
            icon: "error", 
            text: "Error de conexión. Intente nuevamente." 
        });
    }
});
```

### Backend Errors

**Estructura de respuesta:**
```php
return [
    'status' => 200,  // 200=éxito, 400=validación, 500=error servidor
    'message' => 'Mensaje descriptivo',
    'data' => []  // Opcional
];
```

**Manejo de excepciones:**
```php
try {
    $create = $this->createPayment($data);
    if ($create) {
        return ['status' => 200, 'message' => 'Pago registrado correctamente'];
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    return ['status' => 500, 'message' => 'Error al registrar el pago'];
}
```

**Validaciones:**
```php
// Validar proveedor existe
$supplier = $this->getSupplierById([$_POST['supplier_id']]);
if (!$supplier) {
    return ['status' => 400, 'message' => 'Proveedor no válido'];
}

// Validar monto
if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
    return ['status' => 400, 'message' => 'Monto no válido'];
}
```

## Testing Strategy

### Unit Tests

**Frontend (JavaScript):**
- Validación de formularios
- Cálculo de totales
- Formato de moneda
- Manejo de respuestas AJAX

**Backend (PHP):**
- Métodos del modelo (CRUD)
- Cálculo de totales
- Validaciones de datos
- Sanitización de inputs

### Integration Tests

- Flujo completo de agregar pago
- Flujo completo de editar pago
- Flujo completo de eliminar pago
- Actualización de totales después de operaciones
- Carga de filtros (proveedores)

### Manual Tests

**Casos de prueba:**

1. **Agregar pago válido**
   - Llenar formulario con datos válidos
   - Verificar que se guarda correctamente
   - Verificar que aparece en la tabla
   - Verificar que los totales se actualizan

2. **Agregar pago inválido**
   - Intentar guardar sin proveedor → Error
   - Intentar guardar sin tipo de pago → Error
   - Intentar guardar sin monto → Error
   - Intentar guardar con monto negativo → Error

3. **Editar pago**
   - Abrir modal de edición
   - Verificar que datos se precargan
   - Modificar campos
   - Guardar y verificar cambios

4. **Eliminar pago**
   - Click en eliminar
   - Verificar modal de confirmación
   - Confirmar eliminación
   - Verificar que desaparece de la tabla
   - Verificar que totales se actualizan

5. **Cálculo de totales**
   - Agregar pago tipo "Fondo fijo" → Verificar total
   - Agregar pago tipo "Corporativo" → Verificar total
   - Verificar total general
   - Editar tipo de pago → Verificar recálculo
   - Eliminar pago → Verificar recálculo

### Performance Tests

- Carga de tabla con 100+ registros
- Tiempo de respuesta de cálculo de totales
- Tiempo de carga de filtros (proveedores)

## Security Considerations

**Autenticación:**
- Validar sesión activa en todas las peticiones
- Verificar permisos de usuario para el módulo

**Autorización:**
- Validar que usuario solo acceda a datos de su UDN
- Filtrar consultas por `udn_id` del usuario

**Validación de Datos:**
- Sanitizar todos los inputs con `$this->util->sql()`
- Validar tipos de datos en backend
- Prevenir SQL Injection usando prepared statements
- Prevenir XSS escapando outputs

**Soft Delete:**
- No eliminar físicamente registros
- Usar campo `active = 0` para eliminación lógica
- Mantener auditoría de cambios con timestamps

## Deployment Notes

**Archivos a crear:**
```
contabilidad/captura/
├── pago-proveedor.js
├── ctrl/
│   └── ctrl-pago-proveedor.php
└── mdl/
    └── mdl-pago-proveedor.php
```

**Dependencias:**
- Framework CoffeeSoft (coffeSoft.js, plugins.js)
- jQuery 3.x
- TailwindCSS
- SweetAlert2
- Bootbox

**Base de datos:**
- Ejecutar script de creación de tabla `supplier_payment`
- Verificar que tabla `supplier` existe
- Crear índices para optimización

**Configuración:**
- Actualizar menú de navegación para incluir módulo
- Configurar permisos de usuario
- Verificar variable `$_POST['udn']` en filtros
