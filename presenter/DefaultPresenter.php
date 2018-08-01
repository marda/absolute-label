<?php

namespace Absolute\Module\Label\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;
use Absolute\Module\Label\Presenter\LabaleBasePresenter;

class DefaultPresenter extends LabaleBasePresenter
{

    /** @var \Absolute\Module\Label\Manager\LabelCRUDManager @inject */
    public $labelCRUDManager;

    /** @var \Absolute\Module\Label\Manager\LabelManager @inject */
    public $labelManager;

    public function startup()
    {
        parent::startup();
    }

    public function renderDefault($resourceId)
    {
        switch ($this->httpRequest->getMethod())
        {
            case 'GET':
                if ($resourceId != null)
                {
                    $this->_getRequest($resourceId);
                }
                else
                {
                    $this->_getListRequest($this->getParameter('offset'), $this->getParameter('limit'));
                }
                break;
            case 'POST':
                $this->_postRequest($resourceId);
                break;
            case 'PUT':
                $this->_putRequest($resourceId);
                break;
            case 'DELETE':
                $this->_deleteRequest($resourceId);
            default:

                break;
        }
        $this->sendResponse(new JsonResponse(
                $this->jsonResponse->toJson(), "application/json;charset=utf-8"
        ));
    }

    private function _getRequest($id)
    {
        if ($this->labelManager->canUserEdit($this->user->id, $id))
        {
            $label = $this->labelManager->getById($id);
            if (!$label)
            {
                $this->httpResponse->setCode(Response::S404_NOT_FOUND);
                return;
            }
            $this->jsonResponse->payload = $label->toJson();
            $this->httpResponse->setCode(Response::S200_OK);
        }
        else
            $this->httpResponse->setCode(Response::S403_FORBIDDEN);
    }

    private function _getListRequest($offset, $limit)
    {
        $labels = $this->labelManager->getList($this->user->id, $offset, $limit);
        $this->httpResponse->setCode(Response::S200_OK);

        $this->jsonResponse->payload = array_map(function($n)
        {
            return $n->toJson();
        }, $labels);
    }

    private function _putRequest($id)
    {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        if ($this->labelManager->canUserEdit($this->user->id, $id))
        {
            $this->jsonResponse->payload = [];
            $this->labelCRUDManager->update($id, $post);
        }
        else
        {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S403_FORBIDDEN);
        }
    }

    private function _postRequest($urlId)
    {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        $ret = $this->labelCRUDManager->create($this->user->id, $post['name'],$post['color']);
        if (!$ret)
        {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        }
        else
        {

            if (isset($post['notes']))
                $this->labelCRUDManager->connectNotes($ret, $post['notes']);

            if (isset($post['projects']))
                $this->labelCRUDManager->connectProjects($ret, $post['projects']);

            if (isset($post['todos']))
                $this->labelCRUDManager->connectTodos($ret, $post['todos']);

            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S201_CREATED);
        }
    }

    private function _deleteRequest($id)
    {
        if ($this->labelManager->canUserEdit($this->user->id, $id))
        {
            $this->labelCRUDManager->delete($id);
            $this->httpResponse->setCode(Response::S200_OK);
        }
    }

}
