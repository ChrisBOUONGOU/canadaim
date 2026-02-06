<?php

namespace App\Tests\Form;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Contact Message Form Type Tests
 * Tests form configuration and basic behavior
 */
class ContactMessageFormTypeTest extends KernelTestCase
{
    public function testFormCreation(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);

        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('phone'));
        $this->assertTrue($form->has('subject'));
        $this->assertTrue($form->has('message'));
    }

    public function testFormDataClass(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        
        $this->assertEquals(ContactMessage::class, $form->getConfig()->getDataClass());
    }

    public function testNameFieldConfiguration(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        $nameField = $form->get('name');

        $this->assertEquals('text', $nameField->getConfig()->getType()->getBlockPrefix());
        $this->assertEquals('form-control', $nameField->getConfig()->getOption('attr')['class']);
    }

    public function testEmailFieldConfiguration(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        $emailField = $form->get('email');

        $this->assertEquals('email', $emailField->getConfig()->getType()->getBlockPrefix());
    }

    public function testSubjectChoices(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        $subjectField = $form->get('subject');

        $choices = $subjectField->getConfig()->getOption('choices');
        $this->assertNotEmpty($choices);
        $this->assertContains('immigration', $choices);
        $this->assertContains('travail', $choices);
    }

    public function testMessageFieldConfiguration(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        $messageField = $form->get('message');

        $this->assertEquals('textarea', $messageField->getConfig()->getType()->getBlockPrefix());
    }

    public function testPhoneIsOptional(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ContactMessageType::class);
        $phoneField = $form->get('phone');

        $this->assertFalse($phoneField->getConfig()->getRequired());
    }
}
