<?php
queue_js_file('element-sets');
queue_js_file('userprofiles');

$head = array('title' => __('Edit Profile Type'));
echo head($head);
?>

<?php include_once(USER_PROFILES_DIR . '/views/admin/form.php'); ?>

<?php echo foot(); ?>