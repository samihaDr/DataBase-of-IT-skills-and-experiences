<?php
require_once 'framework/Controller.php';
require_once 'framework/View.php';
require_once 'model/User.php';
require_once 'model/Place.php';
require_once 'model/Experience.php';

class ControllerPlace extends Controller
{

    public function index()
    {
    }
    /**
     * init_place : initialisation de l'objet place 
     *
     * @return void
     */
    private function init_place()
    {
        $param = array();
        $param["user"] = $this->get_user_or_redirect();
        $param["name"] = "";
        $param["city"] = "";
        $param["errors"] = array();
        $param["show_modal_error"] = false;
        return $param;
    }
    public function places()
    {
        $param = array();
        $experiences_by_place_counter = array();
        $param = array_merge($this->init_place(), $param);

        if ($param["user"]->is_admin()) {
            $places = Place::get_all_places();

            foreach ($places as $place) {
                $experiences_by_place_counter[] = count(Experience::get_experience_by_place($place->id));
            }

            (new View("manage_places"))->show(array("user" => $param["user"], "places" => $places, "experiences_by_place_counter" => $experiences_by_place_counter, "name" => $param["name"], "city" => $param["city"], "show_modal_error" => $param["show_modal_error"], "errors" => $param["errors"]));
        } else {
            $this->redirect("user", "index");
        }
    }

    /**
     * start_validation_place : permet de lancer les validation dans l'ajout et l'edition d'une place
     *
     * @param  mixed $name
     * @param  mixed $city
     * @return void
     */
    private function start_validation_place($name, $city)
    {
        $errors = Place::validate_name($name);
        $errors = array_merge(Place::validate_city($city), $errors);
        $errors = array_merge(Place::validate_unicity($name, $city), $errors);
        return $errors;
    }

    /**
     * assignation_place : l'assignation d'une nouvelle place ou une place editable
     */
    private function assignation_place($id = null, $name, $city)
    {
        $id_place = $id ?? -1;
        $param["name"] = $name;
        $param["city"] = $city;
        return new Place($id_place, $name, $city);
    }
    public function add_place()
    {
        $param = array();
        $param = array_merge($this->init_place(), $param);
        if ($param["user"]->is_admin()) {
            if (isset($_POST['name'], $_POST['city'])) {
                $param["name"] = $_POST['name'];
                $param["city"] = $_POST['city'];
                $param["errors"] = array_merge($this->start_validation_place($_POST["name"], $_POST['city']), $param["errors"]);

                if (count($param["errors"]) == 0) {
                    $place = $this->assignation_place(null, $param["name"], $param["city"]);
                    $place->save();
                    $this->redirect("place", "places");
                } else {
                    $param["show_modal_error"] = true;
                }

                $places = Place::get_all_places();

                foreach ($places as $place) {
                    $experiences_by_place_counter[] = count(Experience::get_experience_by_place($place->id));
                }
                (new View("manage_places"))->show(array("user" => $param["user"], "places" => $places, "experiences_by_place_counter" => $experiences_by_place_counter, "errors" => $param["errors"], "name" => $param["name"], "city" => $param["city"], "show_modal_error" => $param["show_modal_error"]));
            } else {
                $this->redirect("place", "places");
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    public function edit_place()
    {
        $param = array();
        $experiences_by_place_counter = array();
        $param = array_merge($this->init_place(), $param);
        if ($param["user"]->is_admin()) {
            if (isset($_POST['place_id'], $_POST['name_edit'], $_POST['city_edit'])) {
                $place_edit = $this->assignation_place($_POST['place_id'], $_POST['name_edit'], $_POST['city_edit']);
                $place_actual = Place::get_place_by_id($_POST['place_id']);

                $param["errors"] = Place::validate_name($place_edit->name);
                $param["errors"] = array_merge(Place::validate_city($place_edit->city), $param["errors"]);

                if ($place_actual->name !== $place_edit->name || $place_actual->city !== $place_edit->city) {
                    $param["errors"] = array_merge(Place::validate_unicity($place_edit->name, $place_edit->city), $param["errors"]);
                }

                if (count($param["errors"]) == 0) {
                    $place_edit->save();
                    $this->redirect("place", "places");
                } else {
                    $param["show_modal_error"] = true;
                }

                $places = Place::get_all_places();
                $experiences_by_place_counter = array_merge($this->update_places_list($places,$place_edit),$experiences_by_place_counter);

                (new View("manage_places"))->show(array("user" => $param["user"], "places" => $places, "experiences_by_place_counter" => $experiences_by_place_counter, "errors" => $param["errors"], "name" => $param["name"], "city" => $param["city"], "show_modal_error" => $param["show_modal_error"]));
            } else {
                $this->redirect("place", "places");
            }
        } else {
            $this->redirect("user", "index");
        }
    }
    //replace in places list the edited place
    private function update_places_list($places,$place_edit)
    {
        $experiences_by_place_counter = array();
        for ($i = 0; $i < count($places); $i++) {
            if ($places[$i]->id== $place_edit->id) {
                $places[$i]->name = $place_edit->name;
                $places[$i]->city = $place_edit->city;
            }
            $experiences_by_place_counter[] = count(Experience::get_experience_by_place($places[$i]->id));
        }
            return $experiences_by_place_counter ;
    }
    public function delete_confirm_place()
    {
        $user = $this->get_user_or_redirect();
        $user_consulted = null; //passed into method in view to delete place
        if ($user->is_admin()) {
            if (isset($_GET['param1'])) {
                $place_id = $_GET['param1'];
                $place = Place::get_place_by_id($place_id);
                $deletable = $place;
                $experiences_by_place_counter = count(Experience::get_experience_by_place($place->id));

                if ($place && $experiences_by_place_counter == 0) {
                    (new View("delete_confirm"))->show(array("user_consulted" => $user_consulted, "user" => $user, "place" => $place, "deletable" => $deletable));
                } else {
                    $this->redirect("place", "places");
                }
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    public function delete_place()
    {
        $user = $this->get_user_or_redirect();
        if ($user->is_admin()) {
            if (isset($_GET['param1'])) {
                $place_id = $_GET["param1"];
                $place = Place::get_place_by_id($place_id);
                $experiences_by_place_counter = count(Experience::get_experience_by_place($place->id));

                if ($place && $experiences_by_place_counter == 0) {
                    $place->delete();
                }
                $this->redirect("place", "places");
            }
        } else {
            $this->redirect("user", "index");
        }
    }

    public function cancel_delete()
    {
        $user = $this->get_user_or_redirect();
        if ($user->is_admin()) {
            $this->redirect("place", "places");
        } else {
            $this->redirect("user", "index");
        }
    }
}
