<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240319221050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `customer.version` column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer ADD version INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer DROP version');
    }
}
