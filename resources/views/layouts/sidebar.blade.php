<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('/build/images/logo-dark-sm.png') }}" alt="" height="38">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('/build/images/logo-dark.png') }}" alt="" height="35">
            </span>
        </a>

        <a href="index" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ URL::asset('/build/images/logo-light.png') }}" alt="" height="35">
            </span>
            <span class="logo-sm">
                <img src="{{ URL::asset('/build/images/logo-light-sm.png') }}" alt="" height="38">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
        <i class="bx bx-menu align-middle"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Panel de control</li>

               <li>
                    <a href="javascript: void(0);">
                        <i class="bx bx-home-alt icon nav-icon"></i>
                        <span class="menu-item" data-key="t-dashboard">Dashboard</span>
                        <span class="badge rounded-pill bg-primary">2</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="index" data-key="t-ecommerce">Ecommerce</a></li>
                        <li><a href="dashboard-sales" data-key="t-sales">Sales</a></li>
                    </ul>
                </li>

                <li class="menu-title" data-key="t-applications">Herramientas</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="bx bx-store icon nav-icon"></i>
                        <span class="menu-item" data-key="t-ecommerce">Tu tienda</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="{{ url('listarproductos') }}" data-key="t-products">Productos dropshipping</a></li>
                        <li><a href="{{ url('canales') }}" data-key="t-product-detail">Canales</a></li>
                        <li><a href="{{ url('verpedidos') }}" data-key="t-orders">Pedidos</a></li>
                        <li><a href="ecommerce-customers" data-key="t-customers">Customers</a></li>
                        <li><a href="ecommerce-cart" data-key="t-cart">Cart</a></li>
                        <li><a href="ecommerce-checkout" data-key="t-checkout">Checkout</a></li>
                        <li><a href="ecommerce-shops" data-key="t-shops">Shops</a></li>
                        <li><a href="ecommerce-add-product" data-key="t-add-product">Add Product</a></li>
                    </ul>
                </li>
                <li>
                    <a href="apps-calendar">
                        <i class="bx bx-calendar-event icon nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Calendario</span>
                    </a>
                </li>












            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
