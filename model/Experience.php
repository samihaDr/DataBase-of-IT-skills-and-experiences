<?php

require_once "framework/Model.php";
require_once "User.php";

class Experience extends Model implements Deletable
{
    public $id;
    public $start;
    public $stop;
    public $title;
    public $description;
    public $user_id;
    public $place;
    public $skills;
    public $masterings;


    public function __construct($id, $start, $title, $user_id, $place, $skills  = array() , $stop = null, $description = null, $masterings = array())
    {
        $this->id = $id;
        $this->start = $start;
        $this->stop = $stop;
        $this->title = $title;
        $this->description = $description;
        $this->user_id = $user_id;
        $this->place = $place;
        $this->skills = $skills;
        $this->masterings = $masterings;
    }

    /**
     * validate_start_date : validation input
     *
     * @param  mixed $start
     * @param  mixed $birthdate
     * @return array $errors
     */
    public static function validate_start_date($start, $birthdate)
    {
        $errors = array();
        if (!(isset($start) && strlen($start) > 0)) {
            $errors[] = "Start date is required.";
        }
        if (!(isset($start) && strtotime($start) < strtotime(date("Y-m-d")))) {
            $errors[] = "Start date should be before today.";
        }
        if (!(isset($start) && strtotime($start) > strtotime($birthdate))) {
            $errors[] = "Start date should be after your birthdate.";
        }

        return $errors;
    }

    /**
     * validate_start_date_when_edit : A l'édition vérifier que la date de debut est bien aprés la date de fin (quand on a une date de fin)
     *
     * @param  mixed $start
     * @param  mixed $birthdate
     * @param  mixed $stop
     * @return array
     */
    public static function validate_start_date_when_edit($start, $birthdate, $stop)
    {
        $errors = self::validate_start_date($start,$birthdate);
       
        if (isset($stop) && strtotime($start) > strtotime($stop)) {
            $errors[] = "Start date should be before the end date";
        }
       
        return $errors;
    }



    /**
     * validate_stop_date : validation input
     *
     * @param  mixed $start
     * @param  mixed $stop
     * @param  mixed $birthdate
     * @return array of $errors
     */
    public static function validate_stop_date($start, $stop, $birthdate)
    {
        $errors = array();
        if (isset($stop) && !(strtotime($stop) < strtotime(date("Y-m-d")))) {
            $errors[] = "Stop date should be before today.";
        }
        if (isset($stop) && !(strtotime($stop) > strtotime($start))) {
            $errors[] = "Stop date should be after start date.";
        }
        if (isset($stop) && !(strtotime($stop) > strtotime($birthdate))) {
            $errors[] = "Stop date should be after your birthdate.";
        }
        return $errors;
    }

    /**
     * validate_title : validation input
     *
     * @param string $title
     * @return array $errors
     */
    public static function validate_title($title)
    {
        $errors = array();
        if (!(isset($title) && is_string($title) && strlen($title) > 0)) {
            $errors[] = "Title is required.";
        }
        if (!(isset($title) && is_string($title) && strlen($title) >= 3 && strlen($title) <= 128)) {
            $errors[] = "Title should have min. 3 characters and max. 128 characters.";
        }
        return $errors;
    }

    /**
     * validate_description : validation input
     *
     * @param  string $description
     * @return array $errors
     */
    public static function validate_description($description)
    {
        $errors = array();
        $max_length = Configuration::get("max_length");
        if (isset($description) && strlen($description) > 0 && !(is_string($description) && strlen($description) >= 10 && strlen($description) <= 30)) {
            $errors[] = "Description should have at least 10 characters and at most 30 characters.";
        }
        return $errors;
    }

    /**
     * get_experiences_by_user : retrieve experience list of a specific user
     *
     * @param  int $user_id
     * @return array $experiences
     */
    public static function get_experiences_by_user($user_id)
    {
        $query = self::execute("select * from experience where user = :user_id", array("user_id" => $user_id));
        $data = $query->fetchAll();
        $experiences = array();
        $masterings = Mastering::get_mastering_skills_by_user($user_id);
        $mastering_skills_id = []; 
          foreach ($masterings as $mastering_skill) {
            $mastering_skills_id[] = $mastering_skill->skill->id;
        }
        foreach ($data as $row) {
            $skills = self::get_skills_by_experience($row["ID"]);
            $place = Place::get_place_by_id($row['Place']);
            $experiences[] = new Experience($row['ID'], $row['Start'], $row['Title'], $row['User'], $place, $skills, $row['Stop'], $row['Description'],$mastering_skills_id);
        }
        return $experiences;
    }
    
    /**
     * get_experiences_by_user_with_filter
     *
     * @param  mixed $user_id
     * @param  mixed $start_year
     * @param  mixed $end_year
     * @return void
     */
    public static function get_experiences_by_user_with_filter($user_id,$start_year,$end_year){
        $query = self::execute("select * from experience where user = :user_id AND ((year(Start) >= :start_year  AND  year(Stop) <= :end_year) OR (year(Start) BETWEEN :start_year And :end_year) OR (year(Stop) BETWEEN :start_year And :end_year) OR (year(Start)<= :end_year AND Stop IS NULL))" , array("user_id" => $user_id, "start_year" => $start_year, "end_year" => $end_year));
        $data = $query->fetchAll();
        $experiences = array();
        $masterings = Mastering::get_mastering_skills_by_user($user_id);
        $mastering_skills_id = []; 
          foreach ($masterings as $mastering_skill) {
            $mastering_skills_id[] = $mastering_skill->skill->id;
        }
        foreach ($data as $row) {
            $skills = self::get_skills_by_experience($row["ID"]);
            $place = Place::get_place_by_id($row['Place']);
            $experiences[] = new Experience($row['ID'], $row['Start'], $row['Title'], $row['User'], $place, $skills, $row['Stop'], $row['Description'], $mastering_skills_id);
        }
        return $experiences;
    }

    /**
     * get_experience_by_id : retrieve one experience
     *
     * @param  int $experience_id
     * @return Experience
     */
    public static function get_experience_by_id($experience_id)
    {
        $query = self::execute("select * from experience where id = :experience_id", array("experience_id" => $experience_id));
        $skills = self::get_skills_by_experience($experience_id);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            $place = Place::get_place_by_id($row['Place']);
            return new Experience($row['ID'], $row['Start'], $row['Title'], $row['User'], $place, $skills, $row['Stop'], $row['Description']);
        }
    }

    /**
     * delete : delete one experience of a specific user and his using skills
     *
     * @param  int $experience_id
     * @return Experience or false
     */
    public function delete()
    {
            // delete using skills
            self::execute("delete from `using` where experience = :experience_id", array("experience_id" => $this->id));

            // delete experience
            self::execute("delete from experience where id = :id", array('id' => $this->id));
            
            return $this;
    }

    /**
     * get_user_skills_by_experience : get user skills by experience
     *
     * @param  mixed $experience_id
     * @return array using skills
     */
    public static function get_user_skills_by_experience($experience_id)
    {
        $query = self::execute("select * from `using` where experience = :experience_id", array("experience_id" => $experience_id));
        return $query->fetchAll();
    }
    
    /**
     * get_skills_by_experience
     *
     * @param  mixed $experience_id
     * @return $skills[]
     */
    public static function get_skills_by_experience($experience_id)
    {

        $query = self::execute("select * from skill where id in (select skill from `using` where experience = :experience_id)", array("experience_id" => $experience_id));
        $data = $query->fetchAll();
        $skills = array();
        foreach ($data as $row) {
            $skills[] = new Skill($row['ID'], $row['Name']);
        }
        return $skills;
    }
    
    /**
     * get_experience_by_skill
     *
     * @param  mixed $skill
     * @return array of using_skill
     */
    public static function get_experience_by_skill($skill)
    {
        $query = self::execute("Select * from `using` where skill = :skill", array("skill" => $skill));
        return $query->fetchAll();
    }
    
    /**
     * get_experience_by_place
     *
     * @param  mixed $place_id
     * @return array of experiences
     */
    public static function get_experience_by_place($place_id)
    {
        $query = self::execute("select * from experience where place = :place_id", array(":place_id" => $place_id));
        return $query->fetchAll();
    }

    /**
     * save : update or insert experience
     *
     * @param  array $using_skills
     * @return void
     */
    public function save()
    {
        $experience = $this->get_experience_by_id($this->id);
        if ($experience) { // update

            // update actual experience
            self::execute(
                "update experience set start = :start, stop = :stop, title = :title, description = :description, place = :place where id=:experience_id",
                array("start" => $this->start, "stop" => $this->stop, "title" => $this->title, "description" => $this->description, "place" => $this->place->id, "experience_id" => $experience->id)
            );

            // delete actual using_skills
            self::execute("delete from `using` where experience = :experience_id", array("experience_id" => $this->id));

            // add new skills
            foreach ($this->skills as $skill) {
                self::execute("insert into `using` (experience, skill) values (:experience_id, :skill_id)", array("experience_id" => $this->id, "skill_id" => $skill->id));
            }
        } else { // insert

            // add new experience
            self::execute(
                "insert into experience (start, stop, title, description, user, place) values (:start, :stop, :title, :description, :user, :place)",
                array("start" => $this->start, "stop" => $this->stop, "title" => $this->title, "description" => $this->description, "user" => $this->user_id, "place" => $this->place->id)
            );

            $experience_id_last_inserted = self::lastInsertId();

            // add new skills
            foreach ($this->skills as $skill) {
                self::execute("insert into `using` (experience, skill) values (:experience_id, :skill_id)", array("experience_id" => $experience_id_last_inserted, "skill_id" => $skill->id));
            }
        }
    }
    
    /**
     * get_user_by_id
     *
     * @param  mixed $user_id
     * @return User
     */
    private function get_user_by_id($user_id){
        return User::get_user_by_id($user_id);
    }
    
    /**
     * print_delete_confirm_message : displaying message confirmation delete for an experience
     *
     * @return string
     */
    public function print_delete_confirm_message()
    {
        return "Do you really want to delete experience \"" . $this->title . " (" . $this->place->name . " ,"
        . $this->place->city . ")\" of " . $this->get_user_by_id($this->user_id)->fullname 
        . " and all of its dependencies ?";   
    }
    
    /**
     * get_URL_cancel
     *
     * @param  mixed $user_consulted
     * @return $url_cancel
     */
    public function get_URL_cancel($user_consulted=null)
    {
        $url_cancel = "experience/cancel";
        if($user_consulted){
            $url_cancel = "experience/cancel/".$user_consulted->id."/".$this->id;
        }
        return $url_cancel;
    }
    
    /**
     * get_URL_delete
     *
     * @param  mixed $user_consulted
     * @return $url_delete
     */
    public function get_URL_delete($user_consulted=null)
    {
        $url_delete = "experience/delete_experience/". $this->id;
        if($user_consulted)
            $url_delete = "experience/delete_experience/" . $user_consulted->id. "/" . $this->id;
           
        return $url_delete;
    }

    /**
     * get_min_max_years_for_experiences_by_user : get limit min and max years for all experiences of a user
     *
     * @param  mixed $user_id
     * @return array
     */
    public static function get_min_max_years_for_experiences_by_user($user_id){
        $query= self::execute("select min(start) as min_date,
                                if((select count(*) from experience where stop is null and user = :user_id) = 
                                (select count(*) from experience where user = :user_id), max(start), max(stop)) as max_date
                                from experience where user = :user_id", array("user_id" => $user_id));
        $data = $query->fetch();
        
        $min_year = date_format(date_create($data["min_date"]), 'Y');
        $max_year =  date_format(date_create($data["max_date"]), 'Y');
        $data = array("min_date" => $min_year, "max_date" => $max_year);

        return $data;
    }
}
