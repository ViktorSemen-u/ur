<?php

namespace App\Controller;

use App\Entity\Home;
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
        // $home = new Home();
        // $home->setTitle("Привіт")
        //     ->setContent("Текст привітання");

        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($home);
        // $entityManager->flush();

        // $home = $this->getDoctrine()->getRepository(Home::class)->findOneBy(array("id"=> 1));
        $home = $this->getDoctrine()->getRepository(Home::class)->find(1);
        // dd($home);
        // dump($home).die;
        // dump($home);
        // var_dump($home);
        return $this->render('home/index.html.twig', [
            'user_name' => 'не зреєстрований',
            'home'=> $home,
        ]);
    }
}
