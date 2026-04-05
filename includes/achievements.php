<?php

require_once __DIR__ . '/../../../magictintin_db.php';
include_once(__DIR__ . '/tools.php');

// id => [name, description, icon, obtainable]
$_ACHIEVEMENTS = [
    "first_login"    => ["Premier Pas", "Se connecter pour la toute première fois.", "icons/first_login.jpg", true],
    "collector"      => ["Collectionneur", "Déverrouiller au moins 10 achievements.", "icons/collector.jpg", true],
    "cestbien"     => ["C'est bien", "Cumuler plus de 10 000 bon points.", "icons/cestbien.jpg", true],
    "easter"     => ["Chasseur d'œufs", "A trouvé tous les œufs de Pâques du site magictintin.fr", "icons/easter.jpg", true],
    "nexistepas"     => ["N'existe pas", "Contrairement à cet achievement qui existe", "icons/nexistepas.jpg", false],
    "existe"     => ["Cet achievement existe", "Mais n'est pas obtenable", "icons/existe.jpg", false],
];

function has_achievement(string $achievement_id, ?PDO $dbm = null): array
{
    global $_USER;
    if (!$_USER) return [];
    $dbm ??= dbmConnect();

    $stmt = $dbm->prepare('SELECT * FROM achievements WHERE qsj_id = ? AND achievement_id = ? LIMIT 1');
    $count = $stmt->rowcount();
    $stmt->execute([$_USER["id"], htmlspecialchars($achievement_id)]);
    if ($count) return $stmt->fetch();
    else return [];
}

function add_achievement(string $achievement_id, string $tags = "", ?PDO $dbm = null): bool
{
    global $_USER;
    if (!$_USER) return [];
    $dbm ??= dbmConnect();

    $res = has_achievement($achievement_id, $dbm);
    if ($res != []) return false;

    $stmt = $dbm->prepare(
        'INSERT INTO achievements (qsj_id, achievement_id, properties) VALUES (?, ?, ?)'
    );
    $stmt->execute([$_USER["id"], htmlspecialchars($achievement_id), htmlspecialchars($tags)]);

    return true;
}

function has_achievement_tag(string $achievement_id, string $tag, ?PDO $dbm = null) : bool {
    $ach = has_achievement($achievement_id, $dbm);
    
    if ($ach == []) return false;

    $arr = get_tag_array($ach["properties"]);
    return in_array($tag, $arr);
}

function add_achievement_tag(string $achievement_id, string $tag, ?PDO $dbm = null) : bool {
    $ach = has_achievement($achievement_id, $dbm);
    
    if ($ach == []) {
        add_achievement($achievement_id, $tag, $dbm);
        return true;
    }

    $arr = get_tag_array($ach["properties"]);
    if (in_array($tag, $arr)) return false;
    array_push($arr, $tag);
    $new_tags = implode(",", $arr);
    $stmt = $dbm->prepare("UPDATE achievements SET properties = ? WHERE qsj_id = ? AND achievement_id = ?");
    $stmt->execute([$new_tags, $ach["qsj_id"], $achievement_id]);
}
