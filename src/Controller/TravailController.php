<?php

namespace App\Controller;

use App\Entity\ServiceRequest;
use App\Form\ServiceRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TravailController extends AbstractController
{
    #[Route('/travail', name: 'travail', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $serviceRequest = new ServiceRequest();
        $serviceRequest->setType('travail');
        
        $form = $this->createForm(ServiceRequestType::class, $serviceRequest);
        $form->handleRequest($request);

        $success = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($serviceRequest);
            $em->flush();

            try {
                $email = (new Email())
                    ->from('noreply@canadaim.com')
                    ->to('info@canadaim.com')
                    ->subject('Nouvelle demande: Travail - ' . $serviceRequest->getName())
                    ->html($this->renderView('email/service_request_notification.html.twig', [
                        'request' => $serviceRequest,
                        'service_name' => 'Travail'
                    ]));

                $mailer->send($email);
            } catch (\Exception $e) {
                // Silent fail
            }

            $success = true;
            $this->addFlash('success', 'Votre demande a été enregistrée. Nous vous contacterons bientôt!');
        }

        return $this->render('travail/index.html.twig', [
            'form' => $form->createView(),
            'success' => $success,
        ]);
    }
}
