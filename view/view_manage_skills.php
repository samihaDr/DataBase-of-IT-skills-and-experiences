<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage skills</title>
    <base href="<?= $web_root ?>" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/manage_skills.css">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'view/menu.php'; ?>
    <div class="main">

        <h1>Manage skills</h1>

        <table class="table table-hover container-fluid">
            <thead>
                <tr>
                    <th class="input_text_column">Name</th>
                    <th class="action_buttons">Actions</th>
                    <th class="info_references">Infos</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>
                        <form id="skill_form" method="post" hidden></form>
                        <input form="skill_form" type="text" class="form-control" name="name" placeholder="Name" value="<?= $name ?>">
                    </td>

                    <td colspan="2">
                        <button form="skill_form" type="submit" formaction="skill/add_skill" class="btn btn-outline-success manage">
                            <img src="image/add.png" alt="add_button">
                        </button>
                    </td>
                </tr>

                <?php $idx = 0; ?>
                <?php foreach ($skills as $skill) : ?>
                    <tr>
                        <!-- disabled inputs and buttons if skill is referenced in other tables -->
                        <?php if ($user_by_skill_counter[$idx] > 0 || $experience_by_skill_counter[$idx] > 0) : ?>
                            <td>
                                <form id="skill_form<?= $idx ?>" method="post" hidden></form>
                                <input type="text" class="form-control" value="<?= $skill->name ?>" disabled>
                            </td>

                            <td class="buttons_edition">
                                <input type="hidden" form="skill_form<?= $idx ?>" name="skill_id" value="<?= $skill->id ?>">
                                <button type="submit" form="skill_form<?= $idx ?>" formaction="skill/edit_skill" class="btn btn-outline-primary manage" disabled>
                                    <img src="image/edit.png" alt="edit_button">
                                </button>
                                <button type="submit" form="skill_form<?= $idx ?>" class="btn btn-outline-danger manage" disabled>
                                    <img src="image/delete.png" alt="delete_button">
                                </button>
                            </td>
                            <!-- enabled inputs and buttons if skill is not referenced in other tables -->
                        <?php else : ?>
                            <td>
                                <form id="skill_form<?= $idx ?>" method="post" hidden></form>
                                <input type="text" form="skill_form<?=$idx?>" class="form-control" name="name" value="<?= $skill->name ?>">
                            </td>

                            <td class="buttons_edition">
                                <input type="hidden" form="skill_form<?= $idx ?>" name="skill_id" value="<?= $skill->id ?>">
                                <button type="submit" form="skill_form<?= $idx ?>" formaction="skill/edit_skill" class="btn btn-outline-primary manage">
                                    <img src="image/edit.png" alt="edit_button">
                                </button>
                                <a href="skill/delete_confirm_skill/<?=$skill->id?>" class="btn btn-outline-danger" role="button" aria-pressed="true">
                                    <img src="image/delete.png" alt="delete_button">
                                </a>
                            </td>
                        <?php endif; ?>

                        <td>
                            Mastered by <a href="user/users/by_skill/<?= $skill->id ?>" class="link-primary"><?= $user_by_skill_counter[$idx] ?> user(s)</a>,
                            Used in <?= $experience_by_skill_counter[$idx] ?> experience(s).
                        </td>
                    </tr>
                    <?php $idx++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal displaying errors -->
        <?php include 'view/view_errors.php'; ?>
    </div>
</body>
</html>