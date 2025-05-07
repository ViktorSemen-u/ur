<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // #[Route('/', name: 'app_home')]
    /**
     * Summary of index
     * @Route("/", name="app_home")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'user_name' => 'не зреєстрований'
        ]);
    }
}
