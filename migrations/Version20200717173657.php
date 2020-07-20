<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200717173657 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creation of table_reservation';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS table_reservation 
                (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, start_time DATETIME NOT NULL, 
                end_time DATETIME NOT NULL, table_id INT NOT NULL, PRIMARY KEY(id),
                INDEX(date), INDEX(start_time), INDEX(end_time), INDEX(table_id)) 
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE table_booking');
    }
}
