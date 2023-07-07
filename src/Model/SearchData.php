<?php

namespace App\Model;

class SearchData
{
    /** @var int */
    public $page = 1;

    /** @var string 
     * @var null */
    public $q = "";

    /** @var string 
     * @var null */
    public $local = "";

    /** @var array */
    public $motif = [];

    /** @var string */
    public $genre = "";
}

?>