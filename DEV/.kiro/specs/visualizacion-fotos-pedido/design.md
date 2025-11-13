# Design Document

## Overview

Este documento describe el diseño técnico para implementar la funcionalidad de visualización de fotos de pedidos en el sistema Control Fogaza. La solución permitirá a los usuarios comparar la foto de referencia del catálogo con la foto final del producto terminado mediante un modal interactivo con capacidad de ampliación tipo lightbox.

## Architecture

### Component Structure

```
ListaPedidos (Clase existente)
    ├── showImagesOrder() [NUEVO] - Modal principal de visualización
    ├── getImageOrder() [NUEVO] - Obtención de datos del backend
    └── lightboxImage() [NUEVO] - Visor ampliado de imágenes

Backend (PHP)
    ├── ctrl-pedidos.php
    │   └── getImageOrder() [NUEVO] - Controlador para obtener datos
    └── mdl-pedidos.php
        └── getOrderImagesById() [NUEVO] - Modelo para consulta DB
```

### Data Flow

```
Usuario → Click "Ver Fotos" → getImageOrder(id) → Backend
                                                      ↓
                                                  Query DB
                                                      ↓
Modal ← showImagesOrder(data) ← JSON Response ← Formato datos
```

## Components and Interfaces

### 1. Frontend Component: `showImagesOrder(options)`

**Propósito:** Renderizar el modal con las fotos del pedido y detalles asociados.

**Parámetros:**
```javascript
{
    parent: "root",           // Contenedor donde se renderiza
    id: "modalImageOrder",    // ID del modal
    data: {
        order: {
            folio: "P-159",
            name: "Pastel de Chocolate",
            price: 315.00,
            cliente: "Laura",
            fecha_entrega: "3/10/2025",
            estado: "Pendiente",
            canal: "Local"
        },
        images: {
            reference: "https://erp-varoch.com/path/reference.jpg",
            production: "https://erp-varoch.com/path/production.jpg"
        }
    }
}
```

**Estructura HTML:**
```html
<div id="modalImageOrder" class="modal">
    <!-- Header -->
    <div class="modal-header">
        <h2>Pastel de Chocolate</h2>
        <p>Folio: P-159 | Precio Total: $315.00</p>
        <button class="close-btn">×</button>
    </div>
    
    <!-- Body: Fotos comparativas -->
    <div class="modal-body grid grid-cols-2 gap-4">
        <!-- Foto de Referencia -->
        <div class="image-section">
            <div class="image-header">
                <i class="icon-image"></i>
                <span>Foto de Referencia</span>
            </div>
            <img src="..." class="clickable-image" />
        </div>
        
        <!-- Foto de Producción -->
        <div class="image-section">
            <div class="image-header">
                <i class="icon-cake"></i>
                <span>Foto de Producción</span>
            </div>
            <img src="..." class="clickable-image" />
            <!-- O mensaje si no existe -->
            <div class="no-image-message">
                Foto de producción pendiente
            </div>
        </div>
    </div>
    
    <!-- Footer: Detalles del pedido -->
    <div class="modal-footer">
        <div class="details-grid">
            <div>Cliente: Laura</div>
            <div>Fecha de Entrega: 3/10/2025</div>
            <div>Estado: Pendiente</div>
            <div>Canal: Local</div>
        </div>
    </div>
</div>
```

**Estilos TailwindCSS:**
```javascript
// Modal container
class: "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"

// Modal content
class: "bg-white rounded-xl shadow-2xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto"

// Header
class: "border-b border-gray-200 p-6"

// Image sections
class: "p-4 border border-gray-200 rounded-lg"

// Images
class: "w-full h-auto rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
```

### 2. Frontend Component: `lightboxImage(options)`

**Propósito:** Ampliar imágenes en vista fullscreen con navegación.

**Parámetros:**
```javascript
{
    images: [
        { src: "url1", label: "Foto de Referencia" },
        { src: "url2", label: "Foto de Producción" }
    ],
    currentIndex: 0
}
```

**Funcionalidades:**
- Navegación entre imágenes (flechas izquierda/derecha)
- Cerrar con ESC o click fuera de la imagen
- Zoom opcional con scroll del mouse
- Indicador de imagen actual (1/2)

### 3. Backend Method: `getImageOrder()`

**Archivo:** `ctrl-pedidos.php`

**Propósito:** Obtener datos del pedido e imágenes asociadas.

**Entrada:**
```php
$_POST = [
    'opc' => 'getImageOrder',
    'id' => 123  // ID del pedido
]
```

**Salida:**
```php
[
    'status' => 200,
    'message' => 'Datos obtenidos correctamente',
    'data' => [
        'order' => [
            'id' => 123,
            'folio' => 'P-159',
            'name' => 'Pastel de Chocolate',
            'price' => 315.00,
            'cliente' => 'Laura',
            'fecha_entrega' => '2025-10-03',
            'estado' => 'Pendiente',
            'canal' => 'Local'
        ],
        'images' => [
            'reference' => 'uploads/products/reference_123.jpg',
            'production' => 'uploads/orders/production_123.jpg'
        ]
    ]
]
```

### 4. Backend Method: `getOrderImagesById()`

**Archivo:** `mdl-pedidos.php`

**Propósito:** Consultar base de datos para obtener información del pedido e imágenes.

**Query SQL:**
```sql
SELECT 
    o.id,
    o.folio,
    o.name_cliente AS cliente,
    o.fecha_entrega,
    o.estado,
    o.canal,
    o.total AS price,
    p.name,
    p.image AS reference_image,
    o.production_image
FROM pedidos_orders o
LEFT JOIN pedidos_products p ON o.product_id = p.id
WHERE o.id = ?
```

## Data Models

### Order Model
```javascript
{
    id: Number,
    folio: String,
    name: String,           // Nombre del pastel
    price: Number,
    cliente: String,
    fecha_entrega: String,  // Format: YYYY-MM-DD
    estado: String,         // "Pendiente", "En Producción", "Completado"
    canal: String           // "Local", "WhatsApp", "Web"
}
```

### Images Model
```javascript
{
    reference: String,      // URL o path relativo
    production: String      // URL o path relativo (puede ser null)
}
```

### Complete Response Model
```javascript
{
    status: Number,         // 200, 404, 500
    message: String,
    data: {
        order: Order,
        images: Images
    }
}
```

## Integration Points

### 1. Modificación en `lsPedidos()`

Agregar botón "Ver Fotos" en el dropdown de acciones de cada fila:

```javascript
dropdown: [
    {
        icon: "icon-image",
        text: "Ver Fotos",
        onclick: `pedidos.getImageOrder(${id})`
    },
    // ... otros botones existentes
]
```

### 2. Modificación en `lsPedidosProgramados()`

Aplicar la misma lógica que en `lsPedidos()` para mantener consistencia.

### 3. Estructura de archivos de imágenes

```
uploads/
├── products/
│   └── reference_[product_id].jpg    // Fotos de catálogo
└── orders/
    └── production_[order_id].jpg     // Fotos de producción
```

## Error Handling

### Frontend Error Handling

```javascript
async getImageOrder(id) {
    try {
        const response = await useFetch({
            url: this._link,
            data: { opc: 'getImageOrder', id: id }
        });
        
        if (response.status === 200) {
            this.showImagesOrder({ data: response.data });
        } else {
            alert({
                icon: 'error',
                title: 'Error',
                text: response.message || 'No se pudieron cargar las imágenes'
            });
        }
    } catch (error) {
        console.error('Error al obtener imágenes:', error);
        alert({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor'
        });
    }
}
```

### Backend Error Handling

```php
function getImageOrder() {
    $status = 500;
    $message = 'Error al obtener datos del pedido';
    $data = null;
    
    try {
        $id = $_POST['id'] ?? null;
        
        if (!$id) {
            return [
                'status' => 400,
                'message' => 'ID de pedido no proporcionado',
                'data' => null
            ];
        }
        
        $orderData = $this->getOrderImagesById([$id]);
        
        if ($orderData) {
            $status = 200;
            $message = 'Datos obtenidos correctamente';
            $data = [
                'order' => $orderData,
                'images' => [
                    'reference' => $orderData['reference_image'] ?? null,
                    'production' => $orderData['production_image'] ?? null
                ]
            ];
        } else {
            $status = 404;
            $message = 'Pedido no encontrado';
        }
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error interno del servidor';
        error_log($e->getMessage());
    }
    
    return [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
}
```

### Image Loading Error Handling

```javascript
showImagesOrder(options) {
    // ...
    
    // Para cada imagen
    const img = $('<img>', {
        src: imageUrl,
        class: 'w-full h-auto rounded-lg cursor-pointer',
        error: function() {
            $(this).replaceWith(`
                <div class="flex items-center justify-center h-64 bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <i class="icon-alert-circle text-4xl mb-2"></i>
                        <p>Error al cargar imagen</p>
                    </div>
                </div>
            `);
        }
    });
}
```

## Testing Strategy

### Unit Tests

1. **Frontend Tests:**
   - `showImagesOrder()` renderiza correctamente con datos válidos
   - `showImagesOrder()` maneja correctamente cuando falta foto de producción
   - `lightboxImage()` navega correctamente entre imágenes
   - `getImageOrder()` maneja errores de red correctamente

2. **Backend Tests:**
   - `getImageOrder()` retorna datos correctos con ID válido
   - `getImageOrder()` retorna error 404 con ID inexistente
   - `getOrderImagesById()` ejecuta query correctamente
   - Manejo de imágenes faltantes en base de datos

### Integration Tests

1. **Flujo completo:**
   - Usuario hace click en "Ver Fotos"
   - Modal se abre con datos correctos
   - Imágenes se cargan correctamente
   - Click en imagen abre lightbox
   - Navegación en lightbox funciona
   - Cerrar modal retorna a tabla

2. **Edge Cases:**
   - Pedido sin foto de producción
   - Pedido sin foto de referencia
   - Imágenes con URLs rotas
   - Timeout de carga de imágenes
   - Pedido inexistente

### Manual Testing Checklist

- [ ] Botón "Ver Fotos" aparece en tabla de pedidos
- [ ] Modal se abre correctamente al hacer click
- [ ] Foto de referencia se muestra correctamente
- [ ] Foto de producción se muestra o mensaje de pendiente
- [ ] Detalles del pedido son correctos
- [ ] Click en imagen abre lightbox
- [ ] Navegación entre imágenes funciona
- [ ] Cerrar lightbox con ESC funciona
- [ ] Cerrar modal con X funciona
- [ ] Cerrar modal con click fuera funciona
- [ ] Responsive en móvil funciona correctamente
- [ ] Carga de imágenes es rápida (<3 segundos)
- [ ] Manejo de errores muestra mensajes apropiados

## Performance Considerations

### Image Optimization

1. **Compresión de imágenes:**
   - Máximo 1920x1080 píxeles
   - Formato WebP con fallback a JPEG
   - Calidad 85% para balance tamaño/calidad

2. **Lazy Loading:**
   - Cargar imágenes solo cuando el modal se abre
   - Usar placeholders mientras cargan

3. **Caching:**
   - Cache de navegador para imágenes ya vistas
   - Headers HTTP apropiados (Cache-Control, ETag)

### Code Optimization

```javascript
// Debounce para eventos de resize en lightbox
const debouncedResize = debounce(() => {
    // Ajustar tamaño de imagen
}, 250);

// Preload de imagen siguiente en lightbox
const preloadNextImage = (index) => {
    if (index + 1 < images.length) {
        const img = new Image();
        img.src = images[index + 1].src;
    }
};
```

## Security Considerations

1. **Validación de IDs:**
   - Validar que el ID sea numérico en backend
   - Verificar permisos del usuario para ver el pedido

2. **Sanitización de URLs:**
   - Validar que las URLs de imágenes sean del dominio permitido
   - Escapar HTML en nombres y descripciones

3. **Rate Limiting:**
   - Limitar peticiones por usuario/IP
   - Prevenir scraping masivo de imágenes

## Accessibility

1. **Keyboard Navigation:**
   - Tab para navegar entre elementos
   - ESC para cerrar modal/lightbox
   - Flechas para navegar en lightbox

2. **Screen Readers:**
   - Alt text descriptivo en imágenes
   - ARIA labels en botones
   - Roles ARIA apropiados (dialog, img)

3. **Contraste:**
   - Texto legible sobre fondos
   - Indicadores visuales claros

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Fallbacks:**
- CSS Grid → Flexbox
- Fetch API → XMLHttpRequest (ya implementado en useFetch)
- WebP → JPEG

## Dependencies

### Existing:
- jQuery 3.x
- TailwindCSS 2.x
- Bootbox.js (para modales)
- useFetch() (función global existente)

### New (Optional):
- PhotoSwipe o similar para lightbox avanzado (si se requiere zoom/pan)
- Lazy loading library (si performance es crítica)

## Migration Path

1. **Fase 1:** Implementar backend (ctrl + mdl)
2. **Fase 2:** Implementar `showImagesOrder()` básico
3. **Fase 3:** Agregar botón en tabla
4. **Fase 4:** Implementar `lightboxImage()`
5. **Fase 5:** Optimizaciones y testing
6. **Fase 6:** Deploy a producción

## Rollback Plan

Si hay problemas en producción:
1. Remover botón "Ver Fotos" de dropdown
2. Comentar métodos nuevos en JS
3. Comentar métodos nuevos en PHP
4. Restaurar versión anterior desde Git

No afecta funcionalidad existente ya que es feature aditivo.
