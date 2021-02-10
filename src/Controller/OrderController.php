<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ItemRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
	/**
     * @Route("/orders", name="orders_list")
     */
    public function orders(OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Get orders by role
        $user = $this->getUser();
        if (!empty($user)) {
            $roles = $user->getRoles();
            if (in_array("ROLE_ADMIN", $roles)) {
                $orders = $orderRepository->getOrders();                
            } else {
                if (in_array("ROLE_COOK", $roles)) {
                    $orders = $orderRepository->getOrdersForCook();                
                }
                if (in_array("ROLE_WAITER", $roles)) {
                    $orders = $orderRepository->getOrdersForWaiter();                
                }                
            }
        }

        return $this->render('order/orders.html.twig', ['orders' => $orders]);
    }

    /**
     * @Route("/order/{id}", name="order", requirements={"id" = "\d+"})
     */
    public function order(OrderRepository $orderRepository, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entity = $orderRepository->find($id);

        return $this->render('order/order.html.twig', ['order' => $entity]);
    }


    /**
     * @Route("/order/add", name="add_order")
     * @Route("/order/edit/{id}", name="edit_order", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, OrderRepository $orderRepository, $id = null)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (is_null($id)) {
            $entity = new Order;
        } else {
            $entity = $orderRepository->find($id);
        }

        $form  = $this->createForm(OrderType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
            	$entityManager = $this->getDoctrine()->getManager();
            	$entityManager->persist($entity);
            	$entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Order has been successfully saved!');
                
                return $this->redirect($this->generateUrl('orders_list'));
            }
        }

        return $this->render('order/editOrder.html.twig', [
            'form' 	=>  $form->createView(),
            'order'  =>  $entity
        ]);
    }

    /**
     * @Route("/order/new/order_list", name="order_new")
     * @Route("/order/{id}/order_list", name="order_list", requirements={"id" = "\d+"})
     */
    public function list(ItemRepository $itemRepository, OrderRepository $orderRepository, $id = NULL): Response
    {
        if (empty($id)) {
            $order = new Order;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
        } else {
            $order = $orderRepository->find($id);
        }
        $items = $itemRepository->getItems();

        return $this->render('item/list.html.twig', ['items' => $items, 'order' => $order]);
    }

    /**
     * @Route("/order/{id}/item/{item_id}/add", name="add_item_to_order", requirements={"id" = "\d+", "item_id" = "\d+"})
     */
    public function addItemToOrder(Request $request, OrderRepository $orderRepository, ItemRepository $itemRepository, $id, $item_id)
    {
        $entity = $orderRepository->find($id);
        $item = $itemRepository->find($item_id);
        $entity->addItem($item);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('order_list', ['id' => $entity->getId()]));
    }

    /**
     * @Route("/order/{id}/status/change/{status}", name="order_change_status", requirements={"id" = "\d+", "status" = "ordered|served|payed|ready"})
     */
    public function orderChangeStatus(OrderRepository $orderRepository, $id, $status): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entity = $orderRepository->find($id);
        $entity->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('orders_list'));
    }

    /**
     * @Route("/order/{id}/status/finish", name="order_status_finish", requirements={"id" = "\d+"})
     */
    public function orderFinishStatus(OrderRepository $orderRepository, SessionInterface $session, $id): Response
    {
        $entity = $orderRepository->find($id);
        $entity->setStatus('finished');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $session->set('user_order_id', $id);

        return $this->redirect($this->generateUrl('order_finish'));
    }

    /**
     * @Route("/order/my", name="order_my")
     */
    public function userOrder(OrderRepository $orderRepository, SessionInterface $session): Response
    {
        $id = $session->get('user_order_id');
        if (!empty($id)) {
            $entity = $orderRepository->find($id);
        } else {
            return $this->redirect($this->generateUrl('home'));
        }


        return $this->render('order/orderMy.html.twig', ['order' => $entity]);
    }

    /**
     * @Route("/order/delete/{id}", name="delete_order", requirements={"id" = "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteDevice(OrderRepository $orderRepository, $id)
    {
        $entity = $orderRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();

        $this->get('session')->getFlashBag()->add('success', 'Order has been successfully removed!');
        
        return $this->redirect($this->generateUrl('orders_list'));
    }

    /**
     * @Route("/order/finish", name="order_finish")
     */
    public function orderFinish(OrderRepository $orderRepository): Response
    {
        return $this->render('order/orderFinish.html.twig');
    }
}

