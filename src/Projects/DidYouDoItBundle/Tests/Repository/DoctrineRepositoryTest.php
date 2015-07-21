<?php

namespace Projects\DidYouDoItBundle\Tests\Repository;

use Projects\DidYouDoItBundle\Repository\DoctrineRepository;
use Projects\DidYouDoItBundle\Entity\TaskList;
use Projects\DidYouDoItBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class DoctrineRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $doctrineRepository;

    private $mockDoctrinyEntityManager;

    private $mockDoctrineRepository;

    private $mockQuery;

    private $arrayTask = [];
    private $arrayTaskList = [];

    private $taskListOne; 
    private $taskListTwo; 

    private $taskOne; 
    private $taskTwo; 

    public function setUp()
    {
        $this->mockDoctrinyEntityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockDoctrineRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockQuery = $this->getMockBuilder('\Doctrine\ORM\AbstractQuery')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $this->doctrineRepository = new DoctrineRepository($this->mockDoctrinyEntityManager, null);

        $this->taskListOne = new TaskList();
        $this->taskListOne->setTasklistId(345); 
        $this->taskListOne->setName("Work"); 

        $this->taskListTwo = new TaskList();
        $this->taskListTwo->setTasklistId(723); 
        $this->taskListTwo->setName("Sport"); 
        array_push($this->arrayTaskList, $this->taskListOne);
        array_push($this->arrayTaskList, $this->taskListTwo);

        $this->taskOne = new Task();
        $this->taskOne->setTaskId(23); 
        $this->taskOne->setLibelle("Do some work"); 
        $this->taskOne->setChecked(true); 
        $this->taskOne->setTaskList($this->taskListOne); 

        $this->taskTwo = new Task();
        $this->taskTwo->setTaskId(94); 
        $this->taskTwo->setLibelle("Tennis"); 
        $this->taskTwo->setChecked(false); 
        $this->taskTwo->setTaskList($this->taskListTwo); 

        array_push($this->arrayTask, $this->taskOne);
        array_push($this->arrayTask, $this->taskTwo);
    }

    /**
     * Test the result of findTaskList with a mocked repository
     */
    public function testFindTaskList()
    {
        $this->mockDoctrineRepository
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($this->arrayTaskList));

        $this->returnMockedRepository();

        $result = $this->doctrineRepository->findTaskList();
        $this->assertEquals($result[0]->getTasklistId(), 345);
        $this->assertEquals($result[1]->getName(), "Sport");
    }

    /**
     * Test the result of findTaskList with a mocked repository
     */
    public function testFindTaskListById()
    {
        $this->mockDoctrineRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(723))
            ->will($this->returnValue($this->taskListTwo));

        $this->returnMockedRepository();

        $result = $this->doctrineRepository->findTaskListById(723);
        $this->assertEquals($result->getName(), "Sport");
    }

   /* public function testFindAllTaskInOneTaskList()
      {
        $this->mockDoctrinyEntityManager
            ->expects($this->once())
            ->method('createQuery')
            ->with($this->anything())
            ->will($this->returnValue($this->mockQuery));

        $this->mockQuery
            ->expects($this->once())
            ->method('getResult')
            ->will($this->returnValue($this->arrayTask));

        $result = $this->doctrineRepository->findAllTaskInOneTaskList($this->taskListOne);
        $this->assertEquals($result[0]->getLibelle(), "Do some work");
        $this->assertEquals($result[1]->getChecked(), false);
   }*/

    public function testPersistTaskList()
    {
        $this->mockDoctrinyEntityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($this->taskListTwo));

        $result = $this->doctrineRepository->persistTaskList($this->taskListTwo);
        $this->assertEquals($result, 723);
    }

    public function testUpdateTaskList()
    {
        $result = $this->doctrineRepository->updateTaskList($this->taskListOne, $this->taskListOne->getTasklistId());
        $this->assertEquals($result, null);
    }

    public function testFindTaskById()
    {
        $this->mockDoctrineRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(94))
            ->will($this->returnValue($this->taskTwo));

        $this->returnMockedRepository();

        $result = $this->doctrineRepository->findTaskById(94, 723);
        $this->assertEquals($result, $this->taskTwo);

    }

    public function testPersistTask()
    {
        $this->mockDoctrinyEntityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($this->taskOne));

        $result = $this->doctrineRepository->persistTask($this->taskOne);
        $this->assertEquals($result, 23);
    }


    public function testUpdateTask()
    {
        $result = $this->doctrineRepository->updateTask($this->taskOne, $this->taskOne->getTaskId(), $this->taskListOne->getTasklistId());
        $this->assertEquals($result, null);
    }

    public function testFlush()
    {
        $this->mockDoctrinyEntityManager
            ->expects($this->once())
            ->method('flush');

        $this->doctrineRepository->flush();

    }    

    private function returnMockedRepository()
    {
        $this->mockDoctrinyEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($this->mockDoctrineRepository));
    }
}
