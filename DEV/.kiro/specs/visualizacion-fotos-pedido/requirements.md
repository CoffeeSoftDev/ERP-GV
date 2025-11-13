# Requirements Document

## Introduction

Este documento define los requisitos para implementar la funcionalidad de visualización de fotos de pedidos en el sistema de Control Fogaza. La funcionalidad permitirá a los usuarios comparar la foto de referencia utilizada al crear el pedido con la foto final del producto terminado.

## Glossary

- **Sistema**: Aplicación web Control Fogaza para gestión de pedidos de pastelería
- **Usuario**: Persona que opera el sistema (encargado de producción, ventas, administrador)
- **Pedido**: Registro de solicitud de pastel con datos del cliente y producto
- **Foto de Referencia**: Imagen del catálogo o ejemplo utilizada al generar el pedido
- **Foto de Producción**: Imagen del pastel terminado subida por producción
- **Modal**: Ventana emergente que muestra información sin abandonar la página actual
- **Visor de Imágenes**: Componente que permite visualizar y ampliar fotografías

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a un botón "Ver Fotos" en el listado de pedidos, para poder visualizar las imágenes asociadas a cada pedido de forma rápida.

#### Acceptance Criteria

1. WHEN el Usuario visualiza la tabla de pedidos, THE Sistema SHALL mostrar un botón "Ver Fotos" en la columna de acciones de cada registro
2. WHEN el Usuario hace clic en el botón "Ver Fotos", THE Sistema SHALL abrir un modal con las fotografías del pedido seleccionado
3. THE Sistema SHALL mostrar el botón "Ver Fotos" únicamente para pedidos que tengan al menos una foto de referencia registrada
4. WHEN el Usuario hace clic fuera del modal o en el botón de cerrar, THE Sistema SHALL cerrar el modal y retornar al listado de pedidos

### Requirement 2

**User Story:** Como usuario del sistema, quiero visualizar en el modal tanto la foto de referencia como la foto de producción, para comparar el producto solicitado con el entregado.

#### Acceptance Criteria

1. WHEN el Modal se abre, THE Sistema SHALL mostrar dos secciones claramente diferenciadas: "Foto de Referencia" y "Foto de Producción"
2. THE Sistema SHALL mostrar la foto de referencia en el lado izquierdo del modal con un ícono identificador
3. THE Sistema SHALL mostrar la foto de producción en el lado derecho del modal con un ícono identificador diferente
4. IF la foto de producción no está disponible, THEN THE Sistema SHALL mostrar un mensaje "Foto de producción pendiente" en lugar de la imagen
5. THE Sistema SHALL mantener las proporciones originales de las imágenes sin distorsionarlas

### Requirement 3

**User Story:** Como usuario del sistema, quiero ver los detalles del pedido junto con las fotos, para tener contexto completo de la información.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar el nombre del pastel en el encabezado del modal
2. THE Sistema SHALL mostrar el folio del pedido en el encabezado del modal
3. THE Sistema SHALL mostrar el precio total del pedido en el encabezado del modal
4. WHEN el Modal se abre, THE Sistema SHALL mostrar una sección "Detalles del Pedido" con los campos: Cliente, Fecha de Entrega, Estado y Canal
5. THE Sistema SHALL formatear el precio con el símbolo de moneda y dos decimales

### Requirement 4

**User Story:** Como usuario del sistema, quiero ampliar las fotos al hacer clic sobre ellas, para ver los detalles con mayor claridad.

#### Acceptance Criteria

1. WHEN el Usuario hace clic sobre la foto de referencia, THE Sistema SHALL ampliar la imagen en una vista tipo lightbox
2. WHEN el Usuario hace clic sobre la foto de producción, THE Sistema SHALL ampliar la imagen en una vista tipo lightbox
3. WHEN la imagen está ampliada, THE Sistema SHALL mostrar un botón de cerrar visible
4. WHEN el Usuario hace clic fuera de la imagen ampliada, THE Sistema SHALL cerrar la vista lightbox y retornar al modal
5. THE Sistema SHALL permitir navegar entre ambas fotos cuando estén ampliadas usando controles de navegación

### Requirement 5

**User Story:** Como usuario del sistema, quiero que las imágenes se carguen de forma optimizada, para evitar demoras en la visualización del modal.

#### Acceptance Criteria

1. WHEN el Modal se abre, THE Sistema SHALL mostrar un indicador de carga mientras las imágenes se descargan
2. THE Sistema SHALL cargar las imágenes de forma asíncrona sin bloquear la interfaz
3. IF una imagen no se puede cargar después de 10 segundos, THEN THE Sistema SHALL mostrar un mensaje de error "Error al cargar imagen"
4. THE Sistema SHALL comprimir las imágenes a un tamaño máximo de 1920x1080 píxeles para optimizar la carga
5. THE Sistema SHALL cachear las imágenes ya visualizadas para mejorar el rendimiento en consultas posteriores
