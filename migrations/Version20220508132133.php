<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508132133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_event_log CHANGE event event VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_event_log CHANGE event event ENUM(\'EVENT_SEND\', \'EVENT_EDIT\', \'EVENT_REFUSE\', \'EVENT_CONFIRM\', \'EVENT_COMPLETE\', \'EVENT_CANCEL\', \'EVENT_SEEN\', \'EVENT_NOTIFICATION\', \'EVENT_SHIPMENT\', \'EVENT_BILLING\', \'EVENT_PAYMENT\', \'EVENT_REFUND\') NOT NULL COMMENT \'(DC2Type:EnumOrderEventsType)\'');
    }
}
