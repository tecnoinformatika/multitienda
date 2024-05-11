@extends('layouts.master')
@section('title')
    Advance Tables
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
    </style>


@endsection
@section('page-title')
    Advance Tables
@endsection
@section('body')

    <body>
    @endsection
    @section('content')


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Search</h4>
                    </div><!-- end card header -->
                    <div class="card-body" >
                        <div class="table-responsive">
                            <table id="productosTable" class="table table-nowrap table-responsive table-sm align-middle">
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
        <script src="{{ URL::asset('/build/js/app.js') }}"></script>


        <script>
           $(document).ready(function() {
            // Realizar una solicitud AJAX para obtener los datos de los productos de WooCommerce
            var idcanal = {{ $canal->id }};
            $.ajax({
                url: '/obtenerProductosWoo/' + idcanal,
                type: 'GET',
                dataType: 'json',
                success: function(response) {


                    $('#productosTable').DataTable({
                        data: response,
                        columns: [
                            { data: null, render: function(data, type, row) {
                                return '<div class="form-check font-size-16">' +
                                    '<input type="checkbox" class="form-check-input" id="checkbox' + data.index + '">' +
                                    '<label class="form-check-label" for="checkbox' + data.index + '"></label>' +
                                    '</div>';
                            }},
                            { data: null, render: function(data, type, row) {
                                var imgSrc = (row.images.length > 0) ? row.images[0].src : '/images/iconoscanales/woocommerce.png';
                                return '<img src="' + imgSrc + '" class="img-fluid">';
                            }},
                            // Columna de nombre (con ancho máximo)
                            '<div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + product['name'] + '</div>',
                            { data: 'regular_price' },
                            { data: 'stock_quantity' },
                            { data: function(row) {
                                var categories = row.categories;
                                var categoryString = '';
                                if (categories && categories.length > 0) {
                                    categories.forEach(function(category, index) {
                                        if (category.name) {
                                            if (index > 0) {
                                                categoryString += ' > ';
                                            }
                                            categoryString += category.name;
                                        }
                                    });
                                }
                                if (categoryString === '') {
                                    return 'Categoría no definida';
                                }
                                return categoryString;
                            }},
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

        });

        // Función para crear el grid con las columnas y los datos proporcionados


        </script>
    @endsection
