<?php
/**
 *
 */

namespace Crocos\Notifier\Notifier;

class Tiarra
    implements NotifierInterface
{
    protected $tiarra = null;

    public function __construct(array $options)
    {
        $this->use_notice = isset($options['use_notice']) ? $options['use_notice'] : true;
        $this->channel = $options['channel'];

        $tiarra = stream_resolve_include_path('Net/Socket/Tiarra.php');
        if (!$tiarra) {
            throw new \RuntimeException("Net_Socket_Tiarra is not installed");
        }

        require_once $tiarra;

        $this->tiarra = new \Net_Socket_Tiarra($options['socket']);
    }

    public function notify($message)
    {
        if ($this->use_notice) {
            $this->tiarra->noticeMessage($this->channel, $message);
        } else {
            $this->tiarra->message($this->channel, $message);
        }
    }
}
