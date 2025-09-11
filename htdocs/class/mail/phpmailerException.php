<?php

namespace { // Global namespace

    // Only define if it doesn't already exist (for potential compatibility)
    if (!class_exists('phpmailerException')) {
        /**
         * @deprecated This class is a legacy placeholder for backward compatibility
         *             with PHPMailer < 6. Please update your code to catch
         *             \PHPMailer\PHPMailer\Exception instead.
         */
        class phpmailerException extends \PHPMailer\PHPMailer\Exception
        {
            public function __construct($message = '', $code = 0, $previous = null)
            {
                // Only raise in debug/dev environments to avoid log noise
                if (defined('XOOPS_DEBUG') && XOOPS_DEBUG) {
                    trigger_error(
                        'The phpmailerException class is deprecated. Use \PHPMailer\PHPMailer\Exception instead.',
                        E_USER_DEPRECATED
                    );
                }
                parent::__construct($message, (int)$code, $previous);
            }

            /** @deprecated */
            public function errorMessage()
            {
                return $this->getMessage();
            }
        }
    }
}
