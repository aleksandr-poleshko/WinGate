<?

class SMTPX extends SMTP
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Connect($host, $port = 0, $tval = 30, $local_ip)
    {
        // set the error val to null so there is no confusion
        $this->error = null;

        // make sure we are __not__ connected
        if($this->connected()) {
            // already connected, generate error
            $this->error = array("error" => "Already connected to a server");
            return false;
        }

        if(empty($port)) {
            $port = $this->SMTP_PORT;
        }

        $opts = array(
            'socket' => array(
                'bindto' => "$local_ip:0",
            ),
        );

        // create the context...
        $context = stream_context_create($opts);

        // connect to the smtp server
        $this->smtp_conn = @stream_socket_client($host.':'.$port,
                                                 $errno,
                                                 $errstr,
                                                 $tval,  // give up after ? secs
                                                 STREAM_CLIENT_CONNECT,
                                                 $context);

        // verify we connected properly
        if(empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server",
                "errno" => $errno,
                "errstr" => $errstr);
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] . ": $errstr ($errno)" . $this->CRLF . '<br />';
            }
            return false;
        }

        // SMTP server can take longer to respond, give longer timeout for first read
        // Windows does not have support for this timeout function
        if(substr(PHP_OS, 0, 3) != "WIN")
            socket_set_timeout($this->smtp_conn, $tval, 0);

        // get any announcement
        $announce = $this->get_lines();

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $announce . $this->CRLF . '<br />';
        }

        return true;
    }
} 

?>