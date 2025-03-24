<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250323103107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create phone_verification_code table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE phone_verification_code
            (
                id SERIAL PRIMARY KEY,
                phone_number VARCHAR(50) NOT NULL,
                code VARCHAR(10) NOT NULL,
                attempts SMALLINT NOT NULL DEFAULT 0,
                is_used BOOLEAN NOT NULL DEFAULT FALSE,
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS phone_verification_code');
    }
}
