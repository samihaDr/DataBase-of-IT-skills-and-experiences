<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Change <?= $user_selected->fullname ?> 's password</title>
        <base href="<?= $web_root ?>"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
        <link rel="stylesheet" href="css/change_password.css">
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
            <?php
            $url_cancel = "user/profile";
            $url_save = "user/change_password";
            $user_fullname = $user->fullname;
            ?>

            <?php if ($user_selected) : ?>
                <?php
                $url_cancel = "user/users";
                $url_save = "user/change_password/" . $user_selected->id;
                $user_fullname = $user_selected->fullname;
                ?>
            <?php endif; ?>

            <h1>Change <?= $user_fullname ?>'s password</h1>
            <form action="<?= $url_save ?>" method="post">
                <table>
                    <tr>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-Password"><img alt="" src="image/password.png"/></span>
                                <input type="password" class="form-control" placeholder="Insert your new password"
                                       value="<?= $new_password ?>" name="new_password">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-ConfirmPassword"><img alt="" src="image/password.png"/></span>
                                <input type="password" class="form-control" placeholder="Confirm your new password"
                                       value="<?= $new_password_confirm ?>" name="new_password_confirm">
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="button">
                    <input class="btn btn-warning" type="submit" value="Save">
                    <a href="<?= $url_cancel ?>" class="btn btn-warning" role="button" aria-pressed="true">Cancel</a>
                </div>
            </form>
            <!-- Modal displaying errors -->
            <?php include 'view/view_errors.php'; ?>
        </div>
    </body>
</html>