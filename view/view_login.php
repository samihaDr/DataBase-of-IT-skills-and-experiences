<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <base href="<?= $web_root ?>" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
        <link rel="stylesheet" href="css/signup.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    </head>

    <body>
        <?php include 'view/menu.php'; ?>
        <div class="main">
            <h1>Login</h1>
            <form action="user/login" method="post">
                <div class="input-group mb-2">
                    <span class="input-group-text" id="basic-Email"><img alt="" src="image/email.png" /></span>
                    <input type="email" class="form-control" placeholder="Email" value="<?= $mail ?>" name="mail">
                </div>

                <div class="input-group mb-2">
                    <span class="input-group-text" id="basic-Password"><img alt="" src="image/password.png" /></span>
                    <input type="password" class="form-control" placeholder="Password" value="<?= $password ?>" name="password">
                </div>

                <div class="mb-1">
                    <div class="btn-add-container d-grid">
                        <input type="submit" value="Login" class="btn btn-primary">
                    </div>
                </div>
            </form>
            <!-- Modal displaying errors -->
            <?php include 'view/view_errors.php'; ?>
        </div>
    </body>
</html>



