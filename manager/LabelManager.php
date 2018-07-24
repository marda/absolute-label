<?php

namespace Absolute\Module\Label\Manager;

use Nette\Database\Context;
use Absolute\Core\Manager\BaseManager;

class LabelManager extends BaseManager {

    public function __construct(Context $database) {
        parent::__construct($database);
    } 

    /* INTERNAL/EXTERNAL INTERFACE */

    private function _getById($id) {
        $ret = $this->database->fetch("SELECT * FROM label WHERE id = ?", intval($id));
        return $ret;
    }

    private function _getList($user_id,$offset, $limit) {
        $offset = ($offset == null ? 5 : intval ($offset));
        $limit = ($limit == null ? 5 : intval ($limit));
        $ret = $this->database->fetchAll("SELECT * FROM label LIMIT ?,?", $offset, $limit);
        return $ret;
    }

    private function _getProjectList($projectId) {
        $ret = $this->database->table('label')->where(':project_label.project_id', $projectId)->fetchAll();
        return $ret;
    }

    private function _getUserList($userId) {
        $ret = $this->database->table('label')->where('user_id', $userId)->where('id NOT IN (SELECT label_id FROM project_label)')->fetchAll();
        return $ret;
    }
/*
    private function _getUserProjectList($userId) {
        $projects = $this->database->table('project_user')->where('user_id', $userId)->fetchPairs('project_id', 'role');
        $ret = [];
        $resultDb = $this->database->table('label')->where(':project_label.project_id', array_keys($projects))->order('created DESC');
        foreach ($resultDb as $db) {
            $object = $this->_getLabel($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getUserProjectTodoList($userId) {
        $projects = $this->database->table('project_user')->where('user_id', $userId)->fetchPairs('project_id', 'role');
        $todos = $this->database->table('project_todo')->where('project_id', array_keys($projects))->fetchPairs('todo_id', 'todo_id');
        $labels = $this->database->table('todo_label')->where('todo_id', $todos)->fetchPairs('label_id', 'label_id');
        $ret = [];
        $resultDb = $this->database->table('label')->where(':project_label.project_id', array_keys($projects))->where('label.id', $labels)->order('created DESC');
        foreach ($resultDb as $db) {
            $object = $this->_getLabel($db);
            $ret[] = $object;
        }
        return $ret;
    }

    private function _getUserProjectNoteList($userId) {
        $projects = $this->database->table('project_user')->where('user_id', $userId)->fetchPairs('project_id', 'role');
        $notes = $this->database->table('project_note')->where('project_id', array_keys($projects))->fetchPairs('note_id', 'note_id');
        $labels = $this->database->table('note_label')->where('note_id', $notes)->fetchPairs('label_id', 'label_id');
        $ret = [];
        $resultDb = $this->database->table('label')->where(':project_label.project_id', array_keys($projects))->where('label.id', $labels)->order('created DESC');
        foreach ($resultDb as $db) {
            $object = $this->_getLabel($db);
            $ret[] = $object;
        }
        return $ret;
    }*/

    private function _canUserEdit($id, $userId) {
        $db = $this->database->table('label')->get($id);
        if (!$db) {
            return false;
        }
        if ($db->user_id === $userId) {
            return true;
        }
        $projectsInManagement = $this->database->table('project_user')->where('user_id', $userId)->where('role', array('owner', 'manager'))->fetchPairs('project_id', 'project_id');
        $projects = $this->database->table('project_label')->where('label_id', $id)->fetchPairs('project_id', 'project_id');
        return (!empty(array_intersect($projects, $projectsInManagement))) ? true : false;
    }

    /* EXTERNAL METHOD */

    public function getById($id) {
        return $this->_getById($id);
    }

    public function getList($user_id,$offset, $limit) {
        return $this->_getList($user_id,$offset, $limit);
    }

    public function getProjectList($projectId) {
        return $this->_getProjectList($projectId);
    }

    public function getUserList($userId) {
        return $this->_getUserList($userId);
    }

    /*public function getUserProjectList($userId) {
        return $this->_getUserProjectList($userId);
    }

    public function getUserProjectTodoList($userId) {
        return $this->_getUserProjectTodoList($userId);
    }

    public function getUserProjectNoteList($userId) {
        return $this->_getUserProjectNoteList($userId);
    }*/

    public function canUserEdit($id, $userId) {
        return $this->_canUserEdit($id, $userId);
    }

}
