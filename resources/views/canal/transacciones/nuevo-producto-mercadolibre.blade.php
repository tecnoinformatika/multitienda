@extends('layouts.master')
@section('title')
    Add Product
@endsection
@section('css')
    <!-- choices css -->
    <link href="{{ URL::asset('/build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- dropzone css -->
    <link href="{{ URL::asset('/build/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('page-title')
    Add Product
@endsection
@section('body')

    <body>
    @endsection
    @section('content')
        <div class="row">
            <div class="col-lg-12">
                <div id="addproduct-accordion" class="custom-accordion">
                    <div class="card">
                        <a href="#addproduct-productinfo-collapse" class="text-dark" data-bs-toggle="collapse"
                            aria-expanded="true" aria-controls="addproduct-productinfo-collapse">
                            <div class="p-4">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <h5 class="text-primary font-size-17 mb-0">01</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="font-size-16 mb-1">Nuevo producto</h5>
                                        <p class="text-muted text-truncate mb-0">Completa toda la información</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i>
                                    </div>

                                </div>

                            </div>
                        </a>

                        <div id="addproduct-productinfo-collapse" class="collapse show"
                            data-bs-parent="#addproduct-accordion">
                            <div class="p-4 border-top">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="productname">Nombre del producto</label>
                                        <input id="productname" name="productname" placeholder="Enter Product Name"
                                            type="text" class="form-control">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div id="suggested-category-container" class="mb-3" style="display: none;">
                                                <label class="form-label" for="suggested-category">Categorías sugeridas</label>
                                                <div id="suggested-category-list" class="list-group"></div>
                                                <input id="category-id" name="category-id" type="hidden">
                                                <a href="#" class="btn btn-success" id="boton-buscar-categoria"> <i class="bx bx-plus me-1"></i> Buscar otra categoría </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div id="condition-container" class="mb-3" style="display: none;">
                                                <label class="form-label" for="item-condition">Condición del ítem</label>
                                                Indica el estado en que se encuentra tu producto.
                                                <select id="item-condition" name="item-condition" class="form-control"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">

                                            <div class="mb-3">
                                                <label class="form-label" for="manufacturername">Marca</label>
                                                <input id="marca" name="marca"
                                                    placeholder="Ingresa la marca del producto" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">

                                            <div class="mb-3">
                                                <label class="form-label" for="fabricante">Fabricante</label>
                                                <input id="fabricante" name="fabricante"
                                                    placeholder="Enter Manufacturer Brand" type="text"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="precio">Precio</label>
                                                <input id="precio" name="precio" placeholder="Enter Price"
                                                    type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none" id="categorias-container">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="choices-single-default" class="form-label">Categoria</label>
                                                <select class="form-control" name="choices-single-category"
                                                    id="choices-single-category">
                                                    <option value="">Selecciona</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="choices-single-specifications"
                                                    class="form-label">Subcategoria</label>
                                                <select class="form-control" name="choices-single-specifications"
                                                    id="choices-single-subcategoria">

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="choices-single-specifications"
                                                    class="form-label">Categoria final</label>
                                                <select class="form-control" name="choices-single-specifications"
                                                    id="choices-single-subcategoria2">

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label" for="productdesc">Product Description</label>
                                        <textarea class="form-control" id="productdesc" placeholder="Enter Description" rows="4"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <a href="#addproduct-img-collapse" class="text-dark collapsed" data-bs-toggle="collapse"
                            aria-haspopup="true" aria-expanded="false" aria-haspopup="true"
                            aria-controls="addproduct-img-collapse">
                            <div class="p-4">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <h5 class="text-primary font-size-17 mb-0">02</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="font-size-16 mb-1">Product Image</h5>
                                        <p class="text-muted text-truncate mb-0">Fill all information below</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i>
                                    </div>

                                </div>

                            </div>
                        </a>

                        <div id="addproduct-img-collapse" class="collapse" data-bs-parent="#addproduct-accordion">
                            <div class="p-4 border-top">
                                <form action="#" class="dropzone">
                                    <div class="fallback">
                                        <input name="file" type="file" multiple="multiple">
                                    </div>
                                    <div class="dz-message needsclick">
                                        <div class="mb-3">
                                            <i class="display-4 text-muted mdi mdi-cloud-upload"></i>
                                        </div>

                                        <h4>Drop files here or click to upload.</h4>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <a href="#addproduct-metadata-collapse" class="text-dark collapsed" data-bs-toggle="collapse"
                            aria-haspopup="true" aria-expanded="false" aria-haspopup="true"
                            aria-controls="addproduct-metadata-collapse">
                            <div class="p-4">

                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar">
                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <h5 class="text-primary font-size-17 mb-0">03</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h5 class="font-size-16 mb-1">Meta Data</h5>
                                        <p class="text-muted text-truncate mb-0">Fill all information below</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i>
                                    </div>

                                </div>

                            </div>
                        </a>

                        <div id="addproduct-metadata-collapse" class="collapse" data-bs-parent="#addproduct-accordion">
                            <div class="p-4 border-top">
                                <form>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="metatitle">Meta Title</label>
                                                <input id="metatitle" name="metatitle" placeholder="Enter Title"
                                                    type="text" class="form-control">
                                            </div>

                                        </div>

                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="metakeywords">Meta Keywords</label>
                                                <input id="metakeywords" name="metakeywords" placeholder="Enter Keywords"
                                                    type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label" for="metadescription">Meta Description</label>
                                        <textarea class="form-control" id="metadescription" placeholder="Enter Description" rows="4"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

        <div class="row mb-4">
            <div class="col text-end">
                <a href="#" class="btn btn-danger"> <i class="bx bx-x me-1"></i> Cancel </a>
                <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#success-btn"> <i
                        class=" bx bx-file me-1"></i> Save </a>
            </div> <!-- end col -->
        </div> <!-- end row-->
    @endsection
    @section('scripts')
        <!-- choices js -->
        <script src="{{ URL::asset('/build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

        <!-- dropzone plugin -->
        <script src="{{ URL::asset('/build/libs/dropzone/min/dropzone.min.js') }}"></script>

        <!-- init js -->
        <script src="{{ URL::asset('/build/js/pages/ecommerce-choices.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ URL::asset('/build/js/app.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script>
            var idcanal = {{ $canal->id }};
            $(document).ready(function() {
                $('#productname').on('blur', function() {
                    var productName = $(this).val();
                    if (productName.length > 0) {
                        $.ajax({
                            url: `/predict-category?q=${encodeURIComponent(productName)}&canal_id=${idcanal}`,
                            method: 'GET',
                            success: function(data) {
                                var $container = $('#suggested-category-list');
                                $container.empty(); // Limpiar el contenedor antes de añadir nuevas opciones

                                if (data.length > 0) {
                                    data.forEach(function(category) {
                                        console.log(category);
                                        var $item = $('<div>').addClass('list-group-item list-group-item-action').attr('data-category-id', category.id);
                                        var $title = $('<h5>').addClass('mb-1').text(category.name);
                                        var $path = $('<p>').addClass('mb-1').text(category.path_from_root.map(c => c.name).join(' > '));

                                        $item.append($title).append($path);
                                        $container.append($item);
                                    });

                                    // Mostrar el contenedor de categorías sugeridas
                                    $('#suggested-category-container').show();
                                } else {
                                    $('#suggested-category-container').hide();
                                }
                            },
                            error: function(error) {
                                console.error('Error fetching category:', error);
                                $('#suggested-category-container').hide();
                            }
                        });
                    } else {
                        $('#suggested-category-container').hide();
                    }
                });
                // Evento para seleccionar una categoría del select
                $('#suggested-category-list').on('click', '.list-group-item', function() {
                    var categoryId = $(this).data('category-id');
                    var categoryName = $(this).find('h5').text();

                    $('#category-id').val(categoryId);
                    $('#suggested-category').val(categoryName);

                    // Opcional: marcar el elemento seleccionado visualmente
                    $(this).addClass('active').siblings().removeClass('active');

                    // Hacer una solicitud para obtener los detalles de la categoría
                    $.ajax({
                        url: `https://api.mercadolibre.com/categories/${categoryId}`,
                        method: 'GET',
                        success: function(categoryDetails) {
                            var $conditionSelect = $('#item-condition');
                            $conditionSelect.empty(); // Limpiar el select antes de añadir nuevas opciones

                            if (categoryDetails.settings && categoryDetails.settings.item_conditions) {
                                var conditionMapping = {
                                    "used": "Usado",
                                    "new": "Nuevo",
                                    "not_specified": "No especificado"
                                };
                                categoryDetails.settings.item_conditions.forEach(function(condition) {
                                    var translatedCondition = conditionMapping[condition] || condition;
                                    var $option = $('<option>').val(condition).text(translatedCondition);
                                    $conditionSelect.append($option);
                                });

                                // Mostrar el contenedor de condiciones del ítem
                                $('#condition-container').show();
                            } else {
                                $('#condition-container').hide();
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching category details:', error);
                            $('#condition-container').hide();
                        }
                    });
                });

                // Evento para seleccionar una categoría del select
                $('#suggested-category-list').on('click', '.list-group-item', function() {
                    var categoryId = $(this).data('category-id');
                    var categoryName = $(this).find('h5').text();

                    $('#category-id').val(categoryId);
                    $('#suggested-category').val(categoryName);
                });
            });

            function loadCategories(idcanal) {
                console.log(idcanal);
                $.ajax({
                    url: `/obtener-categorias-meli?canal_id=${idcanal}`,
                    method: 'GET',
                    success: function(data) {
                        var $select = $('#choices-single-category');
                        $.each(data, function(index, category) {
                            var $option = $('<option>').val(category.id).text(category.name);
                            $select.append($option);
                        });
                    },
                    error: function(error) {
                        console.error('Error loading categories:', error);
                    }
                });
            }
            // Función para cargar las subcategorías según la categoría principal seleccionada
            function loadSubcategorias(categoriaId) {
                $.ajax({
                    url: '/obtener-subcategorias', // Reemplaza con la URL correcta de tu backend
                    method: 'GET',
                    data: {
                        categoriaId: categoriaId,
                        canal_id: {{ $canal->id }}
                    },
                    success: function(data) {
                        var $selectSubcategoria = $('#choices-single-subcategoria');
                        $selectSubcategoria.empty();
                        $.each(data, function(index, subcategoria) {
                            console.log(subcategoria);
                            var $option = $('<option>').val(subcategoria.id).text(subcategoria.name);
                            $selectSubcategoria.append($option);
                        });
                    },
                    error: function(error) {
                        console.error('Error loading subcategorías:', error);
                    }
                });
            }
            // Función para cargar las categorías finales según la subcategoría seleccionada
            function loadCategoriasFinal(subcategoriaId) {
                $.ajax({
                    url: '/obtener-categorias-final', // Reemplaza con la URL correcta de tu backend
                    method: 'GET',
                    data: {
                        subcategoriaId: subcategoriaId,
                        canal_id: {{ $canal->id }}
                    },
                    success: function(data) {
                        var $selectCategoriaFinal = $('#choices-single-subcategoria2');
                        $selectCategoriaFinal.empty();
                        $.each(data, function(index, categoriaFinal) {
                            var $option = $('<option>').val(categoriaFinal.id).text(categoriaFinal.name);
                            $selectCategoriaFinal.append($option);
                        });
                    },
                    error: function(error) {
                        console.error('Error loading categorías final:', error);
                    }
                });
            }

            // Escucha el evento change en el select de categorías para cargar las subcategorías
            $('#choices-single-category').on('change', function() {
                var categoriaId = $(this).val();
                loadSubcategorias(categoriaId);
            });

            // Escucha el evento change en el select de subcategorías para cargar las categorías finales
            $('#choices-single-subcategoria').on('change', function() {
                var subcategoriaId = $(this).val();
                loadCategoriasFinal(subcategoriaId);
            });
            $('#boton-buscar-categoria').on('click', function() {
                document.getElementById('categorias-container').style.display = 'flex';
            });

            // Llamar a la función para cargar las categorías
            $(document).ready(function() {
                var idcanal = {{ $canal->id }};
                loadCategories(idcanal);
            });
        </script>
    @endsection
