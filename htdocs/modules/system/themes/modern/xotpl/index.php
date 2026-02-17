<?php
/**
 * Security - prevent direct access
 */
header("HTTP/1.1 404 Not Found");
header("Status: 404 Not Found");
exit();
