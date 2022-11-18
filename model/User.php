<?php
require_once "framework/Model.php";
require_once "model/Deletable.php";

class User extends Model implements Deletable
{
    const LEGAL_AGE = 18;

    public $id;
    public $mail;
    public $fullname;
    public $title;
    public $hashed_password;
    public $registered_at;
    public $birthdate;
    public $role;

    public function __construct($id, $mail, $fullName, $title, $password, $registered_at, $birthDate, $role)
    {
        $this->id = $id;
        $this->mail = $mail;
        $this->fullname = $fullName;
        $this->title = $title;
        $this->hashed_password = $password;
        $this->registered_at = $registered_at;
        $this->birthdate = $birthDate;
        $this->role = $role;
    }

    public static function get_users()
    {
        $query = self::execute("SELECT * FROM user", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["ID"], $row["Mail"], $row["FullName"], $row["Title"], $row["Password"], $row["RegisteredAt"], $row["Birthdate"], $row["Role"]);
        }
        return $results;
    }


    public static function get_user_by_id($id)
    {
        $query = self::execute("SELECT * FROM user WHERE id=:id", array("id" => $id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["ID"], $data["Mail"], $data["FullName"], $data["Title"], $data["Password"], $data["RegisteredAt"], $data["Birthdate"], $data["Role"]);
        }
    }

    public static function get_user_by_mail($mail)
    {
        $query = self::execute("SELECT * FROM user WHERE Mail=:mail", array("mail" => $mail));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["ID"], $data["Mail"], $data["FullName"], $data["Title"], $data["Password"], $data["RegisteredAt"], $data["Birthdate"], $data["Role"]);
        }

    }

    public function get_count_experience_by_user()
    {
        $query = self::execute("select count(*) from experience where User=:id", array("id" => $this->id));
        $result = $query->fetchColumn();
        return $result;
    }

    public function get_count_skills_by_user()
    {
        $query = self::execute("select count(*) from mastering where User=:id", array("id" => $this->id));
        $result = $query->fetchColumn();
        return $result;
    }

    public static function get_users_by_skills($skills)
    {
        $users = [];
        $array_skill_length = count($skills);
        if (!empty($skills)) {
            $query = self::execute("select * from user join mastering on user.id=mastering.user  where skill in(" . implode(",", $skills) . ") group by user.id having count(*)=:list_length", array("list_length" => $array_skill_length));
            $data = $query->fetchAll();
            foreach ($data as $row) {
                $users[] = new User($row["ID"], $row["Mail"], $row["FullName"], $row["Title"], $row["Password"], $row["RegisteredAt"], $row["Birthdate"], $row["Role"]);
            }
        }
        return $users;
    }

    public static function get_users_by_skill($skill_id)
    {
        $query = self::execute("select * from user where id in(select user from mastering where skill=:skill_id) ", array("skill_id" => $skill_id));
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["ID"], $row["Mail"], $row["FullName"], $row["Title"], $row["Password"], $row["RegisteredAt"], $row["Birthdate"], $row["Role"]);
        }
        return $results;
    }
    public function get_free_skill()
    {
        $query = self::execute("select * from skill where id not in (select skill from mastering where user = :userId)",
            array("userId" => $this->id));
        $data = $query->fetchAll();
        foreach ($data as $row) {
            $skills_left[] = new Skill($row["ID"], $row["Name"]);
        }
        return $skills_left;
    }
    public function delete()
    {
            //delete using
            $experiences = Experience::get_experiences_by_user($this->id);
            foreach ($experiences as $experience) {
                self::execute("delete from `using` where experience =:experience_id", array("experience_id" => $experience->id));
            }
            //delete experience
            self::execute("delete from Experience where User=:user_id", array('user_id' => $this->id));
            //delete mastering
            self::execute("delete from Mastering where User=:user_id", array('user_id' => $this->id));
            //delete user
            self::execute("delete from user where id=:user_id", array('user_id' => $this->id));
            return $this;

        return false;
    }

    public function is_admin()
    {
        return $this->role === "admin";
    }

    public function save()
    {
        if (self::get_user_by_id($this->id)) {

            self::execute("UPDATE user SET Mail=:mail, FullName=:FullName,Title=:Title,Password=:Password,Birthdate=:Birthdate,Role=:Role where id=:id",
                array("mail" => $this->mail, "FullName" => $this->fullname, "Title" => $this->title, "Password" => $this->hashed_password, "Birthdate" => $this->birthdate, "Role" => $this->role, "id" => $this->id));
        } else {
            self::execute("INSERT into user(Mail,FullName,Title,Password,RegisteredAt,Birthdate,Role) VALUES(:Mail,:FullName,:Title,:Password,:RegisteredAt ,:Birthdate,:Role)",
                array("Mail" => $this->mail, "FullName" => $this->fullname, "Title" => $this->title, "Password" => $this->hashed_password, "RegisteredAt" => $this->registered_at, "Birthdate" => $this->birthdate, "Role" => $this->role));
        }
    }

    public function validate()
    {
        $errors = array();

        if (!(isset($this->fullname) && is_string($this->fullname) && strlen($this->fullname) > 0)) {
            $errors[] = "Fullname is required.";
        }
        if (isset($this->fullname) && strlen($this->fullname) > 128) {
            $errors[] = "Fullname should have at most 128 characters";
        }
        if (!(isset($this->fullname) && is_string($this->fullname) && strlen($this->fullname) >= 3)) {
            $errors[] = "Fullname length must have at least 3 characters";
        }
        if (!isset($this->title) || empty($this->title)) {
            $errors[] = "The title is required.";
        }
        if (isset($this->title) && strlen($this->title) > 256) {
            $errors[] = "Title should have at most 256 characters";
        }

        if (!isset($this->birthdate) || empty($this->birthdate)) {
            $errors[] = "The birthday is required";
        }

        return $errors;
    }


    public static function validate_age($birthdate)
    {
        $errors = [];
        if (!(isset($birthdate) && self::is_legal_age(date("Y-m-d"), $birthdate) && strtotime($birthdate) < strtotime(date("Y-m-d")))) {
            $errors[] = "You must have at least " . self::LEGAL_AGE . " years old.";
        }
        if (!(isset($birthdate) && strtotime($birthdate) < strtotime(date("Y-m-d")))) {
            $errors[] = "Birthdate should be before today.";
        }
        return $errors;
    }

    /**
     * is_legal_age : check whether user has legal age to subscribe to jobijoba
     *
     * @param mixed $today
     * @param mixed $birthdate
     * @return bool
     */
    private static function is_legal_age($today, $birthdate)
    {
        return (date_diff(date_create($today), date_create($birthdate))->format('%y') >= self::LEGAL_AGE);
    }

    //faire une nouvelle fonction qui verifie que le password est requis pour le login
    public static function validate_password($password)
    {
        $errors = [];
        if (strlen($password) == 0) {
            $errors[] = "The password is required.";
        }

        if (strlen($password) < 8) {
            $errors[] = "Password length must have at least 8 characters.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:!,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_passwords($password, $password_confirm)
    {
        $errors = [];
        if (empty($errors)) {
            if ($password !== $password_confirm) {
                $errors[] = "The passwords must be the same.";
            }
        }
        return $errors;
    }


    public static function validate_mail($mail)
    {
        $errors = [];
        if (!isset($mail)) {
            $errors[] = "The Email is required.";
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        return $errors;
    }

    public static function validate_unicity($mail)
    {
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "This mail already exists";
        }
        return $errors;
    }

    public function check_mails($mail)
    {
        return $this->mail === $mail;
    }

    public static function validate_login($mail, $password)
    {
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            if ($user->hashed_password !== Tools::my_hash($password)) {
                $errors[] = "The password is not correct";
            }
        } else if (!isset($mail) || strlen($mail) == "") {
            $errors[] = "The email is required";
        } else {
            $errors[] = "The user with the mail '$mail' doesn't exist. Please sign up";
        }


        return $errors;
    }

    public function print_delete_confirm_message()
    {
        return "Do you really want to delete user \"" . $this->fullname . "\" and all of its dependencies ?";
    }

    public function get_URL_cancel($user_consulted = null)
    {
        return "user/users";
    }

    public function get_URL_delete($user_consulted = null)
    {
        return "user/delete_user/" . $this->id;
    }

    public function get_non_mastered_skill() {
        $skills = array();
        $query = self::execute("select * from skill where id in (select skill from `using` u 
                            join experience e on e.id= u.experience and e.user=:user)
                            and id not in (select skill from mastering m where m.user=:user) 
                            order by Name", array("user" => $this->id));
        $data = $query->fetchAll();
        foreach($data as $row){
            $skills[] = new Skill($row["ID"], $row["Name"]);
        }
        return $skills;
    }
    public function delete_non_mastered_skill($skill_id){
        $query = self::execute("delete from `using` where experience in (select id from experience e where e.user=:user) and skill=:skill",
        array("user"=>$this->id, "skill"=>$skill_id));
    }

}