
ctrl = '../layout/ctrl/ctrl-sidebar.php';

let noti;
let toast;

$(function () {
 
    toast = new Toast(ctrl);
    toast.Container = '.offcanvas-body';
    // setInterval(function () {
        
        // toast.getNotifications();
       
    // }, 60000);

 
});


class Toast {
    #_containterToast;
    constructor(link) {
        this.#_containterToast = null;
        this._link = link;
    }

    set Container(container) {
        this.#_containterToast = container;
    }
    
    get Container() {
        return $(this.#_containterToast);
    }


    getNotifications() {

        fn_Ajax( {
                
                data: { 'opc': 'Notificaciones',},

                methods: {
                    request: (data) => {
                        this.Notifications(data);
                        toast.updateNotificationCount(); 
                        
                        // Manejador de evento para botones de cierre
                        $('.toast .btn-close').off('click').click(function (e) {
                            e.stopPropagation();
                            toast.deleteNotification($(this));
                        });

                        $('.toast-body').off('click').click(function () {
                            window.location.href = 'https://www.erp-varoch.com/ERP2/calendarizacion/actividades.php';
                         });
                    }
                }
        
        });


    }

    getCountNotifications(){

    }

    // Eventos.
    closeToast(toastElement) {
        toastElement.removeClass('show d-block').toast('hide').queue(function () {
            let parentContainer = toastElement.closest('.notification-group');
            toastElement.remove();
            if (parentContainer.find('.toast').length === 0) {
                parentContainer.prev('hr').remove();
                parentContainer.prev('h6').remove();
                parentContainer.remove();
            }
            toast.updateNotificationCount();
            toast.offcanvasEmpty();
        });
    }

    deleteNotification(toastElement) {

        const close = toastElement.closest('.toast');

        // toast.closeToast($(this).closest('.toast'));


        fn_Ajax({

            data: {
                'opc': 'deleteNotification',
                'id': toastElement.data('id')
            },

            methods: {
                request: (data) => {
                    if (data) {
                        this.closeToast(close);

                    }
                }
            }


        });




    }
   
    // Complementos adicionales.

    Notifications(options) {
        let defauls = [
            {
                title: 'Notificacion',
                notification: [
                    {
                        color: '#007aff',
                        title: 'Event 1',
                        time: '10:50 hrs',
                        description: '<i class="icon-ok"></i> DescriptiÃ³n Event',
                    },
                    {
                        color: '#007aff',
                        title: 'Event 2',
                        time: '10:50 hrs',
                        description: 'Hello, world! This is a toast message.',
                    }]
            }
        
        ];

     
       
        const opts = Object.assign(defauls,options);
        
        if(options)
        options.forEach(noti => {
            // this.createNotification(noti.title);
            // this.createToast(noti.notification);
            let notificationGroup = $('<div>', { class: 'notification-group' });
            this.createNotification(noti.title, notificationGroup);
            this.createToast(noti.notification, notificationGroup);
            this.Container.append(notificationGroup);
        });


    }

    createNotification(title, container) {
        const hr = $('<hr>', { class: 'mt-3 mb-1' });
        const h6 = $('<h6>', { class: 'fw-bold mb-2', text: title });
        // this.Container.append(hr, h6);
        container.append(hr, h6);
    }
 
    createToast(options, container) {
        const defaults = [{
            color: '#007aff',
            title: 'Evento',
            time: '10:50 hrs',
            description: '<i class="icon-ok"></i> DescripciÃ³n del evento',
        }];

        let opts = Object.assign(defaults, options);

        opts.forEach(t => {
            t.color = t.color || '#007aff';
            const toast = $('<div>', { class: 'toast d-block  mb-1', role: 'alert', style: 'min-width: 100%;', 'aria-live': 'assertive', 'aria-atomic': 'true' });
            const header = $('<div>', { class: 'toast-header col-12' });
            const svg = $(this.createSVG(t.color));
            const title = $('<strong>', { class: 'me-auto', text: t.title });
            const time = $('<small>', { class: 'text-body-secondary', text: t.time });
            const close = $('<button>', { type: 'button', class: 'btn-close', 'aria-label': 'Close' });
            const body = $('<div>', { class: 'toast-body pointer col-12', html: t.description ,title:'Ir al calendario'});

            header.append(svg, title, time, close);
            toast.append(header, body);
            container.html(toast);
            // this.Container.append(toast);
        });
    }

    createSVG(color) {
        // Crear el SVG
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute("class", "bd-placeholder-img rounded me-2");
        svg.setAttribute("width", "20");
        svg.setAttribute("height", "20");
        svg.setAttribute("xmlns", "http://www.w3.org/2000/svg");
        svg.setAttribute("aria-hidden", "true");
        svg.setAttribute("preserveAspectRatio", "xMidYMid slice");
        svg.setAttribute("focusable", "false");

        // Crear el rectÃ¡ngulo
        const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute("width", "100%");
        rect.setAttribute("height", "100%");
        rect.setAttribute("fill", color);
        // AÃ±adir el rectÃ¡ngulo al SVG
        svg.appendChild(rect);
        return svg;
    }

    updateNotificationCount() {
        let notificationCount = $('.toast').length;

    
    
        $('#notifications .notification-count').text(notificationCount);
    
        if (notificationCount === 0) {
            $('#notifications .notification-count').text('');
            $('#notifications .notification-count').css('background-color', 'transparent');
        } else {
            $('#notifications .notification-count').css('background-color', 'red');
        }
    }

    offcanvasEmpty() {
        if ($('.offcanvas-body').is(':empty')) {
            $('.offcanvas-body').append('<p class="text-center mt-5">No hay notificaciones</p>');
        } else {
            $('.offcanvas-body p').remove();
        }
    }

    updateEmptyContainers() {
        $('.notification-group').each(function () {
            if ($(this).find('.toast').length === 0) {
                $(this).prev('hr').remove();
                $(this).prev('h6').remove();
                $(this).remove();
            }
        });
        updateNotificationCount();
        offcanvasEmpty();
    }

  

 
}


function fn_Ajax(options) {

    let defaults = {
        url: ctrl,

        idFilterBar: 'filter',

        data: {
            tipo: 'text',
            opc: 'frm-data',
        },

        methods: ''




    };

    const settings = ObjectMerge(defaults, options);

    // console.warn(settings.data);


    $("#" + settings.idFilterBar).validar_contenedor(settings.data, (datos) => {

        fn_ajax(datos, settings.url).then((data) => {


            if (settings.methods) {
                // Obtener las llaves de los metodos
                let methodKeys = Object.keys(settings.methods);
                methodKeys.forEach((key) => {
                    const method = settings.methods[key];
                    method(data);
                });

            }




        });

    });
}

function ObjectMerge(target, source) {
    // Iterar sobre todas las claves del objeto fuente
    for (const key in source) {
        // Verificar si la propiedad es propia del objeto fuente
        if (source.hasOwnProperty(key)) {
            // Verificar si el valor es un objeto y si el target tiene la misma propiedad
            if (typeof source[key] === 'object' && source[key] !== null) {
                // Si el target no tiene la propiedad o no es un objeto, inicializarla como un objeto vacio
                if (!target[key] || typeof target[key] !== 'object') {
                    target[key] = {};
                }
                // Llamada recursiva para combinar sub-objetos
                this.ObjectMerge(target[key], source[key]);
            } else {
                // Si no es un objeto, asignar el valor directamente
                target[key] = source[key];
            }
        }
    }
    return target;
}
