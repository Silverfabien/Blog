<?php

namespace App\ControllerHandler\Security;

use App\Entity\User\User;
use Symfony\Component\Form\FormInterface;

class UserControllerHandler
{
    public function userEdit(FormInterface $form, User $user): bool
    {
        if (!$form->isSubmitted() || !$form->isValid()) {

            return true;
        }
        return false;
    }
}
