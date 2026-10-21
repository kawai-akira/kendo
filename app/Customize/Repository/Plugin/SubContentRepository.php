<?php
/**
 * @version EC=CUBE4.2
 * @copyright 株式会社 翔 kakeru.co.jp
 * @author
 * 2026年08月27日作成
 *
 * app\Customize\Repository\Plugin\RecommendProductRepository.php
 *
 * 
 *
 *
 *
 *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
 ******************************************************/

namespace Customize\Repository\Plugin;

use Eccube\Doctrine\Query\Queries;
//use Plugin\SubContent43\Entity\SubContent;
//use Plugin\SubContent43\Entity\Config;
use Doctrine\Persistence\ManagerRegistry;
use Plugin\SubContent43\Service\ScService;
use Doctrine\ORM\NoResultException;
use Customize\Repository\QueryKey;

class SubContentRepository extends \Plugin\SubContent43\Repository\SubContentRepository
{

    /**
     * @var Queries
     */
    protected $queries;


    /**
     *  SubContentRepository constructor.
     *
     * @param ManagerRegistry $registry
     * 
     */
    public function __construct(ManagerRegistry $registry,ScService $ScService,Queries $queries)
    {

        parent::__construct($registry,$ScService);
        $this->queries = $queries;

    }


    public function getKenJoylist(?string $Holder,?array $Pagination)
    {

        $qb = $this->createQueryBuilder('T')
            ->where('T.visible=:Visible')
            ->setParameter('Visible',true)
            ->OrderBy('T.text03', 'DESC')
            ->addOrderBy('T.create_date','DESC');
        if($Holder){
            $qb->andWhere('T.holder = :Holder')
            ->setParameter('Holder',$Holder);
        }    


        return $this->queries->customize(QueryKey::KENJOY_LIST, $qb ,$Pagination);

    }

    }                        

