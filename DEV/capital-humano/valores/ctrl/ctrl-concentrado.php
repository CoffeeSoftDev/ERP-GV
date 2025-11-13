<?php
if(empty($_POST['opc'])) exit(0);

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

require_once('../mdl/mdl-concentrado.php');
require_once('../../../conf/_Utileria.php');

class Concentrado extends MConcentrado {
    function init(){
        return ['udn' => $this->lsUDN()];
    }
    function lsPeriodos(){
        return ['periodos' => $this->listPeriodos([$_POST['id']])];
    }
    function lsEvaluateds(){
        return ["evaluados" => $this->lsColaboradores([$_POST['id']])];
    }

    function getEvaluator(){
        $evaluators = $this->getListEvaluators([$_POST['id']]);
        return [
            'colaborador' => $evaluators
        ];

    }
    // 📌 Creación de tabla de concentrados
    public function table() {
        $this->loadRequestParams();
        $thead = $this->buildHeader();
        $tbody = $this->buildBody();

        return [
            'table'         => ['id' => 'tbConcentrado-'.$this->tipo_vista],
            'thead'         => $thead,
            'tbody'         => $tbody['rows'],
            'promedio_gral' => number_format($tbody['promedio_general'], 2)
        ];
    }

    // 📌 Carga los datos del POST como propiedades
    private function loadRequestParams() {
        $this->udn         = $_POST['udn'];
        $this->periodo     = $_POST['periodo'];
        $this->colaborador = $_POST['colaborador'];
        $this->tipo_vista  = $_POST['type'];
    }
    // 📌 Construye el encabezado de la tabla
    private function buildHeader() {
        $thead             = ["preguntas"];
        $this->evaluadores = $this->getEvaluators([
            $this->udn, $this->periodo, $this->colaborador
        ]);

        if ($this->tipo_vista === 'detallado') {
            foreach ($this->evaluadores as $i => $ev) {
                $thead[] = "#" . ($i + 1);
            }
            $thead[] = "total";
            $thead[] = "Promedio";
            $thead[] = "Evaluadores";
        }

        array_push($thead, "Promedio Total", "Promedio General");
        return $thead;
    }
    // 📌 Construye el cuerpo de la tabla y calcula el promedio general
    private function buildBody() {
        $rows                = [];
        $suma_promedio_gral  = 0;
        $tabulacion          = $this->resultTabulacion([$this->udn, $this->periodo, $this->colaborador]);
        $count_all_questions = $this->countAllQuestions();
        $valores             = $this->getValuesCorporate();
        $count_valores       = count($valores);
        $count_evaluadores   = count($this->evaluadores);

        foreach ($valores as $i => $valor) {
            $questions        = $this->getQuestionsByValue([$valor['id']]);
            $count_questions  = count($questions);
            $promedio_total   = 0;
            $bloque_preguntas = [];

            $promedios_por_pregunta = [];

            // 1️⃣ Primero, calcula todos los promedios
            foreach ($questions as $pregunta) {
                $total = 0;
                foreach ($this->evaluadores as $ev) {
                    $respuesta = $this->getAnswered([
                        $this->colaborador, $pregunta['id'], $ev['idEvaltion']
                    ]);
                    $total += $respuesta;
                }
                $promedio = $total / $count_evaluadores;
                $promedios_por_pregunta[$pregunta['id']] = $promedio;
            }

            // 2️⃣ Encontrar el máximo y mínimo
            $max = max($promedios_por_pregunta);
            $min = min($promedios_por_pregunta);

            // Armar las filas con colores aplicados
            foreach ($questions as $pregunta) {
                $row           = [];
                $td_evaluacion = [];
                $total         = 0;

                foreach ($this->evaluadores as $ev) {
                    $respuesta = $this->getAnswered([
                        $this->colaborador, $pregunta['id'], $ev['idEvaltion']
                    ]);
                    $total += $respuesta;
                    $td_evaluacion[] = ["html" => $respuesta, "class" => "text-center"];
                }

                $promedio = $promedios_por_pregunta[$pregunta['id']];
                $promedio_total += $promedio;
                $promedio_fmt = number_format($promedio, 2);

                // 3️⃣ Determina color de fondo según si es el máximo o mínimo
                $style = '';
                if ($promedio == $max) {
                    $style = 'background-color:#B9F8CF;';
                } elseif ($promedio == $min) {
                    $style = 'background-color:#FFC9C9;';
                }

                $row[] = [
                    "html"  => $pregunta['valor'],
                    "style" => $style
                ];

                if ($this->tipo_vista === 'detallado') {
                    $row   = array_merge($row, $td_evaluacion);
                    $row[] = ["html" => $total, "class" => "text-center"];
                    $row[] = ["html" => $promedio_fmt, "class" => "text-center fw-bold", "style" => $style];
                    $row[] = ["html" => $count_evaluadores, "class" => "text-center"];
                }


                $bloque_preguntas[] = $row;
            }

            // 🧱 Fila de encabezado por valor corporativo
            $promedio_valor      = $promedio_total / $count_questions;
            $suma_promedio_gral += $promedio_valor;

            $colspan   = ($this->tipo_vista === 'detallado') ? (4 + $count_evaluadores) : 1;
            $row_valor = [[
                "html"    => $valor['valor'],
                "class"   => "fw-bold text-uppercase",
                "colspan" => $colspan
            ]];

            $identificador_valores = [
                1 => "te",
                2 => "ap",
                3 => "pr",
                4 => "ps",
            ];


            if ($this->tipo_vista === 'concentrado') {
                $promedio_valor = $tabulacion[$identificador_valores[$valor['id']]];
            }

            $row_valor[] = [
                "html"    => number_format($promedio_valor, 2),
                "class"   => "text-center fw-bold fs-1",
                "rowspan" => $count_questions + 1,
            ];

            if ($i == 0) {
                $promedio_general = $this->tipo_vista === 'concentrado' ? $tabulacion['calf'] :  $suma_promedio_gral / $count_valores;

                $row_valor[] = [
                    "html"    => $suma_promedio_gral,
                    "class"   => "text-center fw-bold fs-1 td_gral-".$this->tipo_vista,
                    "rowspan" => ($count_all_questions + $count_valores)
                ];
            }

            $rows[] = $row_valor;
            $rows   = array_merge($rows, $bloque_preguntas);
        }

        $promedio_general = $this->tipo_vista === 'concentrado' ? $tabulacion['calf'] :  $suma_promedio_gral / $count_valores;
        return [
            'rows'             => $rows,
            'promedio_general' => $promedio_general
        ];
    }
}

function pintarPromedio($valor) {
      // Definimos los rangos con sus colores correspondientes
    $rangos = [
        ['min' => 1,    'max' => 4,    'color' => '#FFC9C9'],  // BD bg-red-200
        ['min' => 4.01, 'max' => 4.17, 'color' => '#FFD6A7'],  // DA bg-orange-200
        ['min' => 4.18, 'max' => 4.5,  'color' => '#FFF085'],  // DE bg-yellow-200
        ['min' => 4.51, 'max' => 4.67, 'color' => '#B8E6FE'],  // AD bg-sky-200
        ['min' => 4.68, 'max' => 5,    'color' => '#B9F8CF']   // DEX bg-green-200
    ];

      // Buscar el color correspondiente al valor
    foreach ($rangos as $rango) {
        if ($valor >= $rango['min'] && $valor <= $rango['max']) {
            return 'background-color:' . $rango['color'].';';
        }
    }

      // Si no cae en ningún rango
    return '';
}

function pintarValPromedios($row, $campos) {
    $totalPromedio  = 0;
    $countPromedios = 0;

    foreach ($campos as $campo) {
              // 📜 **Extraer valores del campo**
        $currents     = array_column($row, $campo);
        $currentsVals = array_column($currents, 'val');

              // 📜 **Calcular valores máximos y mínimos**
        $maxCurrentMonth = max($currentsVals);
        $minCurrentMonth = min($currentsVals);

              // 📜 **Sumar promedios para calcular el promedio total**
        foreach ($currentsVals as $val) {
            $totalPromedio += $val;
            $countPromedios++;
        }

               // 📜 **Calcular y agregar Promedio Total**
        $promTotal = $countPromedios > 0 ? round($totalPromedio / $countPromedios,2) : 0;

              // 📜 **Aplicar estilos inline para mayor y menor**
        foreach ($row as &$rows) {
            if ($rows[$campo]['val'] == $maxCurrentMonth) {
                $rows[$campo]['style'] = 'background-color: #d4edda; text-align: end;';  // 🔵 Mayor valor (verde claro)
            } elseif ($rows[$campo]['val'] == $minCurrentMonth) {
                $rows[$campo]['style'] = 'background-color: #f8d7da; text-align: end;';  // 🔴 Menor valor (rojo claro)
            }

            $rows['Prom Total']['html'] = $promTotal;

        }
    }

    return $row;
}

$opc = $_POST['opc'];
unset($_POST['opc']);

$obj    = new Concentrado();
$encode = $obj->$opc();
echo json_encode($encode);
?>
