@extends('layouts.master')
@section('title')
    Advance Tables
@endsection
@section('css')
    <!-- gridjs css -->
    <link rel="stylesheet" href="{{ URL::asset('/build/libs/gridjs/theme/mermaid.min.css') }}">
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
                        <div id="productos"></div>
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
        <script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
        <!-- App js -->
        <script type="module">
            import {
                Grid,
                html
            } from "https://unpkg.com/gridjs?module";
        </script>
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
