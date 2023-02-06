<?php

namespace App\Domain\Entity\Moysklad\Contractor;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;


/**
 * @todo rename to MoyskladContractorContact
 * MystoreContractorsContacts
 */
#[Table(name: 'mystore_contractors_contacts', indexes: ['(name="mystore_contact_id", columns={"mystore_contact_id"})'])]
#[Entity]
class MystoreContractorsContacts
{
    #[Column(name: 'contact_id', type: 'integer', nullable: false, options: ['unsigned' => true, 'comment' => 'contact_id из таблицы contractors_contacts'])]
    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    private int $contactId;
    #[Column(name: 'mystore_contact_id', type: 'string', length: 200, nullable: true, options: ['comment' => 'Идентификатор контакта в моём складе'])]
    private ?string $mystoreContactId = null;
}
