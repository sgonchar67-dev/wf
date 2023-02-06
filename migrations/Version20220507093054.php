<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220507093054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            create table if not exists refresh_tokens
            (
                id            int auto_increment
                    primary key,
                refresh_token varchar(128) not null,
                username      varchar(255) not null,
                valid         datetime     not null,
                constraint UNIQ_9BACE7E1C74F2195
                    unique (refresh_token)
            )
                collate = utf8mb4_unicode_ci
                auto_increment = 2;
        ");
    }
}
