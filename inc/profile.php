<div id="profile-main">
    <h1 id= "yourachievements"></h1>
    <div id="profile-avatar-wrapper">
        <img id="profile_avatar" src="" width="200">
    </div>
    <div id="profile-info-wrapper">
        <br>
        <table id="profile_info">
            <tr>
                <td><?php echo _("Name"); ?></td>
                <td id="profile_name"></td>
            </tr>
            <tr>
                <td><?php echo _("Points"); ?></td>
                <td id="profile_points"></td>
            </tr>
            <tr>
                <td><?php echo _("Pending points"); ?></td>
                <td id="pending_points"></td>
            </tr>
            <tr>
                <td><?php echo _("Success Rate"); ?></td>
                <td id="profile_attempts"></td>
            </tr>
        </table>
    </div>
    <div id="profile_trophies_wrapper">
        <table id="profile_trophies"></table>
    </div>
</div>
<div id="profile-footer">
    <img title="Return" class="controlLeft" src="media/leftarrow.png" onclick="playClick();return_to_game();">
    <img id="settings_button" title="Leaders" class="controlMiddle" src="media/medal.png" onclick="playClick();display_leaderboard();updateLeaderboard(); startAutoUpdateOfLeaderboard();">
    <img title="Settings" class="controlRight" src="media/settings.png" onclick="playClick();display_settings();">
</div>
