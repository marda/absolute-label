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

    public function connectProjects($id, $projects)
    {
        $projects = array_unique(array_filter($projects));
        // DELETE
        $this->database->table('project_label')->where('label_id', $id)->delete();
        // INSERT NEW
        $data = [];
        foreach ($projects as $projectId)
        {
            $data[] = array(
                "label_id" => $id,
                "project_id" => $projectId,
            );
        }

        if (!empty($data))
        {
            $this->database->table('project_label')->insert($data);
        }
        return true;
    }

    public function connectNote($id, $projectId)
    {
        $this->database->table('note_label')->where('label_id', $id)->delete();
        return $this->database->table('note_label')->insert(array(
                    "label_id" => $id,
                    "note_id" => $projectId
        ));
    }

    public function connectNotes($id, $notes)
    {
        $notes = array_unique(array_filter($notes));
        // DELETE
        $this->database->table('note_label')->where('label_id', $id)->delete();
        // INSERT NEW
        $data = [];
        foreach ($notes as $noteId)
        {
            $data[] = array(
                "label_id" => $id,
                "note_id" => $noteId,
            );
        }

        if (!empty($data))
        {
            $this->database->table('note_label')->insert($data);
        }
        return true;
    }

    public function connectTodo($id, $projectId)
    {
        $this->database->table('todo_label')->where('label_id', $id)->delete();
        return $this->database->table('todo_label')->insert(array(
                    "label_id" => $id,
                    "todo_id" => $projectId
        ));
    }

    public function connectTodos($id, $todo)
    {
        $todo = array_unique(array_filter($todo));
        // DELETE
        $this->database->table('todo_label')->where('label_id', $id)->delete();
        // INSERT NEW
        $data = [];
        foreach ($todo as $todoId)
        {
            $data[] = array(
                "label_id" => $id,
                "todo_id" => $todoId,
            );
        }

        if (!empty($data))
        {
            $this->database->table('todo_label')->insert($data);
        }
        return true;
    }

    // CUD METHODS

    public function create($userId, $name, $color)
    {
        return $this->database->table('label')->insert( array_merge([
                    'name' => $name,
                    'color' => $color,
                    'user_id' => $userId,
                    'created' => new \DateTime()
                                ]));
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
        if (isset($array['notes']))
            $this->connectNotes($id, $array['notes']);

        if (isset($array['projects']))
            $this->connectProjects($id, $array['projects']);

        if (isset($array['todos']))
            $this->connectTodos($id, $array['todos']);

        unset($array['id']);
        unset($array['created']);
        unset($array['notes']);
        unset($array['projects']);
        unset($array['todos']);
        return $this->database->table('label')->where('id', $id)->update($array);
    }

}
