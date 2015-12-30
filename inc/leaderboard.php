<div id="settings-outer">
    <div class="settings-inner">
        <?php echo _("Language"); ?>
        <select class="scoreSelectors" id= "scoreLanguage" onchange="updateLeaderboard(); playClick();" size="1">
            <option><?php echo _("All languages"); ?></option>
            <option><?php echo _("English"); ?></option>
            <option><?php echo _("French"); ?></option>
            <option><?php echo _("Vietnamese"); ?></option>
            <option><?php echo _("Swahili"); ?></option>
            <option><?php echo _("German"); ?></option>
            <option><?php echo _("Italiano"); ?></option>
            <option><?php echo _("Esperanto"); ?></option>
            <option><?php echo _("Russian"); ?></option>
            <option><?php echo _("Kirundi"); ?></option>
            <option><?php echo _("Spanish"); ?></option>
            <option><?php echo _("Dutch"); ?></option>
            <option><?php echo _("Irish"); ?></option>

        </select>
    </div>
    <div class="settings-inner">
        <?php echo _("Game"); ?>
        <select class="scoreSelectors" id= "scoreGame" onchange="updateLeaderboard(); playClick();" size="1">
            <option><?php echo _("All games"); ?></option>
            <option><?php printf(_("%s"),$gameNames["1"]); ?></option>
            <option><?php printf(_("%s"),$gameNames["2"]); ?></option>
            <option><?php printf(_("%s"),$gameNames["3"]); ?></option>
            <option><?php printf(_("%s"),$gameNames["4"]); ?></option>

        </select>
    </div>
    <div class="settings-inner">
        <?php echo _("TimePeriod"); ?>
        <select class="scoreSelectors" id= "scoretimePeriod" onchange="updateLeaderboard(); playClick();" size="1">
            <option><?php echo _("All time"); ?></option>
            <option><?php echo _("Last Month"); ?></option>
            <option><?php echo _("Last Week"); ?></option>
            <option><?php echo _("Last 24 Hours"); ?></option>
        </select>
    </div>
    <div class="settings-inner">
        <?php echo _("What to compare"); ?>
        <select class="scoreSelectors" id= "scoreMetric" onchange="updateLeaderboard(); playClick();" size="1">
            <option><?php echo _("Points Earned"); ?></option>
            <option><?php echo _("# of Submissions"); ?></option>
            <option><?php echo _("Success Rate"); ?></option>

        </select>
    </div>
    <div class="settings-inner">
    <input id="autoloop" class="scoreSelectors" type="checkbox" name="autoloop" value="autoloop" checked="true" ><?php echo _("Auto-Loop"); ?><br>
    </div>
</div>
<h1><?php echo _("Leaderboard"); ?></h1>

<div id="leader">
    <table id="score_table">
    </table>
</div>
<div id="leaderboard-footer">
    <img title="Return" class="control" src="media/leftarrow.png" onclick="playClick(); display_profile();">
</div>
