<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @stack('head')
</head>

<body class="@yield('body-class')" @if ($autoPrint ?? true) onload="window.print()" @endif>
    @yield('content')
</body>

</html>
