<?php
class Document {

    public $user;
    // Переменная инициализируется, но ни где не используется

    public $name;
    // Смущает что имя публичное в то время как в init далается проверка на входящее значение, ни что не мешает позже заменить имя на произвольный текст

    public function init($name, User $user) {
        assert(strlen($name) > 5);
        $this->user = $user;
        $this->name = $name;
    }

    public function getTitle() {
        $db = Database::getInstance();
        // Инстанс создается на каждый вызов функции, возможно лучше его создать один раз, при инициализации класса
        $row = $db->query('SELECT * FROM document WHERE name = "' . $this->name . '" LIMIT 1');
        // Переменная name отправляется в SQL запрос по сути как есть, что дает возможность проводить SQL иньекции
        return $row[3]; // third column in a row
        // Не понятно зачем запрашивать все поля, а потом выподить только поле отсчитаное по порядку. Правильнее запросить только те данные
        // которые будет выводить.
        // Кроме того, структура таблицы может поменяться и тогда третье поле перестанет быть title
    }

    public function getContent() {
        // эта функция очень похожа на функцию getTitle, возможно лучше создать одну унифицированную функцию принимаюшую как параметр название поля
        $db = Database::getInstance();
        $row = $db->query('SELECT * FROM document WHERE name = "' . $this->name . '" LIMIT 1');
        return $row[6]; // sixth column in a row
    }

    public static function getAllDocuments() {
        // to be implemented later
    }

}

class User {

    public function makeNewDocument($name) {
        $doc = new Document();
        $doc->init($name, $this);
        return $doc;
    }

    public function getMyDocuments() {
        $list = array();
        foreach (Document::getAllDocuments() as $doc) {
            if ($doc->user == $this)
            // Не понятно, зачем вытягивать все документы чтобы потом отфильтровать их по пользователю, возможо правильнее создать функцию getAllDocuments
            // которая принимает на вход пользователя и внутри создаетс SQl запрос который уже фильтрует только пользовательские документы на стороне базы
                $list[] = $doc;
                // Кажется что тут происходит не наращивание массива, а замена ссылки с массива на doc, которая в результате и вернется из функции
        }
        return $list;
    }

}
