# Implementation Plan

## Overview

Este plan de implementación detalla las tareas necesarias para agregar funcionalidad de edición a la tabla de ventas diarias. Las tareas están organizadas en orden lógico de ejecución, comenzando por el backend (modelo y controlador) y finalizando con el frontend.

---

## Tasks

- [x] 1. Implementar métodos del modelo (mdl-ingresos.php)


  - Crear métodos para consultar y actualizar registros de ventas en la base de datos
  - _Requirements: 1.3, 4.2, 4.3_



- [ ] 1.1 Crear método getSaleById() en mdl-ingresos.php
  - Implementar query SELECT para obtener un registro por id_venta
  - Usar método _Read() heredado de CRUD
  - Retornar el primer resultado o null si no existe

  - _Requirements: 1.3, 4.2_

- [ ] 1.2 Crear método updateSale() en mdl-ingresos.php
  - Implementar query UPDATE para actualizar registro de venta
  - Usar método _Update() heredado de CRUD


  - Actualizar campos: personas/noHabitaciones, alimentos, bebidas, AyB, Hospedaje, Diversos, guarniciones, sales, domicilio, total
  - Usar WHERE id_venta = ?
  - _Requirements: 4.3, 4.5_



- [ ] 2. Implementar métodos del controlador (ctrl-ingresos.php)
  - Crear endpoints para obtener y actualizar registros de ventas
  - Implementar validaciones de negocio
  - _Requirements: 1.4, 4.1, 4.4_

- [x] 2.1 Crear método getSale() en ctrl-ingresos.php

  - Recibir id_venta desde $_POST['id']
  - Llamar a $this->getSaleById([$id])
  - Validar que el registro exista
  - Retornar formato estándar: { status, message, data }
  - Status 200 si existe, 404 si no existe
  - _Requirements: 1.3, 4.2_

- [ ] 2.2 Crear método editSale() en ctrl-ingresos.php
  - Recibir datos desde $_POST: id, noHabitaciones, y campos según UDN
  - Validar que noHabitaciones >= 0
  - Validar que todos los campos numéricos sean >= 0
  - Calcular total según UDN:

    - UDN 1: total = Hospedaje + AyB + Diversos
    - UDN 5: total = alimentos + bebidas + guarniciones + sales + domicilio
    - Otros: total = alimentos + bebidas
  - Agregar total a $_POST
  - Llamar a $this->updateSale($this->util->sql($_POST, 1))
  - Retornar status 200 si éxito, 400 si validación falla, 500 si error de BD
  - _Requirements: 1.4, 4.1, 4.3, 4.4, 4.5_



- [ ] 2.3 Modificar método lsIngresosCaptura() para agregar columna de acciones
  - Después de construir $row, agregar campo 'acciones'
  - Si $softVentas['id_venta'] existe, crear botón HTML con:
    - Clase: btn btn-sm btn-primary
    - Icono: icon-pencil
    - Onclick: sales.editSale(id_venta)
  - Si no existe id_venta, dejar campo vacío
  - Aplicar clase 'text-center' al campo
  - _Requirements: 2.1, 2.3_


- [ ] 3. Implementar método jsonEditSale() en clase Sales (kpi-ventas.js)
  - Crear método que retorna array de campos del formulario
  - Obtener UDN actual desde $('#filterBarsales #udn').val()
  - Agregar campo noHabitaciones (tipo: numero) para todas las UDN
  - Si UDN == 1, agregar campos: Hospedaje, AyB, Diversos (tipo: cifra)
  - Si UDN == 5, agregar campos: alimentos, bebidas, guarniciones, sales, domicilio (tipo: cifra)
  - Si UDN es otro, agregar campos: alimentos, bebidas (tipo: cifra)
  - Agregar botón submit con texto "Guardar Cambios"
  - Aplicar clases Bootstrap: col-12 col-md-6 mb-3 a cada campo
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 6.2_

- [ ] 4. Implementar método editSale(id) en clase Sales (kpi-ventas.js)
  - Declarar método async
  - Hacer petición GET con useFetch:
    - url: this._link
    - data: { opc: "getSale", id: id }


  - Guardar respuesta en variable request
  - Llamar a this.createModalForm con:
    - id: 'formEditSale'
    - data: { opc: 'editSale', id: id }
    - autofill: request.data
    - json: this.jsonEditSale()
    - bootbox.title: `Editar Venta - ${request.data.soft_ventas_fecha}`
    - bootbox.closeButton: true
  - En callback success:
    - Si response.status === 200, mostrar alert success y llamar this.listSales()
    - Si response.status !== 200, mostrar alert error con response.message
  - _Requirements: 1.1, 1.2, 5.1, 5.2, 5.3, 5.4, 5.5, 6.1, 6.4, 6.5_

- [ ] 5. Modificar método listSales() en clase Sales (kpi-ventas.js)
  - En configuración de createTable, modificar attr.right
  - Cambiar de right: [4] a right: [4, 5]
  - Esto habilita la columna de acciones que viene del backend
  - _Requirements: 2.1_

- [ ]* 6. Agregar validación en tiempo real a campos numéricos
  - En jsonEditSale(), agregar onkeyup a campos tipo cifra
  - Usar función validationInputForNumber() para validar entrada
  - Prevenir valores negativos
  - _Requirements: 6.3_

- [ ]* 7. Crear tests unitarios para métodos del modelo
  - Test getSaleById() con ID válido retorna registro
  - Test getSaleById() con ID inválido retorna null
  - Test updateSale() actualiza correctamente el registro
  - _Requirements: Testing Strategy_

- [ ]* 8. Crear tests unitarios para métodos del controlador
  - Test getSale() con ID válido retorna status 200
  - Test getSale() con ID inválido retorna status 404
  - Test editSale() con datos válidos retorna status 200
  - Test editSale() con valores negativos retorna status 400
  - Test editSale() calcula total correctamente según UDN
  - _Requirements: Testing Strategy_

- [ ]* 9. Crear tests de integración
  - Test flujo completo: cargar tabla → editar → guardar → verificar actualización
  - Test botón editar solo visible en registros capturados
  - Test modal se abre con datos correctos
  - Test validación de campos funciona
  - _Requirements: Testing Strategy_

- [ ]* 10. Realizar pruebas manuales
  - Verificar botón editar aparece solo en registros capturados
  - Verificar modal se abre con datos correctos
  - Verificar campos correctos según UDN
  - Verificar validación de campos numéricos
  - Verificar cambios se guardan en BD
  - Verificar tabla se refresca después de guardar
  - Verificar mensajes de error se muestran correctamente
  - Verificar botón cancelar cierra modal sin guardar
  - Verificar filtros se mantienen después de editar
  - _Requirements: Testing Strategy_

---

## Notes

- Las tareas marcadas con * son opcionales (testing)
- Cada tarea incluye referencias a los requisitos que satisface
- El orden de las tareas es importante: backend primero, luego frontend
- Las tareas de testing son opcionales pero recomendadas para asegurar calidad
- Todas las tareas de implementación (1-5) son obligatorias
- La tarea 6 (validación en tiempo real) es opcional pero mejora la UX

## Execution Order

1. **Backend (Tareas 1-2):** Implementar modelo y controlador primero para tener los endpoints listos
2. **Frontend (Tareas 3-5):** Implementar interfaz de usuario que consume los endpoints
3. **Validación (Tarea 6):** Opcional, mejora la experiencia de usuario
4. **Testing (Tareas 7-10):** Opcional, asegura calidad del código

## Dependencies

- Tarea 2.1 depende de 1.1
- Tarea 2.2 depende de 1.2
- Tarea 4 depende de 2.1 y 3
- Tarea 5 depende de 2.3
- Tareas de testing (7-10) dependen de todas las tareas de implementación
