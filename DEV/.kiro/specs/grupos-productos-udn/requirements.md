# Requirements Document

## Introduction

Este documento define los requerimientos para el desarrollo de una nueva pestaña dentro del módulo de productos Soft Restaurant que permitirá visualizar grupos de productos organizados por Unidad de Negocio (UDN) en formato de tarjetas (cards). Los usuarios podrán seleccionar un grupo para ver los productos asociados, facilitando la navegación y consulta de productos por categorías.

## Glossary

- **Sistema**: El módulo de grupos de productos por UDN dentro del sistema Soft Restaurant
- **Usuario**: Persona autorizada que accede al sistema para consultar grupos y productos
- **Grupo de Producto**: Categoría o clasificación de productos (tabla soft_grupo_productos)
- **UDN**: Unidad de Negocio identificada por id_UDN
- **Card**: Componente visual tipo tarjeta que muestra información resumida de un grupo
- **Grid Layout**: Disposición de elementos en formato de cuadrícula responsive
- **Producto**: Registro en la tabla soft_productos asociado a un grupo específico
- **FilterBar**: Barra de filtros que permite seleccionar la UDN
- **Tab**: Pestaña de navegación dentro del módulo principal
- **Backend**: Controlador PHP que procesa las peticiones y consulta la base de datos
- **Frontend**: Interfaz JavaScript que renderiza los componentes visuales

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a una nueva pestaña de "Grupos por UDN", para poder visualizar los grupos de productos organizados por unidad de negocio.

#### Acceptance Criteria

1. THE Sistema SHALL agregar una nueva pestaña llamada "Grupos por UDN" en el módulo de productos
2. WHEN el Usuario hace clic en la pestaña, THE Sistema SHALL mostrar el contenido de grupos
3. THE Sistema SHALL mantener la estructura de tabs existente sin afectar otras pestañas
4. THE Sistema SHALL aplicar el mismo tema visual (theme: 'light', type: 'short') que las pestañas existentes
5. THE Sistema SHALL generar automáticamente un contenedor con id "container-grupos-udn"

### Requirement 2

**User Story:** Como usuario del sistema, quiero seleccionar una UDN desde un filtro, para ver únicamente los grupos de productos de esa unidad de negocio.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar un selector desplegable (select) con las UDN disponibles
2. THE Sistema SHALL obtener la lista de UDN mediante el método init() del controlador
3. WHEN el Usuario selecciona una UDN, THE Sistema SHALL ejecutar una consulta al backend con opc: "lsGrupos"
4. THE Sistema SHALL enviar el parámetro udn al backend para filtrar los grupos
5. THE Sistema SHALL actualizar automáticamente las cards de grupos al cambiar la UDN seleccionada

### Requirement 3

**User Story:** Como usuario del sistema, quiero ver los grupos de productos en formato de tarjetas (cards), para tener una visualización más intuitiva y atractiva.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar los grupos en un layout tipo grid responsive
2. THE Sistema SHALL renderizar cada grupo como una card individual
3. THE Sistema SHALL mostrar en cada card: nombre del grupo y cantidad de productos
4. THE Sistema SHALL aplicar estilos TailwindCSS para las cards (bg-white, border, rounded-lg, hover effects)
5. THE Sistema SHALL organizar las cards en un grid adaptable (grid-cols-2 md:grid-cols-4 lg:grid-cols-6)

### Requirement 4

**User Story:** Como usuario del sistema, quiero hacer clic en una card de grupo, para ver los productos asociados a ese grupo específico.

#### Acceptance Criteria

1. WHEN el Usuario hace clic en una card de grupo, THE Sistema SHALL ejecutar el método lsProductos()
2. THE Sistema SHALL enviar el parámetro grupo_id al backend mediante opc: "lsProductos"
3. THE Sistema SHALL filtrar los productos mostrando únicamente los del grupo seleccionado
4. THE Sistema SHALL mantener el filtro de UDN activo al consultar productos por grupo
5. THE Sistema SHALL reemplazar el contenido de cards por una tabla de productos

### Requirement 5

**User Story:** Como usuario del sistema, quiero ver los productos filtrados en una tabla, para consultar la información detallada de cada producto del grupo.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar los productos en una tabla usando createTable() de CoffeeSoft
2. THE Sistema SHALL incluir las columnas: descripción, grupo, homologación, costo y precio de venta
3. THE Sistema SHALL aplicar el tema 'corporativo' con paginación de 15 registros por página
4. THE Sistema SHALL alinear correctamente las columnas (center para homologación, right para precios)
5. THE Sistema SHALL mantener la funcionalidad de búsqueda y ordenamiento de DataTables

### Requirement 6

**User Story:** Como usuario del sistema, quiero un botón de "Regresar", para volver a la vista de cards de grupos después de consultar productos.

#### Acceptance Criteria

1. THE Sistema SHALL mostrar un botón "Regresar a Grupos" WHEN se visualiza la tabla de productos
2. WHEN el Usuario hace clic en el botón, THE Sistema SHALL volver a renderizar las cards de grupos
3. THE Sistema SHALL mantener la UDN seleccionada al regresar a la vista de grupos
4. THE Sistema SHALL ocultar el botón "Regresar" WHEN se muestra la vista de cards
5. THE Sistema SHALL aplicar estilos consistentes al botón (class: "col-sm-2")

### Requirement 7

**User Story:** Como desarrollador del sistema, quiero que el backend implemente el método lsGrupos, para obtener los grupos filtrados por UDN desde la base de datos.

#### Acceptance Criteria

1. THE Sistema SHALL implementar el método lsGrupos() en el controlador (ctrl)
2. THE Sistema SHALL recibir el parámetro udn desde $_POST
3. THE Sistema SHALL llamar al método listGrupos() del modelo con el parámetro udn
4. THE Sistema SHALL retornar un array con estructura: [{ id, valor, cantidad_productos }]
5. THE Sistema SHALL manejar el caso especial WHEN udn = 'all' para mostrar todos los grupos

### Requirement 8

**User Story:** Como desarrollador del sistema, quiero que el modelo ejecute consultas SQL optimizadas, para obtener grupos con su cantidad de productos de forma eficiente.

#### Acceptance Criteria

1. THE Sistema SHALL implementar el método listGrupos() en el modelo (mdl)
2. THE Sistema SHALL ejecutar una consulta SQL con JOIN entre soft_grupo_productos y soft_productos
3. THE Sistema SHALL usar COUNT() para obtener la cantidad de productos por grupo
4. THE Sistema SHALL aplicar filtro WHERE por id_UDN WHEN el parámetro udn no sea 'all'
5. THE Sistema SHALL usar GROUP BY para agrupar productos por id_grupo_productos

### Requirement 9

**User Story:** Como usuario del sistema, quiero que las cards tengan efectos visuales al interactuar, para mejorar la experiencia de usuario.

#### Acceptance Criteria

1. THE Sistema SHALL aplicar efecto hover a las cards (hover:border-blue-500, hover:shadow-lg)
2. THE Sistema SHALL cambiar el cursor a pointer al pasar sobre una card
3. THE Sistema SHALL aplicar transiciones suaves (transition-all) a los efectos hover
4. THE Sistema SHALL mantener un diseño limpio con padding y espaciado consistente
5. THE Sistema SHALL usar iconos o emojis para mejorar la identificación visual de grupos

### Requirement 10

**User Story:** Como desarrollador del sistema, quiero que el código siga los estándares de CoffeeSoft, para mantener consistencia con el resto del proyecto.

#### Acceptance Criteria

1. THE Sistema SHALL extender la clase Templates en el archivo JavaScript
2. THE Sistema SHALL usar métodos de CoffeeSoft: createfilterBar(), createTable()
3. THE Sistema SHALL seguir la nomenclatura: lsGrupos() en frontend, listGrupos() en modelo
4. THE Sistema SHALL usar useFetch() para peticiones AJAX asíncronas
5. THE Sistema SHALL aplicar la estructura MVC: ctrl-productos-soft.php, mdl-productos-soft.php, productos-soft.js

### Requirement 11

**User Story:** Como usuario del sistema, quiero que los datos se carguen rápidamente, para no experimentar demoras al cambiar entre grupos y productos.

#### Acceptance Criteria

1. THE Sistema SHALL completar la carga de grupos en menos de 2 segundos
2. THE Sistema SHALL completar la carga de productos por grupo en menos de 3 segundos
3. THE Sistema SHALL mostrar indicadores de carga WHILE se obtienen datos del backend
4. WHEN ocurre un error, THE Sistema SHALL mostrar un mensaje descriptivo al usuario
5. THE Sistema SHALL implementar manejo de errores en las peticiones AJAX

### Requirement 12

**User Story:** Como usuario del sistema, quiero que la interfaz sea responsive, para poder usar el módulo desde diferentes dispositivos.

#### Acceptance Criteria

1. THE Sistema SHALL adaptar el grid de cards según el tamaño de pantalla (2/4/6 columnas)
2. THE Sistema SHALL mantener la legibilidad de las cards en dispositivos móviles
3. THE Sistema SHALL ajustar el tamaño de fuente y espaciado para pantallas pequeñas
4. THE Sistema SHALL mantener la funcionalidad completa en tablets y móviles
5. THE Sistema SHALL usar clases responsive de TailwindCSS (sm:, md:, lg:)
