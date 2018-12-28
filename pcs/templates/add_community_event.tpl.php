<?php
  if (isset($_SESSION['add_community_event_form'])) {
    $defaults = new DefaultFormValues($_SESSION['add_community_event_form']);
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
    <h2>Add Community Event</h2>

    <form action="index.php?a=add_community_event_submit" method="post">

      <div class="item">
        <label class="description" for="title">Community Event Title</label>
        <div>
          <input class="title" name="title" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('title'); ?>">
        </div>
      </div>

      <div class="item">
        <label class="description" for="link">Community Event Link</label>
        <div>
          <input class="link" name="link" type="text" maxlength="200" length="200" value="<?php print $defaults->getProp('link'); ?>">
        </div>
      </div>

      <?php
      $startDateWidget = new DateWidget();
      $startDateWidget->setDateLabel('Start date');
      $startDateWidget->setTimeLabel('Start time');
      $startDateWidget->setPrefix('start');
      print $startDateWidget->render($defaults);
      ?>

      <?php
      $endDateWidget = new DateWidget();
      $endDateWidget->setDateLabel('End date');
      $endDateWidget->setTimeLabel('End time');
      $endDateWidget->setPrefix('end');
      print $endDateWidget->render($defaults);
      ?>

      <input class="submit" type="submit" name="submit" value="Add Community Event">
    </form>
  </div>
</div>