<?php
require_once "framework/Model.php";
require_once "model/Deletable.php";

class Place extends Model implements Deletable
{
    public $id;
    public $name;
    public $city;

    /**
     * __construct
     *
     * @param  mixed $id
     * @param  mixed $name
     * @param  mixed $city
     * @return Place
     */
    public function __construct($id, $name, $city)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }

    /**
     * get_place_by_id
     *
     * @param  int $id
     * @return Place or false
     */
    public static function get_place_by_id($id)
    {
        $query = self::execute("select * from place where ID = :id", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Place($data["ID"], $data["Name"], $data["City"]);
        }
    }

    /**
     * delete : delete place from DB
     *
     * @param  mixed $id
     * @return Place or false
     */
    public function delete()
    {
        return self::execute('Delete from place where ID = :id', array('id' => $this->id));
    }

    /**
     * save : insert or update place
     *
     * @return Place or false
     */
    public function save()
    {
        if (self::get_place_by_id($this->id)) {
            self::execute(
                "update place set name = :name, city= :city where id = :id",
                array("name" => $this->name, "city" => $this->city, "id" => $this->id)
            );
        } else {
            self::execute(
                "insert into place (name, city) values (:name, :city)",
                array("name" => $this->name, "city" => $this->city)
            );
        }
        return $this;
    }

    /**
     * get_all_places
     *
     * @return places[]
     */
    public static function get_all_places()
    {
        $query = self::execute("SELECT * FROM place", array());
        $data = $query->fetchAll();
        $places = [];
        foreach ($data as $row) {
            $places[] = new Place($row["ID"], $row["Name"], $row["City"]);
        }
        return $places;
    }
    /**
     * validate_place : validate inputs
     *
     * @param  mixed $field
     * @return errors[]
     */
    private static function validate($field, $message)
    {
        $errors = array();
        if (!(isset($field) && is_string($field) && strlen($field) > 0)) {
            $errors[] = $message . "'s is required.";
        }
        if (!(isset($field) && is_string($field) && strlen($field) >= 3)) {
            $errors[] = $message . "'s length must be at least 3 characters.";
        }
        if (!(isset($field) && is_string($field) && strlen($field) < 128)) {
            $errors[] = $message . "'s length should have at most 128 characters.";
        }
        return $errors;
    }

    public static function validate_name($name)
    {
        return self::validate($name, "Name");
    }

    public static function validate_city($city)
    {
        return self::validate($city, "City");
    }

    /**
     * validate_unicity : check that there is only a name/city couple in DB
     *
     * @param  mixed $name
     * @param  mixed $city
     * @return errors[]
     */
    public static function validate_unicity($name, $city)
    {
        $errors = [];
        $query = self::execute("select * from place where Name = :name and City = :city", array("name" => $name, "city" => $city));
        if ($query->rowCount() != 0) {
            $errors[] = "This couple of name/city already exist";
        }
        return $errors;
    }

    public function print_delete_confirm_message()
    {
        return "Do you really want to delete Place (" . $this->name . ", " . $this->city . ") ?";
    }

    public function get_URL_cancel($user_consulted = null)
    {
        return "place/cancel_delete";
    }

    public function get_URL_delete($user_consulted = null)
    {
        return "place/delete_place/" . $this->id;
    }
}
