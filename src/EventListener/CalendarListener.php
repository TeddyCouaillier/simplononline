<?php

namespace App\EventListener;

use CalendarBundle\Entity\Event;
use App\Repository\ScheduleRepository;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarListener
{
    private $scheduleRepository;
    private $router;

    public function __construct(ScheduleRepository $scheduleRepository, UrlGeneratorInterface $router)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->router = $router;
    }

    public function load(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();

        $schedules = $this->scheduleRepository
            ->createQueryBuilder('s')
            ->where('s.beginAt BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach($schedules as $schedule){
            $scheduleEvent = new Event(
                $schedule->getTitle(),
                $schedule->getBeginAt(),
                $schedule->getEndAt()
            );
            $color     = $schedule->getImportant() ? '#DC3D24' : '#FFFFFF';
            $textColor = $schedule->getImportant() ? '#FFFFFF' : '#000000';
            $scheduleEvent->setOptions([
                'backgroundColor' => $color,
                'borderColor'     => 'rgba(0,0,0,.25)',
                'textColor'       => $textColor,
                'classNames'      => 'shadow-small rounded p-2',
                'id'              => $schedule->getId()
            ]);

            $calendar->addEvent($scheduleEvent);
        }
    }
}