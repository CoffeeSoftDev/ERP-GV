# Implementation Plan - Módulo de Cuentas de Mayor

- [x] 1. Configurar estructura base del proyecto



  - Crear archivo `index.php` con contenedor root y scripts de CoffeeSoft
  - Crear estructura de carpetas (ctrl/, mdl/, js/)
  - Configurar inclusión de coffeSoft.js y plugins.js
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_



- [ ] 2. Implementar modelo de datos (mdl-cuentamayor.php)
- [ ] 2.1 Crear clase base del modelo
  - Extender clase CRUD

  - Configurar propiedades $bd y $util
  - Incluir archivos de configuración (_CRUD.php, _Utileria.php)
  - _Requirements: 1.1, 2.1, 3.1, 4.1_

- [ ] 2.2 Implementar métodos para Cuenta de Mayor (product_class)
  - Crear método `listProductClass()` con JOIN a unidades de negocio
  - Crear método `getProductClassById()` para obtener cuenta específica

  - Crear método `existsProductClassByName()` para validar duplicados
  - Crear método `createProductClass()` usando _Insert
  - Crear método `updateProductClass()` usando _Update
  - _Requirements: 1.3, 2.1, 2.2, 2.3, 3.1, 3.4, 4.5, 4.6_

- [ ] 2.3 Implementar métodos para Subcuenta de Mayor (product)
  - Crear método `listProduct()` con JOIN a product_class

  - Crear método `getProductById()`
  - Crear método `existsProductByName()`
  - Crear método `createProduct()`
  - Crear método `updateProduct()`
  - _Requirements: 1.1_

- [x] 2.4 Implementar métodos para Tipos de Compra

  - Crear método `listTipoCompra()` con filtro por UDN
  - Crear método `getTipoCompraById()`
  - Crear método `existsTipoCompraByName()`
  - Crear método `createTipoCompra()`
  - Crear método `updateTipoCompra()`
  - _Requirements: 1.1_


- [ ] 2.5 Implementar métodos para Formas de Pago
  - Crear método `listFormaPago()` con filtro por UDN
  - Crear método `getFormaPagoById()`
  - Crear método `existsFormaPagoByName()`


  - Crear método `createFormaPago()`
  - Crear método `updateFormaPago()`
  - _Requirements: 1.1_


- [ ] 2.6 Implementar métodos auxiliares
  - Crear método `lsUDN()` para obtener lista de unidades de negocio
  - _Requirements: 1.2_


- [ ] 3. Implementar controlador (ctrl-cuentamayor.php)
- [ ] 3.1 Crear clase base del controlador
  - Extender clase mdl
  - Configurar session_start() y validación de $_POST['opc']
  - Incluir modelo (require_once '../mdl/mdl-cuentamayor.php')
  - _Requirements: 1.1, 2.1, 3.1, 4.1_


- [ ] 3.2 Implementar método init()
  - Retornar lista de unidades de negocio para filtros
  - _Requirements: 1.2_

- [ ] 3.3 Implementar métodos para Cuenta de Mayor
  - Crear método `lsCuentaMayor()` que formatee datos para tabla

  - Crear método `getCuentaMayor()` para obtener cuenta por ID
  - Crear método `addCuentaMayor()` con validación de duplicados
  - Crear método `editCuentaMayor()` para actualizar cuenta
  - Crear método `statusCuentaMayor()` para cambiar estado activo/inactivo
  - _Requirements: 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [x] 3.4 Implementar métodos para Subcuenta de Mayor

  - Crear método `lsSubcuenta()` que formatee datos para tabla
  - Crear método `getSubcuenta()` para obtener subcuenta por ID
  - Crear método `addSubcuenta()` con validación de duplicados
  - Crear método `editSubcuenta()` para actualizar subcuenta
  - Crear método `statusSubcuenta()` para cambiar estado
  - _Requirements: 1.1_


- [ ] 3.5 Implementar métodos para Tipos de Compra
  - Crear método `lsTipoCompra()` que formatee datos para tabla
  - Crear método `getTipoCompra()` para obtener tipo por ID
  - Crear método `addTipoCompra()` con validación de duplicados
  - Crear método `editTipoCompra()` para actualizar tipo

  - Crear método `statusTipoCompra()` para cambiar estado
  - _Requirements: 1.1_

- [ ] 3.6 Implementar métodos para Formas de Pago
  - Crear método `lsFormaPago()` que formatee datos para tabla

  - Crear método `getFormaPago()` para obtener forma por ID
  - Crear método `addFormaPago()` con validación de duplicados
  - Crear método `editFormaPago()` para actualizar forma
  - Crear método `statusFormaPago()` para cambiar estado
  - _Requirements: 1.1_


- [ ] 3.7 Implementar funciones auxiliares
  - Crear función `renderStatus($active)` para generar badges HTML
  - Crear función `dropdown($id, $active)` para opciones de acciones
  - _Requirements: 1.3, 4.1_

- [ ] 4. Implementar frontend JavaScript (cuentamayor.js)
- [x] 4.1 Crear clase App principal

  - Extender clase Templates de CoffeeSoft
  - Configurar constructor con PROJECT_NAME = "cuentamayor"
  - Implementar método `render()` que inicialice layout, filterBar y tabla
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 4.2 Implementar layout principal
  - Crear método `layout()` usando primaryLayout
  - Implementar tabLayout con 4 pestañas (Cuenta de mayor, Subcuenta, Tipos de compra, Formas de pago)
  - Configurar contenedores para cada pestaña

  - _Requirements: 1.1_

- [ ] 4.3 Implementar gestión de Cuenta de Mayor
  - Crear método `filterBar()` con filtro de unidad de negocio
  - Crear método `lsCuentaMayor()` usando createTable con tema corporativo
  - Crear método `addCuentaMayor()` usando createModalForm
  - Crear método `editCuentaMayor(id)` con useFetch y createModalForm
  - Crear método `statusCuentaMayor(id, active)` usando swalQuestion
  - Crear método `jsonCuentaMayor()` con estructura del formulario

  - _Requirements: 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [ ] 4.4 Crear clase SubAccount
  - Extender clase App
  - Implementar método `lsSubcuenta()` con createTable
  - Implementar método `filterBarSubcuenta()` con filtros específicos
  - Implementar método `addSubcuenta()` con createModalForm
  - Implementar método `editSubcuenta(id)` con useFetch
  - Implementar método `statusSubcuenta(id, active)` con swalQuestion


  - Crear método `jsonSubcuenta()` con estructura del formulario
  - _Requirements: 1.1_

- [ ] 4.5 Crear clase PurchaseType
  - Extender clase App
  - Implementar método `lsTipoCompra()` con createTable
  - Implementar método `filterBarTipoCompra()` con filtros específicos
  - Implementar método `addTipoCompra()` con createModalForm
  - Implementar método `editTipoCompra(id)` con useFetch
  - Implementar método `statusTipoCompra(id, active)` con swalQuestion
  - Crear método `jsonTipoCompra()` con estructura del formulario
  - _Requirements: 1.1_

- [ ] 4.6 Crear clase PaymentMethod
  - Extender clase App
  - Implementar método `lsFormaPago()` con createTable
  - Implementar método `filterBarFormaPago()` con filtros específicos
  - Implementar método `addFormaPago()` con createModalForm
  - Implementar método `editFormaPago(id)` con useFetch
  - Implementar método `statusFormaPago(id, active)` con swalQuestion
  - Crear método `jsonFormaPago()` con estructura del formulario
  - _Requirements: 1.1_

- [ ] 4.7 Implementar inicialización del módulo
  - Configurar variable global `api` con ruta del controlador
  - Inicializar instancias de todas las clases en $(async () => {})
  - Llamar a método render() de la clase App
  - _Requirements: 1.1_

- [ ] 5. Implementar validaciones y manejo de errores
- [ ] 5.1 Validaciones frontend
  - Validar campos requeridos en formularios
  - Validar que nombre no esté vacío
  - Prevenir envío de formularios inválidos
  - _Requirements: 2.2, 3.3_

- [ ] 5.2 Validaciones backend
  - Validar duplicados por UDN en addCuentaMayor()
  - Validar tipos de datos en controlador
  - Implementar manejo de errores SQL
  - _Requirements: 2.2, 2.3_

- [ ] 5.3 Mensajes de usuario
  - Implementar mensajes de éxito con alert() de CoffeeSoft
  - Implementar mensajes de error descriptivos
  - Configurar mensajes de confirmación en swalQuestion
  - _Requirements: 2.4, 2.5, 3.4, 4.2, 4.3, 4.4, 4.5_

- [ ] 6. Integrar y probar el módulo completo
- [ ] 6.1 Pruebas de integración
  - Probar flujo completo de crear cuenta de mayor
  - Probar flujo completo de editar cuenta
  - Probar flujo completo de cambiar estado
  - Verificar que tabla se actualice correctamente
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [ ] 6.2 Pruebas de navegación
  - Verificar funcionamiento de todas las pestañas
  - Probar filtro por unidad de negocio
  - Verificar que modales abran y cierren correctamente
  - _Requirements: 1.1, 1.2, 1.5_

- [ ] 6.3 Pruebas de validación
  - Verificar validación de duplicados
  - Probar validación de campos requeridos
  - Verificar mensajes de error apropiados
  - _Requirements: 2.2, 2.3_

- [ ] 6.4 Pruebas de estado
  - Verificar que desactivar muestre mensaje correcto
  - Verificar que activar muestre mensaje correcto
  - Confirmar que registros históricos no se afecten
  - _Requirements: 4.2, 4.3, 4.6, 4.7_
