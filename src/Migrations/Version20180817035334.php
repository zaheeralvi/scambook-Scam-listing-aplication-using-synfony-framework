<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180817035334 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema = new Schema();

        $table = $schema->createTable('summary_invoices');
        $table->addColumn('id', 'integer', array(
            'autoincrement' => true,
        ));
        $table->setPrimaryKey(array('id'));

        return $schema;
//        $this->addSql('ALTER TABLE users ADD test VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
