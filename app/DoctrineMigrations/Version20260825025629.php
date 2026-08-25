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


$Value[] = "(1,'セット(面、小手、胴、垂)',0,'ProductType');";
$Value[] = "(2,'面',1,'ProductType');";
$Value[] = "(3,'小手',2,'ProductType');";
$Value[] = "(4,'胴',3,'ProductType');";
$Value[] = "(5,'垂',4,'ProductType');";
$Value[] = "(6,'胴着',5,'ProductType');";
$Value[] = "(7,'袴',6,'ProductType');";
$Value[] = "(8,'竹刀',7,'ProductType');";
$Value[] = "(9,'竹刀袋',8,'ProductType');";
$Value[] = "(10,'防具袋',9,'ProductType');";
$Value[] = "(11,'ゼッケン',10,'ProductType');";
$Value[] = "(12,'その他',11,'ProductType');";



    return $Value;


}
}
