<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
        <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
        <title>Bienvenido</title>
    </head>
    <body>
        <div class="w3-container s12">
            <div>
                <h1>Bienvenido, <? echo $resultados[0]['nombre']; ?></h1>
                <h3>Su id es: <? echo $resultados[0]['id']; ?></h3>
            </div>
        </div>
    </body>
</html>
