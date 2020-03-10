<?php

namespace Drupal\feedback\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Cache\UncacheableDependencyTrait;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Feedback' block.
 *
 * @Block(
 *   id = "feedback_block",
 *   admin_label = @Translation("Feedback block"),
 * )
 */
class FeedbackBlock extends BlockBase implements BlockPluginInterface {
	  use UncacheableDependencyTrait;

  /**
   * {@inheritdoc}
   */
  public function build() { 
	  \Drupal::service('page_cache_kill_switch')->trigger();
	  $cookies = \Drupal::request()->cookies;
	  $tsacoookie = $cookies->get('tsafeedback');
	  if($tsacoookie == null){
        $form = \Drupal::formBuilder()->getForm('\Drupal\feedback\Form\FeedbackBlockForm');
        return $form;
	  }else{
		return null;
	  }
  }
}
