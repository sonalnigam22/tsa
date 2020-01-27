<?php

namespace Drupal\tsa_events\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\tsa_events\ParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\Unicode;

class EventsUploadForm extends FormBase {

  /**
   * The entity type manager service.
  */
  protected $entityTypeManager;

  /**
   * The entity field manager service.
  */
  protected $entityFieldManager;

  /**
   * The entity bundle info service.
  */
  protected $entityBundleInfo;

  /**
   * The parser service.
  */
  protected $parser;

  /**
   * The renderer service.
  */
  protected $renderer;
  
  /**
   * Entity Type Variable.
  */
  protected $entity_type;
  
  /**
   * Entity Type Bundle Variable.
  */
  protected $entity_type_bundle;
  
  

  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_bundle_info, ParserInterface $parser, RendererInterface $renderer) 
  {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityBundleInfo = $entity_bundle_info;
    $this->parser = $parser;
    $this->renderer = $renderer;
	$this->entity_type = 'node';
	$this->entity_type_bundle = 'job_events';
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('tsa_events.parser'),
      $container->get('renderer')
    );
  }

  /**
   *  Event Csv Upload Form Id 
   */
  public function getFormId() {
    return 'events_upload_form';
  }

  /**
   * Build Event Csv Upload Form
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	global $base_url;
	$sampleFile = 'events-sample.csv';
    $form['events_upload'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'events-upload',
      ],
    ];

    $form['events_upload']['csvfile'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Choose CSV file'),
      '#required' => TRUE,
      '#autoupload' => TRUE,
      '#upload_validators' => ['file_validate_extensions' => ['csv']],
      '#weight' => 10,
    ];
	
	/*$form['events_upload']['samplefilelink'] = [
      '#type' => 'markup',
	  '#value'=> '<a class="button button--primary" href="' . $base_url . '/sites/default/files/' . $sampleFile . '">Click here to download Sample CSV</a>',
    ];
	
	
	$form['events_upload']['#markup'] = '<div id="event_fields_wrapper"><a class="button button--primary" href="' . $base_url . '/sites/default/files/' . $sampleFile . '">Click here to download Sample CSV</a></div>';*/

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

   /**
    * Get entity type fields.
   */
  protected function getEntityTypeFields(string $entity_type, string $entity_type_bundle = NULL) {
    $fields = [];
    $entity_fields = $this->entityFieldManager->getFieldDefinitions($entity_type, $entity_type_bundle);
    foreach ($entity_fields as $entity_field) {
      $fields['fields'][] = $entity_field->getName();
      $fields['type'][$entity_field->getName()] = $entity_field->getType();
	  //$fields['type'][]    = $entity_field->getType();
      //$fields['setting'][] = $entity_field->getSettings();

      if ($entity_field->isRequired()) {
        $fields['required'][] = $entity_field->getName();
      }
    }
    return $fields;
  }

   /**
     * Get entity missing fields.
   */
  protected function getEntityTypeMissingFields(string $entity_type , array $required, array $csv) { 
	$entity_definition = $this->entityTypeManager->getDefinition($entity_type);
    if ($entity_definition->hasKey('bundle')) {
      unset($required[array_search($entity_definition->getKey('bundle'), $required)]);
    }
    $csv_fields = [];
	
    foreach ($csv[0] as $csv_row) {
	   $field = 'field_event_'.str_replace(' ','_',strtolower(trim($csv_row))); 	
	   if($field == 'field_event_event_name'){
	      $field = 'title';
	   }
       $csv_fields[] = $field;
    }
	//echo"<pre>";print_R($csv_fields); die;
    $csv_fields = array_values(array_unique($csv_fields));
    return array_diff($required, $csv_fields);
  }
   
   /**
     * Formating Event Csv Data
   */
  
  protected function createNodeData(array $csv_parse) {
  //protected function createNodeData($entity_fields , array $csv_parse) {
	    $return = array();
    	if ($csv_parse && is_array($csv_parse)) {
          $csv_fields = $csv_parse[0];
          unset($csv_parse[0]);
			  foreach ($csv_parse as $index => $data) {  
				foreach ($data as $key => $content) {
				  if ($content && isset($csv_fields[$key])){
                    $content = Unicode::convertToUtf8($content, mb_detect_encoding($content));					  
				    $field   = 'field_event_'.str_replace(' ','_',strtolower($csv_fields[$key])); 
					
					switch ($field) {
						case 'field_event_added_to_calendar':
						     if(trim($content)){
							  $content = date('Y-m-d' , strtotime($content)); 
							  $return['content'][$index][$field] = $content;
					         }else{
							  $return['content'][$index][$field] = '';
							 }
							  break;
							  
						case 'field_event_event_name':
							  $field = 'title';
							  $return['content'][$index][$field] = $content;
							  break;
							  
						case 'field_event_open_to_people':
							  $return['content'][$index][$field] = strtolower($content);
							  break;  
							 
						case 'field_event_event_date':
						      $ArrDate = explode('-' , $content);
							  if(count($ArrDate) == 1){
							      $startdate = date('Y-m-d' , strtotime($ArrDate[0]));
							      $enddate   = date('Y-m-d' , strtotime($ArrDate[0]));
							  }else if(count($ArrDate) > 1){
								  $startdate = date('Y-m-d' , strtotime($ArrDate[0]));
							      $enddate   = date('Y-m-d' , strtotime($ArrDate[1]));
							  }
							  
							  $field = 'field_event_start_date';
							  $return['content'][$index][$field] = $startdate;
							  $field = 'field_event_end_date';
							  $return['content'][$index][$field] = $enddate; 
							  break;
							  
						default:
							  $return['content'][$index][$field] = $content;
							  break;
					}
				  }
				}
		  }
	 }
	 //echo"<pre>";print_R($return); die;
	 return $return;
  }

   /**
   * Action On Form Submission
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	  //drupal_flush_all_caches();
      $csvfile   		  = current($form_state->getValue('csvfile'));
      $csv_parse          = $this->parser->getCsvById($csvfile, ',');
	  //echo"<pre>";print_R($csv_parse); die;
      $entity_fields      = $this->getEntityTypeFields($this->entity_type, $this->entity_type_bundle);
	  //echo"<pre>";print_R($entity_fields); die;
      if ($required = $this->getEntityTypeMissingFields($this->entity_type , $entity_fields['required'], $csv_parse)) {
		  $render = [
			'#theme' => 'item_list',
			'#items' => $required,
		  ];

		  $this->messenger()->addError($this->t('Your CSV has missing required fields: @fields', ['@fields' => $this->renderer->render($render)]));
		  return true;
       }
      
	    $result = $this->createNodeData($csv_parse);
	    //$result = $this->createNodeData($entity_fields , $csv_parse);
		//echo"<pre>";print_R($result); die;
		if (!empty($result)) {
			$added = 0;
			foreach ($result['content'] as $key => $data) { 
			  $data['type']   = 'job_events';
			  $entity_storage = $this->entityTypeManager->getStorage('node');
			  try {
				 
				  $entity = $this->entityTypeManager->getStorage('node')->create($data);
				  if ($entity->save()) {
					$added++;
				  }
			  } catch (\Exception $e) {
			  }
			}
		  	$message = '';
			$message = $this->t('@count_added Events added', ['@count_added' => isset($added) ? $added : 0]);
			$this->messenger()->addMessage($message);
			//$form_state->setRedirect('system.admin_content');
			$form_state->setRedirect('view.tsa_events.page_1');
		}else{
		    $this->messenger()->addError($this->t('Your CSV is either empty or not properly generated'));
		}
  }
  
  
  

}
