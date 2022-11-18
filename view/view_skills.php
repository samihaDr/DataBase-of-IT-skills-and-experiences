<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills</title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>

    <script>
        const NB_STARS = 5;
        let hasParameter = "<?= $has_parameter ?>";
        let userConnectedAdmin = "<?= $user->is_admin() ?>";
        let userConnectedId = "<?= $user->id ?>";

        let userId, skill_lvl_range;

        $(function() {
            userId = getConcernedUserId();
            skill_lvl_range = $(".lvl_range");
            getMasteredSkills();
            buildAddMasteringSkill();
        });

        function getConcernedUserId() {
            let userId;
            if (hasParameter === "true") {
                userId = $("input.user_view_id").val();
            } else if (hasParameter === "false") {
                userId = "<?= $user->id ?>";
            }
            return userId;
        }

        function getMasteredSkills() {
            $('.edit_delete_mastering').html("");

            $.post("skill/get_mastered_skills_service", {
                "user_id": userId
            }, function(data) {
                let masteredSkills = data;
                if(masteredSkills.length > 0){
                    let drawStars = drawingStars();
                    $('.edit_delete_mastering').append('<ul class="list-group">');

                    // build list mastered skill with updating rated stars and deleting  
                    $.each(masteredSkills, function(idx, masteredSkill) {
                        buildMasteredSkillHtml(masteredSkill.skill.name, masteredSkill.skill.id, drawStars, idx);
                        colorStars(masteredSkill.level, idx);
                        updateSkillManager(idx, masteredSkill.user, masteredSkill.skill.id);
                        deleteSkillManager(idx, masteredSkill.user, masteredSkill.skill.id, masteredSkill.skill.name);
                    });
                    $('.edit_delete_mastering').append('</ul>');
                } else {
                    $('.no_mastering').remove();
                    $('.edit_delete_mastering_parent').append('<div class="edit_delete_mastering"></div>');
                    $('.edit_delete_mastering').append('<ul class="list-group">');
                    $('.edit_delete_mastering').append('</ul>');
                }
            }, "json");
           
        }

        function buildMasteredSkillHtml(masteredSkillName, masteredSkillId, drawStars, index) {
            let html = '';
            html += '<li id="skill_item' + index + '"class="list-group-item li_mastering_item">';
            html += '<div class="w-75">';
            html += '<div class="badge bg-primary badge_name_level">';
            html += '<div class="skill-name">' + masteredSkillName + '</div>';
            html += '<div id="skill' + index + '" data-attr-skill-id = "' + masteredSkillId + '">' + drawStars + '</div>';
            html += '</div> </div>';
            html += '<div><img class="delete_action" id="delete' + index + '" data-attr-delete-skill-id = "' + masteredSkillId + '" src="image/bin.png" alt="delete_button" ></div>';
            html += '</li>';
            
            $('.list-group').append(html);
        }

        
        function drawingStars() {
            let drawStars = '<span class="fa fa-star star1" data-value="1"></span>';
            drawStars += '<span class="fa fa-star star2" data-value="2"></span>';
            drawStars += '<span class="fa fa-star star3" data-value="3"></span>';
            drawStars += '<span class="fa fa-star star4" data-value="4"></span>';
            drawStars += '<span class="fa fa-star star5" data-value="5"></span>';

            return drawStars;
        }

        function colorStars(nbStars, skillIndex) {
            let stars = [];
            let concatenateStars = "";

            for (let i = 1; i <= parseInt(nbStars); i++) {
                let star = '#skill' + skillIndex + ' ' + '.star' + i;
                stars.push(star);
                concatenateStars = stars.join();
            }
            $(concatenateStars).addClass("color_yellow");
        }

        function updateSkillManager(idx, userId, skillId){
            for (let j = 1; j <= NB_STARS; j++) {
                // color stars on hover
                colorStarsOnHover(idx, j);

                // update level of mastered skill
                $("#skill" + idx + " .star" + j).click(function() {
                    let level = j;
                    updateMasteredSkill(idx, userId, skillId, level);
                });
            }
        }

        function updateMasteredSkill(idx, userId, skillId, level) {
            $.post("skill/update_mastered_skill_service", {
                "user_id": userId,
                "skill_id": skillId,
                "level": level
            }, function(data) {
                if (data === "true" && ((userConnectedAdmin !== "" && hasParameter === "true") ||
                        (userConnectedId === userId && hasParameter === "false"))) {
                    console.log("success");
                    removeColorStars(idx);
                    colorStars(level, idx);
                } else {
                    console.log("failed");
                }
            });
        }

        function deleteSkillManager(idx, userId, skillId, skillName){
            changeCursorWhenHoveringForDeleting(idx);
            
            // delete a mastered skill
            $("#delete" + idx).click(function() {
                deleteMasteredSkill(idx, userId, skillId, skillName);
            });
        }

        function colorStarsOnHover(i, nbStarsHovered) {
            let stars = [];
            let concatenateStars = "";
            for (let j = 1; j <= nbStarsHovered; j++) {
                let star = '#skill' + i + ' ' + '.star' + j;
                stars.push(star);
                concatenateStars = stars.join();

                $("#skill" + i + " .star" + nbStarsHovered).hover(function() {
                    $(this).css('cursor', 'pointer');
                    $(concatenateStars).addClass('color_red');
                }, function() {
                    $(concatenateStars).removeClass('color_red');
                    $(this).css('cursor', 'auto');
                });
            }
        }

        function removeColorStars(skillIndex) {
            let stars = [];
            let concatenateStars = "";
            for (let i = 1; i <= NB_STARS; i++) {
                let star = '#skill' + skillIndex + ' ' + '.star' + i;
                if ($(star).hasClass("color_yellow")) {
                    $(star).removeClass("color_yellow");
                }
            }
        }

        function changeCursorWhenHoveringForDeleting(index) {
            $("#delete" + index).hover(function() {
                $(this).css('cursor', 'pointer');
            }, function() {
                $(this).css('cursor', 'auto');
            });
        }

        function addToSelectOption(skillId, skillName) {
            $('.form-select').append($('<option>').val(skillId).text(skillName));
        }

        function deleteMasteredSkill(idx, userId, skillId, skillName) {
            $.post("skill/delete_mastered_skill_service", {
                "user_id": userId,
                "skill_id": skillId
            }, function(data) {
                if (data === "true" && ((userConnectedAdmin !== "" && hasParameter === "true") ||
                        (userConnectedId === userId && hasParameter === "false"))) {
                    console.log("success");
                    $("#skill_item" + idx).remove();
                    addToSelectOption(skillId, skillName);
                } else {
                    console.log("failed");
                }
            })
        }

        // adapt this function
        function buildAddMasteringSkill() {
            let level, selectedSkillId;
            $("#add_mastering_btn").remove();
            skill_lvl_range.html("");
            skill_lvl_range.css('font-size', '125%').append(drawingStars());

            for (let i = 1; i <= NB_STARS; i++) {
                colorStarsOnHover(skill_lvl_range.attr("data-attr-skill-nb"), i);
                //get level
                $(".lvl_range .star" + i).click(function() {
                    level = i;
                    selectedSkillId = $("#mastering_skill_option option:selected").val();
                    addMasteringSkill(level, selectedSkillId);
                });
            }
        }

        function addMasteringSkill(level, selectedSkillId) {
            $.post("skill/add_mastering_via_skill_service", {
                "user_id": userId,
                "skill_id": selectedSkillId,
                "level": level
            }, function(data) {
                if (data === "true" && ((userConnectedAdmin !== "" && hasParameter === "true") ||
                        (userConnectedId === userId && hasParameter === "false"))) {
                    let drawStars = drawingStars();
                    let lastIndexSkill =  skill_lvl_range.attr("data-attr-skill-nb"); 
                    let nextIndexSkill = parseInt(lastIndexSkill) + 1;

                    // add new skill to the mastered skill list
                    buildMasteredSkillHtml($('#mastering_skill_option option:selected').text(), selectedSkillId, drawStars, lastIndexSkill);
                    colorStars(level, lastIndexSkill);

                    // on retire de la liste l'option selectionné
                    $('#mastering_skill_option option:selected').remove();

                    // give new id to the lvl_range add section 
                    skill_lvl_range.attr('id', 'skill' + nextIndexSkill);
                    skill_lvl_range.attr('data-attr-skill-nb', nextIndexSkill);
                    // when id is given, we can now remove color
                    removeColorStars(nextIndexSkill);

                    for (let j = 1; j <= NB_STARS; j++) {
                        // remove hover event so it can work properly 
                        $('#skill' + nextIndexSkill + " .star" + j).off('mouseenter mouseleave');

                        // add color hovering for the stars in the adding section and for the new skill added in the mastered skill list 
                        colorStarsOnHover(nextIndexSkill, j);
                        colorStarsOnHover(lastIndexSkill, j);

                        // update section
                        $("#skill" + lastIndexSkill + " .star" + j).click(function() {
                            let level = j;
                            updateMasteredSkill(lastIndexSkill, userId, selectedSkillId, level);
                        });
                    }

                    // delete section 
                    changeCursorWhenHoveringForDeleting(lastIndexSkill);
                    $("#delete" + lastIndexSkill).click(function() {
                        let skillName = $('#skill_item' + lastIndexSkill + ' ' + '.skill-name').text();
                        deleteMasteredSkill(lastIndexSkill, userId, selectedSkillId, skillName);
                    });

                } else {
                    console.log("failed");
                }
            });
        }
    </script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <link rel="stylesheet" href="css/skills.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>

    <?php include 'view/menu.php'; ?>

    <div class="main">
        <h1 class="mastering_skill_title">
            <?php if ($user_view) : ?>
                <?= $user_view->fullname . ', ' . $user_view->title ?>
                <input type="hidden" value="<?= $user_view->id ?>" class="user_view_id">
            <?php else : ?>
                <?= $user->fullname . ', ' . $user->title ?>
            <?php endif; ?>
        </h1>

        <div class="manage_masterings">

            <div class="edit_delete_mastering_parent">

                <h2>Skills</h2>
                <?php $idx = 0; ?>

                <?php if (count($mastering_skills) != 0) : ?>
                    <div class="edit_delete_mastering">
                        <ul class="list-group">

                            <!-- mastering skills list -->
                            <?php foreach ($mastering_skills as $mastering) : ?>

                                <li class="list-group-item li_mastering_item">
                                    <form id=form_mastering<?= $idx ?> method="post" hidden></form>
                                    <div class="w-50">
                                        <div class="badge bg-primary badge_name_level">
                                            <div class="skill-name">
                                                <?= $mastering->skill->name ?>
                                            </div>
                                            <div class="badge bg-warning">
                                                <?= $mastering->level ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- mastering skills buttons up, down and delete -->
                                    <div class="mastering_skills_up_down">

                                        <?php $url_edit = "skill/edit_mastering_skill_up"; ?>
                                        <?php $user_edit_skill_id = $mastering->user; ?>

                                        <?php if ($user_view && $user->is_admin() && $user_view->id !== $user->id) : ?>
                                            <?php $user_edit_skill_id = $user_view->id; ?>
                                            <?php $url_edit = "skill/edit_mastering_skill_up/$user_edit_skill_id"; ?>
                                        <?php endif; ?>

                                        <input type="hidden" name="mastering_skill_id" form="form_mastering<?= $idx ?>" value="<?= $mastering->skill->id ?>">
                                        <input type="hidden" name="mastering_user_id" form="form_mastering<?= $idx ?>" value="<?= $user_edit_skill_id ?>">

                                        <?php if ($mastering->level == 1) : ?>
                                            <button type="submit" form="form_mastering<?= $idx ?>" formaction=<?= $url_edit ?> class="btn btn-outline-primary manage">
                                                <img src="image/arrow-up.png" alt="arrow_up_button">
                                            </button>
                                        <?php elseif ($mastering->level == 5) : ?>
                                            <button type="submit" form="form_mastering<?= $idx ?>" formaction="skill/edit_mastering_skill_down" class="btn btn-outline-primary manage">
                                                <img src="image/arrow-downJ.png" alt="arrow_down_button">
                                            </button>
                                        <?php elseif ($mastering->level > 1 && $mastering->level < 5) : ?>
                                            <button type="submit" form="form_mastering<?= $idx ?>" formaction="skill/edit_mastering_skill_up" class="btn btn-outline-primary manage">
                                                <img src="image/arrow-up.png" alt="arrow_up_button">
                                            </button>

                                            <button type="submit" form="form_mastering<?= $idx ?>" formaction="skill/edit_mastering_skill_down" class="btn btn-outline-primary manage">
                                                <img src="image/arrow-downJ.png" alt="arrow_down_button">
                                            </button>
                                        <?php endif; ?>

                                        <button type="submit" form="form_mastering<?= $idx ?>" formaction="skill/delete_confirm_mastering/<?= $mastering->user ?>/<?= $mastering->skill->id ?>" class="btn btn-outline-danger manage">
                                            <img src="image/delete-button.png" alt="delete_button">
                                        </button>

                                    </div>
                                </li>

                                <?php $idx++; ?>
                            <?php endforeach; ?>

                        </ul>
                    </div>

                <?php else : ?>
                    <div class="no_mastering">

                        <!-- display image when list of mastering skills is empty -->
                        <div class="sorry_img">
                            <div class="card border-light mb-3" style="width: 18rem;">
                                <div class="card-body text-warning">
                                    <img class="card-img-top" src="image/sorryXl.png" alt="Card image cap">
                                    <hr>
                                    <p class="card-text text-info">
                                    <p>You have no skills related to your profile !!</p>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

            </div>

            <!--add new skill to mastering -->
            <div class="add_mastering_parent">
                <h2>Add a skill</h2>

                <div class="add_mastering">
                    <?php $url_add = "skill/add_mastering_skill"; ?>
                    <?php if ($user_view && $user->is_admin() && $user_view->id !== $user->id) : ?>
                        <?php $user_add_skill_id = $user_view->id; ?>
                        <?php $url_add = "skill/add_mastering_skill/$user_add_skill_id/"; ?>
                    <?php endif; ?>
                    <form action="<?= $url_add ?>" method="post" class="add_new_skill">

                        <!-- list of available skills that could be mastered -->
                        <label class="form-label mt-3">Skill :</label>
                        <select class="form-select" aria-label="" name="selected_skill" id="mastering_skill_option">
                            <?php foreach ($user_view->get_free_skill() as $skill) : ?>
                                <option value='<?= $skill->id ?>'>
                                    <?= $skill->name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- skill level range -->
                        <div id="skill<?= count($mastering_skills) ?>" data-attr-skill-nb="<?= count($mastering_skills) ?>" class="mt-3 lvl_range">
                            <label for="level_range" class="form-label">Level (1-5)</label>
                            <input type="range" name="level" class="form-range" min="1" max="5" id="level_range">
                        </div>

                        <button id="add_mastering_btn" type="submit" class="btn btn-outline-primary mt-3">Add</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</body>

</html>