<?php

namespace Lichess\OpeningBundle\Sync;

use Lichess\OpeningBundle\Document\MessageRepository;
use Lichess\OpeningBundle\Document\Message;

class MessagesRenderer
{
    protected $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function render($clientMessageId = null)
    {
        $messages = array_map(function($msg) {
            return array(
                'id' => $msg['_id'],
                'u' => substr($msg['username'], 0, 12),
                'r' => $msg['registered'],
                'm' => nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8'))
            );
        }, array_values(iterator_to_array(
            $clientMessageId ? $this->repository->findSince($clientMessageId) : $this->repository->findRecent(30)
        )));

        $data = array(
            'id' => empty($messages) ? $clientMessageId : $messages[0]['id'],
            'messages' => array_reverse($messages)
        );

        return $data;
    }
}