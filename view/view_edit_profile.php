<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Edit <?= $user->fullname ?> 's profile</title>
        <base href="<?= $web_root ?>"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
        <link rel="stylesheet" href="css/edit_profile.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
                integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
                crossorigin="anonymous"></script>
    </head>
    <body>
        <?php include "view/menu.php"; ?>
        <div class="main">
            <h1>Edit <?= $user->fullname ?> 's profile</h1>

            <form action="user/edit_profile/<?= $user->id ?>" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Mail</th>
                            <th scope="col">Fullname</th>
                            <th scope="col">Title</th>
                            <th scope="col">Birthdate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-Email"><img alt="" src="image/email.png"/></span>
                                    <input type="email" class="form-control" placeholder="Email"
                                           value="<?= $_POST['email'] ?? $user->mail ?>"
                                           name="email">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-Fullname"><img alt="" src="image/id-card.png"/></span>
                                    <input type="text" class="form-control" placeholder="Fullname"
                                           value="<?= $_POST['fullname'] ?? $user->fullname ?>"
                                           name="fullname">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-Title"><img alt="" src="image/title.png"/></span>
                                    <input type="text" class="form-control" placeholder="Title"
                                           value="<?= $_POST['title'] ?? $user->title ?>"
                                           name="title">
                                </div>
                            </td>
                            <td>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-Birthdate"><img alt="" src="image/cupcake.png"/></span>
                                    <input type="date" class="form-control" value="<?= $_POST['birthdate'] ?? $user->birthdate ?>"
                                           name="birthdate">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input class="btn btn-warning" type="submit" value="Save">
            </form>
            <!-- Modal displaying errors -->
            <?php include 'view/view_errors.php'; ?>
        </div>
    </body>
</html>