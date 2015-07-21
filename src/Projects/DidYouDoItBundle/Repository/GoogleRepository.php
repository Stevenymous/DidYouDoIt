<?php 

namespace Projects\DidYouDoItBundle\Repository;

use Projects\DidYouDoItBundle\Entity\Task;
use Projects\DidYouDoItBundle\Entity\TaskList;

/**
 * Service allow application to persist data in data base with Google
 */
class GoogleRepository implements RepositoryManager
{

    /** Wrapper of Google_Client
     */
    private $googleClient;

    /** Session for gogole client
     */
    private $session;

    /** Instance of Google_Service_Tasks
     */
    private $serviceTask;

    /** Constructor of GoogleRepository with 2 parameters
     * @param doctrineEntityManager EntityManager from Doctrine service
     * @param googleClient Client of Google Taks API
     */
    public function __construct($doctrineEntityManager, $googleClient, $session)
    {
        $this->googleClient = $googleClient->getGoogleClient();
        $this->googleClient->setScopes ( \Google_Service_Tasks::TASKS );
        $this->googleClient->setAccessType('offline');
        $this->session = $session;
        
        $this->setServiceTask(new \Google_Service_Tasks($this->googleClient));
    }

    /**
     * Accessor to serviceTask in write
     */
    public function setServiceTask($serviceTask)
    {
        $this->serviceTask = $serviceTask;
    }

    public function authenticate($code)
    {
        try {
            $accessToken = $this->googleClient->authenticate($code);
            $this->session->set('access_token', $accessToken);
            $res = json_decode($accessToken);
            if (array_key_exists('refresh_token', $res))
            {
                $this->session->set('refresh_token', $res->refresh_token);
            }
            else
            {
                throw new \Exception("refresh_token not exist");
            }
        } catch (\Exception $e)
        {
            throw $e;
        }
    }

    public function createAuthUrl()
    {
        return $this->googleClient->createAuthUrl();
    }

    /** Allow access token for each request, and if the access token is expired, it is refresh
     */
    private function setAccessToken()
    {
        $this->googleClient->setAccessToken($this->session->get('access_token'));

        if ($this->googleClient->isAccessTokenExpired()) {
            $this->googleClient->refreshToken($this->session->get('refresh_token'));
            $this->session->set('access_token', $this->googleClient->getAccessToken());
        }
    } 

    /** Find all task lists with their tasks
     */
    public function findTaskList()
    {
        $this->setAccessToken();
        $taskListFromGoogle = $this->serviceTask->tasklists->listTasklists();
        $arrayTaskList = [];
        foreach ($taskListFromGoogle->getItems() as $taskList)
        {
            $taskListCreate = new TaskList();
            $taskListCreate->setName($taskList->getTitle());
            $taskListCreate->setTasklistId($taskList->getId());
            array_push($arrayTaskList, $taskListCreate);
        }
        return $arrayTaskList; 
    }

    /** Find one task list by his id with their tasks
     */
    public function findTaskListById($id)
    {
        $this->setAccessToken();
        $taskListByIdFromGoogle = $this->serviceTask->tasklists->get($id);
        $taskListCreate = new TaskList();
        $taskListCreate->setName($taskListByIdFromGoogle->getTitle());
        $taskListCreate->setTasklistId($taskListByIdFromGoogle->getId());
        return $taskListCreate;
    }

    /** Create a new tasklist
     */
    public function persistTaskList(TaskList $tasklist)
    {
        $this->setAccessToken();
        $googleTasklist = new \Google_Service_Tasks_TaskList(); 
        $googleTasklist->setId($tasklist->getTasklistId());
        $googleTasklist->setTitle($tasklist->getName());
        $result = $this->serviceTask->tasklists->insert($googleTasklist);
        return $result;
    }

    /** modify the task list
     */
    public function updateTaskList(TaskList $tasklist, $id)
    {
        $this->setAccessToken();
        $googleTasklist = new \Google_Service_Tasks_TaskList(); 
        $googleTasklist->setId($tasklist->getTasklistId());
        $googleTasklist->setTitle($tasklist->getName());
        $result = $this->serviceTask->tasklists->update($id, $googleTasklist);
        return $result;
    }

    /** delete a task list
     */
    public function removeTaskList(TaskList $tasklist)
    {
        $this->setAccessToken();
        $result = $this->serviceTask->tasklists->delete($tasklist->getTasklistId());
        return $result;
    }

    /** Find a Task by Id
     */
    public function findTaskById($idTask, $idTasklist)
    {
        $this->setAccessToken();
        $taskByIdFromGoogle = $this->serviceTask->tasks->get($idTasklist, $idTask);
        $taskCreated = new Task();
        $taskCreated->setTaskId($taskByIdFromGoogle->getId()); 
        $taskCreated->setLibelle($taskByIdFromGoogle->getTitle()); 
        $taskCreated->setChecked($this->transformGetStatusInChecked($taskByIdFromGoogle->getStatus()));
        return $taskCreated;
    }

    /** Add new task in a tasklist 
     */
    public function persistTask(Task $task)
    {
        $this->setAccessToken();
        $googleTask = new \Google_Service_Tasks_Task();
        $googleTask->setStatus("needsAction");
        $googleTask->setTitle($task->getLibelle());
        $result = $this->serviceTask->tasks->insert($task->getTasklist()->getTasklistId(), $googleTask);

        return $result;
    }

    /** modify label of task or if is checked
     * parameters must be optionnal
     */
    public function updateTask(Task $task, $idTask, $idTasklist)
    {
        $this->setAccessToken();
        $googleTask = new \Google_Service_Tasks_Task(); 
        $googleTask->setId($task->getTaskId());
        $googleTask->setTitle($task->getLibelle());
        $googleTask->setStatus($this->transformGetCheckedinStatus($task->getChecked()));
        $result = $this->serviceTask->tasks->update($idTasklist, $idTask, $googleTask);
        return $result;
    }

    /** Find all task in the tasklist
     */
    public function findAllTaskInOneTaskList(TaskList $tasklist)
    {
        $this->setAccessToken();
        $tasks = $this->serviceTask->tasks->listTasks($tasklist->getTasklistid());
        $tasksEntities = array();
        foreach ($tasks->getItems() as $task)
        {
            $taskCreated = new Task();
            $taskCreated->setTaskId($task->getId()); 
            $taskCreated->setLibelle($task->getTitle()); 
            $taskCreated->setChecked($this->transformGetStatusInChecked($task->getStatus())); 
            array_push($tasksEntities, $taskCreated);
        }
        return $tasksEntities;
    }

    /**
     * Allow persistance to flush
     */
    public function flush()
    {
        return null;
    }

    /**
     * Transform the result of getStatus() to a boolean value
     */
    private function transformGetStatusInChecked($status)
    {
        if ($status === "needsAction")
        {
            return false;
        }
        else if ($status === "completed")
        {
            return true;
        }
        return false;
    }

    /**
     * Transform the result of getChecked() to a status value
     */
    private function transformGetCheckedinStatus($checked)
    {
        if ($checked)
        {
            return "completed";
        }
        else if (!$checked)
        {
            return "needsAction";
        }
        return "needsAction";
    }
}
