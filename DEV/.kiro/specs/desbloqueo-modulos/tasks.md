# Implementation Plan

- [x] 1. Configurar estructura base del proyecto


  - Crear carpeta `contabilidad/administrador/` en el proyecto
  - Crear archivo `index.php` con contenedor root y scripts de CoffeeSoft
  - Crear estructura de carpetas: `ctrl/`, `mdl/`, `js/`
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [x] 2. Crear modelo de base de datos


  - [x] 2.1 Crear tabla `module` con campos y datos iniciales


    - Escribir script SQL para tabla module
    - Insertar 7 módulos predefinidos (Ventas, Compras, Clientes, etc.)
    - _Requirements: 1.3, 2.2_

  - [x] 2.2 Crear tabla `close_time` con configuración mensual


    - Escribir script SQL para tabla close_time
    - Insertar 12 registros con horario por defecto 23:59
    - Agregar constraint UNIQUE en campo month
    - _Requirements: 3.1, 3.2, 3.3_

  - [x] 2.3 Modificar tabla `module_unlock` según diseño


    - Agregar campos faltantes si no existen
    - Crear índices en active, unlock_date, udn_id, module_id
    - Agregar foreign keys a udn y module
    - _Requirements: 2.5, 4.1, 4.2, 5.1_

- [x] 3. Implementar modelo PHP (mdl-admin.php)


  - [x] 3.1 Crear clase base y configuración


    - Extender clase CRUD
    - Configurar propiedades $bd y $util
    - Requerir archivos _CRUD.php y _Utileria.php
    - _Requirements: 1.1_

  - [x] 3.2 Implementar métodos de consulta de módulos desbloqueados

    - Crear método `listModulesUnlocked()` con JOIN a udn y module
    - Crear método `getUnlockRequestById()` para obtener registro específico
    - Crear método `existsActiveUnlock()` para validar duplicados
    - _Requirements: 2.6, 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 3.3 Implementar métodos CRUD de solicitudes

    - Crear método `createUnlockRequest()` para insertar solicitudes
    - Crear método `updateModuleStatus()` para cambiar estado active
    - _Requirements: 2.3, 2.4, 2.5, 2.6, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [x] 3.4 Implementar métodos de horarios de cierre

    - Crear método `listCloseTime()` para obtener horarios mensuales
    - Crear método `updateCloseTimeByMonth()` con lógica INSERT/UPDATE
    - _Requirements: 3.3, 3.4, 3.8, 3.9_

  - [x] 3.5 Implementar métodos para filtros

    - Crear método `lsUDN()` para obtener lista de unidades de negocio
    - Crear método `lsModules()` para obtener lista de módulos activos
    - _Requirements: 1.1, 2.2_

- [x] 4. Implementar controlador PHP (ctrl-admin.php)


  - [x] 4.1 Crear clase controlador y método init

    - Extender clase mdl
    - Implementar método `init()` que retorne listas de UDN y módulos
    - _Requirements: 1.1, 2.2_

  - [x] 4.2 Implementar método lsModulesUnlocked

    - Obtener datos del modelo con `listModulesUnlocked()`
    - Construir array $__row con formato para tabla
    - Agregar columna dropdown con opciones de bloquear
    - Formatear fechas con `formatSpanishDate()`
    - Retornar array con 'row' y 'thead'
    - _Requirements: 1.3, 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 4.3 Implementar método addUnlockRequest

    - Validar que no exista solicitud activa duplicada
    - Agregar campos operation_date y active = 1
    - Llamar a `createUnlockRequest()` del modelo
    - Retornar status 200/409/500 con mensaje
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [x] 4.4 Implementar método getUnlockRequest

    - Recibir id por POST
    - Llamar a `getUnlockRequestById()` del modelo
    - Retornar status 200/500 con datos
    - _Requirements: 2.1_

  - [x] 4.5 Implementar método toggleLockStatus

    - Recibir id y active por POST
    - Actualizar campo active (invertir valor)
    - Actualizar operation_date a NOW()
    - Retornar status 200/500 con mensaje
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [x] 4.6 Implementar métodos de horarios de cierre

    - Crear método `lsCloseTime()` que retorne tabla de horarios
    - Crear método `updateCloseTime()` con validación de mes futuro
    - Registrar usuario que realizó cambio
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

  - [x] 4.7 Crear funciones auxiliares

    - Implementar función `renderLockIcon()` para íconos de candado
    - Implementar función `renderStatus()` para badges de estado
    - _Requirements: 1.5, 5.5_

- [x] 5. Implementar frontend JavaScript (admin.js)


  - [x] 5.1 Crear clase App y estructura base

    - Extender clase Templates de CoffeeSoft
    - Definir PROJECT_NAME = "unlockModules"
    - Implementar constructor con link y div_modulo
    - Crear variables globales para listas (lsudn, lsmodules)
    - _Requirements: 1.1_

  - [x] 5.2 Implementar método render y layout principal

    - Crear método `render()` que llame a layout y carga inicial
    - Implementar `layout()` con primaryLayout de CoffeeSoft
    - Crear contenedores filterBar y container
    - _Requirements: 1.1, 1.2_

  - [x] 5.3 Implementar encabezado del módulo

    - Crear método `layoutHeader()` con botón de regreso
    - Mostrar "Bienvenido [nombre usuario]"
    - Mostrar fecha actual formateada
    - _Requirements: 1.1_

  - [x] 5.4 Implementar navegación por tabs

    - Usar `tabLayout()` de CoffeeSoft con 5 pestañas
    - Configurar pestaña "Desbloqueo de módulos" como activa
    - Agregar onClick para cada pestaña
    - _Requirements: 1.2_

  - [x] 5.5 Implementar tabla de módulos desbloqueados

    - Crear método `lsModulesUnlocked()` con createTable
    - Configurar columnas: UDN, Fecha solicitada, Módulo, Motivo, Bloquear
    - Usar tema 'corporativo' y DataTables con paginación 15
    - Agregar columna con ícono de candado clickeable
    - _Requirements: 1.3, 1.4, 1.5, 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 5.6 Implementar modal de apertura de módulo

    - Crear método `addUnlockRequest()` con createModalForm
    - Definir `jsonUnlockForm()` con campos: fecha, UDN, módulo, motivo
    - Agregar validación de campos obligatorios
    - Validar que fecha no sea futura
    - Implementar callback success que actualice tabla
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [x] 5.7 Implementar cambio de estado de bloqueo

    - Crear método `toggleLockStatus(id, active)` con swalQuestion
    - Enviar petición AJAX para cambiar estado
    - Actualizar ícono de candado sin recargar página
    - Mostrar mensaje de confirmación
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [x] 5.8 Implementar gestión de horarios de cierre

    - Crear método `lsCloseTime()` con createTable para horarios
    - Crear método `updateCloseTime()` con createModalForm
    - Definir `jsonCloseTimeForm()` con selector de mes y hora
    - Validar que solo se editen meses futuros o actual
    - Deshabilitar edición de meses pasados
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

  - [x] 5.9 Implementar barra de filtros

    - Crear método `filterBar()` con createfilterBar
    - Agregar filtro por UDN
    - Agregar filtro por rango de fechas con dataPicker
    - Conectar filtros con método lsModulesUnlocked
    - _Requirements: 5.1, 5.2_

- [x] 6. Crear archivo index.php


  - Crear estructura HTML base con div#root
  - Incluir scripts de CoffeeSoft: coffeSoft.js y plugins.js
  - Incluir script admin.js del proyecto
  - Agregar estilos de TailwindCSS
  - _Requirements: 1.1_

- [ ]* 7. Integración y pruebas funcionales
  - [ ]* 7.1 Probar flujo completo de apertura de módulo
    - Verificar que modal se abre correctamente
    - Probar validación de campos obligatorios
    - Verificar inserción en base de datos
    - Confirmar actualización de tabla sin recargar
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

  - [ ]* 7.2 Probar cambio de estado de bloqueo
    - Verificar que ícono cambia al hacer clic
    - Confirmar actualización en base de datos
    - Probar que operation_date se actualiza
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

  - [ ]* 7.3 Probar configuración de horarios
    - Verificar que modal muestra horarios actuales
    - Probar validación de meses pasados
    - Confirmar actualización en base de datos
    - Verificar que tabla refleja cambios
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9_

  - [ ]* 7.4 Probar navegación entre tabs
    - Verificar que todas las pestañas son clickeables
    - Confirmar que contenido cambia correctamente
    - Probar que estado se mantiene al cambiar tabs
    - _Requirements: 1.2_

  - [ ]* 7.5 Probar filtros y búsquedas
    - Verificar filtro por UDN
    - Probar filtro por rango de fechas
    - Confirmar que resultados son correctos
    - _Requirements: 5.1, 5.2_
