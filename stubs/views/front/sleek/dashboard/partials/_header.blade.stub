 <!-- Header -->
 <header class="main-header " id="header">
    <nav class="navbar navbar-static-top navbar-expand-lg">
        <!-- Sidebar toggle button -->
        <button id="sidebar-toggler" class="sidebar-toggle">
            <span class="sr-only">Toggle navigation</span>
        </button>
        <!-- search form -->
        <div class="search-form d-none d-lg-inline-block" style="display: ">
            <div class="input-group">
                <button type="button" name="search" id="search-btn" class="btn btn-flat">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>
            <div id="search-results-container">
            </div>
        </div>

        <div class="navbar-right ml-auto">
            <ul class="nav navbar-nav">
                @php
                $unreadNotifications = auth()->user()->unreadNotifications;
                @endphp
                <li class="dropdown notifications-menu">
                    <button class="dropdown-toggle" data-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        @if($unreadNotifications->count())
                        <li class="dropdown-header">Vous avez {{ $unreadNotifications->count() }} notification(s)</li>
                        @endif
                        @forelse($unreadNotifications as $notification)
                        <li>
                            <a href="#">
                                <i class="mdi mdi-account-plus"></i> Notification 1
                                <span class=" font-size-12 d-inline-block float-right"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                            </a>
                        </li>
                        @empty
                        <li>
                            <a href="#">
                                Pas de notifications pour l'instant
                            </a>
                        </li>
                        @endforelse
                        @if(empty($unreadNotifications))
                        <li class="dropdown-footer">
                            <a class="text-center" href="#"> Voir toutes les notifications </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- User Account -->
                <li class="dropdown user-menu">
                    <button href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar }}" class="user-image" alt="User Image" />
                        <span class="d-none d-lg-inline-block">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <!-- User image -->
                        <li class="dropdown-header">
                            <img src="{{ auth()->user()->avatar }}" class="img-circle" alt="User Image" />
                            <div class="d-inline-block">
                               {{ auth()->user()->name }} <small class="pt-1">{{ auth()->user()->email }}</small>
                            </div>
                        </li>

                        <li>
                            <a href="{{ front_route('dashboard.profile.edit') }}">
                                <i class="mdi mdi-account"></i> Editer mon compte
                            </a>
                        </li>

                        <li class="dropdown-footer">
                            <a
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                href="{{ route('logout') }}">
                                <i class="mdi mdi-logout"></i> Déconnexion
                            </a>
                        </li>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                            @honeypot
                        </form>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>


</header>
