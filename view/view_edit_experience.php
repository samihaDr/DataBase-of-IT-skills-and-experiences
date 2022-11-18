<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Experience</title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <link rel="stylesheet" href="css/experiences.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>

    <!-- JUST VALIDATE SCRIPTS -->
    <script src="lib/just-validate.production.patched.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date.production.min.js" type="text/javascript"></script>

    <script>
        let titleField, titleDiv, textArea, description, errDescription, infoNbChar, prec, isLimitReached, maxLength, spanDiv, selectPlace, selectDiv,
            startDateField, stopDateField, startDateDiv, stopDateDiv, userConsultedId, includeFile, isStartDateValid, isJustValidate;
        $(function() {
            titleDiv = $("#titlePart");
            titleField = $("#title");
            textArea = $("#description");
            description = $("#description");
            errDescription = $("#errDescription");
            infoNbChar = $("#infoNbChar");
            prec = description.val();
            maxLength = getMaxLength();
            selectPlace = $("#selectPlace");
            selectDiv = $("#selectDiv");
            spanDiv = $("#span-div");
            startDateField = $("#start_date");
            stopDateField = $("#stop_date");
            startDateDiv = $("#start-div");
            stopDateDiv = $("#stop-div");
            isStartDateValid = true;
            userConsultedId = "<?= $user_consulted->id ?>";
            includeFile = $("#include-file");
            includeFile.remove();
            isJustValidate = "<?= Configuration::get('just_validate') ?>";

            // Description feature of iteration 2 is always active
            $("#description").on('input', function() {
                checkDescription();
            });
        })

        function getMaxLength() {
            $.get("experience/get_max_length_service/", function(data) {
                maxLength = data;
                if (maxLength) {
                    infoNbChar.html(0 + "/" + maxLength).addClass("counter");

                    // if just validate is active => check inputs validation with plugin otherwise iteration2 rules are actives. //
                    InputsValidationManager(parseInt(maxLength));
                    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

                    if (prec.length > 0) {
                        if (prec.length < 10 || prec.length > maxLength) {
                            infoNbChar.html(prec.length + "/" + maxLength).addClass("infoNotOk");
                        } else {
                            infoNbChar.html(prec.length + "/" + maxLength).addClass("infoOk");
                        }
                    } else
                        infoNbChar.html(0 + "/" + maxLength).addClass("counter");
                } else {
                    console.log("failed to get data !");
                }
            });
        }

        // Get data from configuration file to check if just-validate plugin is active or not
        function InputsValidationManager(maxCharacter) {
            if (isJustValidate === "on") {
                getIteration3(maxCharacter);
            } else {
                getIteration2();
            }
        }

        function getIteration2() {
            callEventsToCheckValidation();
        }

        function getIteration3(maxCharacter) {
            // remove div displaying message errors for description
            $("#errDescription").remove();
            const validation = new JustValidate("#edit_experience_validation", {
                errorFieldCssClass: 'is-invalid',
                successFieldCssClass: 'is-valid'
            });

            validation
                .addField('#start_date',
                    [   
                        
                        // ne fonctionne pas dans tous les cas !!!
                        // {
                        //     plugin: JustValidatePluginDate(() => ({
                        //         required: true,
                        //     })),
                        //     errorMessage: "Start date is required !!!",
                        // },

                        // ne fonctionne que si j'enlève ma validation custom sur la date d'anniversaire
                        {
                            rule: 'required',
                            errorMessage: "Start date is required !!!"
                        },
                        {
                            validator: function(value, fields) {
                                const dateNow = new Date();
                                return value < dateNow.toLocaleDateString("en-CA");
                            },
                            errorMessage: "Start date should be before today !!!"
                        },
                        {
                            validator: function(value, fields) {

                                return fields['#stop_date'].elem.value ? fields['#stop_date'].elem.value > value : true;
                            },
                            errorMessage: "Start date should be before stop date !!!"
                        },
                        {
                            validator: function(value) {
                                let fd = new FormData();
                                fd.append("user_consulted_id", userConsultedId);
                                fd.append("start_date", value);

                                return function() {
                                    return fetch("user/user_birthdate_before_start_experience_service", {
                                            method: "POST",
                                            mode: "no-cors",
                                            headers: {
                                                "Content-type": "application/json; charset=UTF-8"
                                            },
                                            body: fd
                                        })
                                        .then(function(response) {
                                            return response.json();
                                        })
                                }
                            },
                            errorMessage: "Start date should be after birthdate !!!",
                        }
                    ], {
                        successMessage: "Looks good !!!"
                    })
                .addField("#stop_date",
                    [{
                        plugin: JustValidatePluginDate((fields) => ({
                            isAfter: $("#start_date").val(),
                        })),
                        errorMessage: "Stop date should be after Start date !!!",
                    }, ], {
                        successMessage: "Looks good !!!"
                    })
                .addField("#title", [{
                        rule: "required",
                        errorMessage: "Title is required !!!"
                    },
                    {
                        rule: "minLength",
                        value: 3,
                        errorMessage: "Title should have minimum 3 characters !!!"
                    },
                    {
                        rule: "maxLength",
                        value: 128,
                        errorMessage: "Title should have maximum 128 characters !!!"
                    }
                ], {
                    successMessage: "Looks good !!!"
                })
                .addField("#description", [{
                        rule: "minLength",
                        value: 10,
                        errorMessage: "Description should have minimum 10 characters !!!"
                    },
                    {
                        rule: "maxLength",
                        value: maxCharacter,
                        errorMessage: "Description should have maximum 30 characters !!!"
                    }
                ], {
                    successMessage: "Looks good !!!"
                })

                .onSuccess(function(event) {
                    event.target.submit();
                })
                .onFail(function() {
                    console.log("failed on submit, see errors");
                });

            $("#stop_date").change(() => validation.revalidateField("#start_date").then(isValid => {
                console.log("stop changed : " + isValid);
            }));

            $("#start_date").change(() => validation.revalidateField("#stop_date").then(isValid => {
                console.log("start changed : " + isValid);
            }));

            $("input[type=date]:first").focus();
        }

        function callEventsToCheckValidation() {
            $("#start_date").on('input', function() {
                checkStartDate();
            });

            $("#stop_date").on('input', function() {
                checkStopDate();
            });

            $("#title").on('input', function() {
                checkTitle();
            });

            $("#selectPlace").on('change', function() {
                validPlace();
            });

            $("#edit_experience_validation").on('submit', function(e) {
                return checkAll();
            });
        }

        function displayMessage(div, idName, msgTxt, field, error) {
            if (error === true) {
                div.append(`<div id=${idName} class='invalid-feedback'></div>`);
                let messErr = $(`#${idName}`);
                messErr.html(msgTxt);
                field.attr("class", "form-control is-invalid");
            } else {
                field.attr("class", "form-control is-valid");
                div.append(`<div id=${idName} class='valid-feedback'></div>`)
                let messOk = $(`#${idName}`);
                messOk.html(msgTxt);
            }
        }

        function checkStartDate() {
            var today = new Date().toISOString().slice(0, 10);
            $.post("user/user_birthdate_before_start_experience_service", {
                "start_date": startDateField.val(),
                "user_consulted_id": userConsultedId
            }, function(data) {
                if (data === "false" && startDateField.val().length !== 0) {
                    isStartDateValid = false;
                    displayMessage(startDateDiv, "startDateNotOk", "The start date must be after the birthdate", startDateField, true);
                } else if (startDateField.val() > today) {
                    displayMessage(startDateDiv, "startDateNotOk", "The start date must be before today", startDateField, true);
                    isStartDateValid = false;
                } else if (stopDateField.val().length > 0 && startDateField.val() > stopDateField.val()) {
                    displayMessage(startDateDiv, "startDateNotOk", "The start date must be before the stop date", startDateField, true);
                    displayMessage(stopDateDiv, "stopDateNotOk", "The stop date must be after the start date", stopDateField, true);
                    isStartDateValid = false;
                } else if (startDateField.val().length === 0) {
                    displayMessage(startDateDiv, "startDateNotOk", "The start date is required", startDateField, true);
                    isStartDateValid = false;
                } else {
                    isStartDateValid = true;
                    $("#startDateNotOk").remove();
                    displayMessage(startDateDiv, "messStartDateOk", "Looks good", startDateField, false);
                }
                if (stopDateField.val().length === 0 || isStartDateValid === true) {
                    displayMessage(stopDateDiv, "messStopDateOk", "Looks good", stopDateField, false);
                }
            });
        }

        function checkStopDate() {
            let ok = true;
            var today = new Date().toISOString().slice(0, 10);
            if (startDateField.val().length > 0 && stopDateField.val() < startDateField.val()) {
                displayMessage(stopDateDiv, "stopDateNotOk", "The stop date must be after the start date", stopDateField, true);
                ok = false;
            } else if (startDateField.val().length === 0) {
                displayMessage(startDateDiv, "startDateNotOk", "The start date is required", startDateField, true);
                ok = false;
            } else {
                if (isStartDateValid) {
                    displayMessage(startDateDiv, "messStartDateOk", "Looks good", startDateField, false);
                }
                displayMessage(stopDateDiv, "messStopDateOk", "Looks good", stopDateField, false);
                ok = true;
            }
            if (stopDateField.val().length === 0) {
                $("#stopDateNotOk").remove();
                displayMessage(stopDateDiv, "messStopDateOk", "Looks good", stopDateField, false);
                displayMessage(startDateDiv, "messStartDateOk", "Looks good", startDateField, false);
                ok = true;
            }
            return ok;
        }

        function checkTitle() {
            let ok = true;
            if (!(/^.{3,128}$/).test(titleField.val())) {
                displayMessage(titleDiv, "titleNotOk", "Title length must be between 3 and 128", titleField, true);
                ok = false;
            } else {
                $("#titleNotOk").remove();
                displayMessage(titleDiv, "messTitleOk", "Looks good", titleField, false);
            }
            return ok;
        }

        function checkDescription() {
            let ok = true;
            isLimitReached = false;
            limits(description, maxLength);
            txtLength = description.val().length;
            infoNbChar.html(txtLength + "/" + maxLength);

            if (txtLength > 0) {
                if (txtLength < 10 || txtLength > maxLength) {
                    infoNbChar.removeClass("infoOk").addClass("infoNotOk");
                    ok = false;
                } else if (txtLength == maxLength && isLimitReached == "false") {
                    description.val(prec);
                    ok = true;
                } else {
                    infoNbChar.removeClass("infoNotOk").addClass("infoOk");
                    ok = true;
                }
                description.focusout(function() {
                    displayInfo();
                });
                description.focusin(function() {
                    clearInfo();
                });
                prec = description.val();
            }

            return ok;
        }

        function limits(description, maxLength) {
            var text = description.val();
            var length = text.length;
            if (length > maxLength) {
                isLimitReached = "true";
                description.val(text.substr(0, maxLength));
                txtLength = description.val().length;
            }
        }
        
        function displayInfo() {
            clearInfo();

            if (txtLength < 10 || txtLength > maxLength) {
                errDescription.html("looks not good").removeClass("infoOk").addClass("infoNotOk");
                errDescription.append("  &#10060;")
            } else {
                errDescription.html("looks good").removeClass("infoNotOk").addClass("infoOk");
                errDescription.append("  &#x2714;")
            }
            if (txtLength === 0) {
                clearInfo();
                infoNbChar.html(0 + "/" + maxLength).removeClass("infoNotOk").addClass("counter");
            }
        }

        function clearInfo() {
            errDescription.html("");
        }

        function validPlace() {
            let ok = true;
            displayMessage(selectDiv, "messOk", "Looks good", selectPlace, false);
            return ok;
        }

        function checkAll() {
            checkStartDate();
            let ok = isStartDateValid;
            ok = checkStopDate() && ok;
            ok = checkTitle() && ok;
            ok = checkDescription() && ok;
            ok = validPlace() && ok;
            return ok;
        }
    </script>
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="main">
        <?php $url_edit = "experience/edit/$experience->id" ?>
        <?php $url_cancel = "experience/cancel/$experience->id" ?>
        <?php if ($user_consulted) : ?>
            <h1>Edit an Experience of <?= $user_consulted->fullname ?> </h1>
            <h2><?= $experience->title ?></h2>
            <?php if ($user->is_admin() && $user_consulted->id !== $user->id) : ?>
                <?php $url_edit = "experience/edit/$user_consulted->id/$experience->id" ?>
                <?php $url_cancel = "experience/cancel/$user_consulted->id/$experience->id" ?>
            <?php endif; ?>
        <?php else : ?>
            <h1>Edit an Experience of <?= $user->fullname ?></h1>
            <h2> <?= $experience->title ?></h2>
        <?php endif; ?>

        <form id="edit_experience_validation" method="post">
            <div id="start-div" class="mb-3">
                <label for="start_date" class="form-label">Start date :</label>
                <input type="date" class="form-control" id="start_date" value="<?= $experience->start ?>" name="start">
            </div>
            <div id="stop-div" class="mb-3">
                <label for="stop_date" class="form-label">End date (optional) :</label>
                <input type="date" class="form-control" id="stop_date" value="<?= $experience->stop ?>" name="stop">
            </div>
            <div id="titlePart" class="mb-3">
                <label for="title" class="form-label">Title :</label>
                <input type="text" class="form-control" id="title" value="<?= $experience->title ?>" name="title">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descritpion (optional) :</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= $experience->description ?></textarea>
                <div id="infoTextarea">
                    <div id="infoNbChar" class="counter"></div>
                    <div id="errDescription" class="counter"></div>
                </div>
            </div>

            <div id="selectDiv" class="mb-3">
                <label class="form-label">Place :</label>
                <select id="selectPlace" class="form-select" name="selected-place">
                    <?php foreach ($places as $place) : ?>
                        <?php if ($experience->place->id == $place->id) : ?>
                            <option value='<?= $place->id ?>' selected><?= $place->name . " (" . $place->city . ")" ?></option>
                        <?php else : ?>
                            <option value='<?= $place->id ?>'><?= $place->name . " (" . $place->city . ")" ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Skills :</label>
                <div class="check_skills">
                    <?php foreach ($skills_all as $skill) : ?>
                        <div class="form-check">
                            <?php if (in_array($skill, $experience->skills)) : ?>
                                <input class="form-check-input" type="checkbox" name="checked-skills[]" value="<?= $skill->id ?>" checked>
                            <?php else : ?>
                                <input class="form-check-input" type="checkbox" name="checked-skills[]" value="<?= $skill->id ?>">
                            <?php endif; ?>
                            <?= $skill->name ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="manage-buttons">
                <a href="<?= $url_cancel ?>" role="button" class="btn btn-secondary btn-cancel-exp">Cancel</a>
                <input type="submit" class="btn btn-primary btn-save-exp" formaction="<?= $url_edit ?>" value="Save">
            </div>
        </form>

        <!-- Modal displaying errors -->
        <div id="include-file">
            <?php include 'view/view_errors.php'; ?>

        </div>
    </div>
</body>

</html>