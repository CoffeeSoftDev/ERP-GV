# Design Document

## Overview

Este diseño implementa la funcionalidad de edición de registros de ventas diarias en el módulo de ventas. La solución se integra con la arquitectura existente de CoffeeSoft, utilizando el patrón MVC y manteniendo la consistencia con los componentes actuales del sistema.

## Architecture

### Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend (JS)                            │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Sales Class (kpi-ventas.js)                           │ │
│  │  ├─ listSales()          // Renderiza tabla           │ │
│  │  ├─ editSale(id)         // Abre modal de edición     │ │
│  │  └─ jsonEditSale()       // Define campos del form    │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              ↓ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Backend (PHP)                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  ctrl-ingresos.php                                     │ │
│  │  ├─ list()              // Lista registros            │ │
│  │  ├─ getSale()           // Obtiene registro por ID    │ │
│  │  └─ editSale()          // Actualiza registro         │ │
│  └────────────────────────────────────────────────────────┘ │
│                              ↓                               │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  mdl-ingresos.php                                      │ │
│  │  ├─ getSaleById()       // Query SELECT por ID        │ │
│  │  └─ updateSale()        // Query UPDATE               │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                   Database (MySQL)                           │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  soft_ventas_fecha                                     │ │
│  │  ├─ id_venta (PK)                                      │ │
│  │  ├─ soft_ventas_fecha                                  │ │
│  │  ├─ id_area (FK)                                       │ │
│  │  ├─ personas / noHabitaciones                          │ │
│  │  ├─ alimentos, bebidas, AyB, Hospedaje, etc.          │ │
│  │  └─ total                                              │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Frontend Components

#### 1. Sales.listSales() - Modificación

**Propósito:** Agregar columna de acciones con botón de edición

**Cambios:**
```javascript
// En la configuración de createTable, agregar:
attr: {
    id: "tbIngresos",
    theme: 'corporativo',
    color_group: "bg-gray-300",
    right: [4, 5]  // Agregar columna 5 para acciones
}
```

**Lógica del botón:**
- Solo visible cuando `Estado === "Capturado"`
- Llama a `sales.editSale(id_venta)` al hacer clic

#### 2. Sales.editSale(id) - Nuevo Método

**Propósito:** Abrir modal con formulario de edición

**Firma:**
```javascript
async editSale(id) {
    // 1. Obtener datos del registro
    const request = await useFetch({
        url: this._link,
        data: { opc: "getSale", id: id }
    });
    
    // 2. Abrir modal con formulario
    this.createModalForm({
        id: 'formEditSale',
        data: { opc: 'editSale', id: id },
        autofill: request.data,
        json: this.jsonEditSale(),
        bootbox: {
            title: `Editar Venta - ${request.data.fecha}`,
            closeButton: true
        },
        success: (response) => {
            if (response.status === 200) {
                alert({ icon: "success", text: response.message });
                this.listSales(); // Refrescar tabla
            } else {
                alert({ icon: "error", text: response.message });
            }
        }
    });
}
```

#### 3. Sales.jsonEditSale() - Nuevo Método

**Propósito:** Definir estructura del formulario según UDN

**Firma:**
```javascript
jsonEditSale() {
    const udn = $('#filterBarsales #udn').val();
    let fields = [
        {
            opc: "input",
            id: "noHabitaciones",
            lbl: udn == 1 ? "No. Habitaciones" : "Clientes",
            tipo: "numero",
            class: "col-12 col-md-6 mb-3"
        }
    ];
    
    // Campos específicos por UDN
    if (udn == 1) {
        fields.push(
            { opc: "input", id: "Hospedaje", lbl: "Hospedaje", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "AyB", lbl: "Alimentos y Bebidas", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "Diversos", lbl: "Diversos", tipo: "cifra", class: "col-12 col-md-6 mb-3" }
        );
    } else if (udn == 5) {
        fields.push(
            { opc: "input", id: "alimentos", lbl: "Alimentos", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "bebidas", lbl: "Bebidas", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "guarniciones", lbl: "Guarniciones", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "sales", lbl: "Sales", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "domicilio", lbl: "Domicilio", tipo: "cifra", class: "col-12 col-md-6 mb-3" }
        );
    } else {
        fields.push(
            { opc: "input", id: "alimentos", lbl: "Alimentos", tipo: "cifra", class: "col-12 col-md-6 mb-3" },
            { opc: "input", id: "bebidas", lbl: "Bebidas", tipo: "cifra", class: "col-12 col-md-6 mb-3" }
        );
    }
    
    fields.push({ opc: "btn-submit", text: "Guardar Cambios", class: "col-12" });
    
    return fields;
}
```

### Backend Components

#### 1. ctrl-ingresos.php - Método getSale()

**Propósito:** Obtener datos de un registro específico

**Firma:**
```php
function getSale() {
    $id = $_POST['id'];
    $status = 404;
    $message = 'Registro no encontrado';
    $data = null;
    
    $sale = $this->getSaleById([$id]);
    
    if ($sale) {
        $status = 200;
        $message = 'Registro encontrado';
        $data = $sale;
    }
    
    return [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
}
```

#### 2. ctrl-ingresos.php - Método editSale()

**Propósito:** Actualizar registro de venta

**Firma:**
```php
function editSale() {
    $id = $_POST['id'];
    $status = 500;
    $message = 'Error al actualizar el registro';
    
    // Validaciones
    $noHabitaciones = floatval($_POST['noHabitaciones'] ?? 0);
    if ($noHabitaciones < 0) {
        return [
            'status' => 400,
            'message' => 'El número de habitaciones/clientes no puede ser negativo'
        ];
    }
    
    // Calcular total según UDN
    $udn = $_POST['udn'] ?? 1;
    if ($udn == 1) {
        $total = floatval($_POST['Hospedaje'] ?? 0) + 
                 floatval($_POST['AyB'] ?? 0) + 
                 floatval($_POST['Diversos'] ?? 0);
    } elseif ($udn == 5) {
        $total = floatval($_POST['alimentos'] ?? 0) + 
                 floatval($_POST['bebidas'] ?? 0) + 
                 floatval($_POST['guarniciones'] ?? 0) + 
                 floatval($_POST['sales'] ?? 0) + 
                 floatval($_POST['domicilio'] ?? 0);
    } else {
        $total = floatval($_POST['alimentos'] ?? 0) + 
                 floatval($_POST['bebidas'] ?? 0);
    }
    
    $_POST['total'] = $total;
    
    $update = $this->updateSale($this->util->sql($_POST, 1));
    
    if ($update) {
        $status = 200;
        $message = 'Registro actualizado correctamente';
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

#### 3. mdl-ingresos.php - Método getSaleById()

**Propósito:** Query SELECT para obtener registro

**Firma:**
```php
function getSaleById($array) {
    $query = "
        SELECT *
        FROM {$this->bd}soft_ventas_fecha
        WHERE id_venta = ?
    ";
    $result = $this->_Read($query, $array);
    return $result[0] ?? null;
}
```

#### 4. mdl-ingresos.php - Método updateSale()

**Propósito:** Query UPDATE para actualizar registro

**Firma:**
```php
function updateSale($array) {
    return $this->_Update([
        'table' => "{$this->bd}soft_ventas_fecha",
        'values' => $array['values'],
        'where' => 'id_venta = ?',
        'data' => $array['data']
    ]);
}
```

### Backend - Modificación en list()

**Propósito:** Agregar columna de acciones en la respuesta

**Cambios en lsIngresosCaptura():**
```php
// Después de definir $row, agregar:
$row['acciones'] = [
    'html' => $softVentas['id_venta'] 
        ? '<button class="btn btn-sm btn-primary" onclick="sales.editSale(' . $softVentas['id_venta'] . ')">
               <i class="icon-pencil"></i>
           </button>'
        : '',
    'class' => 'text-center'
];
```

## Data Models

### Database Schema

**Tabla:** `soft_ventas_fecha`

```sql
CREATE TABLE soft_ventas_fecha (
    id_venta INT PRIMARY KEY AUTO_INCREMENT,
    soft_ventas_fecha TIMESTAMP,
    id_area INT,
    soft_folio INT,
    personas INT,           -- También conocido como noHabitaciones
    alimentos DOUBLE,
    bebidas DOUBLE,
    AyB DOUBLE,
    guarniciones DOUBLE,
    domicilio DOUBLE,
    otros DOUBLE,
    Diversos DOUBLE,
    Hospedaje DOUBLE,
    sales DOUBLE,
    subtotal DOUBLE,
    iva DOUBLE,
    total DOUBLE,
    cuentas INT,
    productosvendidos DOUBLE,
    noHabitaciones DOUBLE,
    costoAmenidades TEXT,
    costoAyB TEXT
);
```

### Data Flow

#### 1. Obtener Registro (GET)

```
Frontend                Backend                 Database
   |                       |                        |
   |-- getSale(id) ------->|                        |
   |                       |-- getSaleById() ------>|
   |                       |<----- result ----------|
   |<--- response ---------|                        |
   |                       |                        |
```

**Request:**
```javascript
{
    opc: "getSale",
    id: 123
}
```

**Response:**
```javascript
{
    status: 200,
    message: "Registro encontrado",
    data: {
        id_venta: 123,
        soft_ventas_fecha: "2024-01-15",
        id_area: 1,
        personas: 50,
        alimentos: 15000.00,
        bebidas: 8000.00,
        total: 23000.00,
        // ... otros campos
    }
}
```

#### 2. Actualizar Registro (UPDATE)

```
Frontend                Backend                 Database
   |                       |                        |
   |-- editSale(data) ---->|                        |
   |                       |-- validate() --------->|
   |                       |-- updateSale() ------->|
   |                       |<----- success ---------|
   |<--- response ---------|                        |
   |                       |                        |
```

**Request:**
```javascript
{
    opc: "editSale",
    id: 123,
    noHabitaciones: 55,
    alimentos: 16000.00,
    bebidas: 8500.00,
    udn: 2
}
```

**Response:**
```javascript
{
    status: 200,
    message: "Registro actualizado correctamente"
}
```

## Error Handling

### Frontend Error Handling

1. **Error de Red:**
```javascript
try {
    const request = await useFetch({...});
} catch (error) {
    alert({
        icon: "error",
        text: "Error de conexión. Por favor intente nuevamente."
    });
}
```

2. **Error de Validación:**
```javascript
if (response.status === 400) {
    alert({
        icon: "warning",
        text: response.message
    });
}
```

3. **Error del Servidor:**
```javascript
if (response.status === 500) {
    alert({
        icon: "error",
        text: "Error del servidor. Contacte al administrador."
    });
}
```

### Backend Error Handling

1. **Validación de Datos:**
```php
// Validar que el ID exista
if (!$sale) {
    return [
        'status' => 404,
        'message' => 'Registro no encontrado'
    ];
}

// Validar valores numéricos
if ($noHabitaciones < 0) {
    return [
        'status' => 400,
        'message' => 'El número de clientes no puede ser negativo'
    ];
}
```

2. **Error de Base de Datos:**
```php
try {
    $update = $this->updateSale($data);
    if (!$update) {
        throw new Exception('Error al actualizar');
    }
} catch (Exception $e) {
    return [
        'status' => 500,
        'message' => 'Error al actualizar el registro'
    ];
}
```

## Testing Strategy

### Unit Tests

#### Frontend Tests

1. **Test: editSale() abre modal correctamente**
```javascript
// Verificar que se llama a createModalForm
// Verificar que se pasa el ID correcto
// Verificar que se cargan los datos del registro
```

2. **Test: jsonEditSale() retorna campos correctos por UDN**
```javascript
// UDN 1: Verificar campos Hospedaje, AyB, Diversos
// UDN 5: Verificar campos alimentos, bebidas, guarniciones, sales, domicilio
// UDN otros: Verificar campos alimentos, bebidas
```

#### Backend Tests

1. **Test: getSale() retorna registro existente**
```php
// Caso: ID válido -> status 200
// Caso: ID inválido -> status 404
```

2. **Test: editSale() valida datos correctamente**
```php
// Caso: Datos válidos -> status 200
// Caso: Valores negativos -> status 400
// Caso: ID inexistente -> status 404
```

3. **Test: updateSale() actualiza correctamente**
```php
// Verificar que el registro se actualiza en BD
// Verificar que el total se calcula correctamente
```

### Integration Tests

1. **Test: Flujo completo de edición**
```
1. Cargar tabla de ventas
2. Hacer clic en botón editar
3. Modificar datos en modal
4. Guardar cambios
5. Verificar que la tabla se actualiza
```

2. **Test: Validación de permisos**
```
1. Verificar que solo registros capturados tienen botón editar
2. Verificar que registros pendientes no tienen botón editar
```

### Manual Testing Checklist

- [ ] El botón de edición aparece solo en registros capturados
- [ ] El modal se abre con los datos correctos
- [ ] Los campos se muestran según la UDN seleccionada
- [ ] La validación de campos numéricos funciona
- [ ] Los cambios se guardan correctamente en la base de datos
- [ ] La tabla se refresca después de guardar
- [ ] Los mensajes de error se muestran correctamente
- [ ] El botón cancelar cierra el modal sin guardar
- [ ] Los filtros se mantienen después de editar

## Design Decisions

### 1. Uso de Modal vs Página Separada

**Decisión:** Usar modal (createModalForm)

**Razones:**
- Consistencia con el patrón existente en el sistema
- Mejor UX: el usuario no pierde el contexto de la tabla
- Menos navegación entre páginas
- Más rápido de implementar con componentes existentes

### 2. Validación en Frontend y Backend

**Decisión:** Implementar validación en ambos lados

**Razones:**
- Frontend: Mejor UX con feedback inmediato
- Backend: Seguridad y integridad de datos
- Prevención de ataques de manipulación de requests

### 3. Cálculo de Total en Backend

**Decisión:** Calcular el total en el backend, no confiar en el frontend

**Razones:**
- Seguridad: evitar manipulación de totales
- Consistencia: lógica de negocio centralizada
- Mantenibilidad: un solo lugar para cambiar la lógica

### 4. Estructura de Respuesta Estándar

**Decisión:** Usar formato { status, message, data }

**Razones:**
- Consistencia con el resto del sistema
- Facilita el manejo de errores
- Permite mensajes descriptivos al usuario

## Performance Considerations

1. **Carga de Datos:**
   - El método `getSale()` solo carga un registro, impacto mínimo
   - Query optimizado con índice en `id_venta`

2. **Actualización:**
   - UPDATE afecta solo un registro
   - No hay queries adicionales innecesarios

3. **Refresco de Tabla:**
   - Se reutiliza el método `listSales()` existente
   - No se recarga toda la página, solo la tabla

## Security Considerations

1. **Validación de Input:**
   - Todos los valores numéricos se validan en backend
   - Se usa `$this->util->sql()` para prevenir SQL injection

2. **Autorización:**
   - Se asume que el sistema ya tiene control de sesión
   - Solo usuarios autenticados pueden acceder

3. **Integridad de Datos:**
   - El total se calcula en backend, no se confía en el frontend
   - Se validan rangos de valores (no negativos)

## Future Enhancements

1. **Historial de Cambios:**
   - Registrar quién y cuándo modificó cada registro
   - Tabla de auditoría para cambios

2. **Validaciones Avanzadas:**
   - Validar rangos razonables según histórico
   - Alertas de valores atípicos

3. **Edición en Línea:**
   - Permitir editar directamente en la tabla
   - Guardar automáticamente al cambiar de campo

4. **Permisos Granulares:**
   - Diferentes niveles de acceso (solo lectura, edición, etc.)
   - Restricciones por UDN o fecha
