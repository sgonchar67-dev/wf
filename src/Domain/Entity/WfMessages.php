<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * WfMessages
 */
#[Table(name: 'wf_messages', indexes: ['(name="in1", columns={"user_id"})', '(name="in3", columns={"user_status"})', '(name="in5", columns={"last_date"})', '(name="in7", columns={"is_support"})', '(name="in2", columns={"recipient_id"})', '(name="in4", columns={"recipient_status"})', '(name="in6", columns={"create_date"})'])]
#[Entity]
class WfMessages
{
    #[Column(name: 'id', type: 'integer', nullable: false)]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[Column(name: 'user_id', type: 'integer', nullable: false)]
    private int $userId;
    #[Column(name: 'recipient_id', type: 'integer', nullable: false)]
    private int $recipientId;
    #[Column(name: 'create_date', type: 'integer', nullable: false)]
    private int $createDate;
    #[Column(name: 'last_date', type: 'integer', nullable: false)]
    private int $lastDate;
    #[Column(name: 'user_status', type: 'integer', nullable: false)]
    private int $userStatus;
    #[Column(name: 'recipient_status', type: 'integer', nullable: false)]
    private int $recipientStatus;
    #[Column(name: 'last_message', type: 'text', length: 65535, nullable: false)]
    private string $lastMessage;
    #[Column(name: 'last_message_type', type: 'integer', nullable: false)]
    private int $lastMessageType;
    #[Column(name: 'last_user_id', type: 'integer', nullable: false)]
    private int $lastUserId;
    #[Column(name: 'last_message_id', type: 'integer', nullable: false)]
    private int $lastMessageId;
    #[Column(name: 'is_support', type: 'integer', nullable: false)]
    private int $isSupport;
    #[Column(name: 'user_count', type: 'integer', nullable: false)]
    private int $userCount;
    #[Column(name: 'recipient_count', type: 'integer', nullable: false)]
    private int $recipientCount;
}
