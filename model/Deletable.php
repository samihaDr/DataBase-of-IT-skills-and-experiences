<?php

interface Deletable{

    public function print_delete_confirm_message();
    public function get_URL_cancel($user_consulted=null);
    public function get_URL_delete($user_consulted=null);
}

?>