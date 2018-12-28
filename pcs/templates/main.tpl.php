<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

<div class="pcs">
  <h1>Dust Planetary Conquest Scheduler</h1>

  <?php if ($this->message): ?>
    <div class="message">
      <?php print $this->escape($this->message); ?>
    </div>
  <?php endif; ?>

  <div>
    <div class="name">Welcome, <?php print $this->escape($this->user_name); ?></div>
    <?php
      $timezone_lookup_failed = false;
      $timezone_name = get_tz_name_by_offset($this->user_tz_offset);
      if (false === $timezone_name) {
        $timezone_lookup_failed = true;
        $timezone_name = timezone_name_from_abbr(null, 0, false);
      }
    ?>
    <div class="tz">
      Your timezone is <span class="timezone-label"><?php print $timezone_name; ?></span>
      <a href="<?php print $this->tz_change_link; ?>">[Change]</a>
      <?php if ($timezone_lookup_failed): ?>
        <div class="error">
          Unfortunately, the timezone offset (<?php print (int) $this->user_tz_offset; ?>) loaded from your forum profile is not supported. Timezone is set to GMT.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($this->can_submit_events): ?>
    <div><a href="index.php?a=add_event">Add Event</a></div>
  <?php endif; ?>

  <?php if ($this->can_submit_community_events): ?>
    <div><a href="index.php?a=add_community_event">Add Community Event</a></div>
  <?php endif; ?>

  <div>
    <table class="tablebg" width="100%" cellspacing="1">
      <tbody>
      <tr><td class="catdiv"><h2>Scheduled Events</h2></td></tr>
      </tbody>
    </table>

    <?php if (empty($this->events)): ?>

      <div>No scheduled upcoming events</div>

    <?php else: ?>

      <?php if (is_mobile()): ?>

          <?php $counter = 0; ?>
          <?php foreach ($this->events as $event): ?>
            <?php $counter++; ?>
            <div class="event">
              <div style="font-weight: bold;">
                <?php if ((time() >= $event->getTs()) && ($event->getTsEnd() > time())): ?>
                  <span style="color: red;">Now!</span>
                <?php endif; ?>

                <a href="http://forums.dust-gents.com/viewtopic.php?f=16&t=<?php print $event->getForumThread(); ?>">
                  <?php print $counter; ?>. <?php print $this->escape($event->getReadableType()); ?>
                  @ <?php print $event->getEveDate()->getDate(); ?> EVE
                </a>
              </div>
              <div><?php print $this->escape($event->getDistrictLocation()); ?></div>
              <div>Enemy: <?php print $this->escape($event->getEnemyCorp()); ?></div>
              <div>Ally: <?php print $this->escape($event->getFriendlyCorp()); ?></div>
              <div>Local time: <strong><?php print $event->getLocalDate($timezone_name, $this->user_date_format); ?></strong></div>

              <div>
                <?php if ($this->can_edit_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-edit-form" action="index.php" method="get">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input type="hidden" name="a" value="edit_event" />
                    <input class="submit edit" type="submit" name="submit" value="Edit" title="Edit the event">
                  </form>
                <?php endif; ?>

                <?php if ($this->can_delete_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-delete-form" action="index.php?a=delete_event" method="post">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input class="submit delete" type="submit" name="submit" value="Delete" title="Delete the event">
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

      <?php else: ?>

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

                <?php if ($this->can_edit_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-edit-form" action="index.php" method="get">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input type="hidden" name="a" value="edit_event" />
                    <input class="submit edit" type="submit" name="submit" value="" title="Edit the event">
                  </form>
                <?php endif; ?>

                <?php if ($this->can_delete_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-delete-form" action="index.php?a=delete_event" method="post">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input class="submit delete" type="submit" name="submit" value="" title="Delete the event">
                  </form>
                <?php endif; ?>

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
      <?php endif; ?>

    <?php endif; ?>


    <?php if (!empty($this->community_events)): ?>

      <table class="tablebg" width="100%" cellspacing="1">
        <tbody>
        <tr><td class="catdiv"><h2>Community Events</h2></td></tr>
        </tbody>
      </table>

      <?php if (is_mobile()): ?>

        <?php $counter = 0; ?>
        <?php foreach ($this->community_events as $event): ?>
          <div class="community-event">
            <?php $counter++; ?>

            <div style="font-weight: bold;">
              <a href="<?php print $event->getLink(); ?>">
                <?php print $counter; ?>.
                <?php print $this->escape($event->getTitle()); ?>
              </a>
            </div>

            <div>
              <?php
              $startDate = new EveDate($event->getStartDate());
              $endDate = new EveDate($event->getEndDate());
              ?>
              <?php if ($startDate->getTs() > time()): ?>
                Not started yet
              <?php else: ?>
                <strong><?php print $endDate->getIntervalFromNow(); ?></strong>
              <?php endif; ?>
            </div>

            <div>
              Eve: <?php print $startDate->getDate(); ?> - <?php print $endDate->getDate(); ?>
            </div>

            <div>
              Local: <?php print $startDate->getLocalDate($timezone_name, $this->user_date_format); ?> -
              <?php print $endDate->getLocalDate($timezone_name, $this->user_date_format); ?>
            </div>

            <div>
              <?php if ($this->can_edit_community_events || ($this->user_id == $event->getCreatedBy())): ?>
                <form class="event-edit-form" action="index.php" method="get">
                  <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                  <input type="hidden" name="a" value="edit_community_event" />
                  <input class="submit edit" type="submit" name="submit" value="Edit" title="Edit the event">
                </form>
              <?php endif; ?>

              <?php if ($this->can_delete_community_events || ($this->user_id == $event->getCreatedBy())): ?>
                <form class="event-delete-form" action="index.php?a=delete_community_event" method="post">
                  <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                  <input class="submit delete" type="submit" name="submit" value="Delete" title="Delete the event">
                </form>
              <?php endif; ?>
            </div>
          </div>

        <?php endforeach; ?>

      <?php else: ?>

        <table class="tablebg" width="100%" cellspacing="1">
          <tbody>
          <tr>
            <td class="row1" nowrap="nowrap">#</td>
            <td class="row1" nowrap="nowrap">Title</td>
            <td class="row1" nowrap="nowrap">Time left</td>
            <td class="row1" nowrap="nowrap">Start</td>
            <td class="row1" nowrap="nowrap">End</td>
          </tr>

          <?php $counter = 0; ?>
          <?php foreach ($this->community_events as $event): ?>
            <?php $counter++; ?>
            <tr class="<?php print ($counter % 2) ? 'row1' : 'row2'; ?>">
              <td class="gen" align="left">
                &nbsp;<?php print $counter; ?>&nbsp;

                <?php if ($this->can_edit_community_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-edit-form" action="index.php" method="get">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input type="hidden" name="a" value="edit_community_event" />
                    <input class="submit edit" type="submit" name="submit" value="" title="Edit the event">
                  </form>
                <?php endif; ?>

                <?php if ($this->can_delete_community_events || ($this->user_id == $event->getCreatedBy())): ?>
                  <form class="event-delete-form" action="index.php?a=delete_community_event" method="post">
                    <input type="hidden" name="event_id" value="<?php print $event->getEventId(); ?>" />
                    <input class="submit delete" type="submit" name="submit" value="" title="Delete the event">
                  </form>
                <?php endif; ?>

              </td>
              <td class="genmed" align="left">
                <a href="<?php print $event->getLink(); ?>"><?php print $this->escape($event->getTitle()); ?></a>
              </td>

              <?php
              $startDate = new EveDate($event->getStartDate());
              $endDate = new EveDate($event->getEndDate());
              ?>
              <td class="genmed" align="left" nowrap="nowrap">
                <?php if ($startDate->getTs() > time()): ?>
                  Not started yet
                <?php else: ?>
                  <?php print $endDate->getIntervalFromNow(); ?>
                <?php endif; ?>
              </td>
              <td class="genmed" align="left" nowrap="nowrap">
                &nbsp;<?php print $startDate->getLocalDate($timezone_name, $this->user_date_format); ?><br />
                &nbsp;[<?php print $startDate->getDate(); ?> Eve]
              </td>
              <td class="gen" align="left">
                &nbsp;<?php print $endDate->getLocalDate($timezone_name, $this->user_date_format); ?><br />
                &nbsp;[<?php print $endDate->getDate(); ?> Eve]
              </td>
            </tr>
          <?php endforeach; ?>

          </tbody>
        </table>

      <?php endif; ?>

    <?php endif; ?>


    <div class="debug">
      <p>Timezone raw value (forum): <?php print $this->user_tz_offset; ?></p>

      <p>You are member of:
        <?php $groups = array(); ?>
        <?php foreach ($this->user_groups as $group): ?>
          <?php $groups[] = $this->escape($group['name']); ?>
        <?php endforeach; ?>
        <?php print implode(', ', $groups); ?>
      </p>

      <p>Planetary Conquest Scheduler tool by <a href="mailto:flammarion@dust-gents.com">flammarion@dust-gents.com</a></p>

    </div>

  </div>
</div>

<script>
  var deleteEventConfirm = function() {
    if(confirm('Are you sure this event should be removed?')) {
      return true;
    }

    return false;
  };

  jQuery('.event-delete-form').submit(function() {
    return deleteEventConfirm();
  });
</script>
