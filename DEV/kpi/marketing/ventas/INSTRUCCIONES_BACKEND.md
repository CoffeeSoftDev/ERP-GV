# Instrucciones para Modificar el Backend - Consulta Ventas

## Objetivo
Agregar el cálculo de totales por categoría en la función `lsSales` del controlador para mostrar cards de resumen.

## Archivo a Modificar
`kpi/marketing/ventas/ctrl/[nombre-controlador].php`

## Modificación Requerida

En la función `lsSales()`, después de procesar todos los registros y antes del `return`, agregar el cálculo de totales:

```php
function lsSales() {
    $__row = [];
    $udn = $_POST['udn'];
    $anio = $_POST['anio'];
    $mes = $_POST['mes'];
    
    // Inicializar totales
    $totales = [
        'total_registros' => 0,
        'total_hospedaje' => 0,
        'total_ayb' => 0,
        'total_alimentos' => 0,
        'total_bebidas' => 0,
        'total_diversos' => 0,
        'total_general' => 0
    ];
    
    // Obtener datos del modelo
    $ventas = $this->listVentasPorMes([$udn, $anio, $mes]);
    
    foreach ($ventas as $venta) {
        $totales['total_registros']++;
        
        if ($udn == 1) {
            // Hotel
            $hospedaje = floatval($venta['hospedaje'] ?? 0);
            $ayb = floatval($venta['ayb'] ?? 0);
            $diversos = floatval($venta['diversos'] ?? 0);
            
            $totales['total_hospedaje'] += $hospedaje;
            $totales['total_ayb'] += $ayb;
            $totales['total_diversos'] += $diversos;
            $totales['total_general'] += ($hospedaje + $ayb + $diversos);
            
            $__row[] = [
                'fecha' => formatSpanishDate($venta['fecha']),
                'Hospedaje' => evaluar($hospedaje),
                'A&B' => evaluar($ayb),
                'Diversos' => evaluar($diversos),
                'Total' => evaluar($hospedaje + $ayb + $diversos),
                'dropdown' => [
                    ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "app.editSale({$venta['id']})"],
                    ['icon' => 'icon-refresh', 'text' => 'Sincronizar', 'onclick' => "app.syncToFolio('{$venta['fecha']}', {$udn})"]
                ]
            ];
        } else {
            // Restaurantes
            $alimentos = floatval($venta['alimentos'] ?? 0);
            $bebidas = floatval($venta['bebidas'] ?? 0);
            
            $totales['total_alimentos'] += $alimentos;
            $totales['total_bebidas'] += $bebidas;
            $totales['total_general'] += ($alimentos + $bebidas);
            
            $__row[] = [
                'fecha' => formatSpanishDate($venta['fecha']),
                'Alimentos' => evaluar($alimentos),
                'Bebidas' => evaluar($bebidas),
                'Total' => evaluar($alimentos + $bebidas),
                'dropdown' => [
                    ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "app.editSale({$venta['id']})"],
                    ['icon' => 'icon-refresh', 'text' => 'Sincronizar', 'onclick' => "app.syncToFolio('{$venta['fecha']}', {$udn})"]
                ]
            ];
        }
    }
    
    return [
        'row' => $__row,
        'totales' => $totales  // ← AGREGAR ESTA LÍNEA
    ];
}
```

## Estructura de Respuesta Esperada

```json
{
    "row": [
        {
            "fecha": "01 Enero 2025",
            "Hospedaje": "$1,500.00",
            "A&B": "$800.00",
            "Diversos": "$200.00",
            "Total": "$2,500.00",
            "dropdown": [...]
        }
    ],
    "totales": {
        "total_registros": 31,
        "total_hospedaje": 45000.00,
        "total_ayb": 25000.00,
        "total_alimentos": 15000.00,
        "total_bebidas": 10000.00,
        "total_diversos": 5000.00,
        "total_general": 75000.00
    }
}
```

## Notas Importantes

1. **Usar `_Read()` para consultas**: Todas las consultas SELECT deben usar el método `_Read()` con SQL raw según la nueva regla del MDL.

2. **Ejemplo de consulta en el modelo**:
```php
function listVentasPorMes($array) {
    $query = "
        SELECT 
            id,
            fecha,
            hospedaje,
            ayb,
            alimentos,
            bebidas,
            diversos
        FROM {$this->bd}ventas_diarias
        WHERE udn = ?
        AND YEAR(fecha) = ?
        AND MONTH(fecha) = ?
        ORDER BY fecha ASC
    ";
    return $this->_Read($query, $array);
}
```

3. **Validar datos**: Usar `floatval()` para asegurar que los valores numéricos sean correctos.

4. **Formato de moneda**: Usar la función `evaluar()` para formatear los montos en la tabla.

## Frontend ya Implementado ✅

El frontend ya está listo y espera recibir el objeto `totales` en la respuesta. Las cards se mostrarán automáticamente cuando el backend devuelva los datos correctos.
