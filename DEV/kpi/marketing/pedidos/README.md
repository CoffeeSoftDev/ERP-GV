# Sistema de Administración de Pedidos

Sistema web para la gestión completa de pedidos recibidos a través de múltiples canales de comunicación.

## 📁 Estructura del Proyecto

```
pedidos/
│
├── index.php                      # Punto de entrada principal
│
├── ctrl/                          # Controladores PHP (Lógica de negocio)
│   ├── ctrl-pedidos.php          # Controlador de pedidos
│   ├── ctrl-productos.php        # Controlador de productos
│   └── ctrl-canales.php          # Controlador de canales y campañas
│
├── mdl/                           # Modelos PHP (Acceso a datos)
│   ├── mdl-pedidos.php           # Modelo de pedidos
│   ├── mdl-productos.php         # Modelo de productos
│   └── mdl-canales.php           # Modelo de canales y campañas
│
├── js/                            # Scripts JavaScript del proyecto
│   ├── dashboard.js              # Módulo de dashboard y métricas
│   ├── pedidos.js                # Módulo de gestión de pedidos
│   ├── productos.js              # Módulo de gestión de productos
│   └── canales.js                # Módulo de gestión de canales
│
└── README.md                      # Este archivo
```

## 🚀 Tecnologías Utilizadas

**Frontend:**
- JavaScript ES6+ / jQuery 3.x
- TailwindCSS 3.x
- Chart.js 3.x (gráficos)
- DataTables 1.x (tablas)
- SweetAlert2 (alertas)
- Moment.js (fechas)

**Backend:**
- PHP 7.4+
- MySQL 5.7+

**Framework:**
- CoffeeSoft (framework custom)

## 📋 Módulos del Sistema

### 1. Dashboard
- Visualización de KPIs principales
- Gráficos comparativos de ventas
- Métricas por canal de comunicación
- Análisis de tendencias

### 2. Pedidos
- Captura de pedidos diarios
- Listado y filtrado de pedidos
- Edición con reglas de negocio (7 días)
- Verificación de transferencias
- Registro de llegadas (servicios)
- Exportación de reportes

### 3. Productos
- Gestión de productos y servicios
- Categorización por UDN
- Activación/desactivación
- Estadísticas de uso

### 4. Canales
- Administración de canales de comunicación
- Gestión de campañas publicitarias
- Métricas de rendimiento
- ROI de campañas

## 👥 Roles de Usuario

1. **Jefe de Atención a Clientes**
   - Acceso completo al sistema
   - Gestión de todos los módulos
   - Visualización de reportes

2. **Auxiliar de Atención a Clientes**
   - Captura de pedidos diarios
   - Visualización de pedidos
   - Edición limitada (7 días)

3. **Tesorería**
   - Verificación de transferencias
   - Visualización de tabla de pedidos
   - Reportes de pagos

## 🔧 Configuración

1. Configurar la base de datos en los archivos `mdl-*.php`:
   ```php
   $this->bd = "nombre_base_datos.";
   ```

2. Asegurar que los archivos de configuración existan:
   - `../../conf/_CRUD.php`
   - `../../conf/_Utileria.php`

3. Configurar las rutas de CoffeeSoft en `index.php`:
   - `../src/js/coffeeSoft.js`
   - `../src/js/plugins.js`

## 📝 Estado del Proyecto

**Tarea Completada:** ✅ Estructura base del proyecto

**Próximas Tareas:**
- Implementar modelo de datos (mdl-pedidos.php)
- Implementar controlador de pedidos (ctrl-pedidos.php)
- Desarrollar módulo frontend de Dashboard
- Desarrollar módulo frontend de Pedidos
- Y más...

## 📖 Documentación

Para más detalles sobre la implementación, consultar:
- `.kiro/specs/pedidos-management/requirements.md` - Requerimientos del sistema
- `.kiro/specs/pedidos-management/design.md` - Diseño técnico
- `.kiro/specs/pedidos-management/tasks.md` - Plan de implementación

## 🎯 Convenciones de Código

- **Controladores:** Métodos en inglés con camelCase (`addPedido()`, `editPedido()`)
- **Modelos:** Métodos en inglés con camelCase (`createPedido()`, `listPedidos()`)
- **Frontend:** Clases que heredan de `Templates`, métodos en camelCase
- **Base de datos:** Nombres de tablas en español, campos en snake_case

## 📞 Soporte

Para dudas o problemas, consultar la documentación técnica en `.kiro/specs/pedidos-management/`
