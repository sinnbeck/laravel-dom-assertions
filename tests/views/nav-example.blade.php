<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Laravel DOM Assertions</title>
    </head>
    <body>
        <nav>lass([
            'p-4',
            'font-bold' => $isActive,
            'text-gray-500' => ! $isActive,
            'bg-red' => $hasError,
        ])></span>
            <ul>
                @foreach ($menuItems as $menuItem)
                <li @class([
                    'p-3 text-white',
                    'text-blue-500 active' => Route::is($menuItem['route'])
                ])>
                    <a href="{{route($menuItem['route'])}}">{{$menuItem['name']}}</a>
                </li>
                @endforeach
            </ul>
        </nav>
    </body>
</html>