<?php
require_once "framework/Model.php";
require_once "model/Deletable.php";

class Mastering extends Model implements Deletable
{
    public $user;
    public $skill;
    public $level;

    /**
     * __construct
     *
     * @param  mixed $user
     * @param  mixed $skill
     * @param  mixed $level
     * @return Mastering
     */
    public function __construct($user, $skill, $level)
    {
        $this->user = $user;
        $this->skill = $skill;
        $this->level = $level;
    }

    /**
     * validate_level
     *
     * @return errors[]
     */
    public function validate_level()
    {
        $errors = array();
        if (!(isset($this->level) && is_numeric($this->level))) {
            $errors[] = "Level is required.";
        }if (!(isset($this->level) && is_numeric($this->level) && ($this->level) >= 1 && ($this->level) <= 5)) {
            $errors[] = "The level value must be between 1 and 5.";
        }
        return $errors;
    }

    /**
     * get_mastering
     *
     * @param  mixed $user
     * @param  mixed $skill
     * @return array of Mastering or false
     */
    public static function get_mastering_skills_by_user($user)
    {
        $query = self::execute("select * from mastering where user = :user", array("user" => $user));
        $data = $query->fetchAll();
        $mastering_skills = array();
        foreach ($data as $row) {
            $skill = Skill::get_skill_by_id($row['Skill']);
            $mastering_skills[] = new Mastering($row["User"], $skill, $row["Level"]);
        }
        return $mastering_skills;
    }
       
    /**
     * get_user_by_skill
     *
     * @param  mixed $skill
     * @return mastering[] or false
     */
    public static function get_mastering_by_skill($skill){
        $query = self::execute("Select * from mastering where skill= :skill", array("skill" => $skill));
        $data = $query->fetchAll();
        $mastering_skills = array();
        foreach ($data as $row) {
            $skill = Skill::get_skill_by_id($row['Skill']);
            $mastering_skills[] = new Mastering($row["User"], $skill, $row["Level"]);
        }
        return $mastering_skills;
    }

    /**
     * get_mastering
     *
     * @param  mixed $user
     * @param  mixed $skill
     * @return Mastering or false
     */
    public static function get_mastering($user,$skill){
        $query = self::execute("select * from mastering where User = :user and Skill = :skill", array("user"=>$user, "skill"=>$skill));
        $data = $query->fetch();
        if($query->rowCount() == 0){
            return false;
        }else {
            $skill = Skill::get_skill_by_id($data['Skill']);
            return new Mastering($data["User"], $skill, $data["Level"]);
        }
        
    }
    

    /**
     * save : update or insert Mastering
     *
     * @return Mastering or false
     */
    public function save()
    {
        if (self::get_mastering($this->user, $this->skill->id)) {
            self::execute("Update mastering set level = :level where user = :user and skill = :skill",
                array("user" => $this->user, "skill" => $this->skill->id, "level" => $this->level));
        } else {
            self::execute("INSERT INTO mastering (user, skill, level) VALUES(:user,:skill,:level)",
                array("user" => $this->user, "skill" => $this->skill->id, "level" => $this->level));
        }
        return $this;
    }

    /**
     * get_skills_by_user
     *
     * @param  mixed $user
     * @return skills[]
     */
    public function get_skills_by_user($user)
    {
        $query = self::execute("select * from mastering where user = :user", array("user" => $user));
        $data = $query->fetchAll();
        $skills = [];
        foreach ($data as $row) {
            $skills[] = new Skill($row["User"], $row["Skill"], $row["Level"]);
        }
    }

    /**
     * delete
     *
     * @return Mastering or false
     */
    public function delete()
    {
        if (self::get_mastering($this->user, $this->skill->id)) {
            self::execute('DELETE FROM mastering WHERE user = :user and skill = :skill', array('user' => $this->user, 'skill' => $this->skill->id));
            return $this;
        }
        return false;
    }

    public function print_delete_confirm_message()
    {
        return "Do you really want to delete mastering (" . $this->skill->name . ")" . " of " 
        . User::get_user_by_id($this->user)->fullname . " ?";   
    }

    public function get_URL_cancel($user_consulted=null)
    {
        return "skill/user_skills/". $this->user;
    }

    public function get_URL_delete($user_consulted=null)
    {
        $a_user_id = $this->user;
        $a_skill_id = $this->skill->id;
        $a_url = "skill/delete_mastering/". $this->user . "/" . $this->skill->id;
        return "skill/delete_mastering/". $this->user . "/" . $this->skill->id;
    }

  

}
