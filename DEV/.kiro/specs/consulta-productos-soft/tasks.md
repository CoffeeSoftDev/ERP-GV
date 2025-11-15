# Implementation Plan

- [x] 1. Crear estructura de directorios y archivos base



  - Crear directorio `DEV/kpi/marketing/ventas/soft/` con subdirectorios `ctrl/`, `mdl/`, `layout/`, `src/js/`
  - Crear archivos vacíos: `index.php`, `ctrl/ctrl-productos-soft.php`, `mdl/mdl-productos-soft.php`, `src/js/productos-soft.js`
  - Crear archivos de layout: `layout/head.php`, `layout/core-libraries.php`, `layout/script.php`
  - _Requirements: 8.5_



- [ ] 2. Implementar capa de modelo (Model Layer)
  - [ ] 2.1 Crear clase mdl en mdl-productos-soft.php
    - Extender de clase CRUD


    - Implementar constructor con inicialización de utilidades y prefijo de base de datos
    - Definir propiedad `$bd` con valor `"rfwsmqex_gvsl_finanzas."`
    - _Requirements: 8.1, 8.5_
  
  - [x] 2.2 Implementar método listProductos()


    - Crear query SQL con JOIN entre soft_productos, soft_costsys, udn y recetas
    - Implementar filtros opcionales por UDN y búsqueda
    - Usar prepared statements con parámetros dinámicos
    - Retornar array con todos los campos necesarios: id_Producto, clave_producto_soft, descripcion, grupo, UDN, costo, precios


    - _Requirements: 1.1, 1.3, 2.3, 3.2, 3.3_
  
  - [ ] 2.3 Implementar método getProductoById()
    - Usar método _Select de clase CRUD
    - Consultar tabla soft_productos por id_Producto


    - Retornar array con datos del producto o null si no existe
    - _Requirements: 1.1_
  
  - [x] 2.4 Implementar método lsUDN()


    - Consultar tabla udn con filtro Stado = 1
    - Retornar array con formato: [['id' => idUDN, 'valor' => UDN]]
    - Ordenar por nombre de UDN ascendente


    - _Requirements: 3.2_

- [ ] 3. Implementar capa de controlador (Controller Layer)
  - [ ] 3.1 Crear clase ctrl en ctrl-productos-soft.php
    - Extender de clase mdl
    - Incluir archivos de dependencias: mdl-productos-soft.php, _Utileria.php, coffeSoft.php
    - Configurar headers CORS para permitir peticiones AJAX


    - _Requirements: 8.2, 8.5_
  
  - [ ] 3.2 Implementar método init()
    - Llamar a lsUDN() para obtener lista de unidades de negocio
    - Retornar array con estructura: ['udn' => lista de UDN]
    - _Requirements: 3.2_
  
  - [ ] 3.3 Implementar método lsProductos()
    - Obtener parámetros POST: udn, search


    - Validar y sanitizar parámetros de entrada
    - Llamar a listProductos() del modelo con parámetros
    - Formatear datos para la tabla usando formatProductRow()
    - Construir array de encabezados: ['Clave', 'Descripción', 'Grupo', 'UDN', 'Costo', 'Precio Venta', 'Precio Licencia']
    - Retornar array con estructura: ['status' => 200, 'row' => datos formateados, 'thead' => encabezados]
    - _Requirements: 1.1, 1.2, 2.1, 2.2, 2.3, 2.4, 3.3, 5.1, 5.2, 5.3_
  



  - [ ] 3.4 Implementar método getProducto()
    - Obtener id desde POST
    - Llamar a getProductoById() del modelo
    - Retornar respuesta con formato: ['status' => código, 'message' => mensaje, 'data' => datos]


    - Manejar caso cuando producto no existe
    - _Requirements: 7.4_
  
  - [ ] 3.5 Implementar método privado formatProductRow()
    - Recibir array de producto

    - Formatear valores monetarios usando formatMoney()
    - Construir array con estructura de fila para tabla
    - Aplicar clases CSS de Tailwind para alineación
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [ ] 3.6 Implementar método privado formatMoney()
    - Recibir valor numérico
    - Formatear con símbolo $, 2 decimales y separador de miles

    - Retornar "$ 0.00" si valor es null o 0
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [ ] 3.7 Implementar manejo de errores y logging
    - Envolver operaciones en try-catch

    - Usar writeToLog() para registrar errores
    - Retornar respuestas de error con status 500 y mensaje descriptivo
    - _Requirements: 7.4, 7.5_
  

  - [ ] 3.8 Implementar dispatcher de métodos
    - Crear instancia de clase ctrl
    - Obtener parámetro 'opc' desde POST
    - Llamar método dinámicamente usando $_POST['opc']


    - Retornar respuesta en formato JSON
    - _Requirements: 1.5, 8.2_

- [x] 4. Implementar vista principal (View Layer)


  - [ ] 4.1 Crear index.php con estructura HTML base
    - Incluir layout/head.php y layout/core-libraries.php
    - Cargar CoffeeSoft Framework y plugins desde CDN
    - Incluir navbar del sistema
    - Crear contenedor principal con id="root"
    - Agregar breadcrumbs: KPI > Marketing > Ventas > Productos Soft
    - Cargar script productos-soft.js con cache busting


    - _Requirements: 6.1, 6.2, 6.3, 6.5_
  
  - [ ] 4.2 Crear layout/head.php
    - Definir meta tags (charset, viewport)
    - Incluir título de página
    - Cargar estilos CSS del sistema
    - _Requirements: 6.1, 6.3_


  
  - [ ] 4.3 Crear layout/core-libraries.php
    - Cargar jQuery si es necesario
    - Cargar librerías core del sistema
    - _Requirements: 6.5_
  
  - [ ] 4.4 Crear layout/script.php (opcional)
    - Incluir scripts adicionales si son necesarios


    - _Requirements: 6.5_

- [ ] 5. Implementar lógica del cliente (JavaScript)
  - [ ] 5.1 Crear objeto productosSoft en productos-soft.js
    - Definir estructura del objeto con métodos y propiedades
    - Crear propiedad state para manejar estado de la aplicación


    - Inicializar state con: udn='all', search='', currentPage=1, itemsPerPage=25, productos=[]
    - _Requirements: 2.1, 3.3, 4.1, 4.2, 4.3, 4.4_
  
  - [ ] 5.2 Implementar método init()
    - Llamar a loadInitialData()


    - Llamar a render()
    - Llamar a attachEventListeners()
    - Manejar errores de inicialización
    - _Requirements: 1.1, 7.2_
  
  - [x] 5.3 Implementar método loadInitialData()




    - Hacer petición AJAX a ctrl-productos-soft.php con opc=init
    - Guardar lista de UDN en state.udnList
    - Llamar a loadProductos()
    - Mostrar indicador de carga durante la petición
    - _Requirements: 3.2, 7.2_
  
  - [ ] 5.4 Implementar método loadProductos()
    - Construir parámetros: opc=lsProductos, udn, search
    - Hacer petición AJAX a ctrl-productos-soft.php
    - Guardar productos en state.productos
    - Llamar a render() para actualizar vista
    - Manejar errores de red y respuestas con status !== 200
    - _Requirements: 1.1, 1.5, 2.1, 2.2, 2.3, 2.4, 3.3, 7.1, 7.2, 7.4_
  
  - [ ] 5.5 Implementar método render()
    - Construir HTML completo de la interfaz
    - Llamar a renderFilters(), renderTable(), renderPagination()
    - Insertar HTML en elemento con id="root"




    - _Requirements: 1.1, 1.2, 6.4_
  
  - [ ] 5.6 Implementar método renderFilters()
    - Crear HTML para campo de búsqueda con id="search"


    - Crear HTML para selector de UDN con id="udn-filter"
    - Incluir opción "Todas" en selector de UDN
    - Aplicar estilos de Tailwind CSS
    - _Requirements: 2.1, 2.2, 3.1, 3.2, 3.4, 6.3, 6.4_



  
  - [ ] 5.7 Implementar método renderTable()
    - Crear tabla HTML con encabezados desde state
    - Aplicar paginación a productos (itemsPerPage)
    - Renderizar filas de productos con datos formateados
    - Mostrar mensaje "No se encontraron productos" cuando array está vacío


    - Aplicar clases CSS de Tailwind para tabla responsive
    - _Requirements: 1.1, 1.2, 1.4, 4.1, 4.2, 5.2, 5.3, 6.3, 6.4_
  
  - [ ] 5.8 Implementar método renderPagination()
    - Calcular número total de páginas
    - Crear controles de paginación (anterior, números, siguiente)
    - Mostrar contador de productos: "Mostrando X-Y de Z productos"
    - Deshabilitar botones cuando no hay más páginas
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [ ] 5.9 Implementar método attachEventListeners()
    - Agregar listener al campo de búsqueda con debounce de 300ms
    - Agregar listener al selector de UDN
    - Agregar listeners a botones de paginación
    - Actualizar state y llamar a loadProductos() en cada evento
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.3, 3.5, 4.4_
  
  - [ ] 5.10 Implementar método debounce()
    - Crear función de utilidad para debouncing
    - Usar timeout para retrasar ejecución
    - Retornar función que cancela timeout anterior
    - _Requirements: 2.2, 7.1_
  
  - [ ] 5.11 Implementar método showError()
    - Mostrar mensaje de error al usuario
    - Usar alertas o toasts del framework CoffeeSoft
    - Incluir opción para cerrar mensaje
    - _Requirements: 7.4_
  
  - [ ] 5.12 Implementar método showLoading()
    - Mostrar indicador de carga durante peticiones AJAX
    - Usar spinner o skeleton del framework
    - Ocultar indicador cuando carga completa
    - _Requirements: 7.2_
  
  - [ ] 5.13 Inicializar aplicación cuando DOM esté listo
    - Agregar event listener para DOMContentLoaded
    - Llamar a productosSoft.init()
    - _Requirements: 1.1_

- [ ] 6. Implementar optimizaciones de base de datos
  - [ ] 6.1 Crear índices en tabla soft_productos
    - Crear índice en columna id_udn
    - Crear índice en columna descripcion
    - Crear índice en columna clave_producto_soft
    - Crear índice en columna id_grupo_productos
    - _Requirements: 7.1, 7.3_

- [ ] 7. Implementar validaciones y seguridad
  - [ ] 7.1 Agregar validación de inputs en controlador
    - Validar parámetro udn es entero positivo o 'all'
    - Sanitizar parámetro search para prevenir XSS
    - Validar parámetro id es entero positivo
    - Retornar error 400 si validación falla
    - _Requirements: 7.4_
  
  - [ ] 7.2 Implementar escape de HTML en outputs
    - Usar htmlspecialchars() en datos que se muestran en HTML
    - Aplicar en descripción, clave y nombre de grupo
    - _Requirements: 7.4_

- [ ] 8. Integración y pruebas finales
  - [ ] 8.1 Verificar integración completa del flujo
    - Probar carga inicial de página
    - Probar filtrado por UDN
    - Probar búsqueda de productos
    - Probar paginación
    - Verificar formato de precios
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [ ] 8.2 Verificar responsive design
    - Probar en diferentes tamaños de pantalla
    - Verificar tabla es scrollable en móvil
    - Verificar filtros funcionan en móvil
    - _Requirements: 6.4_
  
  - [ ] 8.3 Verificar manejo de errores
    - Simular error de conexión a base de datos
    - Simular respuesta vacía
    - Verificar mensajes de error se muestran correctamente
    - _Requirements: 7.4, 7.5_
  
  - [ ] 8.4 Verificar performance
    - Medir tiempo de carga inicial
    - Verificar queries se ejecutan en < 1 segundo
    - Verificar no hay memory leaks en JavaScript
    - _Requirements: 7.1, 7.2, 7.3_
  
  - [ ] 8.5 Verificar consistencia con el sistema
    - Verificar navbar y breadcrumbs se muestran correctamente
    - Verificar estilos son consistentes con otros módulos
    - Verificar no hay conflictos con otros scripts
    - _Requirements: 6.1, 6.2, 6.3, 6.5_
