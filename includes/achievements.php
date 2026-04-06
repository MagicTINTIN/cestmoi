<?php

require_once __DIR__ . '/../../../magictintin_db.php';
include_once(__DIR__ . '/tools.php');
include_once(__DIR__ . '/websocket.php');

function cestmoi_get_icon_path($icon) : string {
    if (file_exists(__DIR__ . "/../icons/$icon"))
        return "cestmoi/icons/$icon";
    return "cestmoi/icons/default.jpg";
}

// id => [name, description, icon, obtainable]
$_ACHIEVEMENTS = [
    "first_login"    => ["C'est un petit pas", "Se connecter pour la toute première fois", cestmoi_get_icon_path("first_login.jpg"), true],
    "collector"      => ["Collectionneur", "Déverrouiller 10 achievements", cestmoi_get_icon_path("collector.jpg"), true],
    "richesse"     => ["C'est bien", "Cumuler plus de 100 bon points", cestmoi_get_icon_path("richesse.jpg"), true],
    "easter"     => ["Chasseur d'œufs", "A trouvé tous les œufs de Pâques du site magictintin.fr", cestmoi_get_icon_path("easter.jpg"), true],
    "micasender"     => ["Micasender", "Envoyer un message sur micasend", cestmoi_get_icon_path("micasender.jpg"), true],
    "bttp"     => ["Retour vers le passé", "Voyager dans le temps", cestmoi_get_icon_path("bttp.jpg"), true],
    "cpoi"     => ["Téléportation", "Copier coller du texte d'un ordinateur à l'autre avec CPOI", cestmoi_get_icon_path("cpoi.jpg"), true],
    "chatcpt"     => ["ChatCPT", "Insulter un pauvre chat qui comprend rien", cestmoi_get_icon_path("chatcpt.jpg"), true],
    // unobtainable
    "nexistepas"     => ["N'existe pas", "Contrairement à cet achievement qui existe", cestmoi_get_icon_path("nexistepas.jpg"), false],
    "existetil"     => ["Existe-t-il ?", "Achievement shrödingeresque", cestmoi_get_icon_path("existetil.jpg"), false],
    "existe"     => ["Cet achievement existe", "Mais n'est pas obtenable", cestmoi_get_icon_path("existe.jpg"), false],
    // special INSA
    "ballincanal"     => ["Un poil fort !", "Envoyer une balle de golf dans le canal", cestmoi_get_icon_path("ballincanal.jpg"), false],
    "gradaphix"     => ["GrADAphics", "Coder un moteur graphique en ADA", cestmoi_get_icon_path("gradaphics.jpg"), false],
    "rust24"     => ["24h du Rust", "Coder en continu pendant 24h (en Rust)", cestmoi_get_icon_path("rust24.jpg"), false],
    "cacaille"     => ["Ça caille con", "Sortir par -24°C", cestmoi_get_icon_path("cacaille.jpg"), false],
    "esoteric"     => ["Et pourquoi pas ?", "Participer à un concours de code en codant en BrainFuck, Prolog et OCaml", cestmoi_get_icon_path("esoteric.jpg"), false],
    "qrdraw"     => ["Rickroll à la main", "Dessiner un QRCode à la main", cestmoi_get_icon_path("cacaille.jpg"), false],
    "chatva"     => ["Chat va ?", "Dessiner un chat dans un angle de tableau", cestmoi_get_icon_path("chatva.jpg"), false],
    "plaqueslt"     => ["CON007", "Noter manuellement 11940 plaques d'immatriculation lituaniennes", cestmoi_get_icon_path("plaqueslt.jpg"), false],
    "escalgolf"     => ["Escalade > Golf", "Je voulais refaire de l'escalade moi, pas du golf de merde !", cestmoi_get_icon_path("escalgolf.jpg"), false],
    "mpi"     => ["Mπ", "Calculer Pi (≈3.141589) sur 1244 cœurs pendant 180s dans les sous-sols du STPI", cestmoi_get_icon_path("mpi.jpg"), false],
    "pwnhotel"     => ["Pawn stars", "Copier le badge de l'hôtel immédiatement après être arrivé pour un CTF", cestmoi_get_icon_path("pwnhotel.jpg"), false],
    "sanslesmains"     => ["Sans les mains !", "Jouer à Minecraft avec les options d'accessibilités de macos", cestmoi_get_icon_path("sanslesmains.jpg"), false],
    // other
    "asp"     => ["ASP", "Astrasia Space Program, ou comment faire décoller des pétards fabriqués avec des tubes de tente.", cestmoi_get_icon_path("asp.jpg"), false],

];

function send_websocket_achievement(string $achievement_id, ?string $tag = "") : void {
    global $_ACHIEVEMENTS;
    if (!key_exists($achievement_id, $_ACHIEVEMENTS)) return;
    $ach = $_ACHIEVEMENTS[$achievement_id];
    $notif = [
        "type" => "new_achievement",
        "name" => $tag == "" ? $ach[0] : $ach[0] . " ($tag)",
        "icon" => $ach[2]
    ];
    send_websocket_notification($notif);
}

function has_achievement(string $achievement_id, ?PDO $dbm = null): array
{
    global $_USER;
    if (!$_USER) return [];
    $dbm ??= dbmConnect();

    $stmt = $dbm->prepare('SELECT * FROM achievements WHERE qsj_id = ? AND achievement_id = ? LIMIT 1');
    $stmt->execute([$_USER["id"], htmlspecialchars($achievement_id)]);
    $count = $stmt->rowcount();
    // print_r($stmt->fetchAll());
    // echo "count $count: qsj_id=" . $_USER["id"] . " achievement_id=$achievement_id";
    if ($count > 0) return $stmt->fetch();
    else return [];
}

function add_achievement(string $achievement_id, string $tags = "", ?PDO $dbm = null): bool
{
    global $_USER;
    if (!$_USER) return false;
    $dbm ??= dbmConnect();

    $res = has_achievement($achievement_id, $dbm);
    if ($res != []) return false;

    $stmt = $dbm->prepare(
        'INSERT INTO achievements (qsj_id, achievement_id, properties) VALUES (?, ?, ?)'
    );
    $stmt->execute([$_USER["id"], htmlspecialchars($achievement_id), htmlspecialchars($tags)]);

    send_websocket_achievement($achievement_id);
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
    
    send_websocket_achievement($achievement_id, $tag);
    return true;
}
