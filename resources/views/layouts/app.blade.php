<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('livros.index') }}">Bookfy</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('livros.index') }}">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Fóruns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Livros</a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('estante.*') ? 'active' : '' }}"
                               href="{{ route('estante.index') }}">Minhas Estantes</a>
                        </li>
                        @endauth
                    </ul>
                    <form class="d-flex" action="{{ route('livros.buscar') }}" method="get">
                        <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar"
                            name="buscar">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>
                    @guest
                        <a href="{{ route('usuario.login') }}" class="btn btn-outline-primary me-2 ms-2">Entrar</a>
                        <a href="{{ route('usuario.registro') }}" class="btn btn-primary">Registrar</a>
                    @else
                        <a href="{{ route('usuario.perfil') }}" class="navbar-text me-3 text-decoration-none">
                            {{ Auth::user()->nome }}
                        </a>
                        <form action="{{ route('usuario.logout') }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Sair</button>
                        </form>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
        @if (session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach ($errors->all() as $erro)
                        <div>{{ $erro }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            </div>
        @endif

        @yield('conteudo')
    </main>

    <footer class="bg-light text-center text-lg-start">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2026 Bookfy. Todos os direitos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
