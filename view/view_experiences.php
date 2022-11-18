<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experiences</title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <link rel="stylesheet" href="css/experiences.css">
    <link href="lib/jquery-ui-1.13.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="lib/jquery-ui-1.13.1/jquery-ui.theme.min.css" rel="stylesheet" type="text/css" />
    <link href="lib/jquery-ui-1.13.1/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="lib/jquery-ui-1.13.1/jquery-ui.min.js" type="text/javascript"></script>
    <script>
        let doubleSliderAndModal = "<?= Configuration::get("double_slider_and_modal") ?>";
        let userConsultedId = "<?= $user_consulted->id ?>";
        let userAdmin = "<?= $user->is_admin() ?>";
        let filterSec, startY, endY, filter;
        let toDelete;
        let userId = "<?= $user->id ?>";
        let experiences;
        let userConnectedFullname = "<?= $user->fullname ?>";
        let html = " ";
        let userConsultedFullname;
        let hasParameter = "<?= $has_parameter ?>";
        const MONTH_NAMES_SHORT = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        $(function() {
            userConsultedFullname = $(".user_consulted_fullname").val();
            if (doubleSliderAndModal === "on") {
                getIteration3();
            } else {
                getIteration2()
            }
            //add mastering skill section
            $("span.not-mastering").click(function() {
                let skillId = $(this).attr("data-skill-id-attr");
                postMasteringSkill(skillId, userConsultedId);
            });

            hoverOnNonMasteredSkill();
        });

        function getIteration2() {
            generateFilterView();
            //filter section
            $(".filter").change(function() {
                checkVal();
                getFilteredExperiences();
            });
        }

        function getIteration3() {
            getDoubleSliderFeature();
            getDialogModalFeature();
        }

        function getDialogModalFeature() {
            getExperiences();
        }

        function getDoubleSliderFeature() {
            let filterSlider = $("#filter-slider");
            let sliderRange = $("#slider-range");
            filterSlider.removeAttr("hidden");

            sliderRange.slider({
                range: true,
                min: 1900,
                max: 2100,
                values: [1950, 2050],
                slide: function(event, date) {
                    $("#date").val(date.values[0] + " to " + date.values[1]);
                },
                stop: function(event, date) {
                    let startVal = date.values[0];
                    let endVal = date.values[1];
                    $.post("experience/get_experiences_by_filter_service", {
                            "user_id": userConsultedId,
                            "start_year": startVal,
                            "end_year": endVal
                        },
                        function(data) {
                            html = " ";

                            let listExperience = data;
                            viewReconstitution(listExperience, html);

                        }, "json");
                }
            });
            $("#date").val(sliderRange.slider("values", 0) +
                " to " + sliderRange.slider("values", 1));
        }

        function getMonthNameShort(date) {
            return MONTH_NAMES_SHORT[date.getMonth()];
        }

        function confirmDialog(experience_id) {
            if (doubleSliderAndModal === "on") {
                $("#btnDelete" + experience_id).attr("href", 'javascript:deleteExperienceConfirm(' + experience_id + ')');
                $("#btnDelete" + experience_id).click(function() {
                    deleteExperienceConfirm(experience_id);
                })
            } else {
                if (userConsultedId)
                    $("#btnDelete" + experience_id).attr("href", "experience/delete_confirm_experience/" + experience_id);
                else
                    $("#btnDelete" + experience_id).attr("href", "experience/delete_confirm_experience/" + userConsultedId + '/' + experience_id);
            }

        }

        function deleteExperienceConfirm(toDelete) {
            var experienceToDelete = experiences.find(function(element) {
                return element.id === toDelete;
            });

            if (experienceToDelete !== undefined) {

                $('#message_to_delete_title').text(experienceToDelete.title);
                $('#message_to_delete_place_name').text(experienceToDelete.place.name);
                $('#message_to_delete_place_city').text(experienceToDelete.place.city);
                if (hasParameter === "true") {
                    $('#message_to_delete_user').text(userConsultedFullname);
                } else if (hasParameter === "false") {
                    $('#message_to_delete_user').text(userConnectedFullname);
                }

                $('#confirm_dialog').removeAttr("hidden");
                $('#confirm_dialog').dialog({
                    resizable: false,
                    height: 300,
                    width: 600,
                    modal: true,
                    autoOpen: true,
                    buttons: {
                        Confirm: function() {
                            deleteExperience(toDelete);
                            $(this).dialog("close");
                        },
                        Cancel: function() {
                            $(this).dialog("close");
                        }
                    }
                });

                return false;
            }
        }

        function deleteExperience(toDelete) {
            var experienceToDelete = experiences.findIndex(function(el, idx, arr) {
                return el.id === toDelete;
            });
            $.post("experience/delete_service", {
                    "id": toDelete,
                    "user_id": userConsultedId
                },
                function(data) {
                    if (data) {
                        $("#experience" + toDelete).remove();
                    }
                }).fail(function() {
                alert("Error encountered while retrieving the experience!")
            });
        }

        function getExperiences() {
            //let experiences;
            $.post("experience/get_experiences_service", {
                "user_id": userConsultedId
            }, function(data) {
                experiences = data;
                viewReconstitution(experiences);
            }, "json");
        }

        function viewReconstitution(list, html) {
            $("#experiences").html("");
            html = "";
            if (list.length != 0) {
                html += '<div class="accordion" id="accordionPanelsStayOpen">';
                $.each(list, function(index, experience) {
                    html += displayExperience(experience, index);
                })
                html += '</div>';
                $("#experiences").append(html);
            } else {
                $(".info-mastered-skill").remove();
            }

            $.each(list, function(index, experience) {
                confirmDialog(experience.id);
            });

            $("span.not-mastering").click(function() {
                let skillId = $(this).attr("data-skill-id-attr");
                postMasteringSkill(skillId, userConsultedId);
            });

            hoverOnNonMasteredSkill();
        }

        function postMasteringSkill(skillId, userId) {
            console.log("UserId : " + userId);
            console.log("skillId : " + skillId);

            $.post("skill/add_mastering_via_experience_service", {
                "skill_id": skillId,
                "user_id": userId
            }, function(data) {
                if (data === "true" && ((userAdmin !== "" && hasParameter === "true") ||
                        (userConsultedId === userId && hasParameter === "false"))) {
                    $("span[data-skill-id-attr=" + skillId + "]").removeClass("bg-warning text-dark not-mastering").addClass("bg-primary mastering");
                    $("span[data-skill-id-attr=" + skillId + "]").off('mouseenter');
                    console.log("success adding mastered skill !");
                } else
                    console.log("Nice try but no dice :)");
            });
        }

        function hoverOnNonMasteredSkill() {
            $("span.not-mastering").hover(function() {
                $(this).css("font-size", "0.90em");
                $(this).css('cursor', 'pointer');
                $(this).append('<img src="image/plus.svg" width="15" height="15" class="append_plus" alt="plus_icon">');
            }, function() {
                $(this).css("font-size", "0.75em");
                $(this).css('cursor', 'auto');
                $(".append_plus").remove();
            });

        }

        function generateFilterView() {
            filterSec = $("#filterSection");
            filterSec.removeAttr("hidden");
            startY = $("#startY");
            endY = $("#endY");
            filter = $(".filter");
            filter.css("cursor", "pointer");
            $("#errYear").html("");
            filter.mouseenter(function() {
                filter.css("cursor", "pointer");
            });
        }


        function getFilteredExperiences() {
            let filteredExperiences;
            let html = "";
            $.post("experience/get_experiences_by_filter_service", {
                "user_id": userConsultedId,
                "start_year": startY.val(),
                "end_year": endY.val()
            }, function(data) {
                filteredExperiences = data;
                viewReconstitution(filteredExperiences);
            }, "json");
        }

        function checkVal() {
            $("#errYear").html("");
            if ($.trim(endY.val()) != '') {
                if (startY.val() < 1900 || startY.val() > 2999 || endY.val() < 1900 || endY.val() > 2999) {
                    $("#errYear").html("You are out of range").css({
                        "color": "red"
                    });
                } else if (endY.val() < startY.val()) {
                    $("#errYear").html("Please enter the correct range").css({
                        "color": "red"
                    });
                }
            } else {
                endY.val(2099);
            }
        }

        function getMonthNameShort(date) {
            return MONTH_NAMES_SHORT[date.getMonth()];
        }

        function displayExperience(experience, index) {
            let html = "";
            const startDate = new Date(experience.start);
            let experience_id = experience.id;
            console.log(" ExpId : " + experience_id);
            html += '<div class="accordion-item" id="experience' + experience_id + '">';
            html += '<h2 class="accordion-header" id="panelsStayOpen-heading' + index + '" >';
            html += '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' + index + '" aria-expanded="true" aria-controls="panelsStayOpen-collapse' + index + '">' +
                experience.title + " at " + experience.place.name + " (" + experience.place.city + ")" +
                '</button></h2>';

            html += '<div id="panelsStayOpen-collapse' + index + '" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-heading' + index + '">';
            html += '<div class="accordion-body">';

            html += '<p>' + experience.title + " at " + experience.place.name + " (" + experience.place.city + ")" + ' from ' + getMonthNameShort(startDate) + " " + startDate.getFullYear();

            if (experience.stop != null) {
                const stopDate = new Date(experience.stop);
                html += ' to ' + getMonthNameShort(stopDate) + " " + stopDate.getFullYear();
            }
            html += '</p>';

            if (experience.description)
                html += '<p><i>' + experience.description + '</i></p>';

            if (experience.skills.length != 0) {
                html += '<p class="used_skills"> Used skills : ';
                for (skill of experience.skills) {
                    if (experience.masterings.includes(skill.id)) {
                        html += '<span class="badge rounded-pill bg-primary mastering">';
                    } else {
                        html += '<span class="badge rounded-pill bg-warning text-dark not-mastering"  data-skill-id-attr="' + skill.id + '">';
                    }

                    html += skill.name + '</span>';

                }
                html += '</p>';
            }

            html += '</div>';

            html += '<div class="manage-buttons">';

            let urlDelete = "experience/delete_confirm_experience/" + experience.id;
            let urlEdit = "experience/edit/" + experience.id;

            if (userConsultedId && userAdmin && userId !== userConsultedId) {
                urlDelete = "experience/delete_confirm_experience/" + userConsultedId + '/' + experience.id;
                urlEdit = "experience/edit/" + userConsultedId + '/' + experience.id;
            }

            html += '<a href="' + urlEdit + '" class="btn btn-primary edit-btn" role="button" aria-pressed="true"> Edit </a>';
            html += '<a href="' + urlDelete + '" class="btn btn-danger delete-btn" role="button" aria-pressed="true" id="btnDelete' + experience_id + '"> Delete </a>';
            html += '</div> </div> </div>';

            return html;
        }
    </script>
</head>

<body>
    <?php include 'view/menu.php'; ?>

    <div class="main">
        <div class="work_title">
            <div class="full_name">
                <?php $user_id = $user->id; ?>
                <?php if ($user_consulted) : ?>
                    <?php $user_id = $user_consulted->id; ?>
                    <h1><?= $user_consulted->fullname . ', ' . $user_consulted->title ?></h1>
                    <input type="hidden" value="<?= $user_consulted->id ?>" class="user_consulted_id">
                    <input type="hidden" value="<?= $user_consulted->fullname ?>" class="user_consulted_fullname">
                <?php else : ?>
                    <h1><?= $user->fullname . ', ' . $user->title ?></h1>
                <?php endif; ?>
            </div>
            <a href="experience/view_timeline/<?= $user_id ?>" class="btn btn-outline-primary view-timeline-btn" role="button" aria-pressed="true">View Timeline</a>
        </div>

        <div id="filterSection" hidden>
            <h3>Filters </h3>

            <label id="expStartYear"> Start year : </label>
            <input type="number" class="filter" id="startY" name="startY" placeholder="1900" min=1900 max=2099 step=1>

            <label id="expEndYear"> End year :</label>
            <input type="number" class="filter" id="endY" name="endY" placeholder="2099" min=1900 max=2099 step=1>

            <span id="errYear"></span>
        </div>
        <h2>Experiences</h2>
        <div id="filter-slider" hidden>
            <h2>Filter </h2>
            <p>
                <label for="date">Year range:</label>
                <input type="text" id="date" readonly style="border:0; color:blue; font-weight:bold;">
            </p>
            <div id="slider-range"></div>
        </div>


        <?php if (count($experiences) != 0) : ?>
            <h6>
                <span class="badge rounded-pill bg-warning text-dark info-mastered-skill ">
                    Skills colored in yellow are those used in your experiences but which are not yet in your skills list.
                </span>
            </h6>
        <?php endif; ?>

        <div id="experiences">
            <?php if (count($experiences) != 0) : ?>
                <div class="accordion" id="accordionPanelsStayOpen">
                    <?php $cpt = 0; ?>

                    <?php foreach ($experiences as $experience) : ?>

                        <!--need variable $cpt for accordion to work-->
                        <?php $cpt++; ?>

                        <div class="accordion-item" id="experience<?= $experience->id ?>">
                            <h2 class="accordion-header" id="panelsStayOpen-heading<?= $cpt ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse<?= $cpt ?>" aria-expanded="true" aria-controls="panelsStayOpen-collapse<?= $cpt ?>">
                                    <input type="hidden" value="<?= $experience->id ?>" class="experience_id">
                                    <?= $experience->title . " at " . $experience->place->name . " (" . $experience->place->city . ")" ?>
                                </button>
                            </h2>

                            <div id="panelsStayOpen-collapse<?= $cpt ?>" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-heading<?= $cpt ?>">
                                <div class="accordion-body">
                                    <p><?= $experience->title . " at " . $experience->place->name . " (" . $experience->place->city . ")" . ' from ' . date('M Y', strtotime($experience->start)) ?>
                                        <?php if (!empty($experience->stop)) : ?>
                                            <?= ' to ' . date('M Y', strtotime($experience->stop)) ?>
                                        <?php endif; ?>
                                    </p>

                                    <?php if ($experience->description) : ?>
                                        <p><i><?= $experience->description ?></i></p>
                                    <?php endif; ?>


                                    <?php if (count($experience->skills) != 0) : ?>
                                        <p class="used_skills"> Used skills :
                                            <?php foreach ($experience->skills as $skill) : ?>

                                                <?php if (in_array($skill->id, $masterings)) : ?>
                                                    <span class="badge rounded-pill bg-primary mastering">
                                                    <?php else : ?>
                                                        <span class="badge rounded-pill bg-warning text-dark not-mastering" data-skill-id-attr="<?= $skill->id ?>">
                                                        <?php endif; ?>
                                                        <?= $skill->name . " " ?>
                                                        </span>

                                                    <?php endforeach; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- edit and delete buttons -->
                                <div class="manage-buttons">

                                    <?php $url_delete = "experience/delete_confirm_experience/$experience->id"; ?>
                                    <?php $url_edit = "experience/edit/$experience->id"; ?>

                                    <?php if ($user_consulted && $user->is_admin() && $user_consulted->id !== $user->id) : ?>
                                        <?php $url_delete = "experience/delete_confirm_experience/$user_consulted->id/$experience->id"; ?>
                                        <?php $url_edit = "experience/edit/$user_consulted->id/$experience->id"; ?>
                                    <?php endif; ?>

                                    <a href="<?= $url_edit ?>" class="btn btn-primary edit-btn" role="button" aria-pressed="true">
                                        Edit
                                    </a>

                                    <a id="btnDelete<?=$experience->id?>" href="<?= $url_delete ?>" class="btn btn-danger delete-btn" role="button" aria-pressed="true">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php $url_add = "experience/add_experience_to_user/"; ?>
        <?php if ($user_consulted && $user->is_admin() && $user_consulted->id !== $user->id) : ?>
            <?php $url_add = "experience/add_experience_to_user/$user_consulted->id"; ?>
        <?php endif; ?>
        <a href="<?= $url_add ?>" class="btn btn-outline-primary btn-lg d-grid new-experience" role="button" aria-pressed="true">Add new experience</a>


        <!-- Itération3 Modal -->
        <div id="confirm_dialog" title="Are you sure??" hidden>
            <p>Do you really want to delete experience <b><span id="message_to_delete_title"></span></b>.
                ( <b><span id="message_to_delete_place_name"></span></b>,
                <b><span id="message_to_delete_place_city"></span></b>) of <b><span id="message_to_delete_user"></span></b>
                and all of its dependencies ?
            </p>

            <p>This operation can't be reversed!</p>
        </div>
    </div>
</body>

</html>