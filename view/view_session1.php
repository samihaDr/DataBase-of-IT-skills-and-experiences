<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session1</title>
    <base href="<?= $web_root ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        let userId, userSelectedId, masterings, userSelectedName, html ;
        let listCheckedMasterings = [];

        $(function() {
            userId = '<?= $user->id ?>';
            console.log("UserId =>  " + userId);
            
            $("#form").change(function() {
                userSelectedId = $("select option:selected").val();
                userSelectedName = $("select option:selected").text();
                console.log("user selectedv============" + userSelectedId);
                console.log("UserSelectedName ====> " + userSelectedName);
                $("#masteringsList").html("");
                getMasterings();
                
            })
           
           
        });

        function getMasterings() {
            masterings = [];
           
            $.get("session1/get_masterings_service/" + userSelectedId ,  function(data) {
                masterings = data;
                if (masterings.length != 0) {

                    $("#masteringsList").append("<h2>Competences de" + userSelectedName + "</h2>");
                    for (let mastering of masterings) {
                        console.log("Mastering => " + mastering.skill.name);
                        $("#masteringsList").append("<div id='selected_skill_"+ mastering.skill.id + "'><input class='form-check-input'  type='checkbox' value=" + mastering.skill.id + ">" + mastering.skill.name + "</div>");
                        //displayDiv(mastering);
                    }
                    $("#masteringsList").append("<div><button class='btn btn-danger' id='btn_delete' type='submit'> Supprimer les skills </button></div>");
                }
                $("input.form-check-input").click(function () {
                    listCheckedMasterings = [];
                    $.each($(".form-check-input:checked"), function () {
                        console.log("Skill checked => " + $(this).val());
                        listCheckedMasterings.push($(this).val());
                    });
                });
                $("#btn_delete").click(function(){
                console.log("j'ai appuyé sur btn_delete");
                deleteMasterings();
            });

            }, "json");
        }

        function deleteMasterings(){
           console.log("list => "+ listCheckedMasterings.length);
            $.post("session1/delete_service/", {
                "user_id" : userSelectedId,"checked-skills" : listCheckedMasterings
            }, function (data){
                if(data === "true"){
                    console.log("list2 => "+ listCheckedMasterings.length);
                   for(let skillSelected of listCheckedMasterings){
                       //console.log("je vais etre supprimer => "+ $(this).val());
                    $("#selected_skill_"+skillSelected).remove();
                   }
                }else{
                    console.log("Probleme")
                }
            })
        }

        function displayDiv(mastering) {
            html += '<div class=form-check>'
            // html += '<input class=form-check-input name=selected_id type=checkbox value=' + mastering.skill.id + '>' + mastering.skill.name
            html +=  '<input class="form-check-input" id="selected_skill_"'+  mastering.skill.id  + 'type="checkbox" name="checked-skills[]" value='+ mastering.skill.id +'checked>'
            html += mastering.skill.name
            html += '</div>'
            console.log("je suis dans le DisplayDiv");
            $("#masteringslist").append(html);
        }
    </script>
</head>

<body>
    <?php include 'view/menu.php'; ?>
    <div class="main">
        <div>
            <h2>UsersList 1</h2>
            <form action="session1/index" method="post" id="form">
                <select class="form-select" aria-label="" name="user_selected_id" id="user_selected_id">
                    <option value="">Select a user</option>
                    <?php foreach ($users as $user_selected) : ?>
                        <option value="<?= $user_selected->id ?>">
                            <?= $user_selected->fullname ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            </form>

        </div>
        <div id="masteringsList">

        </div>

    </div>

</body>

</html>