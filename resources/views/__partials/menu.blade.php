<style>
    nav ul li a:hover:not(#UserDropdown):not(.notme) {
        background-color: #d69d0f !important;
    }

    .menu-eca {
        background-color: #003a63;

    }

    ul li span {
        color: white !important;
    }

    ul li p {
        color: white !important;
    }

    ul li i {
        color: white !important;
    }

    a[aria-expanded="true"]:not(#UserDropdown){
        background-color: #d69d0f !important;
    }
</style>
<nav class="sidebar sidebar-offcanvas menu-eca" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <div class="nav-link">
                <div class="user-wrapper">
                    <div class="profile-image">
                        <img src="{{asset('staradmin/assets/images/faces/face3.jpg')}}" alt="profile image"></div>
                    <div class="text-wrapper">
                        <p class="profile-name">{{\Illuminate\Support\Facades\Auth::user()->name}}  </p>
                        <div>
                            <small class="designation text-muted">Admin</small>
                            <span class="status-indicator online"></span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @if(\Illuminate\Support\Facades\Auth::user()->tipo_usuario == 1)
        	<li class="nav-item hand" id="reload">
	            <a class="nav-link" href="{{route('home')}}">
	                <i class="menu-icon mdi mdi-television"></i>
	                <span class="menu-title">Dashboard</span>
	            </a>
	        </li>
            <li class="nav-item hand">
                <a class="nav-link" href="{{route('agenda')}}">
                    <i class="menu-icon mdi mdi-book-open-page-variant"></i>
                    <span class="menu-title">Agenda</span>
                </a>
            </li>

            <li class="nav-item hand">
                <a class="nav-link" href="{{route('mapas')}}">
                    <i class="menu-icon mdi mdi-map"></i>
                    <span class="menu-title">Mapa de Visitas</span>
                </a>
            </li>

            <li class="nav-item hand">
                <a class="nav-link" href="{{route('agenda.consultas.servicios')}}">
                    <i class="menu-icon mdi mdi-map"></i>
                    <span class="menu-title">Consutas Generales</span>
                </a>
            </li>

            <li class="nav-item hand">
                <a class="nav-link" href="{{route('agenda.pci.uploadlecturas')}}">
                    <i class="menu-icon mdi mdi-map"></i>
                    <span class="menu-title">Carga de Lecturas</span>
                </a>
            </li>
            @if(\Illuminate\Support\Facades\Auth::user()->puesto == 'admin')
              <li class="nav-item hand">
                  <a class="nav-link" href="{{route('usuarios')}}">
                      <i class="menu-icon mdi mdi-map"></i>
                      <span class="menu-title">Usuarios Web</span>
                  </a>
              </li>
              <li class="nav-item hand">
                  <a class="nav-link" href="{{route('usuarioste')}}">
                      <i class="menu-icon mdi mdi-map"></i>
                      <span class="menu-title">Usuarios Terreno</span>
                  </a>
              </li>
            @endif
        @else
            <li class="nav-item hand">
                <a class="nav-link" href="{{route('agenda.consultas.servicios')}}">
                    <i class="menu-icon mdi mdi-map"></i>
                    <span class="menu-title">Consutas Generales</span>
                </a>
            </li>
        @endif
    </ul>
</nav>
