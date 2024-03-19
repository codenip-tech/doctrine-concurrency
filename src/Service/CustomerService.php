<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
    ) {}

    public function deactivateAll(): void
    {
        //        $customers = $this->customerRepository->findAll();
        //
        //        foreach ($customers as $customer) {
        //            $customer->activate();
        //            $this->customerRepository->persist($customer);
        //        }
        //
        //        $this->customerRepository->flush();

        $batchSize = 500;
        $counter = 0;

        /** @var Customer $customer */
        foreach ($this->customerRepository->findAllAsIterator() as $customer) {
            $customer->activate();

            ++$counter;

            if (($counter % $batchSize) === 0) {
                $this->customerRepository->flush(); // Executes all updates.
                $this->customerRepository->clear(); // Detaches all objects from Doctrine!
            }
        }
    }
}
