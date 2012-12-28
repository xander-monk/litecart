<?php
  if (!isset($_GET['page'])) $_GET['page'] = 1;
  
  if (!empty($_POST['enable']) || !empty($_POST['disable'])) {
  
    if (!empty($_POST['currencies'])) {
      foreach ($_POST['currencies'] as $key => $value) $_POST['currencies'][$key] = $system->database->input($value);
      $system->database->query(
        "update ". DB_TABLE_CURRENCIES ."
        set status = '". ((!empty($_POST['enable'])) ? 1 : 0) ."'
        where id in ('". implode("', '", $_POST['currencies']) ."');"
      );
    }
    
    header('Location: '. $system->document->link());
    exit;
  }
?>
<div style="float: right;"><a class="button" href="<?php echo $system->document->href_link('', array('doc' => 'edit_currency.php'), true); ?>"><?php echo $system->language->translate('title_add_new_currency', 'Add New Currency'); ?></a></div>
<h1 style="margin-top: 0px;"><img src="<?php echo WS_DIR_ADMIN . $_GET['app'] .'.app/icon.png'; ?>" width="32" height="32" style="vertical-align: middle;" style="margin-right: 10px;" /><?php echo $system->language->translate('title_currencies', 'Currencies'); ?></h1>

<?php echo $system->functions->form_draw_form_begin('currencies_form', 'post'); ?>
<table width="100%" align="center" class="dataTable">
  <tr class="header">
    <th><?php echo $system->functions->form_draw_checkbox('checkbox_toggle', '', ''); ?></th>
    <th nowrap="nowrap" align="left"><?php echo $system->language->translate('title_id', 'ID'); ?></th>
    <th nowrap="nowrap" align="center"><?php echo $system->language->translate('title_code', 'Code'); ?></th>
    <th nowrap="nowrap" align="left" width="100%"><?php echo $system->language->translate('title_name', 'Name'); ?></th>
    <th nowrap="nowrap" align="center"><?php echo $system->language->translate('title_value', 'Value'); ?></th>
    <th nowrap="nowrap" align="center"><?php echo $system->language->translate('title_prefix', 'Prefix'); ?></th>
    <th nowrap="nowrap" align="center"><?php echo $system->language->translate('title_suffix', 'Suffix'); ?></th>
    <th nowrap="nowrap" align="center"><?php echo $system->language->translate('title_priority', 'Priority'); ?></th>
    <th>&nbsp;</th>
  </tr>
<?php

  $currencies_query = $system->database->query(
    "select * from ". DB_TABLE_CURRENCIES ."
    order by status desc, priority, name;"
  );

  if ($system->database->num_rows($currencies_query) > 0) {
    
    if ($_GET['page'] > 1) $system->database->seek($currencies_query, ($system->settings->get('data_table_rows_per_page', 20) * ($_GET['page']-1)));
    
    $page_items = 0;
    while ($currency = $system->database->fetch($currencies_query)) {
    
      if (!isset($rowclass) || $rowclass == 'even') {
        $rowclass = 'odd';
      } else {
        $rowclass = 'even';
      }
    
?>
  <tr class="<?php echo $rowclass . ($currency['status'] ? false : ' semi-transparent'); ?>">
    <td nowrap="nowrap"><img src="<?php echo WS_DIR_IMAGES .'icons/16x16/'. (!empty($currency['status']) ? 'on.png' : 'off.png') ?>" width="16" height="16" align="absbottom" /> <?php echo $system->functions->form_draw_checkbox('currencies['. $currency['id'] .']', $currency['id']); ?></td>
    <td align="left"><?php echo $currency['id']; ?></td>
    <td align="left" nowrap="nowrap"><?php echo $currency['code']; ?></td>
    <td align="left"><?php echo $currency['name']; ?></td>
    <td align="right"><?php echo $currency['value']; ?></td>
    <td align="center"><?php echo $currency['prefix']; ?></td>
    <td align="center"><?php echo $currency['suffix']; ?></td>
    <td align="right"><?php echo $currency['priority']; ?></td>
    <td align="right"><a href="<?php echo $system->document->href_link('', array('doc' => 'edit_currency.php', 'currency_code' => $currency['code']), true); ?>"><img src="<?php echo WS_DIR_IMAGES . 'icons/16x16/edit.png'; ?>" width="16" height="16" alt="<?php echo $system->language->translate('title_edit', 'Edit'); ?>" title="<?php echo $system->language->translate('title_edit', 'Edit'); ?>" /></a></td>
  </tr>
<?php
      if (++$page_items == $system->settings->get('data_table_rows_per_page', 20)) break;
    }
  }
?>
  <tr class="footer">
    <td colspan="9" align="left"><?php echo $system->language->translate('title_currencies', 'Currencies'); ?>: <?php echo $system->database->num_rows($currencies_query); ?></td>
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
  echo $system->functions->draw_pagination(ceil($system->database->num_rows($currencies_query)/$system->settings->get('data_table_rows_per_page', 20)));
  
?>