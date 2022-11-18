<?php
require_once 'framework/Controller.php';
require_once 'framework/View.php';
require_once 'model/User.php';
require_once 'model/Experience.php';
require_once 'model/Mastering.php';
require_once 'model/Skill.php';
require_once 'model/Place.php';

class ControllerSession extends Controller
{
    public function index()
    {
        $user = $this->get_user_or_redirect();
        $user_concerned = $user;
        $users = User::get_users();
        $user_non_mastered_skills = array();
        if ($user->is_admin()) {
            if (isset($_GET['param1'])) {
                $user_concerned = User::get_user_by_id($_GET["param1"]);
                if ($user_concerned) {
                    $user_non_mastered_skills = $user_concerned->get_non_mastered_skill();
                } else {
                    $this->redirect("experience", "index");
                }
            } else if (isset($_POST['user_selected_id'])) {
                $user_concerned = User::get_user_by_id($_POST['user_selected_id']);
                if ($user_concerned) {
                    $this->redirect("session", "index", $user_concerned->id);
                } else {
                    $this->redirect("experience", "index");
                }
            }
            (new View("session"))->show(array("user" => $user, "users" => $users, "user_concerned" => $user_concerned, "user_non_mastered_skills" => $user_non_mastered_skills));
        } else {
            $this->redirect("experience", "index");
        }
    }
    public function delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->is_admin()) {
            if (isset($_GET['param1'], $_GET["param2"])) {
                $user_id = $_GET['param1'];
                $skill_id = $_GET["param2"];
                $user_concerned = User::get_user_by_id($user_id);
                $skill_concerned = Skill::get_skill_by_id($skill_id);
                if ($user_concerned && $skill_concerned) {
                    $user_concerned->delete_non_mastered_skill($skill_id);
                    $this->redirect("session", "index", $user_id);
                }
            } else {
                $this->redirect("session", "index");
            }
        } else {
            $this->redirect("experience", "index");
        }
    }

    public function delete_service()
    {
        $user = $this->get_user_or_false();
        if ($user->is_admin()) {
            if (isset($_POST["user_id"], $_POST["skill_selected_id"])) {
                $user_id = $_POST["user_id"];
                $skill_id = $_POST["skill_selected_id"];
                $user_concerned = User::get_user_by_id($user_id);
                $skill_concerned = Skill::get_skill_by_id($skill_id);
                if ($user_concerned && $skill_concerned) {
                    $user_concerned->delete_non_mastered_skill($skill_id);
                    echo "true";
                }
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }
    }
    public function add()
    {
        $user = $this->get_user_or_redirect();
        $using_skills = $_POST['checked-skills'] ?? array();
        if ($user->is_admin()) {
            if (isset($_GET["param1"], $_POST["checked-skills"])) {
                $user_id = $_GET["param1"];
                if (count($using_skills) != 0) {
                    foreach ($using_skills as $skill) {
                        $a = $skill;
                        $skill_selected = Skill::get_skill_by_id($skill);
                        $mastering = new Mastering($user_id, $skill_selected, 1);
                        $mastering->save();
                    }
                    $this->redirect("session", "index", $user_id);
                }
            } else {
                $this->redirect("session", "index");
            }
        } else {
            $this->redirect("experience", "index");
        }
    }

    public function add_service()
    {
        $user = $this->get_user_or_false();
        $using_skills = $_POST['checked_skills'] ?? array();
        if ($user->is_admin()) {
            if (isset($_POST["user_id"], $_POST["checked-skills"])) {
                $user_concerned = User::get_user_by_id($_POST["user_id"]);
                if ($user_concerned && count($using_skills) != 0) {
                    foreach ($using_skills as $skill) {
                        $skill_selected = Skill::get_skill_by_id($skill);
                        $mastering = new Mastering($user_concerned, $skill_selected, 1);
                        $mastering->save();
                    }
                    echo ("true");
                    
                } else {
                    echo ("false");
                }
            } else {
                echo ("false");
            }
        } else {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }
}
