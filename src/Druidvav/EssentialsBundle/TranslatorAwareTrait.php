<?php

namespace Druidvav\EssentialsBundle;

use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorAwareTrait
{
    protected TranslatorInterface $translator;

    /**
     * @Required
     */
    #[Required]
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
