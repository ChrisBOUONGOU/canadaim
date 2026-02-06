<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\ServiceRequest;
use App\Repository\ContactMessageRepository;
use App\Repository\ServiceRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function dashboard(
        ContactMessageRepository $contactRepo,
        ServiceRequestRepository $serviceRepo
    ): Response {
        $contactMessages = $contactRepo->findBy([], ['createdAt' => 'DESC'], 10);
        $serviceRequests = $serviceRepo->findBy([], ['createdAt' => 'DESC'], 10);
        
        $totalContacts = $contactRepo->count([]);
        $totalRequests = $serviceRepo->count([]);
        
        return $this->render('admin/dashboard.html.twig', [
            'contact_messages' => $contactMessages,
            'service_requests' => $serviceRequests,
            'total_contacts' => $totalContacts,
            'total_requests' => $totalRequests,
        ]);
    }

    #[Route('/messages', name: 'messages')]
    public function messages(ContactMessageRepository $repo): Response
    {
        $messages = $repo->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('admin/messages.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/requests', name: 'requests')]
    public function requests(ServiceRequestRepository $repo): Response
    {
        $requests = $repo->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('admin/requests.html.twig', [
            'requests' => $requests,
        ]);
    }

    #[Route('/message/{id}', name: 'message_detail', requirements: ['id' => '\d+'])]
    public function messageDetail(ContactMessage $message): Response
    {
        return $this->render('admin/message_detail.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/request/{id}', name: 'request_detail', requirements: ['id' => '\d+'])]
    public function requestDetail(ServiceRequest $request): Response
    {
        return $this->render('admin/request_detail.html.twig', [
            'request' => $request,
        ]);
    }
}
