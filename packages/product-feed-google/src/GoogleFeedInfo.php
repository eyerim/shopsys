<?php

namespace Shopsys\ProductFeed\GoogleBundle;

use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GoogleFeedInfo implements FeedInfoInterface
{
    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Google';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'google';
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation(): string
    {
        return $this->translator->trans(
            'Google Shopping product feed is not optimized for selling to Australia,
            Czechia, France, Germany, Italy, Netherlands, Spain, Switzerland, the UK,
            and the US. It is caused by missing \'shipping\' attribute.'
        );
    }
}
