# Base de Datos - Gestión de Clientes

## Descripción

Este directorio contiene los scripts SQL necesarios para crear la estructura de base de datos del módulo de Gestión de Clientes.

## Archivos

- **schema.sql**: Script de creación de tablas e índices
- **seed.sql**: Script de datos de prueba (opcional, solo para desarrollo)

## Tablas Creadas

### 1. cliente
Almacena la información principal de los clientes que realizan pedidos a domicilio.

**Campos principales:**
- `id`: Identificador único
- `nombre`, `apellido_paterno`, `apellido_materno`: Nombre completo
- `vip`: Indicador de cliente VIP (0 = no, 1 = sí)
- `telefono`: Teléfono de contacto (obligatorio)
- `correo`: Correo electrónico (opcional)
- `fecha_cumpleaños`: Fecha de cumpleaños (opcional)
- `udn_id`: Unidad de negocio de procedencia
- `active`: Estado (1 = activo, 0 = inactivo)

### 2. domicilio_cliente
Almacena los domicilios de entrega de los clientes.

**Campos principales:**
- `id`: Identificador único
- `cliente_id`: Referencia al cliente
- `calle`, `numero_exterior`, `numero_interior`: Dirección
- `colonia`, `ciudad`, `estado`, `codigo_postal`: Ubicación
- `referencias`: Indicaciones para localizar
- `es_principal`: Domicilio principal (1 = sí, 0 = no)

## Instrucciones de Instalación

### Opción 1: Desde phpMyAdmin

1. Accede a phpMyAdmin
2. Selecciona la base de datos del proyecto (generalmente `coffeesoft` o similar)
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `schema.sql`
5. Haz clic en "Continuar" para ejecutar
6. (Opcional) Repite el proceso con `seed.sql` para datos de prueba

### Opción 2: Desde línea de comandos

```bash
# Ejecutar schema.sql
mysql -u usuario -p nombre_base_datos < schema.sql

# Ejecutar seed.sql (opcional)
mysql -u usuario -p nombre_base_datos < seed.sql
```

### Opción 3: Desde código PHP

```php
// Ejecutar desde un script PHP
$sql = file_get_contents('database/schema.sql');
$mysqli->multi_query($sql);
```

## Requisitos Previos

- La tabla `udn` (Unidades de Negocio) debe existir antes de ejecutar estos scripts
- El usuario de base de datos debe tener permisos de CREATE TABLE y CREATE INDEX

## Verificación

Después de ejecutar los scripts, verifica que las tablas se crearon correctamente:

```sql
-- Verificar estructura de tabla cliente
DESCRIBE cliente;

-- Verificar estructura de tabla domicilio_cliente
DESCRIBE domicilio_cliente;

-- Verificar índices creados
SHOW INDEX FROM cliente;
SHOW INDEX FROM domicilio_cliente;

-- Contar registros (si ejecutaste seed.sql)
SELECT COUNT(*) as total_clientes FROM cliente;
SELECT COUNT(*) as total_domicilios FROM domicilio_cliente;
```

## Notas Importantes

- **Baja Lógica**: Los clientes no se eliminan físicamente, solo se cambia el campo `active` a 0
- **Cascada**: Al eliminar un cliente, sus domicilios se eliminan automáticamente (ON DELETE CASCADE)
- **Índices**: Se crearon índices para optimizar consultas frecuentes (teléfono, UDN, estatus, VIP)
- **Charset**: Las tablas usan `utf8mb4_unicode_ci` para soportar caracteres especiales y emojis

## Soporte

Para dudas o problemas con la base de datos, contacta al equipo de desarrollo.
