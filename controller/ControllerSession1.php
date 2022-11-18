<?php
require_once 'framework/Controller.php';
require_once 'framework/View.php';
require_once 'model/User.php';
require_once 'model/Experience.php';
require_once 'model/Mastering.php';
require_once 'model/Skill.php';
require_once 'model/Place.php';
class ControllerSession1 extends Controller
{
    public function index()
    {
        $user = $this->get_user_or_redirect();
       
        if($user-> is_admin()){
            $users = User::get_users();
        }
        else{
            $this->redirect("experiences", "experience");
        }
        (new View("session1"))->show(array("user" => $user, "users" => $users));
    }

    public function get_masterings_service(){
        $user = $this->get_user_or_false();
        $user_consulted = $user;
        //$masterings = [];
        if (isset($_GET['param1']) && ($_GET['param1'] !== "")) {
            $user_consulted = User::get_user_by_id($_GET['param1']);
            if (!$user_consulted || !$user->is_admin()) {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        }
        $masterings = Mastering::get_mastering_skills_by_user($user_consulted->id);
        echo json_encode($masterings);
    }

    
    public function delete_service(){
        $user = $this->get_user_or_false();
        $user_consulted = $user;
        $checkedMasterings = $_POST['checked-skills'] ?? array();
        if($user->is_admin()){
            if(isset($_POST['user_id']) && $_POST['checked-skills']){
                $user_consulted = User::get_user_by_id($_POST['user_id']);
                if($user_consulted && count($checkedMasterings) != 0){
                    foreach($checkedMasterings as $checkedMastering){
                        $mastering = Mastering::get_mastering($user_consulted->id,$checkedMastering);
                        if($mastering){
                            $mastering->delete();
                        }
                    }
                    echo("true");
                    die;
                }
                echo ("false");
                
            }
            echo("false");
        }else{
            header("HTTP/1.1 401 Unauthorized");
                exit; 
        }
        
    }
}