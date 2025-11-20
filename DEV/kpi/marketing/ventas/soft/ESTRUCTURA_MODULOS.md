# 📦 Estructura de Módulos - Productos Soft Restaurant

Se ha separado el archivo `productos-soft.js` en **3 módulos independientes** con sus respectivos controladores y modelos.

---

## 📁 Estructura de Archivos Creados

```
kpi/marketing/ventas/soft/
│
├── src/js/
│   ├── productos-soft.js          ✅ Módulo principal de productos
│   ├── concentrado-periodos.js    ✅ Módulo de concentrado por periodos
│   └── grupos-udn.js               ✅ Módulo de grupos por UDN
│
├── ctrl/
│   ├── ctrl-productos-soft.php           (Original - mantener)
│   ├── ctrl-concentrado-periodos.php     ✅ Controlador de concentrado
│   └── ctrl-grupos-udn.php               ✅ Controlador de grupos
│
└── mdl/
    ├── mdl-productos-soft.php            (Original - mantener)
    ├── mdl-concentrado-periodos.php      ✅ Modelo de concentrado
    └── mdl-grupos-udn.php                ✅ Modelo de grupos
```

---

## 🎯 Módulo 1: Productos Soft

### Frontend: `productos-soft.js`
**Clase:** `ProductosSoft`

**Funcionalidades:**
- ✅ Listado de productos con filtros (UDN, Grupo, Año, Mes)
- ✅ Carga dinámica de grupos por UDN
- ✅ Tabla de productos con homologación
- ✅ Header personalizado con navegación

**API:** `ctrl/ctrl-productos-soft.php`

**Métodos principales:**
- `render()` - Inicializa el módulo
- `layout()` - Crea la estructura visual
- `filterBarProductos()` - Barra de filtros
- `loadGruposByUdn()` - Carga grupos dinámicamente
- `lsProductos()` - Lista productos en tabla

---

## 📊 Módulo 2: Concentrado por Periodos

### Frontend: `concentrado-periodos.js`
**Clase:** `ConcentradoPeriodos`

**Funcionalidades:**
- ✅ Análisis de productos por periodos mensuales
- ✅ Tabla expandible con grupos y productos
- ✅ Filtros: UDN, Grupo, Año, Mes, Periodo (3/6/9 meses)
- ✅ Visualización de cantidades por mes

**API:** `ctrl/ctrl-concentrado-periodos.php`

**Controlador:** `ctrl-concentrado-periodos.php`
**Métodos:**
- `init()` - Inicializa datos (UDN)
- `getGruposByUdn()` - Obtiene grupos por UDN
- `lsConcentrado()` - Lista concentrado con agrupación

**Modelo:** `mdl-concentrado-periodos.php`
**Métodos:**
- `lsUDN()` - Lista unidades de negocio
- `lsGrupos()` - Lista grupos de productos
- `listConcentrado()` - Consulta concentrado con cantidades por trimestre

---

## 📦 Módulo 3: Grupos por UDN

### Frontend: `grupos-udn.js`
**Clase:** `GruposUdn`

**Funcionalidades:**
- ✅ Visualización de grupos en tarjetas (cards)
- ✅ Contador de productos por grupo
- ✅ Vista detallada de productos al hacer clic en grupo
- ✅ Navegación entre vista de grupos y productos

**API:** `ctrl/ctrl-grupos-udn.php`

**Controlador:** `ctrl-grupos-udn.php`
**Métodos:**
- `init()` - Inicializa datos (UDN)
- `lsGroups()` - Lista grupos con cantidad de productos
- `lsProductos()` - Lista productos de un grupo específico

**Modelo:** `mdl-grupos-udn.php`
**Métodos:**
- `lsUDN()` - Lista unidades de negocio
- `listGrupos()` - Lista grupos con conteo de productos
- `listProductos()` - Lista productos por grupo
- `select_homologar()` - Obtiene homologaciones

---

## 🔧 Cómo Usar los Módulos

### 1. Productos Soft (Principal)
```html
<!-- productos-soft.html -->
<script src="src/js/productos-soft.js"></script>
```

### 2. Concentrado por Periodos
```html
<!-- concentrado-periodos.html -->
<script src="src/js/concentrado-periodos.js"></script>
```

### 3. Grupos por UDN
```html
<!-- grupos-udn.html -->
<script src="src/js/grupos-udn.js"></script>
```

---

## 📋 Variables Globales por Módulo

### Productos Soft
```javascript
let apiProductos = 'ctrl/ctrl-productos-soft.php';
let app, lsudn, lsgrupos;
```

### Concentrado Periodos
```javascript
let apiConcentrado = 'ctrl/ctrl-concentrado-periodos.php';
let concentrado, lsudn;
```

### Grupos UDN
```javascript
let apiGrupos = 'ctrl/ctrl-grupos-udn.php';
let gruposUdn, lsudn;
```

---

## 🎨 Componentes Compartidos

Todos los módulos comparten:
- ✅ `headerBar()` - Header personalizado
- ✅ `redirectToHome()` - Navegación al inicio
- ✅ Estilos TailwindCSS
- ✅ Framework CoffeeSoft

---

## 🔄 Flujo de Datos

### Productos Soft
```
Usuario → Filtros (UDN, Grupo, Año, Mes)
       → ctrl-productos-soft.php → lsProductos()
       → mdl-productos-soft.php → listProductos()
       → Tabla con productos y homologación
```

### Concentrado Periodos
```
Usuario → Filtros (UDN, Grupo, Año, Periodo)
       → ctrl-concentrado-periodos.php → lsConcentrado()
       → mdl-concentrado-periodos.php → listConcentrado()
       → Tabla expandible con cantidades mensuales
```

### Grupos UDN
```
Usuario → Selecciona UDN
       → ctrl-grupos-udn.php → lsGroups()
       → mdl-grupos-udn.php → listGrupos()
       → Cards de grupos
       → Click en grupo → lsProductos()
       → Tabla de productos del grupo
```

---

## ✅ Ventajas de la Separación

1. **Modularidad** - Cada módulo es independiente
2. **Mantenibilidad** - Más fácil de mantener y actualizar
3. **Escalabilidad** - Se pueden agregar más módulos sin afectar los existentes
4. **Reutilización** - Los componentes se pueden reutilizar
5. **Claridad** - Código más limpio y organizado
6. **Testing** - Más fácil de probar cada módulo por separado

---

## 🚀 Próximos Pasos

1. Crear archivos HTML para cada módulo
2. Probar cada módulo independientemente
3. Verificar la integración con la base de datos
4. Ajustar estilos según necesidades
5. Implementar funcionalidad de "Grupos por Homologar" (tab pendiente)

---

## 📝 Notas Importantes

- ⚠️ El archivo original `productos-soft.js` ha sido reemplazado con la versión modular
- ⚠️ Los controladores y modelos originales se mantienen para compatibilidad
- ✅ Cada módulo tiene su propia API independiente
- ✅ Se respeta la lógica original del código
- ✅ Se mantienen todas las funcionalidades existentes

---

**Fecha de creación:** 2025
**Framework:** CoffeeSoft
**Arquitectura:** MVC (Modelo-Vista-Controlador)
