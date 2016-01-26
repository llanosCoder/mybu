/*global $, FormData, mostrar_notificacion, alertify, aRut, currencyFormat, sanear_numero, isNumber */
/*jslint plusplus: true */

var cupoIlimitado = 0;

function encontrarCliente(lista, rut) {
    'use strict';
    var i;
    for (i = 0; i < lista.length; i++) {
        if (lista[i].rut === rut) {
            return i;
        }
    }
}

function ocultarDivs() {
    'use strict';
    $('#form_wrap_cliente').hide();
    $('#tabla_wrap_cliente').hide();
    $('#form_wrap_credito').hide();
    $('#wrap_tabla_clientes').hide();
    $('#wrap_tabla_planes').hide();
    $('#wrap_tabla_planes_pagos').hide();
    $('#wrap_formulario_pago').hide();
    $('#wrap_formulario_reportes').hide();
    $('#wrap_formulario_cartola').hide();
}

$('.md-overlay').on('click', reducirModal);

function reducirModal(){
    $('#form-comprar').addClass('no-scroll');
}

function cargarSelect(id) {
    'use strict';
    var url = 'classes/administrar_clientes.php',
        html = '<option value="0">Seleccione un cliente</option>';
    $.post(url,
        {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.resultado, function (i, datos) {
                html += '<option value="' + datos.rut + '">' + datos.rut.substr(0, datos.rut.length - 1) + '-' + datos.rut.substr(datos.rut.length - 1, datos.rut.length) + ' || ' + datos.nombre + ' ' + datos.apaterno + ' ' + datos.amaterno + '</option>';
            });
            $(id).html(html);
        }).done(
        function () {
        }
    );
}

function obtenerLocalidades(accion) {
    'use strict';
    var url = 'classes/obtener_localidades.php',
        localidades,
        selectLocalidades = '<option value="0">Seleccione una opción</option>',
        opcion,
        selectDestino;
    switch (accion) {
    case 1:
        selectDestino = 'pais';
        break;
    case 2:
        opcion = $('#pais').val();
        selectDestino = 'region';
        break;
    case 3:
        opcion = $('#region').val();
        selectDestino = 'comuna';
        break;
    }
    
    $.post(url,
        {
            accion: accion,
            opcion: opcion
        },
        function (data) {
            data = $.parseJSON(data);
            localidades = data.resultado;
        }).done(
        function () {
            $.each(localidades, function (i, localidad) {
                selectLocalidades += '<option value="' + localidad.id + '">' + localidad.nombre + '</option>';
            });
            $('#' + selectDestino).html(selectLocalidades);
        }
    );
}

function guardarCliente() {
    'use strict';
    var hayerror = false,
        url = 'classes/administrar_clientes.php';
    $("#formcliente").find(':input').each(function () {
        var elemento = this;

        if ($("#" + elemento.id).parent().hasClass('has-error')) {

            hayerror = true;
        }
    });
    
    if (!hayerror) {

        $.post(url,
            {
                accion: 1,
                rut: $('#rut').val(),
                nombre: $('#nombre').val(),
                apaterno: $('#apaterno').val(),
                amaterno: $('#amaterno').val(),
                direccion: $('#direccion').val(),
                pais: $('#pais').val(),
                region: $('#region').val(),
                comuna: $('#comuna').val(),
                email: $('#email').val(),
                telefono: $('#telefono').val(),
                f_nacimiento: $('#f_nacimiento').val().split("-").reverse().join("-"),
                monto_autorizado: $('#monto_autorizado').val(),
                cupo_ilimitado: cupoIlimitado
            },
            function (data) {
                if (data !== '[]') {
                    data = $.parseJSON(data);
                    data = data.resultado;
                    switch (data[0].resultado) {
                    case 0:
                        mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                        break;
                    case 1:
                        mostrar_notificacion('Éxito', 'Usuario creado exitosamente', 'success');
                        break;
                    case 2:
                        mostrar_notificacion('Error', 'No tiene permisos para realizar esta acción', 'danger');
                        break;
                    case 3:
                        mostrar_notificacion('Error', 'RUT ingresado ya existe', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;
                    }
                }
            }).done(
            function () {
            }
        );
    }
}

function cargarHistorialCliente(rut) {
    'use strict';
    var url = 'classes/generar_historial_cliente.php',
        valor;
    $.post(url,
        {
            accion: 1,
            rut: rut
        },
        function (data) {
            window.open(data, '_blank');
        }).done(
        function () {
        }
    );
    
}

function cargarClientes() {
    'use strict';
    ocultarDivs();
    $('#tabla_wrap_cliente').show('slow');
    $('#editar_cupo').numeric();
    var url = 'classes/administrar_clientes.php',
        tablaClientes = '<table class="table table-bordered responsive" id="tabla_clientes"><thead><tr><th>RUT</th><th>Nombre</th>',
        detalleClientes = [],
        estadoCuentaGenerado = '',
        estadoCuenta = '',
        correoDestinatario = '';
    //tablaClientes += '<th>Saldo total crédito</th><th>Saldo disponible</th><th>Fecha creación</th><th>Cambiar cupo</th><th>Ver Detalle</th><th>Generar estado de cuenta</th><th>Mail estado de cuenta</th><th>Historial cliente</th></thead>';
    tablaClientes += '<th>Saldo total crédito</th><th>Saldo disponible</th><th>Fecha creación</th><th>Cambiar cupo</th><th>Cupo ilimitado</th><th>Ver Detalle</th></thead>';
    tablaClientes += '<tbody>';
    $.post(url,
        {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.resultado, function (i, datos) {
                detalleClientes.push(datos);
                tablaClientes += '<tr>';
                tablaClientes += '<td>' + aRut(datos.rut) + '</td>';
                tablaClientes += '<td>' + datos.apaterno + ' ' + datos.amaterno + ' ' + datos.nombre + '</td>';
                if (datos.monto_autorizado !== null) {
                    if (datos.pc_nombre === 'PLAN ILIMITADO') {
                        tablaClientes += '<td>Ilimitado</td>';
                        tablaClientes += '<td>Ilimitado</td>';
                    } else {
                        tablaClientes += '<td>' + currencyFormat(datos.monto_autorizado, '$') + '</td>';
                        tablaClientes += '<td>' + currencyFormat(datos.cupo, '$') + '</td>';
                    }
                } else {
                    tablaClientes += '<td>Sin línea de crédito</td>';
                    tablaClientes += '<td>Sin línea de crédito</td>';
                }
                var fecha = datos.f_creacion.split("-").reverse().join("-");
                tablaClientes += '<td><span style="display:none;">' + fecha + '</span>' + datos.f_creacion + '</td>';
                
                if (datos.monto_autorizado !== null) {
                    tablaClientes += '<td><a href="javascript:void(0);"><i class="fa fa-edit fa-2x editar_cupo" rut="' + datos.rut + '" data-modal="formulario-editar_cupo"></i></a></td>';
                } else {
                    tablaClientes += '<td>-</td>';
                }
                tablaClientes += '<td><div data-modal="modal_procesando" rut="' + datos.rut + '" class="plan_limitado_ilimitado disabled"  plan="' + datos.pc_nombre + '" rut="' + datos.rut + '"><input type="checkbox" class="toggle_ilimitado" ';
                    if (datos.pc_nombre === 'PLAN ILIMITADO') {
                        tablaClientes += ' checked ';
                    }
                tablaClientes += 'data-toggle="toggle"></div></td>';
                tablaClientes += '<td><a href="javascript:void(0);"><i class="fa fa-list-alt fa-2x detalle_cliente" rut="' + datos.rut + '" data-modal="detalle_cliente"></i></a></td>';
                /*tablaClientes += '<td><a href="javascript:void(0);"><i class="fa fa-file-text-o fa-2x generar_estado" rut="' + datos.rut + '"></a></td>';
                tablaClientes += '<td><a href="javascript:void(0);"><i class="fa fa-envelope-o fa-2x enviar_mail" rut="' + datos.rut + '"></a></td>';
                if (datos.monto_autorizado !== null) {
                    tablaClientes += '<td><a href="javascript:void(0);"><i class="fa fa-eye fa-2x ver_historial" rut="' + datos.rut + '"></i></a></td>';
                } else {
                    tablaClientes += '<td></td>';
                }*/
                tablaClientes += '</tr>';
            });
            
        }).done(
        function () {
            $('#tabla_wrap_cliente').html(tablaClientes);
            $('.detalle_cliente').modalEffects();
            $('.editar_cupo').modalEffects();
            //$('.ver_historial').modalEffects();
            $('#tabla_clientes').dataTable({
                "aaSorting": [[1, 'asc']],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ clientes",
                    "sZeroRecords": "No se han encontrado clientes",
                    "sEmptyTable": "No se han encontrado clientes",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando clientes del 0 al 0 de un total de 0 clientes",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ clientes)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar: ",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
            $('.detalle_cliente').on('click', function cargarDetallesClientes() {
                var html = '',
                    id = $(this).attr('rut'),
                    indice = encontrarCliente(detalleClientes, id);
                html += '<p><b>Datos de contacto: </b></p>';
                html += '<ul>';
                html += '<li>Nombre: ' + detalleClientes[indice].nombre + '</li>';
                html += '<li>Apellidos: ' + detalleClientes[indice].apaterno + ' ' + detalleClientes[indice].amaterno + '</li>';
                html += '<li>Dirección: ' + detalleClientes[indice].direccion + '</li>';
                html += '<li>Comuna: ' + detalleClientes[indice].comuna + '</li>';
                html += '<li>Fecha de nacimiento: ' + detalleClientes[indice].f_nacimiento + '</li>';
                html += '<li>Fecha de creación: ' + detalleClientes[indice].f_creacion + '</li>';
                html += '<li>Teléfono de contacto: ' + detalleClientes[indice].telefono + '</li>';
                html += '<li>Email de contacto: ' + detalleClientes[indice].correo + '</li>';
                html += '</ul>';
                html += '<p><b>Datos de línea de crédito: </b></p>';
                if (detalleClientes[indice].f_facturacion === null && detalleClientes[indice].f_pago === null) {
                    html += '<p>&nbsp;&nbsp;&nbsp;<i>Este usuario aún no cuenta con línea de crédito</i></p>';
                } else {
                    html += '<ul>';
                    html += '<li>Saldo total: ' + currencyFormat(detalleClientes[indice].monto_autorizado, '$') + '</li>';
                    html += '<li>Monto usado: ' + currencyFormat(detalleClientes[indice].monto_autorizado - detalleClientes[indice].cupo, '$') + '</li>';
                    html += '<li>Cupo disponible: ' + currencyFormat(detalleClientes[indice].cupo, '$') + '</li>';
                    html += '<li>Costo de mantención: ' + currencyFormat(detalleClientes[indice].costo_fijo, '$') + '</li>';
                    html += '<li>Costo por uso (mensual): ' + currencyFormat(detalleClientes[indice].uso, '$') + '</li>';
                    html += '<li>Fecha de facturación: ' + detalleClientes[indice].f_facturacion + '</li>';
                    html += '<li>Fecha de pago: ' + detalleClientes[indice].f_pago + '</li>';
                    html += '</ul>';
                }
                $('#detalle_cliente-body').html(html);
            });
            $('.plan_limitado_ilimitado').modalEffects();
            $('.toggle_ilimitado').bootstrapToggle();
            $('.plan_limitado_ilimitado').off('click');
            $('.plan_limitado_ilimitado').on('click', function () {
                var url = 'classes/administrar_clientes.php',
                    estadoActual = $(this).attr('plan'),
                    rut = $(this).attr('rut');
                
                $.post(url,
                    {
                        accion: 7,
                        plan_actual: estadoActual,
                        rut: rut
                    },
                    function (data) {
                    $('#modal_procesando').removeClass('md-show');
                        if (data.resultado === 1) {
                            $(this).attr('plan', data.plan_nuevo);
                            mostrar_notificacion('Éxito', 'Plan cambiado exitosamente', 'success');
                            cargarClientes();
                        } else {
                            mostrar_notificacion('Error', 'No se ha podido actualizar el plan de este cliente', 'danger');
                        }
                    }, 
                    'json').done(function ()
                    {
                    }
                );
            });
            $('.enviar_mail').on('click', function emailEstadoCuenta() {
                var url = 'classes/email_estado_cuenta.php',
                    id = $(this).attr('rut');
                
                if (estadoCuentaGenerado !== id) {
                    mostrar_notificacion('Error', 'Genere el estado de cuenta primero.', 'warning');
                } else {
                    $.post(url,
                        {
                            estado_cuenta: estadoCuenta,
                            mail: correoDestinatario
                        },
                        function (data) {
                            switch (data) {
                            case '1':
                                mostrar_notificacion('Éxito', 'Estado de cuenta enviado.', 'success');
                                break;
                            default:
                                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                                break;
                            }
                        }).done(
                        function () {
                        }
                    );
                }
            });
            $('.generar_estado').on('click', function generarEstadoCuenta() {
                var id = $(this).attr('rut'),
                    url = 'classes/generar_estado_cuenta.php',
                    newwindow;
                $.post(url,
                    {
                        accion: 1,
                        cId: id
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        switch (data.resultado) {
                        case 1:
                            mostrar_notificacion('Éxito', 'Estado de cuenta generado exitosamente.', 'success');
                            estadoCuentaGenerado = id;
                            estadoCuenta = data.estado_cuenta;
                            correoDestinatario = data.mail;
                            var newWindow = window.open("about:blank", '_blank', "width=500,height=500");
                            newWindow.blur();
                            window.focus();
                            newWindow.location.href = 'files/estado_cuentas/' + data.estado_cuenta + '.pdf';
                            break;
                        default:
                            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                            break;
                        }
                    }).done(
                    function () {
                    }
                );
            });
            $('.editar_cupo').on('click', function () {
                var rut = $(this).attr('rut');
                $('#editar_cupo').parent().removeClass('has-error');
                $('#editar_cupo').val('');
                $('#btn_editar_cupo').off('click');
                $('#btn_editar_cupo').on('click', function () {
                    var url = 'classes/administrar_clientes.php',
                        nuevoMonto = $('#editar_cupo').val();
                    if (isNumber(nuevoMonto) && nuevoMonto > 0) {
                        $.post(url,
                            {
                                accion: 6,
                                rut: rut,
                                nuevo_monto: nuevoMonto
                            },
                            function (data) {
                                data = $.parseJSON(data);
                                switch (data.resultado) {
                                case 1:
                                    mostrar_notificacion('Éxito', 'Monto editado exitosamente', 'success');
                                    $('#formulario-editar_cupo').removeClass('md-show');
                                    cargarClientes();
                                    break;
                                case 0:
                                    mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                                    break;
                                default:
                                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                                    break;
                                }
                            }).done(
                            function () {
                            }
                        );
                    } else {
                        $('#editar_cupo').parent().addClass('has-error');
                        mostrar_notificacion('Error', 'Debe ingresar un monto', 'danger');
                    }
                });
            });
            $('.ver_historial').on('click', function () {
                var rut = $(this).attr('rut');
                cargarHistorialCliente(rut);
            });
        }
    );
}

function nuevaLineaCredito() {
    'use strict';
    var url = 'classes/administrar_creditos.php',
        html = '<option value="0">Selecciona una opción</option>';
    $('.form-control').val('');
    $('#select_plan').val(0).trigger('change');
    ocultarDivs();
    $('#form_wrap_credito').show('slow');
    $.post(url,
        {
            accion: 1,
            filtro: 1
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.planes, function (i, datos) {
                html += '<option value="' + datos.codigo + '">' + datos.nombre + '</option>';
            });
            $('#select_plan').html(html);
        }).done(
        function () {
            url = 'classes/administrar_clientes.php';
            html = '<option value="0">Seleccione un cliente</option>';
            $.post(url,
                {
                    accion: 2
                },
                function (data) {
                    data = $.parseJSON(data);
                    $.each(data.resultado, function (i, datos) {
                        html += '<option value="' + datos.rut + '">' + aRut(datos.rut) + '</option>';
                    });
                    $('#cliente').html(html);
                }).done(
                function () {
                }
            );
        }
    );
    
}

function guardarLineaCredito() {
    'use strict';
    var cliente = $('#cliente').val(),
        monto_autorizado = $('#monto_autorizado').val(),
        plan = $('#select_plan').val(),
        f_facturacion = $('input[name=rad1]:checked').val(),
        f_pago = $('input[name=rad2]:checked').val(),
        focusTaken = false,
        url = 'classes/administrar_clientes.php';
    if (f_facturacion === undefined || f_pago === undefined) {
        mostrar_notificacion('Atención', 'Debe seleccionar una fecha', 'warning');
        focusTaken = true;
    }
    if (!focusTaken) {
        $.post(url,
            {
                accion: 3,
                rut: cliente,
                monto_autorizado: monto_autorizado,
                plan: 1, //antes plan: plan
                f_facturacion: f_facturacion,
                f_pago: f_pago
            },
            function (data) {
            
            
                data = $.parseJSON(data);
                $.each(data.resultado, function (i, datos) {
                    switch (datos.resultado) {
                    case 0:
                        mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                        break;
                    case 1:
                        mostrar_notificacion('Éxito', 'Línea de crédito creada exitosamente', 'success');
                        break;
                    case 2:
                        mostrar_notificacion('Error', 'No tiene permisos para realizar esta acción', 'danger');
                        break;
                    case 3:
                        mostrar_notificacion('Error', 'RUT ingresado no existe', 'danger');
                        break;
                    case 4:
                        mostrar_notificacion('Error', 'Cliente ya tiene una cuenta asociada', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;
                    }
                });
                
            }).done(
            function () {
            }
        );
        
    }
}

function cargarPlanes() {
    'use strict';
    ocultarDivs();
    $('#wrap_tabla_clientes').show('slow');
    var url = 'classes/administrar_creditos.php',
        arrayEstados = {},
        html = '<table class="table table-bordered responsive" id="tabla_planes"><thead><tr><th>Nombre</th><th>Costo mantención</th><th>Costo de uso</th><th>Editar</th><th>Estado</tr></thead><tbody>';
    $.post(url,
        {
            accion: 1,
            filtro: 0
        },
        function (data) {
            data = $.parseJSON(data);
            switch (data.resultado) {
            case 0:
                mostrar_notificacion('Error', 'No se han podido encontrar los planes', 'danger');
                break;
            case 1:
                $.each(data.planes, function (i, datos) {
                    arrayEstados[datos.codigo] = datos.estado;
                    html += '<tr>';
                    html += '<td>' + datos.nombre + '</td>';
                    html += '<td>' + currencyFormat(datos.c_mantencion, '$') + '</td>';
                    html += '<td>' + currencyFormat(datos.c_uso, '$') + '</td>';
                    html += '<td><a href="javascript:void(0)"><i class="fa fa-pencil-square fa-2x editar_plan" data-modal="form-crear_plan" onClick="cargarDatosEdicionPlan(\'' + datos.nombre + '\', \'' + datos.codigo + '\', ' + datos.c_mantencion + ', ' + datos.c_uso + ');"></i></a>';
                    html += '<td><div data-modal="modal_procesando" id="' + datos.codigo + '" class="activar_desactivar_plan disabled"><input type="checkbox" class="toggle_plan" ';
                    if (arrayEstados[datos.codigo] === '1') {
                        html += ' checked ';
                    }
                    html += 'data-toggle="toggle"></div></td>';
                    html += '</tr>';
                });
                html += '</tbody></table>';
                $('#wrap_tabla_planes').html(html);
                $('#wrap_tabla_planes').show();
                $('#tabla_planes').DataTable({
                    "aaSorting": [[0, 'asc']],
                    "oLanguage": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ planes",
                        "sZeroRecords": "No se han encontrado planes",
                        "sEmptyTable": "No se han encontrado planes",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando clientes del 0 al 0 de un total de 0 planes",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ planes)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar: ",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                });
                $('.toggle_plan').bootstrapToggle();
                $('.editar_plan').modalEffects();
                $('.activar_desactivar_plan').modalEffects();
                $('.activar_desactivar_plan').on('click', function () {
                    var id = $(this).attr('id'),
                        url = 'classes/administrar_creditos.php',
                        estadoActual;
                    estadoActual = arrayEstados[id];
                    $.post(url,
                        {
                            accion: 4,
                            codigo: id,
                            estado: estadoActual
                        },
                        function (data) {
                            data = $.parseJSON(data);
                            $('#modal_procesando').removeClass('md-show');
                            switch (data) {
                            case 1:
                                cargarPlanes();
                                break;
                            }
                        }).done(
                        function () {
                        }
                    );
                });
                break;
            }
        }).done(
        function () {
        }
    );
}

function crearPlan(codigo) {
    'use strict';
    var datos = {},
        focusTaken = false,
        valor,
        indicePaginas,
        url = 'classes/administrar_creditos.php',
        accion;
    if (codigo === 0) {
        accion = 2;
    } else {
        accion = 3;
    }
    datos.plan_nombre = sanear_numero($('#plan_nombre').val());
    datos.plan_c_mantencion = sanear_numero($('#plan_c_mantencion').val());
    datos.plan_c_uso = sanear_numero($('#plan_c_uso').val());
    for (valor in datos) {
        if (datos.hasOwnProperty(valor)) {
            if (datos[valor] === null || datos[valor] === '') {
                if ($("#" + valor).hasClass('required')) {
                    $("#" + valor).parent().addClass('has-error');
                    if (!focusTaken) {
                        $("#" + valor).focus();
                        focusTaken = true;
                    }
                }
            } else {
                if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor]) === false) {
                    $("#" + valor).parent().addClass('has-error');
                    focusTaken = true;
                } else {
                    if ($("#" + valor).parent().hasClass('has-error')) {
                        $("#" + valor).parent().removeClass('has-error');
                    }
                    if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor])) {
                        $("#" + valor).parent().removeClass('has-error');
                    }
                }
            }
        }
    }
    if (!focusTaken) {
        $.post(url,
            {
                accion: accion,
                nombre: datos.plan_nombre,
                codigo: codigo,
                c_mantencion: datos.plan_c_mantencion,
                c_uso: datos.plan_c_uso
            },
            function (data) {
                data = $.parseJSON(data);
                switch (data.resultado) {
                case 0:
                    mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    break;
                case 1:
                    if (codigo === 0) {
                        mostrar_notificacion('Éxito', 'Plan creado exitosamente', 'success');
                    } else {
                        mostrar_notificacion('Éxito', 'Plan editado exitosamente', 'success');
                    }
                    $('#form-crear_plan').removeClass('md-show');
                    cargarPlanes();
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                    break;
                }
            }).done(
            function () {
            }
        );
        
    }
}

function cargarModalPlan() {
    'use strict';
    $('.form-control').val('');
    $('#btn_crear_plan').html('Crear');
    if ($('.form-group').hasClass('has-error')) {
        $('.form-group').removeClass('has-error');
    }
    $('#btn_crear_plan').off('click');
    $('#btn_crear_plan').on('click', function () {
        crearPlan(0);
    });
}

function cargarDatosEdicionPlan(nombre, codigo, cMantencion, cUso) {
    'use strict';
    $('#btn_crear_plan').html('Editar');
    $('#plan_nombre').val(nombre);
    $('#plan_c_mantencion').val(currencyFormat(cMantencion, ''));
    $('#plan_c_uso').val(currencyFormat(cUso, ''));
    $('#btn_crear_plan').off('click');
    $('#btn_crear_plan').on('click', function () {
        crearPlan(codigo);
    });
}

function cargarPlanesPago() {
    'use strict';
    var url = 'classes/administrar_planes_pagos.php',
        html = '<table class="table responsive table-bordered" id="tabla_planes_pago"><thead><tr><th>Nombre</th><th>Cuotas</th><th>Interés (%)</th><th>Para habilitados</th><th>Editar</th><th>Eliminar</th></tr></thead><tbody>';
    ocultarDivs();
    $('#wrap_tabla_planes_pagos').show('slow');
    $.post(url,
        {
            accion: 1
        },
        function (data) {
            data = $.parseJSON(data);
            switch (data.resultado) {
            case 1:
                $.each(data.planes, function (i, datos) {
                    html += '<tr>';
                    html += '<td>' + datos.nombre + '</td>';
                    html += '<td>' + datos.cuotas + '</td>';
                    html += '<td>' + datos.interes + '</td>';
                    if (datos.habilitado === '1') {
                        html += '<td>Sí</td>';
                    } else {
                        html += '<td>No</td>';
                    }
                    html += '<td><a href="javascript:void(0);"><i class="fa fa-pencil-square fa-2x editar_plan_pago" data-modal="form-crear_plan_pago" onClick="cargarDatosEdicionPlanPago(\'' + datos.nombre + '\', \'' + datos.codigo + '\', ' + datos.cuotas + ', ' + datos.interes + ');"></i></a></td>';
                    html += '<td><a href="javascript:void(0);" style="color:red;"><i class="fa fa-times-circle fa-2x eliminar_plan_pago" cod="' + datos.codigo + '"></i></a></td>';
                });
                $('#wrap_tabla_planes_pagos').html(html);
                $('#tabla_planes_pago').DataTable({
                    "aaSorting": [[1, 'asc']],
                    "oLanguage": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ clientes",
                        "sZeroRecords": "No se han encontrado clientes",
                        "sEmptyTable": "No se han encontrado clientes",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando clientes del 0 al 0 de un total de 0 clientes",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ clientes)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar: ",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
                });
                $('.editar_plan_pago').modalEffects();
                $('.eliminar_plan_pago').off('click');
                $('.eliminar_plan_pago').on('click', function () {
                    var url = 'classes/administrar_planes_pagos.php',
                        cod = $(this).attr('cod');
                    alertify.confirm('Atención', "Este producto ya tiene una promoción asignada ¿Desea reemplazarla?",
                        function () {
                            
                        
                            $.post(url,
                                {
                                    accion: 4,
                                    codigo: cod
                                },
                                function (data) {
                                    data = $.parseJSON(data);
                                    switch (data.resultado) {
                                    case 0:
                                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                                        break;
                                    case 1:
                                        mostrar_notificacion('Éxito', 'Plan eliminado exitosamente', 'success');
                                        cargarPlanesPago();
                                        break;
                                    default:
                                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                                        break;
                                    }
                                }).done(
                                function () {
                                }
                            );
                        },
                        function () {}
                        ).set('labels',
                        {
                            ok: 'Sí',
                            cancel: 'Cancelar'
                        }
                        );
                });
                break;
            case 0:
                mostrar_notificacion('Atención', 'No se pudo obtener los planes de pago', 'warning');
                break;
            }
        }).done(
        function () {
        }
    );
}

function crearPlanPago(codigo) {
    'use strict';
    var datos = {},
        focusTaken = false,
        valor,
        indicePaginas,
        url = 'classes/administrar_planes_pagos.php',
        habilitados = 0,
        accion;
    if (codigo === 0) {
        accion = 2;
    } else {
        accion = 3;
    }
    datos.plan_pago_nombre = sanear_numero($('#plan_pago_nombre').val());
    datos.plan_pago_cuotas = sanear_numero($('#plan_pago_cuotas').val());
    datos.plan_pago_interes = sanear_numero($('#plan_pago_interes').val());
    if ($('#check_habilitados').is(':checked')) {
        datos.habilitados = 1;
    } else {
        datos.habilitados = 0;
    }
    for (valor in datos) {
        if (datos.hasOwnProperty(valor)) {
            if (datos[valor] === null || datos[valor] === '') {
                if ($("#" + valor).hasClass('required')) {
                    $("#" + valor).parent().addClass('has-error');
                    if (!focusTaken) {
                        $("#" + valor).focus();
                        focusTaken = true;
                    }
                }
            } else {
                if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor]) === false) {
                    $("#" + valor).parent().addClass('has-error');
                    focusTaken = true;
                } else {
                    if ($("#" + valor).parent().hasClass('has-error')) {
                        $("#" + valor).parent().removeClass('has-error');
                    }
                    if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor])) {
                        $("#" + valor).parent().removeClass('has-error');
                    }
                }
            }
        }
    }
    if (!focusTaken) {
        $.post(url,
            {
                accion: accion,
                nombre: datos.plan_pago_nombre,
                codigo: codigo,
                cuotas: datos.plan_pago_cuotas,
                interes: datos.plan_pago_interes,
                habilitados: datos.habilitados
            },
            function (data) {
                data = $.parseJSON(data);
                switch (data.resultado) {
                case 0:
                    mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    break;
                case 1:
                    if (codigo === 0) {
                        mostrar_notificacion('Éxito', 'Plan creado exitosamente', 'success');
                    } else {
                        mostrar_notificacion('Éxito', 'Plan editado exitosamente', 'success');
                    }
                    $('#form-crear_plan_pago').removeClass('md-show');
                    cargarPlanesPago();
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                    break;
                }
            }).done(
            function () {
            }
        );
        
    }
}

function cargarDatosEdicionPlanPago(nombre, codigo, cuotas, interes) {
    'use strict';
    $('#btn_crear_plan_pago').html('Editar');
    $('#plan_pago_nombre').val(nombre);
    $('#plan_pago_cuotas').val(currencyFormat(cuotas, ''));
    $('#plan_pago_interes').val(interes, '');
    $('#btn_crear_plan_pago').off('click');
    $('#btn_crear_plan_pago').on('click', function () {
        crearPlanPago(codigo);
    });
}

function cargarModalPlanPago() {
    'use strict';
    $('.form-control').val('');
    if ($('.form-group').hasClass('has-error')) {
        $('.form-group').removeClass('has-error');
    }
    $('#btn_crear_plan_pago').html('Crear');
    $('#btn_crear_plan_pago').off('click');
    $('#btn_crear_plan_pago').on('click', function () {
        crearPlanPago(0);
    });
}

$('#formcliente').validate({
    rules: {
        rut: {
            required: true,
            rut: true
        },
        nombre: {
            required: true,
            lettersonly: true
        },
        apaterno: {
            required: true,
            lettersonly: true
        },
        amaterno: {
            lettersonly: true
        },
        email: {
            email: true
        },
        telefono: {
            number: true
        },
        direccion: {
        },
        comuna: {
        },
        region: {
        },
        pais: {
        },
        monto_autorizado: {
            number: true,
        },
        submitHandler: function () {
            'use strict';
            //guardarUsuario();
        }
    },
    highlight: function (element) {
        'use strict';
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        //$('#paso1').removeClass('next');
        //$('#guardar').attr('disabled', true);
    },
    unhighlight: function (element) {
        'use strict';
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        //$('#pass').addClass('next');
    }
});

$('#formcredito').validate({
    rules: {
        cliente: {
            required: true,
            rut: true
        },
        monto_autorizado: {
            required: true,
            number: true
        },
        costo_mantencion: {
            required: true,
            number: true
        },
        costo_uso: {
            required: true,
            number: true
        },
        f_facturacion: {
            required: true
        },
        f_pago: {
            required: true
        },
        submitHandler: function () {
            'use strict';
            //guardarUsuario();
        }
    },
    highlight: function (element) {
        'use strict';
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        //$('#paso1').removeClass('next');
        //$('#guardar').attr('disabled', true);
    },
    unhighlight: function (element) {
        'use strict';
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        //$('#pass').addClass('next');
    }
});

function limpiarFormularioPago() {
    'use strict';
    $('#cuotas_vencidas').html('');
    $('#proximas_cuotas').html('');
    $('#datos_cliente_pago_amaterno').html('');
    $('#datos_cliente_pago_apaterno').html('');
    $('#datos_cliente_pago_nombre').html('');
    $('#pago_cliente').off('change');
    $('#pago_cliente').val(0).trigger('change');
    $('#datos_cliente_pago_wrap').hide();
}

function cargarFormularioPago() {
    'use strict';
    var html = '<option value="0">Seleccione un cliente</option>';
    ocultarDivs();
    limpiarFormularioPago();
    cargarSelect('#pago_cliente');
    $('#wrap_formulario_pago').show('');
    $('.form-control').val('');
    $('#total_a_pagar').html('$0');
    $('#pago_cliente').on('change', function () {
        var url = 'classes/administrar_clientes.php',
            html = '',
            cuotasAPagar = [],
            fecha,
            mes,
            dia,
            f = new Date(),
            rut = $('#pago_cliente').val();
        mes = f.getMonth() + 1;
        if (mes < 10) {
            mes = '0' + mes;
        }
        dia = f.getDate();
        if (dia < 10) {
            dia = '0' + dia;
        }
        fecha = f.getFullYear() + '-' + mes + '-' + dia;
        $('#btn_pago').parent().show();
        $('#cuotas_vencidas').html('');
        $('#proximas_cuotas').html('');
        $.post(url,
            {
                accion: 4,
                rut: rut
            },
            function (data) {
                data = $.parseJSON(data);
                $('#datos_cliente_pago_wrap').show('slow');
                $.each(data.resultado, function (i, datos) {
                    $.each(datos, function (i, item) {
                        if (i !== 'monto_pagar') {
                            $('#datos_cliente_pago_' + i).html(item);
                        }
                    });
                    $.each(datos.monto_pagar, function (i, datos) {

                        html = '<input type="checkbox" class="icheck" id="check_' + i + '" fecha="' + datos.fecha + '" name="montos" monto="' + datos.monto + '">   <label>   ' + currencyFormat(datos.monto, '$') + ' <i>(' + datos.fecha.split("-").reverse().join("-") + ')</i></label><br>';
                        if (Date.parse(fecha) > Date.parse(datos.fecha)) {
                            $('#cuotas_vencidas').append(html);
                        } else {
                            $('#proximas_cuotas').append(html);
                        }
                    });
                    //$('#detalle_proximas_cuotas').html(html);
                    $('.icheck').iCheck({
                        checkboxClass: 'icheckbox_flat-blue'
                    });
                    $('.icheck').on('ifChecked', function () {
                        var cuota = {},
                            totalAPagar = sanear_numero($('#total_a_pagar').html());
                        cuota.fecha = $(this).attr('fecha');
                        cuota.monto = $(this).attr('monto');
                        cuotasAPagar.push(cuota);
                        totalAPagar = currencyFormat(parseInt(totalAPagar, 0) + parseInt(cuota.monto, 0), '$');
                        $('#total_a_pagar').html(totalAPagar);
                    });
                    $('.icheck').on('ifUnchecked', function () {
                        var fecha = $(this).attr('fecha'),
                            totalAPagar = sanear_numero($('#total_a_pagar').html()),
                            cuota,
                            indice;
                        for (indice = 0; indice < cuotasAPagar.length; indice++) {
                            if (cuotasAPagar[indice].fecha === fecha) {
                                break;
                            }
                        }
                        cuota = cuotasAPagar[indice];
                        totalAPagar = currencyFormat(parseInt(totalAPagar, 0) - parseInt(cuota.monto, 0), '$');
                        $('#total_a_pagar').html(totalAPagar);
                        cuotasAPagar.splice(indice, 1);
                        
                    });
                    $('#btn_pago').off('click');
                    $('#btn_pago').on('click', function () {
                        var url = 'classes/administrar_clientes.php',
                            rut = $('#pago_cliente').val();
                        $.post(url,
                            {
                                accion: 5,
                                rut: rut,
                                cuotas: cuotasAPagar
                            },
                            function (data) {
                                data = $.parseJSON(data);
                                switch (data.resultado) {
                                case 0:
                                    mostrar_notificacion('Error', 'No se ha procesado el pago', 'danger');
                                    break;
                                case 1:
                                    mostrar_notificacion('Éxito', 'Pago procesado exitosamente', 'success');
                                    $('#total_a_pagar').html('$0');
                                    cargarFormularioPago();
                                    break;
                                case 2:
                                    mostrar_notificacion('Atención', 'Algunos pagos no se procesaron', 'warning');
                                    cargarFormularioPago();
                                    break;
                                default:
                                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                                    break;
                                }
                            }).done(
                            function () {
                            }
                        );
                    });
                });
            }).done(
            function () {
            }
        );

    });
}

function obtenerEstadoHabilitados() {
    'use strict';
    var url = 'classes/habilitar_deshabilitar_habilitados.php';
    $.post(url,
        {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            if (data.resultado === '0') {
                $('#toggle_habilitados').prop('checked', false);
            } else {
                $('#toggle_habilitados').prop('checked', true);
                $('#lista_habilitados').show('slow');
            }
        }).done(
        function () {
        }
    );
    
}

$(document).ready(function () {
    'use strict';
    var f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
    cargarClientes();
    $('#nuevo_plan').modalEffects();
    $('#nuevo_plan_pago').modalEffects();
    obtenerEstadoHabilitados();
});

function nuevoCliente() {
    'use strict';
    $('#monto_autorizado_wrapper').hide();
    $('#check_linea_credito_wrapper').show();
    $('#check_linea_credito').iCheck('uncheck');
    obtenerLocalidades(1);
    ocultarDivs();
    $('#telefono').numeric();
    $('#monto_autorizado').numeric();
    $('.form-control').val('');
    $('.select2').val(0).trigger('change');
    $('#form_wrap_cliente').show('slow');
    
    $('#check_linea_credito').iCheck({
        checkboxClass: 'icheckbox_flat-blue'
    });
    $('#check_linea_credito').on('ifChecked', function (){
        $('#check_linea_credito_wrapper').hide('slow');
        $('#monto_autorizado_wrapper').show('slow');
    });
    $('#check_linea_credito').on('ifUnchecked', function (){
        $('#monto_autorizado_wrapper').hide('slow');
        $('#check_linea_credito_wrapper').show('slow');
    });
    $('#check_cupo_ilimitado').iCheck({
        checkboxClass: 'icheckbox_flat-blue'
    });
    $('#check_cupo_ilimitado').on('ifChecked', function (){
        cupoIlimitado = 1;
    });
    $('#check_cupo_ilimitado').on('ifUnchecked', function (){
        cupoIlimitado = 0;
    });
}

function cargarFormularioReportes() {
    'use strict';
    ocultarDivs();
    $('#wrap_formulario_reportes').show('slow');
    var url = 'classes/administrar_clientes.php',
        html = '<option value="0">Seleccione un habilitador</option>';
    $.post(url,
        {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.resultado, function (i, datos) {
                html += '<option value="' + datos.rut + '">' + aRut(datos.rut) + '</option>';
            });
            $('#habilitadores').html(html);
        }).done(
        function () {
            $('#habilitadores').off('change');
            $('#habilitadores').on('change', function cargarDetalleHabilitador() {
                var url = 'classes/reportes_habilitadores.php',
                    habilitador = $('#habilitadores').val(),
                    html = '';
                $.post(url,
                    {
                        accion: 1,
                        habilitador: habilitador
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        html += '<span>Nombre: ' + data.habilitador.nombre + ' ' + data.habilitador.apellidos + '</span><br>';
                        html += '<span>Rut: ' + aRut(data.habilitador.rut) + '</span><br>';
                        $('#wrap_datos_habilitador').html(html);
                        html = '';
                        $.each(data.habilitados, function (i, habilitados) {
                            html += '<span>Rut: ' + aRut(habilitados.vch_rut) + '</span><br>';
                            html += '<span>Monto adeudado: ' + currencyFormat(habilitados.monto, '$') + '</span><br>';
                        });
                        $('#wrap_datos_habilitados').html(html);
                    }).done(
                    function () {
                        $('#detalle_habilitador').show('slow');
                        $('#btn_reporte').parent().show();
                        $('#btn_reporte').off('click');
                        $('#btn_reporte').on('click', function generarReporte() {
                            var url = 'classes/reportes_habilitadores.php',
                                habilitador = $('#habilitadores').val();
                            $.post(url,
                                {
                                    accion: 2,
                                    habilitador: habilitador
                                },
                                function (data) {
                                    data = $.parseJSON(data);
                                    switch (data.resultado) {
                                    case 1:
                                        window.open(data.url, '_blank');
                                        break;
                                    default:
                                        mostrar_notificacion('Error', 'Error al generar informe', 'danger');
                                        break;
                                        
                                    }
                                }).done(
                                function () {
                                }
                            );
                        });
                    }
                );
            });
        }
    );
}

function generarCartola() {
    'use strict';
    var url = 'classes/generar_cartola.php',
        fInicio = $('#f_inicio').val(),
        fFin = $('#f_fin').val(),
        rut = $('#cliente_cartola').val(),
        numErrores = 0;
    if (fInicio > fFin) {
        $('#f_inicio').parent().addClass('has-error');
        $('#f_fin').parent().addClass('has-error');
        mostrar_notificacion('Error', 'La fecha fin no puede ser menor a la fecha inicio', 'warning');
        numErrores++;
    }
    if (fInicio === '') {
        $('#f_inicio').parent().addClass('has-error');
        mostrar_notificacion('Error', 'Seleccione una fecha de inicio', 'warning');
        numErrores++;
    }
    if (fFin === '') {
        $('#f_fin').parent().addClass('has-error');
        mostrar_notificacion('Error', 'Seleccione una fecha de fin', 'warning');
        numErrores++;
    }
    if (rut === '0') {
        mostrar_notificacion('Error', 'Seleccione un cliente', 'warning');
        numErrores++;
    }
    if (numErrores === 0) {
        $('.form-control').parent().removeClass('has-error');
        $.post(url,
            {
                accion: 1,
                rut: rut,
                f_inicio: fInicio,
                f_fin: fFin
            },
            function (data) {
                data = $.parseJSON(data);
                window.open(data.url, '_blank');
            }).done(
            function () {
            }
        );
    }
}

function cargarFormularioCartola() {
    'use strict';
    ocultarDivs();
    $('.form-control').val('');
    $('.form-control').parent().removeClass('has-error');
    $('#cliente_cartola').val(0).trigger('change');
    $('#wrap_formulario_cartola').show('slow');
    cargarSelect('#cliente_cartola');
    $('#btn_generar_cartola').off('click');
    $('#btn_generar_cartola').on('click', generarCartola);
}

$('#formcliente').on('submit', function (event) {
    'use strict';
    event.preventDefault();
    guardarCliente();
});

$('#formcredito').on('submit', function (event) {
    'use strict';
    event.preventDefault();
    guardarLineaCredito();
});

$('#nuevo_cliente').on('click', nuevoCliente);

$('#listado_clientes').on('click', cargarClientes);

$('#nueva_linea_credito').on('click', nuevaLineaCredito);

$('#pais').on('change', function () {
    'use strict';
    obtenerLocalidades(2);
});

$('#region').on('change', function () {
    'use strict';
    obtenerLocalidades(3);
});

$('.btn_limpiar').on('click', function () {
    'use strict';
    $('.select2').val('0').trigger("change");
    $('#datos_cliente_pago_wrap').hide();
});

$('#tabla_clientes_wrap').on('click', function () {
    'use strict';
    $('.detalle_cliente').modalEffects();
});

$('#toggle_habilitados').on('click', function () {
    'use strict';
    $('.nfn-overlay').show();
    var estado = $(this).prop('checked'),
        url = 'classes/habilitar_deshabilitar_habilitados.php';
    $.post(url,
        {
            accion: 1,
            estado: estado
        },
        function (data) {
        }).done(
        function () {
            $('.nfn-overlay').hide();
            if (estado === true) {
                $('#lista_habilitados').show('slow');
            } else {
                $('#lista_habilitados').hide('slow');
            }
        }
    );
});

$('#administracion_planes').on('click', cargarPlanes);

$('#nuevo_plan').on('click', cargarModalPlan);

$('#administracion_intereses').on('click', cargarPlanesPago);

$('#nuevo_plan_pago').on('click', cargarModalPlanPago);

$('#nuevo_pago').on('click', cargarFormularioPago);

$('#lista_habilitados').on('click', cargarFormularioReportes);

$('#cartola_pagos').on('click', cargarFormularioCartola);

$('#pcont').on('click', function () {
    'use strict';
    $('.editar_plan').modalEffects();
    $('.toggle_plan').modalEffects();
    $('.editar_plan_pago').modalEffects();
    $('.editar_cupo').modalEffects();
    $('.ver_historial').modalEffects();
});