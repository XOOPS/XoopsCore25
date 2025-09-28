<script>
    jQuery(document).ready(function ($) {
        $('a#current').parent().addClass('active');
    });
</script>

<ul class="nav nav-tabs">
    <li class="nav-item"><a class="nav-link" href="view_calendar-month.php" <{if isset($view) && $view == "calmonth"}>id="current" <{else}>class="head"<{/if}>><{$lang.calmonth}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_calendar-week.php" <{if isset($view) && $view =="calweek"}>id="current" <{else}>class="head"<{/if}>><{$lang.calweek}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_year.php" <{if isset($view) && $view =="year"}>id="current" <{else}>class="head"<{/if}>><{$lang.year}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_month.php" <{if isset($view) && $view =="month"}>id="current" <{else}>class="head"<{/if}>><{$lang.month}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_week.php" <{if isset($view) && $view == "week"}>id="current" <{else}>class="head"<{/if}>><{$lang.week}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_day.php" <{if isset($view) && $view == "day"}>id="current" <{else}>class="head"<{/if}>><{$lang.day}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_agenda-week.php" <{if isset($view) && $view == "agendaweek"}>id="current" <{else}>class="head"<{/if}>><{$lang.agendaweek}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_agenda-day.php" <{if isset($view) && $view == "agendaday"}>id="current" <{else}>class="head"<{/if}>><{$lang.agendaday}></a>
    </li>
    <li class="nav-item"><a class="nav-link" href="view_search.php" <{if isset($view) && $view == "search"}>id="current" <{else}>class="head"<{/if}>><{$lang.search}></a>
    </li>
    <{*<li class="nav-item"><a class="nav-link" href="view_new-event.php" <{if isset($view) && $view == "newevent"}>id="current"<{else}>class="head"<{/if}>><{$lang.newevent}></a>*}>
    <{*</li>*}>
</ul>
