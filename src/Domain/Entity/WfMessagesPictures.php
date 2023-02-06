<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Product\Product;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;


/**
 * WfMessagesPictures
 */
#[Table(name: 'wf_messages_pictures')]
#[Entity]
class WfMessagesPictures
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
    #[Column(name: 'picture_url', type: 'string', length: 512, nullable: false)]
    private string $pictureUrl;
    #[Column(name: 'picture_width', type: 'integer', nullable: false)]
    private int $pictureWidth;
    #[Column(name: 'picture_height', type: 'integer', nullable: false)]
    private int $pictureHeight;
    #[Column(name: 'file_name', type: 'string', length: 1024, nullable: false)]
    private string $fileName;
}
