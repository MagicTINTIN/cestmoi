<?php

require_once __DIR__ . '/../../../magictintin_db.php';
require_once __DIR__ . '/achievements.php';

function do_profile_exists(?PDO $dbm = null): array
{
    global $_USER;
    if (!$_USER) return [];
    $dbm ??= dbmConnect();

    $stmt = $dbm->prepare('SELECT * FROM user_info WHERE qsj_id = ? OR username = ? LIMIT 1');
    $stmt->execute([$_USER["id"], $_USER["username"]]);
    $count = $stmt->rowcount();
    if ($count) return $stmt->fetch();
    else return [];
}

function get_who_profile_or_initialize(?PDO $dbm = null): array
{
    global $_USER;
    if (!$_USER) return [];
    $dbm ??= dbmConnect();

    $res = do_profile_exists($dbm);
    if ($res != []) return $res;

    // initialize who page if it doesn't exists yet
    $stmt = $dbm->prepare(
        'INSERT INTO user_info (username, qsj_id, displayed_name, description, points) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$_USER["username"], $_USER["id"], $_USER["username"], "Profil de " . $_USER["username"], 10]);

    // refetch
    return do_profile_exists($dbm);
}


function increment_bon_points(int $amount, ?string $dest = null, ?PDO $dbm = null): void
{
    global $_USER;
    if (!$_USER) return;
    $dbm ??= dbmConnect();

    $res = get_who_profile_or_initialize();
    if ($res == []) return;

    if ($dest) {
        $sql = "UPDATE member_profile SET points = points + ? WHERE username = ?";
        $dbm->prepare($sql)->execute([$amount, $dest]);
    } else {
        $sql = "UPDATE member_profile SET points = points + ? WHERE qsj_id = ?";
        $dbm->prepare($sql)->execute([$amount, $_USER["id"]]);
    }
}
