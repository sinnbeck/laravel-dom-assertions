<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Nesting</title>
    </head>
    <body>
        <nav id="nav"></nav>
        <div>
            <span class="bar foo">Foo</span>
            <div class="foobar">
                <div x-data="foobar">
                    <div class="deep">
                        <span></span>
                    </div>
                </div>
                <ul>
                    <li x-data="foobar"></li>
                    <li x-data="foobar"></li>
                </ul>
            </div>
        </div>
    </body>
</html>