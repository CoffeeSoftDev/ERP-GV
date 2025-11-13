# Implementation Plan - Módulo de Archivos

## 1. Configurar estructura base del proyecto

- Crear estructura de directorios en `contabilidad/captura/`
- Crear archivo `index.php` con contenedor root y scripts de CoffeeSoft
- Configurar rutas de archivos (ctrl, mdl, js)
- _Requirements: 1.1, 1.2_

## 2. Implementar modelo de base de datos

- [ ] 2.1 Crear tabla `file` con todos los campos definidos
  - Incluir campos: id, udn_id, user_id, file_name, upload_date, size_bytes, path, extension, operation_date, module
  - Definir claves foráneas con udn y usuarios
  - Crear índices para optimización (module, operation_date, udn_id)
  - _Requirements: 1.5, 4.1, 5.1_

- [ ] 2.2 Crear tabla `file_logs` para auditoría
  - Incluir campos: id, file_id, user_id, action, action_date, ip_address
  - Definir relación con tabla file (ON DELETE CASCADE)
  - Crear índices para consultas de logs
  - _Requirements: 3.2, 3.5_

## 3. Desarrollar modelo PHP (mdl-archivos.php)

- [ ] 3.1 Crear clase base del modelo
  - Extender clase CRUD
  - Configurar propiedad `$bd = "rfwsmqex_contabilidad."`
  - Inicializar Utileria
  - _Requirements: 1.1_

- [ ] 3.2 Implementar métodos de consulta de archivos
  - Crear `listFiles($array)` con filtros de fecha, módulo y UDN
  - Implementar JOIN con tablas usuarios y udn
  - Agregar ordenamiento por fecha descendente
  - _Requirements: 1.5, 4.1, 4.2, 5.2_

- [ ] 3.3 Implementar métodos CRUD de archivos
  - Crear `getFileById($array)` para obtener archivo específico
  - Crear `deleteFileById($array)` para eliminar archivo
  - _Requirements: 3.1, 3.2_

- [ ] 3.4 Implementar métodos de auditoría
  - Crear `createFileLog($array)` para registrar acciones
  - Implementar registro de fecha, usuario y acción
  - _Requirements: 3.2, 3.5_

- [ ] 3.5 Implementar métodos auxiliares
  - Crear `lsModules()` para listar módulos disponibles
  - Crear `lsUDN()` para listar unidades de negocio
  - Crear `getUserLevel($array)` para obtener nivel de acceso
  - _Requirements: 2.1, 2.2, 2.3, 5.1_

## 4. Desarrollar controlador PHP (ctrl-archivos.php)

- [ ] 4.1 Crear clase base del controlador
  - Extender clase mdl
  - Configurar validación de sesión
  - Implementar manejo de errores
  - _Requirements: 2.4_

- [ ] 4.2 Implementar método init()
  - Retornar lista de módulos con `lsModules()`
  - Retornar lista de UDN con `lsUDN()`
  - Retornar nivel de acceso del usuario
  - _Requirements: 1.4, 2.5, 5.1_

- [ ] 4.3 Implementar método ls()
  - Recibir parámetros: fi, ff, module, udn
  - Llamar a `listFiles()` del modelo
  - Construir array de filas para tabla
  - Formatear fechas con `formatSpanishDate()`
  - Agregar badges de módulo con colores
  - Incluir botones de acción según permisos
  - _Requirements: 1.5, 4.1, 4.2, 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 4.4 Implementar método getFile()
  - Validar parámetro id
  - Llamar a `getFileById()` del modelo
  - Retornar datos completos del archivo
  - _Requirements: 6.6_

- [ ] 4.5 Implementar método downloadFile()
  - Validar sesión activa
  - Validar permisos según nivel de acceso
  - Generar token temporal de descarga
  - Registrar acción en logs
  - Retornar URL segura con token
  - _Requirements: 3.3, 3.4_

- [ ] 4.6 Implementar método deleteFile()
  - Validar permisos de eliminación
  - Validar existencia del archivo
  - Eliminar archivo físico del servidor
  - Eliminar registro de base de datos
  - Registrar acción en logs
  - Retornar status y mensaje
  - _Requirements: 3.1, 3.2, 3.5_

- [ ] 4.7 Crear funciones auxiliares
  - Implementar `renderStatus()` para badges de módulo
  - Implementar `formatFileSize()` para mostrar tamaño
  - Implementar validación de extensiones permitidas
  - _Requirements: 6.5_

## 5. Desarrollar frontend JavaScript (archivos.js)

- [ ] 5.1 Crear clase App base
  - Extender clase Templates de CoffeeSoft
  - Definir `PROJECT_NAME = "archivos"`
  - Configurar `_link` y `_div_modulo`
  - _Requirements: 1.1_

- [ ] 5.2 Implementar método render()
  - Llamar a `layout()`
  - Llamar a `filterBar()`
  - Llamar a `lsFiles()`
  - _Requirements: 1.1_

- [ ] 5.3 Implementar método layout()
  - Usar `primaryLayout()` de CoffeeSoft
  - Crear header con título "📁 Módulo de Archivos"
  - Crear contenedor para tarjetas de totales
  - Crear contenedor para tabla de archivos
  - _Requirements: 1.1, 1.2_

- [ ] 5.4 Implementar método filterBar()
  - Crear selector de rango de fechas con `dataPicker()`
  - Crear dropdown de módulos con datos de init()
  - Crear dropdown de UDN (condicional según rol)
  - Crear botón "Buscar" que ejecute `lsFiles()`
  - _Requirements: 1.3, 1.4, 5.1_

- [ ] 5.5 Implementar método lsFiles()
  - Obtener valores de filtros (fechas, módulo, UDN)
  - Usar `createTable()` de CoffeeSoft
  - Configurar columnas: Fecha subida, Módulo, Subido por, Nombre, Tipo/Tamaño
  - Configurar botones de acción: Ver, Descargar, Eliminar
  - Aplicar tema 'corporativo'
  - Habilitar paginación con DataTables
  - _Requirements: 1.5, 1.6, 4.3, 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 5.6 Implementar método downloadFile(id)
  - Hacer petición AJAX a controlador
  - Validar respuesta del servidor
  - Abrir URL de descarga en nueva pestaña
  - Mostrar mensaje de éxito o error
  - _Requirements: 3.3, 3.4_

- [ ] 5.7 Implementar método deleteFile(id)
  - Usar `swalQuestion()` para confirmación
  - Mostrar mensaje "¿Está seguro de querer eliminar el archivo?"
  - Enviar petición de eliminación al controlador
  - Actualizar tabla tras eliminación exitosa
  - Mostrar mensaje de éxito o error
  - _Requirements: 1.7, 3.1, 3.2, 3.5_

- [ ] 5.8 Implementar método viewFile(id)
  - Obtener datos del archivo con `getFile()`
  - Abrir archivo en modal o nueva pestaña
  - Soportar previsualización de PDF e imágenes
  - _Requirements: 6.6_

- [ ] 5.9 Implementar renderizado de tarjetas de totales
  - Crear método `renderTotalCards()`
  - Mostrar totales por módulo: Ventas, Compras, Proveedores, Almacén
  - Actualizar totales al aplicar filtros
  - _Requirements: 1.2_

## 6. Implementar control de acceso por roles

- [ ] 6.1 Configurar validación de nivel de acceso
  - Implementar detección de rol en init()
  - Almacenar nivel de acceso en variable global
  - _Requirements: 2.1, 2.2, 2.3_

- [ ] 6.2 Aplicar restricciones de nivel Captura
  - Mostrar solo selector de fecha única (no rango)
  - Habilitar botones: Ver, Descargar, Eliminar
  - Ocultar selector de UDN
  - _Requirements: 2.1, 2.5_

- [ ] 6.3 Aplicar restricciones de nivel Gerencia
  - Mostrar selector de rango de fechas
  - Habilitar botones: Ver, Descargar
  - Deshabilitar botón Eliminar
  - Ocultar selector de UDN
  - _Requirements: 2.2, 2.5_

- [ ] 6.4 Aplicar permisos de nivel Contabilidad/Dirección
  - Mostrar selector de rango de fechas
  - Mostrar selector de UDN
  - Habilitar todos los botones: Ver, Descargar, Eliminar
  - _Requirements: 2.3, 2.5, 5.1_

## 7. Implementar seguridad y validaciones

- [ ] 7.1 Configurar validación de sesión
  - Validar sesión activa en cada petición del controlador
  - Retornar error 401 si sesión no válida
  - _Requirements: 2.4_

- [ ] 7.2 Implementar tokens de descarga seguros
  - Generar tokens aleatorios con `random_bytes()`
  - Almacenar tokens en sesión con expiración de 5 minutos
  - Validar token antes de permitir descarga
  - _Requirements: 3.3_

- [ ] 7.3 Implementar prevención de SQL injection
  - Usar prepared statements en todas las consultas
  - Sanitizar parámetros con `$this->util->sql()`
  - _Requirements: 3.2, 4.1_

- [ ] 7.4 Implementar prevención de XSS
  - Escapar HTML en nombres de archivos
  - Usar métodos seguros de jQuery (`.text()` en lugar de `.html()`)
  - _Requirements: 6.4_

## 8. Optimización y rendimiento

- [ ] 8.1 Crear índices de base de datos
  - Crear índice en `file.operation_date`
  - Crear índice en `file.module`
  - Crear índice compuesto en `(operation_date, module, udn_id)`
  - _Requirements: 4.3_

- [ ] 8.2 Implementar paginación en tabla
  - Configurar DataTables con 25 registros por página
  - Habilitar búsqueda y ordenamiento
  - _Requirements: 1.5_

- [ ] 8.3 Implementar cache de datos estáticos
  - Cachear lista de módulos en sesión
  - Cachear lista de UDN en sesión
  - _Requirements: 4.1_

## 9. Integración y pruebas

- [ ] 9.1 Integrar con sistema de navegación
  - Agregar enlace al módulo en menú principal
  - Configurar ícono "📁 Archivos"
  - _Requirements: 1.1_

- [ ] 9.2 Probar flujo completo de consulta
  - Verificar carga de totales por módulo
  - Probar filtros de fecha, módulo y UDN
  - Validar visualización de tabla
  - _Requirements: 1.2, 1.3, 1.4, 1.5_

- [ ] 9.3 Probar flujo de descarga
  - Verificar generación de token
  - Probar descarga de diferentes tipos de archivo
  - Validar registro en logs
  - _Requirements: 3.3, 3.4_

- [ ] 9.4 Probar flujo de eliminación
  - Verificar modal de confirmación
  - Probar eliminación de archivo
  - Validar actualización de tabla
  - Validar registro en logs
  - _Requirements: 1.7, 3.1, 3.2, 3.5_

- [ ] 9.5 Probar control de acceso
  - Validar restricciones de nivel Captura
  - Validar restricciones de nivel Gerencia
  - Validar permisos de nivel Contabilidad/Dirección
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

## 10. Documentación y despliegue

- [ ] 10.1 Crear documentación de usuario
  - Documentar funcionalidades por nivel de acceso
  - Crear guía de uso del módulo
  - _Requirements: 2.1, 2.2, 2.3_

- [ ] 10.2 Configurar variables de entorno
  - Definir `UPLOAD_PATH` para archivos
  - Definir `MAX_FILE_SIZE` permitido
  - Definir `ALLOWED_EXTENSIONS`
  - _Requirements: 3.3_

- [ ] 10.3 Preparar script de migración
  - Crear script SQL para tablas
  - Crear script para índices
  - Documentar proceso de despliegue
  - _Requirements: 2.1_
