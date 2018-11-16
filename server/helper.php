<?php

function extname($file) {
    // $arr = pathinfo($file);
    // return $arr['extension'];
    return pathinfo($file, PATHINFO_EXTENSION);
}

