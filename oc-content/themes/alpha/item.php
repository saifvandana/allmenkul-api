<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php'); ?>
  <link rel="stylesheet" media="print" href="<?php echo osc_current_web_theme_url('css/print.css?v=' . date('YmdHis')); ?>">


  <?php
    $itemviewer = (Params::getParam('itemviewer') == 1 ? 1 : 0);
    $item_extra = alp_item_extra(osc_item_id());

    $location_array = array(osc_item_city(), osc_item_region(), osc_item_country_code());
    $location_array = array_filter($location_array);
    $location = implode(', ', $location_array);


    if(osc_item_user_id() <> 0) {
      $item_user = User::newInstance()->findByPrimaryKey(osc_item_user_id());
      View::newInstance()->_exportVariableToView('user', $item_user);
    } else {
      $item_user = false;
    }

    $user_location_array = array(osc_user_city(), osc_user_region(), osc_user_country(), (osc_user_address() <> '' ? '<br/>' . osc_user_address() : ''));
    $user_location_array = array_filter($user_location_array);
    $user_location = implode(', ', $user_location_array);


    $mobile_found = true;
    $mobile = $item_extra['s_phone'];

    if($mobile == '' && function_exists('bo_mgr_show_mobile')) { $mobile = bo_mgr_show_mobile(); }
    if($mobile == '' && osc_item_user_id() <> 0) { $mobile = $item_user['s_phone_mobile']; }      
    if($mobile == '' && osc_item_user_id() <> 0) { $mobile = $item_user['s_phone_land']; } 
   
    $mobile_login_required = false;

    if(osc_item_show_phone() == 0) {
      $mobile = __('No phone number', 'alpha');
      $mobile_found = false;
    } else if(osc_get_preference('reg_user_can_see_phone', 'osclass') == 1 && !osc_is_web_user_logged_in() && strlen(trim($mobile)) >= 4) {
      $mobile = __('Login to see phone number', 'alpha');
      $mobile_found = true;
      $mobile_login_required = true;
    } else if(trim($mobile) == '' || strlen(trim($mobile)) < 4) { 
      $mobile = __('No phone number', 'alpha');
      $mobile_found = false;
    }  



    $has_cf = false;
    while(osc_has_item_meta()) {
      if(osc_item_meta_value() != '') {
        $has_cf = true;
        break;
      }
    }

    View::newInstance()->_reset('metafields');


    // GET REGISTRATION DATE AND TYPE
    $reg_type = '';
    $reg_has_date = false;

    if($item_user && $item_user['dt_reg_date'] <> '') { 
      $reg_type = alp_smart_date($item_user['dt_reg_date']);
      $reg_has_date = true;
    } else if ($item_user) { 
      $reg_type = __('Registered user', 'alpha');
    } else {
      $reg_type = __('Unregistered user', 'alpha');
    }
  ?>


  <!-- FACEBOOK OPEN GRAPH TAGS -->
  <?php osc_get_item_resources(); ?>
  <meta property="og:title" content="<?php echo osc_esc_html(osc_item_title()); ?>" />
  <?php if(osc_count_item_resources() > 0) { ?><meta property="og:image" content="<?php echo osc_resource_url(); ?>" /><?php } ?>
  <meta property="og:site_name" content="<?php echo osc_esc_html(osc_page_title()); ?>"/>
  <meta property="og:url" content="<?php echo osc_item_url(); ?>" />
  <meta property="og:description" content="<?php echo osc_esc_html(osc_highlight(osc_item_description(), 500)); ?>" />
  <meta property="og:type" content="article" />
  <meta property="og:locale" content="<?php echo osc_current_user_locale(); ?>" />
  <meta property="product:retailer_item_id" content="<?php echo osc_item_id(); ?>" /> 
  <meta property="product:price:amount" content="<?php echo strip_tags(osc_category_price_enabled() ? osc_item_formated_price() : ''); ?>" />
  <?php if(osc_item_price() <> '' and osc_item_price() <> 0) { ?><meta property="product:price:currency" content="<?php echo osc_item_currency(); ?>" /><?php } ?>


  <!-- GOOGLE RICH SNIPPETS -->
  <span itemscope itemtype="http://schema.org/Product">
    <meta itemprop="name" content="<?php echo osc_esc_html(osc_item_title()); ?>" />
    <meta itemprop="description" content="<?php echo osc_esc_html(osc_highlight(osc_item_description(), 500)); ?>" />
    <?php if(osc_count_item_resources() > 0) { ?><meta itemprop="image" content="<?php echo osc_resource_url(); ?>" /><?php } ?>
  </span>
</head>

<body id="body-item" class="page-body<?php if($itemviewer == 1) { ?> itemviewer<?php } ?><?php if(alp_device() <> '') { echo ' dvc-' . alp_device(); } ?>">
  <?php osc_current_web_theme_path('header.php') ; ?>

  <div id="listing" class="inside">
    <?php echo alp_banner('item_top'); ?>

    <!-- HEADER & BASIC DATA -->
    <div class="basic">
      <h1><?php echo osc_item_title(); ?></h1>
      <h2>
        <span><?php echo osc_item_category(); ?></span>

        <?php if(trim($location) <> '') { ?>
          <span class="location"><?php echo $location; ?></span>
        <?php } ?>

        <span class="date" title="<?php echo osc_esc_html(__('Published', 'alpha')); ?> <?php echo osc_esc_html(osc_format_date(osc_item_pub_date())); ?>">
          <?php echo alp_smart_date(osc_item_pub_date()); ?>
        </span>   
      </h2>
    </div>


    <!-- LISTING BODY - LEFT SIDE -->
    <div class="item">

      <?php if(osc_item_is_expired()) { ?>
        <div class="sold-reserved expired">
          <span><?php _e('This listing is expired!', 'alpha'); ?></span>
        </div>
      <?php } ?>

      <?php if($item_extra['i_sold'] > 0) { ?>
        <div class="sold-reserved<?php echo ($item_extra['i_sold'] == 1 ? ' sold' : ' reserved'); ?>">
          <span><?php echo ($item_extra['i_sold'] == 1 ? __('Seller has marked this listing as <strong>SOLD</strong>', 'alpha') : __('Seller has marked this listing as <strong>RESERVED</strong>', 'alpha')); ?></span>
        </div>
      <?php } ?>


      <!-- IMAGE BOX -->
      <?php if(osc_images_enabled_at_items()) { ?> 
        <div id="img">
          <?php osc_get_item_resources(); ?>
          <?php osc_reset_resources(); ?>

          <?php if(osc_count_item_resources() > 0 ) { ?>  
            <ul class="list bx-slider">
              <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                <li>
                  <a href="<?php echo osc_resource_url(); ?>" data-fancybox="gallery">
                    <img src="<?php echo osc_resource_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>"/>
                  </a>
                </li>
              <?php } ?>
            </ul>

            <?php if(osc_count_item_resources() > 1) { ?>
              <div class="item-bx-pager">
                <?php osc_reset_resources(); ?>
                <?php $c = 0; ?>

                <?php for($i = 1;osc_has_item_resources();$i++) { ?>
                  <a data-slide-index="<?php echo $c; ?>" href="" class="navi<?php if($i == 0) { ?> first<?php } ?><?php if($i - 1 == osc_count_item_resources()) { ?> last<?php } ?>">
                    <img src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php _e('Image', 'alpha'); ?> <?php echo $i; ?>"/>
                  </a>

                  <?php $c++; ?>
                <?php } ?>
              </div>
            <?php } ?>

          <?php } else { ?>

            <div class="image-empty"><?php _e('Seller has not uploaded any pictures', 'alpha'); ?></div>

          <?php } ?>
        </div>
      <?php } ?>


      <!-- DESCRIPTION -->
      <div class="data">
        <div class="description">
          <h2><?php _e('Description', 'alpha'); ?></h2>

          <div class="text">
            <?php echo osc_item_description(); ?>
          </div>
        </div>


        <!-- CUSTOM FIELDS -->
        <?php if($has_cf) { ?>
          <div class="custom-fields">
            <h2><?php _e('Attributes', 'alpha'); ?></h2>

            <div class="list">
              <?php while(osc_has_item_meta()) { ?>
                <?php if(osc_item_meta_value() != '') { ?>
                  <div class="field name<?php echo osc_item_meta_name(); ?> value<?php echo osc_esc_html(osc_item_meta_value()); ?>">
                    <span class="name"><?php echo osc_item_meta_name(); ?></span> 
                    <span class="value"><?php echo osc_item_meta_value(); ?></span>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>

          </div>
        <?php } ?>

   
        <!-- PLUGIN HOOK -->
        <div id="plugin-hook">
          <?php osc_run_hook('item_detail', osc_item()); ?>  
        </div>
      </div>


      <?php echo alp_banner('item_description'); ?>




      <!-- COMMENTS-->
      <?php if( osc_comments_enabled()) { ?>
        <div id="comment">
          <h2><?php _e('Comments', 'alpha'); ?></h2>

          <div class="wrap">
            <?php if(osc_item_total_comments() > 0) { ?>
              <?php while(osc_has_item_comments()) { ?>
                <div class="comment">
                  <div class="image">
                    <img src="<?php echo alp_profile_picture(osc_comment_user_id(), 'medium'); ?>" />
                  </div>

                  <div class="info">
                    <h3>
                      <span><?php echo(osc_comment_title() == '' ? __('Comment', 'alpha') : osc_comment_title()); ?> <?php _e('by', 'alpha'); ?> <?php echo (osc_comment_author_name() == '' ? __('Anonymous', 'alpha') : osc_comment_author_name()); ?></span>
                      <span class="date"><?php echo alp_smart_date(osc_comment_pub_date()); ?></span>
                    </h3>

                    <div class="body"><?php echo osc_comment_body(); ?></div>
 
                    <?php if(osc_comment_user_id() && (osc_comment_user_id() == osc_logged_user_id())) { ?>
                      <a rel="nofollow" class="remove" href="<?php echo osc_delete_comment_url(); ?>" title="<?php echo osc_esc_html(__('Delete your comment', 'alpha')); ?>">
                        <i class="fa fa-trash-o"></i> <span class="isDesktop"><?php _e('Delete', 'alpha'); ?></span>
                      </a>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>

              <div class="paginate comment-pagi"><?php echo osc_comments_pagination(); ?></div>

            <?php } else { ?>
              <div class="empty-comment"><?php _e('No comments has been added yet', 'alpha'); ?></div>

            <?php } ?>
            
          </div>


          <?php if(osc_reg_user_post_comments() && osc_is_web_user_logged_in() || !osc_reg_user_post_comments()) { ?>
            <a class="open-form new-comment btn alpBg" href="<?php echo alp_fancy_url('comment'); ?>" data-type="comment"><?php _e('Add a new comment', 'alpha'); ?></a>
          <?php } ?>
        </div>
      <?php } ?>


      <?php alp_related_ads(); ?>

      <?php echo alp_banner('item_bottom'); ?>
    </div>



    <!-- SIDEBAR - RIGHT -->
    <div class="side">
      <?php if($itemviewer == 0) { ?>

        <?php if(function_exists('sp_buttons')) { ?>
          <div class="sms-payments">
            <?php echo sp_buttons(osc_item_id());?>
          </div>
        <?php } ?>


        <?php if(osc_is_web_user_logged_in() && osc_item_user_id() == osc_logged_user_id()) { ?>
          <div class="manage">
            <h2><?php _e('Manage item', 'alpha'); ?></h2>
                
            <div class="tools">
              <a href="<?php echo osc_item_edit_url(); ?>"><span><?php _e('Edit', 'alpha'); ?></span></a>
              <a href="<?php echo osc_item_delete_url(); ?>"" onclick="return confirm('<?php _e('Are you sure you want to delete this listing? This action cannot be undone.', 'alpha'); ?>?')"><span><?php _e('Remove', 'alpha'); ?></span></a>

              <?php if(osc_item_is_inactive()) { ?>
                <a class="activate" target="_blank" href="<?php echo osc_item_activate_url(); ?>"><?php _e('Validate', 'alpha'); ?></a>
              <?php } ?>

            </div>
          </div>
        <?php } ?>


        <div class="data">
          <div class="like">
            <?php if(function_exists('fi_save_favorite')) { echo fi_save_favorite(); } ?>
          </div>

          <?php if(osc_category_price_enabled() && osc_price_enabled_at_items()) { ?>
            <div class="price">
              <span><?php echo osc_item_formated_price(); ?></span>
            </div>
          <?php } ?>

          <div class="map">
            <div class="labs">
              <?php if($item_user !== false && $item_user['b_company'] == 1) { ?>
                <span class="lab box-user"><img src="<?php echo osc_current_web_theme_url('images/shop-small.png'); ?>"/> <?php _e('Professional seller', 'alpha'); ?></span>
              <?php } ?>


              <?php if(osc_item_is_premium()) { ?>
                <div class="lab premium"><?php echo __('Premium', 'alpha'); ?></div>
              <?php } ?>

              <?php if(!in_array(osc_item_category_id(), alp_extra_fields_hide())) { ?>
                <?php if(alp_get_simple_name($item_extra['i_condition'], 'condition') <> '') { ?>
                  <div class="lab condition" title="<?php echo osc_esc_html(__('Item condition', 'alpha')); ?>"><?php echo alp_get_simple_name($item_extra['i_condition'], 'condition'); ?></div>
                <?php } ?>

                <?php if(alp_get_simple_name($item_extra['i_transaction'], 'transaction') <> '') { ?>
                  <div class="lab transaction" title="<?php echo osc_esc_html(__('Transaction', 'alpha')); ?>"><?php echo alp_get_simple_name($item_extra['i_transaction'], 'transaction'); ?></div>
                <?php } ?>
              <?php } ?>
    
            </div>

            <h4><?php echo $location; ?> <?php echo osc_item_address(); ?> <?php echo osc_item_zip(); ?></h4>

            <div class="hook">
              <?php osc_run_hook('location'); ?>
            </div>
          </div>

          <div class="connect">
            <a href="<?php echo alp_fancy_url('contact'); ?>" class="open-form contact btn alpBg" data-type="contact"><?php _e('Message seller', 'alpha'); ?></a>

            <?php if(function_exists('mo_ajax_url')) { ?>
              <a href="#" id="mk-offer" class="make-offer-link alpCl" data-item-id="<?php echo osc_item_id(); ?>" data-item-currency="<?php echo osc_item_currency(); ?>" data-ajax-url="<?php echo mo_ajax_url(); ?>&moAjaxOffer=1&itemId=<?php echo osc_item_id(); ?>"><?php _e('Make offer', 'alpha'); ?></a>
            <?php } ?>

            <?php if($mobile_found) { ?>
              <?php if($mobile_login_required) { ?>
                <a href="<?php echo osc_user_login_url(); ?>" class="mobile login-required" data-phone="" title="<?php echo osc_esc_html(__('Login to show number', 'alpha')); ?>"><?php _e('Login to show number', 'alpha'); ?></a>
              <?php } else { ?>
                <a href="#" class="mobile" data-phone="<?php echo $mobile; ?>" title="<?php echo osc_esc_html(__('Click to show number', 'alpha')); ?>"><?php echo substr($mobile, 0, strlen($mobile) - 4) . 'xxxx'; ?></a>
              <?php } ?>
            <?php } ?>

            <?php if(osc_item_show_email()) { ?>
              <a href="#" class="email" data-email="<?php echo osc_item_contact_email(); ?>" title="<?php echo osc_esc_html(__('Click to show email', 'alpha')); ?>"><?php echo alp_mask_email(osc_item_contact_email()); ?></a>
            <?php } ?>

            <a class="friend open-form" href="<?php echo alp_fancy_url('friend'); ?>" data-type="friend"><?php _e('Recommend to friend', 'alpha'); ?></a>

            <a href="#" class="print"><?php _e('Print listing', 'alpha'); ?></a>

            <?php if (function_exists('show_printpdf')) { ?>
              <a id="print_pdf" class="" target="_blank" href="<?php echo osc_base_url(); ?>oc-content/plugins/printpdf/download.php?item=<?php echo osc_item_id(); ?>"><?php echo osc_esc_html(__('Download in PDF', 'veronika')); ?></a>
            <?php } ?>

          </div>


          <div class="item-share">
            <?php osc_reset_resources(); ?>
              <a class="whatsapp" title="<?php echo osc_esc_html(__('Message on Whatsapp', 'alpha')); ?>" target="_blank" href="https://wa.me/<?php echo "90".$mobile?>?text=<?php echo osc_item_url(); ?>%0A%0AMerhaba%20İlan%20Hakkında%20Bilgi%20Alabilir%20Miyim?"><i class="fa fa-whatsapp"></i></a>
              <a class="facebook" title="<?php echo osc_esc_html(__('Share on Facebook', 'alpha')); ?>" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo osc_item_url(); ?>"><i class="fa fa-facebook"></i></a>
            <a class="twitter" title="<?php echo osc_esc_html(__('Share on Twitter', 'alpha')); ?>" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo urlencode(osc_item_title()); ?>&url=<?php echo urlencode(osc_item_url()); ?>"><i class="fa fa-twitter"></i></a> 
            <a class="pinterest" title="<?php echo osc_esc_html(__('Share on Pinterest', 'alpha')); ?>" target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo osc_item_url(); ?>&media=<?php echo osc_resource_url(); ?>&description=<?php echo htmlspecialchars(osc_item_title()); ?>"><i class="fa fa-pinterest"></i></a> 
          </div>
        </div>

        <div class="stats">
          <span><?php echo osc_item_views(); ?> <?php echo (osc_item_views() == 1 ? __('hit', 'alpha') : __('hits', 'alpha')); ?></span>
          <span><?php echo osc_item_total_comments(); ?> <?php echo (osc_item_total_comments() == 1 ? __('comment', 'alpha') : __('comments', 'alpha')); ?></span>
          <span class="right"><?php _e('ID', 'alpha'); ?> #<?php echo osc_item_id(); ?></span>
        </div>

        <div class="user">
          <h2><?php _e('About the seller', 'alpha'); ?></h2>

          <div class="line line1">
            <div class="user-img">
              <img src="<?php echo alp_profile_picture(osc_item_user_id(), 'medium'); ?>" alt="<?php echo osc_item_contact_name(); ?>" />
            </div>

            <div class="user-name<?php if(function_exists('ur_show_rating_link') && osc_item_user_id() > 0) { ?> ur-active<?php } ?>">
              <strong><?php echo osc_item_contact_name(); ?></strong>
   
              <?php if(function_exists('show_feedback_overall') && osc_item_user_id() > 0 && osc_get_preference('bo_mgr_allow_feedback', 'plugin-bo_mgr') == 1) { ?>
                <span class="bo-fdb"><a href="#" id="leave_feedback"><?php echo show_feedback_overall(); ?></a></span>
              <?php } ?>

              <?php if(function_exists('ur_show_rating_link') && osc_item_user_id() > 0) { ?>
                <span class="ur-fdb">
                  <span class="strs"><?php echo ur_show_rating_stars(); ?></span>
                  <span class="lnk"><?php echo ur_add_rating_link(); ?></span>
                </span>
              <?php } ?>


              <span>
                <?php echo $reg_type; ?>
              </span>

            </div>
          </div>

          <div class="line line2">
            <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>">
              <span><?php _e('Dashboard', 'alpha'); ?></span>
            </a>

            <?php if(function_exists('seller_post')) { ?>
              <?php seller_post(); ?>
            <?php } ?>
          </div>
        </div>

        <?php if(function_exists('show_qrcode')) { ?>
          <div class="qr-code noselect">
            <?php show_qrcode(); ?>
          </div>
        <?php } ?>


        <?php echo alp_banner('item_sidebar'); ?>

      <?php } else { ?>
        <div class="map-viewer"><?php osc_run_hook('location') ; ?></div>

      <?php } ?>

    </div>
  </div>

  <div class="mobile-navi isMobile">
    <div class="top">
      <div class="left">
        <?php if($mobile_login_required) { ?>
          <a href="<?php echo osc_user_login_url(); ?>" class="alpBg call login-required" data-phone="">
            <span><?php _e('Call', 'alpha'); ?></span>
            <strong>
              <?php _e('Login to show number', 'alpha'); ?> 
            </strong>
          </a>
        <?php } else { ?>
          <a href="#" class="alpBg call" data-phone="<?php echo $mobile; ?>">
            <span><?php _e('Call', 'alpha'); ?></span>
            <strong>
              <?php 
                if($mobile_found) { 
                  echo substr($mobile, 0, strlen($mobile) - 4) . 'xxxx';
                } else {
                  echo $mobile;
                }
              ?> 
            </strong>
          </a>
        <?php } ?>
      </div>

      <div class="middle">
        <div class="img">
          <img src="<?php echo alp_profile_picture(osc_item_user_id(), 'medium'); ?>" />
        </div>
      </div>

      <div class="right">
        <a href="<?php echo alp_fancy_url('contact'); ?>" class="alpBg open-form" data-type="contact">
          <span><?php _e('Message', 'alpha'); ?></span>
          <strong><?php echo osc_item_contact_name(); ?></strong>
        </a>
      </div>
    </div>

    <div class="bottom">
      <div class="line name<?php if(osc_item_user_id() > 0) { ?> logged<?php } ?>"><strong><?php echo osc_item_contact_name(); ?></strong></div>

      <?php if(osc_item_user_id() > 0) { ?>
        <div class="line dash"><a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>"><?php _e('Dashboard', 'alpha'); ?></a></div>
      <?php } ?>

      <?php if($user_location <> '') { ?>
        <div class="line loc"><?php echo $user_location; ?></div>
      <?php } ?>

      <div class="line reg"><?php echo ($reg_has_date ? __('Registered', 'alpha') . ' ' : ''); ?><?php echo $reg_type; ?></div>


      <?php if(function_exists('show_feedback_overall') && osc_item_user_id() > 0 && osc_get_preference('bo_mgr_allow_feedback', 'plugin-bo_mgr') == 1) { ?>
        <div class="line bo-fdb"><a href="#" id="leave_feedback"><?php echo show_feedback_overall(); ?></a></div>
      <?php } ?>

      <?php if(function_exists('ur_show_rating_link') && osc_item_user_id() > 0) { ?>
        <div class="line ur-fdb">
          <span class="strs"><?php echo ur_show_rating_stars(); ?></span>
          <span class="lnk"><?php echo ur_add_rating_link(); ?></span>
        </span>
      <?php } ?>

      <?php if(osc_user_info() <> '') { ?>
        <div class="line about"><?php echo osc_highlight(osc_user_info(), 500); ?></div>
      <?php } ?>
    </div>
  </div>



  <script type="text/javascript">
    $(document).ready(function(){

      // SHOW PHONE NUMBER
      $('body').on('click', '.connect .mobile', function(e) {
        if($(this).attr('href') == '#' && !$(this).hasClass('login-required')) {
          e.preventDefault()

          var phoneNumber = $(this).attr('data-phone');
          $(this).text(phoneNumber);
          $(this).attr('href', 'tel:' + phoneNumber);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to call', 'alpha')); ?>');
        }        
      });


      // SHOW PHONE NUMBER - MOBILE BAR
      $('body').on('click', '.mobile-navi .left a', function(e) {
        if($(this).attr('href') == '#' && !$(this).hasClass('login-required')) {
          e.preventDefault()

          var phoneNumber = $(this).attr('data-phone');
          $(this).find('strong').text(phoneNumber);
          $(this).attr('href', 'tel:' + phoneNumber);
        }        
      });


      // SHOW EMAIL
      $('body').on('click', '.email', function(e) {
        if($(this).attr('href') == '#') {
          e.preventDefault()

          var email = $(this).attr('data-email');
          $(this).text(email);
          $(this).attr('href', 'mailto:' + email);
          $(this).attr('title', '<?php echo osc_esc_js(__('Click to send mail', 'alpha')); ?>');
        }        
      });


    });
  </script>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>				