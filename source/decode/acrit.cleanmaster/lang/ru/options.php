<?
$MESS["ACRIT_CLEANMASTER_PERED_NACALOM_OCISTK"] = "Перед началом очистки сделайте резервную копию сайта!";
$MESS["ACRIT_CLEANMASTER_REZERVNOE_KOPIROVANI"] = "РЕЗЕРВНОЕ КОПИРОВАНИЕ";
$MESS["ACRIT_CLEANMASTER_FILESYS_WARNING"] =
	'<div style="font-size:115%;"><b>Внимание!</b> Модуль может создавать нагрузку на файловую систему, желательно выполнять очистку в моменты минимальной посещаемости сайта.'
	.' <h4>Перед запуском диагностики и очистки крайне рекомендуется:</h4>'
		. ' <ul> <li>' . '<span style="color:red;font-weight:bold;">Сделайте резервную копию сайта!</span>'
		. ' <a href="/bitrix/admin/dump.php" style="text-decoration:underline;"><b>Резервное копирование</b></a><br/>' . '</li>'
	. '<li><b><a href="/bitrix/admin/security_stat_activity.php?lang=ru">отключить контроль активности антивируса</a></b></li>'
	. '<li> а также поднимите время жизни сессии (минут) в настройках группы пользователей (<a href="/bitrix/admin/group_edit.php?lang=ru&ID=1"><b>Настройки - 
	Пользователи - Группы пользователей, вкладка безопасность</b></a>)</li>
	<li>Не открывать одновременно диагностику и очистку в разных вкладках браузера.</li>	
	</ul></div>';

$MESS["ACRIT_CLEANMASTER_IS_DEMO_MESSAGE"] = "<span style=\"color:red\">Внимание!</span> Вы используете демо-версию модуля \"Мастер очистки сайта\". Для использования полного функционала модуля нужно приобрести лицензию.";
$MESS["ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BTN"] = 'Купить лицензию';
$MESS["ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BUY_URL"] = 'https://www.acrit-studio.ru/market/module/acrit.cleanmaster/?action=BUY&id=151637';


$MESS["ACRIT_CLEANMASTER_MAIN_TAB_SET"] = "Диагностика и очистка";
$MESS["ACRIT_CLEANMASTER_MAIN_TAB_SUPPORT"] = "Техподдержка";
$MESS["ACRIT_CLEANMASTER_MAIN_TAB_TITLE_SUPPORT"] = "Техподдержка модуля";
$MESS["ACRIT_CLEANMASTER_SUPPORT_TEXT"] = '<p>
 <b>Список рекомендаций, что надо сделать, чтоб удостовериться в устойчивой работе сайта</b>:
</p>
<p>
</p>
<p>
</p>
<ol>
    <li>проверяем сайт стандартным тестом проверки сайта:
<p>Инструменты – Проверка системы – на вкладке - Тестирование системы, нажать кнопку проверки. В случае если будут показаны ошибки или предупреждения, необходимо задать вопрос разработчику сайта или хостеру для их устранения, далее на вкладке - Проверка доступа - запустить полную проверку прав и в случае, если тест покажет, что на какие то файлы не хватает прав, то проблему стоит устранить совместно с оператором хостинга сайта.<p></li>
    <li>выставляем в настройках модуля производительности галочки</li>
</ol>
<p>
</p>
<ul>
    <li>
    <p>
         Вести журнал предупреждений PHP &nbsp;
    </p>
 </li>
    <li>
    <p>
         Вести журнал кеширования &nbsp; &nbsp;&nbsp;
    </p>
 </li>
    <li>
    <p>
         Вести журнал SQL запросов &nbsp; &nbsp; &nbsp;
    </p>
 </li>
    <li>
    <p>
         Сохранять стек вызова для SQL запросов &nbsp;
    </p>
 </li>
</ul>
<p>
     Запускаем монитор производительности на 20 минут и после сохранения настроек пробуем открыть настройки модуля и заново создать настройки модуля в режиме выгрузки компонентом (если каталог товаров больше 1000 позиций и вы не уверены в наличии свободных ресурсов у сервера то выгрузку стоит сделать только на крон или агент) после этого переходим в публичный раздел сайта и пока идет мониторинг <i>(если прошло более 20 минут то монитор производительности стоит включить повторно или на более продолжительный период)</i> проходим в каталог товаров открывая желательно каждый из выгружаемых разделов и в каждом разделе открываем по одному товару и добавляем его в корзину <i>(в случае если категорий более 10 штук достаточно эту процедуру сделать для разделов от 5 до 10 штук чем больше тем лучше так как соберется больше статистических данных) </i>потом проводим оформление покупки собранных товаров до момента оплаты.&nbsp;
</p>
<p>
     Желательно также протестировать работу других инфоблоков при включенном мониторе производительности, так как нагрузку может создавать не только сам каталог, а и новости статьи форум и т.п. разделы где может быть много элементов. Кроме того, даже если элементов мало, то могут присутствовать ошибки при формировании запросов, и даже маленький инфоблок, может из публичной части создавать очень долгие запросы, которые и попадут в статистику для дальнейшего анализа. После того, как отработает тест производительности, надо зайти в раздел Производительность – Индексы – Анализ индексов и выполнить анализ собранных SQL запросов. После выполнения анализа, как правило, в списке появляются рекомендации по созданию индексов. Зайдя в детальный анализ индекса, можно сделать недостающий индекс стандартными средствами. После создания индекса, вы увидите, насколько процентов выросла производительность запроса. В случае, если ни одной записи не появилось, следует повторить процедуру тестирования на более продолжительном тестировании и большем количестве операций с сайтом, включая и операции создания, редактирования элементов инфоблоков внутри административного раздела. Финалом мониторинга будет реестр ошибок предупреждений и уведомлений в разделе Производительность – Ошибка PHP. Если в этом разделе включить группировку, то можно будет увидеть сгруппированный по типам ошибок отчет. Если в этом отчете будут присутствовать строки Error то их надо обязательно устранить совместно с разработчиком сайта иначе вы вообще рискуете потерять какие либо данные. Если присутствуют строки со статусом Warning то стоит проконсультироваться с разработчиком сайта по возможности их устранения и устранить найденные ошибки.
</p>
<p>
</p>
<p>
     - далее идет тест производительности – Производительность – Панель производительности – Конфигурация - и если по его результату появляются рекомендации на вкладках – Битрикс и Разработка, их стоит выполнить, особенно если отключен акселератор или страницы сайта имеют не кешированные компоненты (но имейте в виду что часть компонентов, такие как просматриваемые товары или корзина не должны кешироваться). В отчете на вкладке Битрикс можно увидеть рекомендации к Конфигурации PHP, &nbsp;которые также стоит проверить нажав по ссылке и убедившись, что все настройки оптимальны
</p>
<p>
</p>
<p>
     - далее идет тест масштабируемости сайта (с помощью которого можно протестировать базовые показатели устойчивости сайта к нагрузкам) и в случае нестабильных показателей, а особенно появлении в нем ошибок, стоит подключить к работе разработчика
</p>
<p>
     &nbsp;<br>
     - далее запускается тест сканера безопасности модуля проактивной защиты. Если модуля у вас нет, то вам стоит обратиться к нам в ТП с вопросом по переходу на редакцию, в которую данный модуль входит, или же за проверкой сайта на уязвимости и наличие вирусов. В случае наличия модуля в отчет падают предупреждения которые&nbsp; необходимо устранить совместно с оператором хостинга и разработчиком
</p>
<p>
     &nbsp;<br>
     - далее в разделе – Производительность – Страницы, анализируются страницы сайта по производительности. При наличии страниц генерирующихся более 0,4 секунды, также стоит обратиться к разработчику сайта с вопросом увеличения скорости их работы. Стоит также обратить внимание при проверке скорости сайта, включен ли композитный режим и поддерживается ли он в публичной части о чем должна свидетельствовать кнопка композитного режима
</p>
<p>
</p>
<p>Веб Студия АКРИТ</p>
<fieldset title=\'Обратная связь\'>
	 <legend>Обратная связь</legend>
	 <a href=\'http://www.acrit-studio.ru/services/\' target=\'_blank\' title=\'Услуги по созданию и продвижению сайтов\'>Разработка, продвижение,</a> а также <a href=\'http://www.acrit-studio.ru/technical-support/\' target=\'_blank\'>техническая поддержка:</a> <br/>
	 <a href=\'http://www.acrit-studio.ru/market/\' title=\'Используя наши тиражные решения вы сможете расширить охват вашей целевой аудитории и получить дополнительных клиентов\'>Узнайте как увеличить прибыль с сайта</a>&nbsp; +7 495 008 8452; <a href=\'mailto:design@acrit-studio.ru\' title=\'Электронная почта Веб студии АКРИТ\'>design@acrit-studio.ru</a> <br/>
		 Заказ <a href=\'http://www.acrit-studio.ru/technical-support/\' target=\'_blank\' title=\'Если у вас возникли какие то сложности или задачи по сайту обращайтесь по данной ссылке\'>доработок и&nbsp;техподдержка сайтов через службу Технической поддержки</a>
</fieldset>';

$MESS['SC_FRM_1'] = 'Отправить сообщение в службу техподдержки';
$MESS['SC_FRM_2'] = 'Описание проблемы';
$MESS['SC_FRM_3'] = 'последовательность действий, которая привела к ошибке, описание ошибки,...';
$MESS['SC_FRM_4'] = 'Отправить сообщение';
$MESS['SC_TXT_1'] = 'В случае возникновения проблем с отправкой обращения, пожалуйста, воспользуйтесь формой на нашем сайте:';

$MESS['SC_RUS_L1'] = 'Обращение с сайта';
$MESS['SC_NOT_FILLED'] = 'Введите ваше обращение';
$MESS['A_SUPPORT_URL'] = 'https://www.acrit-studio.ru/support/?show_wizard=Y';
$MESS['ACRIT_CLEANMASTER_MAIN_TAB_OPTIONS'] = 'Опции';
$MESS["ACRITMENU_GROUPNAME_LABEL"] = "Наименование административного пункта меню";
$MESS['ACRIT_CLEANMASTER_LAST_DIAG_TAB_OPTIONS'] = 'Результаты последней диагностики';
$MESS['ACRIT_CLEANMASTER_LOADING'] = 'Загрузка...';
$MESS['ACRIT_CLEANMASTER_FULL_DIAG_INTERRUPT_WARNING'] = '<fieldset>
	<legend>Если полная диагностика завершилась с ошибкой</legend>
	<ol>
		<li>Поднимите время жизни сессии (минут) в <a target="_blank" href="/bitrix/admin/group_edit.php?lang=ru&ID=1&tabControl_active_tab=edit2">настройках группы пользователей</a>
		<li>Отключите <a target="_blank" href="/bitrix/admin/security_session.php?lang=ru">хранение сессий в базе данных, а также смену идентификатора сессии</a>
		<li>Если ничего не помогло, <a href="/bitrix/admin/acrit_cleanmaster_support.php?lang=ru">обратитесь в тех.поддержку модуля, предоставив скриншот с ошибкой</a>
	</ol>  
</fieldset>';
$MESS['ACRIT_CLEANMASTER_PHP_PATH'] = 'Путь к php (можно просто php, если используется машина bitrix vm)';
?>