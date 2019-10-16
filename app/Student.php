<?php


namespace App;

use App\Model\Model;
use PDO;

class Student extends Model
{
    public $table = "students";

    public function find($id)
    {
        $sql = 'SELECT students.id as id, students.name as name, boards.id as board_id , grade FROM ' . $this->table . ' 
            LEFT JOIN grades ON students.id = grades.student_id 
            LEFT JOIN boards on students.board_id = boards.id
            WHERE students.id=:student_id 
            ';

        // prepare query statement
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':student_id', $id);

        // execute query
        $stmt->execute();

        $result = $stmt->fetchAll();

        $grades = [];
        foreach ($result as $grade) {
            $grades[] = $grade['grade'];
        }

        $student = new \stdClass();
        $student->id = $result[0]['id'];
        $student->name = $result[0]['name'];
        $student->board = $result[0]['board_id'] === 1
            ? new CSM($result[0]['id'], $grades, $result[0]['name'])
            : new CSMB($result[0]['id'], $grades, $result[0]['name']);

        return $student;
    }
}