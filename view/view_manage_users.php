<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manage users</title>
    <base href="<?= $web_root ?>"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <link rel="stylesheet" href="css/manage_users.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>

    <script>
        let table_list;
        let filter;
        let skills;
        let users = [];
        let noUserMessage;
        let user_connected_id = "<?= $user->id ?>";
        let user_connected_role = "<?= $user->role ?>";
        $(function () {
            table_list = $("#user_list");
            table_list.html("");
            filter = $("#filter");
            filter.html("");
            noUserMessage = $("#noUsersMessage");
            getUsers();

            $.get("user/get_skill_service", function (data) {
                skills = data;
                for (let skill of skills) {
                    filter.append("<div class=form-check><input class=form-check-input name=selected_id type=checkbox value=" + skill.id + ">" + skill.name + "</div>");
                }
                $("input.form-check-input").click(function () {
                    var skill_checked = [];
                    $.each($(".form-check-input:checked"), function () {
                        skill_checked.push($(this).val());
                    });

                    $.post("user/get_users_by_skills_service/", {"list_skill": skill_checked}, function (data) {
                        users = data;
                        displayUsers();
                    }, "json").fail(function () {
                        alert("<tr><td>Error post</td></tr>");
                    });
                });
            }, "json");
        });

        function getUsers() {
            $.get("user/get_users_service/", function (values) {
                users = values;
                console.log(users);
                displayUsers();
            }, "json");
        }

        function displayUsers() {
            if (users.length === 0) {
                noUserMessage.html("");
                noUserMessage.html("There is no users with selected skill(s). Please try again.");
                noUserMessage.css("color", "red");
                table_list.html(noUserMessage);
            } else {
                let html = "<table class='table'><thead><tr><th scope='col'>Mail</th>" +
                    "<th scope='col'>Fullname</th>" +
                    "<th scope='col'>Title</th>" +
                    "<th scope='col'>Birthdate</th>" +
                    "<th scope='col'>Role</th>" +
                    "<th scope='col'>Action</th></tr></thead><tbody>";

                for (let user of users) {
                    html += "<tr>";
                    html += "<td><form id='user_form_" + user.id + "' method='post' hidden></form><div class='col-auto'> <input form='user_form_" + user.id + "' type='text' name='email'  class='form-control' value='" + user.mail + "'></div></td>";
                    html += "<td>  <input  form='user_form_" + user.id + "' type='text'  class='form-control' name='fullname' value='" + user.fullname + "'></td>";
                    html += "<td>  <input form='user_form_" + user.id + "' type='text'  class='form-control' name='title' value='" + user.title + "'></td>";
                    html += "<td>  <input form='user_form_" + user.id + "'  type='date'  class='form-control' name='birthdate' value='" + user.birthdate + "'></td>";
                    if (user_connected_id === user.id && user_connected_role === "admin") {
                        html += "<input form='user_form_" + user.id + "' type='text' name='role' value='admin' hidden>"
                        html += "<td> <select disabled form='user_form_" + user.id + "' type='text' name='role' class='form-select' >";
                    } else {
                        html += "<td> <select form='user_form_" + user.id + "' type='text' name='role' class='form-select'>";
                    }
                    if (user.role === "admin") {
                        html += "<option>Admin</option><option>User</option><</select></td>";
                    } else {
                        html += "<option>User</option><option value='admin'>Admin</option></select></td>";
                    }
                    html += "<td><div class='btn-group'><button type='button' class='btn btn-danger dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>Edit</button>" +
                        "<ul class='dropdown-menu'><li><input type='hidden' name='" + user.id + "' value='" + user.id + "'form='user_form_" + user.id + "'>" +
                        "<button class='dropdown-item' form='user_form_" + user.id + "' formaction='user/edit_user/" + user.id + "'>Save</button></li>" +
                        " <li><a class='dropdown-item' href='experience/experiences/" + user.id + "'>Show experience(" + user.experience_count + ")</a></li>" +
                        "<li><a class='dropdown-item' href='skill/user_skills/" + user.id + "'>Show skills(" + user.skill_count + ")</a></li>";
                    if (user_connected_id === user.id && user.role === "admin") {
                        html += "  <li><a class='dropdown-item disabled'href = 'user/delete_confirm_user/" + user.id + "'>Delete</a></li>";
                    } else {
                        html += "<li><a class='dropdown-item' href='user/delete_confirm_user/" + user.id + "'>Delete</a></li>";
                    }
                    html += "</ul></div></td></tr>";
                    html += " </tbody></table>";
                    table_list.html(html);
                }
            }
        }
    </script>
</head>
<body>
<?php include 'view/menu.php'; ?>
<div class="main">
    <h1>Manage users
        <?php if (count($skill_selected) !== 0) : ?>
            <?= " (mastering " . $skill_selected[0]->skill->name . ")" ?>
        <?php endif; ?>
    </h1>
    <span id="noUsersMessage"></span>
    <table id="user_list" class="table">
        <thead>
        <tr>
            <th scope="col">Mail</th>
            <th scope="col">Fullname</th>
            <?php if (count($skill_selected) !== 0) : ?>
                <th scope="col">Skill level</th>
            <?php endif; ?>
            <th scope="col">Title</th>
            <th scope="col">Birthdate</th>
            <th scope="col">Role</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user_consulted) : ?>
            <tr>
                <td>
                    <form id="user_form_<?= $user_consulted->id ?>" method="post" hidden></form>
                    <div class="col-auto">
                        <input form="user_form_<?= $user_consulted->id ?>" type="email" name="email"
                               class="form-control"
                               value="<?= $user_consulted->mail ?>">
                    </div>
                </td>
                <td>
                    <input form="user_form_<?= $user_consulted->id ?>" type="text" name="fullname" class="form-control"
                           value="<?= $user_consulted->fullname ?>">
                </td>
                <?php if (count($skill_selected) !== 0) : ?>
                    <td>
                        <?php $key = array_search($user_consulted->id, array_column($skill_selected, "user")); ?>
                        <input type="hidden" form="user_form_<?= $user_consulted->id ?>" name="skill_selected_id"
                               value="<?= $skill_selected[$key]->skill->id ?>">
                        <input form="user_form_<?= $user_consulted->id ?>" type="text" name="level" class="form-control"
                               value="<?= $skill_selected[$key]->level ?>">
                    </td>
                <?php endif; ?>
                <td>
                    <input form="user_form_<?= $user_consulted->id ?>" type="text" name="title" class="form-control"
                           value="<?= $user_consulted->title ?>">
                </td>

                <td>
                    <input form="user_form_<?= $user_consulted->id ?>" type="date" name="birthdate" class="form-control"
                           value="<?= $user_consulted->birthdate ?>">
                </td>

                <td>
                    <?php if ($user->id === $user_consulted->id && $user->is_admin()) : ?>
                    <input form="user_form_<?= $user_consulted->id ?>" type="text" name="role" value="admin" hidden>
                    <select disabled form="user_form_<?= $user_consulted->id ?>" class="form-select" name="role"
                            aria-label="Default select example">
                        <?php else: ?>
                        <select form="user_form_<?= $user_consulted->id ?>" class="form-select" name="role"
                                aria-label="Default select example">
                            <?php endif; ?>

                            <?php if ($user_consulted->is_admin()) : ?>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            <?php else : ?>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            <?php endif; ?>
                        </select>
                </td>

                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">Edit
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <input type="text" name="user_id" hidden value="<?= $user_consulted->id ?>"
                                       form="user_form_<?= $user_consulted->id ?>">
                                <button class="dropdown-item" form="user_form_<?= $user_consulted->id ?>"
                                    <?php if (!empty($skill_selected)) : ?>
                                        formaction="user/edit_user/<?= $user_consulted->id ?>/<?= $skill_selected[$key]->skill->id ?>"
                                    <?php else : ?>
                                        formaction="user/edit_user/<?= $user_consulted->id ?>"
                                    <?php endif; ?>>Save
                                </button>
                            </li>

                            <li><a class="dropdown-item" href='experience/experiences/<?= $user_consulted->id ?>'>Show
                                    experience
                                    (<?= $user_consulted->get_count_experience_by_user() ?>)</a></li>

                            <li><a class="dropdown-item" href="skill/user_skills/<?= $user_consulted->id ?>">Show skills
                                    (<?= $user_consulted->get_count_skills_by_user() ?>)</a>
                            </li>

                            <li><a class="dropdown-item" href="user/change_password/<?= $user_consulted->id ?>">Change
                                    password</a>
                            </li>

                            <?php if ($user->id === $user_consulted->id && $user->is_admin()) : ?>
                                <li><a class="dropdown-item disabled"
                                       href="user/delete_confirm_user/<?= $user_consulted->id ?>">Delete</a>
                                </li>
                            <?php else : ?>
                                <li><a class="dropdown-item"
                                       href="user/delete_confirm_user/<?= $user_consulted->id ?>">Delete</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (count($users) === 0) : ?>
        <div class="no_users">
            <p></p>
            <!-- display image when list of users is empty -->
            <div class="sorry_img">
                <div class="card border-light mb-3" style="width: 18rem;">
                    <div class="card-body text-warning">
                        <img class="card-img-top" src="image/sorryXl.png" alt="Card image cap">
                        <hr>
                        <p class="card-text text-info">
                        <p>No user has this skill !!</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <h2>Filter users by skill</h2>
    <div id="filter" class="skill_filter">
        <form action="user/users" method="post" id="form_skill" hidden></form>
        <form action="user/users" method="post" id="form_reset" hidden></form>
        <select name="selected_skill_id" form="form_skill" class="form-select select_filter">
            <option value="">Select a skill</option>
            <?php foreach ($skills as $skill) : ?>
                <?php if ($skill_filtered_id !== null && $skill->id === $skill_filtered_id) : ?>
                    <option selected value="<?= $skill->id ?>"><?= $skill->name ?></option>
                <?php else : ?>
                    <option value="<?= $skill->id ?>"><?= $skill->name ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <input type="submit" form="form_skill" value="Filter" class="btn btn-primary">
        <a class="btn btn-secondary" href="user/users" role="button">Reset</a>
    </div>
    <!-- Modal displaying errors -->
    <?php include 'view/view_errors.php'; ?>
</div>
</body>
</html>