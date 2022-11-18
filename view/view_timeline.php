<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">

    <link rel="stylesheet" href="css/timeline.css" type="text/css">
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>

    <!-- FULLCALENDAR -->
    <link href='lib/fullcalendar-scheduler-5.11.0/lib/main.css' rel='stylesheet'/>
    <script src='lib/fullcalendar-scheduler-5.11.0/lib/main.js'></script>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>


    <!-- JQUERY UI -->
    <link href="lib/jquery-ui-1.13.1/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <link href="lib/jquery-ui-1.13.1/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="lib/jquery-ui-1.13.1/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
    <script src="lib/jquery-ui-1.13.1/jquery-ui.min.js" type="text/javascript"></script>


    <script>
        let userConsulted, maxYears, minYearExp, customSlotDuration, userBirthdate;

        $(function () {
            userBirthdate = "<?= $user_birthdate ?>";
            const twentyYears = 20;
            const tenYears = 10;
            const fiveYears = 5;
            const oneYear = 1;
            userConsultedId = "<?= $user_consulted->id ?>";
            minYearExp = "<?= $min_year_exp ?>";
            maxYears = "<?= $max_years ?>";
            customSlotDuration = "<?= $slot_duration ?>";
            var calendarEl = $("#calendar")[0];
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'local',
                initialDate: minYearExp,
                initialView: "maxYear",
                headerToolbar: {
                    left: 'today prev,next',
                    center: 'title',
                    right: 'maxYear,twentyYears,tenYears,fiveYears,oneYear'
                },
                editable: true,
                events: {
                    url: 'experience/get_experiences_service_for_full_calendar',
                    method: 'POST',
                    extraParams: function () {
                        return {
                            userConsulted: userConsultedId
                        }
                    },
                },
                eventDrop: function (info) {
                    if (Date.parse(userBirthdate) > Date.parse(info.event.start)) {
                        alert("Birthdate is after experience start date");
                        calendar.refetchEvents();
                    } else if (Date.parse(info.event.start) > Date.now()) {
                        console.log(Date.now());
                        alert("Start date should be before today");
                        calendar.refetchEvents();

                    } else {
                        updateConfirm(info, userConsultedId, calendar);
                    }
                },
                eventResize: function (info) {
                    if (Date.parse(userBirthdate) > Date.parse(info.event.start)) {
                        alert("New start date experience is before Birthdate.");
                        calendar.refetchEvents();
                    } else {
                        updateConfirm(info, userConsultedId, calendar);
                    }
                },
                views: {
                    maxYear: {
                        type: 'timeline',
                        duration: {
                            years: parseInt(maxYears)
                        },
                        slotDuration: {
                            month: parseInt(customSlotDuration)
                        },
                        buttonText: 'Max'
                    },
                    twentyYears: {
                        type: 'timeline',
                        duration: {
                            years: twentyYears
                        },
                        buttonText: '20 Years'
                    },
                    tenYears: {
                        type: 'timeline',
                        duration: {
                            years: tenYears
                        },
                        slotDuration: {
                            month: 6
                        },
                        buttonText: '10 Years'
                    },
                    fiveYears: {
                        type: 'timeline',
                        duration: {
                            years: fiveYears
                        },
                        slotDuration: {
                            month: 3
                        },
                        buttonText: '5 Years'
                    },
                    oneYear: {
                        type: 'timeline',
                        duration: {
                            years: oneYear
                        },
                        slotDuration: {
                            month: 1
                        },
                        buttonText: '1 Years'
                    }
                }
            });
            calendar.render();
        });

        function updateConfirm(data, userId, calendar) {
            // merci pour les dates !
            let stopDate = formatDate(data.event.end, "numeric", "numeric", "numeric");
            let stopDateParsing = Date.parse(stopDate);
            let stopDateLimit = Date.parse("01-01-2500");

            let stopDateMsg = stopDateParsing > stopDateLimit ? "(Not defined)" : formatDate(data.event.end, "numeric", "long", "numeric");
            let startDate = formatDate(data.event.start, "numeric", "long", "numeric");

            $("#update_experience_body").text(data.event.title).css('font-weight', 'bold');
            $('#new_date_experience').text("from " + startDate + " to " + stopDateMsg + " ?").css('font-weight', 'bold');
            $('#confirm_dialog').removeAttr("hidden");
            $('#confirm_dialog').dialog({
                resizable: false,
                height: 300,
                width: 500,
                modal: true,
                autoOpen: true,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                buttons: {
                    Confirm: function () {
                        startDate = formatDate(data.event.start, "numeric", "numeric", "numeric");
                        updateExperience(userId, data.event.id, startDate, stopDate);
                        $(this).dialog("close");
                    },
                    Cancel: function () {
                        calendar.refetchEvents();
                        console.log("update aborted");
                        $(this).dialog("close");
                    }
                }
            });
        }

        function formatDate(date, yearFormat, monthFormat, dayFormat) {
            return date.toLocaleDateString('en-GB', {
                year: yearFormat,
                month: monthFormat,
                day: dayFormat
            });
        }

        function updateExperience(userId, experienceId, startDate, stopDate) {
            console.log('sa' + startDate);
            console.log('so' + stopDate);
            $.post("experience/update_experience_service", {
                "user_id": userId,
                "experience_id": experienceId,
                "start_date": startDate,
                "stop_date": stopDate
            }, function (data) {
                if (data === "true") {
                    console.log("experience updated");
                } else if (data === "false") {
                    console.log("update aborted");
                }
            },);
        }
    </script>
</head>

<body>
<?php include 'view/menu.php'; ?>
<div class="main">
    <div class="work_title">
        <div class="full_name">
            <h1><?= $user_consulted->fullname . ', ' . $user_consulted->title ?></h1>
        </div>
        <a href="experience/experiences/<?= $user_consulted->id ?>" class="btn btn-outline-primary view-experiences-btn"
           role="button" aria-pressed="true">View Experience</a>
    </div>
    <div class="experiences-timeline">
        <h2 class="experiences-title">Experiences</h2>
    </div>

    <div id='calendar'></div>

    <div id="confirm_dialog" title="Confirm Experience Update Drop" hidden>
        <p>Are you sure to change experience "<span id="update_experience_body"></span>"</p>
        <p>With this new range : </p>
        <p id="new_date_experience"></p>
    </div>
</div>

</body>

</html>