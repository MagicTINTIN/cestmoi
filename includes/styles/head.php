<style>
    @font-face {
        font-family: "CM_Creato";
        font-weight: 400;
        font-style: normal;
        font-stretch: 100%;
        src: url("cestmoi/includes/styles/CreatoDisplay-Regular.otf");
    }

    @font-face {
        font-family: "CM_Creato";
        font-weight: bold;
        font-style: normal;
        font-stretch: 100%;
        src: url("cestmoi/includes/styles/CreatoDisplay-Bold.otf");
    }

    @font-face {
        font-family: "CM_Creato";
        font-weight: 800;
        font-style: normal;
        font-stretch: 100%;
        src: url("cestmoi/includes/styles/CreatoDisplay-ExtraBold.otf");
    }

    @font-face {
        font-family: "CM_Creato";
        font-weight: 100;
        font-style: normal;
        font-stretch: 100%;
        src: url("cestmoi/includes/styles/CreatoDisplay-Thin.otf");
    }

    #cestmoi_popup_location {
        position: fixed;
        bottom: 5px;
        right: 0;
        z-index: 9999999;
        display: flex;
        flex-direction: column-reverse;
        font-family: "CM_Creato";
        color: #fff;
    }

    .cestmoi_popup {
        background-color: #222222;
        width: 250px;
        min-height: 70px;
        margin: 5px;
        padding: 5px;
        border-radius: 5px;
        border: solid 2px #ddd;
        box-shadow: black 2px 2px 4px 4px;
        display: flex;
        flex-direction: row;
        text-decoration: none;
        color: #fff;
        transition: all 0.2s;
        cursor: pointer;
        transform: translateX(400px);
    }

   .cestmoi_popup_show {
        transform: translateX(0px);
   }

    .cestmoi_popup:hover {
        transform: translateX(0px) scale(1.02);
        background-color: #373737;
    }

    .cestmoi_popup:active {
        transform: translateX(0px) scale(0.99);
        background-color: #0f0f0f;
    }

    .cestmoi_popup img {
        width: 50px;
        height: 50px;
        border: solid 2px #ddd;
        margin: 3px;
        margin-right: 10px;
        background-color: #888;
        border-radius: 5px;
    }

    .cestmoi_popup_texts {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .cestmoi_popup_title {
        font-weight: bold;
    }

    .cestmoi_popup_achievement_name {
        color: #ddd;
    }
</style>