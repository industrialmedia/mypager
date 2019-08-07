<?php

namespace Drupal\mypager\Plugin\views\pager;


use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\pager\Full;


/**
 * Views pager plugin.
 *
 * @ViewsPager(
 *  id = "mypager",
 *  title = @Translation("Mypager"),
 *  short_title = @Translation("Mypager"),
 *  help = @Translation("A views plugin which provides mypager."),
 *  theme = "mypager_pager"
 * )
 */
class Mypager extends Full {

  /**
   * {@inheritdoc}
   */
  public function render($input) {
    $full_pager = parent::render($input);
    $element = $full_pager['#element'];
    $full_pager['#theme'] = $this->view->buildThemeFunctions('pager');
    $full_pager['#is_mypager'] = TRUE;
    $full_pager = drupal_render($full_pager);
    $full_pager = str_replace('js-pager__items', '', $full_pager);  // Удаляем возможность аякса
    global $pager_page_array;
    $current = $pager_page_array[$element] + 1;
    $build['mypager'] = [
      '#theme' => $this->themeFunctions(),
      '#options' => $this->options, // $this->options['mypager'],
      '#attached' => [
        'library' => ['mypager/views-mypager'],
      ],
      '#element' => $this->options['id'],
      '#parameters' => $input,
      '#prefix' => '<div class="mypager-wrapper" data-drupal-views-mypager-wrapper data-actives="' . $current . '">',
      '#suffix' => $full_pager . '</div>',
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = parent::defineOptions();
    $options['mypager'] = [
      'contains' => [
        'button_text' => [
          'default' => $this->t('Load More'),
        ],
      ],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function summaryTitle() {
    return $this->formatPlural($this->options['items_per_page'], '@count item', '@count items', ['@count' => $this->options['items_per_page']]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $options = $this->options['mypager'];
    $form['mypager'] = [
      '#title' => $this->t('Mypager Options'),
      '#description' => $this->t('Note: The mypager option overrides and requires the <em>Use AJAX</em> setting for this views display.'),
      '#type' => 'details',
      '#open' => TRUE,
      '#tree' => TRUE,
      '#input' => TRUE,
      '#weight' => -100,
      'button_text' => [
        '#type' => 'textfield',
        '#title' => $this->t('Button Text'),
        '#default_value' => $options['button_text'],
        '#description' => 'Возможный токен для замены: @count',
      ],
    ];
  }

}
