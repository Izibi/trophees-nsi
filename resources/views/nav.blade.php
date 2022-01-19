<header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbar-menu">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/projects">Projects</a>
                    </li>
                    @if(Auth::user()->role == 'admin')
                        <li class="nav-item">
                            <a class="nav-link" href="/users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/schools">Schools</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contests">Contests</a>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            {{ Auth::user()->screen_name }}
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/profile?refer_page={{ urlencode(Request::url()) }}">Edit profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/logout">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>