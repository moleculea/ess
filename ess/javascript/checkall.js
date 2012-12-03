$(function () {
	$('#checkall').click(function () {
		$(this).parents('fieldset:eq(0)').find(':infobox').attr('checked', this.checked);
	});
});