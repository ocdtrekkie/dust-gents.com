<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<?php
  if (isset($_SESSION['add_event_form'])) {
    $defaults = new DefaultFormValues($_SESSION['add_event_form']);
  }
  else {
    $defaults = new DefaultFormValues();
  }
?>

<div class="pcs">
  <h1>Dust Planetary Conquest Scheduler</h1>

  <?php if ($this->message): ?>
    <div class="message">
      <?php print $this->escape($this->message); ?>
    </div>
  <?php endif; ?>

  <div>
    <a href="index.php">Return to Upcoming Events list</a>
  </div>

  <div>
    <h2>Add Event</h2>

    <form action="index.php?a=add_event_submit" method="post">

      <div class="item">
        <label class="description" for="event_type">Event Type</label>
        <?php $selected_event_type = $defaults->getProp('event_type', 1); ?>
        <div>
          <input id="event_type_pc_attack" name="event_type" type="radio" value="1" <?php print (1 == $selected_event_type) ? 'checked="checked"' : ''; ?>>
          <label for="event_type_pc_attack">PC Attack</label>

          <input id="event_type_pc_defense" name="event_type" type="radio" value="2" <?php print (2 == $selected_event_type) ? 'checked="checked"' : ''; ?>>
          <label for="event_type_pc_defense">PC Defense</label>

          <input id="event_type_pc_defense" name="event_type" type="radio" value="3" <?php print (3 == $selected_event_type) ? 'checked="checked"' : ''; ?>>
          <label for="event_type_pc_defense">FW Battle</label>

          <input id="event_type_pc_defense" name="event_type" type="radio" value="4" <?php print (4 == $selected_event_type) ? 'checked="checked"' : ''; ?>>
          <label for="event_type_pc_defense">Other</label>
        </div>
      </div>

      <div class="item">
        <label class="description" for="district_location">District Location (e.g. Hrober VIII - District 1) or Description (if it is not PC Event, e.g. FW Training)</label>
        <div>
          <input class="district_location" name="district_location" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('district_location'); ?>">
        </div>
      </div>
      <script>
        jQuery(function() {
          var availableTags = [
            <?php foreach ($this->district_names as $district_name): ?>
              <?php print json_encode($district_name); ?>,
            <?php endforeach; ?>
          ];
          jQuery(".district_location" ).autocomplete({
            source: availableTags
          });
        });
      </script>

      <div class="item">
        <label class="description" for="platoon_leader">Platoon Leader</label>
        <div>
          <input class="platoon_leader" name="platoon_leader" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('platoon_leader'); ?>">
        </div>
      </div>

      <div class="item">
        <label class="description" for="backup_platoon_leaders">Backup Platoon Leaders (optional)</label>
        <div>
          <input class="backup_platoon_leaders" name="backup_platoon_leaders" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('backup_platoon_leaders'); ?>">
        </div>
      </div>

      <div class="item">
        <label class="description" for="enemy_corp">Enemy corporation (optional)</label>
        <div>
          <input class="enemy_corp" name="enemy_corp" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('enemy_corp'); ?>">
        </div>
      </div>

      <div class="item">
        <label class="description" for="friendly_corp">Alliance corporation (optional)</label>
        <div>
          <input class="friendly_corp" name="friendly_corp" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('friendly_corp'); ?>">
        </div>
      </div>

      <?php $dateWidget = new DateWidget(); print $dateWidget->render($defaults); ?>

      <div class="item">
        <?php $selected_duration = $defaults->getProp('duration', false); ?>
        <label class="description" for="duration">Event duration (h:mm)</label>
        <div>
          <span>
            <select id="duration" name="duration" value="">
              <option value="1800" <?php print ('1800' == $selected_duration) ? 'selected' : ''; ?>>:30</option>
              <option value="3600" <?php print ('3600' == $selected_duration) ? 'selected' : ''; ?>>1:00</option>
              <option value="5400" <?php print ('5400' == $selected_duration) ? 'selected' : ''; ?>>1:30</option>
              <option value="7200" <?php print ('7200' == $selected_duration) ? 'selected' : ''; ?>>2:00</option>
              <option value="9000" <?php print ('9000' == $selected_duration) ? 'selected' : ''; ?>>2:30</option>
              <option value="10800" <?php print ('10800' == $selected_duration) ? 'selected' : ''; ?>>3:00</option>
            </select>
          </span>
        </div>
      </div>

      <input class="submit" type="submit" name="submit" value="Add Event">
    </form>
  </div>
</div>