/**
 * For fast migrations interface
 */
$(function () {

	$(".save-profile-process").click(function () {
		var currentAction = $('.action-container.active');
		var form = $('.action-container.active form');
		form.append('<input type="hidden" name="save_profile" value="Y">');
		form.append('<input type="hidden" name="action" value="' + currentAction.attr('data-action-id') + '">');
		var submit_button = $(this);
		submit_button.addClass('disable');
		$.ajax({
			method: 'post',
			url: '/bitrix/admin/acrit_cleanmaster_processor.php',
			data: form.serialize(),
			success: function (data) {
				try {
					var obj = JSON.parse(data);
					if (obj.result == 'OK' && obj.action == 'process') {
						currentAction.find('form').first().html(obj.DATA);
					}
				} catch (e) {
				}
				submit_button.removeClass('disable');
			}
		});
	});

	$(".dyn-url").live('click', function(){
		var _btn = this;
		if ($(_btn).data('clicked') == true) {
			return false;
		}

		$(_btn).fadeTo(0, 0.2).data('clicked', true);
		$.ajax({
			method: 'get',
			url: $(_btn).attr('href'),
			success: function (data) {
				try {
					$(_btn).fadeTo(0, 1).data('clicked', false);
					$(_btn).parent().append( '<div class="dyn-url-mess">' + data + '</div>');
					window.setTimeout(function () {
						$(".dyn-url-mess", $(_btn).parent()).remove();
					}, 2000);
				} catch (e) {
				}
			}
		});

		return false;
	});

}); //\\end doc ready
