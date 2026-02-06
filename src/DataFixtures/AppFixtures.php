<?php

namespace App\DataFixtures;

use App\Entity\ContactMessage;
use App\Entity\ServiceRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des messages de contact d'exemple
        $contactMessages = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'phone' => '+33 6 12 34 56 78',
                'subject' => 'immigration',
                'message' => 'Bonjour, je suis intéressé par l\'immigration au Canada. Pouvez-vous me fournir plus d\'informations sur les programmes disponibles?'
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'phone' => '+33 7 98 76 54 32',
                'subject' => 'travail',
                'message' => 'Je cherche des opportunités d\'emploi au Canada. Quels sont les secteurs en demande actuellement?'
            ],
            [
                'name' => 'Pierre Bernard',
                'email' => 'pierre.bernard@example.com',
                'phone' => '+33 6 55 44 33 22',
                'subject' => 'etude',
                'message' => 'Je suis étudiant et j\'aimerais poursuivre mes études au Canada. Avez-vous des informations sur les bourses disponibles?'
            ],
        ];

        foreach ($contactMessages as $data) {
            $message = new ContactMessage();
            $message->setName($data['name']);
            $message->setEmail($data['email']);
            $message->setPhone($data['phone']);
            $message->setSubject($data['subject']);
            $message->setMessage($data['message']);
            $message->setStatus('new');
            $manager->persist($message);
        }

        // Créer des demandes de service d'exemple
        $serviceRequests = [
            [
                'type' => 'immigration',
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+216 98 123 456',
                'country' => 'Tunisie',
                'details' => 'Je suis ingénieur informatique avec 5 ans d\'expérience et je souhaite immigrer au Canada.'
            ],
            [
                'type' => 'travail',
                'name' => 'Sofia Rodriguez',
                'email' => 'sofia.rodriguez@example.com',
                'phone' => '+34 612 345 678',
                'country' => 'Espagne',
                'details' => 'Je suis infirmière et je souhaite travailler au Canada.'
            ],
            [
                'type' => 'etude',
                'name' => 'Carlos Silva',
                'email' => 'carlos.silva@example.com',
                'phone' => '+55 11 98765 4321',
                'country' => 'Brésil',
                'details' => 'Je suis lycéen et je veux étudier l\'informatique au Canada.'
            ],
            [
                'type' => 'sponsor',
                'name' => 'Yuki Tanaka',
                'email' => 'yuki.tanaka@example.com',
                'phone' => '+81 90 1234 5678',
                'country' => 'Japon',
                'details' => 'Je suis citoyen canadien et je souhaite parrainer ma mère.'
            ],
            [
                'type' => 'immigration',
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@example.com',
                'phone' => '+91 9876 543 210',
                'country' => 'Inde',
                'details' => 'Je suis architecte et j\'aimerais explorer les opportunités d\'immigration au Canada.'
            ],
        ];

        foreach ($serviceRequests as $data) {
            $request = new ServiceRequest();
            $request->setType($data['type']);
            $request->setName($data['name']);
            $request->setEmail($data['email']);
            $request->setPhone($data['phone']);
            $request->setCountry($data['country']);
            $request->setDetails($data['details']);
            $request->setStatus('new');
            $manager->persist($request);
        }

        $manager->flush();
    }
}
