<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <title>Registracija</title>
</head>
<body>
    <div id="bg"></div>
        <form action="register.php" method="post">
            <div class="form-field">
                <h1 class="naslov">Registracija</h1>
            </div>
            <div class="form-field">
                <input type="text" name="ime" placeholder="Ime" required/>
            </div>
            <div class="form-field">
                <input type="text" name="priimek" placeholder="Priimek" required/>
            </div>
            <div class="form-field">
                <input type="email" name="email" placeholder="E-mail" required/>
            </div>
            <div class="form-field">
                <input type="password" name="geslo" placeholder="Geslo" required/>
            </div>
            <div class="form-field">
                <button class="btn" type="submit">Prijavi se</button>
            </div>
            <div class="form-field">
            <a class="prijava" href="prijava.php">Ste že registrirani? Prijavite se.</a>
            </div>
        </form>
    </div>
    <?php
    session_start();
    require_once 'alert.php';
    ?>
</body>
</html>