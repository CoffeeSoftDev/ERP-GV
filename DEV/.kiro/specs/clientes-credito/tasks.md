# Implementation Plan - Módulo de Clientes con Movimientos a Crédito

- [x] 1. Configurar estructura de base de datos


  - Crear tabla `customer` con campos id, udn_id, name, balance, active
  - Crear tabla `detail_credit_customer` con campos id, customer_id, daily_closure_id, movement_type, method_pay, amount, description, created_at, updated_by
  - Agregar índices en customer_id, daily_closure_id, movement_type
  - Agregar foreign keys hacia customer, daily_closure
  - _Requirements: 1.1, 2.1, 6.2, 7.3_





- [ ] 2. Implementar modelo de datos (mdl-clientes.php)
- [x] 2.1 Crear clase mdl extendiendo CRUD


  - Configurar constructor con $this->bd = "rfwsmqex_contabilidad."

  - Inicializar $this->util = new Utileria
  - Cargar archivos _CRUD.php y _Utileria.php
  - _Requirements: 1.1, 2.1_

- [x] 2.2 Implementar métodos de consulta de movimientos

  - Crear listMovements() con filtros por UDN, tipo de movimiento, fechas
  - Crear getMovementById() para obtener detalle completo con joins
  - Implementar joins con customer y daily_closure
  - _Requirements: 1.2, 4.2, 4.3_



- [ ] 2.3 Implementar métodos CRUD de movimientos
  - Crear createMovement() para insertar en detail_credit_customer
  - Crear updateMovement() para editar movimientos existentes
  - Crear deleteMovementById() para eliminar movimientos
  - _Requirements: 2.6, 3.3, 5.4_

- [x] 2.4 Implementar métodos de gestión de clientes

  - Crear listCustomers() con filtro por UDN y estado activo
  - Crear getCustomerById() para obtener datos específicos
  - Crear createCustomer() con balance inicial en 0
  - Crear updateCustomer() para modificar nombre y UDN
  - Crear existsCustomerByName() para validar duplicados


  - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [ ] 2.5 Implementar lógica de actualización de saldos
  - Crear updateCustomerBalance() que suma/resta según tipo de movimiento
  - Validar que pagos no excedan deuda actual


  - Implementar cálculo: balance += amount (consumos) o balance -= amount (pagos)
  - _Requirements: 2.8, 2.9, 5.4_

- [ ] 2.6 Implementar métodos auxiliares para filtros
  - Crear lsUDN() para obtener lista de unidades de negocio
  - Crear lsMovementTypes() retornando ["Consumo a crédito", "Anticipo", "Pago total"]
  - Crear lsPaymentMethods() retornando ["Efectivo", "Banco", "N/A"]


  - Crear getCurrentDailyClosure() para obtener corte activo por UDN
  - _Requirements: 1.1, 2.1, 6.1_

- [x] 3. Implementar controlador (ctrl-clientes.php)

- [ ] 3.1 Crear clase ctrl extendiendo mdl
  - Implementar session_start() y validación de $_POST['opc']
  - Requerir mdl-clientes.php
  - Configurar estructura base del controlador
  - _Requirements: 1.1, 2.1_


- [ ] 3.2 Implementar método init()
  - Cargar lista de UDN con lsUDN()
  - Cargar tipos de movimiento con lsMovementTypes()
  - Cargar formas de pago con lsPaymentMethods()
  - Retornar array asociativo para frontend
  - _Requirements: 1.1, 2.1_

- [x] 3.3 Implementar lsMovements() para listar movimientos

  - Recibir filtros desde $_POST (udn, tipo, fechas)
  - Llamar a listMovements() del modelo
  - Iterar resultados y construir array $__row con formato para tabla
  - Formatear montos con evaluar() y fechas con formatSpanishDate()
  - Agregar dropdown con opciones Ver, Editar, Eliminar
  - Retornar ['row' => $__row, 'ls' => $ls]

  - _Requirements: 1.2, 1.3, 1.4_

- [ ] 3.4 Implementar getMovement() para obtener detalle
  - Recibir id desde $_POST
  - Llamar a getMovementById() del modelo
  - Calcular nueva deuda según tipo de movimiento
  - Retornar status 200 con datos completos o 404 si no existe
  - _Requirements: 4.1, 4.2, 4.6, 4.7, 4.8_


- [ ] 3.5 Implementar addMovement() para crear movimientos
  - Validar que existe corte diario activo con getCurrentDailyClosure()
  - Validar que cliente existe y está activo
  - Si tipo es "Consumo a crédito", forzar method_pay = "N/A"
  - Si tipo es "Pago total", validar amount <= balance actual
  - Agregar created_at y updated_by desde sesión
  - Llamar a createMovement() y updateCustomerBalance()
  - Retornar status 200 con nuevo balance o error apropiado

  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 6.1_

- [ ] 3.6 Implementar editMovement() para actualizar movimientos
  - Obtener movimiento original para calcular diferencia de balance
  - Validar nuevos datos según tipo de movimiento
  - Revertir cambio de balance anterior

  - Aplicar nuevo cambio de balance
  - Llamar a updateMovement() del modelo
  - Retornar status 200 o error
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 3.7 Implementar deleteMovement() para eliminar movimientos
  - Obtener movimiento para conocer el monto

  - Revertir cambio de balance (sumar si era pago, restar si era consumo)
  - Llamar a deleteMovementById() del modelo
  - Retornar status 200 con mensaje de éxito
  - _Requirements: 3.3, 3.5_

- [x] 3.8 Implementar métodos de gestión de clientes


  - Crear lsCustomers() que llama a listCustomers() y formatea tabla
  - Crear getCustomer() que obtiene datos por id
  - Crear addCustomer() que valida duplicados y crea con balance 0

  - Crear editCustomer() que actualiza nombre y UDN (no balance)
  - Crear statusCustomer() que cambia active entre 0 y 1
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

- [x] 3.9 Crear funciones auxiliares (Complements)

  - Implementar dropdown() para generar opciones de acciones
  - Implementar renderStatus() para badges de estado activo/inactivo
  - Implementar renderMovementType() para badges de tipo de movimiento
  - _Requirements: 1.4_


- [ ] 4. Implementar frontend principal (clientes.js)
- [ ] 4.1 Crear clase App extendiendo Templates
  - Configurar constructor con api y PROJECT_NAME = "clientes"
  - Implementar método render() que ejecuta layout(), filterBar(), lsMovements()
  - _Requirements: 1.1_


- [ ] 4.2 Implementar layout() con estructura de pestañas
  - Usar primaryLayout() para crear contenedor principal
  - Usar tabLayout() para crear pestañas: Movimientos, Clientes
  - Configurar theme: 'light' y type: 'short'
  - _Requirements: 1.1_

- [ ] 4.3 Implementar filterBar() para filtros de movimientos
  - Usar createfilterBar() con select para tipo de movimiento
  - Agregar botón "Registrar nuevo movimiento" que llama a addMovement()

  - Agregar select para mostrar "Consumos a crédito" o "Pagos y anticipos"
  - _Requirements: 1.3, 1.5_

- [ ] 4.4 Implementar lsMovements() para mostrar tabla
  - Usar createTable() con coffeesoft: true
  - Configurar data: { opc: 'lsMovements' }
  - Configurar attr con theme: 'corporativo', center: [1], right: [4]

  - Mostrar columnas: Cliente, Tipo de movimiento, Forma de pago, Monto, Acciones
  - _Requirements: 1.2, 1.4_

- [ ] 4.5 Implementar addMovement() para modal de nuevo movimiento
  - Usar createModalForm() con bootbox title: "Nuevo Movimiento de Crédito"
  - Crear jsonMovement() con campos: customer_id (select), movement_type (select), method_pay (select), amount (cifra), description (textarea)
  - Implementar lógica: si movement_type = "Consumo a crédito", deshabilitar y setear method_pay = "N/A"

  - Mostrar deuda actual del cliente seleccionado en tiempo real
  - Calcular y mostrar nueva deuda según tipo y monto
  - Configurar data: { opc: 'addMovement' }
  - En success, mostrar alert de éxito y ejecutar lsMovements()
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.7_


- [ ] 4.6 Implementar editMovement(id) para editar movimientos
  - Usar useFetch() con opc: 'getMovement' para obtener datos
  - Usar createModalForm() con autofill de datos obtenidos
  - Reutilizar jsonMovement() con misma lógica de validación
  - Configurar data: { opc: 'editMovement', id: id }
  - En success, mostrar alert y refrescar tabla
  - _Requirements: 5.1, 5.2, 5.3, 5.5_

- [x] 4.7 Implementar viewDetail(id) para ver detalle completo

  - Usar useFetch() con opc: 'getMovement'
  - Crear modal personalizado con secciones: Información del Cliente, Detalles del Movimiento, Descripción, Resumen Financiero
  - Mostrar cálculo: Deuda actual ± Monto = Nueva deuda
  - Usar colores: verde para consumos (+), rojo para pagos (-)

  - Mostrar timestamp y usuario que actualizó
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8_

- [ ] 4.8 Implementar deleteMovement(id) para eliminar
  - Usar swalQuestion() con mensaje "¿Está seguro de querer eliminar el movimiento a crédito?"
  - Configurar opts con icon: 'warning'

  - Configurar data: { opc: 'deleteMovement', id: id }
  - En methods.send, mostrar alert de éxito y ejecutar lsMovements()
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 4.9 Implementar jsonMovement() para estructura del formulario
  - Crear array con campos del formulario de movimiento

  - Incluir select de clientes con data desde init()
  - Incluir select de tipos de movimiento
  - Incluir select de formas de pago
  - Incluir input tipo cifra para monto con validación
  - Incluir textarea opcional para descripción
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_


- [ ] 5. Implementar gestión de clientes (CustomerManager)
- [ ] 5.1 Crear clase CustomerManager extendiendo App
  - Implementar lsCustomers() para mostrar tabla de clientes
  - Usar createTable() con columnas: Nombre, Saldo Actual, Estado, Acciones
  - _Requirements: 7.1, 7.6_

- [ ] 5.2 Implementar addCustomer() para nuevo cliente
  - Usar createModalForm() con campos: name (input), udn_id (select)
  - Configurar data: { opc: 'addCustomer' }
  - Validar que nombre no esté vacío
  - En success, mostrar alert y refrescar tabla
  - _Requirements: 7.2, 7.3_

- [ ] 5.3 Implementar editCustomer(id) para editar cliente
  - Obtener datos con useFetch() y opc: 'getCustomer'

  - Usar createModalForm() con autofill
  - Permitir editar solo name y udn_id (no balance)
  - Configurar data: { opc: 'editCustomer', id: id }
  - _Requirements: 7.4_

- [x] 5.4 Implementar statusCustomer(id) para activar/desactivar

  - Usar swalQuestion() con confirmación

  - Configurar data: { opc: 'statusCustomer', id: id, active: 0/1 }
  - Validar que cliente no tenga saldo pendiente antes de desactivar
  - En success, refrescar tabla
  - _Requirements: 7.5_


- [ ] 6. Implementar dashboard con totales
- [ ] 6.1 Crear método renderDashboard() en App
  - Usar infoCard() de CoffeeSoft para mostrar 3 tarjetas
  - Tarjeta 1: Total de consumos a crédito
  - Tarjeta 2: Total de pagos/anticipos en efectivo
  - Tarjeta 3: Total de pagos/anticipos en banco
  - Obtener datos con useFetch() y opc: 'getDashboardTotals'
  - _Requirements: 1.1_


- [ ] 6.2 Implementar getDashboardTotals() en controlador
  - Calcular SUM(amount) WHERE movement_type = 'Consumo a crédito'
  - Calcular SUM(amount) WHERE movement_type IN ('Anticipo', 'Pago total') AND method_pay = 'Efectivo'
  - Calcular SUM(amount) WHERE movement_type IN ('Anticipo', 'Pago total') AND method_pay = 'Banco'

  - Retornar array con los 3 totales
  - _Requirements: 1.1_

- [ ] 7. Validaciones y manejo de errores
- [ ] 7.1 Implementar validaciones en frontend
  - Validar que monto sea mayor a 0
  - Validar que cliente esté seleccionado
  - Validar que tipo de movimiento esté seleccionado
  - Para pagos, validar que monto no exceda deuda (mostrar mensaje en tiempo real)
  - _Requirements: 2.4, 2.5_

- [x] 7.2 Implementar validaciones en backend


  - Validar existencia de corte diario activo
  - Validar que cliente existe y está activo
  - Validar que monto es numérico y positivo
  - Validar que tipo de movimiento es válido
  - Para pagos, validar amount <= balance
  - _Requirements: 2.4, 2.5, 6.5_

- [ ] 7.3 Implementar mensajes de error descriptivos
  - Error 404: "Cliente no encontrado"
  - Error 400: "No hay corte diario activo"
  - Error 400: "El monto excede la deuda actual"
  - Error 409: "Ya existe un cliente con ese nombre"
  - Usar alert() de CoffeeSoft con icon: 'error'
  - _Requirements: 2.4, 2.5, 6.5, 7.2_

- [ ] 8. Integración con sistema de corte diario
- [ ] 8.1 Implementar vinculación automática con daily_closure
  - En addMovement(), obtener daily_closure_id activo antes de insertar
  - Validar que existe corte activo, si no, retornar error
  - Almacenar daily_closure_id en detail_credit_customer
  - _Requirements: 6.1, 6.2, 6.5_

- [ ] 8.2 Mostrar información de corte en detalle de movimiento
  - En viewDetail(), incluir datos del corte: fecha, turno
  - Obtener mediante join con daily_closure
  - _Requirements: 6.4_

- [ ] 9. Optimización y refinamiento
- [ ] 9.1 Agregar índices a base de datos
  - Crear índice en detail_credit_customer(customer_id)
  - Crear índice en detail_credit_customer(daily_closure_id)
  - Crear índice en detail_credit_customer(movement_type)
  - Crear índice compuesto en (customer_id, created_at)
  - _Requirements: Performance_

- [ ] 9.2 Implementar paginación en tablas
  - Configurar createTable() con conf: { datatable: true, pag: 15 }
  - Aplicar a lsMovements() y lsCustomers()
  - _Requirements: 1.2, 7.1_

- [ ] 9.3 Agregar filtro por rango de fechas
  - Agregar dataPicker() en filterBar()
  - Enviar fi y ff a lsMovements()
  - Filtrar en modelo con WHERE created_at BETWEEN ? AND ?
  - _Requirements: 1.3_

- [ ]* 10. Testing y validación
- [ ]* 10.1 Crear datos de prueba
  - Insertar 3 clientes de prueba
  - Crear 10 movimientos de diferentes tipos
  - Verificar cálculos de balance
  - _Requirements: All_

- [ ]* 10.2 Probar flujo completo de crédito
  - Crear cliente con balance 0
  - Agregar consumo y verificar balance aumenta
  - Agregar anticipo y verificar balance disminuye
  - Agregar pago total y verificar balance llega a 0
  - _Requirements: 2.8, 2.9_

- [ ]* 10.3 Probar validaciones de error
  - Intentar pago mayor a deuda
  - Intentar movimiento sin corte activo
  - Intentar crear cliente duplicado
  - Verificar mensajes de error apropiados
  - _Requirements: 2.4, 2.5, 6.5, 7.2_

- [ ]* 10.4 Probar eliminación de movimientos
  - Eliminar consumo y verificar balance disminuye
  - Eliminar pago y verificar balance aumenta
  - Verificar confirmación modal funciona
  - _Requirements: 3.3, 3.5_
