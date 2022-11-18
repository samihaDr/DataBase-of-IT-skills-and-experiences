<?php
require_once "framework/Model.php";
require_once "model/Deletable.php";
class Skill extends Model implements Deletable
{
    public $id;
    public $name;
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    /**
     * validate_name: validation of skill's name
     *
     * @return errors[]
     */
    public static function validate_name($name)
    {
        $errors = array();
        if (!(isset($name) && is_string($name) && strlen($name) > 0)) {
            $errors[] = "Name is required.";
        }
        if (!(isset($name) && is_string($name) && strlen($name) >= 1)) {
            $errors[] = "Name must have at least 1 character.";
        }
        if (!(isset($name) && is_string($name) && strlen($name) < 128)) {
            $errors[] = "Name should have at most 128 character.";
        }
        return $errors;
    }
    public static function unicity_name($name)
    {
        $errors = [];
        $query = self::execute("Select * From skill where name = :name", array("name" => $name));
        if ($query->rowCount() != 0) {
            $errors[] = "This name already exist.";
        }
        return $errors;
    }
    /**
     * get_skills
     *
     * @return array of skills
     */
    public static function get_skills()
    {
        $query = self::execute("Select * from skill order by Name ", array());
        $data = $query->fetchAll();
        $skills = [];
        foreach ($data as $row) {
            $skills[] = new Skill($row["ID"], $row["Name"]);
        }
        return $skills;
    }
   
    /**
     * get_skill_by_id :
     *
     * @param  mixed $id
     * @return Skill or false
     */
    public static function get_skill_by_id($id)
    {
        $query = self::execute("Select *from skill where id = :id", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Skill($data["ID"], $data["Name"]);
        }
    }
    /**
     * save : Insert or update a skill
     *
     * @return Skill
     */
    public function save()
    {
        if (self::get_skill_by_id($this->id)) {
            self::execute("update skill set name = :name where id = :id", array("id" => $this->id, "name" => $this->name));
        } else {
            self::execute("INSERT into skill (name) VALUES (:name)", array("name" => $this->name));
        }
        return $this;
    }
    /**
     * delete : delete a skill whose id is passed as a parameter
     *
     * @param  mixed $id
     * @return Skill or false
     */
    public function delete()
    {
        self::execute('DELETE FROM skill WHERE id = :id', array('id' => $this->id));
        return $this;
    }
    public function print_delete_confirm_message()
    {
        return "Do you really want to delete skill (" . $this->name . ") ?";
    }
    public function get_URL_cancel($user_consulted = null)
    {
        return "skill/cancel";
    }
    public function get_URL_delete($user_consulted = null)
    {
        return "skill/delete_skill/" . $this->id;
    }
}