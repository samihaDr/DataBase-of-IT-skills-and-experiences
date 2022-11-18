<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session</title>
    <base href="<?= $web_root ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        let userConcernedId;
        let userid;
        let skillSelectedId;
        let skills_checked = [];
        $(function() {
            userId = '<?= $user->id ?>';
            console.log("userId =>" + userId);
            userConcernedId = "<?= $user_concerned->id ?>";
            console.log("UserConcerned => " + userConcernedId);
            $("#formAdd").remove();

            $(".form-check").click(function(){
                console.log("form-check =>");                
                 skills_checked = [];
                 console.log("countTab1  => "+ skills_checked.length);
                $.each($(".form-check-input:checked"),function(){
                    skills_checked.push($(this).val());
                    console.log("countTab1  => "+ skills_checked.length);
                    console.log("skill selected is =>"+ $(this).val());
                })

            });
            $('#add').click(function() {
                if(skills_checked.length != 0){
                    $.post("session/add_service/",{"user_id" : userConcernedId, "checked_skills" : skills_checked},
                    function(data){
                        if(data === "true"){
                            console.log("DATA  =>" + data);
                            for(let skill of skills_checked){
                                $("#skill_selected_" +skill).hide();
                            }
                        }
                        if(data ==="false"){
                            console.log("Fail");
                        }
                    })
                }else{
                    console.log("tableau est vide");
                }
                
            })
            // $(".delete").click(function() {
            //     skillSelectedId = $(this).attr("data-skill-id");
            //     $(this).removeAttr("href");

            //     console.log("userConcerned " + userConcernedId);
            //     console.log("skillSelectedId " + skillSelectedId);

            //     $.post("session/delete_service", {
            //             "user_id": userConcernedId,
            //             "skill_selected_id": skillSelectedId
            //         },
            //         function(data) {
            //             if (data === "true") {
            //                 $("#skill" + skillSelectedId).remove();
            //                 console.log("success");
            //             } else if (data === "false")
            //                 console.log("failed");
            //         });
            // });

            

        });

       
    </script>
</head>

<body>
    <?php include 'view/menu.php'; ?>
    <label class="form-label mt-3">Select User :</label>
    
    <form action="session/index" method="post" id="user_form" >
        <select class="form-select" aria-label="" name="user_selected_id" id="user_selected_id">
           <option  value="">---Select a user---</option>
            <?php foreach ($users as $user_selected) : ?>
                <option value="<?= $user_selected->id ?>">
                    <?= $user_selected->fullname ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div>
        <button id="select_user" form="user_form" type="submit" class="btn btn-outline-primary mt-3">Show skills</button>
        </div>
        
    </form>
    <?php if (count($user_non_mastered_skills) != 0) : ?>
        <form method="post" id="formAdd" hidden> </form>
            <?php foreach ($user_non_mastered_skills as $skill) : ?>
                <div class="form-check" id="skill_selected_<?= $skill->id ?>">
                    <input class="form-check-input" type="checkbox" name="checked-skills[]" value="<?= $skill->id ?>">
                    <?= $skill->name ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" form="formAdd" formaction="session/add/<?= $user_concerned->id ?>" id="add">Add</button>
       


    <?php endif; ?>
    </div>
   
</body>

</html>