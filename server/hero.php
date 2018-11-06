<?php

class Hero {
    public $identify;
    public $name;
    public $class;
    public $speed;
    public $might;
    public $sanity;
    public $knowledge;

    public function __construct() {
    }


    public static function createHeroes() {
        $heroes = array();

        $path = "./data/hero/";
        $dir = opendir($path);
        if ($dir) {
            while (($file = readdir($dir)) !== false) {
                $c = file_get_contents($path . $file);
                $c = json_decode($c, true);

                $h = new Hero();
                $h->identify = basename($file, ".json");
                $h->name = $c["name"];
                $h->class = $c["class"];
                $h->speed = $c["prop"]["speed"];
                $h->might = $c["prop"]["might"];
                $h->sanity = $c["prop"]["sanity"];
                $h->knowledge = $c["prop"]["knowledge"];
                $heroes []= $h;
            }
            closedir($dir);
        }
        return $heroes;
    }
};



