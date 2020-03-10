<?php

namespace Drupal\feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

/**
 * Defines a form for the report download.
 */
class FeedbackReportDownloadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feedback_report_download_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Download'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    // Get the status from the dropdown.
    $requested_status = $values['status'];

    // Build the query to retrieve the feedbacks.
    // Build the query.
    $query = Database::getConnection()->select('feedback_page', 'fd');
    $query->fields('fd');
    $query->orderBy('timestamp', 'ASC');

    // Header for the output file.
    $csv_output .= t('"User ID",');
    $csv_output .= t('"Feedback",');
    $csv_output .= t('"Page URL",');
    $csv_output .= t('"Time"');
    $csv_output .= "\n";

    // Add the data from all requested feedbacks.
    foreach ($query->execute() as $row) {
      if ($row->uid == 0) {
        $username = t('Anonymous');
      }
      else {
        $tmp_user = \Drupal::entityTypeManager()->getStorage('user')->load($row->uid);
        $username = $tmp_user->getDisplayName();
      }
      $csv_output .= '"' . $row->uid . ' (\'' . $username . '\')",';

      // Helpfulnes Rating.
      if ($row->feedback) {
        $csv_output .= t('"Yes",');
      }
      else {
        $csv_output .= t('"No",');
      }

      // Path information.
      $csv_output .= '"' . $row->base_url .$row->path_alias .'",';
      // Time of submission.
      $csv_output .= '"' . format_date($row->timestamp, 'custom', "d-m-Y H:i-s") . '",';

      // That should be everything for this submission.
      $csv_output .= "\n";
    }// End foreach

    // Build the filename and start the download.
    $prefix = t('feedbackreport');
    $filename = $prefix . "_" . format_date(time(), 'custom', "Y-m-d_H-i-s");
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: filename=" . $filename . ".csv");
    print $csv_output;

    exit();
  }

}
