<div id="settings-main">
    <img src="media/language_selector.png">
    <p><?php echo _("Hints and Help Language"); ?></p>
    <br>
    <select id="menuLanguageSettings" class="interface-lang" size="1">
    </select>
    <br><br>
    <br><br>
    <p><?php echo _("Game Language"); ?></p>
    <select id="gamelanguage" size="1">
    </select>
    <br><br>
    <p><?php echo _("Notify me about new points I earn..."); ?></p>
    <select id="notifications" size="1">
        <option><?php echo _("Real time"); ?></option>
        <option><?php echo _("Once daily"); ?></option>
        <option><?php echo _("Once weekly"); ?></option>
        <option><?php echo _("When pigs fly..."); ?></option>
    </select>
    <br><br>
    <p><?php echo _("Post achievements to my timeline..."); ?></p>

    <select id= "posts" size="1">
        <option><?php echo _("Always (Every time I have a winning entry)"); ?></option>
        <option><?php echo _("Often (Max once a day)"); ?></option>
        <option><?php echo _("Sometimes (Max once a week)"); ?></option>
        <option><?php echo _("Occasionally (Max once monthly)"); ?></option>
        <option><?php echo _("When pigs fly... (Never)"); ?></option>
    </select>
</div>
<br><br>
<img title="Submit" class="control" src="media/checksign.jpg" onclick="playClick(); saveSettings();">
