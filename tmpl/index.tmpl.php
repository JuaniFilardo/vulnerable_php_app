<!DOCTYPE html>
<html>
    <head>
        <title>Ingresar</title>
        <meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
        <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
        <link rel="stylesheet" href="<? echo $WEB_PATH; ?>css/styleLogin.css">
    </head>

    <body>
        <div class="w3-container s12">
            <div class="form">
                <div id="login">
                    <h1>Bienvenido</h1>

                    <form action="src/ctrl/index.ctrl.php" method="post">
                    <div class="field-wrap">
                        <input type="text" name="username" required autocomplete="off" placeholder="Usuario"/>
                    </div>

                    <div class="field-wrap">
                      <input type="text" name="password" required autocomplete="off" placeholder="Contraseña"/>
                    </div>

                    <button class="button button-block"/>Ingresar</button>

                    </form>
                </div>
            </div> <!-- /form -->
        </div>  <!-- /container-->
    </body>
</html>
