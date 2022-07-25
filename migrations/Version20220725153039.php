<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220725153039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gender_user (gender_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6C5697D3708A0E0 (gender_id), INDEX IDX_6C5697D3A76ED395 (user_id), PRIMARY KEY(gender_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gender_user ADD CONSTRAINT FK_6C5697D3708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gender_user ADD CONSTRAINT FK_6C5697D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gender DROP FOREIGN KEY FK_C7470A42A76ED395');
        $this->addSql('DROP INDEX IDX_C7470A42A76ED395 ON gender');
        $this->addSql('ALTER TABLE gender DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE gender_user');
        $this->addSql('ALTER TABLE gender ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE gender ADD CONSTRAINT FK_C7470A42A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C7470A42A76ED395 ON gender (user_id)');
    }
}
