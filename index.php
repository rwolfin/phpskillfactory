<?php

$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];

/**
 * Соединяет фамилию, имя и отчество в одну строку.
 *
 * @param string $surname Фамилия.
 * @param string $name Имя.
 * @param string $patronomyc Отчество.
 *
 * @return string Полное ФИО.
 */
function getFullnameFromParts(string $surname, string $name, string $patronomyc): string
{
    return $surname . ' ' . $name . ' ' . $patronomyc;
}

/**
 * Разбивает ФИО на фамилию, имя и отчество.
 *
 * @param string $fullname Полное ФИО.
 *
 * @return array Массив с ключами 'surname', 'name' и 'patronomyc'.
 */
function getPartsFromFullname(string $fullname): array
{
    $parts = explode(' ', $fullname);
    return [
        'surname' => $parts[0],
        'name' => $parts[1],
        'patronomyc' => $parts[2],
    ];
}

/**
 * Сокращает ФИО до вида "Имя Ф.".
 *
 * @param string $fullname Полное ФИО.
 *
 * @return string Сокращенное ФИО.
 */
function getShortName(string $fullname): string
{
    $parts = getPartsFromFullname($fullname);
    return $parts['name'] . ' ' . mb_substr($parts['surname'], 0, 1) . '.';
}

/**
 * Определяет пол по ФИО.
 *
 * @param string $fullname Полное ФИО.
 *
 * @return int 1 - мужской, -1 - женский, 0 - неопределенный.
 */
function getGenderFromName(string $fullname): int
{
    $parts = getPartsFromFullname($fullname);
    $gender = 0;

    // Признаки женского пола
    if (mb_substr($parts['patronomyc'], -3) === 'вна') {
        $gender--;
    }
    if (mb_substr($parts['name'], -1) === 'а') {
        $gender--;
    }
    if (mb_substr($parts['surname'], -2) === 'ва') {
        $gender--;
    }

    // Признаки мужского пола
    if (mb_substr($parts['patronomyc'], -2) === 'ич') {
        $gender++;
    }
    if (in_array(mb_substr($parts['name'], -1), ['й', 'н'])) {
        $gender++;
    }
    if (mb_substr($parts['surname'], -1) === 'в') {
        $gender++;
    }

    if ($gender > 0) {
        return 1;
    } elseif ($gender < 0) {
        return -1;
    } else {
        return 0;
    }
}

/**
 * Определяет половой состав аудитории.
 *
 * @param array $persons_array Массив с данными о людях.
 *
 * @return string Информация о половом составе аудитории.
 */
function getGenderDescription(array $persons_array): string
{
    $total_count = count($persons_array);

    $male_count = 0;
    $female_count = 0;
    $undefined_count = 0;

    foreach ($persons_array as $person) {
        $gender = getGenderFromName($person['fullname']);
        if ($gender === 1) {
            $male_count++;
        } elseif ($gender === -1) {
            $female_count++;
        } else {
            $undefined_count++;
        }
    }

    $male_percentage = round(($male_count / $total_count) * 100, 1);
    $female_percentage = round(($female_count / $total_count) * 100, 1);
    $undefined_percentage = round(($undefined_count / $total_count) * 100, 1);

    return <<<HEREDOC
Гендерный состав аудитории:
---------------------------
Мужчины - {$male_percentage}%
Женщины - {$female_percentage}%
Не удалось определить - {$undefined_percentage}%
HEREDOC;
}


/**
 * Подбирает идеальную пару.
 *
 * @param string $surname Фамилия.
 * @param string $name Имя.
 * @param string $patronomyc Отчество.
 * @param array $persons_array Массив с данными о людях.
 *
 * @return string Информация об идеальной паре.
 */
function getPerfectPartner(string $surname, string $name, string $patronomyc, array $persons_array): string
{
    $surname = mb_convert_case($surname, MB_CASE_TITLE, "UTF-8");
    $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE, "UTF-8");
    $fullname = getFullnameFromParts($surname, $name, $patronomyc);
    $gender = getGenderFromName($fullname);

    do {
        $random_person = $persons_array[array_rand($persons_array)];
        $partner_gender = getGenderFromName($random_person['fullname']);
    } while ($gender === $partner_gender || $partner_gender === 0);

    $compatibility = round(mt_rand(5000, 10000) / 100, 2);
    return getShortName($fullname) . ' + ' . getShortName($random_person['fullname']) . " = \n" .
        "♡ Идеально на {$compatibility}% ♡\n";
}


$result =  <<<HEREDOC
\n\n
Примеры вызова функций (для тестирования)
\n
1. Пример getFullnameFromParts:  . getFullnameFromParts('Иванов', 'Иван', 'Иванович') . "
\n
2. Пример getPartsFromFullname: 
\n
3.  . print_r(getPartsFromFullname('Иванов Иван Иванович'), true) . "
\n
4. Пример getShortName: " . getShortName('Иванов Иван Иванович') . "
\n\n
5. Пример getGenderFromName: " . getGenderFromName('Иванова Алина Ивановна') . "
\n\n
6. Пример getGenderFromName: " . getGenderFromName('Иванов Иван Иванович') . "
\n\n
7. Пример getGenderFromName: " . getGenderFromName('Яковлев Яков Яковлевич') . "
\n\n
8. Пример getGenderDescription: \n" . getGenderDescription($example_persons_array) . "
\n\n
9. Пример getPerfectPartner: \n" . getPerfectPartner('иванов', 'иван', 'иванович', $example_persons_array) . "
\n
HEREDOC;

?>