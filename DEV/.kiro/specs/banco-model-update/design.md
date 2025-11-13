# Design Document - Actualización del Modelo de Banco

## Overview

Este documento describe el diseño técnico para la actualización del modelo de datos del módulo de banco en el sistema de contabilidad CoffeeSoft. La actualización incluye la modificación de la estructura de las tablas `banks` y `bank_accounts`, así como las actualizaciones necesarias en el modelo PHP (mdl-banco.php) para reflejar los cambios en la base de datos.

## Architecture

### Database Schema

El diseño se basa en una arquitectura relacional con las siguientes tablas principales:

```
banks (1) ----< (N) bank_accounts (N) >---- (1) payment_methods
                         |
                         |
                         v
                       (1) udn
```

### Component Structure

```
contabilidad/administrador/
├── mdl/
│   └── mdl-banco.php          # Modelo de datos (actualizado)
├── ctrl/
│   └── ctrl-banco.php         # Controlador (sin cambios)
├── js/
│   └── banco.js               # Frontend (sin cambios)
└── sql/
    └── banco_schema.sql       # Script SQL de actualización
```

## Components and Interfaces

### 1. Database Tables

#### Table: banks

Almacena el catálogo de bancos disponibles en el sistema.

```sql
CREATE TABLE banks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_bank_name (name),
    INDEX idx_banks_name (name),
    INDEX idx_banks_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `id`: Identificador único del banco
- `name`: Nombre del banco (ej: BBVA, Santander, Banamex)
- `active`: Estado del banco (1=activo, 0=inactivo)
- `created_at`: Fecha y hora de creación
- `updated_at`: Fecha y hora de última actualización

**Índices:**
- `unique_bank_name`: Garantiza que no existan bancos duplicados
- `idx_banks_name`: Optimiza búsquedas por nombre
- `idx_banks_active`: Optimiza filtrado por estado

#### Table: bank_accounts

Almacena las cuentas bancarias asociadas a bancos y unidades de negocio.

```sql
CREATE TABLE bank_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_alias VARCHAR(100) DEFAULT NULL,
    last_four_digits CHAR(4) NOT NULL,
    payment_method_id INT DEFAULT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    bank_id INT NOT NULL,
    udn_id INT NOT NULL,
    
    CONSTRAINT fk_bank_accounts_bank 
        FOREIGN KEY (bank_id) REFERENCES banks(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_bank_accounts_payment_method 
        FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT fk_bank_accounts_udn 
        FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_bank_accounts_bank_id (bank_id),
    INDEX idx_bank_accounts_udn_id (udn_id),
    INDEX idx_bank_accounts_payment_method_id (payment_method_id),
    INDEX idx_bank_accounts_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `id`: Identificador único de la cuenta bancaria
- `account_alias`: Alias o nombre personalizado de la cuenta (opcional)
- `last_four_digits`: Últimos 4 dígitos de la cuenta (obligatorio)
- `payment_method_id`: Referencia a la forma de pago (opcional)
- `active`: Estado de la cuenta (1=activa, 0=inactiva)
- `created_at`: Fecha y hora de creación
- `updated_at`: Fecha y hora de última actualización
- `bank_id`: Referencia al banco (obligatorio)
- `udn_id`: Referencia a la unidad de negocio (obligatorio)

**Relaciones:**
- `fk_bank_accounts_bank`: Relación con tabla banks (ON DELETE RESTRICT)
- `fk_bank_accounts_payment_method`: Relación con tabla payment_methods (ON DELETE SET NULL)
- `fk_bank_accounts_udn`: Relación con tabla udn (ON DELETE RESTRICT)

**Índices:**
- `idx_bank_accounts_bank_id`: Optimiza consultas por banco
- `idx_bank_accounts_udn_id`: Optimiza consultas por unidad de negocio
- `idx_bank_accounts_payment_method_id`: Optimiza consultas por forma de pago
- `idx_bank_accounts_active`: Optimiza filtrado por estado

### 2. Model Layer (mdl-banco.php)

El modelo PHP debe actualizarse para reflejar la nueva estructura de la base de datos.

#### Métodos Existentes (sin cambios)

Los siguientes métodos ya están correctamente implementados y no requieren modificaciones:

- `listBanks($array)`: Lista bancos activos/inactivos
- `listBankAccounts($array)`: Lista cuentas bancarias con filtros
- `getBankById($array)`: Obtiene un banco por ID
- `getBankAccountById($array)`: Obtiene una cuenta bancaria por ID
- `createBank($array)`: Crea un nuevo banco
- `createBankAccount($array)`: Crea una nueva cuenta bancaria
- `updateBankAccount($array)`: Actualiza una cuenta bancaria
- `existsBankByName($array)`: Verifica si existe un banco por nombre
- `lsUDN()`: Lista unidades de negocio
- `lsPaymentMethods()`: Lista formas de pago

#### Métodos a Agregar

```php
function updateBank($array) {
    return $this->_Update([
        'table' => $this->bd . 'banks',
        'values' => $array['values'],
        'where' => $array['where'],
        'data' => $array['data']
    ]);
}
```

**Propósito:** Actualizar información de un banco existente.

**Parámetros:**
- `$array['values']`: Campos a actualizar (ej: "name = ?, active = ?")
- `$array['where']`: Condición WHERE (ej: "id = ?")
- `$array['data']`: Valores para los placeholders

**Retorno:** `true` si la actualización fue exitosa, `false` en caso contrario.

### 3. Controller Layer (ctrl-banco.php)

El controlador actual ya implementa correctamente todos los métodos necesarios:

- `init()`: Inicializa datos para filtros
- `lsBankAccounts()`: Lista cuentas bancarias con filtros
- `getBank()`: Obtiene información de un banco
- `getBankAccount()`: Obtiene información de una cuenta bancaria
- `addBank()`: Agrega un nuevo banco
- `addBankAccount()`: Agrega una nueva cuenta bancaria
- `editBankAccount()`: Edita una cuenta bancaria existente
- `toggleStatusAccount()`: Cambia el estado de una cuenta bancaria

**No se requieren cambios en el controlador.**

### 4. Frontend Layer (banco.js)

El frontend actual ya implementa correctamente toda la funcionalidad:

- Filtrado por UDN, forma de pago y estado
- Listado de cuentas bancarias
- Formularios para agregar/editar bancos y cuentas
- Cambio de estado de cuentas

**No se requieren cambios en el frontend.**

## Data Models

### Bank Entity

```javascript
{
    id: number,
    name: string,
    active: number (0|1),
    created_at: timestamp,
    updated_at: timestamp
}
```

### Bank Account Entity

```javascript
{
    id: number,
    account_alias: string | null,
    last_four_digits: string (4 digits),
    payment_method_id: number | null,
    active: number (0|1),
    created_at: timestamp,
    updated_at: timestamp,
    bank_id: number,
    udn_id: number
}
```

### Bank Account View (with joins)

```javascript
{
    id: number,
    account_alias: string | null,
    last_four_digits: string,
    payment_method_id: number | null,
    active: number,
    bank_id: number,
    udn_id: number,
    bank_name: string,
    udn_name: string,
    payment_method_name: string | null,
    created_date: string (formatted)
}
```

## Error Handling

### Database Errors

1. **Foreign Key Constraint Violations**
   - Escenario: Intentar eliminar un banco que tiene cuentas asociadas
   - Manejo: ON DELETE RESTRICT previene la eliminación
   - Mensaje: "No se puede eliminar el banco porque tiene cuentas asociadas"

2. **Unique Constraint Violations**
   - Escenario: Intentar crear un banco con nombre duplicado
   - Manejo: Validación en `existsBankByName()` antes de insertar
   - Mensaje: "Ya existe un banco con ese nombre"

3. **NULL Constraint Violations**
   - Escenario: Intentar crear cuenta sin bank_id o last_four_digits
   - Manejo: Validación en controlador antes de insertar
   - Mensaje: "El banco y los últimos 4 dígitos son obligatorios"

### Validation Errors

1. **Invalid last_four_digits Format**
   - Validación: Regex `/^\d{4}$/`
   - Mensaje: "Los últimos 4 dígitos deben ser numéricos"

2. **Missing Required Fields**
   - Validación: `empty()` check en controlador
   - Mensaje: "El campo [nombre] es obligatorio"

3. **Invalid Foreign Key References**
   - Validación: Verificar existencia antes de insertar
   - Mensaje: "El [banco/forma de pago/UDN] seleccionado no existe"

## Testing Strategy

### Unit Tests

1. **Model Layer Tests**
   ```php
   testListBanks() // Verificar listado de bancos
   testListBankAccounts() // Verificar listado con filtros
   testCreateBank() // Verificar creación de banco
   testCreateBankAccount() // Verificar creación de cuenta
   testUpdateBank() // Verificar actualización de banco
   testUpdateBankAccount() // Verificar actualización de cuenta
   testExistsBankByName() // Verificar validación de duplicados
   ```

2. **Controller Layer Tests**
   ```php
   testInitReturnsCorrectData() // Verificar datos de inicialización
   testAddBankValidation() // Verificar validaciones al agregar banco
   testAddBankAccountValidation() // Verificar validaciones al agregar cuenta
   testToggleStatusAccount() // Verificar cambio de estado
   ```

### Integration Tests

1. **Database Integration**
   - Verificar que las foreign keys funcionen correctamente
   - Verificar que los índices mejoren el rendimiento
   - Verificar que los timestamps se actualicen automáticamente

2. **End-to-End Tests**
   - Crear banco → Crear cuenta → Editar cuenta → Desactivar cuenta
   - Filtrar cuentas por UDN → Verificar resultados
   - Filtrar cuentas por forma de pago → Verificar resultados
   - Intentar crear banco duplicado → Verificar error

### Manual Testing Checklist

- [ ] Crear un nuevo banco
- [ ] Verificar que no se pueda crear banco duplicado
- [ ] Crear cuenta bancaria con todos los campos
- [ ] Crear cuenta bancaria solo con campos obligatorios
- [ ] Editar cuenta bancaria
- [ ] Desactivar cuenta bancaria
- [ ] Activar cuenta bancaria
- [ ] Filtrar cuentas por UDN
- [ ] Filtrar cuentas por forma de pago
- [ ] Filtrar cuentas por estado
- [ ] Aplicar múltiples filtros simultáneamente
- [ ] Verificar que cuentas desactivadas no aparezcan en filtro de activas
- [ ] Verificar formato de últimos 4 dígitos (****XXXX)
- [ ] Verificar mensajes de error en validaciones

## Migration Strategy

### Step 1: Backup

```sql
-- Crear backup de tablas existentes
CREATE TABLE banks_backup AS SELECT * FROM banks;
CREATE TABLE bank_accounts_backup AS SELECT * FROM bank_accounts;
```

### Step 2: Schema Update

```sql
-- Ejecutar script de actualización (banco_schema.sql)
-- El script incluye:
-- 1. ALTER TABLE para agregar/modificar columnas
-- 2. CREATE INDEX para optimizar consultas
-- 3. ADD CONSTRAINT para foreign keys
```

### Step 3: Data Migration

```sql
-- Migrar datos existentes si es necesario
-- Actualizar valores NULL a valores por defecto
-- Verificar integridad referencial
```

### Step 4: Verification

```sql
-- Verificar estructura de tablas
DESCRIBE banks;
DESCRIBE bank_accounts;

-- Verificar foreign keys
SELECT * FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'bank_accounts';

-- Verificar índices
SHOW INDEX FROM banks;
SHOW INDEX FROM bank_accounts;
```

### Step 5: Model Update

1. Actualizar `mdl-banco.php` con el método `updateBank()`
2. Verificar que todos los métodos existentes funcionen correctamente
3. Ejecutar pruebas unitarias

### Step 6: Rollback Plan

En caso de problemas:

```sql
-- Restaurar desde backup
DROP TABLE banks;
DROP TABLE bank_accounts;

CREATE TABLE banks AS SELECT * FROM banks_backup;
CREATE TABLE bank_accounts AS SELECT * FROM bank_accounts_backup;

-- Restaurar archivo mdl-banco.php desde control de versiones
```

## Performance Considerations

### Indexing Strategy

1. **Primary Keys**: Índices automáticos en `id` de ambas tablas
2. **Foreign Keys**: Índices en `bank_id`, `udn_id`, `payment_method_id`
3. **Search Fields**: Índice en `banks.name` para búsquedas
4. **Filter Fields**: Índices en `active` para filtrado rápido

### Query Optimization

1. **listBankAccounts()**: Usa INNER JOIN y LEFT JOIN eficientemente
2. **Filtros**: Los índices en columnas de filtro mejoran el rendimiento
3. **Paginación**: DataTables maneja paginación en frontend

### Caching Strategy

- No se requiere caching adicional
- Los datos de bancos y formas de pago se cargan una vez en `init()`
- Las consultas de cuentas se ejecutan bajo demanda con filtros

## Security Considerations

### SQL Injection Prevention

- Todos los métodos usan prepared statements con placeholders `?`
- La clase CRUD maneja automáticamente el escape de valores
- No se construyen queries dinámicas con concatenación de strings

### Access Control

- El módulo requiere sesión activa (`session_start()`)
- Solo usuarios autenticados pueden acceder
- Se recomienda agregar validación de roles/permisos

### Data Validation

- Validación de formato en `last_four_digits` (4 dígitos numéricos)
- Validación de existencia en foreign keys
- Validación de campos obligatorios en controlador

## Deployment Checklist

- [ ] Crear backup de base de datos
- [ ] Ejecutar script SQL de actualización
- [ ] Verificar estructura de tablas
- [ ] Verificar foreign keys e índices
- [ ] Actualizar archivo mdl-banco.php
- [ ] Ejecutar pruebas unitarias
- [ ] Ejecutar pruebas de integración
- [ ] Verificar funcionalidad en ambiente de desarrollo
- [ ] Documentar cambios en changelog
- [ ] Desplegar a producción
- [ ] Verificar funcionalidad en producción
- [ ] Monitorear logs por 24 horas
