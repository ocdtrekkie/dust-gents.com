<div class="item">
  <label class="description" for="<?php print $this->prefix; ?>date"><?php print $this->escape($this->date_label); ?> (EVE, MM/DD/YYYY). Current EVE date: <?php print $this->current_eve_date->format('M d, Y'); ?></label>
  <div>
    <span>
      <select id="<?php print $this->prefix; ?>date_month" name="<?php print $this->prefix; ?>date_month" value="">
        <?php foreach ($this->months as $month_number => $month_name): ?>
          <option value="<?php print $month_number; ?>" <?php print $month_number == $this->selected_month ? 'selected' : ''; ?>>
            <?php print sprintf('%02d', $month_number); ?> - <?php print $month_name; ?>
          </option>
        <?php endforeach; ?>
      </select> /
    </span>
    <span>
      <select id="<?php print $this->prefix; ?>date_day" name="<?php print $this->prefix; ?>date_day" value="">
        <?php for ($i = 1; $i <= 31; $i++): ?>
          <option value="<?php print $i; ?>" <?php print $i == $this->selected_day ? 'selected' : ''; ?>><?php print $i; ?></option>
        <?php endfor; ?>
      </select> /
    </span>
    <span>
      <select id="<?php print $this->prefix; ?>date_year" name="<?php print $this->prefix; ?>date_year" value="">
        <?php for ($year = $this->current_year; $year <= $this->current_year + 5; $year++): ?>
          <option value="<?php print $year; ?>" <?php print ($year == $this->selected_year) ? 'selected' : ''; ?>><?php print $year; ?></option>
        <?php endfor; ?>
      </select>
    </span>
  </div>
</div>

<div class="item">
  <label class="description" for="<?php print $this->prefix; ?>time"><?php print $this->escape($this->date_label); ?> (EVE, hh:mm). Current EVE time: <?php print $this->current_eve_date->format('H:i'); ?></label>
  <div>
    <span>
      <select id="<?php print $this->prefix; ?>time_hours" name="<?php print $this->prefix; ?>time_hours" value="">
        <?php for ($hour = 0; $hour <= 23; $hour++): ?>
          <option value="<?php print $hour; ?>" <?php print ($hour == $this->selected_hour) ? 'selected' : ''; ?>><?php print sprintf('%02d', $hour); ?></option>
        <?php endfor; ?>
      </select>
    </span>
    <span>
      <select id="<?php print $this->prefix; ?>time_minutes" name="<?php print $this->prefix; ?>time_minutes" value="">
        <?php for ($minutes = 0; $minutes <= 50; $minutes = $minutes + 10): ?>
          <option value="<?php print $minutes; ?>" <?php print ($minutes == $this->selected_minutes) ? 'selected' : ''; ?>><?php print sprintf('%02d', $minutes); ?></option>
        <?php endfor; ?>
      </select>
    </span>
  </div>
</div>
