<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once 'model/Skill.php';
require_once 'model/Experience.php';
require_once 'model/Place.php';
require_once 'model/Mastering.php';

class ControllerUser extends Controller
{
    public function index()
    {
        $user = $this->get_user_or_false();

        if ($user) {
            $this->refresh(USER::get_user_by_id($user->id));
            $this->redirect("experience", "index");
        } else {
            (new View("index"))->show(array("user" => $user));
        }
    }

    public function user_birthdate_before_start_experience_service()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;
        $res = "true";
        if (!$user->is_admin() && $user->id !== $_POST["user_consulted_id"]) {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        } else {
            if (isset($_POST["user_consulted_id"])) {
                $user_consulted = User::get_user_by_id($_POST["user_consulted_id"]);
            }
            if (isset($_POST["start_date"])) {
                $experience_start_date = $_POST["start_date"];
                if ($experience_start_date < $user_consulted->birthdate)
                    $res = "false";
            }
        }
        echo $res;
    }

    /**
     * signouts
     *
     * @return void
     */
    public function signout()
    {
        $this->logout();
        (new View("index"))->show();
    }

    /**
     * login
     *
     * @return void
     */
    public function login()
    {
        $mail = "";
        $password = "";
        $errors = [];
        $show_modal_error = false;

        if (isset($_POST["mail"]) && isset($_POST["password"])) {
            $mail = $_POST["mail"];
            $password = $_POST["password"];
            $errors = User::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($mail));
            } else {
                $show_modal_error = true;
            }
        }
        (new View("login"))->show(["mail" => $mail, "password" => $password, "errors" => $errors, "show_modal_error" => $show_modal_error]);
    }

    //validation des input lors du signup
    private function validate_input($user, $mail, $birthdate, $password, $password_confirm)
    {
        $errors = User::validate_mail($mail);
        $errors = array_merge($errors, User::validate_unicity($mail));
        $errors = array_merge($errors, User::validate_age($birthdate));
        $errors = array_merge($errors, User::validate_password($password));
        $errors = array_merge($errors, $user->validate());
        $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
        return $errors;
    }


    /**
     * signup : gérer une nouvelle inscription
     *
     * @return void
     */
    // gestion de l'inscription d'un nouveau member

    public function signup()
    {
        $mail = '';
        $fullname = '';
        $title = '';
        $password = '';
        $password_confirm = '';
        $birthdate = '';
        $errors = [];
        $show_modal_error = false;

        if (isset($_POST['mail']) && isset($_POST['fullname']) && isset($_POST['title']) && isset($_POST['birthdate']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $mail = $_POST['mail'];
            $fullname = $_POST['fullname'];
            $title = $_POST['title'];
            $birthdate = $_POST['birthdate'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User(-1, $mail, $fullname, $title, Tools::my_hash($password), date('Y-m-d H-i-s'), $birthdate, 'user');
            $errors = $this->validate_input($user, $mail, $birthdate, $password, $password_confirm);

            if (count($errors) == 0) {
                $user->save(); //sauve l'utilisateur dans la BD
                $show_modal_error = false;
                $this->log_user(User::get_user_by_mail($mail));
            } else {
                $show_modal_error = true;
            }
        }
        (new View("signup"))->show(array("mail" => $mail, "fullname" => $fullname, "title" => $title, "birthdate" => $birthdate, "password" => $password, "password_confirm" => $password_confirm, "errors" => $errors, "show_modal_error" => $show_modal_error));
    }

    public function profile()
    {
        $user = $this->get_user_or_redirect();
        (new View("profile"))->show(['user' => $user]);
    }

    private function update_user($user, $mail, $fullname, $title, $birthdate, $errors)
    {
        // si le mail taper dans le textfield est != de celui en base de donnée alors on le check
        if (!$user->check_mails($_POST['email'])) {
            $errors = array_merge($errors, User::validate_mail($_POST['email']));
            $errors = array_merge($errors, User::validate_unicity($_POST['email']));
        }
        $errors = array_merge($errors, User::validate_age($_POST['birthdate']));

        $user->fullname = $fullname;
        $user->title = $title;
        $user->birthdate = $birthdate;
        $user->mail = $mail;
        $errors = array_merge($errors, $user->validate());
        $data = array("user" => $user, "errors" => $errors);
        return $data;
    }

    public function edit_profile()
    {
        $user = $this->get_user_or_redirect();
        $user_edit = new User($user->id, $user->mail, $user->fullname, $user->title, $user->hashed_password, $user->registered_at, $user->birthdate, $user->role);
        $errors = [];
        $show_modal_error = false;
        //si un user essaie d'accèder à l'édit d'un autre user
        if ($_GET['param1'] !== $user->id) {
            $this->redirect("user", "profile");
        }
        if (isset($_POST['email']) && isset($_POST['fullname']) && isset($_POST['title']) && isset($_POST['birthdate'])) {
            $data = $this->update_user($user_edit, $_POST['email'], $_POST['fullname'], $_POST['title'], $_POST['birthdate'], $errors);
            $errors = $data["errors"];
            $user_edit = $data["user"];

            if (count($errors) === 0) {
                $user = $user_edit;
                $user->save();
                $this->refresh($user);
                $this->redirect("user", "profile");
            } else {
                $show_modal_error = true;
            }
        }
        (new View("edit_profile"))->show(["user" => $user, "errors" => $errors, "show_modal_error" => $show_modal_error]);
    }

    private function refresh($user)
    {
        unset($_SESSION['user']);
        $_SESSION["user"] = $user;
    }

    private function update_mastering($level, $skill_id, $user)
    {
        $level = ($level);
        $skill_id = $skill_id;
        $skill = Skill::get_skill_by_id($skill_id);
        $mastering = new Mastering($user->id, $skill, $level);
        $skill_selected = Mastering::get_mastering_by_skill($skill_id);
        $data = array("mastering" => $mastering, "skill_selected" => $skill_selected, 'level' => $level);
        return $data;
    }

    public function edit_user()
    {
        $user = $this->get_user_or_redirect();
        $users = User::get_users();
        $skills = Skill::get_skills();
        $level = null;
        $mastering = null;
        $skill_selected = array();
        $errors = [];
        $show_modal_error = false;

        if ($user->is_admin()) {
            if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
                $user_edit = User::get_user_by_id($_GET["param1"]);
                //si l'id n'existe pas en BD
                if (!$user_edit) {
                    $this->redirect("user", "users");
                }
            }
            if (isset($_POST['email']) && isset($_POST['fullname']) && isset($_POST['title']) && isset($_POST['birthdate']) && isset($_POST['role'])) {
                //gére l'edit du level lors de l'application du filtre
                if (isset($_POST['level']) && isset($_POST['skill_selected_id'])) {
                    $skill_data = $this->update_mastering($_POST['level'], $_POST['skill_selected_id'], $user_edit);
                    $level = $skill_data['level'];
                    $mastering = $skill_data['mastering'];
                    $skill_selected = $skill_data['skill_selected'];
                    $users = User::get_users_by_skill($_POST['skill_selected_id']);
                }
                $user_data = $this->update_user($user_edit, $_POST['email'], $_POST['fullname'], $_POST['title'], $_POST['birthdate'], $errors);
                if ($mastering) {
                    $errors = $mastering->validate_level();
                }

                $user_edit = $user_data["user"];
                $user_edit->role = $_POST['role'];
                $this->keep_errors_input($users, $user_edit, $skill_selected, $level);
                $errors = array_merge($errors, $user_data["errors"]);

                if (count($errors) === 0) {
                    $user_edit->save();
                    //si le manager edite son profile via manage_users 
                    if ($user_edit->is_admin() && $user_edit->id === $user->id) {
                        $this->refresh($user_edit);
                    }
                    if ($level !== null) {
                        $mastering->save();
                    }
                    isset($_POST['skill_selected_id']) ? $this->redirect("user", "users", "by_skill", $_POST['skill_selected_id'])
                        : $this->redirect("user", "users");
                } else {
                    $show_modal_error = true;
                }
            }
            (new View("manage_users"))->show(['users' => $users, 'user' => $user, 'skills' => $skills, 'skill_selected' => $skill_selected, 'errors' => $errors, "show_modal_error" => $show_modal_error]);

        } else {
            $this->redirect("user", "index");
        }
    }

    private function keep_errors_input($users, $user_edit, $skill_selected, $level)
    {
        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]->id == $user_edit->id) {
                $users[$i]->fullname = $user_edit->fullname;
                $users[$i]->title = $user_edit->title;
                $users[$i]->birthdate = $user_edit->birthdate;
                $users[$i]->mail = $user_edit->mail;
                $users[$i]->role = $user_edit->role;

                if (count($skill_selected) != 0)
                    $skill_selected[$i]->level = $level;
                break;
            }
        }
    }

    public function change_password()
    {
        $user = $this->get_user_or_redirect();
        $user_selected = null;
        $new_password = "";
        $new_password_confirm = "";
        $errors = [];
        $show_modal_error = false;

        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user_selected = User::get_user_by_id($_GET["param1"]);
            if (!$user_selected)
                // in case i'm admin and user_selected doesn't exist
                if ($user->is_admin())
                    $this->redirect("user", "users");
                else
                    // in case i'm not admin and user_selected doesn't exist
                    $this->redirect("user", "profile");
            else
                // in case i'm not admin and user_selected exist
                if (!$user->is_admin())
                    $this->redirect("user", "profile");
        }

        if (isset($_POST['new_password']) && isset($_POST['new_password_confirm'])) {
            $new_password = $_POST['new_password'];
            $new_password_confirm = $_POST['new_password_confirm'];
            $errors = User::validate_password($new_password);
            $errors = array_merge($errors, User::validate_passwords($new_password, $new_password_confirm));

            if (empty($errors)) {
                if ($user_selected) {
                    $user_selected->hashed_password = Tools::my_hash($new_password);
                    $user_selected->save();
                } else {
                    $user->hashed_password = Tools::my_hash($new_password);
                    $user->save();
                }

                if ($user->is_admin() && isset($_GET['param1']))
                    $this->redirect("user", "users");
                else
                    $this->redirect("user", "profile");
            } else {
                $show_modal_error = true;
            }
        }
        (new View("change_password"))->show(["user" => $user, "user_selected" => $user_selected, "new_password" => $new_password, "new_password_confirm" => $new_password_confirm, "errors" => $errors, "show_modal_error" => $show_modal_error]);
    }

    public function delete_confirm_user()
    {
        $user = $this->get_user_or_redirect();
        $user_selected = null;
        $user_consulted = null; //passed into method in view to delete user

        if (isset($_GET["param1"])) {
            if ($user->is_admin()) {
                $id = $_GET["param1"];
                //évite l'accès à la confirmation de suppression de l'admin connecté
                if ($id === $user->id) {
                    $this->redirect("user", "users");
                }
                $user_selected = User::get_user_by_id($id);
                $deletable = $user_selected;
                //si un user id n'existe pas en base de donnée
                if (!$user_selected)
                    $this->redirect("user", "users");
            } else {
                $this->redirect("user", "index");
            }

            (new View("delete_confirm"))->show(["user" => $user, 'user_selected' => $user_selected, "user_consulted" => $user_consulted, "deletable" => $deletable]);
        }
    }

    public function delete_user()
    {
        $user = $this->get_user_or_redirect();
        $user_selected = null;
        if (isset($_GET["param1"])) {
            $user_selected = User::get_user_by_id($_GET["param1"]);
            //si un user id n'existe pas en base de donnée
            if (!$user_selected)
                $this->redirect("user", "users");
            if ($user->is_admin() && $user_selected->id !== $user->id) {
                $id = $_GET["param1"];
                $user_selected = User::get_user_by_id($id);
                //supression du user et toutes les dépendances (cf methode dans User model)
                $user_selected->delete();
                $this->redirect("user", "users");
            } else {
                $this->redirect("user", "index");
            }
        }
    }

    public function get_skill_service()
    {
        $user = $this->get_user_or_redirect();
        if ($user->is_admin()) {
            echo json_encode(Skill::get_skills());
        } else {
            $this->redirect("user", "index");
        }

    }

    private function add_key_to_users_list($users)
    {
        if (!empty($users)) {
            for ($i = 0; $i < count($users); ++$i) {
                $users[$i]->skill_count = $users[$i]->get_count_skills_by_user();
                $users[$i]->experience_count = $users[$i]->get_count_experience_by_user();
            }
        }

    }

    public function get_users_by_skills_service()
    {
        $user = $this->get_user_or_redirect();
        $users = User::get_users();
        $this->add_key_to_users_list($users);
        if ($user->is_admin()) {
            if (isset($_POST['list_skill'])) {
                $list_skill = $_POST['list_skill'];
                if (!empty($list_skill)) {
                    $users = User::get_users_by_skills($list_skill);
                    $this->add_key_to_users_list($users);
                    echo json_encode($users);
                }
            } else {
                echo json_encode($users);
            }
        }else {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function get_users_service()
    {
        $user = $this->get_user_or_redirect();
        $users = User::get_users();
        $this->add_key_to_users_list($users);
        if ($user->is_admin()) {
            echo json_encode($users);
        }else {
            header("HTTP/1.1 401 Unauthorized");
            exit;
        }
    }

    public function users()
    {
        $user = $this->get_user_or_redirect();
        $users = User::get_users();
        $skills = Skill::get_skills();
        $skill_selected = array();
        $skill_filtered_id=null;
        $errors = array();
        $show_modal_error = false;
        if ($user->is_admin()) {
            if (isset($_POST['selected_skill_id'])) {
                $this->redirect("user", "users", "by_skill", $_POST['selected_skill_id']);
            } else if (isset($_GET['param2'])) {
                $skill_filtered_id=$_GET['param2'];
                $users = User::get_users_by_skill($_GET['param2']);
                $skill_selected = Mastering::get_mastering_by_skill($_GET['param2']);
                $skill = Skill::get_skill_by_id($_GET['param2']);
                if (!$skill)
                    $this->redirect("user", "users");
            }
            (new View("manage_users"))->show(array('users' => $users, 'user' => $user, 'errors' => $errors, "skill_selected" => $skill_selected, "skill_filtered_id"=>$skill_filtered_id,"skills" => $skills, "show_modal_error" => $show_modal_error));
        } else {
            $this->redirect("user", "index");
        }
    }

}
