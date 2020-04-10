<?
$MESS["CLEANMASTER_ACTION_1"] = "Удаление неактивных сайтов";
$MESS["CLEANMASTER_ACTION_2"] = "Удаление неиспользуемых шаблонов сайтов";
$MESS["CLEANMASTER_ACTION_3"] = "Удаление неактивных элементов инфоблоков";
$MESS["CLEANMASTER_ACTION_4"] = "Почтовые шаблоны (неактивные)";
$MESS["CLEANMASTER_ACTION_5"] = "Очистка папки upload";
$MESS["CLEANMASTER_ACTION_5_DELETE_RESIZE_CACHE"] = "Удалить папку /upload/resize_cache с уменьшенными копиями изображений. <br> Важно! Также будет сброшен кеш сайта, для того, чтобы очистить ссылки на уменьшенные копии изображений.";
$MESS["CLEANMASTER_ACTION_5_TMP_DIR_DESCRIPTION"] = "При очистке папки upload файлы сперва помещаются во временный каталог <b>/upload/cleanmaster/</b>, а не удаляются полностью с сайта.<br><br>
Это необходимо для того, чтобы была возможность после очистки проверить сайт и в случае ошибки откатить удаленные файлы.<br><br>
В конце очистки папки upload Вам будет предложено <br><b>удалить временную папку /upload/cleanmaster/</b> (размер - <b>#SIZE_MB#мб</b>) ";
$MESS["CLEANMASTER_ACTION_5_TMP_DIR_DESCRIPTION_DOING"] =  "
<a href=\"/bitrix/admin/acrit_cleanmaster_processor.php?clear_tmp_dir=Y\" class=\"dyn-url\"><b>очистить</b></a> или
<a href=\"/bitrix/admin/acrit_cleanmaster_processor.php?revert_tmp_dir=Y\" class=\"dyn-url\"><b>откатить</b></a>";

$MESS["CLEANMASTER_ACTION_6"] = "Удаление неактивных пользователей";
$MESS["CLEANMASTER_ACTION_7"] = "Статистика по заказам";
$MESS["CLEANMASTER_ACTION_8"] = "Очистить кэш";
$MESS["CLEANMASTER_ACTION_9"] = "Очистить журнал вторжений";
$MESS["CLEANMASTER_ACTION_10"] = "Очистить историю веб-статистики";
$MESS["CLEANMASTER_ACTION_11"] = "Неоформленные корзины";
$MESS["CLEANMASTER_ACTION_12"] = "Несуществующие адреса подписчиков";
$MESS["CLEANMASTER_ACTION_13"] = "История почтовых событий";
$MESS["CLEANMASTER_ACTION_14"] = "Очистка результатов веб-форм";
$MESS["CLEANMASTER_ACTION_15"] = "Файлы в папке upload";
$MESS["CLEANMASTER_ACTION_16"] = "Языковые файлы";
$MESS["CLEANMASTER_ACTION_17"] = "Модули (в статусе удален)";
$MESS["CLEANMASTER_ACTION_17_DESCRIPTION"] = "Системные модули (представлены <b>жирным шрифтом</b> в диагностике) будут скачаны с серверов обновлений 1С-Битрикс при следующем обновлении платформы.<br>
Можно удалить агенты, оставив при удалении только соответствующую настройку<br>";
$MESS["CLEANMASTER_ACTION_18"] = "Разделы и страницы";
$MESS["CLEANMASTER_ACTION_19"] = "Таблицы БД";
$MESS["CLEANMASTER_ACTION_20"] = "Компоненты";
$MESS["CLEANMASTER_ACTION_21"] = "Выпуски рассылки за выбранный период";
$MESS["CLEANMASTER_ACTION_22"] = "Статистика монитора производительности";
$MESS["CLEANMASTER_ACTION_23"] = "Последние просмотренные товары";
$MESS["CLEANMASTER_ACTION_24"] = "Пошаговое удаление элементов инфоблоков";
$MESS["CLEANMASTER_ACTION_25"] = "Очистка спама на сайте (форум)";
$MESS["CLEANMASTER_ACTION_26"] = "Очистка спама на сайте (блог)";
$MESS["CLEANMASTER_ACTION_27"] = "Исправление совпадающих символьных кодов у свойств элементов инфооблоков";
$MESS["CLEANMASTER_ACTION_28"] = "Очистка отдельных таблиц БД";
$MESS["CLEANMASTER_ACTION_28_DESCRIPTION"] = "Некоторые таблицы платформы битрикс могут занимать значительное пространство диска, при этом может быть безопасно очищено.";
$MESS["CLEANMASTER_ACTION_27_DESCRIPTION"] = "Платформа битрикс считает совпадающие коды ошибкой. Этот инструмент позволяет исправить данный тип ошибки.";
$MESS["CLEANMASTER_ACTION_1_DESCRIPTION"] = "Будут удалены неактивные сайты и связанные с ними инфоблоки. Если инфоблок привязан хотя-бы к одному активному сайту, то удален не будет. Выберите из списка неактивных сайтов те, который вы хотите удалить.";
$MESS["CLEANMASTER_ACTION_1_NOT_USED"] = "Список неактивных сайтов:";
$MESS["CLEANMASTER_ACTION_1_NOT_FOUND"] = "Неактивные сайты не найдены.";
$MESS["CLEANMASTER_ACTION_1_SITE"] = "Сайт #SITE# : ";
$MESS["CLEANMASTER_ACTION_1_SITE_DELETED"] = "Сайт #SITE# удален.";
$MESS["CLEANMASTER_ACTION_2_NOT_USED"] = "Неиспользуемые шаблоны:";
$MESS["CLEANMASTER_ACTION_2_NOT_FOUND"] = "Неиспользуемые шаблоны не найдены.";
$MESS["CLEANMASTER_ACTION_3_NOT_USED"] = "Выберите инфоблоки, из которых будут удалены неактивные элементы:";
$MESS["CLEANMASTER_ACTION_3_NOT_FOUND"] = "Не найдено ни одного дезактивированного элемента инфоблока.";
$MESS["CLEANMASTER_ACTION_4_NOT_USED"] = "Неактивные почтовые шаблоны:";
$MESS["CLEANMASTER_ACTION_4_NOT_FOUND"] = "Неактивные почтовые шаблоны не найдены";
$MESS["CLEANMASTER_ACTION_5_DESCRIPTION"] = "Будут удалены файлы, незарегистрироованые в базе данных, модуля инфоблоков, медиабиблиотеки, блога, форума, основного модуля в битриксе. 
При этом папки и файлы не относящиеся к зарегистрированным модулям удалены не будут.";
$MESS["CLEANMASTER_ACTION_6_DELETE_ASSETS"] = "Удалить связанные с пользователями данные <br><span style='color:red; font-weight:bold'>Внимание!</span> Будут удалены неактивные пользователи, <span style='font-weight:bold'>их заказы, сообщения на форуме, результаты веб-форм, обращения в ТП, блоги, личный счет, сообщения и группы соц.сети</span>.";
$MESS["CLEANMASTER_ACTION_6_DESCRIPTION"] = "";
$MESS["CLEANMASTER_ACTION_6_NOT_AUTH"] = "Удалять ни разу не авторизованных пользователей";
$MESS["CLEANMASTER_ACTION_7_NOT_PAYED"] = "Удалять неоплаченные заказы";
$MESS["CLEANMASTER_ACTION_8_DESCRIPTION"] = "Будет удален весь кэш (управляемый кэш, кэш меню, неуправляемый)";
$MESS["CLEANMASTER_ACTION_16_DESCRIPTION"] = "Выберите, которые вы используете и не хотите удалять. Все остальные языковые файлы будут удалены.";
$MESS["CLEANMASTER_ACTION_FROM"] = "Удалять с:";
$MESS["CLEANMASTER_ACTION_TO"] = "Удалять до:";
$MESS["CLEANMASTER_ACTION_DAY"] = "День";
$MESS["CLEANMASTER_ACTION_MONTH"] = "Месяц";
$MESS["CLEANMASTER_ACTION_YEAR"] = "Год";
$MESS["CLEANMASTER_ACTION_MENU"] = "Меню CleanMaster";
$MESS["CLEANMASTER_ACTION_CLEANSTART"] = "Запустить очистку";
$MESS["CLEANMASTER_ACTION_SAVE_PROFILE"] = "Сохранить профиль";
$MESS["CLEANMASTER_ACTION_ANALYSE_START"] = "Анализировать";
$MESS["CLEANMASTER_ACTION_SELECT"] = "Продолжить";
$MESS["ACRIT_CLEANMASTER_RUN_DIAGNOSTIC"] = "Запустить диагностику";
$MESS["ACRIT_CLEANMASTER_RUN_DIAGNOSTIC_FULL"] = "Запустить полную диагностику";
$MESS["ACRIT_CLEANMASTER_TO_CLEAR"] = "Перейти к очистке";
$MESS["ACRIT_CLEANMASTER_DOP_TOOLS"] = "Дополнительные инструменты";
$MESS["ACRIT_CLEANMASTER_BACKTO_DIAGNOSTIC"] = "Вернуться к диагностике";
$MESS["ACRIT_CLEANMASTER_BACKTO_DIAGNOSTIC_FULL"] = "Вернуться к общей диагностике";
$MESS["ACRIT_CLEANMASTER_OR"] = "или";
$MESS["ACRIT_CLEANMASTER_DEMO"] = "<h2 style='color:red'>Вы пользуетесь демо-версией модуля.<br>Полный функционал очистки доступен в платной версии.<br></h2>";
$MESS["CLEANMASTER_ACTION_24_NOT_USED"] = "<span style='color:red'>Важно! Будут удалены все элементы выбранных инфоблоков. Можно использовать для быстрой очистки выгрузок из 1с, демо-данных и т.д.</span>
<p>Выберите инфоблоки, из которых будут удалены <span style='color:red'>ВСЕ ЭЛЕМЕНТЫ</span>:</p>";
$MESS["CLEANMASTER_ACTION_24_NOT_FOUND"] = "Не найдено ни одного элемента инфоблока.";
$MESS["CLEANMASTER_ACTION_25_NOT_FOUND"] = "Модуль форума не установлен";
$MESS["CLEANMASTER_ACTION_25_SPAM_HELP"] = '<span style=\'color:red\'>Одно из полей обязательно нужно заполнить, иначе будут удалены все сообщения с форума!</span>';
$MESS["CLEANMASTER_ACTION_25_SPAM"] = "Введите часть спам-сообщения для удаления всех подобных сообщений с форума:";
$MESS["CLEANMASTER_ACTION_25_POST_MESSAGE_HTML"] = "Подстрока в сообщения для удаления подобных:";
$MESS["CLEANMASTER_ACTION_25_AUTHOR_NAME"] = "Имя автора, сообщения которого требуется удалить:";
$MESS["CLEANMASTER_ACTION_26_SPAM"] = "Введите часть спам-сообщения для удаления всех подобных комментариев с блога:";
$MESS["CLEANMASTER_ACTION_26_SPAM_HELP"] = '<span style=\'color:red\'>Одно из полей обязательно нужно заполнить, иначе будут удалены все комментарии с блога!</span>';
$MESS["CLEANMASTER_ACTION_26_POST_MESSAGE_HTML"] = "Подстрока в сообщения для удаления подобных:";
$MESS["CLEANMASTER_ACTION_26_AUTHOR_NAME"] = "Имя автора, сообщения которого требуется удалить:";
$MESS["CLEANMASTER_ACTION_26_NOT_FOUND"] = "Модуль блогов не установлен";
$MESS["CLEANMASTER_ACTION_CLEANSTART_REPAIR"] = "Исправить";
$MESS["CLEANMASTER_ROWS"] = "строк";
$MESS["CLEANMASTER_ACTION_DEMO"] = "<span style='color:red'>Полный функционал доступен в лицензионной версии модуля.</span>";
$MESS["CLEANMASTER_MB"] = 'мб';
$MESS["CLEANMASTER_ACTION_17_deleteFiles"] = "Удалить файлы удаленных модулей";
$MESS["CLEANMASTER_ACTION_17_deleteAgents"] = "Удалить агенты удаленных модулей";
$MESS["CLEANMASTER_ACTION_17_deleteFilesSys"] = "Включить системные модули (представлены жирным в диагностике). Если закончилась лицензия на получение обновлений, не рекомендуем удалять их файлы.";
?>