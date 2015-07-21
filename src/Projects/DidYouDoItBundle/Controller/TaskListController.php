<?php

namespace Projects\DidYouDoItBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Projects\DidYouDoItBundle\Entity\TaskList;
use Projects\DidYouDoItBundle\Form\TaskListType;

/**
 * TaskList controller.
 *
 */
class TaskListController extends Controller
{

    /**
     * Lists all TaskList entities.
     *
     */
    public function getAllAction()
    {
        $persistance = $this->get('persistance');
        try {
            $entities = $persistance->findTaskList();
        } 
        catch (\Google_Service_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        catch (\Google_Auth_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        return $this->render('DidYouDoItBundle:TaskList:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TaskList entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TaskList();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) 
        {
            try {
                $persistance = $this->get('persistance');
                $persistance->persistTaskList($entity);
                $persistance->flush();
                $entities = $persistance->findTaskList();
            } catch (\Google_Service_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }
            catch (\Google_Auth_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }

            return $this->redirect($this->generateUrl('did_you_do_it_get_all_tasklist', array('entities' => $entities)));
        }

        return $this->render('DidYouDoItBundle:TaskList:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new TaskList entity.
     *
     */
    public function newAction()
    {
        $entity = new TaskList();
        $form   = $this->createCreateForm($entity);

        return $this->render('DidYouDoItBundle:TaskList:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TaskList entity.
     *
     */
    public function getOneAction($id)
    {
        $persistance = $this->get('persistance');
        try {
            $entity = $persistance->findTaskListById($id);
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
                'error'      => 'Unable to find TaskList entity.',
            ));
        }

        try {
            $tasks = $persistance->findAllTaskInOneTaskList($entity);
            $deleteForm = $this->createDeleteForm($id);
        } catch (\Google_Service_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }
        catch (\Google_Auth_Exception $e)
        {
            return $this->redirect($this->generateUrl('connection_oauth'));
        }

        return $this->render('DidYouDoItBundle:TaskList:show.html.twig', array(
            'entity'      => $entity,
            'tasks'       => $tasks,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing TaskList entity.
     *
     */
    public function modifyAction(Request $request, $id)
    {
        $persistance = $this->get('persistance');
        try {
            $entity = $persistance->findTaskListById($id);
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
                'error'      => 'Unable to find TaskList entity.',
            ));
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            try {
                $persistance->updateTaskList($entity, $id);
                $persistance->flush();
            } catch (\Google_Service_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }
            catch (\Google_Auth_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }

            return $this->redirect($this->generateUrl('did_you_do_it_modify_tasklist', array('id' => $id)));
        }

        return $this->render('DidYouDoItBundle:TaskList:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TaskList entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $persistance = $this->get('persistance');
            try {
                $entity = $persistance->findTaskListById($id);

                if (!$entity) {
                    return $this->render('DidYouDoItBundle:Error:error.html.twig', array(
                        'error'      => 'Unable to find TaskList entity.',
                    ));
                }
                $persistance->removeTaskList($entity);
                $persistance->flush();
            } catch (\Google_Service_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }
            catch (\Google_Auth_Exception $e)
            {
                return $this->redirect($this->generateUrl('connection_oauth'));
            }


            return $this->redirect($this->generateUrl('did_you_do_it_get_all_tasklist'));
        }
    }

    /**
     * Creates a form to create a TaskList entity.
     *
     * @param TaskList $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TaskList $entity)
    {
        $taskListType = $this->get('TaskListType');
        $form = $this->createForm($taskListType, $entity, array(
            'action' => $this->generateUrl('did_you_do_it_create_tasklist'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing TaskList entity.
     *
     */
    public function editAction($id)
    {
        $persistance = $this->get('persistance');

        try {
            $entity = $persistance->findTaskListById($id);
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
                'error'      => 'Unable to find TaskList entity.',
            ));
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DidYouDoItBundle:TaskList:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a TaskList entity.
     *
     * @param TaskList $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TaskList $entity)
    {
        $taskListType = $this->get('TaskListType');
        $form = $this->createForm($taskListType, $entity, array(
            'action' => $this->generateUrl('did_you_do_it_modify_tasklist', array('id' => $entity->getTasklistId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Creates a form to delete a TaskList entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('did_you_do_it_delete_tasklist', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
