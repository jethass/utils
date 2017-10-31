<?php
namespace AppBundle\Services;

use AppBundle\Entity\Employee;
use Doctrine\Common\Persistence\ObjectManager;

class SalaryCalculator
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function calculateTotalSalary($id)
    {
        $employeeRepository = $this->objectManager->getRepository(Employee::class);
        $employee = $employeeRepository->find($id);

        return $employee->getSalary() + $employee->getBonus();
    }
}