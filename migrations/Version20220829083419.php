<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220829083419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL,
                            short_url VARCHAR(255) DEFAULT NULL,
                            full_url VARCHAR(255) DEFAULT NULL, visual LONGTEXT DEFAULT NULL,
                            uuid VARCHAR(255) NOT NULL, UNIQUE INDEX uniq_5399b645d17f50a6 (uuid),
                            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }
}
