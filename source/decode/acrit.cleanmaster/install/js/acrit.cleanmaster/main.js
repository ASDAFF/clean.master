/**
 * acrit.cleanmaster main scripts
 */
jQuery(document).ready(function () {

	$(document).on('click', 'a#action_20_delete', function () {
		var form = $('.action-container[data-action-id=20] form').first();
		form.append('<input type="hidden" name="delete_components" value="y">').append('<input type="hidden" name="action" value="20">');
		$.ajax({
			method: 'POST',
			url: '/bitrix/admin/acrit_cleanmaster_processor.php',
			data: form.serialize(),
			success: function (data) {
				try {
					var obj = JSON.parse(data);
					if (obj.result == 'OK' && obj.action == 'delete_components') {
						$('.action-container[data-action-id=20] form').html(obj.DATA);
					}
				} catch (e) {
				}
			}
		});
	});
	$(document).on('click', 'a#action_5_delete', function () {
		var currentAction = $('.action-container.active');
		$.ajax({
			method: 'post',
			url: '/bitrix/admin/acrit_cleanmaster_processor.php',
			data: {action: 5, upload_delete_tmp: 'y', 'delete_resize_cache': $(this).data('delete_resize_cache')},
			success: function (data) {
				try {
					var obj = JSON.parse(data);
					if (obj.result == 'OK' && obj.action == 'upload_delete_tmp') {
						currentAction.find('form').first().html(obj.DATA);
					}
				} catch (e) {
				}
			}
		});
	});
	$('.action-container[data-action-id="1"] input[type="checkbox"]').change(function () {
		var currentAction = $('.action-container[data-action-id="1"]');
		var form = currentAction.find('form').first();
		form.append('<input type="hidden" name="get_iblock" value="Y">');
		form.append('<input type="hidden" name="action" value="1">');
		$.ajax({
			method: 'post',
			url: '/bitrix/admin/acrit_cleanmaster_processor.php',
			data: form.serialize(),
			success: function (data) {
				try {
					var obj = JSON.parse(data);
					if (obj.result == 'OK' && obj.action == 'get_iblock') {
						currentAction.find('.deleted-iblocks').first().html(obj.DATA);
					}
				} catch (e) {
				}
			}
		});
	});
	$(document).on('click', '.action-process', function () {
		var currentAction = $('.action-container.active');
		var form = $('.action-container.active form');
		form.append('<input type="hidden" name="clear" value="Y">');
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
						//if (currentAction.find('.info-area').length <= 0) {
						//    currentAction.append('<div class="info-area"></div>');
						//}
						//currentAction.find('.info-area').first().html(obj.DATA);
						currentAction.find('form').first().html(obj.DATA);
					}
				} catch (e) {
				}
				submit_button.removeClass('disable');
			}
		});
	});
	$('.clean-action-wrapper a.action-container-next').click(function () {
		var currentAction = $('.action-container.active');
		var newActionId = $(this).attr('data-action-id');
		var newAction = $('.action-container[data-action-id="' + newActionId + '"]');
		var prev_button = $('.clean-action-wrapper a.action-container-prev');

		currentAction.hide().removeClass('active');
		prev_button.text(action_list[currentAction.attr('data-action-id')]).attr('data-action-id', selected_action_list[newActionId]['prev']);
		$(this).text(action_list[selected_action_list[newActionId]['next']]).attr('data-action-id', selected_action_list[newActionId]['next']);
		newAction.show().addClass('active');
		if (isNaN(parseInt(selected_action_list[newActionId]['next']))) {
			$(this).parent().addClass('hide');
		}
		if (prev_button.parent().hasClass('hide')) {
			prev_button.parent().removeClass('hide');
		}
	});
	$('.clean-action-wrapper a.action-container-prev').click(function () {
		var currentAction = $('.action-container.active');
		var newActionId = $(this).attr('data-action-id');
		var newAction = $('.action-container[data-action-id="' + newActionId + '"]');
		var next_button = $('.clean-action-wrapper a.action-container-next');
		currentAction.hide().removeClass('active');
		next_button.text(action_list[currentAction.attr('data-action-id')]).attr('data-action-id', selected_action_list[newActionId]['next']);
		$(this).text(action_list[selected_action_list[newActionId]['prev']]).attr('data-action-id', selected_action_list[newActionId]['prev']);
		newAction.show().addClass('active');
		if (isNaN(parseInt(selected_action_list[newActionId]['prev']))) {
			$(this).parent().addClass('hide');
		}
		if (next_button.parent().hasClass('hide')) {
			next_button.parent().removeClass('hide');
		}
	});
	$('.clean-action-menu li a').click(function () {
		var current = $('.action-container.active');
		var newActionId = $(this).attr('data-action-id');
		var newAction = $('.action-container[data-action-id="' + newActionId + '"]');
		current.hide().removeClass('active');
		newAction.show().addClass('active');
		var prev_button = $('.clean-action-wrapper a.action-container-prev');
		var next_button = $('.clean-action-wrapper a.action-container-next');
		prev_button.attr('data-action-id', selected_action_list[newActionId]['prev']).text(action_list[selected_action_list[newActionId]['prev']]).parent().removeClass('hide');
		next_button.attr('data-action-id', selected_action_list[newActionId]['next']).text(action_list[selected_action_list[newActionId]['next']]).parent().removeClass('hide');
		if (isNaN(parseInt(selected_action_list[newActionId]['prev']))) {
			prev_button.parent().addClass('hide');
		}
		if (isNaN(parseInt(selected_action_list[newActionId]['next']))) {
			next_button.parent().addClass('hide');
		}
	});
	$('#diagnostic').click(function () {
		var btn = this;

		var stepsEx = [];
		$.each(selected_action_list, function (i, v) {
			stepsEx[stepsEx.length] = i;
		});
		var steps = stepsEx.join('|');

		$(btn).fadeTo(0, 0.5);
		$.ajax({
			method: 'post',
			url: '/bitrix/admin/acrit_cleanmaster_processor.php',
			data: 'funcName=diagnostic&diagnosticStep=1&steps=' + steps + '&showFinded=' + $(btn).data('restore'),
			success: function (data) {
				try {
					var obj = JSON.parse(data);
					//console.log(obj);
					if (obj.result == 'OK' && obj.action == 'process') {
						//$('.cleanmaster-area').first().append(obj.DATA);
						$('.cleanmaster-area .diagnostic-steps').html(obj.DATA);
					}
				} catch (e) {
					console.log(e);
                    console.log(data);
					alert('Error: wrong response from server. Please, refresh page!');
				}
				$(btn).fadeTo(0, 1);
			}
		});
	});

	// step analyze block
	$(document).on('click', '.action-analys', function () {
		var currentAction = $('.action-container.active');
		var form = $('.action-container.active form');
		$('.analyse_flag', form).val('Y');

		$('.action-process', form).click();
		$('.analyse_flag', form).val('');
	});
});