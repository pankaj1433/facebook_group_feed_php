<?php

namespace Drupal\facebook_feed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'FacebookFeedBlock' block.
 *
 * @Block(
 *  id = "facebook_feed_block",
 *  admin_label = @Translation("Facebook feed block"),
 * )
 */
class FacebookFeedBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'page_id' => $this->t(''),
      'access_token' => $this->t(''),
      'show_socials' => TRUE,
      'limit' => 10,
    ] + parent::defaultConfiguration();
 }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['feed_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Feed settings'),
      '#weight' => '5',
    ];

    $form['feed_settings']['page_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page ID'),
      '#description' => $this->t('ID of the page'),
      '#default_value' => $this->configuration['page_id'],
      '#maxlength' => 64,
      '#size' => 15,
      '#required' => TRUE,
    ];

    $form['feed_settings']['page_id_info'] = [
      '#type' => 'details',
      '#title' => $this->t('What is my page ID?'),
      '#weight' => '6',
    ];
    $form['feed_settings']['page_id_info']['summary'] = [
      '#markup' => '<p>If you have a Facebook <b>page</b> with a URL like this: <code>https://www.facebook.com/your_page_name</code> then the Page ID is just <b>your_page_name</b>.</p>',
    ];

    $form['feed_settings']['access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('(optional) Access token'),
      '#description' => $this->t('Access token needed to deal with the Facebook API'),
      '#default_value' => $this->configuration['access_token'],
      '#maxlength' => 64,
      '#size' => 50,
      '#weight' => '7',
    ];

    $form['feed_settings']['access_token_info'] = [
      '#type' => 'details',
      '#title' => $this->t('What is an access token?'),
      '#weight' => '8',
    ];
    $form['feed_settings']['access_token_info']['summary'] = [
      '#markup' => '<p>A Facebook Access Token is not required to use this module, but we recommend it so that you are not reliant on the token built into the module.</p>'
        . '<p>If you have your own token then you can enter it here.</p>'
        . '<p>To get your own Access Token you can follow these step-by-step instructions.</p>',
    ];

    $form['feed_settings']['show_socials'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show social media stats'),
      '#description' => $this->t('Whether the number of likes, comments and shares of each post should be shown.'),
      '#default_value' => $this->configuration['show_socials'],
      '#weight' => '9',
    ];

    $form['feed_settings']['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Posts limit'),
      '#description' => $this->t('The maximum number of posts that will be fetched.'),
      '#default_value' => $this->configuration['limit'],
      '#min' => 0,
      '#max' => 100,
      '#step' => 1,
      '#size' => 3,
      '#weight' => '10',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // kint($form_state, '$form_state');
    $this->configuration['page_id'] = $form_state->getValue('feed_settings')['page_id'];
    $this->configuration['access_token'] = $form_state->getValue('feed_settings')['access_token'];
    $this->configuration['show_socials'] = $form_state->getValue('feed_settings')['show_socials'];
    $this->configuration['limit'] = $form_state->getValue('feed_settings')['limit'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    try {
      $posts = $this->getPosts();
    }
    catch (\Exception $e) {
      $error_msg = '<p>Sorry, there was a problem fetching posts from Facebook:</p>'
        . '<pre>' . $e->getMessage() . '</pre>'
        . '<p>Try checking that the page ID you provided is correct.</p>';
      return ['#markup' => $error_msg];
    }

    if (empty($posts)) {
      $error_msg = '<p>Sorry, no posts were found</p>';
      return ['#markup' => $error_msg];
    }

    $build = [
      '#prefix' => '<div class="facebook_feed">',
      '#suffix' => '</div>',
    ];
    foreach ($posts as $index => $post) {
      // kint($post, '$post');
      $build[$post->id] = $this->themePost($post);
    }

    $build['#attached']['library'][] = 'facebook_feed/display';
    $build['#attached']['library'][] = 'facebook_feed/font_awesome';

    return $build;
  }



  /**
   * Fetches a list of page posts using Facebook's Graph API.
   *
   * @return
   *   An array of objects containing post data.
   */
  public function getPosts() {

    $page_id = $this->configuration['page_id'];
    // kint($this->configuration['page_id'], 'config: page_id');
    if (! $page_id) {
      throw new Exception("Please edit the Facebook feed block and provide a page ID.", 1);
    }
    
    // Which type of posts should be returned
    $feedType = 'feed';

    $access_token = $this->getAccessToken();
    if (! $access_token) {
      throw new Exception("Please edit the Facebook feed block and provide an access token.", 1);
    }

    $post_fields = [
      'id',
      'created_time',
      'message',
      'picture',
      'link',
      'comments',
      'likes',
      'shares',
    ];

    $url = "https://graph.facebook.com/"
      . $page_id . "/"
      . $feedType
      . "?summary=true"
      . "&limit=" . $this->configuration['limit']
      . "&access_token=" . $access_token
      . '&fields=' . implode(',', $post_fields)
    ;

    $posts = [];

    $response = \Drupal::httpClient()->get($url, [
      'headers' => array('Accept' => 'text/plain'),
    ]);
    $posts = json_decode((string) $response->getBody())->data;

    return $posts;
  }



  function getAccessToken() {
    $access_token = $this->configuration['access_token'];

    if ($access_token) {
      return $access_token;
    }
    // kint('Not using provided access token');
  
    // Regular tokens
    $access_token_array = array(
        '214840262228845|jDMpRKuUA6pE50zkcLI_n0O_xo8',
        '109107172826653|2ZWWn9b2kGF4LD3IWdgvFSV5Icw',
        '1089043857827104|sQP6VAF9GYWw63F6hoo5ZbkmbL4',
        '559167130910609|_k3Jp7zVjgcJYHaPEppyxBAbpJs',
        '1710591165888924|Ng5pfmT-qoYtvvcJ1cz8vJJxJvc',
        '994360207285429|lL1a1xxcWYASdw0Vr_qwQw8NZAM',
        '783129931822943|RDyZgqMwI51LNDhU9EYxx2JK5kA',
        '480939705434761|joaCCxWk05Ik4t4tli7Mzvg0rt8'
    );
    // FQL tokens
    $access_token_array_fql = array(
        '300694180076642|-cozSG1L4topnAqQOwaIEpy4Ufk',
        '439271626171835|-V79s0TIUVsjj_5lgc6ydVvaFZ8',
        '188877464498533|gObD45qMCG-uE9WGVt3-djx-6Sw',
        '636437039752698|Tt-zXlDy-Nu4CCkNteGfcUe65ow',
        '1448491852049169|eUTjw_pIVoPzC1R1pxVQhmtFqQ0'
    );

    return $access_token_array[rand(0, 7)];
  }



  /**
   * Creates a themable array of post data.
   *
   * @param $post
   *   Object containing post data from a call to Facebook's Graph API.
   *
   * @return
   *   A renderable array.
   */
  public function themePost($post) {
    // kint($post, '$post');
    $post_themed = [
      '#theme' => 'facebook_post',
      '#id' => $post->id,
      '#created_time' => $post->created_time,
      '#message' => $post->message,
      '#picture' => $post->picture,
      '#link' => $post->link,
      '#show_socials' => $this->configuration['show_socials'],
      '#num_likes' => 0,
      '#num_comments' => 0,
      '#num_shares' => 0,
    ];
    if (property_exists($post, 'likes')) {
      $post_themed['#num_likes'] = count($post->likes->data);
    }
    if (property_exists($post, 'comments')) {
      $post_themed['#num_comments'] = count($post->comments->data);
    }
    if (property_exists($post, 'shares')) {
      $post_themed['#num_shares'] = $post->shares->count;
    }
    return $post_themed;
  }

}
