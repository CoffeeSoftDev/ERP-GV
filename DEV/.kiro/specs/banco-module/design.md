# Design Document - Módulo de Banco

## Overview

El módulo de Banco es un submódulo del sistema contable CoffeeSoft que implementa la gestión completa de instituciones bancarias y sus cuentas asociadas. Utiliza la arquitectura MVC del framework CoffeeSoft con componentes reutilizables basados en jQuery y TailwindCSS.

### Objetivos del Diseño

- Proporcionar una interfaz intuitiva para administrar bancos y cuentas bancarias
- Mantener la integridad referencial entre bancos, cuentas y unidades de negocio
- Permitir control de estados sin pérdida de datos históricos
- Seguir los patrones establecidos en el pivote de administración (moneda)
- Integrar validaciones robustas en frontend y backend

## Architecture

### Estructura de Archivos

```
contabilidad/administrador/
├── banco.php                    # Vista principal (punto de entrada)
├── js/
│   └── banco.js                 # Frontend JavaScript (extiende Templates)
├── ctrl/
│   └── ctrl-banco.php           # Controlador PHP (lógica de negocio)
└── mdl/
    └── mdl-banco.php            # Modelo PHP (acceso a datos)
```

### Patrón Arquitectónico

**MVC (Model-View-Controller)**

- **Model (mdl-banco.php)**: Gestiona operaciones CRUD en base de datos
- **Controller (ctrl-banco.php)**: Procesa peticiones, valida datos y coordina modelo
- **View (banco.js)**: Renderiza interfaz, maneja eventos y consume API del controlador

### Flujo de Datos

```
Usuario → banco.js (Frontend)
    ↓
ctrl-banco.php (Controlador)
    ↓
mdl-banco.php (Modelo)
    ↓
Base de Datos MySQL
```

## Components and Interfaces

### Frontend Components (banco.js)

#### Clase Principal: AdminBankAccounts

Extiende la clase `Templates` del framework CoffeeSoft.

**Métodos Principales:**

```javascript
class AdminBankAccounts extends Templates {
    constructor(link, div_modulo)
    render()                    // Inicializa el módulo
    layout()                    // Crea estructura visual con primaryLayout
    filterBar()                 // Barra de filtros (UDN, forma de pago)
    lsBankAccounts()           // Lista cuentas bancarias en tabla
    addBank()                   // Modal para agregar banco
    addBankAccount()           // Modal para agregar cuenta bancaria
    editBankAccount(id)        // Modal para editar cuenta
    toggleStatusAccount(id, status) // Activar/desactivar cuenta
    jsonBankForm()             // Estructura JSON del formulario de banco
    jsonAccountForm()          // Estructura JSON del formulario de cuenta
}
```

**Componentes CoffeeSoft Utilizados:**

- `primaryLayout()`: Layout principal con filterBar y container
- `createfilterBar()`: Barra de filtros con selects dinámicos
- `createTable()`: Tabla dinámica con paginación y acciones
- `createModalForm()`: Formularios modales con validación
- `swalQuestion()`: Diálogos de confirmación
- `useFetch()`: Peticiones AJAX asíncronas

### Backend Components

#### Controlador (ctrl-banco.php)

**Clase:** `ctrl extends mdl`

**Métodos:**

```php
init()                      // Inicializa filtros (UDN, formas de pago, bancos)
lsBankAccounts()           // Lista cuentas bancarias con filtros
getBank()                   // Obtiene datos de un banco por ID
getBankAccount()           // Obtiene datos de una cuenta por ID
addBank()                   // Crea nuevo banco
addBankAccount()           // Crea nueva cuenta bancaria
editBankAccount()          // Actualiza cuenta bancaria
toggleStatusAccount()      // Cambia estado de cuenta (activa/inactiva)
```

**Funciones Auxiliares:**

```php
renderStatus($status)      // Genera badge HTML según estado
formatAccountNumber($last4) // Formatea últimos 4 dígitos
```

#### Modelo (mdl-banco.php)

**Clase:** `mdl extends CRUD`

**Métodos de Consulta:**

```php
listBanks($array)              // Lista bancos activos
listBankAccounts($array)       // Lista cuentas con filtros
getBankById($array)            // Obtiene banco por ID
getBankAccountById($array)     // Obtiene cuenta por ID
lsUDN()                        // Lista unidades de negocio
lsPaymentMethods()             // Lista formas de pago
```

**Métodos CRUD:**

```php
createBank($array)             // Inserta nuevo banco
createBankAccount($array)      // Inserta nueva cuenta
updateBankAccount($array)      // Actualiza cuenta existente
existsBankByName($array)       // Valida duplicidad de banco
```

## Data Models

### Tabla: banks

Almacena instituciones bancarias registradas.

```sql
CREATE TABLE banks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bank_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**

- `id`: Identificador único del banco
- `name`: Nombre de la institución bancaria
- `active`: Estado (1=activo, 0=inactivo)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de última actualización

### Tabla: bank_accounts

Almacena cuentas bancarias vinculadas a bancos y UDN.

```sql
CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT NOT NULL,
    bank_id INT NOT NULL,
    account_alias VARCHAR(100),
    last_four_digits CHAR(4) NOT NULL,
    payment_method_id INT,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bank_id) REFERENCES banks(id),
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    INDEX idx_udn_bank (udn_id, bank_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**

- `id`: Identificador único de la cuenta
- `udn_id`: Referencia a unidad de negocio
- `bank_id`: Referencia al banco
- `account_alias`: Nombre o alias opcional de la cuenta
- `last_four_digits`: Últimos 4 dígitos de la cuenta (CHAR(4))
- `payment_method_id`: Referencia a forma de pago
- `active`: Estado operativo (1=activa, 0=inactiva)
- `created_at`: Fecha de creación
- `updated_at`: Fecha de última actualización

### Relaciones

```
udn (1) ──────< (N) bank_accounts
banks (1) ─────< (N) bank_accounts
payment_methods (1) ──< (N) bank_accounts
```

## Error Handling

### Frontend Validation

**Validaciones en banco.js:**

1. **Formulario de Banco:**
   - Campo `name` obligatorio (required)
   - Longitud mínima: 3 caracteres
   - Validación de duplicidad en backend

2. **Formulario de Cuenta Bancaria:**
   - Campo `bank_id` obligatorio
   - Campo `last_four_digits` obligatorio
   - Validación de formato: exactamente 4 dígitos numéricos
   - Regex: `/^\d{4}$/`

**Manejo de Errores:**

```javascript
success: (response) => {
    if (response.status === 200) {
        alert({ icon: "success", text: response.message });
        this.lsBankAccounts();
    } else {
        alert({ icon: "error", text: response.message });
    }
}
```

### Backend Validation

**Validaciones en ctrl-banco.php:**

1. **Duplicidad de Bancos:**
   ```php
   if ($this->existsBankByName([$_POST['name']])) {
       return ['status' => 409, 'message' => 'Ya existe un banco con ese nombre'];
   }
   ```

2. **Formato de Últimos 4 Dígitos:**
   ```php
   if (!preg_match('/^\d{4}$/', $_POST['last_four_digits'])) {
       return ['status' => 400, 'message' => 'Los últimos 4 dígitos deben ser numéricos'];
   }
   ```

3. **Validación de Referencias:**
   - Verificar existencia de `bank_id` antes de crear cuenta
   - Verificar existencia de `udn_id` en tabla `udn`

### Error Response Format

```json
{
    "status": 200|400|404|409|500,
    "message": "Descripción del resultado",
    "data": {} // Opcional
}
```

**Códigos de Estado:**

- `200`: Operación exitosa
- `400`: Datos inválidos
- `404`: Registro no encontrado
- `409`: Conflicto (duplicidad)
- `500`: Error del servidor

## Testing Strategy

### Unit Tests

**Frontend (banco.js):**

1. **Test de Renderizado:**
   - Verificar que `layout()` crea estructura correcta
   - Validar que `filterBar()` genera filtros dinámicos
   - Comprobar que `lsBankAccounts()` renderiza tabla

2. **Test de Validación:**
   - Validar formato de 4 dígitos numéricos
   - Verificar campos obligatorios en formularios
   - Comprobar mensajes de error

3. **Test de Eventos:**
   - Simular clic en "Agregar banco"
   - Simular clic en "Agregar cuenta"
   - Simular toggle de estado

**Backend (ctrl-banco.php):**

1. **Test de Métodos CRUD:**
   - `addBank()` con datos válidos
   - `addBank()` con nombre duplicado
   - `addBankAccount()` con datos válidos
   - `addBankAccount()` con formato inválido
   - `editBankAccount()` con ID existente
   - `toggleStatusAccount()` cambia estado correctamente

2. **Test de Validaciones:**
   - Duplicidad de nombres de banco
   - Formato de últimos 4 dígitos
   - Referencias foráneas válidas

**Modelo (mdl-banco.php):**

1. **Test de Consultas:**
   - `listBanks()` retorna datos correctos
   - `listBankAccounts()` aplica filtros correctamente
   - `getBankById()` retorna banco existente
   - `getBankAccountById()` retorna cuenta existente

2. **Test de Operaciones:**
   - `createBank()` inserta correctamente
   - `createBankAccount()` inserta con referencias válidas
   - `updateBankAccount()` actualiza campos correctos

### Integration Tests

1. **Flujo Completo de Banco:**
   - Crear banco → Verificar en lista → Editar → Verificar cambios

2. **Flujo Completo de Cuenta:**
   - Crear cuenta → Verificar en tabla → Editar → Cambiar estado → Verificar histórico

3. **Filtros Dinámicos:**
   - Filtrar por UDN → Verificar resultados
   - Filtrar por forma de pago → Verificar resultados
   - Combinar filtros → Verificar resultados

### Manual Testing Checklist

- [ ] Interfaz se carga correctamente
- [ ] Filtros funcionan y actualizan tabla
- [ ] Modal de nuevo banco se abre y cierra
- [ ] Modal de nueva cuenta se abre y cierra
- [ ] Validación de duplicidad funciona
- [ ] Validación de 4 dígitos funciona
- [ ] Edición de cuenta carga datos correctos
- [ ] Toggle de estado muestra mensajes correctos
- [ ] Tabla se actualiza después de operaciones
- [ ] Mensajes de éxito/error se muestran correctamente

## Design Decisions

### 1. Separación de Bancos y Cuentas

**Decisión:** Crear dos tablas separadas (`banks` y `bank_accounts`) en lugar de una sola.

**Razón:**
- Normalización de datos (evita redundancia)
- Permite múltiples cuentas por banco
- Facilita mantenimiento y escalabilidad
- Sigue principios de diseño relacional

### 2. Campo last_four_digits como CHAR(4)

**Decisión:** Usar tipo `CHAR(4)` en lugar de `INT`.

**Razón:**
- Preserva ceros a la izquierda (ej: "0123")
- Longitud fija garantiza 4 caracteres
- No se realizan operaciones matemáticas
- Facilita validación y formato

### 3. Soft Delete (active flag)

**Decisión:** Usar campo `active` en lugar de eliminar registros.

**Razón:**
- Mantiene integridad de datos históricos
- Permite auditoría y trazabilidad
- Cumple con requisito de no pérdida de datos
- Facilita reactivación de cuentas

### 4. Uso de Templates de CoffeeSoft

**Decisión:** Extender clase `Templates` y usar componentes del framework.

**Razón:**
- Consistencia con el resto del sistema
- Reutilización de código probado
- Reducción de tiempo de desarrollo
- Mantenimiento simplificado

### 5. Validación en Frontend y Backend

**Decisión:** Implementar validaciones duplicadas en ambas capas.

**Razón:**
- Frontend: Mejora experiencia de usuario (feedback inmediato)
- Backend: Garantiza seguridad y consistencia de datos
- Defensa en profundidad contra datos inválidos

### 6. Filtros Dinámicos por UDN

**Decisión:** Filtrar cuentas por unidad de negocio del usuario.

**Razón:**
- Segmentación de datos por contexto operativo
- Reduce complejidad visual
- Mejora rendimiento de consultas
- Alineado con arquitectura multi-tenant del sistema

## Technical Constraints

### Framework Dependencies

- **CoffeeSoft Framework**: Obligatorio para componentes visuales
- **jQuery 3.x**: Requerido por CoffeeSoft
- **TailwindCSS**: Para estilos y diseño responsive
- **PHP 7.4+**: Versión mínima del servidor
- **MySQL 5.7+**: Base de datos relacional

### Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Performance Requirements

- Carga inicial de tabla: < 2 segundos
- Respuesta de filtros: < 500ms
- Operaciones CRUD: < 1 segundo
- Paginación de tabla: 15 registros por página

### Security Considerations

- Validación de sesión en todas las peticiones
- Sanitización de inputs con `$this->util->sql()`
- Prepared statements para prevenir SQL injection
- Validación de permisos por UDN
- HTTPS obligatorio en producción
