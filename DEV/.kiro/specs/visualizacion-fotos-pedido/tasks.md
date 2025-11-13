# Implementation Plan

- [x] 1. Implementar backend para obtención de datos de imágenes


  - Crear método `getOrderImagesById()` en `mdl-pedidos.php` que consulte la base de datos para obtener información del pedido e imágenes asociadas (referencia y producción)
  - Crear método `getImageOrder()` en `ctrl-pedidos.php` que procese la petición, valide el ID del pedido y retorne datos formateados con manejo de errores
  - Implementar validación de ID numérico y manejo de casos cuando no existen imágenes
  - _Requirements: 1.1, 2.1, 2.4, 5.1_





- [ ] 2. Crear componente modal de visualización de fotos
  - [ ] 2.1 Implementar método `showImagesOrder(options)` en `pedidos-list.js`
    - Crear estructura del modal con header (folio, nombre, precio), body (dos secciones para fotos) y footer (detalles del pedido)
    - Aplicar estilos TailwindCSS para layout responsive con grid de 2 columnas

    - Implementar lógica para mostrar mensaje "Foto de producción pendiente" cuando no existe la imagen
    - Agregar botón de cerrar y funcionalidad para cerrar con click fuera del modal
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 3.3, 3.4, 3.5_

  - [x] 2.2 Implementar método `getImageOrder(id)` en `pedidos-list.js`


    - Crear petición asíncrona al backend usando `useFetch()` con manejo de errores
    - Implementar indicador de carga mientras se obtienen los datos
    - Validar respuesta del servidor y mostrar alertas apropiadas en caso de error
    - Llamar a `showImagesOrder()` con los datos obtenidos
    - _Requirements: 1.2, 5.1, 5.2, 5.3_



- [ ] 3. Integrar botón "Ver Fotos" en tabla de pedidos
  - Modificar método `lsPedidos()` para agregar opción "Ver Fotos" en el dropdown de acciones de cada fila
  - Modificar método `lsPedidosProgramados()` para agregar la misma opción y mantener consistencia
  - Configurar onclick del botón para llamar a `pedidos.getImageOrder(id)`
  - Implementar lógica para mostrar el botón solo cuando el pedido tenga foto de referencia
  - _Requirements: 1.1, 1.2, 1.3_


- [ ] 4. Implementar visor lightbox para ampliación de imágenes
  - [ ] 4.1 Crear método `lightboxImage(options)` en `pedidos-list.js`
    - Implementar modal fullscreen con fondo oscuro semitransparente
    - Crear estructura para mostrar imagen ampliada con controles de navegación
    - Agregar indicador de imagen actual (ej: "1/2")

    - Implementar botón de cerrar visible y funcionalidad con ESC
    - _Requirements: 4.1, 4.2, 4.3_

  - [x] 4.2 Agregar navegación entre imágenes en lightbox

    - Implementar controles de navegación (flechas izquierda/derecha)
    - Agregar funcionalidad de teclado (flechas) para navegar
    - Implementar preload de imagen siguiente para mejorar experiencia
    - Cerrar lightbox con click fuera de la imagen
    - _Requirements: 4.4, 4.5_



  - [ ] 4.3 Conectar lightbox con modal principal
    - Agregar event listeners en imágenes del modal para abrir lightbox al hacer click
    - Pasar array de imágenes y índice actual al lightbox
    - Mantener estado del modal principal al abrir/cerrar lightbox
    - _Requirements: 4.1, 4.2_

- [ ] 5. Implementar optimización de carga de imágenes
  - Agregar placeholders o spinners mientras las imágenes se cargan
  - Implementar manejo de error de carga con imagen de fallback o mensaje
  - Configurar timeout de 10 segundos para carga de imágenes
  - Agregar lazy loading para cargar imágenes solo cuando el modal se abre
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 6. Agregar manejo de errores y validaciones
  - Implementar validación de URLs de imágenes en frontend
  - Agregar manejo de errores de red con mensajes descriptivos
  - Implementar validación de permisos en backend (verificar que usuario puede ver el pedido)
  - Agregar sanitización de datos en backend para prevenir XSS
  - _Requirements: 2.4, 5.3_

- [ ]* 7. Implementar mejoras de accesibilidad
  - Agregar atributos ARIA apropiados (roles, labels) en modal y lightbox
  - Implementar navegación completa por teclado (Tab, ESC, flechas)
  - Agregar alt text descriptivo en todas las imágenes
  - Verificar contraste de colores para legibilidad
  - _Requirements: 1.4, 4.3, 4.4_

- [ ]* 8. Optimizar performance
  - Implementar compresión de imágenes a máximo 1920x1080 píxeles
  - Configurar headers HTTP de cache apropiados en servidor
  - Agregar debounce en eventos de resize del lightbox
  - Implementar cache de imágenes ya visualizadas en navegador
  - _Requirements: 5.4, 5.5_

- [ ]* 9. Testing y validación
  - Verificar funcionamiento en diferentes navegadores (Chrome, Firefox, Safari, Edge)
  - Probar responsive en dispositivos móviles y tablets
  - Validar manejo de casos edge (imágenes faltantes, URLs rotas, timeouts)
  - Realizar pruebas de carga con múltiples usuarios simultáneos
  - _Requirements: Todos_
