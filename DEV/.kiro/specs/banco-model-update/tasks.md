# Implementation Plan - Actualización del Modelo de Banco

## Overview

Este plan de implementación detalla las tareas necesarias para actualizar el modelo de datos del módulo de banco en el sistema de contabilidad CoffeeSoft. Las tareas están organizadas en orden secuencial para garantizar una implementación segura y sin interrupciones.

---

## Tasks

- [x] 1. Preparación y Backup


  - Crear backup completo de la base de datos antes de realizar cambios
  - Verificar que el backup sea restaurable
  - Documentar el estado actual de las tablas banks y bank_accounts
  - _Requirements: 8.1, 8.2, 8.3, 8.4_



- [ ] 2. Crear Script SQL de Actualización
  - [ ] 2.1 Crear archivo banco_schema.sql con la estructura completa
    - Definir tabla banks con todos los campos y constraints
    - Definir tabla bank_accounts con todos los campos y constraints
    - Agregar foreign keys entre bank_accounts y banks
    - Agregar foreign keys entre bank_accounts y payment_methods
    - Agregar foreign keys entre bank_accounts y udn

    - Crear índices para optimizar consultas
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3, 3.5_

  - [ ] 2.2 Agregar validaciones y constraints
    - Implementar UNIQUE constraint en banks.name

    - Implementar NOT NULL constraints en campos obligatorios
    - Configurar ON DELETE y ON UPDATE para foreign keys
    - _Requirements: 1.2, 2.2, 2.3, 7.1, 7.2_

  - [ ] 2.3 Agregar timestamps automáticos
    - Configurar created_at con DEFAULT CURRENT_TIMESTAMP
    - Configurar updated_at con ON UPDATE CURRENT_TIMESTAMP
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 3. Ejecutar Migración de Base de Datos
  - [ ] 3.1 Verificar estructura actual de las tablas
    - Ejecutar DESCRIBE banks y DESCRIBE bank_accounts
    - Documentar diferencias con la estructura objetivo
    - _Requirements: 1.1, 2.1_

  - [ ] 3.2 Ejecutar script SQL de actualización
    - Ejecutar banco_schema.sql en ambiente de desarrollo
    - Verificar que no haya errores en la ejecución
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 3.3 Verificar integridad de datos
    - Verificar que todas las foreign keys estén correctamente creadas


    - Verificar que todos los índices estén creados
    - Ejecutar queries de prueba para validar relaciones
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_


- [ ] 4. Actualizar Modelo PHP (mdl-banco.php)
  - [ ] 4.1 Agregar método updateBank()
    - Implementar método usando _Update() de la clase CRUD
    - Seguir el patrón de updateBankAccount() existente
    - Validar parámetros de entrada


    - _Requirements: 1.3, 7.1_

  - [ ] 4.2 Verificar métodos existentes
    - Revisar que listBanks() funcione con la nueva estructura
    - Revisar que listBankAccounts() funcione con las foreign keys
    - Revisar que createBank() y createBankAccount() funcionen correctamente
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 4.3 Actualizar consultas SQL si es necesario
    - Verificar que los JOINs en listBankAccounts() usen las foreign keys correctas
    - Optimizar queries aprovechando los nuevos índices
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

- [ ] 5. Verificar Funcionalidad del Controlador
  - [ ] 5.1 Probar método init()
    - Verificar que retorne correctamente lsUDN(), lsPaymentMethods() y listBanks()
    - Validar estructura de datos retornados
    - _Requirements: 4.5_

  - [ ] 5.2 Probar métodos de listado
    - Ejecutar lsBankAccounts() con diferentes filtros
    - Verificar que los filtros por UDN, payment_method y active funcionen
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

  - [ ] 5.3 Probar métodos de creación
    - Ejecutar addBank() con datos válidos
    - Ejecutar addBankAccount() con datos válidos
    - Verificar validaciones de campos obligatorios
    - _Requirements: 1.2, 2.2, 2.3, 7.1, 7.2, 7.3_

  - [ ] 5.4 Probar métodos de edición
    - Ejecutar editBankAccount() con datos válidos
    - Verificar que las validaciones funcionen correctamente
    - _Requirements: 2.2, 2.3, 7.1, 7.2, 7.3_

  - [ ] 5.5 Probar cambio de estado
    - Ejecutar toggleStatusAccount() para activar cuenta
    - Ejecutar toggleStatusAccount() para desactivar cuenta
    - Verificar mensajes de confirmación
    - _Requirements: 1.3, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ] 6. Verificar Funcionalidad del Frontend
  - [ ] 6.1 Probar filtros
    - Seleccionar diferentes UDN y verificar resultados
    - Seleccionar diferentes formas de pago y verificar resultados
    - Cambiar entre activas/inactivas y verificar resultados
    - Aplicar múltiples filtros simultáneamente
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ] 6.2 Probar visualización de datos
    - Verificar que se muestre el nombre del banco correctamente
    - Verificar que se muestre el alias o "Sin alias"
    - Verificar formato de últimos 4 dígitos (****XXXX)
    - Verificar que se muestre la UDN correctamente
    - Verificar que se muestre la forma de pago o "N/A"
    - Verificar badges de estado (verde/rojo)
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [ ] 6.3 Probar formularios
    - Abrir formulario de nuevo banco y verificar campos
    - Abrir formulario de nueva cuenta y verificar campos
    - Verificar que los selects se llenen correctamente
    - Verificar validación de últimos 4 dígitos
    - _Requirements: 1.2, 2.2, 2.3, 2.4, 2.5_

  - [ ] 6.4 Probar acciones CRUD
    - Crear un nuevo banco
    - Crear una nueva cuenta bancaria
    - Editar una cuenta bancaria existente
    - Desactivar una cuenta bancaria
    - Activar una cuenta bancaria
    - _Requirements: 1.2, 2.2, 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 7. Pruebas de Validación y Manejo de Errores
  - [ ] 7.1 Probar validaciones de banco
    - Intentar crear banco sin nombre
    - Intentar crear banco con nombre duplicado
    - Verificar mensajes de error apropiados
    - _Requirements: 1.2, 7.1, 7.2_

  - [ ] 7.2 Probar validaciones de cuenta bancaria
    - Intentar crear cuenta sin bank_id
    - Intentar crear cuenta sin last_four_digits
    - Intentar crear cuenta con last_four_digits inválidos (no numéricos)
    - Intentar crear cuenta con last_four_digits de longitud incorrecta
    - Verificar mensajes de error apropiados
    - _Requirements: 2.2, 2.3, 7.2, 7.3, 7.4, 7.5_

  - [ ] 7.3 Probar constraints de base de datos
    - Intentar eliminar un banco que tiene cuentas asociadas
    - Verificar que ON DELETE RESTRICT funcione
    - Intentar crear cuenta con bank_id inexistente
    - Verificar que foreign key constraint funcione
    - _Requirements: 3.1, 3.2, 3.3, 7.1, 7.4_

- [ ] 8. Pruebas de Integración
  - [ ] 8.1 Flujo completo de creación
    - Crear banco → Crear cuenta asociada → Verificar relación
    - Verificar que los timestamps se establezcan correctamente
    - _Requirements: 1.1, 1.2, 2.1, 2.2, 8.1, 8.2_

  - [ ] 8.2 Flujo completo de edición
    - Editar cuenta → Verificar cambios → Verificar updated_at
    - _Requirements: 2.2, 8.3, 8.4_

  - [ ] 8.3 Flujo completo de filtrado
    - Aplicar filtro por UDN → Verificar resultados
    - Aplicar filtro por forma de pago → Verificar resultados
    - Aplicar filtro por estado → Verificar resultados
    - Combinar múltiples filtros → Verificar resultados
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ] 8.4 Flujo completo de cambio de estado
    - Desactivar cuenta → Verificar que no aparezca en filtro de activas
    - Verificar que siga apareciendo en registros históricos
    - Activar cuenta → Verificar que aparezca en filtro de activas
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ] 9. Optimización y Performance
  - [ ] 9.1 Verificar uso de índices
    - Ejecutar EXPLAIN en queries principales
    - Verificar que los índices se estén utilizando
    - _Requirements: 1.4, 3.5_

  - [ ] 9.2 Medir tiempos de respuesta
    - Medir tiempo de listBankAccounts() con diferentes filtros
    - Medir tiempo de creación de banco y cuenta
    - Verificar que los tiempos sean aceptables (<500ms)
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 10. Documentación y Deployment
  - [ ] 10.1 Actualizar documentación técnica
    - Documentar cambios en la estructura de base de datos
    - Documentar nuevo método updateBank()
    - Actualizar diagramas ER si existen
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 10.2 Crear guía de migración
    - Documentar pasos para ejecutar la migración
    - Documentar plan de rollback
    - Incluir checklist de verificación
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 10.3 Preparar deployment a producción
    - Crear script de deployment automatizado
    - Programar ventana de mantenimiento
    - Notificar a usuarios sobre cambios
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 10.4 Ejecutar deployment
    - Crear backup de producción
    - Ejecutar script SQL en producción
    - Actualizar archivo mdl-banco.php en producción
    - Verificar funcionalidad en producción
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

  - [ ] 10.5 Monitoreo post-deployment
    - Monitorear logs por 24 horas
    - Verificar que no haya errores
    - Recopilar feedback de usuarios
    - _Requirements: 1.1, 2.1, 3.1, 3.2, 3.3_

---

## Notes

- Las tareas deben ejecutarse en orden secuencial
- Cada tarea debe completarse y verificarse antes de continuar con la siguiente
- Si alguna tarea falla, ejecutar el plan de rollback documentado en el diseño
- Las tareas marcadas con "*" son opcionales y pueden omitirse si no se requiere testing exhaustivo
- Se recomienda ejecutar todas las tareas en ambiente de desarrollo antes de deployment a producción
