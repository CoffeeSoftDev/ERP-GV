# Implementation Plan

- [x] 1. Modificar el layout del historial para incluir filtro de fecha y contador


  - Actualizar el método `layoutCaptureForm()` para incluir el filtro de fecha en el encabezado del historial
  - Agregar contenedor para el contador de métricas en la parte superior derecha
  - Implementar estructura HTML para el área desplazable con altura mínima y máxima
  - _Requirements: 1.1, 2.2, 3.2, 4.1_



- [ ] 2. Implementar el componente de filtro de fecha
  - [ ] 2.1 Crear método `createHistoryFilter()` para generar el select de meses
    - Generar opciones "Todas" y meses del año (Enero-Diciembre)
    - Aplicar estilos consistentes con el framework CoffeeSoft


    - Configurar evento onChange para activar el filtrado
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ] 2.2 Implementar método `filterHistoryByMonth()` para filtrado local
    - Filtrar métricas por mes seleccionado usando JavaScript del lado cliente


    - Manejar caso especial "Todas" para mostrar todas las métricas
    - Mantener referencia al dataset original para filtrado eficiente
    - _Requirements: 1.3, 1.4, 1.5_



- [ ] 3. Desarrollar sistema de contador de métricas
  - [ ] 3.1 Crear método `updateMetricsCounter()` para mostrar conteo dinámico
    - Implementar contador con formato "X métricas"
    - Posicionar en la parte superior derecha del encabezado
    - Aplicar estilos de tipografía consistentes con el diseño actual

    - _Requirements: 3.1, 3.3, 3.4_

  - [ ] 3.2 Integrar actualización automática del contador con filtros
    - Conectar contador con el sistema de filtrado por fecha
    - Actualizar contador cuando se cambian filtros principales (UDN, Año)

    - Manejar casos de error mostrando placeholder "-- métricas"
    - _Requirements: 3.2, 5.5_

- [ ] 4. Implementar área desplazable para el historial
  - [ ] 4.1 Aplicar estilos CSS para scroll vertical
    - Establecer `min-height: 400px` y `max-height: 600px`
    - Configurar `overflow-y: auto` para scroll automático
    - Implementar scroll suave y responsivo
    - _Requirements: 2.1, 2.2, 2.3_



  - [ ] 4.2 Mantener funcionalidad de las tarjetas dentro del área desplazable
    - Verificar que botones de editar y eliminar funcionen correctamente
    - Conservar hover effects y transiciones de las tarjetas
    - Asegurar que el espaciado y diseño se mantenga consistente


    - _Requirements: 2.4, 5.3, 5.5_

- [ ] 5. Actualizar título dinámico con información del año
  - [ ] 5.1 Crear método `updateHistoryTitle()` para título dinámico
    - Implementar formato "Historial de Métricas - Año YYYY"
    - Obtener año del filtro principal existente
    - Mantener icono y estilos del título actual




    - _Requirements: 4.1, 4.3, 4.4_

  - [ ] 5.2 Integrar actualización automática del título
    - Conectar con cambios en el filtro de año principal
    - Actualizar título cuando se carga el componente inicialmente
    - Sincronizar con el método `reloadCaptureAndHistory()`
    - _Requirements: 4.2, 4.4_

- [ ] 6. Modificar método `loadHistory()` principal
  - [ ] 6.1 Integrar todos los componentes nuevos en el flujo principal
    - Llamar a `createHistoryFilter()` después de crear el contenedor
    - Ejecutar `updateMetricsCounter()` después de renderizar las tarjetas
    - Invocar `updateHistoryTitle()` al inicio del método
    - _Requirements: 1.1, 3.1, 4.1_

  - [ ] 6.2 Mantener compatibilidad con funcionalidad existente
    - Preservar llamadas a API existentes sin modificaciones
    - Conservar manejo de errores y estados de carga actuales
    - Asegurar que métodos `editHistory()` y `deleteHistory()` sigan funcionando
    - _Requirements: 5.5_

- [ ]* 7. Implementar manejo de errores y casos edge
  - Agregar validación para selecciones de filtro inválidas
  - Implementar fallbacks para errores de renderizado
  - Manejar casos de datasets vacíos o muy grandes
  - _Requirements: 1.5, 3.2_

- [ ]* 8. Optimizar performance para grandes volúmenes de datos
  - Implementar debouncing para filtros si es necesario
  - Considerar lazy loading para scroll en datasets grandes
  - Optimizar manipulación del DOM para mejor rendimiento
  - _Requirements: 2.3_