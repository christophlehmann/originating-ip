<?php

declare(strict_types=1);

namespace Lemming\OriginatingIp\EventListener;

use Symfony\Component\Mime\Message;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AddMailHeader
{
    public function __construct(
        private string $originatingIpHeaderName = 'X-Originating-IP',
        private bool $skipForAuthenticatedUsers = true
    ) {
    }

    public function __invoke(BeforeMailerSentMessageEvent $event): void
    {
        if ($this->shouldAddHeader()) {
            $message = $event->getMessage();
            if ($message instanceof Message) {
                $value = '[' . GeneralUtility::getIndpEnv('REMOTE_ADDR') . ']';
                $message->getHeaders()->addHeader($this->originatingIpHeaderName, $value);
            }
        }
    }

    protected function shouldAddHeader(): bool
    {
        $request = $this->getServerRequest();
        if ($this->skipForAuthenticatedUsers && $request?->getAttribute('frontend.user')?->getUserId()) {
            return false;
        }

        return $request instanceof ServerRequest
            && ApplicationType::fromRequest($request)->isFrontend();
    }

    protected function getServerRequest(): ?ServerRequest
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
