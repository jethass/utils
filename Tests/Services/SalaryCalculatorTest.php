<?php 
namespace AppBundle\Tests;

use AppBundle\Entity\Employee;
use AppBundle\Services\SalaryCalculator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class SalaryCalculatorTest extends TestCase
{
    public function testCalculateTotalSalary()
    {
        $employee = new Employee();
        $employee->setSalaray(1000);
        $employee->setBonus(1100);

        // Now, mock the repository so it returns the mock of the employee
        $employeeRepository = $this->createMock(ObjectRepository::class);
        // use getMock() on PHPUnit 5.3 or below
        // $employeeRepository = $this->getMock(ObjectRepository::class);
        $employeeRepository->expects($this->any())
            ->method('find')
            ->willReturn($employee);

        // Last, mock the EntityManager to return the mock of the repository
        $objectManager = $this->createMock(ObjectManager::class);
        // use getMock() on PHPUnit 5.3 or below
        // $objectManager = $this->getMock(ObjectManager::class);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($employeeRepository);

        $salaryCalculator = new SalaryCalculator($objectManager);
        $this->assertEquals(2100, $salaryCalculator->calculateTotalSalary(1));
    }
}