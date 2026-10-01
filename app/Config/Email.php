<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = 'Appraisal Management System';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = '';

    /**
     * SMTP authentication method: login, plain
     */
    public string $SMTPAuthMethod = 'login';

    /**
     * SMTP Username
     */
    public string $SMTPUser = '';

    /**
     * SMTP Password
     */
    public string $SMTPPass = '';

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 30;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption: '', 'tls' or 'ssl'
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail: text or html
     */
    public string $mailType = 'html';

    /**
     * Character set
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest, 5 = lowest, 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;

    /**
     * Load email configuration from environment variables.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fromEmail = (string) env('email.fromEmail', '');
        $this->fromName  = (string) env(
            'email.fromName',
            'Appraisal Management System'
        );

        $this->protocol = (string) env('email.protocol', 'smtp');

        $this->SMTPHost = (string) env('email.SMTPHost', '');
        $this->SMTPUser = (string) env('email.SMTPUser', '');
        $this->SMTPPass = (string) env('email.SMTPPass', '');

        $this->SMTPPort = (int) env('email.SMTPPort', 587);

        $this->SMTPCrypto = (string) env('email.SMTPCrypto', 'tls');

        $this->SMTPTimeout = (int) env('email.SMTPTimeout', 30);

        $this->SMTPAuthMethod = (string) env('email.SMTPAuthMethod', 'login');

        $this->SMTPKeepAlive = filter_var(env('email.SMTPKeepAlive', false), FILTER_VALIDATE_BOOLEAN);

        $this->mailType = (string) env('email.mailType', 'html');
        $this->charset  = (string) env('email.charset', 'UTF-8');

        $this->wordWrap = filter_var(env('email.wordWrap', true), FILTER_VALIDATE_BOOLEAN);
    }
}
