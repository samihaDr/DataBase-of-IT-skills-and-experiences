<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Jobijoba</title>
    <base href="<?= $web_root ?>" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <link rel="stylesheet" href="css/welcome.css">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'view/menu.php'; ?>
    <div class="main">
        <div class="card-light">
            <div class="img-card"><img src="image/cv-logo.png" class="card-img-top" alt="Logo"></div>
            <div class="card-body">
                <div class="card-title">
                    <span class="p-3">
                        <span style="color:#139898">J</span>
                        <span style="color:#dc4f4f">o</span>
                        <span style="color:#eab960">b</span>
                        <span style="color:#c14bdb">i</span>
                        <span style="color:#dc4f4f">J</span>
                        <span style="color:#c14bdb">o</span>
                        <span style="color:#139898">b</span>
                        <span style="color:#eab960">a</span>
                    </span>
                </div>
            </div>
            <div class="card-text">
                <p>Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <div class="card-button">
                <a href="user/login" class="btn btn-primary">Login</a>
                <a href="user/signup" class="btn btn-secondary">Sign Up</a>
            </div>

        </div>
    </div>
</body>

</html>