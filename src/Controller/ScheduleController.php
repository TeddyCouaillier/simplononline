<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/schedule")
 */
class ScheduleController extends AbstractController
{
    /**
     * @Route("/test", name="schedule_index", methods={"GET"})
     */
    public function index(ScheduleRepository $scheduleRepository): Response
    {
        return $this->render('schedule/index.html.twig', [
            'schedules' => $scheduleRepository->findAll(),
        ]);
    }

    /**
     * Calendar view
     * @Route("/calendar", name="schedule_calendar")
     * @return Response
     */
    public function calendar()
    {
        return $this->render('calendar.html.twig');
    }

    /**
     * Create a new schedule
     * @Route("/new", name="schedule_new")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return JsonResponse/Response
     */
    public function new(Request $request, ObjectManager $manager)
    {
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule,  array(
            'action' => $this->generateUrl('schedule_new')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($schedule);
            $manager->flush();

            return $this->redirectToRoute('schedule_calendar');
        }

        if($request->isXmlHttpRequest()){
            $render = $this->render('schedule/_form.html.twig', [
                'form' => $form->createView()
            ]);

            $response = [ "render" => $render->getContent() ];

            return new JsonResponse($response);
        }

        return $this->render('schedule/new.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit schedule when the user moves schedule
     * @Route("/move", name="schedule_move")
     * @param Request            $request
     * @param ObjectManager      $manager
     * @param ScheduleRepository $rep
     * @return JsonResponse
     */
    public function move(Request $request, ObjectManager $manager, ScheduleRepository $rep)
    {
        $id = $request->request->get('id');
        $startDate = new \DateTime($request->request->get('start'));
        $endDate   = new \DateTime($request->request->get('end'));

        $schedule = $rep->find($id);
        $schedule->setBeginAt($startDate);
        $schedule->setEndAt($endDate);

        $manager->persist($schedule);
        $manager->flush();

        return new JsonResponse(['id'=>$id]);
    }

    /**
     * Create schedule when user selects timezone
     * @Route("/createAjax", name="create_ajax")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function createAjax(Request $request, ObjectManager $manager)
    {
        $start    = new \DateTime($request->request->get('start'));
        $end      = new \DateTime($request->request->get('end'));
        $schedule = new Schedule();

        $schedule->setBeginAt($start)
                 ->setEndAt($end)
                 ->setTitle('');

        $manager->persist($schedule);
        $manager->flush();

        $response = [ "id" => $schedule->getId() ];

        return new JsonResponse($response);
    }

    /**
     * Edit a specific schedule in the calendar
     * @Route("/editAjax", name="edit_ajax")
     * @param Request            $request
     * @param ObjectManager      $manager
     * @param ScheduleRepository $rep
     * @return JsonResponse/Response
     */
    public function editAjax(Request $request, ObjectManager $manager, ScheduleRepository $rep)
    {
        $id = $request->isXmlHttpRequest() ? $request->request->get('id') : $request->query->get('id');
        $schedule = $rep->find($id);

        $form = $this->createForm(ScheduleType::class, $schedule, array(
            'action' => $this->generateUrl('edit_ajax',['id' => $schedule->getId()])
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($schedule);
            $manager->flush();

            return $this->redirectToRoute('schedule_calendar');
        }

        $render = $this->render('schedule/_form.html.twig', [
            'form' => $form->createView(),
            'schedule' => $schedule
        ]);

        $response = [ "render" => $render->getContent() ];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}", name="schedule_show", methods={"GET"})
     */
    public function show(Schedule $schedule): Response
    {
        return $this->render('schedule/show.html.twig', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="schedule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Schedule $schedule): Response
    {
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('schedule_index');
        }

        return $this->render('schedule/edit.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a specific schedule
     * @Route("/delete/{id}", name="schedule_delete")
     * @param Schedule      $schedule
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Schedule $schedule, ObjectManager $manager)
    {
        $manager->remove($schedule);
        $manager->flush();

        return $this->redirectToRoute('schedule_calendar');
    }
}
