<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegSuccessController extends AbstractController
{
    #[Route('/reg-success', name: 'app_reg_success')]
    public function index(): Response
    {
        return $this->render('reg_success/index.html.twig', [
            'user_name' =>  is_null($this->getUser()) ? 'не зареєстрований' : $this->getUser()->getUsername(),
        ]);
    }
}
