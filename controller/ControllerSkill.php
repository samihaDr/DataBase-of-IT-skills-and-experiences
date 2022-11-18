<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Mastering.php';
require_once 'model/User.php';
require_once 'model/Skill.php';
require_once 'model/Experience.php';

class ControllerSkill extends controller
{
    public function index()
    {
    }

    private function has_skill_already($mastered_skills, $skill_id){
        foreach ($mastered_skills as $mastered_skill) {
            if ($mastered_skill->skill->id === $skill_id) {
                return true;
            }
        }
        return false;
    }

    public function add_mastering_via_experience_service()
    {
        $this->add_mastering_service(1);
    }

    public function add_mastering_via_skill_service(){
        //ici on va checker le $_POST['level'] et ensuite appeler la methode private
        if(isset($_POST['level'])){
            $this->add_mastering_service($_POST['level']);
        }
    }

    private function add_mastering_service($level_skill){
        $user = $this->get_user_or_redirect();
        $user_concerned = null;
        $exists_skill = false;
        if (isset($_POST['skill_id'], $_POST['user_id'])) {
            $skill = Skill::get_skill_by_id($_POST['skill_id']);
            $user_concerned = User::get_user_by_id($_POST['user_id']);

            $mastered_skills = Mastering::get_mastering_skills_by_user($user_concerned->id);
            $exists_skill = $this->has_skill_already($mastered_skills, $skill->id);

            //rajout de la condition sur le level
            if (!$exists_skill && $skill && $user_concerned && ($level_skill >= 1 && $level_skill <= 5)) {
                if ($user->id == $user_concerned->id || ($user->id !== $user_concerned->id && $user->is_admin())) {
                    $mastering = new Mastering($user_concerned->id, $skill, $level_skill);
                    echo "true";
                } else {
                    echo "false";
                }
                
                $mastering->save();
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }
    }

    public function update_mastered_skill_service(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST['user_id'], $_POST['skill_id'], $_POST['level'])){
            $mastered_skill = Mastering::get_mastering($_POST['user_id'], $_POST['skill_id']);
            $user_concerned = User::get_user_by_id($_POST['user_id']);
            $level = $_POST['level'];

            if($mastered_skill && $user_concerned && ($level >=1 && $level <=5)){
                $mastered_skill->level = $level;
                if($user->id == $user_concerned->id || ($user->id != $user_concerned->id && $user->is_admin())){
                    $mastered_skill->save();
                    echo "true";
                } else {
                    echo "false";
                }
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }
    }

    public function delete_mastered_skill_service(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST['user_id'], $_POST['skill_id'])){
            $mastered_skill = Mastering::get_mastering($_POST['user_id'], $_POST['skill_id']);
            $user_concerned = User::get_user_by_id($_POST['user_id']);
            
            if($mastered_skill && $user_concerned){
                if($user->id == $user_concerned->id || ($user->id != $user_concerned->id && $user->is_admin())){
                    $mastered_skill->delete();
                    echo "true";
                } else {
                    echo "false";
                }
            } else {
                echo "false";
            }
        } else {
            echo "false";
        }
    }

    public function get_mastered_skills_service(){
        $user = $this->get_user_or_redirect();
        $mastered_skills = array();
        if(isset($_POST['user_id'])){
            $user_concerned = User::get_user_by_id($_POST['user_id']);
            if($user_concerned){
                if($user->id == $user_concerned->id || ($user->id != $user_concerned->id && $user->is_admin())){
                    $mastered_skills = Mastering::get_mastering_skills_by_user($_POST['user_id']);
                    echo json_encode($mastered_skills);
                }
            }
        }
    }

    public function user_skills()
    {
        $user = $this->get_user_or_redirect();
        $user_view = $user;
        $id = $user->id;
        $has_parameter = "false";
        if (isset($_GET["param1"])) {
            $has_parameter = "true";
            if ($user->is_admin()) {
                $id = $_GET["param1"];
                $user_view = User::get_user_by_id($id);
                if (!$user_view) {
                    $this->redirect("user", "users");
                }
            } else {
                $this->redirect("skill", "user_skills");
            }
        }
        $mastering_skills = Mastering::get_mastering_skills_by_user($id);

        (new View("skills"))->show(array("mastering_skills" => $mastering_skills, "user" => $user, 'user_view' => $user_view, "has_parameter" => $has_parameter));
    }

    public function edit_mastering_skill_up()
    {
        $this->edit_mastering_skill(1);
    }

    public function edit_mastering_skill_down()
    {
        $this->edit_mastering_skill(-1);
    }

    private function edit_mastering_skill($level_counter)
    {
        $errors = array();

        if (isset($_POST['mastering_skill_id'], $_POST['mastering_user_id'])) {
            $mastering_skill = Mastering::get_mastering($_POST['mastering_user_id'], $_POST['mastering_skill_id']);
            $mastering_skill->level += $level_counter;
            $errors = $mastering_skill->validate_level();

            if (count($errors) == 0) {
                $mastering_skill->save();
            }
            $this->redirect("skill", "user_skills", $_POST['mastering_user_id']);
        }
    }

    public function add_mastering_skill()
    {
        $user = $this->get_user_or_redirect();
        $user_view = null;
        $errors = array();
        if (isset($_GET['param1'])) {
            $user_view = User::get_user_by_id($_GET['param1']);
        }

        if (isset($_POST['selected_skill'], $_POST['level'])) {
            $level = $_POST['level'];
            $selected_skill_id = $_POST['selected_skill'];
        }
        $skill = Skill::get_skill_by_id($selected_skill_id);
        if ($user_view) {
            $mastering_skill = new Mastering($user_view->id, $skill, $level);
        } else {
            $mastering_skill = new Mastering($user->id, $skill, $level);
        }
        $errors = $mastering_skill->validate_level();


        if (count($errors) == 0) {
            $mastering_skill->save();
        }

        $this->redirect("skill", "user_skills", $_GET['param1']);
    }

    /**
     * init_skill : initialisation de l'objet Skill
     *
     * @return void
     */
    private function init_skill()
    {
        $param = array();
        $param["user"] = $this->get_user_or_redirect();
        $param["name"] = "";
        $param["errors"] = [];
        $param["show_modal_error"] = false;
        return $param;
    }

    /**
     * skills: return skills list
     *
     * @return void
     */
    public function skills()
    {
        $param = array();
        $user_by_skill_counter = [];
        $experience_by_skill_counter = [];

        $param = array_merge($param, $this->init_skill());
        if ($param["user"]->is_admin()) {
            $skills = Skill::get_skills();

            foreach ($skills as $skill) {
                $user_by_skill_counter[] = count(Mastering::get_mastering_by_skill($skill->id));
                $experience_by_skill_counter[] = count(Experience::get_experience_by_skill($skill->id));
            }

            (new View("manage_skills"))->show(array("skills" => $skills, "user" => $param["user"], "user_by_skill_counter" => $user_by_skill_counter, "experience_by_skill_counter" => $experience_by_skill_counter, "name" => $param["name"], "show_modal_error" => $param["show_modal_error"], "errors" => $param["errors"]));
        } else {
            $this->redirect("user", "index");
        }
    }

    /*fonction qui permet l'assignation d'un skill avec un id=-1 à l'ajout d'un nouveau skill et son id à l'edition*/
    private function assignation_skill($id = null, $name)
    {
        $skill_id = $id ?? -1;
        $param["name"] = $name;

        return new Skill($skill_id, $name);
    }

    /**
     * start_skill_validation : lance la validation et l'unicité du nom dans l'ajout et l'edit d'un skill
     *
     * @param  mixed $name
     * @param  mixed $errors
     * @return void
     */
    private function start_skill_validation($name, $errors)
    {
        $errors = Skill::validate_name($name);
        $errors = array_merge(Skill::unicity_name($name), $errors);
        return $errors;
    }

    /**
     * add_skill
     *
     * @return void
     */
    public function add_skill()
    {
        $param = array();
        $errors = array();
        $user_by_skill_counter = [];
        $experience_by_skill_counter = [];
        $param = array_merge($param, $this->init_skill());

        if ($param["user"]->is_admin()) {

            if (isset($_POST['name'])) {
                $param["name"] = $_POST['name'];
                $param["errors"] = array_merge($errors, $this->start_skill_validation($param["name"], $errors));
                $skill = $this->assignation_skill(null, $param["name"]);
                if (count($param["errors"]) == 0) {
                    $skill->save();
                    $this->redirect("skill", "skills");
                } else {
                    $param["show_modal_error"] = true;
                }

                $skills = Skill::get_skills();

                foreach ($skills as $skill) {
                    $user_by_skill_counter[] = count(Mastering::get_mastering_by_skill($skill->id));
                    $experience_by_skill_counter[] = count(Experience::get_experience_by_skill($skill->id));
                }

                (new View("manage_skills"))->show(array("skills" => $skills, "user" => $param["user"], "user_by_skill_counter" => $user_by_skill_counter, "experience_by_skill_counter" => $experience_by_skill_counter, "name" => $param["name"], "show_modal_error" => $param["show_modal_error"], "errors" => $param["errors"]));
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    /**
     * edit_skill : Editer un skill
     *
     * @return skill
     */
    public function edit_skill()
    {
        $param = array();
        $errors = array();
        $param = array_merge($param, $this->init_skill());

        if ($param["user"]->is_admin()) {
            if (isset($_POST['skill_id']) || (isset($_POST['name']))) {
                $skill_edit = $this->assignation_skill($_POST['skill_id'], $_POST['name']);
                $skill = Skill::get_skill_by_id($_POST['skill_id']);

                if ($skill->name !== $skill_edit->name) {
                    $param["errors"] = array_merge($errors, $this->start_skill_validation($skill_edit->name, $errors));
                }
                if (count($param["errors"]) == 0) {
                    $skill_edit->save();
                    $this->redirect("skill", "skills");
                } else {
                    $param["show_modal_error"] = true;
                }
                $skills = Skill::get_skills();

                for ($i = 0; $i < count($skills); $i++) {
                    if ($skills[$i]->id == $skill_edit->id) {
                        $skills[$i]->name = $skill_edit->name;
                    }
                    $user_by_skill_counter[] = count(Mastering::get_mastering_by_skill($skills[$i]->id));
                    $experience_by_skill_counter[] = count(Experience::get_experience_by_skill($skills[$i]->id));
                }
                (new View("manage_skills"))->show(array("skills" => $skills, "user" => $param["user"], "user_by_skill_counter" => $user_by_skill_counter, "experience_by_skill_counter" => $experience_by_skill_counter, "name" => $param["name"], "show_modal_error" => $param["show_modal_error"], "errors" => $param["errors"]));
            } else {
                $this->redirect("skill", "skills");
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    /**
     * delete_confirm_skill : une fonction qui permet la redirection vers une page de confirmation avant de suprimer de la BD
     *
     * @return void
     */
    public function delete_confirm_skill()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = null; //passed into method in view to delete skill
        if ($user->is_admin()) {
            if (isset($_GET["param1"])) {
                $skill_id = $_GET["param1"];
                $skill = Skill::get_skill_by_id($skill_id);

                $user_by_skill_counter = count(Mastering::get_mastering_by_skill($skill_id));
                $experience_by_skill_counter = count(Experience::get_experience_by_skill($skill_id));

                if ($skill && $user_by_skill_counter == 0 && $experience_by_skill_counter == 0) {
                    $deletable = $skill;
                    (new View("delete_confirm"))->show(array("skill" => $skill, "user" => $user, "user_consulted" => $user_consulted, "deletable" => $deletable));
                } else {
                    $this->redirect("skill", "skills");
                }
            } else {
                $this->redirect("skill", "skills");
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    /**
     * delete_skill = Supprimer un skill da la base de données
     *
     * @return void
     */
    public function delete_skill()
    {
        $user = $this->get_user_or_redirect();
        if ($user->is_admin()) {
            if (isset($_GET["param1"])) {
                $skill_id = $_GET["param1"];
                $skill = Skill::get_skill_by_id($skill_id);

                $user_by_skill_counter = count(Mastering::get_mastering_by_skill($skill_id));

                $experience_by_skill_counter = count(Experience::get_experience_by_skill($skill_id));

                if ($skill && $user_by_skill_counter == 0 && $experience_by_skill_counter == 0) {
                    $skill->delete();
                }
            }
            $this->redirect("skill", "skills");
        } else {
            $this->redirect("user", "index");
        }
    }

    public function cancel()
    {
        $this->redirect("skill", "skills");
    }


    /**
     * init_mastering : fonction qui permet l'initialisation de l'objet mastering
     *
     * @return void
     */
    private function init_mastering()
    {
        $param = array();
        $param["user"] = $this->get_user_or_redirect();
        $param["mastering"] = null;
        $param["user_mastering"] = null;
        $param["skill_mastering"] = null;
        $param["user_consulted"] = null; //passed into method in view to delete mastering skill

        return $param;
    }

    /**
     * assignation_mastering : Fonction qui permet de réccuperer l'objet mastering avec les params passées dans l'URL
     *
     * @return void
     */
    private function assignation_mastering()
    {
        $param = array();
        $mastering_user_id = $_GET["param1"];
        $mastering_skill_id = $_GET["param2"];
        $param["mastering"] = Mastering::get_mastering($mastering_user_id, $mastering_skill_id);
        $param["user_mastering"] = User::get_user_by_id($mastering_user_id);
        $param["skill_mastering"] = Skill::get_skill_by_id($mastering_skill_id);

        return $param;
    }

    /**
     * delete_confirm_mastering 
     *
     * @return void
     */
    public function delete_confirm_mastering()
    {
        $param = array();
        $param = array_merge($param, $this->init_mastering());

        if (isset($_GET["param1"]) && isset($_GET["param2"])) {

            $param = array_merge($param, $this->assignation_mastering());
            $deletable = $param["mastering"];

            if (!$param["user"]->is_admin() && ($param["user"]->id !== $param["user_mastering"]->id || !$param["mastering"])) {
                $this->redirect("skill", "user_skills", $param["user"]->id);
            } else if ($param["user"]->is_admin() && $param["user_mastering"] && !$param["mastering"]) {
                $this->redirect("skill", "user_skills", $param["user_mastering"]->id);
            } else if ($param["user"]->is_admin() && !$param["user_mastering"] && !$param["mastering"]) {
                $this->redirect("user", "users");
            }
            (new View("delete_confirm"))->show(["user_consulted" => $param["user_consulted"], 'mastering' => $param["mastering"], 'user' => $param["user"], "deletable" => $deletable, "user_mastering" => $param["user_mastering"], "skill_mastering" => $param["skill_mastering"]]);
        } else {
            $this->redirect("skill", "user_skills", $param["user"]->id->id);
        }
    }

    /**
     * delete_mastering : supprimer un mastering de la BD
     *
     * @return void
     */
    public function delete_mastering()
    {
        $user = $this->get_user_or_redirect();
        $param = array();
        if (isset($_GET["param1"]) && isset($_GET["param2"])) {

            $param = array_merge($param, $this->assignation_mastering());
            if (($user->id ===  $_GET["param1"] && $param["mastering"]) || ($user->is_admin() && $param["user_mastering"] && $param["mastering"])) {
                $param["mastering"]->delete();
                $this->redirect("skill", "user_skills", $param["user_mastering"]->id);
            } else if (($user->is_admin() && $param["user_mastering"] && !$param["mastering"]) || (!$user->is_admin() && ($user->id === $_GET["param1"] && !$param["mastering"]) || ($user->id !== $_GET["param1"]))) {
                $this->redirect("skill", "user_skills", $_GET["param1"]);
            } else if ($user->is_admin() && !$param["user_mastering"]) {
                $this->redirect("user", "users");
            }
        } else {
            $this->redirect("skill", "user_skills", $user->id);
        }
    }
}
