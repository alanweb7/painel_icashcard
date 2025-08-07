<?php defined('BASEPATH') or exit('No direct script access allowed');

init_head();
echo '<script src="' . poly_utilities_common_helper::get_assets('modules/poly_utilities/dist/assets/js/lib/vuejs/3.4.27/vue.global.prod.js') . '"></script>';
echo '<link rel="stylesheet" href="' . poly_utilities_common_helper::get_assets('modules/poly_utilities/dist/assets/css/lib/select2/select2.min.css') . '">';
?>

<div id="polyApp" v-cloak>
  <div id="wrapper">
    <!-- Loading -->
    <div class="poly-loader" :class="{'hide': !isProccessing }">
      <div :class="{'poly-loading': isProccessing }">&nbsp;</div>
    </div>
    <!-- Loading -->

    <div class="content poly-data-container">
      <div class="row poly_utilities_settings">
        <?php if ($this->session->flashdata('debug')) {
        ?>
          <div class="col-lg-12">
            <div class="alert alert-warning">
              <?php echo $this->session->flashdata('debug'); ?>
            </div>
          </div>
        <?php
        } ?>
        <div class="col-md-12 poly-banners-main-tabs">
          <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked poly-banners-main-tabs__tabs">
            <?php
            $i = 0;
            foreach ($tabs as $group) { ?>
              <li class="<?php echo ($group['is_display'] == false ? "hide " : "") ?>poly_utilities-group-<?php echo html_escape($group['slug']); ?><?php echo ($i === 0) ? ' active' : '' ?>">
                <a href="<?php echo admin_url('poly_utilities/banners?group=' . $group['slug']); ?>" data-group="<?php echo html_escape($group['slug']); ?>">
                  <i class="<?php echo $group['icon'] ?: 'fa-regular fa-circle-question'; ?> menu-icon"></i>
                  <?php echo html_escape($group['name']); ?>

                  <?php if (isset($group['badge'], $group['badge']['value']) && !empty($group['badge'])) { ?>
                    <span class="badge pull-right
        <?= isset($group['badge']['type']) && $group['badge']['type'] != '' ? "bg-{$group['badge']['type']}" : 'bg-info' ?>" <?= (isset($group['badge']['type']) && $group['badge']['type'] == '') || isset($group['badge']['color']) ? "style='background-color: {$group['badge']['color']}'" : '' ?>>
                      <?= $group['badge']['value'] ?>
                    </span>
                  <?php } ?>
                </a>
              </li>
            <?php $i++;
            }
            ?>
          </ul>
        </div>
        <div class="col-md-12">
          <div class="panel_s">
            <div class="panel-body">
              <?php $this->load->view($tab['view']) ?>
              <?php
              if ($current_tab !== 'settings') {
                switch ($current_tab) {
                  case 'announcements':
                    $this->load->view('poly_utilities/banners/announcements_add');
                    break;
                  default:
                    $this->load->view('poly_utilities/banners/add');
                }
              }
              ?>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<?php
echo '<script src="' . poly_utilities_common_helper::get_assets('modules/poly_utilities/dist/assets/js/lib/sortable/1.15.0/sortable.min.js') . '"></script>';
echo '<script src="' . poly_utilities_common_helper::get_assets('modules/poly_utilities/dist/assets/js/lib/select2/select2.min.js') . '"></script>';
