<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbar-menu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/projects">Projets</a>
                    </li>
                    @if(Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="/users">Utilisateurs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/schools">Établissements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contests">Concours</a>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            {{ Auth::user()->screen_name }}
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/profile?refer_page={{ urlencode(Request::url()) }}">Modifier mon profil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/logout">Déconnexion</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>