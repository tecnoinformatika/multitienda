@extends('layouts.master')
@section('title')
    Visualiza y sincroniza tus productos
@endsection
@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('/build/libs/gridjs/theme/mermaid.min.css') }}">
    <link href="{{ URL::asset('/build/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ URL::asset('/build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ URL::asset('/build/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .dataTables_paginate {
            text-align: center;
        }

        .dataTables_paginate a.paginate_button {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 2px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            color: #495057;
            text-decoration: none;
            cursor: pointer;
        }

        .dataTables_paginate a.paginate_button.current {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .dataTables_paginate a.paginate_button:hover {
            background-color: #e9ecef;
        }

        .dataTables_paginate a.paginate_button.disabled {
            pointer-events: none;
            opacity: 0.5;
        }
        #productosTable_filter {
            margin-bottom: 20px; /* Espacio entre el cuadro de búsqueda y la tabla */
        }

        #productosTable_filter label {
            font-weight: bold; /* Texto en negrita */
        }

        #productosTable_filter input[type="search"] {
            padding: 5px; /* Espacio interno del cuadro de búsqueda */
            border: 1px solid #ccc; /* Borde del cuadro de búsqueda */
            border-radius: 5px; /* Bordes redondeados del cuadro de búsqueda */
            width: 200px; /* Ancho del cuadro de búsqueda */
        }
        #productosTable th:nth-child(2),
        #productosTable td:nth-child(2),
        #productosTable th:nth-child(5),
        #productosTable td:nth-child(5) {
            max-width: 200px; /* Ancho máximo permitido */
            overflow: hidden; /* Ocultar el texto que exceda el ancho máximo */
            text-overflow: ellipsis; /* Mostrar puntos suspensivos (...) cuando el texto exceda el ancho máximo */
            white-space: nowrap; /* Evitar el salto de línea para mostrar el texto en una sola línea */
        }
        .img-fluid{
            max-width: 27% !important;
        }
    </style>


@endsection
@section('page-title')
    Visualiza y sincroniza tus productos
@endsection
@section('body')

    <body>
    @endsection
    @section('content')


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sincronizar con canales</h4>
                    </div><!-- end card header -->
                    <div class="card-body" >
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <button type="button" id="botonEnviar" class="btn btn-primary btn-lg waves-effect waves-light">Sincronizar</button>

                            <div class="modal fade" id="modalEnviarProductos" data-bs-backdrop="static"
                                data-bs-keyboard="false" tabindex="-1" role="dialog"
                                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>IDs de los productos seleccionados:</p>
                                            <ul id="listaProductosSeleccionados"></ul>
                                            <div class="mb-3">
                                                <label for="selectCanal" class="form-label">Selecciona un canal:</label>
                                                <select class="form-select" id="selectCanal">
                                                    <!-- Aquí puedes agregar opciones de canales -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" id="enviarP" class="btn btn-primary">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card body -->

                </div>
                <!-- end card -->
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Tus productos de {{ $canal->url }}</h4>
                    </div><!-- end card header -->
                    <div class="card-body" >
                        <div class="table-responsive">
                            <table id="productosTable" class="table table-responsive table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-4" style="width: 50px;">
                                            <div class="form-check font-size-16">
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th scope="col">Imagen</th>
                                        <th scope="col" style="width: 200px;">Nombre</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Cantidad disponible</th>
                                        <th scope="col" style="width: 200px;">Categorías</th>
                                        <th scope="col" style="width: 200px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Aquí se agregarán las filas dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- end card body -->

                </div>
                <!-- end card -->
            </div>
            <!-- end col -->

        </div>
        <!-- end row -->
    @endsection
    @section('scripts')
        <!-- gridjs js -->
       <!-- App js -->
          <!-- jQuery (obligatorio para DataTables) -->
          <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
          <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
          <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
          <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <script src="{{ URL::asset('/build/js/app.js') }}"></script>


        <script>
           $(document).ready(function() {
            // Realizar una solicitud AJAX para obtener los datos de los productos de WooCommerce
            var idcanal = {{ $canal->id }};
            $.ajax({
                url: '/obtenerProductosMeli/' + idcanal,
                type: 'GET',
                dataType: 'json',
                success: function(response) {


                    $('#productosTable').DataTable({
                        data: response,
                        columns: [
                            { data: null, render: function(data, type, row) {
                                return '<div class="form-check font-size-16">' +
                                    '<input type="checkbox" class="form-check-input" id="checkbox' + data.id + '">' +
                                    '<label class="form-check-label" for="checkbox' + data.id + '"></label>' +
                                    '</div>';
                            }},
                            {
                                data: null,
                                render: function(data, type, row) {
                                    var imgSrc = (row.pictures.length > 0) ? row.pictures[0].url : '/images/iconoscanales/mercadolibre.png';
                                    return '<img src="' + imgSrc + '" class="img-fluid">';
                                }
                            },
                            // Columna de nombre (con ancho máximo)
                            {data: 'title'},
                            { data: 'price' },
                            { data: 'initial_quantity' },
                            { 
                                data: function(row) {
                                    var categories = row.category_name;
                                    var categoryString = '';
                                    if (categories && categories.length > 0) {
                                        var firstCategory = categories[0]; // Tomar solo la primera categoría
                                        if (firstCategory) {
                                            var badgeClass = 'badge-soft-success'; // Aplicar la clase "badge-soft-success"
                                            categoryString += '<span class="badge ' + badgeClass + ' mb-0">' + firstCategory + '</span>';
                                        }
                                    }
                                    return categoryString;
                                }
                                
                            },
                            { data: null, render: function(data, type, row) {
                                return '<ul class="list-inline mb-0">' +
                                    '<li class="list-inline-item">' +
                                    '<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" class="px-2 text-primary"><i class="bx bx-pencil font-size-18"></i></a>' +
                                    '</li>' +
                                    '<li class="list-inline-item">' +
                                    '<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" class="px-2 text-danger"><i class="bx bx-trash-alt font-size-18"></i></a>' +
                                    '</li>' +
                                    '<li class="list-inline-item dropdown">' +
                                    '<a class="text-muted dropdown-toggle font-size-18 px-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true">' +
                                    '<i class="bx bx-dots-vertical-rounded"></i>' +
                                    '</a>' +
                                    '<div class="dropdown-menu dropdown-menu-end">' +
                                    '<a class="dropdown-item" href="#">Action</a>' +
                                    '<a class="dropdown-item" href="#">Another action</a>' +
                                    '<a class="dropdown-item" href="#">Something else here</a>' +
                                    '</div>' +
                                    '</li>' +
                                    '</ul>';
                            }}
                        ],
                        paging: true,
                        pageLength: 10 // Cantidad de registros por página
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los datos de los productos:", error);
                }
            });
            // Función para enviar productos seleccionados al controlador
            $('#botonEnviar').on('click', function() {
                abrirModalEnviarProductos();
            });

            // Función para abrir el modal de enviar productos
            function abrirModalEnviarProductos() {
                var productosSeleccionados = [];
                $('#productosTable tbody input[type="checkbox"]:checked').each(function() {
                    var productoId = $(this).attr('id').replace('checkbox', '');
                    productosSeleccionados.push(productoId);
                });

                // Mostrar los IDs de los productos seleccionados en el modal
                var listaProductosSeleccionados = $('#listaProductosSeleccionados');
                listaProductosSeleccionados.empty();
                productosSeleccionados.forEach(function(id) {
                    listaProductosSeleccionados.append('<li>' + id + '</li>');
                });

                // Aquí puedes cargar las opciones del select con los canales disponibles
                cargarOpcionesCanales();

                // Abrir el modal
                $('#modalEnviarProductos').modal('show');
            }

            // Función para cargar las opciones del select con los canales disponibles
            function cargarOpcionesCanales() {
                // Aquí puedes realizar una petición AJAX para obtener los canales disponibles
                // Por ejemplo:
                $.ajax({
                    url: '/obtenerCanales/'+idcanal,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var selectCanal = $('#selectCanal');
                        selectCanal.empty();
                        response.forEach(function(canal) {
                            selectCanal.append('<option value="' + canal.id + '">' + canal.canal + ' '+ canal.url +'</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al obtener los canales:", error);
                    }
                });
            }

            // Evento de cambio en el select de canal
            $('#selectCanal').on('change', function() {
                // Habilitar el botón de enviar
                $('#enviarP').prop('disabled', false);
            });

            // Evento de clic en el botón de enviar
            $('#enviarP').on('click', function() {
                enviarProductosSeleccionados();
            });

            // Función para enviar productos seleccionados al controlador
            function enviarProductosSeleccionados() {
                var productosSeleccionados = [];
                $('#productosTable tbody input[type="checkbox"]:checked').each(function() {
                    var productoId = $(this).attr('id').replace('checkbox', '');
                    var producto = $('#productosTable').DataTable().row($(this).closest('tr')).data();
                    productosSeleccionados.push(producto);
                });

                var canalSeleccionado = $('#selectCanal').val();

                // Aquí puedes enviar los productos seleccionados al controlador usando AJAX
                // Por ejemplo:
                $.ajax({
                    url: '/ruta-del-controlador',
                    type: 'POST',
                    data: { productos: productosSeleccionados, canal: canalSeleccionado },
                    success: function(response) {
                        // Manejar la respuesta del controlador si es necesario
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al enviar los productos seleccionados:", error);
                    }
                });
            }
            // Función para seleccionar/deseleccionar todos los checkboxes en el tbody
            function toggleSelectAll() {
                var selectAllCheckbox = $('#selectAll');
                var checkboxes = $('#productosTable tbody input[type="checkbox"]');

                checkboxes.prop('checked', selectAllCheckbox.prop('checked'));
            }

            // Evento de cambio en el checkbox #selectAll
            $('#selectAll').on('change', function() {
                toggleSelectAll();
            });

        });

        // Función para crear el grid con las columnas y los datos proporcionados


        </script>
    @endsection
