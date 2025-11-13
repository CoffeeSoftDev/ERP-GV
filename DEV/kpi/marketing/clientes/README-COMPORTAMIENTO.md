# 📊 Módulo de Comportamiento de Clientes

## Descripción

El módulo de **Comportamiento de Clientes** analiza las interacciones de los clientes con el sistema de pedidos, proporcionando métricas clave para estrategias de marketing y fidelización.

## 🎯 Funcionalidades Principales

### 1. Análisis de Comportamiento Individual

Para cada cliente se muestra:

- **Total de Pedidos**: Cantidad total de pedidos realizados
- **Monto Total**: Suma de todos los pedidos
- **Ticket Promedio**: Promedio de gasto por pedido
- **Última Compra**: Fecha del último pedido
- **Días sin Comprar**: Días transcurridos desde la última compra
- **Primera Compra**: Fecha del primer pedido
- **Historial de Pedidos**: Últimos 10 pedidos con detalles

### 2. Segmentación por Frecuencia

Los clientes se clasifican automáticamente en:

- 🟢 **Activo**: Compró en los últimos 30 días
- 🟡 **Regular**: Compró entre 31 y 90 días atrás
- 🔴 **Inactivo**: Más de 90 días sin comprar
- ⚪ **Sin Pedidos**: Cliente registrado sin pedidos

### 3. Top Clientes

Ranking de los 10 mejores clientes por:
- Monto total gastado
- Cantidad de pedidos
- Ticket promedio

### 4. Filtros Disponibles

- **Unidad de Negocio**: Filtrar por UDN específica o todas
- **Estatus**: Clientes activos o inactivos en el sistema

## 📁 Archivos del Módulo

```
DEV/kpi/marketing/clientes/
├── comportamiento.php           # Vista principal
├── js/
│   └── comportamiento.js        # Lógica del frontend
├── ctrl/
│   └── ctrl-clientes.php        # Métodos de comportamiento agregados
└── mdl/
    └── mdl-clientes.php         # Consultas de comportamiento agregadas
```

## 🔧 Métodos del Modelo (mdl-clientes.php)

### Métodos Principales

```php
// Obtiene comportamiento detallado de un cliente
getComportamientoCliente($clienteId)

// Obtiene historial de pedidos
getHistorialPedidos($clienteId, $limit = 10)

// Lista todos los clientes con métricas de comportamiento
getComportamientoClientes($params)

// Obtiene clientes por frecuencia (activo, regular, inactivo)
getClientesPorFrecuencia($frecuencia, $udnId = null)

// Obtiene top clientes por monto
getTopClientes($limit = 10, $udnId = null)
```

## 🎨 Interfaz de Usuario

### Tabla Principal

Columnas mostradas:
1. Cliente (con badge VIP si aplica)
2. UDN
3. Total Pedidos
4. Monto Total
5. Ticket Promedio
6. Última Compra
7. Días sin Comprar
8. Frecuencia (badge con color)
9. Acciones (botón ver detalle)

### Modal de Detalle

Al hacer clic en el botón de detalle (📊), se muestra:

- **Información del Cliente**: Nombre, teléfono, correo, UDN
- **Métricas en Cards**:
  - Total Pedidos (azul)
  - Monto Total (verde)
  - Ticket Promedio (cyan)
  - Días sin Comprar (amarillo/rojo según días)
- **Fechas Importantes**: Primera y última compra
- **Historial de Pedidos**: Últimos 10 pedidos con scroll

### Modal Top Clientes

Muestra ranking con:
- Medallas para top 3 (🥇🥈🥉)
- Nombre con badge VIP
- UDN y cantidad de pedidos
- Monto total y ticket promedio
- Fecha de última compra

## 📊 Métricas y KPIs

### Por Cliente

- **Recencia**: Días desde última compra
- **Frecuencia**: Clasificación según días sin comprar
- **Valor**: Monto total gastado
- **Ticket Promedio**: Gasto promedio por pedido

### Generales

- Total de clientes con pedidos
- Clientes activos vs regulares vs inactivos
- Top clientes por monto
- Distribución por UDN

## 🚀 Casos de Uso

### 1. Identificar Clientes en Riesgo

Filtrar por frecuencia "Inactivo" para ver clientes que no han comprado en más de 90 días y crear campañas de reactivación.

### 2. Premiar Clientes Leales

Usar el Top Clientes para identificar a los mejores compradores y ofrecerles beneficios especiales o estatus VIP.

### 3. Análisis de Recencia

Monitorear los "Días sin Comprar" para detectar patrones y anticipar abandono.

### 4. Segmentación para Campañas

Usar la clasificación de frecuencia para crear campañas dirigidas:
- Activos: Promociones de productos nuevos
- Regulares: Incentivos para aumentar frecuencia
- Inactivos: Ofertas de reactivación

## 🔗 Integración con Sistema de Pedidos

El módulo se integra automáticamente con la tabla `pedido` del sistema, analizando:

- `pedido.cliente_id`: Relación con el cliente
- `pedido.fecha_pedido`: Para calcular recencia y frecuencia
- `pedido.monto`: Para calcular totales y promedios
- `pedido.udn_id`: Para filtrar por unidad de negocio

## 📈 Próximas Mejoras Sugeridas

1. **Gráficos de Tendencia**: Visualizar evolución de compras en el tiempo
2. **Predicción de Abandono**: ML para predecir clientes en riesgo
3. **Productos Favoritos**: Análisis de productos más comprados por cliente
4. **Comparativas**: Comparar comportamiento entre UDNs
5. **Exportación**: Exportar reportes a Excel/PDF
6. **Alertas Automáticas**: Notificaciones de clientes inactivos

## 🎯 Acceso al Módulo

**URL**: `DEV/kpi/marketing/clientes/comportamiento.php`

**Breadcrumb**: KPI > Marketing > Comportamiento de Clientes

## 📝 Notas Técnicas

- Usa LEFT JOIN para incluir clientes sin pedidos
- Las consultas están optimizadas con GROUP BY
- Los badges usan colores de TailwindCSS
- Compatible con DataTables para búsqueda y paginación
- Responsive design para móviles y tablets

## 🆘 Soporte

Para dudas o problemas con el módulo de comportamiento, contacta al equipo de desarrollo.
