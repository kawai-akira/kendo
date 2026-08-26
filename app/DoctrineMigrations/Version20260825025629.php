<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260825025629 extends AbstractMigration
{

    const INSERT  = "INSERT INTO mtb_product_type (id, name, sort_no, discriminator_type) VALUES ";
    const TRUNCATE  = "TRUNCATE TABLE mtb_product_type;";
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $Sql = $this->MakeSql();
        $this->addSql($Sql);

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }


protected  function MakeSql(){

    $Sql = 'SET FOREIGN_KEY_CHECKS = 0;';

   

    $Sql .= self::TRUNCATE;


        $Instert = self::INSERT;

        foreach ($this->mtb_csv_type() as $Value){
             $Sql .= $Instert . $Value;

        };

   
    $Sql .= 'SET FOREIGN_KEY_CHECKS = 1;';
    return $Sql;

}


    
    private function mtb_csv_type(){


$Value[] = "(1,'セット(面、小手、胴、垂)',0,'producttype');";
$Value[] = "(2,'面',1,'producttype');";
$Value[] = "(3,'小手',2,'producttype');";
$Value[] = "(4,'胴',3,'producttype');";
$Value[] = "(5,'垂',4,'producttype');";
$Value[] = "(6,'胴着',5,'producttype');";
$Value[] = "(7,'袴',6,'producttype');";
$Value[] = "(8,'竹刀',7,'producttype');";
$Value[] = "(9,'竹刀袋',8,'producttype');";
$Value[] = "(10,'防具袋',9,'producttype');";
$Value[] = "(11,'ゼッケン',10,'producttype');";
$Value[] = "(12,'その他',11,'producttype');";



    return $Value;


}
}
