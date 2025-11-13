<?php
if (empty($_POST['opc'])) exit(0);
require_once '../mdl/mdl-summary.php';

class ctrl extends mdl {
    function lsSummary() {
        $udn_id        = $_POST['udn_id'];
        $red_social_id = $_POST['red_social_id'];
        $año           = $_POST['año'];
        $mes           = $_POST['mes'];

        $data           = $this->getCampaignSummary([$udn_id, $red_social_id, $año, $mes]);
        $totals         = $this->getCampaignTotals([$udn_id, $red_social_id, $año, $mes]);
        $monthlySummary = $this->getMonthlySummary([$udn_id, $red_social_id, $año, $mes]);

        $agrupado = [];
        $suma_cpc = 0;
        $conteo_anuncios = 0;

        foreach ($data as $item) {
            $campaña = $item['campaña'];

            if (!isset($agrupado[$campaña])) {
                $agrupado[$campaña] = [
                    'group' => $campaña,
                    'rows' => [],
                    'footer' => [
                        'Resultados (clics)' => 0,
                        'Inversión'          => 0,
                    ]
                ];
            }

            $clics = (int) $item['total_clics'];
            $monto = (float) $item['total_monto'];
            $cpc = $clics > 0 ? $monto / $clics : 0;

            // Sumar CPC y contar anuncio
            $suma_cpc += $cpc;
            $conteo_anuncios++;

            $agrupado[$campaña]['rows'][] = [
                'Anuncio'         => $item['anuncio'],
                'Clasificación'   => $item['clasificacion'],
                'Resultados (clics)' => $clics,
                'Inversión'       => evaluar($monto),
                'CPC'             => evaluar($cpc),
            ];

            $agrupado[$campaña]['footer']['Resultados (clics)'] += $clics;
            $agrupado[$campaña]['footer']['Inversión']          += $monto;
        }

        // Evaluar totales por campaña
        foreach ($agrupado as &$grupo) {
            $grupo['footer']['Inversión'] = evaluar($grupo['footer']['Inversión']);
        }

        // Añadir CPC promedio al objeto totals
        $totals[0]['promedio_cpc'] = $conteo_anuncios > 0 ? $suma_cpc / $conteo_anuncios : 0;

        return [
            'grouped' => array_values($agrupado),
            'totals'  => $totals,
            'monthlySummary' => $monthlySummary
        ];
    }

}

function evaluar($valor) {
    return '$' . number_format($valor, 2, '.', ',');
}
$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());