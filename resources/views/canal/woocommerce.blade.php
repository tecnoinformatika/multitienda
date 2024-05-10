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
    <link href="https://cdn.datatables.net/v/bs/jq-3.7.0/dt-2.0.7/fh-4.0.1/kt-2.12.0/r-3.0.2/rr-1.5.0/sc-2.4.2/sb-1.7.1/sl-2.0.1/datatables.min.css" rel="stylesheet">
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
                    <div class="card-body">
                        <table id="example" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Progress</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Progress</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </tfoot>
                        </table>
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
        <script src="https://cdn.datatables.net/v/bs/jq-3.7.0/dt-2.0.7/fh-4.0.1/kt-2.12.0/r-3.0.2/rr-1.5.0/sc-2.4.2/sb-1.7.1/sl-2.0.1/datatables.min.js"></script>

        <!-- App js -->

        <script src="{{ URL::asset('/build/js/app.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
           $(document).ready(function() {
            // Realizar una solicitud AJAX para obtener los datos de los productos de WooCommerce
            var idcanal = {{ $canal->id }};
            $.ajax({
                url: '/obtenerProductosWoo/' + idcanal,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Datos de los productos:", response);
                    // Procesar los datos obtenidos para construir las columnas y los datos de la tabla
                    var columns = [
                        "imagen",
                        "Name",
                        "Price",
                        "Description"

                    ]; // Definir las columnas de la tabla
                    var data = []; // Inicializar un array para almacenar los datos de la tabla

                    // Iterar sobre los productos obtenidos y extraer la información relevante
                    $.each(response, function(index, product) {
                        var rowData = [
                            $('<img>').attr('src', product.images[0].src).addClass('img-fluid')[0], // Imagen del producto
                            product.name, // Nombre del producto
                            product.price, // Precio del producto
                            product.short_description, // Descripción del producto
                        ];
                        data.push(rowData); // Agregar los datos de la fila al array de datos
                    });

                    // Llamar a la función para crear el grid con las columnas y los datos construidos
                    createGrid(columns, data);
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los datos de los productos:", error);
                }
            });
            new DataTable('#example', {
                ajax: '/obtenerProductosWoo/' + idcanal,
                columns: [
                    {
                        data: 'name'
                    },
                    {
                        data: 'position',
                        render: function (data, type) {
                            if (type === 'display') {
                                let link = 'https://datatables.net';

                                if (data[0] < 'H') {
                                    link = 'https://cloudtables.com';
                                }
                                else if (data[0] < 'S') {
                                    link = 'https://editor.datatables.net';
                                }

                                return '<a href="' + link + '">' + data + '</a>';
                            }

                            return data;
                        }
                    },
                    {
                        className: 'f32', // used by world-flags-sprite library
                        data: 'office',
                        render: function (data, type) {
                            if (type === 'display') {
                                let country = '';

                                switch (data) {
                                    case 'Argentina':
                                        country = 'ar';
                                        break;
                                    case 'Edinburgh':
                                        country = '_Scotland';
                                        break;
                                    case 'London':
                                        country = '_England';
                                        break;
                                    case 'New York':
                                    case 'San Francisco':
                                        country = 'us';
                                        break;
                                    case 'Sydney':
                                        country = 'au';
                                        break;
                                    case 'Tokyo':
                                        country = 'jp';
                                        break;
                                }

                                return '<span class="flag ' + country + '"></span> ' + data;
                            }

                            return data;
                        }
                    },
                    {
                        data: 'extn',
                        render: function (data, type, row, meta) {
                            return type === 'display'
                                ? '<progress value="' + data + '" max="9999"></progress>'
                                : data;
                        }
                    },
                    {
                        data: 'start_date'
                    },
                    {
                        data: 'salary',
                        render: function (data, type) {
                            var number = DataTable.render
                                .number(',', '.', 2, '$')
                                .display(data);

                            if (type === 'display') {
                                let color = 'green';
                                if (data < 250000) {
                                    color = 'red';
                                }
                                else if (data < 500000) {
                                    color = 'orange';
                                }

                                return `<span style="color:${color}">${number}</span>`;
                            }

                            return number;
                        }
                    }
                ]
            });
        });

        // Función para crear el grid con las columnas y los datos proporcionados
        function createGrid(columns, data) {
            new gridjs.Grid({
                search:true, // Opcional: habilitar búsqueda por columnas
                pagination: true, // Opcional: habilitar paginación si hay muchos productos
                sort: true, // Opcional: habilitar ordenamiento de columnas
                columns: columns,
                data: data,
                className: {
                    table: 'table table-striped table-responsive', // Clases para la tabla
                    pagination: 'pagination-class' // Clases para la paginación (si se habilita)
                },

            }).render(document.getElementById("productos"));
        }
        </script>
    @endsection
