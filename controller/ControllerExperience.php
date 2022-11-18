<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Experience.php';
require_once 'model/Skill.php';
require_once 'model/Mastering.php';
require_once 'model/Place.php';
require_once 'model/User.php';

class ControllerExperience extends Controller
{
    /**
     * index : List of experiences
     *
     * @return void
     */
    public function index()
    {
        $this->experiences();
    }

    /**
     * experiences : displays experiences list of a particular user
     *
     * @return void
     */
    public function experiences()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;
        $masterings = array();
        $id = $user_consulted->id;
        $has_parameter = "false";

        if (isset($_GET["param1"]) && $_GET['param1'] !== $id) {
            $has_parameter = "true";
            if ($user->is_admin()) {
                //user id admin try to get in
                $id = $_GET["param1"];
                //for use in filtering experiences
                $has_parameter = "true";
                //to display the right name of the user in the view
                $user_consulted = User::get_user_by_id($id);
                if (!$user_consulted) {
                    $this->redirect("experience", "experiences");
                }
            } else {
                $this->redirect("experience", "experiences");
            }
        }

        $experiences = Experience::get_experiences_by_user($id);
        $mastering_skills = Mastering::get_mastering_skills_by_user($id);

        //get mastering ids to display in view experiences
        foreach ($mastering_skills as $mastering_skill) {
            $masterings[] = $mastering_skill->skill->id;
        }

        (new View("experiences"))->show(array("experiences" => $experiences, "user" => $user, "user_consulted" => $user_consulted, "masterings" => $masterings, "has_parameter" => $has_parameter));
    }

    /**
     * add_experience_to_user : add an experience to a user
     *
     * @return void
     */
    public function add_experience_to_user()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;

        // check when admin try to add a new experience
        if (isset($_GET["param1"]) && $user->is_admin()) {
            $user_id = $_GET['param1'];
            $user_consulted = User::get_user_by_id($user_id);
            //case if param1 dont get a user
            if ($user_consulted == $user) {
                $this->redirect("experience", "experiences");
            }
            // redirect to list experiences when simple user try to add experience like admin permission
        } else if (!$user->is_admin() && isset($_GET['param1'])) {
            $this->redirect("experience", "experiences");
        }
        $this->add_experience($user, $user_consulted);
    }

    /**
     * add_experience : private method => add experience to a particular user
     *
     * @param mixed $user
     * @param mixed $user_consulted
     * @return void
     */
    private function add_experience($user, $user_consulted)
    {
        $places = Place::get_all_places();
        $skills_all = Skill::get_skills();
        $start = null;
        $title = "";
        $place = null;
        $show_modal_error = false;
        $errors = array();
        $skills = array();

        $user_actual = $user_consulted ?? $user;

        $experience = new Experience(-1, $start, $title, $user_actual->id, $place);

        if (isset($_POST['start'], $_POST['title'], $_POST['selected-place'])) {

            $experience = $this->experience_post_assignation(-1, $user_actual, $skills);
            $errors = $this->add_experience_check_validation($experience, $user_actual);

            if (count($errors) == 0) {
                $this->save_experience($experience, $user_consulted);
            } else {
                $show_modal_error = true;
            }
        }
        (new View("add_new_experience"))->show(array("user" => $user, "user_consulted" => $user_consulted, "places" => $places, "skills_all" => $skills_all, "errors" => $errors, "experience" => $experience, "show_modal_error" => $show_modal_error));
    }

    /**
     * experience_post_assignation : create experience with $_POST data from the form
     *
     * @param mixed $experience_id
     * @param mixed $user_actual
     * @param mixed $skills
     * @return experience
     */
    private function experience_post_assignation($experience_id, $user_actual, $skills)
    {
        $start = $_POST['start'];
        $stop = !empty($_POST['stop']) ? $_POST['stop'] : null;
        $title = $_POST['title'];
        $place = Place::get_place_by_id($_POST['selected-place']);
        $description = !empty($_POST['description']) ? $_POST['description'] : null;
        $using_skills = $_POST['checked-skills'] ?? array();
        //return -1 in case we add an experience, return $experience_id in case we edit an experience
        $experience_id = $experience_id ?? -1;
        $skills = $this->get_using_skills($using_skills, $skills);

        return new Experience($experience_id, $start, $title, $user_actual->id, $place, $skills, $stop, $description);
    }

    /**
     * get_using_skills : get list of skills for an experience
     *
     * @param mixed $using_skills
     * @param mixed $skills
     * @return $skills[]
     */
    private function get_using_skills($using_skills, $skills)
    {
        if (count($using_skills) != 0) {
            foreach ($using_skills as $skill_id) {
                $skills[] = Skill::get_skill_by_id($skill_id);
            }
        }
        return $skills;
    }

    /**
     * add_experience_check_validation : checking validation of inputs when adding experience and return an array of errors
     *
     * @param mixed $experience
     * @param mixed $user_actual
     * @return $errors[]
     */
    private function add_experience_check_validation($experience, $user_actual)
    {
        $errors = Experience::validate_title($experience->title);
        $errors = array_merge(Experience::validate_start_date($experience->start, $user_actual->birthdate), $errors);
        if ($experience->stop) {
            $errors = array_merge(Experience::validate_stop_date($experience->start, $experience->stop, $user_actual->birthdate), $errors);
        }
        $errors = array_merge(Experience::validate_description($experience->description), $errors);

        return $errors;
    }

    /**
     * save_experience : save experience when adding or editing
     *
     * @param mixed $experience
     * @param mixed $user_consulted
     * @return void
     */
    private function save_experience($experience, $user_consulted)
    {
        $experience->save();
        if ($user_consulted) {
            $this->redirect("experience", "experiences", $user_consulted->id);
        }
        $this->redirect("experience", "experiences");
    }

    /**
     * edit : edit an experience of a user
     *
     * @return void
     */
    public function edit()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;

        if (isset($_GET["param1"]) && isset($_GET['param2']) && $user->is_admin()) {
            $user_id = $_GET["param1"];
            $user_consulted = User::get_user_by_id($user_id);
        }
        $this->edit_user_experience($user, $user_consulted);
    }

    /**
     * edit_user_experience : edit experience of a particular user
     *
     * @param mixed $user
     * @param mixed $user_consulted
     * @return void
     */
    private function edit_user_experience($user, $user_consulted)
    {
        $places = Place::get_all_places();
        $skills_all = Skill::get_skills();
        $skills = array();
        $errors = [];
        $show_modal_error = false;
        $experience = null;
        $user_actual = $user;

        if (isset($_GET['param1']) && isset($_GET['param2'])) {
            $user_actual = $user_consulted;
            $experience_id = $_GET['param2'];
        } elseif (isset($_GET['param1'])) {
            $experience_id = $_GET['param1'];
        }
        $experience = Experience::get_experience_by_id($experience_id);

        if ($experience && $experience->user_id === $user_actual->id) {
            if (isset($_POST['start'], $_POST['title'], $_POST['selected-place'])) {
                $experience_edit = $this->experience_post_assignation($experience_id, $user_actual, $skills);
                $errors = $this->edit_experience_check_validation($experience, $experience_edit, $user_actual, $errors);
                $experience = $experience_edit; //$experience is displaying in the view

                if (count($errors) == 0) {
                    $this->save_experience($experience, $user_consulted);
                } else {
                    $show_modal_error = true;
                }
            }
        } else {
            $user_consulted ? $this->redirect("experience", "experiences", $user_actual->id) :
                $this->redirect("experience", "experiences");
        }
        (new View("edit_experience"))->show(array("user" => $user, "user_consulted" => $user_consulted, "experience" => $experience, "places" => $places, "skills_all" => $skills_all, "errors" => $errors, "show_modal_error" => $show_modal_error));
    }

    /**
     * edit_experience_check_validation : checking validation of inputs when editing experience and return an array of errors
     *
     * @param mixed $experience
     * @param mixed $experience_edit
     * @param mixed $user_actual
     * @return $errors[]
     */
    private function edit_experience_check_validation($experience, $experience_edit, $user_actual, $errors)
    {
        if ($experience_edit->start != $experience->start) {
            $errors = Experience::validate_start_date_when_edit($experience_edit->start, $user_actual->birthdate, $experience_edit->stop);
        }
        if ($experience_edit->stop != $experience->stop) {
            $errors = array_merge($errors, Experience::validate_stop_date($experience_edit->start, $experience_edit->stop, $user_actual->birthdate));
        }
        if ($experience_edit->title != $experience->title) {
            $errors = array_merge($errors, Experience::validate_title($experience_edit->title));
        }
        if ($experience_edit->description != $experience->description) {
            $errors = array_merge($errors, Experience::validate_description($experience_edit->description));
        }

        return $errors;
    }

    /**
     * delete_confirm_experience : confirmation delete of an experience
     *
     * @return void
     */
    public function delete_confirm_experience()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = null;
        $has_parameter = "false";

        if (isset($_GET["param1"]) && isset($_GET['param2']) && $user->is_admin()) {
            $user_id = $_GET["param1"];
            $has_parameter = "true";
            $user_consulted = User::get_user_by_id($user_id);
        }
        $this->confirm_delete_experience($user, $user_consulted, $has_parameter);
    }

    /**
     * confirm_delete_experience : confirmation delete of an experience
     *
     * @return void
     */
    private function confirm_delete_experience($user, $user_consulted, $has_parameter)
    {
        //get experience id whether there is one or two parameters passed in the url
        if ($user_consulted) {
            $experience_id = ($_GET['param2']);
        } elseif (isset($_GET['param1'])) {
            $experience_id = ($_GET['param1']);
        }

        $experience = Experience::get_experience_by_id($experience_id);
        $deletable = $experience;

        if ($experience == null || ($user_consulted == null && $experience->user_id !== $user->id)) {
            $this->redirect("experience", "experiences");
        }

        (new View("delete_confirm"))->show(array("user" => $user, "user_consulted" => $user_consulted, "deletable" => $deletable, "experience" => $experience, "has_parameter" => $has_parameter));
    }

    /**
     * delete_experience : direct delete of an experience
     *
     * @return void
     */
    public function delete_experience()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = null;

        //get user and experience id whether there is one or two parameters passed to the url.
        if (isset($_GET["param1"]) && isset($_GET['param2']) && $user->is_admin()) {
            $user_id = $_GET["param1"];
            $user_consulted = User::get_user_by_id($user_id);
            $experience_id = $_GET['param2'];
        } else if (isset($_GET['param1'])) {
            $experience_id = $_GET['param1'];
        }

        $experience = Experience::get_experience_by_id($experience_id);

        if ($experience) {
            if ($user_consulted && $experience->user_id == $user_consulted->id) {
                $experience->delete();
                $this->redirect("experience", "experiences", $user_consulted->id);
            } else if ($experience->user_id == $user->id) {
                $experience->delete();
                $this->redirect("user", "index");
            }
        }
        $this->redirect("experience", "experiences");
    }

    /**
     * cancel : cancel delete confirmation experience
     *
     * @return void
     */
    public function cancel()
    {
        $user = $this->get_user_or_redirect();

        if (isset($_GET["param1"]) && isset($_GET['param2']) && $user->is_admin()) {
            $user_id = $_GET["param1"];
            $user_consulted = User::get_user_by_id($user_id);
            $this->redirect("experience", "experiences", $user_consulted->id);
        }
        $this->redirect("experience", "experiences");
    }

    /**
     * get_max_length_service
     *
     * @return void
     */
    public function get_max_length_service()
    {
        $user = $this->get_user_or_false();
        $max_length = Configuration::get("max_length");

        echo ($max_length);
    }


    public function get_experiences_by_filter_service()
    {
        $user = $this->get_user_or_false();
        $filtered_experiences = [];

        if (isset($_POST['user_id']) && ($_POST['user_id'] !== "")) {
            $user_consulted = User::get_user_by_id($_POST['user_id']);

            if ($user_consulted && ($user->is_admin() || $user->id === $user_consulted->id)) {
                if (isset($_POST['start_year']) && ($_POST['end_year'])) {
                    $start_year = $_POST['start_year'];
                    $end_year = $_POST['end_year'];
                    $filtered_experiences = Experience::get_experiences_by_user_with_filter($user_consulted->id, $start_year, $end_year);
                } else {
                    echo ("false");
                }
            } else {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
            echo json_encode($filtered_experiences);
        } else {
            echo "false";
        }
    }

    public function get_experiences_service()
    {
        $user = $this->get_user_or_false();
        $user_consulted = $user;
        $experiences = [];
        if (isset($_POST['user_id']) && ($_POST['user_id'] !== "")) {
            $user_consulted = User::get_user_by_id($_POST['user_id']);
            if (!$user_consulted || (!$user->is_admin() && $user->id != $user_consulted->id)) {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        }
        $experiences = Experience::get_experiences_by_user($user_consulted->id);
        echo json_encode($experiences);
    }

    public function delete_service()
    {
        $user = $this->get_user_or_false();
        if (isset($_POST['user_id']) && isset($_POST['id'])) {
            $user_consulted_id = $_POST['user_id'];
            $user_consulted = User::get_user_by_id($user_consulted_id);
            if ($user_consulted && ($user->is_admin() || $user->id === $user_consulted_id)) {
                $experience_id = $_POST['id'];
                $experience = Experience::get_experience_by_id($experience_id);
                if ($experience)
                    $experience->delete();
            } else {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        }
        echo $experience ? "true" : "false";
    }


    public function view_timeline()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = $user;
        if (isset($_GET['param1']) && ($_GET['param1'] == $user->id || $user->is_admin())) {
            $user_consulted = User::get_user_by_id($_GET['param1']);
            $dates = Experience::get_min_max_years_for_experiences_by_user($user_consulted->id);

            $max_years = $dates["max_date"] - $dates["min_date"] + 1; //can't be 0
            $min_year_exp = $dates["min_date"];

            // custom slot duration for calendar view according to max_years
            $slot_duration = $this->custom_slot_duration_by_max_years($max_years);
        } else {
            $this->redirect("experience", "experiences");
        }
        $user_birthdate = $user_consulted->birthdate;
        (new View("timeline"))->show(array("user_consulted" => $user_consulted, "user" => $user, "user_birthdate" => $user_birthdate, "min_year_exp" => $min_year_exp, "max_years" => $max_years, "slot_duration" => $slot_duration));
    }

    private function custom_slot_duration_by_max_years($max_years)
    {
        $slot_duration = 0;

        if ($max_years == 1) {
            $slot_duration = 1;
        } else if ($max_years > 1 && $max_years < 5) {
            $slot_duration = 3;
        } else if ($max_years >= 5 && $max_years < 10) {
            $slot_duration = 4;
        } else if ($max_years >= 10 && $max_years < 20) {
            $slot_duration = 6;
        } else {
            $slot_duration = 0;
        }
        return $slot_duration;
    }

    public function get_experiences_service_for_full_calendar()
    {
        $user = $this->get_user_or_false();
        $experiences_event = array();

        if (isset($_POST["userConsulted"])) {
            $user_consulted_id = $_POST["userConsulted"];
            if ($user->is_admin() || $user->id === $user_consulted_id) {
                $experiences = Experience::get_experiences_by_user($user_consulted_id);
                if (count($experiences) != 0) {
                    foreach ($experiences as $experience) {
                        $experience_stop = $experience->stop;
                        if ($experience->stop == null) {
                            $experience_stop = "2999-01-01";
                        }

                        $background_color_rand = '#' . rand(100, 999);
                        $title = $experience->title . " at " . $experience->place->name . " (" . $experience->place->city . ")";
                        $experiences_event[] = ["start" => $experience->start, "end" => $experience_stop, "title" => $title, "id" => $experience->id, "backgroundColor" => $background_color_rand];
                    }
                    echo json_encode($experiences_event);
                    die;
                }
            } else {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        }
    }

    public function update_experience_service()
    {
        $user = $this->get_user_or_false();
        if (isset($_POST["user_id"], $_POST["experience_id"], $_POST["stop_date"], $_POST["start_date"])) {
            $user_concerned = User::get_user_by_id($_POST["user_id"]);
            
            $start = date_create_from_format('d/m/Y', $_POST["start_date"]);
            $stop = date_create_from_format('d/m/Y', $_POST["stop_date"]);
            $stop = date_format($stop,'Y-m-d');
            $start = date_format($start, 'Y-m-d');
            
            $stopLimit = date_create_from_format('d/m/Y', "01/01/2500");
            $stopLimit = date_format($stopLimit, 'Y-m-d');
            if ($user_concerned && ($user->id === $user_concerned->id || $user->is_admin())) {
                $experience = Experience::get_experience_by_id($_POST["experience_id"]);
                if ($experience) {
                   // update experience dates
                    if( $stop >  $stopLimit){
                        $stop = null;
                    }
                    $experience->start = $start;
                    $experience->stop = $stop;
                    $experience->save();
                } 
            }
            else {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            }
        }
    }
}
