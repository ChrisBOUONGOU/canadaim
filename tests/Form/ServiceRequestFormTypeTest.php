<?php

namespace App\Tests\Form;

use App\Entity\ServiceRequest;
use App\Form\ServiceRequestType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Service Request Form Type Tests
 * Tests form configuration and basic behavior
 */
class ServiceRequestFormTypeTest extends KernelTestCase
{
    public function testFormCreation(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);

        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('phone'));
        $this->assertTrue($form->has('country'));
        $this->assertTrue($form->has('details'));
    }

    public function testFormDataClass(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        
        $this->assertEquals(ServiceRequest::class, $form->getConfig()->getDataClass());
    }

    public function testNameFieldConfiguration(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $nameField = $form->get('name');

        $this->assertEquals('text', $nameField->getConfig()->getType()->getBlockPrefix());
        $this->assertEquals('form-control', $nameField->getConfig()->getOption('attr')['class']);
    }

    public function testEmailFieldConfiguration(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $emailField = $form->get('email');

        $this->assertEquals('email', $emailField->getConfig()->getType()->getBlockPrefix());
    }

    public function testCountryFieldIsOptional(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $countryField = $form->get('country');

        $this->assertFalse($countryField->getConfig()->getRequired());
    }

    public function testDetailsFieldIsOptional(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $detailsField = $form->get('details');

        $this->assertEquals('textarea', $detailsField->getConfig()->getType()->getBlockPrefix());
        $this->assertFalse($detailsField->getConfig()->getRequired());
    }

    public function testPhoneFieldIsOptional(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $phoneField = $form->get('phone');

        $this->assertEquals('tel', $phoneField->getConfig()->getType()->getBlockPrefix());
        $this->assertFalse($phoneField->getConfig()->getRequired());
    }

    public function testNameFieldRequired(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $nameField = $form->get('name');

        $this->assertTrue($nameField->getConfig()->getRequired());
    }

    public function testEmailFieldRequired(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get('form.factory');
        $form = $formFactory->create(ServiceRequestType::class);
        $emailField = $form->get('email');

        $this->assertTrue($emailField->getConfig()->getRequired());
    }
}
