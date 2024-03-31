<?php
class Model_User extends Model
{
    public function insertMultiple($rows){
        
        try {
            $this->conn->beginTransaction(); 
            $insert_values = array();

            foreach($rows as $d){
                $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
                $datafields = array_keys($d);
            }

            $sql = "INSERT INTO users (" . implode(",", $datafields ) . ") VALUES " . implode(',', $question_marks);

            $stmt = $this->conn->prepare($sql);
            
            $stmt->execute($insert_values);
        } catch (Exception $e){
            $this->conn->rollBack();
            return ['error' => true, 'message' => $e->getMessage()];
        }
        $this->conn->commit();
        return ['error' => false,  'message' => 'Транзакция завершена'];
    }

    function placeholders($text, $count=0, $separator=","){
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }
    

    public function getRelatives($limit, $offset) {
        $sql = "SELECT name_last as lastname, street_city as city, count(name_last) as count FROM users 
                    GROUP BY lastname, city HAVING count  > 1 ORDER BY count DESC LIMIT {$limit} OFFSET {$offset};";

        $data =  $this->multipleQuery($sql);
        return $data;
    }

    public function getRelativesCount() {
        $sql = "SELECT count(count) as count from (SELECT name_last as lastname, street_city as city, count(name_last) as count FROM users 
        GROUP BY lastname, city HAVING count  > 1) as c;";

        $data =  $this->query($sql);
        return $data;
    }

}



// public function createTable( $cols, $types ) {

//     $str = '';
//     for ($i=0; $i < count($cols); $i++) { 
//         if($types[$i] == 'string') $types[$i] = 'varchar(128)';
//         if($types[$i] == 'integer') $types[$i] = 'integer';
//         $str .= $cols[$i] . ' ' . $types[$i] . ', ';
//     }
//     // }
//     $str = substr($str, 0, -2);
//     $sql = "CREATE TABLE IF NOT EXISTS `users` ($str)";
//     $this->conn->exec($sql);
//     return ['message' => 'success create'];

// }



