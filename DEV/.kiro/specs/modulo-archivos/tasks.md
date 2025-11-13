# Implementation Plan

- [x] 1. Configurar estructura base del módulo


  - Crear directorio `contabilidad/captura/archivos/`
  - Crear archivo `index.php` con contenedor root y scripts de CoffeeSoft
  - Crear estructura de carpetas: `ctrl/`, `mdl/`, `js/`
  - _Requirements: 1.1, 1.2_





- [ ] 2. Implementar modelo de datos (mdl-archivos.php)
- [ ] 2.1 Crear clase base del modelo
  - Crear archivo `mdl/mdl-archivos.php`
  - Extender clase CRUD

  - Configurar propiedades `$bd` y `$util`
  - Definir nombre de base de datos: `rfwsmqex_contabilidad.`
  - _Requirements: 1.1, 2.1, 3.1_

- [ ] 2.2 Implementar métodos de consulta de archivos
  - Crear método `listFiles($array)` con filtros por módulo y búsqueda

  - Crear método `getFileById($array)` para obtener archivo específico
  - Crear método `getFileCountsByModule()` para contadores del dashboard
  - Implementar JOIN con tabla de usuarios para obtener nombre de quien subió
  - _Requirements: 1.4, 1.5, 3.1_


- [ ] 2.3 Implementar métodos de gestión de módulos
  - Crear método `lsModules()` para obtener lista de módulos disponibles




  - Crear método `getFilesByModule($array)` para filtrar por módulo específico
  - _Requirements: 1.2, 3.1_

- [ ] 2.4 Implementar método de eliminación
  - Crear método `deleteFileById($array)` para eliminar registro de BD
  - Usar método `_Delete` de clase CRUD

  - _Requirements: 2.1_

- [ ] 3. Implementar controlador (ctrl-archivos.php)
- [ ] 3.1 Crear estructura base del controlador
  - Crear archivo `ctrl/ctrl-archivos.php`

  - Configurar headers CORS
  - Requerir modelo `mdl-archivos.php`
  - Crear clase `ctrl` que extienda `mdl`
  - Implementar instanciación y llamada dinámica de métodos
  - _Requirements: 1.1, 2.1, 3.1_

- [ ] 3.2 Implementar método init()
  - Obtener lista de módulos con `lsModules()`

  - Obtener contadores de archivos con `getFileCountsByModule()`
  - Retornar array con datos para inicialización del frontend
  - _Requirements: 1.2, 1.5_

- [ ] 3.3 Implementar método ls()
  - Recibir parámetros de filtro: `$_POST['module']`, `$_POST['search']`

  - Llamar a `listFiles()` del modelo
  - Iterar resultados y construir array `$__row[]`
  - Formatear columnas: Módulo, Subido por, Nombre del archivo, Tipo/Tamaño
  - Agregar dropdown con acciones: ver, descargar, eliminar
  - Retornar array con clave `row`
  - _Requirements: 1.4, 3.1, 3.3_

- [ ] 3.4 Implementar método getFile()
  - Recibir `$_POST['id']`

  - Llamar a `getFileById()` del modelo
  - Validar existencia del archivo
  - Retornar datos completos con status 200 o 404
  - _Requirements: 1.4_




- [ ] 3.5 Implementar método deleteFile()
  - Recibir `$_POST['id']`
  - Obtener información del archivo con `getFileById()`
  - Validar existencia del archivo físico
  - Eliminar archivo físico con `unlink()`
  - Eliminar registro de BD con `deleteFileById()`

  - Manejar errores con try-catch
  - Retornar status y message
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ] 3.6 Crear funciones auxiliares
  - Crear función `dropdown($id)` para generar opciones de acciones

  - Crear función `formatFileSize($bytes)` para formatear tamaño de archivo
  - Crear función `getModuleName($moduleId)` para obtener nombre del módulo
  - _Requirements: 1.4, 3.1_

- [ ] 4. Implementar frontend base (archivos.js)
- [ ] 4.1 Crear estructura base del archivo JavaScript
  - Crear archivo `js/archivos.js`

  - Definir variable global `api` apuntando a `ctrl/ctrl-archivos.php`

  - Definir variables globales: `app`, `dashboardFiles`, `adminFiles`
  - Implementar inicialización con jQuery `$(async () => {})`
  - Llamar a `init()` del backend para obtener datos iniciales
  - _Requirements: 1.1_


- [ ] 4.2 Crear clase App principal
  - Crear clase `App` que extienda `Templates`
  - Definir constructor con parámetros `link` y `div_modulo`
  - Definir propiedad `PROJECT_NAME = "archivos"`
  - Implementar método `render()` que llame a layout
  - _Requirements: 1.1_


- [ ] 4.3 Implementar layout principal con tabs
  - Crear método `layout()` en clase App
  - Usar `primaryLayout()` para estructura base
  - Implementar `tabLayout()` con pestañas:
    - Dashboard (activa por defecto)
    - Administrador de archivos

  - Configurar contenedores: `container-dashboard`, `container-admin`
  - _Requirements: 1.2, 1.3_

- [ ] 5. Implementar Dashboard de archivos
- [ ] 5.1 Crear clase DashboardFiles
  - Crear clase `DashboardFiles` que extienda `App`
  - Implementar método `render()` que llame a layout y componentes
  - _Requirements: 1.5_

- [x] 5.2 Implementar layout del dashboard

  - Crear método `layout()` con estructura de dashboard

  - Agregar header con título "📦 Módulo de Archivos"
  - Agregar subtítulo descriptivo
  - Crear contenedor para cards de contadores
  - Crear contenedor para filtros

  - _Requirements: 1.1, 1.5_

- [ ] 5.3 Implementar barra de filtros del dashboard
  - Crear método `filterBarDashboard()`
  - Usar `createfilterBar()` de CoffeeSoft

  - Agregar select de módulos con datos de `init()`
  - Agregar input de búsqueda por nombre de archivo
  - Configurar eventos `onchange` para actualizar vista
  - _Requirements: 1.6, 3.1_

- [ ] 5.4 Implementar cards de contadores
  - Crear método `showCards(data)`

  - Usar componente `infoCard()` de CoffeeSoft
  - Crear cards para:
    - Archivos totales
    - Archivos de ventas
    - Archivos de compras
    - Archivos de proveedores
    - Archivos de almacén
  - Aplicar colores y estilos según diseño
  - _Requirements: 1.5_

- [ ] 6. Implementar administrador de archivos
- [x] 6.1 Crear clase AdminFiles

  - Crear clase `AdminFiles` que extienda `App`

  - Implementar método `render()` que llame a layout y tabla
  - _Requirements: 1.1_

- [ ] 6.2 Implementar layout del administrador
  - Crear método `layout()` con `primaryLayout()`
  - Definir contenedores: `filterBarAdmin`, `containerAdmin`

  - Agregar header con título y descripción
  - _Requirements: 1.1_

- [ ] 6.3 Implementar barra de filtros del administrador
  - Crear método `filterBarFiles()`
  - Usar `createfilterBar()` de CoffeeSoft

  - Agregar select de módulos con opción "Mostrar todas los archivos"

  - Agregar botón de actualizar
  - Configurar evento `onchange` para llamar a `lsFiles()`
  - _Requirements: 1.6, 3.1, 3.2, 3.3_

- [x] 6.4 Implementar tabla de archivos

  - Crear método `lsFiles()`
  - Usar `createTable()` de CoffeeSoft
  - Configurar columnas:
    - Módulo
    - Subido por

    - Nombre del archivo

    - Tipo/Tamaño
    - Acciones
  - Configurar `data: { opc: 'ls' }` para llamar al backend
  - Configurar paginación con DataTables (15 registros por página)
  - Aplicar tema `corporativo` de CoffeeSoft
  - _Requirements: 1.4, 3.1_


- [ ] 7. Implementar funcionalidad de eliminación
- [ ] 7.1 Crear método deleteFile()
  - Implementar método `deleteFile(id)` en clase AdminFiles
  - Usar `swalQuestion()` de CoffeeSoft para confirmación
  - Configurar título: "¿Está seguro de querer eliminar el archivo?"
  - Agregar botones "Continuar" y "Cancelar"

  - _Requirements: 2.1, 2.2, 2.3_

- [ ] 7.2 Implementar lógica de eliminación
  - En callback de confirmación, llamar a backend con `opc: 'deleteFile'`
  - Usar `useFetch()` para petición AJAX
  - Manejar respuesta del servidor
  - Mostrar mensaje de éxito o error con `alert()`

  - Actualizar tabla sin recargar página llamando a `lsFiles()`
  - _Requirements: 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ] 8. Implementar funcionalidades adicionales
- [ ] 8.1 Implementar visualización de archivos
  - Crear método `viewFile(id)` en clase AdminFiles
  - Obtener URL del archivo desde el backend

  - Abrir archivo en nueva ventana con `window.open()`
  - _Requirements: 1.4_

- [ ] 8.2 Implementar descarga de archivos
  - Crear método `downloadFile(id)` en clase AdminFiles

  - Obtener URL del archivo desde el backend

  - Crear elemento `<a>` temporal con atributo `download`
  - Simular click para iniciar descarga
  - _Requirements: 1.4_

- [ ] 9. Integración y pruebas
- [x] 9.1 Integrar todos los componentes

  - Verificar que todas las clases estén correctamente instanciadas
  - Verificar que los eventos entre componentes funcionen
  - Verificar que los filtros actualicen correctamente las vistas
  - Verificar que los contadores se actualicen después de eliminar
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7_




- [ ] 9.2 Validar flujo completo de eliminación
  - Probar eliminación de archivo con confirmación
  - Verificar que el archivo físico se elimine
  - Verificar que el registro de BD se elimine
  - Verificar que la tabla se actualice sin recargar

  - Verificar que los contadores se actualicen
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_

- [ ] 9.3 Validar filtrado por módulo
  - Probar filtro "Mostrar todas los archivos"

  - Probar filtro por cada módulo específico
  - Verificar que la tabla muestre solo archivos del módulo seleccionado
  - Verificar que los contadores se actualicen según el filtro
  - Verificar que el filtro se mantenga hasta que el usuario lo cambie
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7_

- [ ] 9.4 Validar visualización de información
  - Verificar que todas las columnas de la tabla muestren datos correctos
  - Verificar formato de fecha en español
  - Verificar formato de tamaño de archivo (KB, MB)
  - Verificar que los nombres de módulos sean correctos
  - Verificar que los nombres de usuarios sean correctos
  - _Requirements: 1.4_

- [ ] 9.5 Validar contadores del dashboard
  - Verificar que el contador de archivos totales sea correcto
  - Verificar que los contadores por módulo sean correctos
  - Verificar que los contadores se actualicen después de eliminar
  - Verificar que los contadores se actualicen según el filtro aplicado
  - _Requirements: 1.5_

- [ ] 10. Aplicar estilos y tema CoffeeSoft
- [ ] 10.1 Aplicar tema corporativo
  - Verificar que las tablas usen tema `corporativo` de CoffeeSoft
  - Verificar que los colores sigan la paleta de CoffeeSoft
  - Verificar que los iconos sean consistentes
  - Aplicar estilos TailwindCSS según diseño
  - _Requirements: 1.1, 1.4_

- [ ] 10.2 Optimizar responsive design
  - Verificar que el dashboard sea responsive
  - Verificar que la tabla sea responsive
  - Verificar que los filtros sean responsive
  - Ajustar clases de Bootstrap/Tailwind según necesidad
  - _Requirements: 1.1, 1.4_

- [ ] 11. Documentación y limpieza de código
- [ ] 11.1 Documentar código JavaScript
  - Agregar comentarios solo donde sea necesario (lógica compleja)
  - Verificar nomenclatura de métodos en camelCase
  - Verificar que no haya código duplicado
  - _Requirements: 1.1_

- [ ] 11.2 Documentar código PHP
  - Agregar comentarios solo donde sea necesario
  - Verificar nomenclatura de métodos
  - Verificar que controlador y modelo tengan nombres diferentes
  - _Requirements: 1.1_

- [ ] 11.3 Verificar seguridad
  - Validar que todas las queries usen prepared statements
  - Validar que los inputs estén sanitizados
  - Validar que los archivos se eliminen de forma segura
  - Verificar prevención de SQL injection
  - _Requirements: 2.1_
