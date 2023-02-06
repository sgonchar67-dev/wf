<?php

namespace App\Service\Notification;

use App\Domain\Entity\Contractor\ContractorInviteToken;
use App\Domain\Entity\User\EmailConfirmationCode;
use App\Domain\Entity\User\PhoneConfirmationCode;
use App\Domain\Entity\User\User;
use RuntimeException;

class NotificationService
{
    public function sendPhoneConfirmationCode(PhoneConfirmationCode $phoneConfirmationCode): void
    {
        $url = 'https://events.sendpulse.com/events/id/32dc678e10f0465ed89982bf84cc80b9/7332584';
        $data = [
            'text' => $phoneConfirmationCode->getCode(),
            'phone' => $phoneConfirmationCode->getPhone(),
        ];

        $this->send($url, $data);
    }

    public function sendEmailConfirmationCode(EmailConfirmationCode $emailConfirmationCode): void
    {
        $url = 'https://events.sendpulse.com/events/id/64211bd0355e41a58d9fa251e22afe5f/7332584';
        $data = [
            'email' => $emailConfirmationCode->getEmail(),
            'phone' => $emailConfirmationCode->getUser()->getPhone(),
            "email_confirmation_code" => $emailConfirmationCode->getCode(),
        ];

        $this->send($url, $data);
    }

    public function sendPassword(User $user)
    {
        if (!$plainPassword = $user->getPlainPassword()) {
            throw new RuntimeException(
                "Error on send password after register user {$user->getId()}. Plain password is empty"
            );
        }

        $url = 'https://events.sendpulse.com/events/id/50cdc202176f42f2f2e1543b807403f5/7332584';
        $data = [
            'text' => $plainPassword,
            'phone' => $user->getPhone(),
        ];

        $this->send($url, $data);
    }

    public function sendContractorInviteToken(ContractorInviteToken $token): void
    {
        $url = 'https://events.sendpulse.com/events/id/be8c2ad8762f40aeddcd6119f8251c95/7332584';
        $data = [
            'email' => $token->getEmail(),
            'phone' => $token->getContractor()->getCompany()->getPhone(),
            'seller_company_name' => $token->getContractor()->getName(),
            "contractor_invite_token_link" => $token->getInviteLink(),
        ];
        $this->send($url, $data);
    }

    public function send(string $url, array $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));

        curl_exec($ch);
        curl_close($ch);
    }
}