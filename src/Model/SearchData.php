<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{
    /** @var int */
    public $page = 1;

    /** @var string 
     * @var null */
    // #[Assert\Length(min: 2)]
    // #[Assert\Regex(
    //     pattern: '/^[a-zA-Z]*$/',
    //     match: true,
    //     message: 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.',
    // )]
    public $q = "";

    /** @var string 
     * @var null */
    // #[Assert\Length(min: 2)]
    // #[Assert\Regex(
    //     pattern: '/^[a-zA-Z]*$/',
    //     match: true,
    //     message: 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.',
    // )]
    public $local = "";

    /** @var array */
    public $motif = [];

    /** @var string */
    public $genre = "";
}

?>