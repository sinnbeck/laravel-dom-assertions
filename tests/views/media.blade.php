<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media</title>
        <link rel="stylesheet" href="/css/app.css">
        <style>
            .hidden { display: none; }
        </style>
    </head>
    <body>
        <div id="gallery" data-testid="gallery-1" aria-hidden="false">
            <img src="/images/cat.jpg" alt="A cat" class="photo hidden" data-id="123" />
            <img src="/images/dog.jpg" alt="A dog" class="photo" data-id="456" />
            <picture>
                <source srcset="/images/bird.webp" type="image/webp">
                <img src="/images/bird.jpg" alt="A bird" class="photo" data-id="789" />
            </picture>
        </div>
        <script type="application/json" id="config">
            {"feature": "media", "enabled": true}
        </script>
    </body>
</html>
