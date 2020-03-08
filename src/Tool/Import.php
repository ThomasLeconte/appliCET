<?php

namespace App\Tool;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class Import{

    /**
     * @Assert\File(
     *     maxSize = "2048k",
     * )
     */    
    private $file;


    /**
     * Get the value of file
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */ 
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }
}


?>