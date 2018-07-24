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

    public function renderDefault() {
        $user_id = 5; //$this->user->id;

        switch ($this->httpRequest->getMethod()) {
            case 'GET':
                $urlId = $this->getParameter('urlId');
                if ($this->getParameter('urlId') != null) {
                    $this->_getRequest($urlId);
                } else {
                    $this->_getListRequest($this->getParameter('offset'), $this->getParameter('limit'));
                }
                break;
            case 'POST':
                $name = json_decode($this->httpRequest->getRawBody(), true)["name"];
                $ret = $this->labelCRUDManager->create($user_id, $name);
                if ($ret == null) {
                    $this->jsonResponse->payload = [];
                    $this->httpResponse->setCode(Response::S500_INTERNAL_SERVER_ERROR);
                } else {
                    $this->jsonResponse->payload = [];
                    $this->httpResponse->setCode(Response::S201_CREATED);
                }

                break;
            case 'PUT':
                $post = json_decode($this->httpRequest->getRawBody(), true);
                $id = isset($post['id']) ? $post['id'] : $this->getParameter('urlId');
                if ($id == null) {
                    $this->jsonResponse->payload = [];
                    $this->httpResponse->setCode(Response::S400_BAD_REQUEST);
                } else if ($this->labelManager->canUserEdit($user_id, $id)) {
                    unset($post['id']);
                    unset($post['user_id']);
                    $this->jsonResponse->payload = [];
                    $this->labelCRUDManager->update($id, $post);
                } else {
                    $this->jsonResponse->payload = [];
                    $this->httpResponse->setCode(Response::S403_FORBIDDEN);
                }
                break;
            case 'DELETE':
                
                $post = json_decode($this->httpRequest->getRawBody(), true);
                $id = isset($post['id']) ? $post['id'] : $this->getParameter('urlId');
                
                if ($this->labelManager->canUserEdit($user_id, $id)) {
                    $this->labelCRUDManager->delete($id);
                    $this->httpResponse->setCode(Response::S200_OK);
                }
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
        $users = $this->labelManager->getList($this->user->id, $offset, $limit);
        $this->httpResponse->setCode(Response::S200_OK);
        $this->jsonResponse->payload = $users;
    }

}
