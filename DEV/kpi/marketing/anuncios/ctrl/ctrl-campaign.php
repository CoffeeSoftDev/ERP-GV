<?php
if (empty($_POST['opc'])) exit(0);
require_once '../mdl/mdl-campaign.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn'             => $this->lsUDN(),
            'red_social'      => $this->lsRedSocial(),
            'tipo_anuncio'    => $this->lsTypes(),
            'clasificacion'   => $this->lsClassifications()
        ];
    }

    // Campaign Methods

    function lsCampaigns() {
        $__row = [];
        $active = $_POST['active'] ?? 1;
        $udn_id = $_POST['udn_id'] ;

        $ls = $this->listCampaigns([$active, $udn_id]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-eye"></i>',
                    'onclick' => 'campaign.viewAnnouncements(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-success me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'campaign.editCampaign(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'campaign.statusCampaign(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'campaign.statusCampaign(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'              => $key['id'],
                'Nombre'          => $key['nombre'],
                'Estrategia'      => $key['estrategia'],
                'Red Social'      => $key['red_social'],
                'Fecha Creación'  => $key['fecha_creacion'],
                'Estado'          => renderStatus($key['active']),
                'a'               => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getCampaign() {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            return ['status' => 400, 'message' => 'Falta ID de campaña'];
        }

        $campaña  = $this->getCampaignById([$id]);
        $ads = $this->getAnnouncementsByCampaign([$id]);

        $anuncios = [];
        foreach ($ads as $ad) {
            $anuncios[] = [
                'id'             => $ad['id'],
                'nombre'         => $ad['nombre'],
                'fecha_inicio'   => date('Y-m-d', strtotime($ad['fecha_inicio'])),
                'fecha_fin'      => date('Y-m-d', strtotime($ad['fecha_fin'])),
                'imagen'         => $ad['imagen'] ? "https://www.erp-varoch.com/ERP24/" . ltrim($ad['imagen']) : null,
                'tipo_id'        => $ad['tipo_id'],
                'clasificacion_id'=> $ad['clasificacion_id'],
                'total_monto'    => $ad['total_monto'],
                'total_clics'    => $ad['total_clics']
            ];
        }

        return [
            'status' => 200,
            'data'   => [
                'campaña'  => $campaña,
                'anuncios' => $anuncios
            ]
        ];
    }


    function addCampaign() {
        $status = 500;
        $message = 'No se pudo crear la campaña';

        $lastId = $this->getLastCampaignId();
        $_POST['nombre'] = 'Campaña ' . ($lastId + 1);
        $_POST['fecha_creacion'] = date('Y-m-d H:i:s');
        // $_POST['active'] = 1;
        $_POST['udn_id'] = 4;

        $create = $this->createCampaign($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Campaña creada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'      => ['id' => $lastId + 1]
        ];
    }

    function editCampaign() {
        $status = 500;
        $message = 'Error al editar campaña';

        $edit = $this->updateCampaign($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Campaña editada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function statusCampaign() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateCampaign($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'Estado actualizado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    // Announcement Methods

    function lsAnnouncements() {
        $__row = [];
        $campaña_id    = $_POST['campaña_id'] ?? null;
        $udn_id        = $_POST['udn_id'] ?? null;
        $red_social_id = $_POST['red_social_id'] ?? null;

        $values = [$campaña_id, $udn_id, $red_social_id];

        // 🧩 Obtener lista filtrada
        $ls = $this->listAnnouncements($campaña_id, $udn_id, $red_social_id);

        foreach ($ls as $key) {
            $a = []; // 🔹 Reiniciar acciones por fila

            // 🎨 Color del ícono según red social
                $colorIcon = '#9CA3AF'; // gris por defecto

                switch ((int)($key['red_social_id'] ?? 0)) {
                    case 1:
                        $colorIcon = '#1877F2'; // Facebook
                        $bgColor   = 'bg-blue-100';
                        break;
                    case 2:
                        $colorIcon = '#F527BE'; // Instagram
                        $bgColor   = 'bg-pink-100';
                        break;
                    case 3:
                        $colorIcon = '#010101'; // TikTok
                        $bgColor   = 'bg-gray-300';
                        break;
                }

            // 🖼️ Imagen o ícono por defecto
        $imageHtml = !empty($key['imagen']) 
            ? '<img src="https://www.erp-varoch.com/ERP24/' . ltrim($key['imagen'], '/') . '" class="w-16 h-16 rounded-lg object-cover shadow-md mx-auto my-1" />'
            : '<div class="w-16 h-16 ' . $bgColor . ' rounded-lg flex items-center justify-center mx-auto my-1">
                   <i class="icon-heart-1 text-lg" style="color:' . $colorIcon . ';"></i>
               </div>';

            // 🎨 Badges
            $clasificacionBadge = renderBadge($key['clasificacion_nombre'], 'gray');
            $tipoBadge          = renderBadge($key['tipo_nombre'], 'blue');

            // 📅 Validación fechas
            $mostrarBoton = false;
            $hoy = new DateTime();
            $fechaResultado = !empty($key['fecha_resultado']) ? new DateTime($key['fecha_resultado']) : null;

            if (empty($key['total_monto'])) {
                $mostrarBoton = true;
            } elseif ($fechaResultado) {
                $diff = (int)$fechaResultado->diff($hoy)->format('%r%a');
                if ($diff >= 0 && $diff <= 2) $mostrarBoton = true;
            }

            // 🧩 Acciones
            $tieneEditar = false;
            $tieneResultados = false;

            if ($mostrarBoton) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-success me-1',
                    'html'    => '<i class="icon-chart-bar"></i>',
                    'onclick' => 'campaign.captureResults(' . $key['id'] . ')'
                ];
                $tieneResultados = true;
            }

            if (empty($key['total_monto'])) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'campaign.editCampaign(' . $key['campaña_id'] . ')'
                ];
                $tieneEditar = true;
            }

            if (!($tieneEditar && $tieneResultados)) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-info',
                    'html'    => '<i class="icon-eye"></i>',
                    'onclick' => 'campaign.viewCampaign(' . $key['campaña_id'] . ')'
                ];
            }

            // 🧱 Construcción de fila
            $__row[] = [
                'id'             => $key['id'],
                'Imagen'         => ['html' => $imageHtml, 'class' => 'text-center align-middle'],
                'Campaña'        => $key['campaña_nombre'],
                'Anuncio'        => $key['anuncio_nombre'],
                'Clasificación'  => ['html' => $clasificacionBadge, 'class' => 'text-center'],
                'Tipo'           => ['html' => $tipoBadge, 'class' => 'text-center'],
                'Fecha Inicio'   => $key['fecha_inicio'],
                'Fecha Final'    => $key['fecha_fin'],
                'a'              => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls,
            $values
        ];
    }





    function getAnnouncement() {
        $status = 500;
        $message = 'Error al obtener los datos';
        $getAnnouncement = $this->getAnnouncementById([$_POST['id']]);

        if ($getAnnouncement) {
            $status = 200;
            $message = 'Datos obtenidos correctamente.';
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $getAnnouncement
        ];
    }

    function addAnnouncement() {
        $status  = 500;
        $message = 'No se pudo crear el anuncio';
        $fileUrl = null;

        // 🧩 Validaciones básicas
        if (empty($_POST['nombre']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
            return [
                'status'  => 400,
                'message' => 'Campos obligatorios faltantes'
            ];
        }

        if (strtotime($_POST['fecha_fin']) < strtotime($_POST['fecha_inicio'])) {
            return [
                'status'  => 400,
                'message' => 'La fecha fin debe ser mayor a la fecha inicio'
            ];
        }

        // 🖼️ Cargar imagen si existe
        if (!empty($_FILES['image']['name'])) {
            $carpeta = "../../../../erp_files/marketing/anuncios/";

            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $tmp_name = $_FILES['image']['tmp_name'];
            $nombre   = $_FILES['image']['name'];
            $ext      = pathinfo($nombre, PATHINFO_EXTENSION);
            $nuevo    = uniqid("anuncio_") . "." . strtolower($ext);
            $destino  = $carpeta . $nuevo;

            if (move_uploaded_file($tmp_name, $destino)) {
                $fileUrl = "erp_files/marketing/anuncios/" . $nuevo;
            }
        }

        // 📦 Preparar datos para insertar
        $values = [
            'nombre'          => $_POST['nombre'],
            'fecha_inicio'    => $_POST['fecha_inicio'],
            'fecha_fin'       => $_POST['fecha_fin'],
            'imagen'          => $fileUrl,
            'campaña_id'      => $_POST['campaña_id'] ?? null,
            'tipo_id'         => $_POST['tipo_id'] ?? null,
            'clasificacion_id'=> $_POST['clasificacion_id'] ?? null
        ];

        $create = $this->createAnnouncement($this->util->sql($values));
        $idAd = null;
        if ($create) {
            $status  = 200;
            $message = '✅ Anuncio creado correctamente';
            $idAd = $this->maxAnnouncement();

            // Actualizar el active de campaña a 1
            $dataCampaign = [
                'active' => 1,
                'id'     => $_POST['campaña_id']
            ];
            $this->updateCampaign($this->util->sql($dataCampaign, 1));
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => [
                'id'    => $idAd,
                'image' => $fileUrl
            ]
        ];
    }

    function editAnnouncement() {
        $status  = 500;
        $message = 'No se pudo actualizar el anuncio';
        $fileUrl = null;

        if (empty($_POST['id'])) {
            return [
                'status'  => 400,
                'message' => 'Falta el ID del anuncio'
            ];
        }

        if (empty($_POST['nombre']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
            return [
                'status'  => 400,
                'message' => 'Campos obligatorios faltantes'
            ];
        }

        if (strtotime($_POST['fecha_fin']) < strtotime($_POST['fecha_inicio'])) {
            return [
                'status'  => 400,
                'message' => 'La fecha fin debe ser mayor a la fecha inicio'
            ];
        }

        // 📸 Si se sube una nueva imagen
        if (!empty($_FILES['image']['name'])) {
            $carpeta = "../../../../erp_files/marketing/anuncios/";

            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $tmp_name = $_FILES['image']['tmp_name'];
            $nombre   = $_FILES['image']['name'];
            $ext      = pathinfo($nombre, PATHINFO_EXTENSION);
            $nuevo    = uniqid("anuncio_") . "." . strtolower($ext);
            $destino  = $carpeta . $nuevo;

            if (move_uploaded_file($tmp_name, $destino)) {
                $fileUrl = "erp_files/marketing/anuncios/" . $nuevo;
            }
        }

        // 🔹 Preparar datos para actualizar
        $values = [
            'nombre'           => $_POST['nombre'],
            'fecha_inicio'     => $_POST['fecha_inicio'],
            'fecha_fin'        => $_POST['fecha_fin'],
            'campaña_id'       => $_POST['campaña_id'] ?? null,
            'tipo_id'          => $_POST['tipo_id'] ?? null,
            'clasificacion_id' => $_POST['clasificacion_id'] ?? null,
        ];

        // ✅ Solo agrega el campo imagen si hay una nueva
        if (!empty($fileUrl)) {
            $values['imagen'] = $fileUrl;
        }

        // Agregar a values el id
        $values['id'] = $_POST['id'];

        $update = $this->updateAnnouncement($this->util->sql($values, 1));

        if ($update) {
            $status  = 200;
            $message = '✅ Anuncio actualizado correctamente';
        }

        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/ERP24/';

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => [
                'id'    => $_POST['id'],
                'image' => !empty($fileUrl) ? $baseUrl . ltrim($fileUrl) : null
            ]
        ];
    }

    function removeAnnouncement() {
        $status  = 500;
        $message = 'No se pudo eliminar el anuncio';
        $fileUrl = null;

        // 🧩 Validar ID obligatorio
        if (empty($_POST['id'])) {
            return [
                'status'  => 400,
                'message' => 'Falta el ID del anuncio'
            ];
        }

        // 📦 Obtener anuncio antes de eliminar (para borrar la imagen física)
        $ad = $this->getAnnouncementById([$_POST['id']]);

        if (empty($ad)) {
            return [
                'status'  => 404,
                'message' => 'El anuncio no existe'
            ];
        }

        $fileUrl = $ad['imagen'] ?? null;
        $values = [
            'id'  => $_POST['id']
        ];
        // 🗑️ Eliminar registro
        $delete = $this->deleteAnnouncement($this->util->sql($values, 1));

        if ($delete) {
            // 🧹 Si hay imagen, eliminar archivo físico
            if (!empty($fileUrl)) {
                $path = "../../../../" . ltrim($fileUrl, '/');
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $status  = 200;
            $message = '🗑️ Anuncio eliminado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function captureResults() {
        $status = 500;
        $message = 'Error al capturar resultados';

        if ($_POST['total_monto'] <= 0 || $_POST['total_clics'] <= 0) {
            return [
                'status'  => 400,
                'message' => 'El monto y los clics deben ser mayores a 0'
            ];
        }

        $update = $this->updateAnnouncement($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'Resultados capturados correctamente';

            // Desactivar campaña si todos los anuncios tienen resultados
            $ad = $this->getAnnouncementById([$_POST['id']]);
            $campaña_id = $ad['campaña_id'] ?? null;
            if ($campaña_id) {
                $adsWithoutResults = $this->countAnnouncementsWithoutResults([$campaña_id]);
                if ($adsWithoutResults == 0) {
                    $dataCampaign = [
                        'active' => '0',
                        'id'     => $campaña_id
                    ];
                    $this->updateCampaign($this->util->sql($dataCampaign, 1));
                }
            }
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }
}

// Complements



// function evaluar($value) {
//     return '$' . number_format($value, 2, '.', ',');
// }
function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Activo
                    </span>';
        case 0:
            return '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-700">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Inactivo
                    </span>';
        default:
            return '<span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-700">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        Desconocido
                    </span>';
    }
}

function renderBadge($text, $color = 'green') {
    $colors = [
        'green' => 'bg-gray-200 text-green-800',
        'blue' => 'bg-gray-200 text-blue-900',
        // 'purple' => 'bg-gray-200 text-purple-800',
        // 'gray' => 'bg-gray-200 text-gray-800',
    ];
    
    $colorClass = $colors[$color] ?? $colors['green'];
    
    return '<span class="px-3 py-1 rounded-full text-xs font-semibold ' . $colorClass . '">' . $text . '</span>';
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
