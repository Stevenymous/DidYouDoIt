<?php

namespace Projects\DidYouDoItBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Projects\DidYouDoItBundle\Entity\Task;
use Projects\DidYouDoItBundle\Form\TaskType;

/**
 * Task controller.
 *
 */
class TaskController extends Controller
{

    /**
     * Creates a new Task entity.
     *
     */
    public function createAction(Request $request, $idTaskList)
    {
        $persistance = $this->get('persistance');
        try {
            $entityTaskList = $persistance->findTaskListById($idTaskList);
        } catch (\Google_Service_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        catch (\Google_Auth_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        $entity = new Task();
        $entity->setChecked(false);
        $entity->setTasklist($entityTaskList);

        $form = $this->createCreateForm($entity, $idTaskList);
        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $persistance->persistTask($entity);
                $persistance->flush();
            } catch (\Google_Service_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }
            catch (\Google_Auth_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }

            return $this->redirect($this->generateUrl('did_you_do_it_get_one_tasklist', array('id' => $idTaskList)));
        }

        return $this->render('DidYouDoItBundle:Task:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Task entity.
     *
     */

    public function newAction($idTaskList)
    {
        $entity = new Task();
        $form = $this->createCreateForm($entity, $idTaskList);

        return $this->render('DidYouDoItBundle:Task:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Task $entity, $idTaskList)
    {
        $taskType = $this->get('TaskType');
        $taskType->setCreateMode(true);
        $form = $this->createForm($taskType, $entity, array(
            'action' => $this->generateUrl('did_you_do_it_create_task', array('idTaskList' => $idTaskList)),
            'method' => 'POST',

        ));
        return $form;
    }

    /**
     * Edits an existing Task entity.
     *
     */
    public function modifyAction(Request $request, $idTask, $idTaskList)
    {
        $persistance = $this->get('persistance');
        try {
            $entity = $persistance->findTaskById($idTask, $idTaskList);
        } catch (\Google_Service_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        catch (\Google_Auth_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }

        if (!$entity) {
            return $this->render('DidYouDoItBundle:Error:error.html.twig', array(
                'error'      => 'Unable to find Task entity.',
            ));
        }

        $editForm = $this->createEditForm($entity, $idTaskList);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            try {
                $persistance->updateTask($entity, $idTask, $idTaskList);
                $persistance->flush();
            } catch (\Google_Service_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }
            catch (\Google_Auth_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }

            return $this->redirect($this->generateUrl('did_you_do_it_get_one_tasklist', array('id' => $idTaskList)));
        }

        return $this->render('DidYouDoItBundle:Task:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     */
    public function editAction($idTaskList, $idTask)
    {
        $persistance = $this->get('persistance');
        try {
            $entity = $persistance->findTaskById($idTask, $idTaskList);
        } catch (\Google_Service_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        catch (\Google_Auth_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }

        if (!$entity) {
            return $this->render('DidYouDoItBundle:Error:error.html.twig', array(
                'error'      => 'Unable to find Task entity.',
            ));
        }

        $editForm = $this->createEditForm($entity, $idTaskList);

        return $this->render('DidYouDoItBundle:Task:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Task $entity, $idTaskList)
    {
        $taskType = $this->get('TaskType');
        $taskType->setCreateMode(false);
        $form = $this->createForm($taskType, $entity, array(
            'action' => $this->generateUrl('did_you_do_it_modify_task_from_tasklist', array('idTask' => $entity->getTaskId(), 'idTaskList' => $idTaskList)),
            'method' => 'PUT',
        ));
        return $form;
    }
}
