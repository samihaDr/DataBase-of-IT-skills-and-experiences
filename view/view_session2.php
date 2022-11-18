<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session2</title>
    <base href="<?= $web_root ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">

    <link rel="stylesheet" href="css/timeline.css" type="text/css">
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        let user, userConsulted, skillSelected;
        $(function() {
            userConsulted = '<?= $user_concerned->id ?>';
            user = '<?= $user->id ?>';
            $("#formdelete").remove();
            $("#btn").click(function() {
                skillSelected = $(this).val();
                console.log("Skill Selected =>" + skillSelected);
                deleteNonMastering();
            });
        });

        function deleteNonMastering() {
            $.post("session2/delete_service/", {
                'user_id': userConsulted,
                'skill_selected': skillSelected
            }, function(data) {
                console.log("Data => " + data);
                if (data === "true") {
                    console.log("Skill a suprimer => " + skillSelected);
                    $("#skill_" + skillSelected).remove();
                } else {
                    console.log("blibla");
                }
            })
        }
    </script>
</head>

<body>
    <?php include 'view/menu.php'; ?>

    <h2>Users List 2</h2>
    <div id="usersList">

        <form action="session2/index" method="post" id="user_form"></form>
        <select name="selected_user_id" form="user_form" class="form-select">
            <option selected value="">----Select a user----</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?= $user->id ?>"><?= $user->fullname ?></option>
            <?php endforeach; ?>
        </select>
        <div>
            <input id="btn_show" type="submit" form="user_form" value="Show non mastering skills" class="btn btn-primary">
        </div>

    </div>

    <div>
        <?php if (count($list_masterings) != 0) : ?>
            <form method="post" id="formdelete" hidden></form>
            <?php foreach ($list_masterings as $skill) : ?>
                <div class="form" id="skill_<?= $skill->id ?>">
                    <p>
                        <?= $skill->name ?>
                        <button id="btn" value="<?= $skill->id ?>" type="submit" form="formdelete" formaction="session2/delete/<?= $user_concerned->id ?>/<?= $skill->id ?>">Delete</button>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>