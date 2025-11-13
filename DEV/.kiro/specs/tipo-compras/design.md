# Design Document - Módulo Tipos de Compra

## Overview

El módulo de Tipos de Compra es un submódulo dentro del sistema de Contabilidad/Administración que permite gestionar los diferentes tipos de compra utilizados en las unidades de negocio. El diseño sigue el patrón arquitectónico MVC (Modelo-Vista-Controlador) del framework CoffeeSoft, con una interfaz basada en jQuery y TailwindCSS.

## Architecture

### Patrón Arquitectónico
- **MVC (Model-View-Controller)**
  - **Model (mdl-tipo-compras.php)**: Gestiona el acceso a datos y operaciones CRUD en la tabla `tipo_compra`
  - **Controller (ctrl-tipo-compras.php)**: Procesa las peticiones del frontend y coordina la lógica de negocio
  - **View (tipo-compras.js)**: Maneja la interfaz de usuario y la interacción con el usuario

### Integración con el Sistema
- El módulo se integra como una pestaña adicional dentro del módulo de Administración
- Comparte la estructura de navegación y estilos con otros submódulos (Formas de Pago, Clientes, Proveedores)
- Utiliza el API endpoint `ctrl/ctrl-tipo-compras.php` para todas las operaciones

## Components and Interfaces

### Frontend Components (tipo-compras.js)

#### Clase Principal: `PurchaseType`
```javascript
class PurchaseType extends Templates {
    constructor(link, div_modulo)
    PROJECT_NAME: "tipoCompras"
}
```

**Métodos principales:**
- `render()`: Inicializa el módulo y renderiza la interfaz
- `layout()`: Crea la estructura base del contenedor
- `filterBar()`: Genera la barra de filtros con el botón "Agregar nuevo tipo de compra"
- `lsTipoCompras()`: Lista todos los tipos de compra en una tabla
- `addTipoCompra()`: Muestra modal para agregar nuevo tipo
- `editTipoCompra(id)`: Muestra modal para editar tipo existente
- `statusTipoCompra(id, active)`: Cambia el estado activo/inactivo del tipo

#### Componentes CoffeeSoft Utilizados
1. **primaryLayout**: Estructura base con filterBar y container
2. **createfilterBar**: Barra de filtros con botón de agregar
3. **createTable**: Tabla dinámica con datos del backend
4. **createModalForm**: Modales para agregar/editar
5. **swalQuestion**: Diálogos de confirmación para activar/desactivar

### Backend Components

#### Controlador (ctrl-tipo-compras.php)

**Clase:** `ctrl extends mdl`

**Métodos:**
- `init()`: Retorna datos iniciales (si se requieren filtros adicionales)
- `ls()`: Lista todos los tipos de compra con formato para tabla
- `getTipoCompra()`: Obtiene un tipo de compra específico por ID
- `addTipoCompra()`: Crea un nuevo tipo de compra
- `editTipoCompra()`: Actualiza un tipo de compra existente
- `statusTipoCompra()`: Cambia el estado activo/inactivo

**Funciones auxiliares:**
- `renderStatus($active)`: Genera HTML para mostrar el estado (Activo/Inactivo)

#### Modelo (mdl-tipo-compras.php)

**Clase:** `mdl extends CRUD`

**Propiedades:**
- `$bd`: Nombre de la base de datos (rfwsmqex_contabilidad)
- `$util`: Instancia de la clase Utileria

**Métodos:**
- `listTipoCompras($array)`: Consulta todos los tipos de compra
- `getTipoCompraById($id)`: Obtiene un tipo de compra por ID
- `existsTipoCompraByName($array)`: Valida si existe un tipo con el mismo nombre
- `createTipoCompra($array)`: Inserta un nuevo tipo de compra
- `updateTipoCompra($array)`: Actualiza un tipo de compra existente

## Data Models

### Tabla: tipo_compra

```sql
CREATE TABLE tipo_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activo (activo),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `id`: Identificador único (clave primaria)
- `nombre`: Nombre del tipo de compra (ej: Corporativo, Fondo fijo, Crédito)
- `activo`: Estado del tipo de compra (1 = activo, 0 = inactivo)
- `date_creation`: Fecha y hora de creación del registro

**Índices:**
- `idx_activo`: Optimiza consultas por estado
- `idx_nombre`: Optimiza búsquedas y validaciones por nombre

### Estructura de Datos Frontend

#### Formato de Tabla (ls)
```javascript
{
    row: [
        {
            id: 1,
            'Tipo de compra': 'Corporativo',
            'Estado': '<span class="badge">Activo</span>',
            a: [
                { class: 'btn btn-sm btn-primary', html: '<i class="icon-pencil"></i>', onclick: 'purchaseType.editTipoCompra(1)' },
                { class: 'btn btn-sm btn-danger', html: '<i class="icon-toggle-on"></i>', onclick: 'purchaseType.statusTipoCompra(1, 1)' }
            ]
        }
    ]
}
```

#### Formato de Formulario (add/edit)
```javascript
[
    {
        opc: "input",
        id: "nombre",
        lbl: "Nombre del tipo de compra",
        placeholder: "Ej: Corporativo, Fondo fijo, Crédito",
        class: "col-12 mb-3",
        required: true
    }
]
```

## Error Handling

### Validaciones Frontend
1. **Campo vacío**: Validación automática con atributo `required`
2. **Duplicados**: Mensaje de error si el nombre ya existe
3. **Confirmación de cambios**: Modal de confirmación antes de activar/desactivar

### Validaciones Backend
1. **Nombre vacío**: Retorna `status: 400` con mensaje de error
2. **Nombre duplicado**: Retorna `status: 409` con mensaje "Ya existe un tipo de compra con ese nombre"
3. **Registro no encontrado**: Retorna `status: 404` con mensaje de error
4. **Error de base de datos**: Retorna `status: 500` con mensaje genérico

### Mensajes de Error Estándar
```javascript
{
    status: 400,  // Bad Request
    message: "El nombre del tipo de compra es obligatorio"
}

{
    status: 409,  // Conflict
    message: "Ya existe un tipo de compra con ese nombre"
}

{
    status: 500,  // Internal Server Error
    message: "Error al procesar la solicitud"
}
```

### Mensajes de Éxito
```javascript
{
    status: 200,
    message: "Tipo de compra agregado correctamente"
}

{
    status: 200,
    message: "Tipo de compra actualizado correctamente"
}

{
    status: 200,
    message: "Estado actualizado correctamente"
}
```

## Testing Strategy

### Pruebas Unitarias (Modelo)
1. **Crear tipo de compra**: Verificar inserción correcta en BD
2. **Validar duplicados**: Verificar que detecta nombres existentes
3. **Actualizar tipo**: Verificar actualización correcta de campos
4. **Cambiar estado**: Verificar cambio de activo/inactivo

### Pruebas de Integración (Controlador)
1. **Flujo completo de creación**: POST → validación → inserción → respuesta
2. **Flujo de edición**: GET → modificación → POST → actualización
3. **Flujo de cambio de estado**: POST → actualización → respuesta

### Pruebas de Interfaz (Frontend)
1. **Renderizado de tabla**: Verificar que muestra todos los registros
2. **Modal de agregar**: Verificar apertura y cierre correcto
3. **Modal de editar**: Verificar prellenado de datos
4. **Modal de confirmación**: Verificar mensajes y acciones
5. **Actualización automática**: Verificar que la tabla se actualiza después de cada operación

### Casos de Prueba Específicos

#### Caso 1: Crear tipo de compra exitoso
- **Input**: nombre = "Corporativo"
- **Expected**: status 200, registro creado, tabla actualizada

#### Caso 2: Crear tipo duplicado
- **Input**: nombre = "Corporativo" (ya existe)
- **Expected**: status 409, mensaje de error, no se crea registro

#### Caso 3: Editar tipo de compra
- **Input**: id = 1, nombre = "Corporativo Actualizado"
- **Expected**: status 200, registro actualizado, tabla actualizada

#### Caso 4: Desactivar tipo de compra
- **Input**: id = 1, activo = 0
- **Expected**: status 200, estado cambiado, estilos visuales actualizados

#### Caso 5: Reactivar tipo de compra
- **Input**: id = 1, activo = 1
- **Expected**: status 200, estado cambiado, estilos visuales actualizados

## UI/UX Considerations

### Diseño Visual
- **Tema**: Corporativo (azul #003360)
- **Framework CSS**: TailwindCSS
- **Iconos**: Font Awesome / Icon Font
- **Responsive**: Adaptable a móviles, tablets y desktop

### Interacciones
1. **Hover effects**: Botones y filas de tabla
2. **Loading states**: Indicadores durante peticiones AJAX
3. **Feedback visual**: Mensajes de éxito/error con SweetAlert2
4. **Confirmaciones**: Modales para acciones destructivas

### Accesibilidad
- Labels descriptivos en todos los inputs
- Atributos ARIA en elementos interactivos
- Contraste de colores adecuado
- Navegación por teclado funcional

## Integration Points

### Integración con Módulo de Administración
```javascript
// En admin.js
let api_tipoCompras = 'ctrl/ctrl-tipo-compras.php';
let purchaseType;

purchaseType = new PurchaseType(api_tipoCompras, "root");

// Agregar tab en layoutTabs()
{
    id: "tipo-compras",
    tab: "Tipos de compra",
    onClick: () => purchaseType.lsTipoCompras()
}
```

### Estructura de Archivos
```
contabilidad/
└── administrador/
    ├── index.php
    ├── js/
    │   ├── admin.js (modificado)
    │   └── tipo-compras.js (nuevo)
    ├── ctrl/
    │   └── ctrl-tipo-compras.php (nuevo)
    └── mdl/
        └── mdl-tipo-compras.php (nuevo)
```

## Performance Considerations

### Optimizaciones
1. **Índices en BD**: Índices en campos `activo` y `nombre`
2. **Caché de consultas**: Uso de prepared statements
3. **Lazy loading**: Carga de datos solo cuando se accede al tab
4. **Paginación**: DataTables con paginación de 15 registros

### Métricas Esperadas
- Tiempo de carga inicial: < 500ms
- Tiempo de respuesta CRUD: < 200ms
- Tamaño de payload: < 50KB por petición

## Security Considerations

### Validaciones
1. **SQL Injection**: Uso de prepared statements en todas las consultas
2. **XSS**: Sanitización de inputs con `htmlspecialchars()`
3. **CSRF**: Validación de sesión en todas las peticiones
4. **Autorización**: Verificación de permisos de usuario

### Sanitización de Datos
```php
// En modelo
$this->util->sql($_POST)  // Sanitiza todos los campos POST
```

### Validación de Sesión
```php
// En controlador
session_start();
if (empty($_POST['opc'])) exit(0);
```
