<?php

namespace App\Controller;

use App\Form\Product1Type;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Vendor;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\VendorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Image;

/**
 * @Route("/crud/product")
 */
class CrudProductController extends AbstractController
{
    /**
     * @Route("/", name="crud_product_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, VendorRepository $vendorRepository, ProductRepository $productRepository): Response
    {
        return $this->render('crud_product/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'vendors'=>$vendorRepository->findAll(),
            'products'=>$productRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="crud_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, VendorRepository $vendorRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $imageFile = $form->get('file')->getData();
            $imageFile->move('/var/www/html/national02/bogdanw/symfony/symfonyShop/public/images', $imageFile->getClientOriginalName());
            $product->setFile($imageFile->getClientOriginalName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        return $this->render('crud_product/new.html.twig', [
            'category' => $product,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'vendors'=>$vendorRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="crud_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('crud_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="crud_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, VendorRepository $vendorRepository): Response
    {
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $imageFile = $form->get('file')->getData();
            $imageFile->move('/var/www/html/national02/bogdanw/symfony/symfonyShop/public/images', $imageFile->getClientOriginalName());
            $product->setFile($imageFile->getClientOriginalName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        return $this->render('crud_product/edit.html.twig', [
            'category' => $product,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'vendors'=>$vendorRepository->findAll(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}", name="crud_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('crud_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
