<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <base href="<?= $web_root ?>"/>
        <link rel="stylesheet" href="css/profile.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
        <title><?= $user->fullname ?> 's Profile</title>
    </head>
    <body>
        <?php include('menu.php'); ?>
        <div class="main">
            <h1><?= $user->fullname ?> 's Profile</h1>
            <form>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Mail</th>
                            <th scope="col">Fullname</th>
                            <th scope="col">Title</th>
                            <th scope="col">Birthdate</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-Email"><img alt="" src="image/email.png"/></span>
                                <input disabled type="email" class="form-control" placeholder="Email" value="<?= $user->mail ?>"
                                       name="email">
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-Fullname"><img alt="" src="image/id-card.png"/></span>
                                <input disabled type="text" class="form-control" placeholder="Fullname"
                                       value="<?= $user->fullname ?>"
                                       name="fullname">
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-Title"><img alt="" src="image/title.png"/></span>
                                <input disabled type="text" class="form-control" placeholder="Title" value="<?= $user->title ?>"
                                       name="title">
                            </div>
                        </td>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-Birthdate"><img alt="" src="image/cupcake.png"/></span>
                                <input disabled type="date" class="form-control"
                                       value="<?= $user->birthdate ?>"
                                       name="birthdate">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
            <div class="button">
                <a class="btn btn-warning" href="user/edit_profile/<?=$user->id?>" role="button">Edit</a>
                <a class="btn btn-warning" href="user/change_password" role="button">Change Password</a>
            </div>
        </div>
    </body>
</html>