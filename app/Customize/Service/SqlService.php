<?php
   /**
    * @version EC-CUBE4.3
    * @copyright 株式会社 翔 kakeru.co.jp
    *
    * 2026年08月06日作成
    *
    *  app\Customize\Service\SqlService.php
    * 
    *
    * SQL文を作成する サイトがデッキ田自伝で削除する
    *
    *
    *                                        ≡≡≡┏(＾o＾)┛
    *****************************************************/
    namespace Customize\Service;


    class SqlService{


    const USER  = 'user';
    const PASS  = 'wZz77iWW';
    const HOST  = 'localhost';
    public const DBNAMES = ['kendo_db','kendo_old'];



    private $Cons;
    private $Table_;
    private $Name;
    private $Select_='T.*';
    private $Set_=[];
    private $Sql;
    private $Sqls_ =[];
    private $Where_=[];

    private $Param =[];
    private $Value_;
    private $Order_=[];
    private $OrderBy_;
    private $Join_ =[];
    private $Limit_;
    private $OffSet_;
    private $Group_;
    private $Field_;
    private $ConName;
    private $Duplicate_ = '';
    private $WhereLogical=[];
    private $Num=0;
    private $ForeignKey_;



    function __construct(){
        define('DEF','');    

        foreach (self::DBNAMES as $DbName){
            
          $this->Cons[$DbName] =  $this->DbManager($DbName);
        }

    }
    /**
     * @param string $Dbname;
     */

    public function Find($Dbname = self::DBNAMES[1]){
        $this->MakeSelect();
  

    return  $this->Fetch($Dbname );

    }

    public function FindArr($Flg=false){

        $Re= $this->FindAll($Flg);

        return $Re == DEF ? [] : $Re;

    }
    public function FindAll($Flg=false){
        #Select の作成
        $this->MakeSelect();
        if ($Flg){
            echo $this->Sql.'<br>';
            print_r($this->Param);
        }
        if (2==$Flg){
            exit;}


    return  $this->FetchAll();

    }

    public function Field($Field){
        $this->Field_=$Field;
        return $this;
    }



    public function Max($Flg=false,$Field=DEF){
        $Field=$this->Field_ ?? $Field;
        $this->Select_="MAX(T.{$Field}) as Value";
        $this->MakeSelect();
        $this->ShowSql($Flg);
        $Arr= $this->Fetch();
        return $Arr['Value'] ?? 0;
    }

    public function Min($Flg=false,$Field=DEF){
        $Field=$this->Field_ ?? $Field;
        $this->Select_="MIN(T.{$Field}) as Value";
        $this->MakeSelect();
        $this->ShowSql($Flg);
        $Arr= $this->Fetch();
        return $Arr['Value'] ?? 0;
    }


    public function Update($Flg=false,$Sqls=DEF){

        $this->Sqls($Sqls);
        $this->Sql ='UPDATE '. $this->Table_  . ' AS ' . $this->Name . ' SET ';
        $this->Sql.= $this->SetColumn();
        $this->Sql.= $this->SetWhere();

         $this->ShowSql($Flg);

        return $this->Exec();
    }

    public function Insert($Flg=false,$Sqls=DEF){

        $this->Name='';

        $this->Sqls($Sqls);
        $this->Sql ='INSERT  INTO '. $this->Table_  . ' SET ' ;
        $this->Sql.= $this->SetColumn();

        $this->ShowSql($Flg);

        return $this->Exec();
    }
    public function Duplicate($Value=DEF){
        $this->Duplicate_ =$Value;
        return $this;
    }

    public function ForeignKey($Flg=0){
        $this->ForeignKey_= $Flg;
        return $this;
    }

    /**
  * 2020/06/03
  * 有ればUP 無ければINSERT
    @param string $DbName
  * @param  bool $Flg; 複数行

  */
  public function Inserts($DbName = self::DBNAMES[1] ,$Flg=false){
 
 
    $this->Sql = "INSERT INTO {$this->Table_} (" . implode(',',array_keys($this->Sqls_[0])) .') VALUES ';
    //$Sql = "";

    foreach ($this->Sqls_ as $Data){
        $Sql = '(';
        foreach ($Data as $Column =>$Value){
            $Sql .=  $this->SetValue($Value,$Column)  .",";
        }
        $this->Sql .= trim($Sql,',') . '),';
    }
        $this->Sql = trim($this->Sql,',');
//echo $this->Sql;

    if($this->Duplicate_){
        $this->Sql .=' ON DUPLICATE KEY UPDATE '.$this->Duplicate_;
    }

       $this->ShowSql($Flg);

        return $this->Exec($DbName);
}




    public function Delete($Flg=false){

        $this->Name='';
        $this->Sql ='DELETE FROM '. $this->Table_  .' ';
        $this->Sql.= $this->SetWhere();

         $this->ShowSql($Flg);

        return $this->Exec();
    }
    public function ShowColumns(){
        ;
        $this->Sql = "SHOW COLUMNS FROM {$this->Table_} ";

        $Arr = $this->FetchAll();
        foreach ($Arr as $i => $Val){
            $Re[$Val['Field']]=$Val;
        }

return $Re;
}
    public function ShowTable(){
        $this->Sql ="SHOW TABLE STATUS";
        $Arr= $this->FetchAll();
        foreach ($Arr as $val){
            $Re[$val['Name']]=$val;
        }
 return $Re;
}
    public function AUTO_INCREMENT($Flg=false){

        $this->Sql="ALTER TABLE {$this->Table_} AUTO_INCREMENT={$this->Value_}" ;

        $this->ShowSql($Flg);

        return $this->Exec();

    }


    protected function ShowSql($Flg=false){

            if (!$Flg) {
               return;
            }else{
                echo $this->Sql.'<br>';
                print_r($this->Param);
            }
            if ($Flg==2){
                exit;
            }
    }

    protected function Initialize(){

        $this->Table_   =   null;
        $this->Name     =   null;
        $this->Select_  =   'T.*';
        $this->Set_     =   [];
        $this->Where_   =   [];
        $this->Param    =   [];
        $this->Order_   =   [];
        $this->OrderBy_ =   [];
        $this->Join_    =   [];
        $this->Limit_   =   null;
        $this->OffSet_   =   null;
        $this->Group_   =   null;
        $this->Sqls_    =  [];
        $this->Sql      = null;
        $this->Duplicate_ =DEF;
//        $this->Or_      = [];
        $this->Num      = 0;
        if (0 == $this->ForeignKey_){
            $this->FOREIGN_KEY(self::DBNAMES[1]);
        }


        $this->ForeignKey_=null;

    }

    public function GetVerSion(){
        //$SqlInfo=$mysqli->server_info;
        $this->ConName;
        $this->Sql='select version()';
        $Re=$this->Fetch();
        return $Re['version()'];
        //printf("<br>MySql Server version: %s\n", $mysqli->server_info);
    }


    /**
     * Undocumented function
     *
     * @param string $Sql;
     * @return SqlService
     */
    public function setSql($Sql){

        $this->Sql = $Sql;
        return $this;
    }



   /**
    * テーブルのの定義
    * @param string $Value
    * @param Name string
    *
    */
    public function Table($Value,$Name='T'){
        $this->Table_=$Value;
        if ($Name){
            $this->Name=$Name;
        }
    return $this;
    }

   /**
    * SELECTの定義
    * @param Select string
    *
    */
    Public function Select($Value){
        $this->Select_=$Value;
        return $this;
    }
   /**
    * Whereの定義
    * @param Select string
    *
    */
    public function Where($Value,$Logical='AND'){
        if (is_array($Value)){
        }

        $this->Where_[]=$Value;
        $this->WhereLogical[]=$Logical;
        return $this;
    }

   /**
    * Whereの OR でくくる 今後未使用
    * @param Column string
    * @param Value 値
    * @param Math 四則 =,!=,<,<=,>,>=,NULL
    * @param Param array $Param['Name'] = AS 通常はT1､T2, $Logical=>'AND';
    */
   /* public function SetOr($Column,$Value,$Math='=',$Param=[]){
        $Math= $Math ?? '=';
        $this->Or_[] = array_merge(['Column'=>$Column,'Value'=>$Value,'Math'=>$Math],$Param);
        return $this;
    }*/

   /**
    * Setの定義 And でつなぐ場合
    * @param Column string
    * @param Value 値
    * @param Math 四則 =,!=,<,<=,>,>=,NULL
    * @param Param array Name' = AS 通常はT1､T2, logical  AND OR divide 区切り
    *
    */
    public function Set($Column,$Value,$Math='=',$Param=[]){
        $Math= $Math ?? '=';
        $this->Set_[]=array_merge(['Column'=>$Column,'Value'=>$Value,'Math'=>$Math],$Param);
#       $this->Set_[]=['Column'=>$Column,'Value'=>$Value,'Math'=>$Math];
       return $this;
    }

   /**
    * Setの定義 And でつなぐ場合
    * @param Column string
    * @param Value 値
    */
    public function Sets($Wheres=[]){
        foreach ($Wheres as $Column => $Value) {
            $this->Set_[]=['Column'=>$Column,'Value'=>$Value,'Math'=>'='];
        }
        return $this;
    }


   /**
    * Joinの定義
    * @param Table string
    * @param On string
    * @param Join string
    *
    */
    public function Join($Table,$On,$Join='LEFT'){
    $this->Join_[]=['Table'=>$Table,'On'=>$On,'Join'=>$Join];
    return $this;
    }

   /**
    * Orderの定義
    * 2020/05/20 今後はこれを使用する
    * @param Column string
    * @param ASC string ASC,DESC
    *
    */
    public function Orders($Columns){

        $Set =function ($Colum) {

            $Colums= explode(' ', $Colum);

            $Sort= $Colums[1] ?? 'ASC';

            $Sort= preg_match('/DESC/i', trim($Sort)) ? 'DESC' : 'ASC';

            return ['Column'=>trim($Colums[0]),'Asc'=>$Sort];

        };

        if (is_array($Columns)){
            foreach ($Columns as $Column){
               $this->Order_[] =$Set($Column);
            }
        }else{
               $this->Order_[] =$Set($Columns);
        }

    return $this;
    }


    public function Order($Column,$Asc='ASC'){
    $this->Order_[] =['Column'=>$Column,'Asc'=>$Asc];
    return $this;
    }
    public function OrderBy($Column){
    $this->OrderBy_ =$Column;
    return $this;
    }


    public function Group($Group){
    $this->Group_=$Group;
    return $this;
    }

    public function Sqls($Sqls = DEF,$Value=DEF){
        if (is_array($Sqls)){
            $this->Sqls_=array_merge($this->Sqls_,$Sqls);
        }else{
            if (!$Sqls){return $this;}
            $this->Sqls_[$Sqls]=$Value;
        }
        return $this;

    }
    public function Value($Value){

        $this->Value_ =$Value ;
        return $this;

    }

    public function Limit($Limit,$OffSet=''){
    $this->Limit_  =$Limit;
    $this->OffSet_ =$OffSet;
    return $this;
    }
    public function Offset($OffSet){
    $this->OffSet_ =$OffSet;
    return $this;
    }
   /**
    * Activeの設定
    * @param Column string
    * @param ASC string ASC,DESC
    *
    */
    Public function Active($Value=0,$Math='='){
        $this->Set('activ',$Value,$Math);
        return $this;
    }

    public function Auditd($Flg='UP'){

        $this->Sqls_['update_id']   = $this->App->GetLogin('member_id') ?? 0;
        $this->Sqls_['update_date'] = date('Y-m-d H-i-s');
        $this->Sqls_['update_page'] = $this->App->GetPageName();

        if ('DL'==$Flg){
                $this->Sqls_['active']=1;
        }

        if ('IN'==$Flg){
            $this->Sqls_['active'] =0;
            $this->Sqls_['create_id']   = $this->App->GetLogin('member_id') ?? 0;
            $this->Sqls_['create_date'] = date('Y-m-d H-i-s');
        }


        return $this;
    }
   /**
    * SELECT SQL文の作成
    *
    * @return string
    */

    protected function MakeSelect(){

        $this->Sql ='SELECT ' . $this->Select_ . ' ';
        $AS = $this->Name ? ' AS ' .$this->Name :'';
        $this->Sql.='FROM '  . $this->Table_   . $AS ;
        $this->Sql.= $this->SetJoin();
        $this->Sql.= $this->SetWhere();
        $this->Sql.= $this->SetOrder();

        if ($this->Group_){
            $this->Sql.= ' GROUP BY '.$this->Group_;
        }
        if ($this->Limit_){
            $this->Sql.= ' Limit ' .$this->Limit_;
        }
        if ($this->OffSet_){
            $this->Sql.= ' OFFSET ' .$this->OffSet_;
        }

        return $this;

        }

   /**
    * 外部連結を作成する
    *
    * @return string $joins
    *
    */
    private function SetJoin(){

        $Joins ='';
        foreach ($this->Join_ as $i =>$Join){
            $Joins.= ' ' . $Join['Join'] .' JOIN ' . $Join['Table'] . ' AS ' . $this->Name. ($i+1) . ' ON ' .$Join['On'];
        }
        return $Joins;
    }


    protected function SetColumn(){


        $Sql='';
        $KIgo = $this->Name ? '.' : '' ;
        foreach ($this->Sqls_ as  $Column=>$Value){

            $Param=":{$Column}";

            switch(true){
                case 'NOW()'===$Value:
                    $Value =date('Y-m-d H-i-s');
                break;
           //    case 'NULL'===$Value:
           //          $Value=null;
           //    break;
           // case preg_match('/\*/',$Val):
           //      $Val= $this->Con->quote($Val);
           // break;
           //     case preg_match('/NULL/i',$Value):
           //         $Value='NULL';
           //     break;

            default:

            break;

        }

         $Sql.=$this->Name.$KIgo."{$Column} = {$Param} ".C;
         $this->Param[$Param]=$Value;

        }

        return  rtrim($Sql,C);

    }
    /**
     * @param mixed $Value
     * @param string $Column
     * @return mixed $Val 
     */

    protected function SetValue($Value,$Column){

           if($Column =='birth'){ 
                if($Value == '0000-00-00 00:00:00'){
                    $Value = null;
                };

           }

            $Val = null;
            switch(true){
                case 'NOW()'===$Value:
                    //$Sqls[$Column] ="'". date('Y-m-d H-i-s') ."'";
                    break;
                case iS_NULL($Value):
                case 'NULL' == $Value:
                        $Val = 'NULL';
                    break;
                case is_numeric($Value):
                    
                    if('phone_number' == $Column || 'fax_number' == $Column){
                        $Val =   "'" . $Value ."'";
                    }else{
                        $Val = $Value;
                    }
                   
                    break;
                    
                default:
                    $Val  =   "'" . $Value ."'";
                    break;

            }

       




        return   $Val;

    }


   /**
    * WHERE句を作成する
    *
    * @return string $where
    *
    */
    protected function SetWhere(){

        $Where ='';
        foreach ($this->Set_ as  $i=>$Set){
            #区切りを閉じる
            if (isset($Set['divide'])) {$Where .=' ) ' ;}
            #論理記号を入れる
            $Logical= $Set['logical'] ?? 'AND';
            $Where .= $Where ? " {$Logical} ": ' WHERE (';
            #区切りを始める
            if (isset($Set['divide'])) {$Where .=' ( ' ;}
            $Where .=$this->MakeWhere($Set);
            }

            $Where .= $Where && count($this->Set_)  ? ')' :DEF;


        foreach ($this->Where_ as $i=>$Wheres){
             if ($Wheres === reset($this->Where_)) {
                 $Where .= $Where ? ' ' . $this->WhereLogical[$i] : ' WHERE ';
            }
                $Where .= ' ( ' . $Wheres . ' ) ';


        }
        //$Where .= $Where  && count($this->Where_)  ? ')' :DEF;
        //if($Where ){$Where.=') ' ;}

        /*foreach ($this->Or_ as $i=>$Set){
            if (isset($Set['divide'])) {$Where .=' ) ' ;}
            if ($Set === reset($this->Or_)) {
                $Where .= $Where ? " OR( ": ' WHERE (';
            }else{
                $Logical= $Set['logical'] ?? 'AND';
                $Where .= " {$Logical} " ;
            }
             #区切りを始める
            if (isset($Set['divide'])) {$Where .=' ( ' ;}
            $Where .=$this->MakeWhere($Set);
        }

        $Where .= $Where  && count($this->Or_)  ? ')' :DEF;*/

        return $Where;

    }

    protected function MakeWhere($Set){

    #カラムの設定
       $Column =  $Set['Column'];
    if(preg_match('/\.(.+)/', $Column)){
        $Name='';
        $Param=str_replace('.', '_',$Column) ;
    }else{
        $Kigo=$this->Name ? '.' : '';
        $Name= isset($Set['Param']['Name'])  ? $Set['Param']['Name'] .'.': $this->Name. $Kigo;
        $Param=$Column;
    }

    $Param=':' .$Param  .'_' . $this->Num++;

    $Math  = $Set['Math'];
    $Value = $Set['Value'];

    switch (true) {
        case 'IN'==$Math:
                $Value =  is_array($Value) ? implode(',', $Value) : $Value;
                $Math="IN ( {$Value} ) ";
              //    $this->Param[$Param]= " ({$Value}) ";
                $Param ='';
            break;
        case preg_match('/NOT NULL|NOTNULL/i'   ,$Value):
        case preg_match('/NOT NULL|NOTNULL/ii'  ,$Math):
        case preg_match('/IS NOT NULL/i',$Math):
                $Math='IS NOT NULL';
                $Param ='';
                break;
        case preg_match('/NULL/i'   ,$Math):
        case preg_match('/IS NULL/i',$Math):
        case preg_match('/NULL/i'   ,$Value):
                $Math='IS NULL';
                $Param ='';
                break;
        case preg_match('/NO TLIKE/i',$Math):
        case preg_match('/\%NOT LIKE\%/i',$Math):
                $Math='NOT LIKE';
                $this->Param[$Param]='%'.$Value.'%';
                break;
        case preg_match('/\%NOT LIKE/i',$Math):
                $Math='NOT LIKE';
                $this->Param[$Param]='%'.$Value;
            break;
        case preg_match('/NOT LIKE\%/i',$Math):
                $Math='NOT LIKE';
                $this->Param[$Param]=$Value.'%';
            break;

        case preg_match('/LIKE/i',$Math):
        case preg_match('/\%LIKE\%/i',$Math):
                $Math='LIKE';
                $this->Param[$Param]='%'.$Value.'%';
                break;
        case preg_match('/\%LIKE/i',$Math):
                $Math='LIKE';
                $this->Param[$Param]='%'.$Value;
            break;
        case preg_match('/LIKE\%/i',$Math):
                $Math='LIKE';
                $this->Param[$Param]=$Value.'%';
            break;
        case 'EXISTS'==$Math:
                $Name='';
                break;
        default:
            $this->Param[$Param]=$Value;
            break;
    }

       return $Name.$Column.' '.$Math.' '.$Param.' ';


    }
   /**
    * Order の設定
    *
    * @return string 
    */
    protected function SetOrder(){

        $Orders = DEF;
        if ($this->OrderBy_){
        $Orders = ' ORDER BY ' . $this->OrderBy_ . ' ';
        }

        foreach ($this->Order_ as $Order) {
             $Orders .= $Orders ? ',' :' ORDER BY ';
             $Column = $this->SetName($Order['Column']);
             $Orders .= $Column . ' ' . $Order['Asc']. ' ';
        }

        return $Orders;

    }
    protected function SetName($Column){
        if (!$this->Name){return $Column;}
        if(preg_match('/^(.+)\.(.+)$/', $Column)){return $Column;}
        return $this->Name.'.'.$Column;
    }



/*
http://dozo.matrix.jp/pear/index.php/PECL/pdo/fetch.html
execute()  準備したprepareに入っているSQL文を実行
prepare()  値部分にパラメータを付けて実行待ち
query()    prepareを使わずにSQL文を実行
PDOException  エラーを投げる
bindParam  与えられた変数を文字列としてパラメータに入れる
bindValue  与えられた変数や数値を型を指定してパラメータに入れる※1
PDO::PARAM_STR 変数の値を文字列として扱う
PDO::PARAM_INT 変数の値を数値として扱う
PDO::PARAM_BOOL
PDO::PARAM_NULL (integer)
:nameなど    パラメータ（：の後に任意の文字）
PDO::FETCH_ASSOC   連想配列として取得します。※2

PDO::ATTR_ORACLE_NULLS
$stmt->bindParam(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':value', 1, PDO::PARAM_INT);
$stmt->bindValue(":value", null, PDO::PARAM_NULL); NULL を入れる

*/





/*
$
$sth->execute(array(':calories' => 150, ':colour' => 'red'));
$red = $sth->fetchAll();
*/
 /**
     * クエリを実行し、結果をすべて取得
     *
     * @param string $DbName
     * @param array $params
     * @return array
     * 空の戻り値がbArrayを返す を DEF 初期値 に統一
     */
    protected function FetchAll($DbName = self::DBNAMES[1])
    {

        if(!$this->Sql){return DEF ;}

        $Con=$this->Cons[$DbName];

     //   echo $this->Sql.'<br>';
        $Re =[];

        if (count($this->Param)>0){
            $stmt = $Con->prepare($this->Sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);

            foreach ($this->Param as $Param=>$Value){
                $stmt->bindValue($Param,$Value,\PDO::PARAM_STR);
            }
            $stmt->execute();
            //$stmt->execute($this->Param);
        }else{
            $stmt = $Con->query($this->Sql);
        }

        $Re = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        $this->Initialize();

    return Count($Re)>0 ? $Re: DEF;
   }
    /**
     * クエリを実行し、結果を1行取得
     *
     * @param string $DbName
     * @return array
     *
     * 空の戻り値がboolean =falseを返す FetchAllと統一
     */
   protected function Fetch($DbName = self::DBNAMES[1]){

    if(!$this->Sql){return DEF ;}

   $Con=$this->Cons[$DbName];

    if (count($this->Param)>0){
         $stmt = $Con->prepare($this->Sql ,[\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);

       foreach ($this->Param as $Param=>$Value){

                $stmt->bindValue($Param,$Value,\PDO::PARAM_STR);

       }
        $stmt->execute();
     }else{

        $stmt = $Con->query($this->Sql);
    }

    $this->Initialize();

    $Value = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return $Value[0] ?? DEF;

    }


    /**
     * @param string $DbName 
     * @param int $Flg
     */

    public function FOREIGN_KEY($DbName = self::DBNAMES[1] ,$Flg = 0){

    //echo $DbName;

    $Con = $this->Cons[$DbName];
                           
    $stmt = $Con->prepare("SET FOREIGN_KEY_CHECKS = {$Flg};");
    $stmt->execute();

    }


    public function Exec($DbName = self::DBNAMES[1]){

    if (!is_null($this->ForeignKey_)){
        $this->FOREIGN_KEY($DbName,$this->ForeignKey_);
    }
    $Con = $this->Cons[$DbName];
    $stmt = $Con->prepare($this->Sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);

    foreach ($this->Param as $Param=>$Value){
        switch (true) {
            case is_null($Value):
                 $stmt->bindValue($Param, null, \PDO::PARAM_NULL);
                break;
            case preg_match('/^0+[0-9]+$/',$Value): #頭が0の数字は　文字として判断
                $stmt->bindValue($Param,$Value,\PDO::PARAM_STR);
                break;
            case is_numeric($Value):
                $stmt->bindValue($Param, $Value, \PDO::PARAM_INT);
                break;
             default:
                $stmt->bindValue($Param,$Value,\PDO::PARAM_STR);
                break;
        }
    }

    $Num = $stmt->execute();
//    $stmt =$Con->prepare("SET FOREIGN_KEY_CHECKS=1;");
//    $stmt->execute();
    $this->Initialize();
    return $Num;

    }

public function TRUNCATE($DbName =self::DBNAMES[1]){
if (!$this->Table_){return ;}

if (!is_null($this->ForeignKey_)){
        $this->FOREIGN_KEY($DbName,$this->ForeignKey_);
}
$Sql= 'TRUNCATE ' . $this->Table_;

$Con = $this->Cons[$DbName];
$Con->exec($Sql);

if (!is_null($this->ForeignKey_)){
        $this->FOREIGN_KEY($DbName,1);
}
 $this->Initialize();
 return ;

}


/**
 *
 * @param string $DbName
 * @return object $Con
 */
private function DbManager($DbName){

        try {
        $Con = new \PDO(
            "mysql:dbname=". $DbName .";host=" .self::HOST.";charset=utf8;",self::USER,self::PASS);


        $Con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
        echo '接続できない';
        }

        
    
    return $Con;
    }   

     /**
      * @param string $DbName
      */           

    public function ShowColumn($DbName =self::DBNAMES[1]){

     //   $this->Sql  = 'SELECT COLUMN_NAME, COLUMN_TYPE FROM information_schema.COLUMNS' ;
    //    $this->Sql .= ' WHERE TABLE_SCHEMA = '.$DbName;
    //    $this->Sql .= ' AND TABLE_NAME = ' .$this->Table_ .';';
    $this->Sql = 'SHOW COLUMNS FROM ' . $this->Table_ .';';

       return $this->FetchAll($DbName);

    }

    /**
     * Undocumented function
     *
     * @param string $Table
     * @param string $Sql
     * @return array
     */
    public function Converter1($Table,$Sql =null){

        $this->ForeignKey(0)
                         ->Table($Table)
                         ->TRUNCATE(self::DBNAMES[0]);

        $this->Table($Table);


        
        return $this->FindAll();


    }

    /**
     * Undocumented function
     *
     * @param string $Table
     * @param array $Data
     * @return void
     */
    public function Converter2($Table,$Data){

      return   $this->ForeignKey(0)
                     ->Table($Table)
                     ->Sqls($Data)
                     ->Inserts(self::DBNAMES[0]);


    }


}