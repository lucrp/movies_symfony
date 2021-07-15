<?php

namespace App\Service;

use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class MovieImporter
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }


    public function parseXml($xml)
    {
        $movies = $moviesRep->findAll();
    }

}