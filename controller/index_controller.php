<?php
include_once(dirname(__FILE__) . "/../config.php");

class index_controller {

    public function index_action() {
        return $this->game_action();
        $url = "http://localhost/ffa/?index/auth";
        $url = urlencode($url);
        $url = "http://114.215.82.75/comacc/?index&next=$url&title=betrayal";
        header("Location: $url");
    }

    public function auth_action() {
        $userid = get_request_assert("userid");
        $token = get_request_assert("token");
        $url = "http://114.215.82.75/comacc/ajax.php?action=login.authorize&userid=$userid&token=$token";
        $c = file_get_contents($url);
        $c = json_decode($c, true);
        dump_var($c);
    }

    public function game_action() {
        $tpl = new tpl("header", "footer");
        $tpl->display("game");
    }
}

