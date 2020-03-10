<?php

namespace Drupal\tsa_events_calendar\Controller;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\File\FileSystem;

/**
 * An example controller.
 */
class CalendarController extends ControllerBase {

  protected $file;

  protected $entity;

  protected $currentUser;

  public $request;

  /**
   * Constructor.
   */
  public function __construct(EntityStorageInterface $entityStorage, AccountProxy $currentuser, RequestStack $request, FileSystem $fileStorage) {
    $this->entity = $entityStorage;
    $this->currentUser = $currentuser;
    $this->request = $request;
    $this->file = $fileStorage;
  }

  /**
   * Create dependency injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('node'),
      $container->get('current_user'),
      $container->get('request_stack'),
      $container->get('file_system')
    );

  }

  /**
   *
   * Project https://www.drupal.org/project/file_download.
   *
   */
  public function icsdownload($nid) {

    // Load the node.
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if (empty($node)) {
      return FALSE;
    }
	
    $start_date = $node->field_event_start_date->date->format(
      'Y-m-d H:i:s'
    );
    $end_date = $node->field_event_end_date->date->format(
      'Y-m-d H:i:s'
    );
  //echo $start_date; die;
    // Get Host.
    $host = \Drupal::request()->getHost();

    // 1. Create a Calendar object.
    $vCalendar = new Calendar($host);

    // 2. Create an Event object.
    $vEvent = new Event();

    // 3. Add your information to the Event.
    $vEvent
      ->setDtStart(new \DateTime($start_date))
      ->setDtEnd(new \DateTime($end_date))
      ->setSummary($node->getTitle());

    // 4. Add Event to Calendar.
    $vCalendar->addComponent($vEvent);

    // 5. Send output.
    $filename = 'cal-' . $nid . '.ics';
    $uri = 'public://' . $filename;
    $content = $vCalendar->render();
    $file = file_save_data($content, $uri, FILE_EXISTS_REPLACE);
    if (empty($file)) {
      return new Response(
        'Simple ICS Error, Please contact the System Administrator'
      );
    }

    $mimetype = 'text/calendar';
    $scheme = 'public';
    $parts = explode('://', $uri);
    $file_directory = \Drupal::service('file_system')->realpath(
      $scheme . "://"
    );
    $filepath = $file_directory . '/' . $parts[1];
    $filename = $file->getFilename();

    // File doesn't exist
    // This may occur if the download path is used outside of a formatter
    // and the file path is wrong or file is gone.
    if (!file_exists($filepath)) {
      throw new NotFoundHttpException();
    }

    $headers = [
      'Content-Type' => $mimetype,
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Content-Length' => $file->getSize(),
      'Content-Transfer-Encoding' => 'binary',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
      'Accept-Ranges' => 'bytes',
    ];

    return new BinaryFileResponse($uri, 200, $headers, $scheme !== 'private');
  }

}