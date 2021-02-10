<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ItemController extends AbstractController
{
	/**
     * @Route("/items", name="items_list")
     */
    public function items(ItemRepository $itemRepository): Response
    {
    	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $items = $itemRepository->getItems();

        return $this->render('item/items.html.twig', ['items' => $items]);
    }

    /**
     * @Route("/item/add", name="add_item")
     * @Route("/item/edit/{id}", name="edit_item", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, ItemRepository $itemRepository, $id = null)
    {
    	$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (is_null($id)) {
            $entity = new Item;
        } else {
            $entity = $itemRepository->find($id);
        }

        $form  = $this->createForm(ItemType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
            	$entityManager = $this->getDoctrine()->getManager();
            	$entityManager->persist($entity);
            	$entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'Item has been successfully saved!');
                
                return $this->redirect($this->generateUrl('items_list'));
            }
        }

        return $this->render('item/editItem.html.twig', [
            'form' 	=>  $form->createView(),
            'item'  =>  $entity
        ]);
    }
}