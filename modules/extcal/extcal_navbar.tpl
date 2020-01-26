<script>
    jQuery(document).ready(function ($) {
        $('a#current').addClass('active');
    });
</script>

<ul class="nav nav-tabs">
    <li class="nav-item"><a href="view_calendar-month.php" class="nav-link"<{if $view =="calmonth"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_MONTH}></a>
    </li>
    <li class="nav-item"><a href="view_calendar-week.php" class="nav-link"<{if $view =="calweek"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_CALWEEK}></a>
    </li>
    <li class="nav-item"><a href="view_year.php" class="nav-link"<{if $view =="year"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_YEAR}></a>
    </li>
    <li class="nav-item"><a href="view_month.php" class="nav-link"<{if $view =="month"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_MONTH}></a>
    </li>
    <li class="nav-item"><a href="view_week.php" class="nav-link"<{if $view == "week"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_WEEK}></a>
    </li>
    <li class="nav-item"><a href="view_day.php" class="nav-link"<{if $view == "day"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_DAY}></a>
    </li>
    <li class="nav-item"><a href="view_new-event.php" class="nav-link"<{if $view == "newevent"}> id="current"<{/if}>><{$smarty.const._MD_EXTCAL_NAV_NEW_EVENT}></a>
    </li>
</ul>
