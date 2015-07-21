<?php

namespace Projects\DidYouDoItBundle\Tests\Repository;

use Projects\DidYouDoItBundle\Repository\GoogleRepository;
use Projects\DidYouDoItBundle\Entity\TaskList;
use Projects\DidYouDoItBundle\Entity\Task;

class GoogleRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $googleRepository;

    private $mockHappyrGoogleClient;

    private $mockGoogleClient;

    private $mockSession;

    private $mockServiceTask;

    private $mockTasklistResource;
    private $mockTaskResource;

    private $mockTasklistCollection;

    private $arrayTask = [];
    private $arrayTaskList = [];

    private $taskListOne; 
    private $taskListTwo; 

    private $taskOne; 
    private $taskTwo; 

    public function setUp()
    {
        $this->mockHappyrGoogleClient = $this->getMockBuilder('\HappyR\Google\ApiBundle\Services\GoogleClient')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockGoogleClient = $this->getMockBuilder('\Google_Client')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockSession = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockServiceTask = $this->getMockBuilder('\Google_Service_Tasks')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->mockTasklistCollection = $this->getMockBuilder('\Google_Service_Tasks_TaskLists')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockSession
            ->expects($this->once())
            ->method('get')
            ->with($this->anything())
            ->will($this->returnValue(null));

        $this->mockGoogleClient
            ->expects($this->once())
            ->method('setAccessToken')
            ->with($this->anything());

        $this->mockGoogleClient
            ->expects($this->once())
            ->method('isAccessTokenExpired')
            ->will($this->returnValue(false));


        $this->mockHappyrGoogleClient 
            ->expects($this->once())
            ->method('getGoogleClient')
            ->will($this->returnValue($this->mockGoogleClient));

        $this->googleRepository = new GoogleRepository(null, $this->mockHappyrGoogleClient, $this->mockSession);
        $this->googleRepository->setServiceTask($this->mockServiceTask);


        $this->taskListOne = new \Google_Service_Tasks_TaskList();
        $this->taskListOne->setId(49); 
        $this->taskListOne->setTitle("IUT"); 

        $this->taskListTwo = new \Google_Service_Tasks_TaskList();
        $this->taskListTwo->setId(50); 
        $this->taskListTwo->setTitle("Purchase"); 
        array_push($this->arrayTaskList, $this->taskListOne);
        array_push($this->arrayTaskList, $this->taskListTwo);

        $this->taskOne = new \Google_Service_Tasks_Task();
        $this->taskOne->setId(2); 
        $this->taskOne->setTitle("Do some work"); 
        $this->taskOne->setStatus("completed"); 

        $this->taskTwo = new \Google_Service_Tasks_Task();
        $this->taskTwo->setId(3); 
        $this->taskTwo->setTitle("Tennis"); 
        $this->taskTwo->setStatus("needsAction"); 

        array_push($this->arrayTask, $this->taskOne);
        array_push($this->arrayTask, $this->taskTwo);

        $this->mockTasklistCollection->setItems($this->arrayTaskList);

    }

   /* 
    public function testFindTaskList()
    {
        $this->mockTasklistResource = $this->getMockBuilder('\Google_Service_Tasks_Tasklists_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTasklistResource 
            ->expects($this->once())
            ->method('listTaskslists')
            ->will($this->returnValue($this->mockTasklistCollection));
        
        $this->mockServiceTask->tasklists = $this->mockTasklistResource;

        $result = $this->googleRepository->findTaskList();
        $this->assertEquals($result[0]->getTasklistId(), 49);
        $this->assertEquals($result[1]->getName(), "IUT");
    }
    */

    public function testFindTaskListById()
    {
        $this->mockTasklistResource = $this->getMockBuilder('\Google_Service_Tasks_Tasklists_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTasklistResource 
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(50))
            ->will($this->returnValue($this->taskListTwo));

        $this->mockServiceTask->tasklists = $this->mockTasklistResource;

        $result = $this->googleRepository->findTaskListById(50);
        $this->assertEquals($result->getTasklistId(), 50);
        $this->assertEquals($result->getName(), "Purchase");
    }

    public function testPersistTaskList()
    {
        $tasklist = new TaskList();
        $tasklist->setTasklistId(12);
        $tasklist->setName("New list");

        $this->mockTasklistResource = $this->getMockBuilder('\Google_Service_Tasks_Tasklists_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTasklistResource 
            ->expects($this->once())
            ->method('insert')
            ->with($this->anything())
            ->will($this->returnValue($tasklist->getTasklistId()));

        $this->mockServiceTask->tasklists = $this->mockTasklistResource;

        $result = $this->googleRepository->persistTaskList($tasklist);
        $this->assertEquals($result, 12);
    }

    public function testUpdateTaskList()
    {
        $tasklist = new TaskList();
        $tasklist->setTasklistId(2345);
        $tasklist->setName("Homework");

        $this->mockTasklistResource = $this->getMockBuilder('\Google_Service_Tasks_Tasklists_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTasklistResource 
            ->expects($this->once())
            ->method('update')
            ->with($this->anything())
            ->will($this->returnValue($tasklist->getTasklistId()));

        $this->mockServiceTask->tasklists = $this->mockTasklistResource;

        $result = $this->googleRepository->updateTaskList($tasklist, 2345);
        $this->assertEquals($result, 2345);
    }

    public function testRemoveTaskList()
    {
        $tasklist = new TaskList();
        $tasklist->setTasklistId(87);
        $tasklist->setName("Do something");

        $this->mockTasklistResource = $this->getMockBuilder('\Google_Service_Tasks_Tasklists_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTasklistResource 
            ->expects($this->once())
            ->method('delete')
            ->with($this->anything())
            ->will($this->returnValue($tasklist->getTasklistId()));

        $this->mockServiceTask->tasklists = $this->mockTasklistResource;

        $result = $this->googleRepository->removeTaskList($tasklist);
        $this->assertEquals($result, 87);
    }
    
    public function testFindTaskById()
    {
        $this->mockTaskResource = $this->getMockBuilder('\Google_Service_Tasks_Tasks_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTaskResource 
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(49), $this->equalTo(3))
            ->will($this->returnValue($this->taskTwo));

        $this->mockServiceTask->tasks = $this->mockTaskResource;

        $result = $this->googleRepository->findTaskById(3, 49);
        $this->assertEquals($result->getTaskId(), 3);
        $this->assertEquals($result->getLibelle(), "Tennis");
    }

    public function testPersistTask()
    {
        $tasklist = new TaskList();
        $tasklist->setTasklistId(555);
        $tasklist->setName("Purchase");

        $task = new Task();
        $task->setTaskId(248);
        $task->setLibelle("Acheter sous-marin");
        $task->setChecked(false);
        $task->setTasklist($tasklist);

        $this->mockTaskResource = $this->getMockBuilder('\Google_Service_Tasks_Tasks_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTaskResource 
            ->expects($this->once())
            ->method('insert')
            ->with($this->anything())
            ->will($this->returnValue($task->getTaskId()));

        $this->mockServiceTask->tasks = $this->mockTaskResource;

        $result = $this->googleRepository->persistTask($task);
        $this->assertEquals($result, 248);
    }


    public function testUpdateTask()
    {
        $tasklist = new TaskList();
        $tasklist->setTasklistId(8332);
        $tasklist->setName("Museum to visit");

        $task = new Task();
        $task->setTaskId(723);
        $task->setLibelle("Reina sofia, Madrid");
        $task->setChecked(true);
        $task->setTasklist($tasklist);

        $this->mockTaskResource = $this->getMockBuilder('\Google_Service_Tasks_Tasks_Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTaskResource 
            ->expects($this->once())
            ->method('update')
            ->with($this->anything())
            ->will($this->returnValue($task->getTaskId()));

        $this->mockServiceTask->tasks = $this->mockTaskResource;

        $result = $this->googleRepository->updateTask($task, 723, 8332);
        $this->assertEquals($result, 723);
    }

}
