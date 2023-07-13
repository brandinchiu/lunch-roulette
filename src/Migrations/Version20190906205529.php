<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190906205529 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add a list of tags to use for lunch options.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
            INSERT INTO tag(`name`) VALUES 
            ('healthy'),
            ('vegetarian'),
            ('vegan'),
            ('burgers'),
            ('sandwiches'),
            ('pizza'),
            ('asian'),
            ('italian'),
            ('american'),
            ('middle-eastern'),
            ('thai'),
            ('chinese'),
            ('japanese'),
            ('korean'),
            ('indian'),
            ('mexican'),
            ('greek'),
            ('fusion'),
            ('eat-in'),
            ('nearby'),
            ('favourite')
            ;
        ");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("
            DELETE FROM tag;
        ");
    }
}
