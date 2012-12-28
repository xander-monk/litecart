<?php
  if (!isset($_GET['page'])) $_GET['page'] = 1;
  
  if (!empty($_POST['enable']) || !empty($_POST['disable'])) {
  
    if (!empty($_POST['countries'])) {
      foreach ($_POST['countries'] as $key => $value) $_POST['countries'][$key] = $system->database->input($value);
      $system->database->query(
        "update ". DB_TABLE_COUNTRIES ."
        set status = '". ((!empty($_POST['enable'])) ? 1 : 0) ."'
        where id in ('". implode("', '", $_POST['countries']) ."');"
      );
    }
    
    header('Location: '. $system->document->link());
    exit;
  }
?>
<div style="float: right;"><a class="button" href="<?php echo $system->document->href_link('', array('doc' => 'edit_country.php'), true); ?>"><?php echo $system->language->translate('title_add_new_country', 'Add New Country'); ?></a></div>
<h1 style="margin-top: 0px;"><img src="<?php echo WS_DIR_ADMIN . $_GET['app'] .'.app/icon.png'; ?>" width="32" height="32" style="vertical-align: middle;" style="margin-right: 10px;" /><?php echo $system->language->translate('title_countries', 'Countries'); ?></h1>

<?php echo $system->functions->form_draw_form_begin('countries_form', 'post'); ?>
<table width="100%" align="center" class="dataTable">
  <tr class="header">
    <th><?php echo $system->functions->form_draw_checkbox('checkbox_toggle', '', ''); ?></th>
    <th nowrap="nowrap" align="left"><?php echo $system->language->translate('title_id', 'ID'); ?></th>
    <th nowrap="nowrap" align="left">ISO-2</th>
    <th nowrap="nowrap" align="left">ISO-3</th>
    <th nowrap="nowrap" align="left" width="100%"><?php echo $system->language->translate('title_name', 'Name'); ?></th>
    <th nowrap="nowrap" align="left"><?php echo $system->language->translate('title_zones', 'Zones'); ?></th>
    <th>&nbsp;</th>
  </tr>
<?php

  $countries_query = $system->database->query(
    "select * from ". DB_TABLE_COUNTRIES ."
    order by status desc, name asc;"
  );

  if ($system->database->num_rows($countries_query) > 0) {
    
  // Jump to data for current page
    if ($_GET['page'] > 1) $system->database->seek($countries_query, ($system->settings->get('data_table_rows_per_page', 20) * ($_GET['page']-1)));
    
    $page_items = 0;
    while ($country = $system->database->fetch($countries_query)) {
    
      if (!isset($rowclass) || $rowclass == 'even') {
        $rowclass = 'odd';
      } else {
        $rowclass = 'even';
      }
?>
  <tr class="<?=$rowclass?>"<?php echo $country['status'] ? false : ' style="color: #999;"'; ?>>
    <td nowrap="nowrap"><img src="<?php echo WS_DIR_IMAGES .'icons/16x16/'. (!empty($country['status']) ? 'on.png' : 'off.png') ?>" width="16" height="16" align="absbottom" /> <?php echo $system->functions->form_draw_checkbox('countries['. $country['id'] .']', $country['id']); ?></td>
    <td align="left"><?php echo $country['id']; ?></td>
    <td align="left" nowrap="nowrap"><?php echo $country['iso_code_2']; ?></td>
    <td align="left" nowrap="nowrap"><?php echo $country['iso_code_3']; ?></td>
    <td align="left"><?php echo $country['name']; ?></td>
    <td align="left"><?php echo $system->database->num_rows($system->database->query("select id from ". DB_TABLE_ZONES ." where country_code = '". $system->database->input($country['iso_code_2']) ."'")); ?></td>
    <td align="right"><a href="<?php echo $system->document->href_link('', array('doc' => 'edit_country.php', 'country_code' => $country['iso_code_2']), true); ?>"><img src="<?php echo WS_DIR_IMAGES . 'icons/16x16/edit.png'; ?>" width="16" height="16" alt="<?php echo $system->language->translate('title_edit', 'Edit'); ?>" title="<?php echo $system->language->translate('title_edit', 'Edit'); ?>" /></a></td>
  </tr>
<?php
      if (++$page_items == $system->settings->get('data_table_rows_per_page', 20)) break;
    }
  }
?>
  <tr class="footer">
    <td colspan="7" align="left"><?php echo $system->language->translate('title_countries', 'Countries'); ?>: <?php echo $system->database->num_rows($countries_query); ?></td>
  </tr>
</table>

<script type="text/javascript">
  $(".dataTable input[name='checkbox_toggle']").click(function() {
    $(this).closest("form").find(":checkbox").each(function() {
      $(this).attr('checked', !$(this).attr('checked'));
    });
    $(".dataTable input[name='checkbox_toggle']").attr("checked", true);
  });

  $('.dataTable tr').click(function(event) {
    if ($(event.target).is('input:checkbox')) return;
    if ($(event.target).is('a *')) return;
    if ($(event.target).is('th')) return;
    $(this).find('input:checkbox').trigger('click');
  });
</script>

<p><?php echo $system->functions->form_draw_button('enable', $system->language->translate('title_enable', 'Enable'), 'submit'); ?> <?php echo $system->functions->form_draw_button('disable', $system->language->translate('title_disable', 'Disable'), 'submit'); ?></p>

<?php
  echo $system->functions->form_draw_form_end();
  
// Display page links
  echo $system->functions->draw_pagination(ceil($system->database->num_rows($countries_query)/$system->settings->get('data_table_rows_per_page', 20)));
  
?>