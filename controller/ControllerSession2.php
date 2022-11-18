<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Experience.php';
require_once 'model/Skill.php';
require_once 'model/Mastering.php';
require_once 'model/Place.php';
require_once 'model/User.php';

class ControllerSession2 extends Controller
{
    public function index(){
        $user = $this->get_user_or_redirect();
        $user_concerned = $user;
        $users = User::get_users();
        $non_masterings = array();
        if($user->is_admin()){
            if(isset($_GET["param1"])){
                $user_concerned = User::get_user_by_id($_GET["param1"]);
                if($user_concerned){
                    $non_masterings = $user_concerned->get_non_mastered_skill();
                }else{
                    $this->redirect("experience" , "index");
                }
            }else if (isset($_POST['selected_user_id'])) {
                $user_concerned = User::get_user_by_id($_POST['selected_user_id']);
                if ($user_concerned) {
                    $this->redirect("session2", "index", $user_concerned->id);
                } else {
                    $this->redirect("experience", "index");
                }
            }
            (new View("session2"))->show(array("user" => $user, "users" => $users, "user_concerned" => $user_concerned, "list_masterings" => $non_masterings));
        }else{
            $this->redirect("experience" , "index"); 
        }
    }

    public function delete(){
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;
        if($user->is_admin()){
            if(isset($_GET["param1"]) && $_GET["param2"]){          
                $user_consulted= User::get_user_by_id($_GET["param1"]);
                if($user_consulted){
                    $skill_selected = Skill::get_skill_by_id($_GET["param2"]);
                    if($skill_selected){
                        $user_consulted->delete_non_mastered_skill($skill_selected->id);
                        $this->redirect("session2", "index", $user_consulted->id);
                    }
                    else{
                        $this->redirect("session2", "index");
                    }
                }else{
                    $this->redirect("session2", "index");
                }
            }else{
                $this->redirect("session2", "index");
            }
        }else{
            $this->redirect("experience", "index");
        }
    }

    public function delete_service(){
        $user= $this->get_user_or_false();
        if($user->is_admin()){
            if(isset($_POST["user_id"], $_POST["skill_selected"])){
                $user_consulted=User::get_user_by_id($_POST["user_id"]);
                if($user_consulted){
                    $skill = Skill::get_skill_by_id($_POST["skill_selected"]);
                    if($skill){
                        $user_consulted->delete_non_mastered_skill($skill->id);
                        echo("true");
                    }else{
                        echo("false");
                    }

                }else{
                    echo("false");
                }
            }else{
                echo("false");
            }
        }else {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }
}