<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Repository\ItemRepository;
use App\Repository\OrderRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RestController extends AbstractFOSRestController
{
    /**
     * Get items
     *
     * @return JSON array
     *
     * @Rest\Get("/api/items")
     */
	public function getItemsAction(ItemRepository $itemRepository, NormalizerInterface $serializer)
    {
        $data = $itemRepository->getItems();
        $json = $serializer->serialize(
            $data,
            'json',
            ['groups' => 'show_items']
        );
        $view = $this->view($json, 200);
        if (!empty($json)) {
            $view->setStatusCode(Response::HTTP_OK);
        }
        $view->setFormat('json');

        return $this->handleView($view);
    }

    /**
     * Get orders
     *
     * @return JSON array
     *
     * @Rest\Get("/api/orders")
     */
    public function getOrdersAction(OrderRepository $orderRepository, NormalizerInterface $serializer)
    {
        $data = $orderRepository->getOrders();
        $json = $serializer->serialize(
            $data,
            'json',
            ['groups' => 'show_orders']
        );
        $view = $this->view($json, 200);
        if (!empty($json)) {
            $view->setStatusCode(Response::HTTP_OK);
        }
        $view->setFormat('json');

        return $this->handleView($view);
    }
}