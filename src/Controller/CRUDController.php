<?php
// src/Controller/CRUDController.php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CRUDController extends BaseController
{
    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function batchActionEdit(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $this->admin->checkAccess('edit');
        $this->admin->checkAccess('delete');

        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                $newPrice = $request->get('newPrice_' . $selectedModel->getId());
                if ($newPrice) {
                    $selectedModel->setPrice($newPrice);
                    $selectedModel->setUpdatedBy($this->container->get('security.token_storage')->getToken()->getUser());
                    $selectedModel->setUpdatedOn(new \DateTime('now'));
                    $modelManager->update($selectedModel);
                }
            }

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());

            return new RedirectResponse(
                $this->admin->generateUrl('list', [
                    'filter' => $this->admin->getFilterParameters()
                ])
            );
        }

        $this->addFlash('sonata_flash_success', 'Batch Edited Price Successfully');

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters()
            ])
        );
    }
}