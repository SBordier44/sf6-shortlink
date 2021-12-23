<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    #[Route('/', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, LinkRepository $linkRepository): Response
    {
        $link = new Link();

        $form = $this->createForm(LinkType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link->setShort(uniqid('', false));

            $em->persist($link);
            $em->flush();

            $this->addFlash('success', $link);

            return $this->redirectToRoute('add');
        }

        return $this->render('link/add.html.twig', [
            'form' => $form->createView(),
            'links' => count($linkRepository->findAll())
        ]);
    }

    #[Route('/{short}', name: 'link')]
    public function index(Link $link): RedirectResponse
    {
        return $this->redirect($link->getLink());
    }
}
