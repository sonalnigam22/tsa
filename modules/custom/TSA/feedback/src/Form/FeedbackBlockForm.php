<?php

namespace Drupal\feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines a block form to leave user feedback.
 */
class FeedbackBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feedback_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Include the css.

    $form['feedback_option'] = [
      '#type' => 'radios',
	  '#title' => t('What this page helpful to you?'),
      '#options' => [1 => t('Yes'), 0 => t('No')],
	  '#prefix' => '<div id="response-result">',
	  '#postfix' => '</div>',
      '#ajax' => array(
         'callback' => '::saveUserFeedback',
         'effect' => 'fade',
         'event' => 'change',
          'progress' => array(
             'type' => 'throbber',
             'message' => NULL,
          ),
		),
		
    ];

    return $form;
  }
  
	  
	/**
	 * {@inheritdoc}
	 */
	public function validateForm(array &$form, FormStateInterface $form_state) {}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm(array &$form, FormStateInterface $form_state) {}

 
    public function saveUserFeedback(array &$form, FormStateInterface $form_state) {
		$ajaxresponse  = new AjaxResponse();
		$feedback  = $form_state->getValue('feedback_option');
		$user_id   = \Drupal::currentUser()->id();


		// Form values.
		$fields = [
		  'uid'      => $user_id,
		  'feedback' => $feedback,
		  'system_path' => \Drupal::service('path.current')->getPath(),
		  'path_alias' => Url::fromRoute("<current>")->toString(),
		  'base_url' => $GLOBALS['base_url'],
		  'timestamp' => REQUEST_TIME,
		];

		// Get the database connection.
		$db = Database::getConnection();

		// Insert the data to the feedback_page table.
		$db->insert('feedback_page')->fields($fields)->execute();
		setcookie('tsafeedback', '1', time() + 120, '/');
		$ajaxresponse->addCommand(
			new HtmlCommand(
				'#response-result',
				'<div class="feedback_message">Thank you for your valuable feedback </div>')
			);
			return $ajaxresponse;
	  }
}
