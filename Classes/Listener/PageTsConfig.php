<?php

declare(strict_types=1);

namespace TRAW\TcaHelper\Listener;

use TRAW\TcaHelper\Service\PageTsGenerator;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Event\ModifyLoadedPageTsConfigEvent;

#[AsEventListener(
    identifier: 'txtcahelper-page-tsconfig'
)]
final class PageTsConfig
{
    /**
     * Generate Page tsconfig for registered CTypes
     *
     *
     * @throws \Exception
     */
    public function __invoke(ModifyLoadedPageTsConfigEvent $event): void
    {
        $tsConfig = $event->getTsConfig();

        $generated = PageTsGenerator::generate();

        if ($generated !== '' && $generated !== '0') {
            $tsConfig = array_merge(['pagesTsConfig-package-tcahelper' => $generated], $tsConfig);
            $event->setTsConfig($tsConfig);
        }

    }
}
