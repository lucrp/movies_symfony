<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MovieImporter
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function parseXml($xml)
    {
        $file = $this->getParameter('kernel.project_dir') . 'public/assets/movies.xml';
        
        $movies
    }

}