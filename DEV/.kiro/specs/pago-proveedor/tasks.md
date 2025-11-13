# Implementation Plan - Módulo de Pagos a Proveedor

## Task List

- [x] 1. Configurar estructura base del proyecto



  - Crear estructura de carpetas en `contabilidad/captura/`
  - Crear archivo `pago-proveedor.js` vacío
  - Crear archivo `ctrl/ctrl-pago-proveedor.php` vacío
  - Crear archivo `mdl/mdl-pago-proveedor.php` vacío




  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6_

- [ ] 2. Implementar modelo de datos (mdl-pago-proveedor.php)
  - [x] 2.1 Crear clase mdl que extienda CRUD


    - Configurar propiedades `$util` y `$bd`
    - Requerir archivos `_CRUD.php` y `_Utileria.php`
    - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1_
  
  - [x] 2.2 Implementar métodos de consulta

    - Crear método `listPayments($array)` con JOIN a supplier
    - Crear método `getPaymentById($array)` para obtener pago específico
    - Crear método `lsSuppliers($array)` para filtro de proveedores




    - Crear método `calculateTotals($array)` para sumar por tipo de pago
    - _Requirements: 1.3, 1.4, 5.1, 5.2, 5.3, 5.4_
  
  - [x] 2.3 Implementar métodos de escritura

    - Crear método `createPayment($array)` usando `_Insert`
    - Crear método `updatePayment($array)` usando `_Update`
    - Crear método `deletePaymentById($array)` con soft delete (active = 0)
    - _Requirements: 2.7, 3.9, 4.4, 4.5_


- [ ] 3. Implementar controlador (ctrl-pago-proveedor.php)
  - [ ] 3.1 Crear clase ctrl que extienda mdl
    - Configurar estructura base del controlador
    - Implementar manejo de `$_POST['opc']`
    - _Requirements: 1.1, 2.1, 3.1, 4.1_
  
  - [ ] 3.2 Implementar método init()
    - Cargar lista de proveedores activos con `lsSuppliers([1])`

    - Definir array de tipos de pago (Fondo fijo, Corporativo)
    - Retornar estructura con `suppliers` y `paymentTypes`
    - _Requirements: 2.2, 2.3_
  
  - [ ] 3.3 Implementar método ls()
    - Llamar `listPayments([$_POST['udn']])`

    - Iterar resultados y construir array `$__row[]`
    - Formatear montos con `evaluar()`
    - Agregar botones de acción (editar, eliminar) en cada fila
    - Llamar `calculateTotals([$_POST['udn']])` para obtener totales
    - Retornar `['row' => $__row, 'totals' => $totals]`
    - _Requirements: 1.3, 1.4, 1.5, 5.1, 5.2, 5.3, 5.4_
  
  - [ ] 3.4 Implementar método getPayment()
    - Recibir `$_POST['id']`

    - Llamar `getPaymentById([$_POST['id']])`
    - Validar que el pago existe
    - Retornar `['status' => 200/404, 'message' => '...', 'data' => $payment]`
    - _Requirements: 3.2, 3.3_
  
  - [ ] 3.5 Implementar método addPayment()
    - Recibir datos del formulario por POST

    - Agregar `operation_date = date('Y-m-d')`
    - Agregar `active = 1`
    - Validar campos obligatorios (supplier_id, payment_type, amount)
    - Validar que amount sea numérico y > 0
    - Llamar `createPayment($this->util->sql($_POST))`

    - Retornar `['status' => 200/400/500, 'message' => '...']`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_
  




  - [ ] 3.6 Implementar método editPayment()
    - Recibir datos del formulario por POST con id
    - Validar campos obligatorios
    - Validar que amount sea numérico y > 0
    - Llamar `updatePayment($this->util->sql($_POST, 1))`
    - Retornar `['status' => 200/400/500, 'message' => '...']`

    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_
  
  - [ ] 3.7 Implementar método deletePayment()
    - Recibir `$_POST['id']`
    - Llamar `deletePaymentById([$_POST['id']])`

    - Retornar `['status' => 200/500, 'message' => '...']`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_
  
  - [ ] 3.8 Crear funciones auxiliares
    - Crear función `renderStatus($active)` para badges de estado

    - Crear función `dropdown($id)` para menú de acciones
    - Agregar instanciación final: `$obj = new ctrl(); echo json_encode($obj->{$_POST['opc']}());`
    - _Requirements: 1.4, 1.5_

- [ ] 4. Implementar frontend (pago-proveedor.js)
  - [ ] 4.1 Crear estructura base de la clase App
    - Declarar variables globales: `api`, `app`, `suppliers`, `paymentTypes`
    - Crear clase `App extends Templates`

    - Configurar constructor con `PROJECT_NAME = "pagoProveedor"`
    - Implementar método `render()` que llame a `layout()`, `filterBar()`, `ls()`
    - _Requirements: 1.1_
  
  - [ ] 4.2 Implementar método layout()
    - Usar `primaryLayout()` de CoffeeSoft
    - Crear estructura con `filterBar` y `container`

    - Agregar encabezado con fecha de captura y nombre de usuario
    - _Requirements: 1.1, 1.2_
  
  - [ ] 4.3 Implementar método filterBar()
    - Usar `createfilterBar()` de CoffeeSoft
    - Agregar botón "Registrar nuevo pago a proveedor" que llame a `addPayment()`
    - Agregar botón "Subir archivos de proveedores"
    - _Requirements: 1.6_

  
  - [ ] 4.4 Implementar método ls()
    - Usar `createTable()` de CoffeeSoft con `coffeesoft: true`
    - Configurar `data: { opc: 'ls', udn: $_POST['udn'] }`
    - Configurar columnas: Proveedor, Tipo de Pago, Monto, Descripción, Acciones
    - Configurar `center` y `right` para alineación de columnas
    - Configurar tema `corporativo`
    - Llamar `showTotals()` después de cargar tabla
    - _Requirements: 1.3, 1.4, 1.5_

  
  - [ ] 4.5 Implementar método showTotals(data)
    - Crear 3 cards usando componente personalizado o HTML
    - Card 1: "Total de pagos a proveedores" con `data.total_general`
    - Card 2: "Total pagos de fondo fijo" con `data.total_fondo_fijo`
    - Card 3: "Total pagos de corporativo" con `data.total_corporativo`
    - Usar `formatPrice()` para formato de moneda
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.8_
  

  - [ ] 4.6 Implementar método jsonPayment()
    - Crear estructura JSON del formulario
    - Campo: `supplier_id` (select, required) con data de proveedores
    - Campo: `payment_type` (select, required) con opciones Fondo fijo/Corporativo
    - Campo: `amount` (input tipo cifra, required) con símbolo $
    - Campo: `description` (textarea, optional)
    - Botón submit: "Guardar pago a proveedor"
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.6_

  
  - [ ] 4.7 Implementar método addPayment()
    - Usar `createModalForm()` de CoffeeSoft
    - Configurar `bootbox: { title: 'Registrar nuevo pago a proveedor' }`
    - Configurar `data: { opc: 'addPayment', udn: $_POST['udn'] }`
    - Usar `json: this.jsonPayment()`
    - Implementar callback `success` para manejar respuesta
    - Mostrar mensaje de éxito/error con `alert()`
    - Recargar tabla con `this.ls()` si es exitoso
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_
  
  - [ ] 4.8 Implementar método editPayment(id)
    - Hacer petición async con `useFetch()` a `getPayment`
    - Usar `createModalForm()` con `autofill: payment`
    - Configurar `bootbox: { title: 'Editar pago a proveedor' }`
    - Configurar `data: { opc: 'editPayment', id: id, udn: $_POST['udn'] }`
    - Usar `json: this.jsonPayment()`
    - Implementar callback `success` para manejar respuesta
    - Recargar tabla con `this.ls()` si es exitoso
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_
  
  - [ ] 4.9 Implementar método deletePayment(id)
    - Usar `swalQuestion()` de CoffeeSoft
    - Configurar `opts: { title: '¿Está seguro?', text: '¿Desea eliminar el pago a proveedor?', icon: 'warning' }`
    - Configurar `data: { opc: 'deletePayment', id: id, udn: $_POST['udn'] }`
    - Implementar callback `methods.send` para manejar respuesta
    - Mostrar mensaje de éxito/error con `alert()`
    - Recargar tabla con `this.ls()` si es exitoso
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_
  
  - [ ] 4.10 Implementar inicialización del módulo
    - Crear función async autoejecutable `$(async () => {})`
    - Hacer petición a `init()` para cargar filtros
    - Asignar `suppliers` y `paymentTypes` a variables globales
    - Instanciar `app = new App(api, "root")`
    - Llamar `app.render()`
    - _Requirements: 1.1, 2.2, 2.3_

- [ ] 5. Crear script de base de datos
  - Crear archivo SQL con tabla `supplier_payment`
  - Definir campos: id, supplier_id, payment_type, amount, description, operation_date, udn_id, active, created_at, updated_at
  - Agregar foreign key a tabla `supplier`
  - Crear índices para optimización (supplier_id, payment_type, operation_date, active, udn_id)
  - _Requirements: 1.3, 1.4, 2.1, 3.1, 4.1, 5.1_

- [ ] 6. Integración y pruebas
  - [ ] 6.1 Verificar carga inicial del módulo
    - Verificar que se carga la interfaz correctamente
    - Verificar que se muestran los totales en cero si no hay datos
    - Verificar que se carga la lista de proveedores en el filtro
    - _Requirements: 1.1, 1.2, 1.3_
  
  - [ ] 6.2 Probar flujo de agregar pago
    - Abrir modal de nuevo pago
    - Llenar formulario con datos válidos
    - Guardar y verificar mensaje de éxito
    - Verificar que aparece en la tabla
    - Verificar que los totales se actualizan correctamente
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 5.5, 5.8_
  
  - [ ] 6.3 Probar validaciones de formulario
    - Intentar guardar sin seleccionar proveedor
    - Intentar guardar sin seleccionar tipo de pago
    - Intentar guardar sin ingresar monto
    - Verificar mensajes de error
    - _Requirements: 2.6_
  
  - [ ] 6.4 Probar flujo de editar pago
    - Click en botón editar de un registro
    - Verificar que modal se abre con datos precargados
    - Modificar campos
    - Guardar y verificar mensaje de éxito
    - Verificar que cambios se reflejan en la tabla
    - Verificar que totales se recalculan
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 5.6, 5.8_
  
  - [ ] 6.5 Probar flujo de eliminar pago
    - Click en botón eliminar de un registro
    - Verificar modal de confirmación
    - Confirmar eliminación
    - Verificar mensaje de éxito
    - Verificar que registro desaparece de la tabla
    - Verificar que totales se recalculan
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 5.7, 5.8_
  
  - [ ] 6.6 Probar cálculo de totales
    - Agregar pago tipo "Fondo fijo" y verificar total
    - Agregar pago tipo "Corporativo" y verificar total
    - Verificar total general
    - Editar tipo de pago y verificar recálculo
    - Eliminar pago y verificar recálculo
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8_

- [ ] 7. Documentación y deployment
  - Actualizar menú de navegación para incluir módulo
  - Configurar permisos de usuario para el módulo
  - Verificar que variable `$_POST['udn']` se pasa correctamente en todas las peticiones
  - Crear documentación de usuario (opcional)
  - _Requirements: 1.1_
