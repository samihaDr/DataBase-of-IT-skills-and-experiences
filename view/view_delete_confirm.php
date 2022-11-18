<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmation delete page</title>
        <base href="<?=$web_root?>"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
        <link rel="stylesheet" href="css/delete.css">
    </head>
    <body>
        <?php include 'view/menu.php';?>
        <div class="delete_card">
            <div class="card border-danger mb-3">
                <div class="card-body text-danger">
                    <img class="card-img-top" src="image/delete-confirmation.png" alt="Card image cap">
                    <h2 class="card-title text-danger">Are you sure ?</h2>
                    <hr>
                    <div class="card-text text-danger">
                        <p><?=$deletable->print_delete_confirm_message()?></p>
                        <p>This process cannot be undone.</p>
                        <br>
                    </div>
                    <form method="post" class="form_delete_confirm">
                        <button type="submit" class="btn btn-danger form-control" formaction="<?=$deletable->get_URL_delete($user_consulted)?>">Delete</button>
                        <button type="submit" class="btn btn-secondary form-control" formaction="<?=$deletable->get_URL_cancel($user_consulted)?>">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>