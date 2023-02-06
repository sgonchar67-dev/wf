<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AddMultipleSqlTrait;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220507104302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable("order_data_log")) {
            $this->addSql("
                create table if not exists order_data_log (
                    id                 int auto_increment
                        primary key,
                    order_id           int  null,
                    order_event_log_id int  null,
                    delivery           json null,
                    payment            json null,
                    products           json null,
                    constraint UNIQ_2A4B6BC1DC3EBEBE
                        unique (order_event_log_id),
                    constraint FK_2A4B6BC18D9F6D38
                        foreign key (order_id) references `order` (id)
                            on delete cascade,
                    constraint FK_2A4B6BC1DC3EBEBE
                        foreign key (order_event_log_id) references order_event_log (id)
                            on delete cascade
                )
                    charset = utf8;
            ");
            $this->addSql("create index IDX_2A4B6BC18D9F6D38
                    on order_data_log (order_id);
            ");
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_data_log');
    }
}
