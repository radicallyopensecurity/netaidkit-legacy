<?php

class NotFoundException extends Exception
{
    public function do_404()
    {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        die('404 not found');
    }
}
