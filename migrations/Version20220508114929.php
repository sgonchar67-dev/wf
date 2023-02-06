<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508114929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD placed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE rbv_measure_id rbv_measure_id INT UNSIGNED NOT NULL, CHANGE rbv_weight_measure_id rbv_weight_measure_id INT UNSIGNED NOT NULL, CHANGE rbv_volume_measure_id rbv_volume_measure_id INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP placed_at');
        $this->addSql('ALTER TABLE product CHANGE rbv_measure_id rbv_measure_id INT UNSIGNED DEFAULT 19 NOT NULL, CHANGE rbv_weight_measure_id rbv_weight_measure_id INT UNSIGNED DEFAULT 14 NOT NULL, CHANGE rbv_volume_measure_id rbv_volume_measure_id INT UNSIGNED DEFAULT 9 NOT NULL');
    }
}
