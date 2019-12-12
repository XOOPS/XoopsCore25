<script>
    jQuery(document).ready(function ($) {
        $('a#current').parent().addClass('active');
    });
</script>

<ul class="nav nav-tabs">
    <li><a href="view_calendar-month.php" <{if $view =="calmonth"}>id="current" <{else}>class="head"<{/if}>><{$lang.calmonth}></a>
    </li>
    <li><a href="view_calendar-week.php" <{if $view =="calweek"}>id="current" <{else}>class="head"<{/if}>><{$lang.calweek}></a>
    </li>
    <li><a href="view_year.php" <{if $view =="year"}>id="current" <{else}>class="head"<{/if}>><{$lang.year}></a>
    </li>
    <li><a href="view_month.php" <{if $view =="month"}>id="current" <{else}>class="head"<{/if}>><{$lang.month}></a>
    </li>
    <li><a href="view_week.php" <{if $view == "week"}>id="current" <{else}>class="head"<{/if}>><{$lang.week}></a>
    </li>
    <li><a href="view_day.php" <{if $view == "day"}>id="current" <{else}>class="head"<{/if}>><{$lang.day}></a>
    </li>
    <li><a href="view_agenda-week.php" <{if $view == "agendaweek"}>id="current" <{else}>class="head"<{/if}>><{$lang.agendaweek}></a>
    </li>
    <li><a href="view_agenda-day.php" <{if $view == "agendaday"}>id="current" <{else}>class="head"<{/if}>><{$lang.agendaday}></a>
    </li>
    <li><a href="view_search.php" <{if $view == "search"}>id="current" <{else}>class="head"<{/if}>><{$lang.search}></a>
    </li>
    <{*<li><a href="view_new-event.php" <{if $view == "newevent"}>id="current"<{else}>class="head"<{/if}>><{$lang.newevent}></a>*}>
    <{*</li>*}>
</ul>
