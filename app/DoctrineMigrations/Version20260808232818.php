<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class 8 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }


private function mtbList(){

        $Mtbs = [

            //    'mtb_authority',
            //    'mtb_country',
                'mtb_doui_hope',
                'mtb_csv_type',
                'mtb_customer_order_status',

                'mtb_customer_status',
                'mtb_device_type',
                'mtb_job',
                'mtb_login_history_status',
                'mtb_order_item_type',
                'mtb_order_status',
                'mtb_order_status_color',
                'mtb_page_max',
                'mtb_pref',
                'mtb_product_list_max',
                'mtb_product_list_order_by',
                'mtb_product_status',
                'mtb_rounding_type',
                'mtb_sale_type',
                'mtb_sex',
                'mtb_tax_display_type',
                'mtb_tax_type',
                'mtb_work'
        ];


$Inster = "INSERT INTO TableName (id, name, sort_no, discriminator_type) VALUES "; 


}



private function mtb_csv_type(){


    $Value[] = "(1, '商品CSV', 3, 'csvtype')";
    $Value[] = "(2, '会員CSV', 4, 'csvtype')";
    $Value[] = "(3, '受注CSV', 1, 'csvtype')";
    $Value[] = "(4, '配送CSV', 1, 'csvtype')";
    $Value[] = "(5, 'カテゴリCSV', 5, 'csvtype')";
    $Value[] = "(6, '規格CSV', 6, 'csvtype')";
    $Value[] = "(7, '規格分類CSV', 7, 'csvtype')";
    $Value[] = "(8, '商品レビューCSV', 8, 'csvtype')";

return $Value;



}

private function mtb_order_status(){

    $Value[] = "1, '新規受付', 0, 'orderstatus')";
    $Value[] = "3, '注文取消し', 3, 'orderstatus')";
    $Value[] = "4, '対応中', 2, 'orderstatus')";
    $Value[] = "5, '発送済み', 4, 'orderstatus')";
    $Value[] = "6, '入金済み', 1, 'orderstatus')";
    $Value[] = "7, '決済処理中', 6, 'orderstatus')";
    $Value[] = "8, '購入処理中', 5, 'orderstatus')";
    $Value[] = "9, '返品', 7, 'orderstatus')";

return $Value;
}


    }
