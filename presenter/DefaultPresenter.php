<?php

namespace Absolute\Module\Label\Presenter;

use Nette\Http\Response;
use Nette\Application\Responses\JsonResponse;
use Absolute\Module\Label\Presenter\LabaleBasePresenter;

class DefaultPresenter extends LabaleBasePresenter {

    /** @var \Absolute\Module\Label\Manager\LabelCRUDManager @inject */
    public $labelCRUDManager;

    /** @var \Absolute\Module\Label\Manager\LabelManager @inject */
    public $labelManager;

    public function startup() {
        parent::startup();
    }
    
    public function renderDefault($urlId) {
        switch ($this->httpRequest->getMethod()) {
            case 'GET':
                if ($$urlId != null) {
                    $this->_getRequest($urlId);
                } else {
                    $this->_getListRequest($this->getParameter('offset'), $this->getParameter('limit'));
                }
                break;
            case 'POST':
                $this->_postRequest($urlId);
                break;
            case 'PUT':
                $this->_putRequest($urlId);
                break;
            case 'DELETE':
                $this->_deleteRequest($urlId);
            default:
                
                break;
        }
        $this->sendResponse(new JsonResponse(
                $this->jsonResponse->toJson(), "application/json;charset=utf-8"
        ));
    }

    private function _getRequest($id) {
        $label = $this->labelManager->getById($id);
        if (!$label) {
            $this->httpResponse->setCode(Response::S404_NOT_FOUND);
            return;
        }
        $this->jsonResponse->payload = $label;
        $this->httpResponse->setCode(Response::S200_OK);
    }

    private function _getListRequest($offset, $limit) {
        $labels = $this->labelManager->getList($this->user->id, $offset, $limit);
        $this->httpResponse->setCode(Response::S200_OK);
        $this->jsonResponse->payload = $labels;
    }

    private function _putRequest($id) {
        $post = json_decode($this->httpRequest->getRawBody(), true);
        if ($id == null) {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
        } else if ($this->labelManager->canUserEdit($this->user->id, $id)) {
            unset($post['id']);
            unset($post['user_id']);
            $this->jsonResponse->payload = [];
            $this->labelCRUDManager->update($id, $post);
        } else {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S403_FORBIDDEN);
        }
    }

    private function _postRequest($urlId) {
        $name = json_decode($this->httpRequest->getRawBody(), true)["name"];
        $ret = $this->labelCRUDManager->create($this->user->id, $name);
        if (!$ret) {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
        } else {
            $this->jsonResponse->payload = [];
            $this->httpResponse->setCode(Response::S201_CREATED);
        }
    }

    private function _deleteRequest($id) {
        $post = json_decode($this->httpRequest->getRawBody(), true);

        if ($this->labelManager->canUserEdit($this->user->id, $id)) {
            $this->labelCRUDManager->delete($id);
            $this->httpResponse->setCode(Response::S200_OK);
        }
    }

}
