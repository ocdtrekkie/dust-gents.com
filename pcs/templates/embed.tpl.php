<div class="pcs">
  <div>
    <?php
    $timezone_lookup_failed = false;
    $timezone_name = get_tz_name_by_offset($this->user_tz_offset);
    if (false === $timezone_name) {
      $timezone_lookup_failed = true;
      $timezone_name = timezone_name_from_abbr(null, 0, false);
    }
    ?>
    <div class="tz">
      Your timezone is <span style="color: yellow;"><?php print $timezone_name; ?></span>
      <a href="<?php print $this->tz_change_link; ?>">[Change]</a>
      <?php if ($timezone_lookup_failed): ?>
        <div class="error">
          Unfortunately, the timezone offset (<?php print (int) $this->user_tz_offset; ?>) loaded from your forum profile is not supported. Timezone is set to GMT.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <table class="tablebg" width="100%" cellspacing="1">
      <tbody>
      <tr><td class="catdiv"><h2><a href="<?php print $this->pcs_url; ?>">Upcoming Events (next 3 days)</a></h2></td></tr>
      </tbody>
    </table>

    <table class="tablebg" width="100%" cellspacing="1">
      <tbody>
      <tr>
        <td class="row1" nowrap="nowrap">#</td>
        <td class="row1" nowrap="nowrap">Type</td>
        <td class="row1" nowrap="nowrap">District/Description</td>
        <td class="row1" nowrap="nowrap">Enemy Corp</td>
        <td class="row1" nowrap="nowrap">Friendly Corp</td>
        <td class="row1" nowrap="nowrap">Local Time</td>
        <td class="row1" nowrap="nowrap">EVE Time</td>
      </tr>

      <?php $counter = 0; ?>
      <?php foreach ($this->events as $event): ?>
        <?php $counter++; ?>
        <tr class="<?php print ($counter % 2) ? 'row1' : 'row2'; ?>">
          <td class="gen" align="left">
            &nbsp;<?php print $counter; ?>&nbsp;
          </td>

          <td class="genmed" align="left">
            <a href="http://forums.dust-gents.com/viewtopic.php?f=16&t=<?php print $event->getForumThread(); ?>">
              <?php print $this->escape($event->getReadableType()); ?>
            </a>
          </td>
          <td class="genmed" align="left"><?php print $this->escape($event->getDistrictLocation()); ?></td>
          <td class="genmed" align="left"><?php print $this->escape($event->getEnemyCorp()); ?></td>
          <td class="genmed" align="left"><?php print $this->escape($event->getFriendlyCorp()); ?></td>

          <td class="genmed" align="left" nowrap="nowrap">
            &nbsp;<?php print $event->getLocalDate($timezone_name, $this->user_date_format); ?>&nbsp;
            <?php if ((time() >= $event->getTs()) && ($event->getTsEnd() > time())): ?>
              <span style="color: red;">Now!</span>
            <?php endif; ?>
          </td>
          <td class="gen" align="left"><?php print $event->getEveDate()->getDate(); ?></td>
        </tr>
      <?php endforeach; ?>

      </tbody>
    </table>

  </div>


  <?php if (!empty($this->community_events)): ?>

    <table class="tablebg" width="100%" cellspacing="1">
      <tbody>
      <tr><td class="catdiv"><h2><a href="<?php print $this->pcs_url; ?>">Current Community Events</a></h2></td></tr>
      </tbody>
    </table>

    <table class="tablebg" width="100%" cellspacing="1">
      <tbody>
      <tr>
        <td class="row1" nowrap="nowrap">#</td>
        <td class="row1" nowrap="nowrap">Title</td>
        <td class="row1" nowrap="nowrap">Started</td>
        <td class="row1" nowrap="nowrap">Days left</td>
      </tr>

      <?php $counter = 0; ?>
      <?php foreach ($this->community_events as $event): ?>
        <?php $counter++; ?>
        <tr class="<?php print ($counter % 2) ? 'row1' : 'row2'; ?>">
          <td class="gen" align="left">
            &nbsp;<?php print $counter; ?>&nbsp;
          </td>
          <td class="genmed" align="left">
            <a href="<?php print $event->getLink(); ?>"><?php print $this->escape($event->getTitle()); ?></a>
          </td>

          <?php
          $startDate = new EveDate($event->getStartDate());
          $endDate = new EveDate($event->getEndDate());
          ?>
          <td class="genmed" align="left" nowrap="nowrap">
            <?php print $startDate->getIntervalFromNow(); ?>
          </td>
          <td class="gen" align="left">
            <?php print $endDate->getIntervalFromNow(); ?>
          </td>
        </tr>
      <?php endforeach; ?>

      </tbody>
    </table>

  <?php endif; ?>

</div>