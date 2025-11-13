# 👥 Sistema de Gestión de Clientes - CoffeeSoft ERP

## 📋 Descripción General

Sistema completo de gestión de clientes para el módulo KPI/Marketing del ERP CoffeeSoft. Incluye administración de clientes, análisis de comportamiento y métricas de fidelización.

## 🎯 Módulos Implementados

### 1. 📝 Gestión de Clientes (`index.php`)

Módulo principal para administración de clientes.

**Funcionalidades:**
- ✅ Registro de nuevos clientes con información completa
- ✅ Edición de datos existentes
- ✅ Baja controlada (cambio de estatus activo/inactivo)
- ✅ Gestión de domicilios de entrega
- ✅ Clasificación por Unidad de Negocio
- ✅ Marcado de clientes VIP
- ✅ Filtros avanzados (UDN, estatus, tipo VIP)
- ✅ Búsqueda dinámica en tabla
- ✅ Validaciones completas (teléfono, correo, duplicados)

**Campos del Cliente:**
- Nombre, apellidos
- Teléfono (obligatorio, 10+ dígitos)
- Correo electrónico (validación de formato)
- Fecha de cumpleaños
- Unidad de negocio (obligatorio)
- Estatus VIP
- Domicilio completo (calle, número, colonia, ciudad, estado, CP, referencias)

### 2. 📊 Comportamiento de Clientes (`comportamiento.php`)

Módulo de análisis de interacciones y patrones de compra.

**Funcionalidades:**
- ✅ Análisis individual de cada cliente
- ✅ Segmentación automática por frecuencia
- ✅ Top 10 clientes por monto
- ✅ Historial de pedidos
- ✅ Métricas de recencia, frecuencia y valor

**Métricas Calculadas:**
- Total de pedidos
- Monto total gastado
- Ticket promedio
- Última compra (fecha)
- Días sin comprar
- Primera compra
- Clasificación de frecuencia

**Segmentación:**
- 🟢 **Activo**: Compró en últimos 30 días
- 🟡 **Regular**: Compró entre 31-90 días
- 🔴 **Inactivo**: Más de 90 días sin comprar
- ⚪ **Sin Pedidos**: Registrado sin pedidos

## 📁 Estructura del Proyecto

```
DEV/kpi/marketing/clientes/
│
├── index.php                      # Vista principal - Gestión de Clientes
├── comportamiento.php             # Vista - Análisis de Comportamiento
│
├── ctrl/
│   └── ctrl-clientes.php          # Controlador con toda la lógica
│
├── mdl/
│   └── mdl-clientes.php           # Modelo con consultas SQL
│
├── js/
│   ├── clientes.js                # Frontend - Gestión
│   └── comportamiento.js          # Frontend - Comportamiento
│
├── layout/
│   ├── head.php                   # Head con estilos
│   └── core-libraries.php         # Scripts core
│
├── database/
│   ├── schema.sql                 # Estructura de BD
│   ├── seed.sql                   # Datos de prueba
│   └── README.md                  # Documentación BD
│
├── README.md                      # Este archivo
└── README-COMPORTAMIENTO.md       # Documentación detallada
```

## 🗄️ Base de Datos

### Tablas Creadas

**1. cliente**
- Información principal del cliente
- Relación con `udn` (Unidad de Negocio)
- Campos: id, nombre, apellidos, vip, teléfono, correo, fecha_cumpleaños, fecha_creacion, udn_id, active

**2. domicilio_cliente**
- Domicilios de entrega
- Relación con `cliente` (ON DELETE CASCADE)
- Campos: id, cliente_id, calle, numero_exterior, numero_interior, colonia, ciudad, estado, codigo_postal, referencias, es_principal

### Índices Optimizados
- `idx_cliente_telefono` - Búsqueda por teléfono
- `idx_cliente_udn` - Filtrado por UDN
- `idx_cliente_active` - Filtrado por estatus
- `idx_cliente_vip` - Filtrado de VIP
- `idx_domicilio_cliente` - Consulta de domicilios

## 🚀 Instalación

### 1. Base de Datos

```bash
# Ejecutar desde phpMyAdmin o línea de comandos
mysql -u usuario -p nombre_bd < database/schema.sql

# Opcional: Datos de prueba
mysql -u usuario -p nombre_bd < database/seed.sql
```

### 2. Configuración

Verificar que la configuración de base de datos en `mdl-clientes.php` sea correcta:

```php
$this->bd = "rfwsmqex_kpi.";  // Ajustar según tu BD
```

### 3. Acceso

**Gestión de Clientes:**
```
http://localhost/CoffeeLab/CoffeeLab/CoffeeERP/DEV/kpi/marketing/clientes/index.php
```

**Comportamiento de Clientes:**
```
http://localhost/CoffeeLab/CoffeeLab/CoffeeERP/DEV/kpi/marketing/clientes/comportamiento.php
```

## 🎨 Tecnologías Utilizadas

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: JavaScript (jQuery), TailwindCSS, Bootstrap 5
- **Framework**: CoffeeSoft (Templates, Components)
- **Plugins**: DataTables, SweetAlert2, Select2, Bootbox

## 📊 Casos de Uso

### Gestión de Clientes

1. **Registrar nuevo cliente** con información completa
2. **Actualizar datos** de clientes existentes
3. **Desactivar clientes** sin eliminar historial
4. **Filtrar clientes** por UDN, estatus o tipo VIP
5. **Buscar clientes** por nombre, teléfono o correo

### Análisis de Comportamiento

1. **Identificar clientes en riesgo** de abandono
2. **Premiar clientes leales** (top clientes)
3. **Segmentar para campañas** de marketing
4. **Analizar patrones** de compra
5. **Monitorear recencia** y frecuencia

## 🔐 Validaciones Implementadas

### Frontend (JavaScript)
- Campos obligatorios
- Formato de teléfono (10+ dígitos numéricos)
- Formato de correo electrónico
- Validación en tiempo real

### Backend (PHP)
- Campos obligatorios
- Formato de teléfono y correo
- Duplicados por teléfono
- Sanitización de inputs
- Prepared statements (SQL injection)

## 📈 KPIs y Métricas

### Gestión
- Total de clientes activos
- Total de clientes VIP
- Clientes con cumpleaños del mes
- Nuevos registros por periodo

### Comportamiento
- Recencia (días desde última compra)
- Frecuencia (clasificación automática)
- Valor (monto total gastado)
- Ticket promedio
- Tasa de retención

## 🔗 Integración

El sistema se integra con:
- **Sistema de Pedidos**: Clientes disponibles para pedidos
- **Tabla UDN**: Unidades de negocio
- **Tabla Pedido**: Historial de compras
- **Tabla Canal**: Canales de venta

## 🆘 Soporte y Mantenimiento

### Logs y Debugging
- Revisar respuestas AJAX en consola del navegador
- Verificar errores PHP en logs del servidor
- Validar estructura de BD con `DESCRIBE cliente`

### Problemas Comunes

**1. No se muestran clientes:**
- Verificar que exista la tabla `cliente`
- Revisar configuración de BD en modelo
- Verificar permisos de usuario de BD

**2. Error al guardar:**
- Validar que todos los campos obligatorios estén completos
- Verificar que no exista teléfono duplicado
- Revisar formato de correo electrónico

**3. Comportamiento sin datos:**
- Verificar que exista la tabla `pedido`
- Asegurar que haya relación `pedido.cliente_id`
- Revisar que haya pedidos registrados

## 📝 Próximas Mejoras

### Corto Plazo
- [ ] Exportación a Excel/PDF
- [ ] Gráficos de tendencias
- [ ] Alertas automáticas de clientes inactivos
- [ ] Productos favoritos por cliente

### Mediano Plazo
- [ ] Predicción de abandono con ML
- [ ] Segmentación RFM avanzada
- [ ] Campañas automatizadas
- [ ] Dashboard ejecutivo

### Largo Plazo
- [ ] App móvil para gestión
- [ ] Integración con CRM externo
- [ ] Análisis predictivo avanzado
- [ ] Gamificación de fidelización

## 👥 Créditos

**Desarrollado para**: CoffeeSoft ERP  
**Módulo**: KPI / Marketing  
**Sistema**: Gestión de Clientes  
**Versión**: 1.0.0  
**Fecha**: Octubre 2025

## 📄 Licencia

Uso interno de CoffeeSoft ERP. Todos los derechos reservados.

---

**¿Necesitas ayuda?** Contacta al equipo de desarrollo.
