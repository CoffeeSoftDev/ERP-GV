<?php
if (empty($_POST['opc']))
    exit(0);

// incluir tu modelo
require_once ('../mdl/mdl-mercadotecnia.php');
require_once ('../../conf/_Utileria.php');
require_once('../../conf/coffeSoft.php');

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Kpismerca{
    public $util;

    public function __construct(){
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria();
    }

    function lsCapturarIngresos(){

        $fi = new DateTime($_POST['Anio'].'-' . $_POST['Mes'] . '-01');
        $hoy = clone $fi;

        $hoy->modify('last day of this month');

        $__row = [];

        while ($fi <= $hoy) {
            $idRow++;
            $fecha      = $fi->format('Y-m-d');
            $softVentas = $this->getsoft_ventas([$_POST['UDN'],$fecha]);
            $idVentas   = $softVentas['id_venta'];

            $row = array(

                'id'     => $idRow,
                'fecha'  => $fecha,
                'dia'    => formatSpanishDay($fecha),
                'Estado' => $softVentas['id_venta'] ? '<i class="icon-ok-circled-2 text-success"></i>': '<i class="icon-info-circled-3 text-warning"></i>',
            
            );

            if($_POST['UDN'] == 1): // Quinta tabachines

                $total =  $softVentas['Hospedaje'] + $softVentas['AyB'] + $softVentas['Diversos'] ;

                $grupo          = $this -> createdGroups( ['noHabitaciones','Hospedaje','AyB','Diversos'],$softVentas,$idVentas);
                $grupo['total'] = evaluar($total);
                $grupo['opc']   = 0;
           
            elseif( $_POST['UDN']  == 5 ): //Sonoras meat
                
                $total          = $softVentas['alimentos'] + $softVentas['bebidas'] + $softVentas['guarniciones'] + $softVentas['sales'] + $softVentas['domicilio'];
                $grupo          = $this -> createdGroups( ['noHabitaciones','alimentos','bebidas','guarniciones','sales','domicilio'],$softVentas,$idVentas);
                $grupo['total'] = evaluar($total);
                $grupo['opc']   = 0;

            else: // BAOS

                $grupo = array(
                
                'alimentos' => createElement('input',[
                    'name'    => 'alimentos',
                    'value'   => number_format($softVentas['alimentos'],2,'.',''),
                    'onkeyup' => 'ingresosDiarios.setVentas(event,' . $idVentas . ')',

                ]),
                  
                'bebidas'   => createElement('input',[
                    'name'  => 'bebidas',
                    'value' => number_format($softVentas['bebidas'],2,'.',''),
                    'onkeyup' => 'ingresosDiarios.setVentas(event,' . $idVentas . ')',

                ]),

                'No habitaciones' => createElement('input', [

                    'name'    => 'noHabitaciones',
                    'value'   => $softVentas['noHabitaciones'],
                    'onkeyup' => 'ingresosDiarios.setVentas(event,' . $idVentas . ')'
                ]),

                 'Total' => evaluar($softVentas['bebidas'] + $softVentas['alimentos']),

               

                'opc'       => 0
            );

             

            endif;

            $__row[] = array_merge($row, $grupo);


            $fi->modify('+1 day');
        }


        #encapsular datos
        return [

            "row"      => $__row,
            "thead"    => '',
            "frm_head" => "<strong>Conectado a: </strong>  {$this->bd}"
        ];

    }

    function IngresosPorDia(){
        # Variables

        $__row = [];

        $days = array(2 => 'Lunes', 3 => 'Martes', 4 => 'Miercoles', 5 => 'Jueves', 6 => 'Viernes', 7 => 'Sabado', 1 => 'Domingo');

        foreach ($days as $noDias => $Days):

            $lsDays = $this->getIngresosDayOfWeek([$_POST['UDN'],$_POST['Anio'],$_POST['Mes'],$noDias]);


            foreach ($lsDays as $key ) {

                if($_POST['UDN'] == 1):
                
                
                        $__row[] = [
                            
                            'id'               => $noDias,
                            'fecha'            => $key['fecha'],
                            'Dia de la semana' => $Days,
                            'Hospedaje'        => evaluar($key['Hospedaje']),
                            'AyB'              => evaluar($key['AyB']),
                            'Diversos'         => evaluar($key['Diversos']),
                            'No habitaciones'  => $key['noHabitaciones'],
                            'Total'            => evaluar($key['total']),
                        
                            'opc' => 0
                        ];
                
                elseif($_POST['UDN'] == 5):

                    $__row[] = [
                            
                            'id'               => $noDias,
                            'fecha'            => $key['fecha'],
                            'Dia de la semana' => $Days,
                            'alimentos'        => evaluar($key['alimentos']),
                            'bebidas'          => evaluar($key['bebidas']),
                            'complementos'     => evaluar($key['complementos']),
                            // 'AyB'              => evaluar($key['AyB']),
                            // 'Diversos'         => evaluar($key['Diversos']),
                            // 'No habitaciones'  => $key['noHabitaciones'],
                            'Total'            => evaluar( $key['total']),
                        
                            'opc' => 0
                        ];
                
                else: 
                        
                         $__row[] = [
                            
                            'id'               => $noDias,
                            'fecha'            => $key['fecha'],
                            'Dia de la semana' => $Days,
                            'alimentos'        => $key['alimentos'],
                            'bebidas'          => $key['bebidas'],
                            'No habitaciones'  => $key['noHabitaciones'],
                            'total'            => evaluar($key['totalGral']),
                            'opc'              => 0
                         ];

                endif;
                
            }

            $__row[] = [

                'id' => '',
                'fecha' => '',
                'colgroup' => true,               
            ];
        
        
        endforeach;  



     

        return [
            "thead"    => $this-> get_th_ingresos(),
            "row"      => $__row,
            "frm_head" => ''
        ];
    }

    function get_th_ingresos(){

        switch ($_POST['UDN']) {
            case 1:
                return ['Fecha','Dia','Hospedaje','AYB','DIVERSOS','No. Habitaciones','TOTAL'] ;
            case 5:
                return ['Fecha','Dia','alimentos','bebidas','complementos','TOTAL'];
                
            default:
                return ['Fecha','Dia','alimentos','bebidas','Clientes','TOTAL'];
        }

    }

    function setVentas(){
        $UDN = 1;

        $isCreated = $this-> getFolio([ $_POST['fecha'], $_POST['UDN']]);

        $values = [ 
             $_POST['name'] => $_POST[$_POST['name']],
             'id_venta'     => $_POST['id_venta']
        ];

        

        $data = $this->util->sql($values, 1);
        $ok   = $this->updateVentas($data);

        return [
            'ok' => $ok,
            'data' => $data,
            'isCreated' => $isCreated
        ];
    }


    function createdGroups($groups,$ventas,$id){
        $row = [];

        foreach ($groups as $key => $nameGroup) {

          $value =  evaluar($ventas[$nameGroup] ?? '', ''); 

          if($key == 0){ $value =$ventas[$nameGroup];}

           $row[$nameGroup] =  ['html' => 
           createElement('input', [
                'name'    => $nameGroup,
                'value'   =>  $value,
                'onkeyup' => "ingresosDiarios.setVentas(event, $id)",
            ]),
            'style' => 'padding:0; margin:0;'];
        }

        return $row;
    }

}


// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);

// Complementos.



/**
 * Crea un elemento HTML dinámicamente.
 *
 * @param string $tag La etiqueta del elemento (ej. 'input', 'div').
 * @param array $attributes Un array asociativo con los atributos del elemento (ej. ['placeholder' => '0.00', 'class' => 'form-control']).
 * @param string|null $text El texto interno del elemento (para elementos como 'div', 'span').
 * @return string El elemento HTML creado.
 */
function createElement($tag, $attributes = [], $text = null){

    $defaultAttributes = [
        'placeholder' => '',
        // 'class'       => 'form-control input-xs text-end text-primary ',
        'class'       => '
        w-full bg-gray-50  
        text-slate-700 text-end text-sm  px-3 py-2
        focus:border-gray-400
        hover:border-slate-300 hover:bg-gray-100
         
        ',
        // 'style'       => 'font-size:1rem;'
    ];

    $attributes = array_merge($defaultAttributes, $attributes);

    $element = "<$tag";

    foreach ($attributes as $key => $value) {
        $element .= " $key=\"" . htmlspecialchars($value) . "\"";
    }

    $element .= ">";

    if ($text !== null) {
        $element .= htmlspecialchars($text);
    }

    // Añadir la etiqueta de cierre si el elemento no es auto-cerrado
    if (!in_array($tag, ['input', 'img', 'br', 'hr', 'meta', 'link'])) {
        $element .= "</$tag>";
    }

    return $element;
}

