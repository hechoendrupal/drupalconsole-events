<?php

/**
 * @file
 * Contains \Drupal\events\Controller\EventController.
 */

namespace Drupal\events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class EventController.
 *
 * @package Drupal\events\Controller
 */
class EventController extends ControllerBase {

  /**
   * Page.
   *
   * @return string
   *   Return page
   */
  public function page() {
    $events = $this->getEvents();
    return [
        '#type' => 'theme',
        '#theme' => 'events_list',
        '#events' => $events,
    ];
  }

  public function api()
  {
    $events = $this->getEvents();
    return new JsonResponse($events);
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface[]
   */
  private function getEvents()
  {
    $now = new \DateTime('now');
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'event');
    $query->condition('field_event_end_date', $now->format('Y-m-d'), '>=');
    $nids = $query->execute();

    $eventList = \Drupal::entityManager()->getStorage('node')->loadMultiple($nids);

    $events = [];
    foreach($eventList as $event){
      $attendeeList = $event->field_event_attendee->referencedEntities();
      $attendees = [];
      foreach($attendeeList as $attendee){
        $attendees[] =  sprintf('@%s', $attendee->getUsername());
      }
      $endDate =  new \DateTime($event->field_event_end_date->value);
      $startDate =  new \DateTime($event->field_event_start_date->value);
      $events[] = [
        'title' => $event->label(),
        'link' => $event->field_event_page->value,
        'attendees' => $attendees,
        'lat' => $event->field_event_latitude->value,
        'lng' => $event->field_event_longitude->value,
        'title' => $event->label(),
        'details' => sprintf(
          '%s - %s',
          $startDate->format('M d'),
          $endDate->format('M d')
        ),
        'img' => '/themes/custom/drupalconsole/assets/dist/images/mini-druprompt.png'
      ];
    }

    return $events;
  }

}
