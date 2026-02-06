<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $message = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $message);
        $form->handleRequest($request);

        $success = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();

            // Send email notification
            try {
                $email = (new Email())
                    ->from('noreply@canadaim.com')
                    ->to('info@canadaim.com')
                    ->subject('Nouveau message de contact: ' . $message->getSubject())
                    ->html($this->renderView('email/contact_notification.html.twig', [
                        'message' => $message
                    ]));

                $mailer->send($email);

                // Send confirmation email to user
                $confirmationEmail = (new Email())
                    ->from('noreply@canadaim.com')
                    ->to($message->getEmail())
                    ->subject('Confirmation: Nous avons reçu votre message')
                    ->html($this->renderView('email/contact_confirmation.html.twig', [
                        'message' => $message
                    ]));

                $mailer->send($confirmationEmail);
            } catch (\Exception $e) {
                // Log error but don't fail the form submission
                $this->addFlash('warning', 'Votre message a été enregistré. Il y a eu un problème avec l\'envoi du mail.');
            }

            $success = true;
            $this->addFlash('success', 'Merci! Votre message a été envoyé avec succès. Nous vous répondrons dès que possible.');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'success' => $success,
        ]);
    }
}
