<?php


use Drupal\Component\Utility\Xss;
use Drupal\Core\Datetime\DrupalDateTime;   

/**
 * Implement Event Calender view theme preprocess function.
 *
 * @param array $variables
 *   Template variables.
 */

//function template_preprocess_views_view_unformatted__tsa_event_calendar(array &$variables) {
/*function template_preprocess_views_view_unformatted__events_calendar(array &$variables) {
       global $base_url;
       $view = $variables['view'];
	   //kint($view);die;
	   $i = 0;
       //kint($view->field);
	   $options = ['absolute' => TRUE];
	   $events = array();
	   foreach ($view->result as $row) { 
	   //kint($row);
		  $current_entity =  $row->_entity;
		  $entity_id      =  $current_entity->id();
		  $title          =  $current_entity->get('title')->value;
		  $location       =  $current_entity->get('field_event_location')->value;
		  $startdate      =  $current_entity->get('field_event_start_date')->value;
		  $enddate        =  $current_entity->get('field_event_end_date')->value; 
	      $events[$i]['id']       =  $entity_id;
	      $events[$i]['title']    =  $title;
	      $events[$i]['url']      =  $base_url.'/node/'.$entity_id;
	      $events[$i]['location'] =  $location;
	      $events[$i]['start']    =  strtotime($startdate) * 1000;
		  $events[$i]['class']    =  "event-important";
		  $events[$i]['end']      =  strtotime($enddate) * 1000;
		  $i++;
	   } 
	   
	 //$events = array();
	 $arrevents = json_encode($events);
	 //echo"<pre>";print_R($arrevents);
     $curdate   = date('Y-m-d'); 	 
     $variables['#attached']['library'][] = 'tsa_events_calendar/eventscalendar';
	 $variables['#attached']['drupalSettings'] = [
      'tsaevents' => $arrevents,
	  'startdate' => $curdate, 
    ];
}*/


function template_preprocess_views_view_unformatted__events_view__calendar_view(array &$variables) {
       global $base_url;   
       $view = $variables['view'];
	   //kint($view->result);
	   $resultitem = array();
	   foreach($view->result as $row){
		    $eventresults[] = $row->_item;			
	   }
	   $i = 0;
	   $events = array();
	   foreach ($eventresults as $key => $eventresult) {
          //kint($eventresult);
		  $originalobject = $eventresult->getOriginalObject();
		  $current_entity = $originalobject->getEntity();
		  //kint($current_entity); die;
		  $entity_id      = isset($current_entity->get('nid')->value) ? $current_entity->get('nid')->value : null;
		  $title          = isset($current_entity->get('title')->value) ? $current_entity->get('title')->value : null;
		  $location       = isset($current_entity->get('field_event_location')->value) ? $current_entity->get('field_event_location')->value : null;
		  $startdate      = isset($current_entity->get('field_event_start_date')->value) ? $current_entity->get('field_event_start_date')->value : null; 
		  $enddate        = isset($current_entity->get('field_event_end_date')->value) ? $current_entity->get('field_event_end_date')->value : null;
		  if($entity_id && $startdate){
			  $events[$i]['id']       =  $entity_id;
			  $events[$i]['title']    =  $title;
			  $events[$i]['url']      =  $base_url.'/node/'.$entity_id;
			  $events[$i]['location'] =  $location;
			  $events[$i]['start']    =  strtotime($startdate) * 1000;
			  $events[$i]['class']    =  "event-important";
			  $events[$i]['end']      =  strtotime($enddate) * 1000;
			  //$events[$i]['end']    =  $events[$i]['start'] + 1000;
		  $i++;
		  }
	   } 
	   //echo"<pre>";print_R($events);
	   //$events = array();
	   $arrevents = json_encode($events);
	   //echo"<pre>";print_R($arrevents);
       $curdate   = date('Y-m-d'); 	 
       $variables['#attached']['library'][] = 'tsa_events_calendar/eventscalendar';
	   $variables['#attached']['drupalSettings'] = [
         'tsaevents' => $arrevents,
	     'startdate' => $curdate, 
       ];
}
