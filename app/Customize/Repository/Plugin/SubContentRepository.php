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
use Plugin\SubContent43\Entity\Config;
use Doctrine\Persistence\ManagerRegistry;
use Plugin\SubContent43\Service\ScService;
use Doctrine\ORM\NoResultException;
use Customize\Repository\QueryKey;
use Doctrine\ORM\EntityManager;

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
        $Config = $this->getEntityManager()->getRepository(Config::class)->find(1);
        $qb = $this->createQueryBuilder('T')
            ->where('T.visible=:Visible')
            ->setParameter('Visible',true)
            ->andWhere('T.Config = :Config')
            ->setParameter('Config' , $Config)
            ->OrderBy('T.text03', 'DESC')
            ->addOrderBy('T.create_date','DESC');
        
        

        if($Holder){
            if ('mix' == $Holder){
                $qb->andWhere($qb->expr()->in('T.holder', ':Holders'))
                    ->setParameter('Holders',['keiko','kendo-bogu']);
            }else{
                $qb->andWhere('T.holder = :Holder')
                ->setParameter('Holder',$Holder);
            }
        }    


        return $this->queries->customize(QueryKey::KENJOY_LIST, $qb ,$Pagination);

    }

    public function getkenjoyNew(int $limit = 6, bool $flg = false)
    {

        $Config = $this->getEntityManager()->getRepository(Config::class)->find(1);
        

        $qb = $this->createQueryBuilder('T')
            ->where('T.visible=:Visible')
            ->setParameter('Visible',true)
            ->andWhere('T.Config = :Config')
            ->setParameter('Config' , $Config)
            ->setMaxResults($limit)
            ->OrderBy('T.text03', 'DESC')
            ->addOrderBy('T.create_date','DESC');


        if($flg){
            $qb->andWhere($qb->expr()->in('T.holder', ':Holders'))
               ->setParameter('Holders',['keiko','kendo-bogu']);
        }    

        return $qb->getQuery()->getResult(); 

    }

    }                                   

