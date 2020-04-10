<?
$MESS["acrit_cleanmaster_PROFILES_LIST"] = "Список профилей модуля очистки";
$MESS["ACRIT_CLEANMASTER_CRONTAB_HELP_HTML"] = "";
$MESS["CRON_CMD_TITLE"] = "Команда для установки в крон";
$MESS["ACRIT_CLEANMASTER_CRONTAB_HELP_HTML"] = "
<b>Установка задач на выполнение</b>
<p><b>Все команды из колонки &laquo;Команда для установки в крон&raquo; необходимо установить в планировщик крон.</b><br>
Время выполнения вы можете выбрать по желанию, 
однако мы рекомендуем устанавливать запуск профиля очистки не чаще раза в сутки ночью (в моменты минимальной посещаемости сайта)</p>

<p>
Например, запустить профиль с ID = 12  в 3 часа ночи можно следующей записью в crontab<br>
<code>
# редактируем кронтаб, добавляем записи:<br>
crontab -e -u bitrix<br><br>

# m h dom mon dow command<br>
0 3 * * * php -f /var/www/bitrix/modules/acrit.cleanmaster/cron/profile_run.php 12<br>
</code>
</p>

<em>cron — классическая компьютерная программа, использующийся для периодического выполнения заданий в определённое время.</em>
";
$MESS["ACRIT_CLEANMASTER_CRONTAB_PHP_PATH_HTML"] = "Путь к php можно поменять в 
<a href='/bitrix/admin/settings.php?lang=ru&mid=acrit.cleanmaster&mid_menu=1&tabControlAcritClean_active_tab=edit3'>опциях модуля</a>.<br>
Сейчас установлен путь к php: #PHP_PATH#";

?>