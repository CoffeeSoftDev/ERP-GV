<?php 
class Destajo {
    // INSTANCIAS
    private $c;
    private $obj;
    private $util;
    private $bd_ch;

    public function __construct($ch) {
        $this->c     = $ch;
        $this->obj   = $this->c->obj;
        $this->util  = $this->c->util;
        $this->bd_ch = $this->obj->getCH();
    }

    public function destajoAnual(){
        $date1  = $this->c->getVar('date1');
        $date2  = $this->c->getVar('date2');
        $result = [];
        $meses  = $this->util->intervalDates($date1,$date2);
        $area   = [
            [
                'id'            => 1,
                'area'          => 'FRANCES',
                'colaboradores' => [],
            ],
            [
                'id'            => 2,
                'area'          => 'PASTELERÍA',
                'colaboradores' => [],
            ],
            [
                'id'            => 4,
                'area'          => 'BIZCOCHO',
                'colaboradores' => [],
            ],
        ];
        $sql    = $this->obj->lsDestajoColabordaores([$date1,$date2]);
        foreach ($area as &$a) { // Usamos &$a para modificar directamente el array $area
            $promedio  = 0;
            $destajo   = 0;
            $bono      = 0;
            $fonacot   = 0;
            $infonavit = 0;
            $perdida   = 0;
            $prestamo  = 0;
            $total     = 0;
            foreach ($sql as &$c) {
                if ($a['id'] == $c['ida']) {
                    $c['total'] = $c['destajo'] + $c['bono'] - $c['fonacot'] - $c['infonavit'] - $c['perdida'] - $c['prestamo'];
                    
                    
                    foreach ($meses['my'] as $mes) {
                        $m   = explode('-',$mes);
                        $mes = $this->util->lgMonth(intval($m[0]));
                        $p   = $this->obj->lsDestajoMensual([$c['id'],intval($m[0]),intval($m[1])]);

                        $p['mes']     = $mes;
                        $c['meses'][] = $p;
                        
                    }

                    $a['colaboradores'][]  = $c;

                    $promedio  += $c['promedio'];
                    $destajo   += $c['destajo'];
                    $bono      += $c['bono'];
                    $fonacot   += $c['fonacot'];
                    $infonavit += $c['infonavit'];
                    $perdida   += $c['perdida'];
                    $prestamo  += $c['prestamo'];
                    $total     += $c['total'];
                }
            }

            $a['promedio']  = $promedio;
            $a['destajo']   = $destajo;
            $a['bono']      = $bono;
            $a['fonacot']   = $fonacot;
            $a['infonavit'] = $infonavit;
            $a['perdida']   = $perdida;
            $a['prestamo']  = $prestamo;
            $a['total']     = $total;
        }

        return $area;
    }
}
?>