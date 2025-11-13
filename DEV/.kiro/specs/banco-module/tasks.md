# Implementation Plan - Módulo de Banco

## Task List

- [x] 1. Crear estructura de base de datos




- [ ] 1.1 Crear tabla `banks` con campos id, name, active, created_at, updated_at
  - Implementar índice único en campo name
  - Configurar motor InnoDB con charset utf8mb4

  - _Requirements: 2.1, 2.2, 2.3_

- [ ] 1.2 Crear tabla `bank_accounts` con relaciones a UDN, banks y payment_methods
  - Implementar claves foráneas a banks(id), udn(idUDN), payment_methods(id)



  - Crear índices en udn_id, bank_id y active
  - Configurar campo last_four_digits como CHAR(4)
  - _Requirements: 3.1, 3.2, 3.3, 3.4_


- [ ] 2. Desarrollar modelo de datos (mdl-banco.php)
- [ ] 2.1 Crear clase mdl extendiendo CRUD con propiedades $bd y $util
  - Configurar conexión a base de datos rfwsmqex_contabilidad
  - Inicializar clase Utileria para sanitización
  - _Requirements: 1.1, 2.1, 3.1_


- [ ] 2.2 Implementar métodos de consulta para bancos
  - Crear método listBanks() con filtro por estado activo
  - Crear método getBankById() para obtener banco específico
  - Crear método existsBankByName() para validar duplicidad

  - _Requirements: 2.2, 2.3_

- [ ] 2.3 Implementar métodos de consulta para cuentas bancarias
  - Crear método listBankAccounts() con filtros por UDN y forma de pago

  - Incluir JOIN con tablas udn, banks y payment_methods
  - Crear método getBankAccountById() para obtener cuenta específica
  - _Requirements: 1.1, 3.1, 4.1_

- [x] 2.4 Implementar métodos CRUD para bancos

  - Crear método createBank() usando _Insert
  - Validar datos con $this->util->sql()
  - _Requirements: 2.4_




- [ ] 2.5 Implementar métodos CRUD para cuentas bancarias
  - Crear método createBankAccount() usando _Insert
  - Crear método updateBankAccount() usando _Update
  - Validar referencias foráneas antes de insertar
  - _Requirements: 3.5, 4.4_


- [ ] 2.6 Implementar métodos auxiliares para filtros
  - Crear método lsUDN() para listar unidades de negocio activas
  - Crear método lsPaymentMethods() para listar formas de pago

  - Excluir UDN con id 7, 8, 10 según lógica del sistema
  - _Requirements: 1.2_

- [ ] 3. Desarrollar controlador (ctrl-banco.php)
- [ ] 3.1 Crear clase ctrl extendiendo mdl con validación de sesión
  - Implementar validación de $_POST['opc']
  - Configurar headers CORS si es necesario

  - Instanciar objeto y ejecutar método dinámico
  - _Requirements: 1.1, 2.1, 3.1_

- [ ] 3.2 Implementar método init() para inicializar filtros
  - Retornar array con lsUDN(), lsPaymentMethods() y listBanks()
  - Formato: ['udn' => [...], 'payment_methods' => [...], 'banks' => [...]]
  - _Requirements: 1.2_


- [ ] 3.3 Implementar método lsBankAccounts() para listar cuentas
  - Recibir filtros desde $_POST (udn_id, payment_method_id, active)
  - Llamar a listBankAccounts() del modelo
  - Construir array $__row con columnas: Banco, Nombre cuenta, Últimos 4 dígitos, Estado
  - Agregar columna 'a' con botones de editar y toggle status
  - Retornar ['row' => $__row, 'ls' => $ls]
  - _Requirements: 1.1, 1.4_


- [ ] 3.4 Implementar método addBank() para crear banco
  - Validar campo name no vacío
  - Verificar duplicidad con existsBankByName()
  - Si existe, retornar status 409 con mensaje de error
  - Si no existe, llamar a createBank() del modelo

  - Retornar status 200 con mensaje de éxito
  - _Requirements: 2.2, 2.3, 2.4_

- [ ] 3.5 Implementar método addBankAccount() para crear cuenta
  - Validar campos obligatorios: bank_id, last_four_digits

  - Validar formato de last_four_digits con regex /^\d{4}$/
  - Si formato inválido, retornar status 400
  - Asignar udn_id desde $_POST['udn']
  - Llamar a createBankAccount() del modelo
  - Retornar status 200 con mensaje de éxito
  - _Requirements: 3.2, 3.3, 3.4, 3.5_


- [ ] 3.6 Implementar método getBankAccount() para obtener cuenta
  - Recibir id desde $_POST['id']
  - Llamar a getBankAccountById() del modelo



  - Si existe, retornar status 200 con data
  - Si no existe, retornar status 404
  - _Requirements: 4.1_

- [x] 3.7 Implementar método editBankAccount() para actualizar cuenta

  - Validar formato de last_four_digits si fue modificado
  - Llamar a updateBankAccount() del modelo con $this->util->sql($_POST, 1)
  - Retornar status 200 con mensaje de éxito
  - _Requirements: 4.2, 4.3, 4.4, 4.5_


- [ ] 3.8 Implementar método toggleStatusAccount() para cambiar estado
  - Recibir id y active desde $_POST
  - Invertir valor de active (1 → 0, 0 → 1)
  - Llamar a updateBankAccount() del modelo
  - Retornar status 200 con mensaje según acción (activar/desactivar)

  - _Requirements: 5.3, 5.4, 5.6_

- [ ] 3.9 Crear funciones auxiliares después de la clase
  - Crear función renderStatus($status) para generar badges HTML
  - Usar clases TailwindCSS para estados activo/inactivo
  - Crear función formatAccountNumber($last4) si es necesario
  - _Requirements: 1.1, 5.5_


- [ ] 4. Desarrollar frontend JavaScript (banco.js)
- [ ] 4.1 Crear clase AdminBankAccounts extendiendo Templates
  - Definir constructor con parámetros link y div_modulo
  - Establecer PROJECT_NAME = "banco"
  - Inicializar variables globales para filtros
  - _Requirements: 1.1_

- [x] 4.2 Implementar método render() para inicializar módulo

  - Llamar a layout() para crear estructura visual
  - Llamar a filterBar() para crear barra de filtros
  - Llamar a lsBankAccounts() para cargar tabla inicial
  - _Requirements: 1.1_

- [ ] 4.3 Implementar método layout() usando primaryLayout
  - Crear estructura con filterBar y container

  - Configurar IDs: filterBarBanco, containerBanco
  - Aplicar clases TailwindCSS para diseño responsive
  - _Requirements: 1.1_

- [ ] 4.4 Implementar método filterBar() con createfilterBar
  - Crear select para UDN con datos de init()
  - Crear select para forma de pago con datos de init()

  - Crear select para estado (activo/inactivo)
  - Agregar botón "Agregar nuevo banco" con onClick: addBank()
  - Agregar botón "Agregar nueva cuenta de banco" con onClick: addBankAccount()
  - Configurar onchange de selects para llamar a lsBankAccounts()
  - _Requirements: 1.2, 1.3_

- [ ] 4.5 Implementar método lsBankAccounts() usando createTable
  - Configurar data: { opc: 'lsBankAccounts', filtros desde filterBar }

  - Configurar attr.theme: 'corporativo'
  - Configurar attr.title: '🏦 Cuentas Bancarias'
  - Configurar attr.subtitle: 'Administración de cuentas por banco y UDN'
  - Habilitar datatable con paginación de 15 registros
  - Configurar columnas center y right según diseño
  - _Requirements: 1.1, 1.4_


- [ ] 4.6 Implementar método addBank() con createModalForm
  - Configurar bootbox.title: 'Nuevo Banco'
  - Crear json con campo input para name (obligatorio)
  - Configurar data: { opc: 'addBank' }
  - Implementar callback success para mostrar alert y recargar lista




  - Manejar errores con status 409 (duplicado) y 500 (error servidor)
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ] 4.7 Implementar método addBankAccount() con createModalForm
  - Configurar bootbox.title: 'Nueva Cuenta Bancaria'

  - Crear json con campos: select bank_id, input account_alias, input last_four_digits
  - Agregar validación onkeyup para last_four_digits (solo 4 dígitos)
  - Configurar data: { opc: 'addBankAccount', udn: desde filtro }
  - Implementar callback success para mostrar alert y recargar tabla
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_


- [ ] 4.8 Implementar método editBankAccount(id) con async/await
  - Hacer petición useFetch con opc: 'getBankAccount', id
  - Crear createModalForm con autofill de datos obtenidos
  - Configurar bootbox.title: 'Editar Cuenta Bancaria'

  - Reutilizar json de addBankAccount() con datos precargados
  - Configurar data: { opc: 'editBankAccount', id }
  - Implementar callback success para mostrar alert y recargar tabla
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ] 4.9 Implementar método toggleStatusAccount(id, status) con swalQuestion
  - Configurar mensaje según acción (activar/desactivar)
  - Si desactivar: "La cuenta bancaria ya no estará disponible, pero seguirá reflejándose en los registros contables"
  - Si activar: "La cuenta estará disponible para captura de información"
  - Configurar data: { opc: 'toggleStatusAccount', id, active: invertir status }
  - Implementar callback send para mostrar alert y recargar tabla
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 4.10 Implementar métodos json para formularios
  - Crear jsonBankForm() con estructura de campo name
  - Crear jsonAccountForm() con estructura de campos bank_id, account_alias, last_four_digits
  - Configurar clases Bootstrap/TailwindCSS para layout responsive
  - Agregar validaciones required y pattern donde corresponda
  - _Requirements: 2.1, 3.1_

- [ ] 5. Crear vista principal (banco.php)
- [ ] 5.1 Configurar estructura HTML base
  - Incluir validación de sesión con $_COOKIE["IDU"]
  - Cargar layout/head.php y layout/core-libraries.php
  - Incluir navbar desde ../../layout/navbar.php
  - _Requirements: 1.1_

- [ ] 5.2 Incluir dependencias de CoffeeSoft Framework
  - Cargar script CoffeeSoft.js desde CDN o local
  - Cargar script plugins.js desde CDN o local
  - Cargar script complementos.js si es necesario
  - _Requirements: 1.1_

- [ ] 5.3 Crear contenedor principal y breadcrumb
  - Crear div#root para renderizado del módulo
  - Configurar breadcrumb: Contabilidad > Administrador > Banco
  - Aplicar clases main-container para layout
  - _Requirements: 1.1_

- [ ] 5.4 Incluir script banco.js con cache busting
  - Cargar js/banco.js con parámetro ?t=<?php echo time(); ?>
  - Inicializar módulo en document ready
  - _Requirements: 1.1_

- [ ] 6. Integración y pruebas
- [ ] 6.1 Verificar flujo completo de creación de banco
  - Probar addBank() con nombre válido
  - Probar addBank() con nombre duplicado
  - Verificar que aparece en select de cuentas
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ] 6.2 Verificar flujo completo de creación de cuenta
  - Probar addBankAccount() con datos válidos
  - Probar validación de 4 dígitos numéricos
  - Verificar que aparece en tabla principal
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 6.3 Verificar flujo de edición de cuenta
  - Probar editBankAccount() carga datos correctos
  - Probar actualización de campos
  - Verificar cambios en tabla
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ] 6.4 Verificar flujo de cambio de estado
  - Probar toggleStatusAccount() para desactivar
  - Verificar mensaje de confirmación correcto
  - Probar toggleStatusAccount() para activar
  - Verificar que datos históricos se mantienen
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

- [ ] 6.5 Verificar funcionamiento de filtros
  - Probar filtro por UDN
  - Probar filtro por forma de pago
  - Probar filtro por estado
  - Probar combinación de filtros
  - _Requirements: 1.2_

- [ ] 6.6 Verificar responsividad y diseño
  - Probar en diferentes resoluciones
  - Verificar modales se centran correctamente
  - Verificar tabla es responsive
  - Verificar botones son accesibles en móvil
  - _Requirements: 1.1_

- [ ]* 6.7 Ejecutar pruebas de validación
  - Probar validación de campos vacíos
  - Probar validación de formato de 4 dígitos
  - Probar validación de duplicidad de bancos
  - Probar manejo de errores del servidor
  - _Requirements: 2.2, 2.3, 3.3, 3.4_

- [ ]* 6.8 Verificar seguridad y permisos
  - Probar acceso sin sesión activa
  - Verificar sanitización de inputs
  - Verificar prepared statements en consultas
  - Probar inyección SQL en campos de texto
  - _Requirements: 1.1, 2.1, 3.1_
