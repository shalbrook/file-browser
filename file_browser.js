$(document).ready(function() {
  $('.upload_div').hide();
  $('.newfolder_div').hide();
  $('a.folder_new_icon').click(function() {
    $(this).next('.newfolder_div').toggle();
  });
  $('a.arrow_up_icon').click(function() {
    $(this).next('.upload_div').toggle();
  });
});
