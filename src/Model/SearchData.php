<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

class SearchData
{
    /** @var int */
    public $page = 1;

    /** @var string 
     * @var null */
    #[Assert\Length(min: 3)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your name cannot contain a number',
    )]
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