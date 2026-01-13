<?php

namespace App\ControllerHandler;

use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;
use Symfony\Component\Form\FormInterface;

readonly class DefaultControllerHandler
{
    public function __construct(private ContactRepository $contactRepository) {}

    public function contact(FormInterface $form, Contact $contact): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactRepository->create($contact);

            return true;
        }

        return false;
    }
}
