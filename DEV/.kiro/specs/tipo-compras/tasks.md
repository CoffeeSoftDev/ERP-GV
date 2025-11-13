# Implementation Plan - Módulo Tipos de Compra

- [ ] 1. Crear estructura de base de datos
  - Crear tabla `tipo_compra` con campos: id, nombre, activo, date_creation
  - Agregar índices en campos `activo` y `nombre` para optimizar consultas
  - Insertar registros iniciales de ejemplo (Corporativo, Fondo fijo, Crédito)
  - _Requirements: 1.1, 2.5, 2.6, 2.7_

- [ ] 2. Implementar modelo de datos (mdl-tipo-compras.php)
- [ ] 2.1 Crear archivo mdl-tipo-compras.php con estructura base
  - Extender clase CRUD
  - Configurar propiedades $bd y $util
  - Definir nombre de base de datos: rfwsmqex_contabilidad
  - _Requirements: 1.1, 1.2_

- [ ] 2.2 Implementar métodos de consulta
  - Crear método `listTipoCompras($array)` para listar todos los tipos
  - Crear método `getTipoCompraById($id)` para obtener un tipo específico
  - Usar método `_Select` de clase CRUD con estructura correcta
  - _Requirements: 1.1, 3.2_

- [ ] 2.3 Implementar métodos de validación
  - Crear método `existsTipoCompraByName($array)` para validar duplicados
  - Implementar consulta con LOWER() para comparación case-insensitive
  - _Requirements: 2.5, 2.6, 3.5_

- [ ] 2.4 Implementar métodos de escritura
  - Crear método `createTipoCompra($array)` usando `_Insert`
  - Crear método `updateTipoCompra($array)` usando `_Update`
  - Asegurar uso correcto de `$this->util->sql()` para sanitización
  - _Requirements: 2.7, 3.6_

- [ ] 3. Implementar controlador (ctrl-tipo-compras.php)
- [ ] 3.1 Crear archivo ctrl-tipo-compras.php con estructura base
  - Configurar session_start() y validación de $_POST['opc']
  - Requerir archivos mdl-tipo-compras.php
  - Crear clase ctrl que extienda mdl
  - _Requirements: 1.1_

- [ ] 3.2 Implementar método ls() para listar tipos
  - Llamar a `listTipoCompras()` del modelo
  - Iterar resultados y construir array $__row con formato de tabla
  - Agregar columna de acciones con botones editar y toggle estado
  - Usar función auxiliar `renderStatus()` para mostrar estado
  - Retornar array con clave 'row'
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 3.3 Implementar método getTipoCompra()
  - Recibir id por POST
  - Llamar a `getTipoCompraById()` del modelo
  - Retornar status 200 si se encuentra, 404 si no existe
  - _Requirements: 3.1, 3.2_

- [ ] 3.4 Implementar método addTipoCompra()
  - Validar que el nombre no esté vacío
  - Llamar a `existsTipoCompraByName()` para validar duplicados
  - Si existe, retornar status 409 con mensaje de error
  - Si no existe, llamar a `createTipoCompra()` con datos sanitizados
  - Agregar fecha de creación automática
  - Retornar status 200 en éxito, 500 en error
  - _Requirements: 2.4, 2.5, 2.6, 2.7, 2.8, 2.9_

- [ ] 3.5 Implementar método editTipoCompra()
  - Recibir id y nombre por POST
  - Validar que el nombre no esté vacío
  - Validar que no exista otro tipo con el mismo nombre
  - Llamar a `updateTipoCompra()` con datos sanitizados
  - Retornar status 200 en éxito, 500 en error
  - _Requirements: 3.3, 3.4, 3.5, 3.6, 3.7, 3.8_

- [ ] 3.6 Implementar método statusTipoCompra()
  - Recibir id y nuevo estado (activo) por POST
  - Llamar a `updateTipoCompra()` para cambiar estado
  - Retornar status 200 en éxito, 500 en error
  - _Requirements: 4.5, 4.6, 4.7, 4.8, 5.5, 5.6, 5.7, 5.8_

- [ ] 3.7 Crear funciones auxiliares
  - Implementar función `renderStatus($active)` para generar badges HTML
  - Usar clases de TailwindCSS para estilos (bg-green-500 para activo, bg-red-500 para inactivo)
  - _Requirements: 1.5, 4.7, 5.7_

- [ ] 3.8 Configurar instanciación y llamada dinámica
  - Crear instancia `$obj = new ctrl()`
  - Implementar llamada dinámica: `$obj->{$_POST['opc']}()`
  - Retornar respuesta con `json_encode()`
  - _Requirements: 1.1_

- [ ] 4. Implementar interfaz frontend (tipo-compras.js)
- [ ] 4.1 Crear archivo tipo-compras.js con clase PurchaseType
  - Extender clase Templates de CoffeeSoft
  - Definir constructor con link y div_modulo
  - Configurar PROJECT_NAME = "tipoCompras"
  - _Requirements: 6.1, 6.2_

- [ ] 4.2 Implementar método render()
  - Llamar a layout() para crear estructura base
  - Llamar a filterBar() para crear barra de filtros
  - Llamar a lsTipoCompras() para cargar tabla inicial
  - _Requirements: 1.1, 6.5_

- [ ] 4.3 Implementar método layout()
  - Usar `primaryLayout()` de CoffeeSoft
  - Configurar parent: 'container-tipo-compras'
  - Definir filterBar y container con IDs únicos
  - Aplicar clases de TailwindCSS para estilos
  - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6_

- [ ] 4.4 Implementar método filterBar()
  - Usar `createfilterBar()` de CoffeeSoft
  - Agregar botón "Agregar nuevo tipo de compra"
  - Configurar onClick para llamar a addTipoCompra()
  - Aplicar estilos corporativos
  - _Requirements: 2.1, 6.1, 6.2_

- [ ] 4.5 Implementar método lsTipoCompras()
  - Usar `createTable()` de CoffeeSoft
  - Configurar data: { opc: 'ls' }
  - Habilitar DataTables con paginación de 15 registros
  - Aplicar tema 'corporativo'
  - Configurar columnas centradas y alineadas a la derecha
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 6.3, 6.6_

- [ ] 4.6 Implementar método addTipoCompra()
  - Usar `createModalForm()` de CoffeeSoft
  - Configurar título "NUEVO TIPO DE COMPRA"
  - Agregar campo input con label "Nombre del tipo de compra"
  - Configurar placeholder "Ej: Corporativo, Fondo fijo, Crédito"
  - Agregar botón "Guardar"
  - Configurar data: { opc: 'addTipoCompra' }
  - Implementar callback success para mostrar alertas y actualizar tabla
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.8, 2.9_

- [ ] 4.7 Implementar método editTipoCompra(id)
  - Hacer petición async con useFetch para obtener datos: { opc: 'getTipoCompra', id }
  - Usar `createModalForm()` de CoffeeSoft
  - Configurar título "EDITAR TIPO DE COMPRA"
  - Prellenar campo con autofill usando datos obtenidos
  - Configurar data: { opc: 'editTipoCompra', id }
  - Implementar callback success para mostrar alertas y actualizar tabla
  - _Requirements: 3.1, 3.2, 3.3, 3.7, 3.8_

- [ ] 4.8 Implementar método statusTipoCompra(id, active)
  - Determinar nuevo estado (toggle: 1 → 0, 0 → 1)
  - Determinar título del modal según acción (ACTIVAR/DESACTIVAR)
  - Usar `swalQuestion()` de CoffeeSoft
  - Configurar mensaje apropiado según acción
  - Incluir ícono de advertencia
  - Agregar botones "Continuar" y "Cancelar"
  - Configurar data: { opc: 'statusTipoCompra', id, activo: nuevoEstado }
  - Implementar callback send para mostrar alertas y actualizar tabla
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8_

- [ ] 5. Integrar módulo con sistema de Administración
- [ ] 5.1 Modificar admin.js para incluir PurchaseType
  - Agregar variable global `let purchaseType`
  - Agregar API endpoint `let api_tipoCompras = 'ctrl/ctrl-tipo-compras.php'`
  - Instanciar clase: `purchaseType = new PurchaseType(api_tipoCompras, "root")`
  - Llamar a `purchaseType.render()` en el método render() de App
  - _Requirements: 6.4_

- [ ] 5.2 Agregar tab "Tipos de compra" en layoutTabs()
  - Agregar objeto de configuración en array json de tabLayout
  - Configurar id: "tipo-compras"
  - Configurar tab: "Tipos de compra"
  - Configurar onClick: () => purchaseType.lsTipoCompras()
  - _Requirements: 6.4, 6.5_

- [ ] 5.3 Crear contenedor para el tab
  - Agregar HTML con estructura: header + filterbar + table
  - Configurar IDs: container-tipo-compras, filterbar-tipo-compras, table-tipo-compras
  - Agregar título "📦 Tipos de Compra" y subtítulo descriptivo
  - _Requirements: 6.5_

- [ ] 6. Validaciones y manejo de errores
- [ ] 6.1 Implementar validaciones frontend
  - Agregar atributo required en campos de formulario
  - Validar respuestas del servidor en callbacks success
  - Mostrar alertas de error con SweetAlert2 cuando status !== 200
  - _Requirements: 2.4, 2.6, 3.4, 3.5_

- [ ] 6.2 Implementar validaciones backend
  - Validar campos vacíos en controlador
  - Validar duplicados antes de insertar/actualizar
  - Retornar códigos de estado HTTP apropiados (200, 400, 409, 500)
  - Retornar mensajes descriptivos en español
  - _Requirements: 2.5, 2.6, 3.5_

- [ ] 7. Aplicar estilos y tema corporativo
- [ ] 7.1 Configurar tema de tabla
  - Usar theme: 'corporativo' en createTable
  - Configurar columnas centradas: center: [0, 1]
  - Configurar columnas a la derecha: right: [2]
  - _Requirements: 6.1, 6.2, 6.3_

- [ ] 7.2 Aplicar estilos a modales
  - Usar clases de TailwindCSS en formularios
  - Configurar espaciado con mb-3 en campos
  - Aplicar estilos a botones (bg-blue-600, hover:bg-blue-700)
  - _Requirements: 6.1, 6.2, 6.6_

- [ ] 7.3 Aplicar estilos a badges de estado
  - Usar bg-green-500 para estado activo
  - Usar bg-red-500 para estado inactivo
  - Agregar clases: px-2 py-1 rounded-md text-sm font-semibold
  - _Requirements: 1.5, 4.7, 5.7_

- [ ] 8. Documentación y pruebas finales
- [ ] 8.1 Verificar funcionamiento completo
  - Probar creación de tipos de compra
  - Probar edición de tipos existentes
  - Probar activación/desactivación de tipos
  - Verificar validación de duplicados
  - Verificar actualización automática de tabla
  - _Requirements: 1.1, 2.9, 3.8, 4.8, 5.8_

- [ ]* 8.2 Realizar pruebas de integración
  - Verificar que el tab se muestra correctamente en el módulo de Administración
  - Verificar que no hay conflictos con otros submódulos
  - Verificar responsive en diferentes dispositivos
  - _Requirements: 6.4, 6.5, 6.6_

- [ ]* 8.3 Optimización y limpieza de código
  - Revisar y eliminar código comentado
  - Verificar nomenclatura consistente
  - Verificar que no hay console.log() en producción
  - _Requirements: 6.1, 6.2_
