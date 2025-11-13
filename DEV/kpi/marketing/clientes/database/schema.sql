-- ============================================
-- Script de Creación de Base de Datos
-- Módulo: Gestión de Clientes
-- Sistema: KPI / Marketing - CoffeeSoft ERP
-- ============================================

-- Tabla: cliente
-- Descripción: Almacena la información principal de los clientes
-- que realizan pedidos a domicilio en las unidades de negocio
CREATE TABLE IF NOT EXISTS cliente (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nombre TEXT NOT NULL,
    apellido_paterno TEXT,
    apellido_materno TEXT,
    vip SMALLINT(6) DEFAULT 0 COMMENT '0 = Cliente regular, 1 = Cliente VIP',
    telefono TEXT NOT NULL,
    correo TEXT,
    fecha_cumpleaños DATE,
    fecha_creacion DATETIME(0) DEFAULT CURRENT_TIMESTAMP,
    udn_id INT(11),
    active SMALLINT(6) DEFAULT 1 COMMENT '1 = Activo, 0 = Inactivo',
    FOREIGN KEY (udn_id) REFERENCES udn(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: domicilio_cliente
-- Descripción: Almacena los domicilios de entrega de los clientes
-- Permite múltiples domicilios por cliente (futuro)
CREATE TABLE IF NOT EXISTS domicilio_cliente (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT(11) NOT NULL,
    calle TEXT NOT NULL,
    numero_exterior TEXT,
    numero_interior TEXT,
    colonia TEXT,
    ciudad TEXT,
    estado TEXT,
    codigo_postal TEXT,
    referencias TEXT COMMENT 'Referencias para localizar el domicilio',
    es_principal SMALLINT(6) DEFAULT 1 COMMENT '1 = Domicilio principal, 0 = Secundario',
    FOREIGN KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para mejorar el rendimiento de consultas
CREATE INDEX idx_cliente_telefono ON cliente(telefono(20));
CREATE INDEX idx_cliente_udn ON cliente(udn_id);
CREATE INDEX idx_cliente_active ON cliente(active);
CREATE INDEX idx_cliente_vip ON cliente(vip);
CREATE INDEX idx_domicilio_cliente ON domicilio_cliente(cliente_id);

-- ============================================
-- Comentarios de Documentación
-- ============================================
-- 
-- TABLA: cliente
-- - id: Identificador único autoincremental
-- - nombre: Nombre del cliente (obligatorio)
-- - apellido_paterno: Apellido paterno (opcional)
-- - apellido_materno: Apellido materno (opcional)
-- - vip: Indicador de cliente VIP para seguimiento preferencial
-- - telefono: Teléfono de contacto principal (obligatorio, validar 10+ dígitos)
-- - correo: Correo electrónico (opcional, validar formato)
-- - fecha_cumpleaños: Fecha de cumpleaños para estrategias de marketing
-- - fecha_creacion: Timestamp automático de registro
-- - udn_id: Unidad de negocio de procedencia (obligatorio)
-- - active: Estado del cliente (baja lógica, no física)
--
-- TABLA: domicilio_cliente
-- - id: Identificador único autoincremental
-- - cliente_id: Referencia al cliente (ON DELETE CASCADE)
-- - calle: Calle del domicilio (obligatorio)
-- - numero_exterior: Número exterior
-- - numero_interior: Número interior (departamento, suite, etc.)
-- - colonia: Colonia o barrio
-- - ciudad: Ciudad
-- - estado: Estado o provincia
-- - codigo_postal: Código postal
-- - referencias: Indicaciones para localizar el domicilio
-- - es_principal: Indica el domicilio principal de entrega
--
-- RELACIONES:
-- - cliente.udn_id -> udn.id (Unidad de negocio)
-- - domicilio_cliente.cliente_id -> cliente.id (Cascada en eliminación)
--
-- ÍNDICES:
-- - idx_cliente_telefono: Búsqueda rápida por teléfono
-- - idx_cliente_udn: Filtrado por unidad de negocio
-- - idx_cliente_active: Filtrado por estatus activo/inactivo
-- - idx_cliente_vip: Filtrado de clientes VIP
-- - idx_domicilio_cliente: Consulta de domicilios por cliente
--
-- ============================================
