-- ============================================
-- Script de Datos de Prueba
-- Módulo: Gestión de Clientes
-- Sistema: KPI / Marketing - CoffeeSoft ERP
-- ============================================

-- NOTA: Este script es opcional y solo para desarrollo/pruebas
-- Asegúrate de que existan registros en la tabla 'udn' antes de ejecutar

-- Insertar clientes de prueba
INSERT INTO cliente (nombre, apellido_paterno, apellido_materno, vip, telefono, correo, fecha_cumpleaños, udn_id, active) VALUES
('Juan', 'Pérez', 'García', 1, '4421234567', 'juan.perez@email.com', '1985-03-15', 1, 1),
('María', 'López', 'Martínez', 0, '4429876543', 'maria.lopez@email.com', '1990-07-22', 1, 1),
('Carlos', 'Rodríguez', 'Hernández', 1, '4425551234', 'carlos.rodriguez@email.com', '1988-11-30', 2, 1),
('Ana', 'González', 'Ramírez', 0, '4427778888', 'ana.gonzalez@email.com', '1992-05-10', 2, 1),
('Luis', 'Sánchez', 'Torres', 0, '4423334444', 'luis.sanchez@email.com', NULL, 1, 1),
('Patricia', 'Flores', 'Morales', 1, '4426665555', 'patricia.flores@email.com', '1987-09-18', 3, 1),
('Roberto', 'Díaz', 'Castro', 0, '4428889999', 'roberto.diaz@email.com', '1995-02-28', 1, 0),
('Laura', 'Jiménez', 'Ortiz', 0, '4421112222', 'laura.jimenez@email.com', '1991-12-05', 2, 0);

-- Insertar domicilios de prueba
INSERT INTO domicilio_cliente (cliente_id, calle, numero_exterior, numero_interior, colonia, ciudad, estado, codigo_postal, referencias, es_principal) VALUES
(1, 'Av. Constituyentes', '123', NULL, 'Centro', 'Querétaro', 'Querétaro', '76000', 'Frente al parque', 1),
(2, 'Calle Juárez', '456', 'Depto 3', 'El Marqués', 'Querétaro', 'Querétaro', '76047', 'Edificio azul', 1),
(3, 'Blvd. Bernardo Quintana', '789', NULL, 'Carretas', 'Querétaro', 'Querétaro', '76050', 'Casa con portón negro', 1),
(4, 'Calle Hidalgo', '321', NULL, 'San Pablo', 'Querétaro', 'Querétaro', '76125', 'Junto a la tienda', 1),
(5, 'Av. Universidad', '654', 'Piso 2', 'Juriquilla', 'Querétaro', 'Querétaro', '76230', 'Torre B', 1),
(6, 'Calle Morelos', '987', NULL, 'Tabachines', 'Querétaro', 'Querétaro', '76180', 'Casa blanca con jardín', 1),
(7, 'Av. 5 de Febrero', '147', NULL, 'Niños Héroes', 'Querétaro', 'Querétaro', '76010', 'Esquina con Allende', 1),
(8, 'Calle Zaragoza', '258', 'Local 5', 'Centro Sur', 'Querétaro', 'Querétaro', '76090', 'Plaza comercial', 1);

-- ============================================
-- Verificación de Datos Insertados
-- ============================================

-- Consultar clientes activos
-- SELECT * FROM cliente WHERE active = 1;

-- Consultar clientes VIP
-- SELECT * FROM cliente WHERE vip = 1 AND active = 1;

-- Consultar clientes con sus domicilios
-- SELECT c.*, d.* FROM cliente c 
-- LEFT JOIN domicilio_cliente d ON c.id = d.cliente_id 
-- WHERE c.active = 1;

-- ============================================
