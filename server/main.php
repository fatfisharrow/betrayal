<?php

include_once(dirname(__FILE__) . "/config.php");
include_once(dirname(__FILE__) . "/server.php");



function main() {
    logging::set_file_prefix("server-");
    logging::set_logging_dir(dirname(__FILE__) . "/logs");
    $s = new Server();
    $s->Start(19504);
}


main();

