<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Service\CustomerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
    ) {}

    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $this->customerRepository->findAll(),
        ]);
    }

    #[Route('/deactivate-all', name: 'deactivate-all', methods: ['GET'])]
    public function deactivateAll(CustomerService $customerService): Response
    {
        $customerService->deactivateAll();

        return $this->render('customer/index.html.twig', [
            'customers' => $this->customerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        $currentCustomer = $entityManager->getRepository(Customer::class)->find($customer->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
            } catch (OptimisticLockException) {
                $this->addFlash('error', 'This customer was already changed by another user. Please refresh before updating it.');

                return $this->render('customer/edit.html.twig', [
                    'customer' => $currentCustomer,
                    'form' => $form,
                ]);
            }
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($customer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
