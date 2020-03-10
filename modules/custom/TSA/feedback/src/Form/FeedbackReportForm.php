<?php

namespace Drupal\feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;

/**
 * Defines a form showing the submitted feedbacks.
 */
class FeedbackReportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feedback_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Include the css.
    $form['#attached']['library'] = ['feedback/feedback-block-form'];

   
	 /*$countheader = [
	     'feedbackvalue' => t('Feedback'),
	     'feedbackcount' => t('Count'),
        ];*/
		
	$countheader = [t('Feedback') , t('Count')];
		
	$query = Database::getConnection()->select('feedback_page', 'fd');
	$query->fields('fd');
	$query->condition('fd.feedback', 1);
    $fdyesrow = $query->countQuery()->execute()->fetchField();
	$options_valuecount['Yes'] = $fdyesrow;
	
	$query = Database::getConnection()->select('feedback_page', 'fd');
	$query->fields('fd');
	$query->condition('fd.feedback', 0);
    $fdnorow = $query->countQuery()->execute()->fetchField();
	$options_valuecount['No'] = $fdnorow;
	$options_count = [];
	$options_value = array('Yes' , 'No');
	for($i = 0; $i < count($options_value); $i++){
	   //$options_count[$i]['feedbackvalue'] = $options_value[$i]; 
	   //$options_count[$i]['feedbackcount'] = $options_valuecount[$options_value[$i]]; 
	   $options_count[$i] = array($options_value[$i] , $options_valuecount[$options_value[$i]]);
	}
	
	$form['feedback_feedback_count'] = [
      //'#type' => 'tableselect',
      '#type' => 'table',
      '#header' => $countheader,
      '#rows' => $options_count,
      '#empty' => t('No feedback found'),
    ];
	
	$header = [];
   
      $header['uid'] = [
        'data' => t('User'),
        'field' => 'uid',
      ];
   
      $header['feedback'] = [
        'data' => t('Feedback'),
        'field' => 'feecback',
      ];
    
      
	  $header['page_url'] = [
        'data' => t('Page URL'),
        'field' => 'page_url',
      ];
   
      $header['date'] = [
        'data' => t('Date'),
        'field' => 'timestamp',
      ];
	  
	
	
	//echo"<pre>";print_R($header); 
	//echo"<pre>";print_R($countheader); 
	//echo"<pre>";print_R($options_count); 
	
	//echo"<pre>";print_R($options_count);
    
	
	/*
    $header = [];
   
      $header['uid'] = [
        'data' => t('User'),
        'field' => 'uid',
      ];
   
      $header['feedback'] = [
        'data' => t('Feedback'),
        'field' => 'feecback',
      ];
    
      
	  $header['page_url'] = [
        'data' => t('Page URL'),
        'field' => 'page_url',
      ];
   
      $header['date'] = [
        'data' => t('Date'),
        'field' => 'timestamp',
      ];
	  
	  
   
	
    
    $sort = 'ASC';
    $order = 'timestamp';
    
    $feedbacks = [];
	
	
	 
	//echo"<pre>";print_r($num); die;
    // Build the query.
    $query = Database::getConnection()->select('feedback_page', 'fd');
    $query->fields('fd');
	$query->condition('fd.feedback', 1);
    $query->orderBy($order, $sort);
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender');

    // Fetch all results from the db.
    foreach ($table_sort->execute() as $row) {
      $feedbacks[] = [
        'fid' => $row->fid,
        'uid' => $row->uid,
        'feedback' => $row->feedback,
        'timestamp' => $row->timestamp,
        'page_url' => $row->base_url.$row->path_alias,
      ];
    }
    //echo"<pre>";print_r($feedbacks); die;
    // Build the rows for the table.
    date_default_timezone_set(drupal_get_user_timezone());
    $options_open = [];
    $options_archived = [];

    foreach ($feedbacks as $feedback) {
      $option = [];
      $tmp_user = \Drupal::entityTypeManager()->getStorage('user')->load($feedback['uid']);
      $username = $tmp_user->getDisplayName();
      $option['uid'] = $feedback['uid'] . ' (' . $username . ')';
      $option['feedback'] = ($feedback['faadback']) ? 'Yes' : 'No';
      $option['page_url'] = $feedback['page_url'];
      $option['date'] = format_date($feedback['timestamp'], 'custom', 'd-m-Y').' '.format_date($feedback['timestamp'], 'custom', 'h:i a');
	  $options_open[$feedback['fid']] = $option;
    }
	//echo"<pre>";print_r($options_open); 

    // If the user has permissions to delete feedbacks add that option as well.
    /*if (\Drupal::currentUser()->hasPermission('delete feedback')) {
      $options += [HELPFULNESS_STATUS_DELETED => t('Delete')];
    }*/
    /*$options = array();
    $form['update_action'] = [
      '#type' => 'select',
      '#title' => t('Update options:'),
      '#options' => $options,
      '#required' => TRUE,
    ];

    // Submit Button.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Update'),
    ];
	
	
	


    $form['feedback_feedback_open']['feedback_feedbacks_open_table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options_open,
      '#empty' => t('No new feedback found'),
    ];*/
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    /*$open_fids = array_diff($values['helpfulness_feedbacks_open_table'], ["0"]);
    $archived_fids = array_diff($values['helpfulness_feedbacks_archived_table'], ["0"]);

    if (empty($open_fids) && empty($archived_fids)) {
      drupal_set_message(t('Please select the items you would like to update.'), 'error');
      return;
    }

    $action = $values['update_action'];

    if ($action == HELPFULNESS_STATUS_DELETED) {
      $id_string = implode('-', array_merge($open_fids, $archived_fids));
      $form_state->setRedirect('helpfulness.report_confirm_deletions_form',
        ['idstring' => $id_string]);
    }
    else {
      $this->helpfulnessProcessUpdateAction($action, $open_fids);
      $this->helpfulnessProcessUpdateAction($action, $archived_fids);
      drupal_set_message(t('Your selected items have been updated.'));
    }*/

  }

  /**
   * Implements helpfulnessProcessUpdateAction().
   */
  /*private function feedbackProcessUpdateAction($action, $selected_fids) {
    if (empty($selected_fids)) {
      return;
    }

    // Build the update query and execute.
    $db = Database::getConnection();
    $query = $db->update('feedback_page');
    $query->fields(['status' => $action]);
    $query->condition('fid', $selected_fids, 'IN');
    $query->execute();
  }*/

}
