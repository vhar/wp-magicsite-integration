Плагин интеграции MagicSite с WordPress
=======================================

# Описание  
Плагин предназначен для автоматического встраивания обязательных разделов сайта образовательных организаций, предусмотренных законодательством Российской Федерации, из информационной системы «MagicSite» на сайт под управлением CMS Wordpress.  
Внешний вид сайта, включая меню и инкапсулированные обязательные разделы, устанавливается в CMS Wordpress. Плагин не оказывает влияния на необязательные разделы, которые ведутся на сайте CMS Wordpress.  
При использовании плагина владелец сайта должен завести аккаунт в информационной системе MagicSite и внести в неё данные о своей организации.  
Информационная система «MagicSite» зарегистрирована в РОСПАТЕНТ № 2020662557 от 16 октября 2020 год, включена в Единый реестр российских программ для электронных вычислительных машин и баз данных по Приказу Минцифры России от 15.03.2021 № 151 Приложение № 2, реестровый №9719, совместима со всеми операционными системами, в том числе с операционной системой Альт на платформе х86 и для архитектуры aarch64.

Информационная система «MagicSite» предусматривает ведение следующих обязательных разделов со своими подраздеоами:  
 * Сведения об образовательной организации;
 * Информационная безопасность;
 * Противодействие коррупции;
 * Независимая оценка качества;
 * Педагоги;
 * Организация питания. 

Представление на сайте производится в строгом соответствии с законодательством. При изменении законодательства в ИС MagicSite вносятся соответствующие правки, что автоматически находит отражение на сайте пользователя. Пользователь избавлен от необходимости отслеживать требования к сайтам образовательных организаций – достаточно заполнять поля ИС MagicSite. Данные попадают в систему мониторинга сайтов.

# Установка
 1. Скачайте ZIP архив плагина https://github.com/vhar/wp-magicsite-integration/raw/master/wp-magicsite-integration.zip  
 2. Зайдите в административную панель CMS WordPress вашего сайта  
     > Чтобы войти в административную панель WordPress, вбейте в адресной строке ссылку http://ваш_сайт/wp-login. php или http://ваш_сайт/wp-admin/, где вместо «ваш_сайт» — доменное имя (адрес) вашего сайта.  
     > Откроется страница с формой для входа в админ-панель.  
 3. Перейдите в раздел Плагины -> Добавить новый  
 4. Нажмите кнопку "Загрузить плагин"  
 5. Нажимите "Выберите файл" и выберите скачанный в шаге 1 архив, после чего нажмите кнопку "Установить"  
 6. После установки плагина активируйте его, нажав кнопку "Активировать плагин"

# Настройка
После успешной активации плагина необходимо указать адрес сайта в ИС MagicSite данные которого будут отображаться на вашем сайте. Для этого:
 1. Зайдите в административную панель CMS WordPress вашего сайта  
     > Чтобы войти в административную панель WordPress, вбейте в адресной строке ссылку http://ваш_сайт/wp-login. php или http://ваш_сайт/wp-admin/, где вместо «ваш_сайт» — доменное имя (адрес) вашего сайта.  
     > Откроется страница с формой для входа в админ-панель.  
 2. Перейдите в Настройки -> MagicSite Integration;
 3. В поле "URL сайта в ИС MagicSite" укажите адрес сайта, созданного в виртуальном кабинете MagicSite (https://cp.edusite.ru);
 4. Нажмите кнопку "Сохранить изменения".

# Установка виджета
Виджет меню навигации MagicSite не требует настроек.
По умолчанию меню навигации виджета устанавливается в доступную для виджетов область сайта.
Для изменения расположения виждета:
 1. Зайдите в административную панель CMS WordPress вашего сайта  
     > Чтобы войти в административную панель WordPress, вбейте в адресной строке ссылку http://ваш_сайт/wp-login. php или http://ваш_сайт/wp-admin/, где вместо «ваш_сайт» — доменное имя (адрес) вашего сайта.  
     > Откроется страница с формой для входа в админ-панель.  
 2. Перейдите в Внешний вид -> Виджеты;
 3. Перетащите панель виджета MagicSite Menu в нужную область/место области сайта.

Если виджет не установился автоматически, добавьте его в нужную область сайта. Для этого:
 1. Зайдите в административную панель CMS WordPress вашего сайта  
     > Чтобы войти в административную панель WordPress, вбейте в адресной строке ссылку http://ваш_сайт/wp-login. php или http://ваш_сайт/wp-admin/, где вместо «ваш_сайт» — доменное имя (адрес) вашего сайта.  
     > Откроется страница с формой для входа в админ-панель.  
2. Перейдите в раздел Внешний вид -> Виджеты;
3. Щелкните по панели виджета MagicSite Menu;
4. Выберите в списке нужную область сайта для установки;
5. Нажимте на "Добавить виджет".

# Обновление
 1. Скачайте ZIP архив плагина https://github.com/vhar/wp-magicsite-integration/raw/master/wp-magicsite-integration.zip  
 2. Зайдите в административную панель CMS WordPress вашего сайта  
     > Чтобы войти в административную панель WordPress, вбейте в адресной строке ссылку http://ваш_сайт/wp-login. php или http://ваш_сайт/wp-admin/, где вместо «ваш_сайт» — доменное имя (адрес) вашего сайта.  
     > Откроется страница с формой для входа в админ-панель.  
 3. Перейдите в раздел Плагины -> Добавить новый  
 4. Нажмите кнопку "Загрузить плагин"  
 5. Нажимите "Выберите файл" и выберите скачанный в шаге 1 архив, после чего нажмите кнопку "Установить"  
 6. На странице подтверждения убедитесь, что версия загруженного плагина старше версии текущего и нажмите "Заменить текущую версию загруженной".  
    > Если версия загруженного плагина младше текущей, нажмите "Отменить и вернуться назад".  
    > Если вы хотите понизить версию плагина, удалите установленный плагин и установите заново нужную версию.  
 7. После обновления плагина, проделайте процедуру "Настройки". Проверьте корректность введенных ранее данных и/или заполните, по необходимости, новые поля. Нажмите кнопку "Сохранить изменения".  
    > Даже если введенные ранее данные корректны и нет, или не требуется заполнить новые поля, нажмите кнопку "Сохранить изменения".
