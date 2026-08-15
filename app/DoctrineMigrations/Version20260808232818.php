<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 * 
 */
final class Version20260808232818 extends AbstractMigration
{

    const IINSERT1  = "INSERT INTO TableName (id, name, sort_no, discriminator_type) VALUES ";
    const IINSERT2  = "INSERT INTO TableName (id,display_order_count, name, sort_no, discriminator_type) VALUES ";
    const TRUNCATE  = "TRUNCATE TABLE TableName;";


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


    foreach ($this->mtbLists()  as $mtbName){

      $Sql .= str_replace('TableName',$mtbName,self::TRUNCATE);


        $Instert = str_replace('TableName',$mtbName,$mtbName === 'mtb_order_status'  ? self::IINSERT2:self::IINSERT1);

        foreach ($this->$mtbName() as $Value){



        $Sql .= $Instert . $Value;


        };


    }

    $Sql .= 'SET FOREIGN_KEY_CHECKS = 1;';
    return $Sql;







}

/**
 * @return array
 */
private function mtbLists(){

        $Mtbs = [

            #    'mtb_authority',
            #    'mtb_country',
                'mtb_csv_type',
                'mtb_customer_order_status',
            #    'mtb_customer_status',
            #    'mtb_device_type',
            #    'mtb_job',
            #    'mtb_login_history_status',
            #    'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
            #    'mtb_page_max',
            #    'mtb_pref',
            #    'mtb_product_list_max',
                'mtb_product_list_order_by',
            #    'mtb_product_status',
            #    'mtb_rounding_type',
            #    'mtb_sale_type',
            #    'mtb_sex',
            #    'mtb_tax_display_type',
            #    'mtb_tax_type',
            #    'mtb_work'
        ];





return $Mtbs;

}



private function mtb_csv_type(){

    $Value[] = "(1, '商品CSV', 3, 'csvtype');";
    $Value[] = "(2, '会員CSV', 4, 'csvtype');";
    $Value[] = "(3, '受注CSV', 1, 'csvtype');";
    $Value[] = "(4, '配送CSV', 1, 'csvtype;');";
    $Value[] = "(5, 'カテゴリCSV', 5, 'csvtype');";
    $Value[] = "(6, '規格CSV', 6, 'csvtype');";
    $Value[] = "(7, '規格分類CSV', 7, 'csvtype');";
    $Value[] = "(8, '商品レビューCSV', 8, 'csvtype');";

    return $Value;


}



private function mtb_customer_order_status(){

    $Value[] = "(1, '注文受付', 0, 'customerorderstatus');";
    $Value[] = "(2, '入金待ち', 8, 'customerorderstatus');";
    $Value[] = "(3, '注文取消し', 3, 'customerorderstatus');";
    $Value[] = "(4, '注文受付', 2, 'customerorderstatus');";
    $Value[] = "(5, '発送済み', 4, 'customerorderstatus');";
    $Value[] = "(6, '注文受付', 1, 'customerorderstatus');";
    $Value[] = "(7, '注文未完了', 6, 'customerorderstatus');";
    $Value[] = "(8, '注文未完了', 5, 'customerorderstatus');";
    $Value[] = "(9, '入金確認中', 7, 'customerorderstatus');";

return $Value;

}



private function mtb_order_status(){

    $Value[] = "(1, 1,'新規受付', 0, 'orderstatus');";
    $Value[] = "(2, 0,'入金待ち', 8, 'orderstatus');";
    $Value[] = "(3, 0,'注文取消し', 3, 'orderstatus');";
    $Value[] = "(4, 1,'取り寄せ中', 2, 'orderstatus');";
    $Value[] = "(5, 0,'発送済み', 4, 'orderstatus');";
    $Value[] = "(6, 1,'入金済み', 1, 'orderstatus');";
    $Value[] = "(7, 0,'決済処理中', 6, 'orderstatus');";
    $Value[] = "(8, 0,'購入処理中', 5, 'orderstatus');";
    $Value[] = "(9, 0,'受注未確定', 7, 'orderstatus');";

return $Value;
}
private function mtb_order_status_color(){

    $Value[] = "(1, '#437ec4', 0, 'orderstatuscolor');";
    $Value[] = "(2, '#FFDE9B', 8, 'orderstatuscolor');";
    $Value[] = "(3, '#C04949', 3, 'orderstatuscolor');";
    $Value[] = "(4, '#EEB128', 2, 'orderstatuscolor');";
    $Value[] = "(5, '#25B877', 4, 'orderstatuscolor');";
    $Value[] = "(6, '#25B877', 1, 'orderstatuscolor');";
    $Value[] = "(7, '#A3A3A3', 6, 'orderstatuscolor');";
    $Value[] = "(8, '#A3A3A3', 5, 'orderstatuscolor');";
    $Value[] = "(9, '#C04949', 7, 'orderstatuscolor');";

return $Value;
}

private function mtb_product_list_order_by(){

    $Value[] = "(1, '価格が低い順', 0, 'productlistorderby');";
    $Value[] = "(2, '新着順', 1, 'productlistorderby');";
    $Value[] = "(3, 'オススメ順', 2, 'productlistorderby');";
    $Value[] = "(4, 'レビュー順', 3, 'productlistorderby');";
return $Value;
}
    
}