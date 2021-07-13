<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    #[Route('/movies', name: 'movies')]
    public function index(MovieRepository $moviesRep): Response
    {
        return $this->render('movies/index.html.twig', [
            'controller_name' => 'MoviesController',
            'movies' => $moviesRep->findAll(),
        ]);
    }

    #[Route('/movies/$id', name: 'movie')]
    public function getMovie($id)
    {
        
    }

}
