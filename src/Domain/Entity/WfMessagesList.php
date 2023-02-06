<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * WfMessagesList
 */
#[Table(name: 'wf_messages_list', indexes: ['(name="in1", columns={"create_date"})', '(name="in3", columns={"recipient_status"})', '(name="in5", columns={"message_id"})', '(name="in7", columns={"is_bot"})', '(name="in2", columns={"email_send"})', '(name="in4", columns={"user_id"})', '(name="in6", columns={"user_status"})'])]
#[Entity]
class WfMessagesList
{
    #[Column(name: 'id', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'message_id', type: 'integer', nullable: false)]
    private int $messageId;
    #[Column(name: 'user_id', type: 'integer', nullable: false)]
    private int $userId;
    #[Column(name: 'create_date', type: 'integer', nullable: false)]
    private int $createDate;
    #[Column(name: 'user_status', type: 'integer', nullable: false, options: ['default' => '1'])]
    private int $userStatus = 1;
    #[Column(name: 'recipient_status', type: 'integer', nullable: false)]
    private int $recipientStatus;
    #[Column(name: 'message', type: 'text', length: 65535, nullable: false)]
    private string $message;
    #[Column(name: 'message_type', type: 'integer', nullable: false)]
    private int $messageType;
    #[Column(name: 'width', type: 'integer', nullable: false)]
    private int $width;
    #[Column(name: 'height', type: 'integer', nullable: false)]
    private int $height;
    #[Column(name: 'is_bot', type: 'integer', nullable: false)]
    private int $isBot;
    #[Column(name: 'email_send', type: 'integer', nullable: false)]
    private int $emailSend;
}
