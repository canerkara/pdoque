<?php
/**
 * pdoque
 * Extend your models from this class for basic CRUD
 * For complex queries use your extended classes.
 * @author Caner KARA
 */
class Pdoque
{
    /**
     * Table name
     * @var string
     */
    protected $tableName = '';

    /**
     * Table name
     * @var string
     */
    protected $primaryKey = '';

    /**
     * Autoload an object on __construct
     * @param $tableName
     * @param $primaryKey
     */
    function __construct($tableName,$primaryKey=null) {
        $this->tableName=$tableName;
        if(!isset($primaryKey)){
            $this->primaryKey='id';
        }
        else{
            $this->primaryKey=$primaryKey;
        }
    }


    /**
     * @param array $fields
     * @param array $conds
     * @param string $condop
     * @param string $orderby
     * @param string $ordertype
     * @param null $limit
     * @return array
     */
    public function getAll($fields=[], $conds=[], $condop='AND', $orderby='' , $ordertype='ASC', $limit=null){

        $query="";
        if(empty($fields)){
            $query="SELECT * FROM ".$this->tableName;
        }
        else{
            $query="SELECT ".implode(',',$fields)." FROM ".$this->tableName;
        }
        $arr=[];
        if(!empty($conds)){
            $query.=" WHERE ";
            foreach($conds as $field=>$value){
                if(strpos($field,' like')!==false){
                    $query.=$field.' :'.substr($field, 0, -5).' '.$condop.' ';
                    $arr+=[substr($field, 0, -5)=>$value];
                }
                else if(strpos($field,' <')!==false){
                    $query.=$field.' <'.substr($field, 0, -2).' '.$condop.' ';
                    $arr+=[substr($field, 0, -2)=>$value];
                }
                else if(strpos($field,' >')!==false){
                    $query.=$field.' <'.substr($field, 0, -2).' '.$condop.' ';
                    $arr+=[substr($field, 0, -2)=>$value];
                }
                else if(strpos($field,' <=')!==false){
                    $query.=$field.' <'.substr($field, 0, -3).' '.$condop.' ';
                    $arr+=[substr($field, 0, -3)=>$value];
                }
                else if(strpos($field,' >=')!==false){
                    $query.=$field.' <'.substr($field, 0, -3).' '.$condop.' ';
                    $arr+=[substr($field, 0, -3)=>$value];
                }
                else{
                    $query.=$field.'=:'.$field.' '.$condop.' ';
                    $arr+=[$field=>$value];
                }

            }
            if($condop=='OR') {
                $query = substr($query, 0, -4);
            }
            else{
                $query = substr($query, 0, -5);
            }
        }
        if(!empty($orderby)){
            $query.=" ORDER BY ".$orderby." ".$ordertype;
        }
        else{
            $query .= " ORDER BY ".$this->primaryKey." ".$ordertype;
        }
        if(isset($limit)){
            $query.=" LIMIT ".$limit;
        }
        //var_dump($query);
        //var_dump($arr);die;
        global $db;
        $select=$db->prepare($query);
        $select->execute($arr);
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $idVal
     * @param array $fields
     * @return mixed
     */
    public function getById($idVal, $fields=[]){

        if(empty($fields)){
            $query="SELECT * FROM ".$this->tableName." WHERE ".$this->primaryKey."=:".$this->primaryKey;
        }
        else{
            $query="SELECT ".implode(',',$fields)." FROM ".$this->tableName." WHERE ".$this->primaryKey."=:".$this->primaryKey;
        }
        global $db;
        $select=$db->prepare($query);
        $select->execute(array($this->primaryKey=>$idVal));
        return $select->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param $fields
     * @return int|string
     */
    public function insert($fields){

        if(!empty($fields)){
            $query="INSERT INTO ".$this->tableName." SET ";
            $arr=[];
            foreach($fields as $field=>$value){
                $query.=$field.'=:'.$field.',';
                $arr+=[$field=>$value];
            }
            $query = substr($query, 0, -1);

            global $db;
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $ins_query=$db->prepare($query);
            $insert=$ins_query->execute($arr);
            if($insert){
                return $db->lastInsertId();
            }
            else{
                return -1;
            }
        }
        else{
            return -1;
        }
    }

    /**
     * @param $fields
     * @param $conds
     * @param string $condop
     * @return int
     */
    public function update($fields, $conds, $condop='AND'){

        if(!empty($fields) && !empty($conds)){
            $query="UPDATE ".$this->tableName." SET ";
            $arr=[];
            foreach($fields as $field=>$value){
                $query.=$field.'=:'.$field.',';
                $arr+=[$field=>$value];
            }
            $query = substr($query, 0, -1);

            $query.=" WHERE ";
            foreach($conds as $field=>$value){
                if(strpos($field,'like')!==false){
                    $query.=$field.' :'.substr($field, 0, -5).' '.$condop.' ';
                    $arr+=[substr($field, 0, -5)=>$value];
                }
                else{
                    $query.=$field.'=:'.$field.' '.$condop.' ';
                    $arr+=[$field=>$value];
                }
            }
            if($condop=='OR') {
                $query = substr($query, 0, -4);
            }
            else{
                $query = substr($query, 0, -5);
            }

            //var_dump($query);
            //var_dump($arr);die;
            global $db;
            $upd_query=$db->prepare($query);
            $update=$upd_query->execute($arr);
            if($update){
                return 1;
            }
            else{
                return -1;
            }
        }
        else{
            return -1;
        }
    }

    /**
     * @param $idVal
     * @param $fields
     * @return int
     */
    public function updateById($idVal, $fields){

        if(!empty($fields)){
            $query="UPDATE ".$this->tableName." SET ";
            $arr=[];
            foreach($fields as $field=>$value){
                $query.=$field.'=:'.$field.',';
                $arr+=[$field=>$value];
            }
            $query = substr($query, 0, -1);

            $query.=" WHERE ".$this->primaryKey."=:".$this->primaryKey;
            $arr+=[$this->primaryKey=>$idVal];

            global $db;
            //$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
            $upd_query=$db->prepare($query);

            $update=$upd_query->execute($arr);
            if($update){
                return 1;
            }
            else{
                return -1;
            }
        }
        else{
            return -1;
        }
    }

    /**
     * @param $conds
     * @param string $condop
     * @return int
     */

    public function delete($conds, $condop='AND'){

        if(!empty($conds)){
            $query="DELETE FROM ".$this->tableName." WHERE ";
            $arr=[];
            foreach($conds as $field=>$value){
                if(strpos($field,'like')!==false){
                    $query.=$field.' :'.substr($field, 0, -5).' '.$condop.' ';
                    $arr+=[substr($field, 0, -5)=>$value];
                }
                else{
                    $query.=$field.'=:'.$field.' '.$condop.' ';
                    $arr+=[$field=>$value];
                }
            }
            if($condop=='OR') {
                $query = substr($query, 0, -4);
            }
            else{
                $query = substr($query, 0, -5);
            }

            //var_dump($query);die;
            //var_dump($arr);die;
            global $db;
            $del_query=$db->prepare($query);
            $delete=$del_query->execute($conds);
            if($delete){
                return 1;
            }
            else{
                return -1;
            }
        }
        else{
            return -1;
        }
    }

    /**
     * @param $idVal
     * @return int
     */
    public function deleteById($idVal){

        $query="DELETE FROM ".$this->tableName." WHERE id=:id";

        global $db;
        $del_query=$db->prepare($query);
        $delete=$del_query->execute([
            "id"=>$idVal
        ]);
        if($delete){
            return 1;
        }
        else{
            return -1;
        }
    }


    /**
     * @param $query
     * @param array $arr
     * @param bool $rowsExpected
     * @return array|int
     */
    public function customQuery($query, $arr=[], $rowsExpected=true){
        global $db;
        $custom=$db->prepare($query);
        //$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $result=$custom->execute($arr);
        if(!$rowsExpected){
            if($result){
                return 1;
            }
            else{
                return -1;
            }
        }
        else{
            return $custom->fetchAll(PDO::FETCH_ASSOC);
        }

    }
}
