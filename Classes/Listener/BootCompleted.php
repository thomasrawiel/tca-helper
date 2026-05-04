<?php

namespace TRAW\TcaHelper\Listener;

use TRAW\TcaHelper\Configuration\Doktypes;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Event\BootCompletedEvent;

#[AsEventListener(
    identifier: 'txtcahelper-boot-completed'
)]
final class BootCompleted
{
    /**
     * @param BootCompletedEvent $event
     *
     * @throws \Exception
     */
    public function __invoke(BootCompletedEvent $event): void
    {
        Doktypes::registerDoktypesAfterBootCompleted();
    }
}
