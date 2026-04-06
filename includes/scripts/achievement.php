<?php
function insert_achievement_notification()
{
    global $_ACHIEVEMENT_NOTIFICATIONS;
?>
    <script>
        function add_new_achievement_notification(name, icon) {
            const notifs = document.getElementById("cestmoi_popup_location");
            const notif = document.createElement("a");
            notif.target = "_blank"
            notif.href = "https://who.magictintin.fr/"
            notif.id = "cestmoi_new_achievement_" + name;
            notif.classList.add("cestmoi_popup");
            const notif_icon = document.createElement("img");
            notif_icon.src = icon;
            const notif_texts = document.createElement("div");
            notif_texts.classList.add("cestmoi_popup_texts");
            const notif_title = document.createElement("span");
            notif_title.classList.add("cestmoi_popup_title");
            notif_title.innerText = "NEW ACHIEVEMENT!";

            const notif_name = document.createElement("span");
            notif_name.classList.add("cestmoi_popup_achievement_name");
            notif_name.innerText = name;


            notif.appendChild(notif_icon);

            notif_texts.appendChild(notif_title);
            notif_texts.appendChild(notif_name);
            notif.appendChild(notif_texts);

            notifs.appendChild(notif);

            setTimeout(() => {
                notif.classList.add("cestmoi_popup_show");
            }, 100);

            let closeInterval = setInterval(() => {
                notif.classList.add("cestmoi_popup");
                if (!notif.matches(':hover')) {
                    clearInterval(closeInterval);
                    notif.classList.remove("cestmoi_popup_show");

                    setTimeout(() => {
                        notifs.removeChild(notif);
                    }, 300);
                }
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?= $_ACHIEVEMENT_NOTIFICATIONS ?>
        });
    </script>
<?php }
