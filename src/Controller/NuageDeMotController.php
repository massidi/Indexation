<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NuageDeMotController extends AbstractController
{
    #[Route('/nuage/mot', name: 'app_nuage_de_mot',methods: ['GET'])]
    public function index(Request $request): Response
    {
        $documentId = $request->request->get('id');

//        dd($documentId);

        return $this->render('nuage_de_mot/index.html.twig', [
            'controller_name' => 'NuageDeMotController',
        ]);
    }
}
