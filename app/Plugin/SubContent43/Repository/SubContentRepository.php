<?php
    /**
     * @version EC-CUBE4.3
     * @copyright 株式会社 翔 kakeru.co.jp
     * @author
     * 2026年05月10日作成
     *
     * app\Plugin\SubContent43\Repository\SubContentRepository.php
     *
     * サブコンテンツリポジトリー
     *
     *
     *
     *                             C= C= C= ┌(;･_･)┘ﾄｺﾄｺ
     ******************************************************/
namespace Plugin\SubContent43\Repository;

use Eccube\Repository\AbstractRepository;
use Plugin\SubContent43\Entity\SubContent;
use Plugin\SubContent43\Entity\Config;
use Doctrine\Persistence\ManagerRegistry;
use Plugin\SubContent43\Service\ScService;
use Doctrine\ORM\NoResultException;

class SubContentRepository extends AbstractRepository
{

    const Sort =['ASC','DESC','ASC','DESC'];


    protected ScService $ScService;

    /**
     *  SubContentRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry,ScService $ScService)
    {

        parent::__construct($registry, SubContent::class);
        $this->ScService = $ScService;
    }

    /**
     * 新規登録
     */
    public function New()
    {
        return  new SubContent();
    }


    public function getSearchData($searchData)
    {

        $qb = $this->createQueryBuilder('T')
            ->select('T')
            ->where('T.visible=:Visible')
            ->setParameter('Visible',true)
            ->addOrderBy('T.update_date', 'DESC');



        if(!is_null($searchData )){
            foreach ($searchData as $Key =>$Value){
                if ($Value || $Value===0 ){
                    $Column= strtolower(str_replace('Search_','', $Key));
                    switch  ($Column){
                        case 'holder':
                            $qb->innerJoin('T.Config','C') 
                               ->andwhere("C.id = :{$Key}")
                               ->setParameter($Key,$Value);
                        break;    
                    default:    
                    $qb->andwhere("T.{$Column} LIKE :{$Key}")
                       ->setParameter($Key,"%{$Value}%");
                    break;
                    }    
                }
            }
        }

        return      $qb;//->getQuery()->getResult();


    }

    public function GetSubContentData($Id){

        if (!$SubContent = $this->find($Id)){return null;};
        return  $this->SetSubContent($SubContent);

    }
    public function GetSubContentPart(string $Holder, $Parameter = null ,$kbn = 1 ){

     
        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.visible = :Visible')
            ->setParameter('Visible',true)
            ->andWhere('s.holder = :Holder')
            ->setParameter('Holder',$Holder)
            ->andWhere('s.kbn = :kbn')
            ->setParameter('kbn', $kbn);

        if($Parameter){
            $qb->andWhere('s.parameter = :Parameter')
               ->setParameter('Parameter',$Parameter);
        }
        try{
      
          $SubContent = $qb->getQuery()
                           ->getSingleResult();
          return  $this->SetSubContent($SubContent);
  
        } catch (NoResultException $e) {
            return null;
             
        }
    }
    public function getSubContentCompoPage(string $Holder, string $Parameter, $kbn = 0){


        if(!$Config = $this->getEntityManager()->getRepository(Config::class)->GetConfig($Holder)){return null;}

        $qb = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.visible = :Visible')
            ->setParameter('Visible',true)
            ->andWhere('s.Config = :Config')
            ->setParameter('Config',$Config)
            ->andWhere('s.kbn = :kbn')
            ->setParameter('kbn', $kbn)
            ->andWhere('s.parameter = :Parameter')
            ->setParameter('Parameter',$Parameter);
        
        try{
      
          $SubContent = $qb->getQuery()
                           ->getSingleResult();
          return  $this->SetSubContent($SubContent);
  
        } catch (NoResultException $e) {
            return null;
             
        }                   



    }                        



    public function SetSubContent($SubContent){

        $this->ScService->SetSubContent($SubContent);
        return $this->ScService->GetSubcontent();
    }


    public function Valid($Config,$Subcontent){

        try{

            $Parameter = $Subcontent->getParameter();
                if($Config == null){
                    $Qb=$this->createQueryBuilder('S')
                    ->where('S.Config is null');
                }else{
                    $Qb=$this->createQueryBuilder('S')
                        ->where('S.Config = :Config')
                        ->setParameter('Config', $Config);
                }    
                if ($Parameter){
                    $Qb->andWhere('S.parameter = :paramter')
                       ->setParameter('paramter', $Parameter);
                }else{
                    $Qb->andWhere('S.parameter IS NULL');
                }
                
                if( $Id = $Subcontent->getId()){
                    $Qb->andWhere('S.id != :Id')
                    ->setParameter('Id', $Id);

                }

                $Data = $Qb->getQuery()
                           ->getSingleResult();
       
   
            return true;

        } catch (NoResultException $e) {
            return false;
                  
        }
    }

    public function ValidParts($Holder,$SubContent){

        $Kbn = $this->ScService->GetConfig('Plugin_SubContent_prameter_parts');

    try{
            $Qb  =$this->createQueryBuilder('S');
                    $Qb->where('S.visible=:Visible')
                       ->setParameter('Visible',true)
                      #ホルダーがダブっているのはダメ
                       ->andWhere('S.holder = :Holder')
                       ->setParameter('Holder',$Holder);

                if($Id = $SubContent->getId()){
                        $Qb->andWhere('S.id != :ID')
                        ->setParameter('ID', $Id);
                    }

                if(2 == $Kbn){
                $Parameter = $SubContent->getParameter();
                if ($Parameter){
                    $Qb->andWhere('S.parameter = :paramter')
                       ->setParameter('paramter', $Parameter);
                }else{
                    $Qb->andWhere('S.parameter IS NULL');
                }
            }
                $Data = $Qb->getQuery()
                           ->getSingleResult();
       
   
            return true;

        } catch (NoResultException $e) {
            return false;
                  
        }
    }


    #使っていない
	public function GetList($holder,$Sort=null, $Order = 'ASC' ,$Limit=null){
	
        $qb=$this->createQueryBuilder('S')
                 ->innerJoin('S.Config','C')
                 ->where('C.holder = :holder')
                 ->andWhere('S.visible=0')
                 ->setParameter('holder', $holder);
        
        if ($Sort){
            $qb->orderBy($Sort,$Order);
        }

        if($Limit){
            $qb->setMaxResults($Limit);
        }

        return      $qb->getQuery()
                       ->getResult();

	}

    public function getSubcontentList($Config){

        $Sort = Self::Sort[$Config->getSortFlg()];

        $Order = $Config->getSortFlg() >= 2 ? 's.parameter' : 's.id';

        $qb=$this->createQueryBuilder('s')
             ->innerJoin('s.Config','c') 
             ->where('c.id = :Config')
             ->setParameter('Config',$Config->getJoinId())
             ->orderBy($Order,$Sort);

        return $qb;

    }
    public function getSubcontentListFarst($qb){

        $qb->setMaxResults(1);

        try{
           
            return  $qb->getQuery()
                       ->getSingleResult();
        } catch (NoResultException $e) {
            return false;

        }

    }




}