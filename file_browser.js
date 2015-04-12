$(document).ready(function() {
  $('.upload_div').hide();
  $('.newfolder_div').hide();
  $('.edit_div').hide();
  $('a.folder_new_icon').click(function() {
    $(this).next('.newfolder_div').toggle();
  });
  $('a.arrow_up_icon').click(function() {
    $(this).next('.upload_div').toggle();
  });
  $('a.edit_icon').click(function() {
    $(this).next('.edit_div').toggle();
  });

  $('li.folder').click(function() {
  	$(this).next('ul.folderlist').toggle();
    $(this).toggleClass('plus');
  });

  $( "ul.toplist li:odd" ).css( "background-color", "#ffffff" )
});
