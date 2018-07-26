<?php

namespace Absolute\Module\Label\Manager;

use Nette\Database\Context;
use Absolute\Core\Manager\BaseCRUDManager;

class LabelCRUDManager extends BaseCRUDManager
{

    public function __construct(Context $database)
    {
        parent::__construct($database);
    }

    // OTHER METHODS
    // CONNECT METHODS

    public function connectProject($id, $projectId)
    {
        $this->database->table('project_label')->where('label_id', $id)->delete();
        return $this->database->table('project_label')->insert(array(
                    "label_id" => $id,
                    "project_id" => $projectId
        ));
    }

    public function connectNote($id, $projectId)
    {
        $this->database->table('note_label')->where('label_id', $id)->delete();
        return $this->database->table('note_label')->insert(array(
                    "label_id" => $id,
                    "note_id" => $projectId
        ));
    }

    public function connectTodo($id, $projectId)
    {
        $this->database->table('todo_label')->where('label_id', $id)->delete();
        return $this->database->table('todo_label')->insert(array(
                    "label_id" => $id,
                    "todo_id" => $projectId
        ));
    }

    // CUD METHODS

    public function create($userId, $name)
    {
        return $this->database->query("INSERT INTO label SET ?", [
                    'name' => $name,
                    'user_id' => $userId,
                    'created' => new \DateTime(),
        ]);
    }

    public function delete($id)
    {
        $this->database->table('todo_label')->where('label_id', $id)->delete();
        $this->database->table('project_label')->where('label_id', $id)->delete();
        $this->database->table('note_label')->where('label_id', $id)->delete();
        return $this->database->table('label')->where('id', $id)->delete();
    }

    public function update($id, $array)
    {
        return $this->database->table('label')->where('id', $id)->update($array);
    }

}
