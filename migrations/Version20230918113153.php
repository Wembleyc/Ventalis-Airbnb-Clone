<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918113153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD user_reservation_id INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDED3B748BE FOREIGN KEY (user_reservation_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDED3B748BE ON booking (user_reservation_id)');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDED3B748BE');
        $this->addSql('DROP INDEX IDX_E00CEDDED3B748BE ON booking');
        $this->addSql('ALTER TABLE booking DROP user_reservation_id');

    }
}
